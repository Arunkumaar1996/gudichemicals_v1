<?php

namespace App\Services\Purchasing;

use App\Models\CompanySetting;
use App\Models\DocumentSequence;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\InventoryBatch;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SupplierInvoice;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Services\Inventory\InventoryService;
use Illuminate\Support\Facades\DB;

class PurchasingService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Create a Purchase Order.
     */
    public function createPurchaseOrder(
        int $vendorId,
        int $warehouseId,
        string $orderDate,
        ?string $expectedDate,
        array $items, // Array of ['product_id', 'unit_id', 'quantity', 'unit_price', 'tax_rate']
        ?string $notes = null,
        ?int $userId = null
    ): PurchaseOrder {
        $company = CompanySetting::current();
        $poNumber = DocumentSequence::getNextSequence(
            documentType: 'po',
            fyCode: $company->fy_code ?? '2026-27',
            prefix: 'PO'
        );

        return DB::transaction(function () use (
            $poNumber,
            $vendorId,
            $warehouseId,
            $orderDate,
            $expectedDate,
            $items,
            $notes,
            $userId
        ) {
            $subtotal = 0.0;
            $taxTotal = 0.0;

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'vendor_id' => $vendorId,
                'warehouse_id' => $warehouseId,
                'order_date' => $orderDate,
                'expected_date' => $expectedDate,
                'subtotal' => 0.0,
                'tax_total' => 0.0,
                'grand_total' => 0.0,
                'status' => 'draft',
                'created_by' => $userId ?? auth()->id(),
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $lineSub = round($qty * $price, 4);
                $taxRate = (float)($item['tax_rate'] ?? 18);
                $taxAmount = round($lineSub * ($taxRate / 100), 4);
                $total = round($lineSub + $taxAmount, 2);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $qty,
                    'received_quantity' => 0.0,
                    'unit_price' => $price,
                    'discount_amount' => 0.0,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $total,
                ]);

                $subtotal += $lineSub;
                $taxTotal += $taxAmount;
            }

            $po->subtotal = $subtotal;
            $po->tax_total = $taxTotal;
            $po->grand_total = round($subtotal + $taxTotal, 2);
            $po->save();

            return $po;
        });
    }

    /**
     * Post Goods Receipt Note (GRN) and update stock atomically.
     */
    public function postGoodsReceipt(
        int $vendorId,
        int $warehouseId,
        string $receiptDate,
        array $items, // Array of ['product_id', 'quantity', 'unit_cost', 'tax_rate', 'batch_number', 'supplier_lot_number', 'mfg_date', 'expiry_date', 'purchase_order_item_id']
        ?int $purchaseOrderId = null,
        ?string $supplierChallanNo = null,
        ?string $supplierInvoiceNo = null,
        ?string $notes = null,
        ?int $userId = null
    ): GoodsReceipt {
        $company = CompanySetting::current();
        $grnNumber = DocumentSequence::getNextSequence(
            documentType: 'grn',
            fyCode: $company->fy_code ?? '2026-27',
            prefix: 'GRN'
        );

        return DB::transaction(function () use (
            $grnNumber,
            $purchaseOrderId,
            $vendorId,
            $warehouseId,
            $receiptDate,
            $supplierChallanNo,
            $supplierInvoiceNo,
            $notes,
            $userId,
            $items
        ) {
            $grn = GoodsReceipt::create([
                'grn_number' => $grnNumber,
                'purchase_order_id' => $purchaseOrderId,
                'vendor_id' => $vendorId,
                'warehouse_id' => $warehouseId,
                'receipt_date' => $receiptDate,
                'supplier_challan_no' => $supplierChallanNo,
                'supplier_invoice_no' => $supplierInvoiceNo,
                'status' => 'posted',
                'received_by' => $userId ?? auth()->id(),
                'notes' => $notes,
            ]);

            $totalInvoiceAmount = 0.0;
            $totalTaxAmount = 0.0;
            $totalSubtotal = 0.0;

            foreach ($items as $itemData) {
                $qty = (float)$itemData['quantity'];
                $cost = (float)$itemData['unit_cost'];
                $taxRate = (float)($itemData['tax_rate'] ?? 18);
                $lineSub = round($qty * $cost, 4);
                $lineTax = round($lineSub * ($taxRate / 100), 4);
                $lineTotal = round($lineSub + $lineTax, 2);

                $batchId = null;
                $batchNumber = $itemData['batch_number'] ?? null;
                if (!empty($batchNumber)) {
                    $batch = InventoryBatch::firstOrCreate(
                        ['product_id' => $itemData['product_id'], 'batch_number' => $batchNumber],
                        [
                            'supplier_lot_number' => $itemData['supplier_lot_number'] ?? null,
                            'mfg_date' => $itemData['mfg_date'] ?? null,
                            'expiry_date' => $itemData['expiry_date'] ?? null,
                            'cost_per_unit' => $cost,
                            'is_active' => true,
                        ]
                    );
                    $batchId = $batch->id;
                }

                GoodsReceiptItem::create([
                    'goods_receipt_id' => $grn->id,
                    'purchase_order_item_id' => $itemData['purchase_order_item_id'] ?? null,
                    'product_id' => $itemData['product_id'],
                    'batch_number' => $batchNumber,
                    'supplier_lot_number' => $itemData['supplier_lot_number'] ?? null,
                    'mfg_date' => $itemData['mfg_date'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'received_quantity' => $qty,
                    'unit_cost' => $cost,
                    'tax_rate' => $taxRate,
                    'total_cost' => $lineTotal,
                    'batch_id' => $batchId,
                ]);

                // Increment Inventory atomically
                $this->inventoryService->addStock(
                    productId: $itemData['product_id'],
                    warehouseId: $warehouseId,
                    quantity: $qty,
                    unitCost: $cost,
                    movementType: 'purchase_receipt',
                    batchId: $batchId,
                    referenceType: GoodsReceipt::class,
                    referenceId: $grn->id,
                    notes: "GRN #{$grnNumber}",
                    userId: $userId ?? auth()->id()
                );

                // Update PO Item received quantity if linked
                if (!empty($itemData['purchase_order_item_id'])) {
                    $poItem = PurchaseOrderItem::find($itemData['purchase_order_item_id']);
                    if ($poItem) {
                        $poItem->received_quantity += $qty;
                        $poItem->save();
                    }
                }

                $totalSubtotal += $lineSub;
                $totalTaxAmount += $lineTax;
                $totalInvoiceAmount += $lineTotal;
            }

            // If PO is linked, check if all items received and update status
            if ($purchaseOrderId) {
                $po = PurchaseOrder::with('items')->find($purchaseOrderId);
                if ($po) {
                    $allReceived = $po->items->every(fn($i) => (float)$i->received_quantity >= (float)$i->quantity);
                    $po->status = $allReceived ? 'received' : 'partial';
                    $po->save();
                }
            }

            // Create Supplier Bill record if supplier invoice number provided
            if (!empty($supplierInvoiceNo)) {
                $bill = SupplierInvoice::create([
                    'bill_number' => 'BILL-' . $grnNumber,
                    'supplier_invoice_no' => $supplierInvoiceNo,
                    'goods_receipt_id' => $grn->id,
                    'vendor_id' => $vendorId,
                    'bill_date' => $receiptDate,
                    'due_date' => now()->addDays(30)->toDateString(),
                    'subtotal' => $totalSubtotal,
                    'tax_amount' => $totalTaxAmount,
                    'total_amount' => $totalInvoiceAmount,
                    'paid_amount' => 0.0,
                    'status' => 'unpaid',
                ]);

                // Update Vendor outstanding payable balance
                $vendor = Vendor::find($vendorId);
                if ($vendor) {
                    $vendor->current_balance = (float)$vendor->current_balance + $totalInvoiceAmount;
                    $vendor->save();
                }
            }

            return $grn;
        });
    }

    /**
     * Record a vendor payment.
     */
    public function recordPayment(
        int $vendorId,
        float $amount,
        string $paymentMethod,
        string $paymentDate,
        ?int $supplierInvoiceId = null,
        ?string $referenceNo = null,
        ?string $notes = null
    ): VendorPayment {
        return DB::transaction(function () use (
            $vendorId,
            $amount,
            $paymentMethod,
            $paymentDate,
            $supplierInvoiceId,
            $referenceNo,
            $notes
        ) {
            $company = CompanySetting::current();
            $paymentNumber = DocumentSequence::getNextSequence(
                documentType: 'receipt',
                fyCode: $company->fy_code ?? '2026-27',
                prefix: 'VPAY'
            );

            $payment = VendorPayment::create([
                'payment_number' => $paymentNumber,
                'vendor_id' => $vendorId,
                'supplier_invoice_id' => $supplierInvoiceId,
                'payment_date' => $paymentDate,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_no' => $referenceNo,
                'notes' => $notes,
                'created_by' => auth()->id(),
            ]);

            // Adjust invoice if linked
            if ($supplierInvoiceId) {
                $inv = SupplierInvoice::find($supplierInvoiceId);
                if ($inv) {
                    $inv->paid_amount += $amount;
                    if ($inv->paid_amount >= $inv->total_amount) {
                        $inv->status = 'paid';
                    } else {
                        $inv->status = 'partially_paid';
                    }
                    $inv->save();
                }
            }

            // Deduct Vendor payable balance
            $vendor = Vendor::find($vendorId);
            if ($vendor) {
                $vendor->current_balance = max(0, (float)$vendor->current_balance - $amount);
                $vendor->save();
            }

            return $payment;
        });
    }
}
