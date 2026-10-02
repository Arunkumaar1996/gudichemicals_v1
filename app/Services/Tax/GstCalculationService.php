<?php

namespace App\Services\Tax;

use App\Models\CompanySetting;
use App\Models\Customer;

class GstCalculationService
{
    /**
     * Determine whether the transaction is inter-state based on supplier and customer state codes.
     */
    public function isInterstate(string $customerStateCode, ?string $supplierStateCode = null): bool
    {
        if (!$supplierStateCode) {
            $company = CompanySetting::current();
            $supplierStateCode = $company->state_code ?? '27';
        }

        // Clean any leading zeros for comparison
        $cleanSupplier = ltrim(trim($supplierStateCode), '0');
        $cleanCustomer = ltrim(trim($customerStateCode), '0');

        return !empty($cleanCustomer) && ($cleanSupplier !== $cleanCustomer);
    }

    /**
     * Calculate line item tax figures.
     *
     * @param float $unitPrice
     * @param float $quantity
     * @param float $discountAmount
     * @param float $gstRate e.g. 18.00
     * @param bool $isInterstate
     * @param bool $isTaxInclusive
     */
    public function calculateLineItem(
        float $unitPrice,
        float $quantity,
        float $discountAmount = 0.0,
        float $gstRate = 18.00,
        bool $isInterstate = false,
        bool $isTaxInclusive = false
    ): array {
        $gross = round($unitPrice * $quantity, 4);
        $discountAmount = min($gross, round($discountAmount, 4));

        if ($isTaxInclusive) {
            $effectiveGross = $gross - $discountAmount;
            $taxable = round($effectiveGross / (1 + ($gstRate / 100)), 4);
            $taxAmount = round($effectiveGross - $taxable, 4);
        } else {
            $taxable = round($gross - $discountAmount, 4);
            $taxAmount = round($taxable * ($gstRate / 100), 4);
        }

        if ($isInterstate) {
            $cgstRate = 0.0;
            $cgstAmount = 0.0;
            $sgstRate = 0.0;
            $sgstAmount = 0.0;
            $igstRate = $gstRate;
            $igstAmount = $taxAmount;
        } else {
            $halfRate = round($gstRate / 2, 2);
            $halfTax = round($taxAmount / 2, 4);
            $cgstRate = $halfRate;
            $cgstAmount = $halfTax;
            $sgstRate = $halfRate;
            $sgstAmount = $halfTax;
            $igstRate = 0.0;
            $igstAmount = 0.0;
        }

        $lineTotal = round($taxable + $taxAmount, 2);

        return [
            'gross_amount' => $gross,
            'discount_amount' => $discountAmount,
            'taxable_amount' => $taxable,
            'gst_rate' => $gstRate,
            'cgst_rate' => $cgstRate,
            'cgst_amount' => $cgstAmount,
            'sgst_rate' => $sgstRate,
            'sgst_amount' => $sgstAmount,
            'igst_rate' => $igstRate,
            'igst_amount' => $igstAmount,
            'tax_amount' => $taxAmount,
            'line_total' => $lineTotal,
        ];
    }

    /**
     * Compute entire invoice breakdown with line item allocation and rounding.
     *
     * @param array $items Array of ['product_id', 'quantity', 'unit_price', 'discount_amount', 'gst_rate', 'is_free']
     * @param Customer $customer
     * @param float $invoiceDiscountFlat
     */
    public function calculateInvoice(array $items, Customer $customer, float $invoiceDiscountFlat = 0.0): array
    {
        $company = CompanySetting::current();
        $isInterstate = $this->isInterstate($customer->state_code ?? '27', $company->state_code ?? '27');

        $computedItems = [];
        $subtotal = 0.0;
        $itemDiscountTotal = 0.0;
        $taxableTotal = 0.0;
        $cgstTotal = 0.0;
        $sgstTotal = 0.0;
        $igstTotal = 0.0;

        foreach ($items as $item) {
            $qty = (float)($item['quantity'] ?? 1);
            $price = (float)($item['unit_price'] ?? 0);
            $disc = (float)($item['discount_amount'] ?? 0);
            $rate = (float)($item['gst_rate'] ?? 18);
            $isFree = (bool)($item['is_free'] ?? false);

            if ($isFree) {
                $price = 0.0;
                $disc = 0.0;
            }

            $line = $this->calculateLineItem($price, $qty, $disc, $rate, $isInterstate, false);
            $line['product_id'] = $item['product_id'];
            $line['product_name'] = $item['product_name'] ?? '';
            $line['sku'] = $item['sku'] ?? '';
            $line['hsn_code'] = $item['hsn_code'] ?? '3402';
            $line['unit_id'] = $item['unit_id'] ?? 1;
            $line['batch_id'] = $item['batch_id'] ?? null;
            $line['quantity'] = $qty;
            $line['unit_price'] = $price;
            $line['is_free_item'] = $isFree;
            $line['cost_price'] = (float)($item['cost_price'] ?? 0);

            $computedItems[] = $line;

            $subtotal += $line['gross_amount'];
            $itemDiscountTotal += $line['discount_amount'];
            $taxableTotal += $line['taxable_amount'];
            $cgstTotal += $line['cgst_amount'];
            $sgstTotal += $line['sgst_amount'];
            $igstTotal += $line['igst_amount'];
        }

        // Apply additional invoice-level discount if any
        $totalDiscount = $itemDiscountTotal + $invoiceDiscountFlat;
        $rawGrandTotal = $taxableTotal + $cgstTotal + $sgstTotal + $igstTotal - $invoiceDiscountFlat;
        $roundedGrandTotal = round($rawGrandTotal);
        $roundingAdjustment = round($roundedGrandTotal - $rawGrandTotal, 2);

        return [
            'is_interstate' => $isInterstate,
            'place_of_supply' => ($customer->state_name ?? 'Maharashtra') . " (" . ($customer->state_code ?? '27') . ")",
            'subtotal' => round($subtotal, 4),
            'discount_total' => round($totalDiscount, 4),
            'taxable_amount' => round(max(0, $taxableTotal - $invoiceDiscountFlat), 4),
            'cgst_amount' => round($cgstTotal, 4),
            'sgst_amount' => round($sgstTotal, 4),
            'igst_amount' => round($igstTotal, 4),
            'rounding_adjustment' => $roundingAdjustment,
            'grand_total' => max(0, $roundedGrandTotal),
            'items' => $computedItems,
        ];
    }
}
