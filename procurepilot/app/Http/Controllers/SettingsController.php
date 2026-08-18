<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\PaymentGateway;
use App\Models\SupplierDocumentRequirement;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $org = currentOrganization();

        $gateways = PaymentGateway::where('organization_id', $org->id)
            ->get()
            ->keyBy('provider');

        $requirements = SupplierDocumentRequirement::where('organization_id', $org->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('settings.index', compact('org', 'gateways', 'requirements'));
    }

    public function updateOrganization(Request $request)
    {
        $org = currentOrganization();

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'max:120'],
            'currency'=> ['nullable', 'string', 'max:10'],
            'tax_id'  => ['nullable', 'string', 'max:120'],
        ]);

        $before = $org->toArray();
        $org->update($data);

        AuditLog::record('organization_updated', $org, $before, $org->toArray());

        return back()->with('success', 'Organization settings updated.');
    }

    public function updateGateways(Request $request)
    {
        $org = currentOrganization();

        $data = $request->validate([
            'gateways'              => ['required', 'array'],
            'gateways.*.provider'   => ['required', 'in:paystack,flutterwave,mono'],
            'gateways.*.public_key' => ['nullable', 'string', 'max:255'],
            'gateways.*.secret_key' => ['nullable', 'string', 'max:255'],
            'gateways.*.is_active'  => ['sometimes', 'boolean'],
        ]);

        foreach ($data['gateways'] as $item) {
            $existing = PaymentGateway::where('organization_id', $org->id)
                ->where('provider', $item['provider'])
                ->first();

            PaymentGateway::updateOrCreate(
                ['organization_id' => $org->id, 'provider' => $item['provider']],
                [
                    'public_key' => $item['public_key'] ?? $existing?->public_key,
                    'secret_key' => $item['secret_key'] ?? $existing?->secret_key,
                    'is_active'  => isset($item['is_active']) && $item['is_active'],
                ]
            );
        }

        AuditLog::record('payment_gateways_updated', $org, [], ['providers' => array_column($data['gateways'], 'provider')]);

        return back()->with('success', 'Payment gateway settings updated.');
    }

    public function publicRequirements(Request $request)
    {
        $org = Organization::where('is_active', true)->orderBy('id')->first();

        if (! $org) {
            abort(404, 'No organisation configured.');
        }

        $requirements = SupplierDocumentRequirement::where('organization_id', $org->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('settings.public-requirements', compact('org', 'requirements'));
    }

    public function publicRequirementsPdf(Request $request)
    {        $org = Organization::where('is_active', true)
            ->orderBy('id')
            ->first();

        if (! $org) {
            abort(404, 'No organisation configured.');
        }

        $requirements = SupplierDocumentRequirement::where('organization_id', $org->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $html = view('settings.requirements-pdf', compact('org', 'requirements'))->render();

        $tmpHtml = tempnam(sys_get_temp_dir(), 'reqpdf') . '.html';
        file_put_contents($tmpHtml, $html);

        $tmpPdf = tempnam(sys_get_temp_dir(), 'reqpdf') . '.pdf';

        $command = sprintf(
            'chromium --headless --no-sandbox --disable-gpu --disable-dev-shm-usage --print-to-pdf=%s --no-pdf-header-footer %s 2>/dev/null',
            escapeshellarg($tmpPdf),
            escapeshellarg('file://' . $tmpHtml)
        );

        exec($command, $output, $exitCode);

        @unlink($tmpHtml);

        if ($exitCode !== 0 || ! file_exists($tmpPdf) || filesize($tmpPdf) < 1000) {
            if (file_exists($tmpPdf)) {
                @unlink($tmpPdf);
            }

            abort(500, 'Could not generate the PDF. Please try again.');
        }

        $filename = 'Supplier-Registration-Requirements-' . now()->format('Y-m-d') . '.pdf';

        return response()->download($tmpPdf, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    public function requirementsPdf(Request $request)
    {
        $org = currentOrganization();

        $requirements = SupplierDocumentRequirement::where('organization_id', $org->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $html = view('settings.requirements-pdf', compact('org', 'requirements'))->render();

        $tmpHtml = tempnam(sys_get_temp_dir(), 'reqpdf') . '.html';
        file_put_contents($tmpHtml, $html);

        $tmpPdf = tempnam(sys_get_temp_dir(), 'reqpdf') . '.pdf';

        $command = sprintf(
            'chromium --headless --no-sandbox --disable-gpu --disable-dev-shm-usage --print-to-pdf=%s --no-pdf-header-footer %s 2>/dev/null',
            escapeshellarg($tmpPdf),
            escapeshellarg('file://' . $tmpHtml)
        );

        exec($command, $output, $exitCode);

        @unlink($tmpHtml);

        if ($exitCode !== 0 || ! file_exists($tmpPdf) || filesize($tmpPdf) < 1000) {
            if (file_exists($tmpPdf)) {
                @unlink($tmpPdf);
            }

            return back()->with('error', 'Could not generate the PDF. Please try again.');
        }

        $filename = 'Supplier-Registration-Requirements-' . now()->format('Y-m-d') . '.pdf';

        return response()->download($tmpPdf, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    public function storeRequirement(Request $request)
    {        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_required' => ['sometimes', 'boolean'],
        ]);

        $org = currentOrganization();

        $requirement = SupplierDocumentRequirement::create([
            'organization_id' => $org->id,
            'name'            => $data['name'],
            'description'     => $data['description'] ?? null,
            'is_required'     => $request->boolean('is_required'),
            'sort_order'      => (int) SupplierDocumentRequirement::where('organization_id', $org->id)->max('sort_order') + 1,
        ]);

        AuditLog::record('supplier_document_requirement_created', $requirement, [], $requirement->toArray());

        return back()->with('success', 'Document requirement added. It now appears on the supplier registration form.');
    }

    public function updateRequirement(Request $request, SupplierDocumentRequirement $requirement)
    {
        abort_unless($requirement->organization_id === currentOrganization()->id, 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_required' => ['sometimes', 'boolean'],
        ]);

        $before = $requirement->toArray();
        $requirement->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_required' => $request->boolean('is_required'),
        ]);

        AuditLog::record('supplier_document_requirement_updated', $requirement, $before, $requirement->toArray());

        return back()->with('success', 'Document requirement updated.');
    }

    public function destroyRequirement(SupplierDocumentRequirement $requirement)
    {
        abort_unless($requirement->organization_id === currentOrganization()->id, 403);

        AuditLog::record('supplier_document_requirement_removed', $requirement, $requirement->toArray(), []);
        $requirement->delete();

        return back()->with('success', 'Document requirement removed.');
    }
}
