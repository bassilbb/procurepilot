<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $org = currentOrganization();

        $query = PurchaseOrder::where('organization_id', $org->id)
            ->with('supplier')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();
        $statuses = ['draft', 'issued', 'approved', 'partially_received', 'received', 'cancelled'];

        return view('purchase-orders.index', compact('orders', 'statuses'));
    }

    public function create()
    {
        $suppliers = Supplier::where('organization_id', currentOrganization()->id)->where('status', 'approved')->orderBy('name')->get();
        $contracts = Contract::where('organization_id', currentOrganization()->id)->whereIn('status', ['draft', 'active'])->orderBy('title')->get();

        return view('purchase-orders.create', compact('suppliers', 'contracts'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['organization_id'] = currentOrganization()->id;
        $data['reference'] = ReferenceService::po(currentOrganization());
        $data['total'] = collect($request->items)->sum(fn ($i) => $i['quantity'] * $i['unit_price']);

        $po = PurchaseOrder::create($data);

        foreach ($request->items as $item) {
            if (empty($item['description'])) {
                continue;
            }

            $po->items()->create([
                'description'  => $item['description'],
                'quantity'     => $item['quantity'],
                'unit'         => $item['unit'] ?? null,
                'unit_price'   => $item['unit_price'],
                'total_price'  => $item['quantity'] * $item['unit_price'],
            ]);
        }

        AuditLog::record('purchase_order_created', $po, [], $po->toArray());

        return redirect()->route('purchase-orders.show', $po)->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->organization_id === currentOrganization()->id, 403);

        $purchaseOrder->load(['supplier', 'contract', 'tender', 'items', 'receipts.items.poItem', 'approver']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function issue(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->organization_id === currentOrganization()->id, 403);

        $before = $purchaseOrder->toArray();
        $purchaseOrder->update(['status' => 'issued', 'order_date' => $purchaseOrder->order_date ?? now()]);

        AuditLog::record('purchase_order_issued', $purchaseOrder, $before, $purchaseOrder->toArray());

        return back()->with('success', 'Purchase order issued to supplier.');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $purchaseOrder->toArray();
        $purchaseOrder->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLog::record('purchase_order_approved', $purchaseOrder, $before, $purchaseOrder->toArray());

        return back()->with('success', 'Purchase order approved.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->organization_id === currentOrganization()->id, 403);

        $request->validate([
            'items'           => ['required', 'array'],
            'items.*.quantity'=> ['required', 'numeric', 'min:0'],
            'note'            => ['nullable', 'string'],
        ]);

        $receipt = GoodsReceipt::create([
            'organization_id'   => currentOrganization()->id,
            'purchase_order_id' => $purchaseOrder->id,
            'received_by'       => auth()->id(),
            'received_at'       => now(),
            'note'              => $request->note,
        ]);

        $allReceived = true;
        $anyReceived = false;

        foreach ($request->items as $poItemId => $data) {
            $poItem = PoItem::findOrFail($poItemId);
            $qty = (float) $data['quantity'];

            if ($qty <= 0) {
                continue;
            }

            $receipt->items()->create([
                'po_item_id' => $poItem->id,
                'quantity'   => $qty,
                'condition'  => $data['condition'] ?? 'ok',
            ]);

            $poItem->update(['received_qty' => $poItem->received_qty + $qty]);
            $anyReceived = true;

            if ($poItem->received_qty < $poItem->quantity) {
                $allReceived = false;
            }
        }

        if (! $anyReceived) {
            $receipt->delete();

            return back()->withErrors(['No quantities were received.']);
        }

        $purchaseOrder->update([
            'status' => $allReceived ? 'received' : 'partially_received',
        ]);

        AuditLog::record('goods_received', $receipt, [], $receipt->toArray());

        return back()->with('success', 'Goods received recorded.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->organization_id === currentOrganization()->id, 403);

        $before = $purchaseOrder->toArray();
        $purchaseOrder->update(['status' => 'cancelled']);

        AuditLog::record('purchase_order_cancelled', $purchaseOrder, $before, $purchaseOrder->toArray());

        return back()->with('success', 'Purchase order cancelled.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        if ($purchaseOrder->isReceived()) {
            return back()->withErrors(['Received purchase orders cannot be deleted.']);
        }

        AuditLog::record('purchase_order_removed', $purchaseOrder, $purchaseOrder->toArray(), []);
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order removed.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'supplier_id'      => ['required', 'exists:suppliers,id'],
            'contract_id'      => ['nullable', 'exists:contracts,id'],
            'tender_id'        => ['nullable', 'exists:tenders,id'],
            'order_date'       => ['nullable', 'date'],
            'expected_delivery'=> ['nullable', 'date'],
            'currency'         => ['nullable', 'string', 'max:10'],
            'items'            => ['required', 'array', 'min:1'],
        ]);
    }
}
