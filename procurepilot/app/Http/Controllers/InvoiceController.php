<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\GoodsReceipt;
use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\User;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $org = currentOrganization();

        $query = SupplierInvoice::where('organization_id', $org->id)
            ->with(['supplier', 'purchaseOrder'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('match_status')) {
            $query->where('match_status', $request->match_status);
        }

        $invoices = $query->paginate(10)->withQueryString();
        $statuses = ['pending', 'verified', 'matched', 'approved', 'rejected', 'paid'];
        $matchStatuses = ['none', 'unmatched', 'partial', 'full'];
        $counts = [
            'total'      => SupplierInvoice::where('organization_id', $org->id)->count(),
            'pending'    => SupplierInvoice::where('organization_id', $org->id)->whereIn('status', ['pending', 'verified'])->count(),
            'approved'   => SupplierInvoice::where('organization_id', $org->id)->where('status', 'approved')->count(),
            'paid'       => SupplierInvoice::where('organization_id', $org->id)->where('status', 'paid')->count(),
        ];

        return view('invoices.index', compact('invoices', 'statuses', 'matchStatuses', 'counts'));
    }

    public function create()
    {
        $org = currentOrganization();

        $suppliers = Supplier::where('organization_id', $org->id)->where('status', 'approved')->orderBy('name')->get();
        $pos = PurchaseOrder::where('organization_id', $org->id)->whereIn('status', ['approved', 'issued', 'received', 'partially_received'])->orderByDesc('order_date')->get();
        $contracts = Contract::where('organization_id', $org->id)->whereIn('status', ['active', 'draft'])->orderBy('title')->get();

        return view('invoices.create', compact('suppliers', 'pos', 'contracts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'      => ['required', 'exists:suppliers,id'],
            'purchase_order_id'=> ['nullable', 'exists:purchase_orders,id'],
            'contract_id'      => ['nullable', 'exists:contracts,id'],
            'invoice_date'     => ['nullable', 'date'],
            'due_date'         => ['nullable', 'date'],
            'subtotal'         => ['required', 'numeric', 'min:0'],
            'tax_amount'       => ['nullable', 'numeric', 'min:0'],
            'currency'         => ['nullable', 'string', 'max:10'],
            'notes'            => ['nullable', 'string'],
            'items'            => ['required', 'array', 'min:1'],
        ]);

        $data['organization_id'] = currentOrganization()->id;
        $data['number'] = ReferenceService::supplierInvoice(currentOrganization());
        $data['tax_amount'] = $data['tax_amount'] ?? 0;
        $data['total'] = $data['subtotal'] + $data['tax_amount'];

        $invoice = SupplierInvoice::create($data);

        foreach ($request->items as $item) {
            if (empty($item['description'])) {
                continue;
            }

            $qty = $item['quantity'] ?? 1;
            $price = $item['unit_price'] ?? 0;

            $invoice->items()->create([
                'po_item_id' => $item['po_item_id'] ?? null,
                'description' => $item['description'],
                'quantity'    => $qty,
                'unit'        => $item['unit'] ?? null,
                'unit_price'  => $price,
                'total_price' => $qty * $price,
            ]);
        }

        AuditLog::record('supplier_invoice_registered', $invoice, [], $invoice->toArray());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Supplier invoice registered.');
    }

    public function show(SupplierInvoice $invoice)
    {
        abort_unless($invoice->organization_id === currentOrganization()->id, 403);

        $invoice->load(['supplier', 'purchaseOrder', 'contract', 'verifier', 'approver', 'items.poItem']);

        return view('invoices.show', compact('invoice'));
    }

    public function match(SupplierInvoice $invoice)
    {
        abort_unless($invoice->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isProcurement(), 403);

        $before = $invoice->toArray();

        $matchResult = $this->runThreeWayMatch($invoice);

        $invoice->update([
            'match_status' => $matchResult['status'],
            'match_detail' => $matchResult['detail'],
            'status'       => $matchResult['status'] === 'full' ? 'matched' : 'verified',
            'verified_by'  => auth()->id(),
            'verified_at'  => now(),
        ]);

        AuditLog::record('three_way_match', $invoice, $before, $invoice->toArray());

        return back()->with(
            $matchResult['status'] === 'full' ? 'success' : 'warning',
            $matchResult['message']
        );
    }

    public function verify(SupplierInvoice $invoice)
    {
        abort_unless($invoice->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isProcurement(), 403);

        $before = $invoice->toArray();
        $invoice->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        AuditLog::record('supplier_invoice_verified', $invoice, $before, $invoice->toArray());

        return back()->with('success', 'Invoice verified. Run three-way matching next.');
    }

    public function approve(SupplierInvoice $invoice)
    {
        abort_unless($invoice->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);
        abort_unless(in_array($invoice->status, ['verified', 'matched']), 403);

        if ($invoice->match_status !== 'full') {
            return back()->withErrors(['Three-way matching must be complete before approval.']);
        }

        $before = $invoice->toArray();
        $invoice->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLog::record('supplier_invoice_approved', $invoice, $before, $invoice->toArray());

        return back()->with('success', 'Invoice approved for payment.');
    }

    public function pay(SupplierInvoice $invoice)
    {
        abort_unless($invoice->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);
        abort_unless($invoice->status === 'approved', 403);

        $before = $invoice->toArray();
        $invoice->update([
            'status' => 'paid',
            'paid_at'=> now(),
        ]);

        AuditLog::record('supplier_invoice_paid', $invoice, $before, $invoice->toArray());

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function reject(Request $request, SupplierInvoice $invoice)
    {
        abort_unless($invoice->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);
        abort_unless(in_array($invoice->status, ['pending', 'verified', 'matched']), 403);

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $before = $invoice->toArray();
        $invoice->update([
            'status' => 'rejected',
            'notes'  => $invoice->notes . "\nREJECTED: " . $request->rejection_reason,
        ]);

        AuditLog::record('supplier_invoice_rejected', $invoice, $before, $invoice->toArray());

        return back()->with('success', 'Invoice rejected.');
    }

    public function destroy(SupplierInvoice $invoice)
    {
        abort_unless($invoice->organization_id === currentOrganization()->id, 403);
        abort_unless(in_array($invoice->status, ['pending', 'rejected']), 403);

        AuditLog::record('supplier_invoice_removed', $invoice, $invoice->toArray(), []);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice removed.');
    }

    protected function runThreeWayMatch(SupplierInvoice $invoice): array
    {
        if (! $invoice->purchase_order_id) {
            return ['status' => 'unmatched', 'message' => 'No purchase order linked to this invoice — cannot match.', 'detail' => []];
        }

        $po = PurchaseOrder::with('items')->find($invoice->purchase_order_id);
        if (! $po) {
            return ['status' => 'unmatched', 'message' => 'Linked purchase order not found.', 'detail' => []];
        }

        $receipts = GoodsReceipt::where('purchase_order_id', $po->id)->with('items')->get();
        $receivedQtyByItem = [];
        foreach ($receipts as $receipt) {
            foreach ($receipt->items as $ri) {
                $receivedQtyByItem[$ri->po_item_id] = ($receivedQtyByItem[$ri->po_item_id] ?? 0) + (float) $ri->quantity;
            }
        }

        $detail = [];
        $allMatched = true;
        $anyMatch = false;

        foreach ($invoice->items as $line) {
            $lineDetail = ['line' => $line->description, 'status' => 'unmatched'];

            if ($line->po_item_id) {
                $poItem = PoItem::find($line->po_item_id);
                if ($poItem) {
                    $poQty = (float) $poItem->quantity;
                    $received = (float) ($receivedQtyByItem[$poItem->id] ?? 0);

                    $qtyOk = abs((float) $line->quantity - $received) <= max(0.01, $poQty * 0.005);
                    $priceOk = abs((float) $line->unit_price - (float) $poItem->unit_price) <= max(0.01, (float) $poItem->unit_price * 0.005);

                    if ($qtyOk && $priceOk) {
                        $lineDetail['status'] = 'matched';
                        $anyMatch = true;
                    } else {
                        $allMatched = false;
                        $lineDetail['status'] = 'mismatch';
                        $lineDetail['po_qty'] = $poQty;
                        $lineDetail['received_qty'] = $received;
                        $lineDetail['po_price'] = $poItem->unit_price;
                    }
                }
            } else {
                $allMatched = false;
            }

            $detail[] = $lineDetail;
        }

        if ($allMatched && $invoice->items->count()) {
            return [
                'status' => 'full',
                'message' => 'Three-way match complete — invoice, purchase order and goods receipt agree.',
                'detail' => $detail,
            ];
        }

        if ($anyMatch) {
            return [
                'status' => 'partial',
                'message' => 'Partial match — some line items disagree with the purchase order or goods receipt.',
                'detail' => $detail,
            ];
        }

        return ['status' => 'unmatched', 'message' => 'No line items could be matched.', 'detail' => $detail];
    }
}
