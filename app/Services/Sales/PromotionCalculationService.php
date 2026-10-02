<?php

namespace App\Services\Sales;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Promotion;

class PromotionCalculationService
{
    /**
     * Evaluate active promotions against a list of cart items.
     * Returns an enriched array of items with discount amounts and auto-added free promotional items.
     *
     * @param array $cartItems Array of ['product_id', 'quantity', 'unit_price', ...]
     * @param Customer $customer
     * @return array ['items' => ..., 'applied_promotions' => ..., 'invoice_discount' => ...]
     */
    public function applyPromotions(array $cartItems, Customer $customer): array
    {
        $today = now()->toDateString();
        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->with(['rules.buyProduct', 'rules.getProduct'])
            ->orderBy('priority', 'asc')
            ->get();

        $processedItems = $cartItems;
        $appliedPromotions = [];
        $invoiceDiscount = 0.0;

        foreach ($promotions as $promo) {
            foreach ($promo->rules as $rule) {
                // Check customer type eligibility
                if ($rule->customer_type !== 'all' && $rule->customer_type !== $customer->customer_type) {
                    continue;
                }

                switch ($promo->type) {
                    case 'bogo':
                    case 'b2g1':
                    case 'bxgy':
                        // If buy_product_id is in cart
                        foreach ($processedItems as &$item) {
                            if ($item['product_id'] == $rule->buy_product_id && !$item['is_free']) {
                                $buyQty = (float)$item['quantity'];
                                $reqQty = (float)$rule->buy_quantity > 0 ? (float)$rule->buy_quantity : 1;
                                $rewardQty = (float)$rule->get_quantity > 0 ? (float)$rule->get_quantity : 1;

                                if ($buyQty >= $reqQty) {
                                    $times = floor($buyQty / $reqQty);
                                    $freeQty = $times * $rewardQty;
                                    $freeProductId = $rule->get_product_id ?? $item['product_id'];

                                    // Add free reward item line
                                    $freeProduct = Product::find($freeProductId);
                                    if ($freeProduct) {
                                        $processedItems[] = [
                                            'product_id' => $freeProduct->id,
                                            'product_name' => $freeProduct->name . ' (Offer: ' . $promo->name . ')',
                                            'sku' => $freeProduct->sku,
                                            'hsn_code' => $freeProduct->hsn_code,
                                            'unit_id' => $freeProduct->unit_id,
                                            'quantity' => $freeQty,
                                            'unit_price' => 0.0,
                                            'discount_amount' => 0.0,
                                            'gst_rate' => (float)$freeProduct->gst_rate,
                                            'is_free' => true,
                                            'cost_price' => (float)$freeProduct->purchase_cost,
                                        ];

                                        $appliedPromotions[] = [
                                            'name' => $promo->name,
                                            'type' => $promo->type,
                                            'details' => "Free {$freeQty} {$freeProduct->name}",
                                        ];
                                    }
                                }
                            }
                        }
                        unset($item);
                        break;

                    case 'percent_discount':
                        foreach ($processedItems as &$item) {
                            if ((!$rule->buy_product_id || $item['product_id'] == $rule->buy_product_id) && !$item['is_free']) {
                                if ((float)$rule->discount_pct > 0) {
                                    $disc = round(($item['unit_price'] * $item['quantity']) * ((float)$rule->discount_pct / 100), 4);
                                    $item['discount_amount'] = max((float)($item['discount_amount'] ?? 0), $disc);
                                    $appliedPromotions[] = [
                                        'name' => $promo->name,
                                        'type' => $promo->type,
                                        'details' => "{$rule->discount_pct}% off on {$item['product_name']}",
                                    ];
                                }
                            }
                        }
                        unset($item);
                        break;

                    case 'flat_discount':
                        if ((float)$rule->discount_flat > 0) {
                            $invoiceDiscount += (float)$rule->discount_flat;
                            $appliedPromotions[] = [
                                'name' => $promo->name,
                                'type' => $promo->type,
                                'details' => "₹{$rule->discount_flat} flat discount",
                            ];
                        }
                        break;

                    case 'slab_discount':
                        foreach ($processedItems as &$item) {
                            if ($item['product_id'] == $rule->buy_product_id && !$item['is_free']) {
                                if ((float)$item['quantity'] >= (float)$rule->buy_quantity && (float)$rule->discount_pct > 0) {
                                    $disc = round(($item['unit_price'] * $item['quantity']) * ((float)$rule->discount_pct / 100), 4);
                                    $item['discount_amount'] = max((float)($item['discount_amount'] ?? 0), $disc);
                                    $appliedPromotions[] = [
                                        'name' => $promo->name,
                                        'type' => 'slab_discount',
                                        'details' => "Bulk slab {$rule->discount_pct}% off for quantity >= {$rule->buy_quantity}",
                                    ];
                                }
                            }
                        }
                        unset($item);
                        break;
                }
            }
        }

        return [
            'items' => $processedItems,
            'applied_promotions' => $appliedPromotions,
            'invoice_discount' => $invoiceDiscount,
        ];
    }
}
