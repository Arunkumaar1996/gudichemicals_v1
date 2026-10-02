<?php

namespace App\Services\Sales;

use App\Exceptions\BillingException;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\DocumentSequence;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesPayment;
use App\Services\Inventory\InventoryService;
use App\Services\Notifications\NotificationService;
use App\Services\Tax\GstCalculationService;
use Illuminate\Support\Facades\DB;

class InvoicePostingService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected GstCalculationService $gstService,
        protected PromotionCalculationService $promotionService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Preview calculation for POS / Cart without committing.
     */
    public function calculatePreview(array $rawCartItems, int $customerId, string $priceTier = 'retail'): array
    {
        $customer = Customer::findOrFail($customerId);

        $preparedItems = [];
        foreach ($rawCartItems as $raw) {
            $product = Product::findOrFail($raw['product_id']);
            $qty = (float)($raw['quantity'] ?? 1);
            $unitPrice = $priceTier === 'wholesale' ? (float)$product->wholesale_price : (float)$product->retail_price;

            // Optional manual discount if provided
            $disc = (float)($raw['discount_amount'] ?? 0);

            $preparedItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'hsn_code' => $product->hsn_code,
                'unit_id' => $product->unit_id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'discount_amount' => $disc,
                'gst_rate' => (float)$product->gst_rate,
                'is_free' => false,
                'cost_price' => (float)$product->purchase_cost,
                'batch_id' => $raw['batch_id'] ?? null,
            ];
        }

        // Apply promotions
        $promoResult = $this->promotionService->applyPromotions($preparedItems, $customer);
        $finalItems = $promoResult['items'];
        $invoiceDiscount = $promoResult['invoice_discount'];

        // Compute GST and totals
        $calc = $this->gstService->calculateInvoice($finalItems, $customer, $invoiceDiscount);
        $calc['applied_promotions'] = $promoResult['applied_promotions'];

        return $calc;
    }

    /**
     * Atomically create and post a sales invoice.
     *
     * @param array $rawCartItems Array of ['product_id', 'quantity', 'batch_id' (optional), ...]
     * @param int $customerId
     * @param int $warehouseId
     * @param string $priceTier 'retail' or 'wholesale'
     * @param array $payments Array of ['amount', 'payment_method', 'reference_no']
     * @param string|null $notes
     * @param int|null $userId
     * @return SalesInvoice
     */
    public function postInvoice(
        array $rawCartItems,
        int $customerId,
        int $warehouseId,
        string $priceTier,
        array $payments,
        ?string $notes = null,
        ?int $userId = null
    ): SalesInvoice {
        if (empty($rawCartItems)) {
            throw new BillingException('Cannot post an invoice with an empty cart.');
        }

        $customer = Customer::findOrFail($customerId);
        $company = CompanySetting::current();

        // 1. Calculate authoritative totals
        $calc = $this->calculatePreview($rawCartItems, $customerId, $priceTier);

        return DB::transaction(function () use (
            $calc,
            $customer,
            $company,
            $warehouseId,
            $priceTier,
            $payments,
            $notes,
            $userId
        ) {
            // 2. Generate sequential invoice number
            $invoiceNumber = DocumentSequence::getNextSequence(
                documentType: 'invoice',
                fyCode: $company->fy_code ?? '2026-27',
                prefix: $company->invoice_prefix ?? 'GC'
            );

            // 3. Sum payments
            $totalPaid = 0.0;
            foreach ($payments as $p) {
                $totalPaid += (float)($p['amount'] ?? 0);
            }
            $grandTotal = (float)$calc['grand_total'];
            $dueAmount = max(0, round($grandTotal - $totalPaid, 2));

            $paymentStatus = 'paid';
            if ($totalPaid <= 0) {
                $paymentStatus = 'unpaid';
            } elseif ($dueAmount > 0) {
                $paymentStatus = 'partially_paid';
            }

            // 4. Create Sales Invoice Record
            $invoice = SalesInvoice::create([
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now()->toDateString(),
                'customer_id' => $customer->id,
                'warehouse_id' => $warehouseId,
                'price_tier' => $priceTier,
                'is_interstate' => $calc['is_interstate'],
                'place_of_supply' => $calc['place_of_supply'],
                'subtotal' => $calc['subtotal'],
                'discount_total' => $calc['discount_total'],
                'taxable_amount' => $calc['taxable_amount'],
                'cgst_amount' => $calc['cgst_amount'],
                'sgst_amount' => $calc['sgst_amount'],
                'igst_amount' => $calc['igst_amount'],
                'rounding_adjustment' => $calc['rounding_adjustment'],
                'grand_total' => $grandTotal,
                'paid_amount' => $totalPaid,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'status' => 'posted',
                'notes' => $notes,
                'created_by' => $userId ?? auth()->id(),
            ]);

            // 5. Create Line Items & Deduct Stock
            foreach ($calc['items'] as $itemData) {
                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $itemData['product_id'],
                    'batch_id' => $itemData['batch_id'] ?? null,
                    'product_name' => $itemData['product_name'],
                    'sku' => $itemData['sku'],
                    'hsn_code' => $itemData['hsn_code'],
                    'quantity' => $itemData['quantity'],
                    'unit_id' => $itemData['unit_id'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_amount' => $itemData['discount_amount'],
                    'taxable_amount' => $itemData['taxable_amount'],
                    'gst_rate' => $itemData['gst_rate'],
                    'cgst_rate' => $itemData['cgst_rate'],
                    'cgst_amount' => $itemData['cgst_amount'],
                    'sgst_rate' => $itemData['sgst_rate'],
                    'sgst_amount' => $itemData['sgst_amount'],
                    'igst_rate' => $itemData['igst_rate'],
                    'igst_amount' => $itemData['igst_amount'],
                    'line_total' => $itemData['line_total'],
                    'is_free_item' => $itemData['is_free_item'],
                    'cost_price' => $itemData['cost_price'],
                ]);

                // Stock deduction
                $this->inventoryService->deductStock(
                    productId: $itemData['product_id'],
                    warehouseId: $warehouseId,
                    quantity: (float)$itemData['quantity'],
                    movementType: 'sales_issue',
                    batchId: $itemData['batch_id'] ?? null,
                    referenceType: SalesInvoice::class,
                    referenceId: $invoice->id,
                    notes: "Invoice #{$invoiceNumber}",
                    userId: $userId ?? auth()->id()
                );
            }

            // 6. Record Payments
            foreach ($payments as $payment) {
                if ((float)($payment['amount'] ?? 0) > 0) {
                    SalesPayment::create([
                        'sales_invoice_id' => $invoice->id,
                        'customer_id' => $customer->id,
                        'payment_date' => now()->toDateString(),
                        'amount' => (float)$payment['amount'],
                        'payment_method' => $payment['payment_method'],
                        'reference_no' => $payment['reference_no'] ?? null,
                        'received_by' => $userId ?? auth()->id(),
                    ]);
                }
            }

            // 7. Update Customer Outstanding Balance if credit due
            if ($dueAmount > 0) {
                $customer->current_balance = (float)$customer->current_balance + $dueAmount;
                $customer->save();
            }

            // 8. Queue notification (Email / WhatsApp)
            $this->notificationService->queueInvoiceNotification($invoice);

            return $invoice;
        });
    }
}
