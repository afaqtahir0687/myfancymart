<?php

if (!function_exists('calculateCorrectTax')) {
    function calculateCorrectTax(array|object $product, float $basePrice = null): float
    {
        // Correct tax calculation: tax on discounted price
        $taxAmount = 0;
        
        if ($product->tax > 0 && ($product->tax_model == 'exclude' || $product->tax_model == 'hidden')) {
            // Get base price if not provided
            if ($basePrice === null) {
                $basePrice = getProductBasePrice($product);
            }
            
            // Get discount amount
            $discountAmount = getProductDiscountAmount($product, $basePrice);
            
            // Calculate taxable amount (price - discount)
            $taxableAmount = $basePrice - $discountAmount;
            
            // Calculate tax on discounted price
            $taxAmount = ($taxableAmount / 100) * $product->tax;
        }
        
        return round($taxAmount, 2);
    }
}

if (!function_exists('getProductBasePrice')) {
    function getProductBasePrice(array|object $product): float
    {
        $productUnitPrice = $product->unit_price;
        
        // Check for variations
        if (isset($product->variation) && !empty($product->variation)) {
            $variations = json_decode($product->variation);
            if (count($variations) > 0) {
                $productUnitPrice = $variations[0]->price;
            }
        }
        
        // Check for digital variations
        if (isset($product->digitalVariation) && count($product->digitalVariation) > 0) {
            $digitalVariations = $product->digitalVariation->toArray();
            if (count($digitalVariations) > 0) {
                $productUnitPrice = $digitalVariations[0]['price'];
            }
        }
        
        return $productUnitPrice;
    }
}

if (!function_exists('getProductDiscountAmount')) {
    function getProductDiscountAmount(array|object $product, float $basePrice): float
    {
        $discountAmount = 0;
        
        if ($product->discount > 0) {
            if ($product->discount_type == 'percent') {
                $discountAmount = ($basePrice * $product->discount) / 100;
            } else {
                $discountAmount = $product->discount;
            }
        }
        
        return round($discountAmount, 2);
    }
}

if (!function_exists('calculateFinalPriceWithTax')) {
    function calculateFinalPriceWithTax(array|object $product, float $basePrice = null): float
    {
        if ($basePrice === null) {
            $basePrice = getProductBasePrice($product);
        }
        
        $discountAmount = getProductDiscountAmount($product, $basePrice);
        $taxableAmount = $basePrice - $discountAmount;
        $taxAmount = calculateCorrectTax($product, $basePrice);
        
        // Final price = discounted price + tax
        return round($taxableAmount + $taxAmount, 2);
    }
}

if (!function_exists('calculateHiddenTax')) {
    function calculateHiddenTax(array|object $product): float
    {
        // Legacy function - use calculateCorrectTax instead
        return calculateCorrectTax($product);
    }
}

if (!function_exists('getPriceWithoutTaxForDisplay')) {
    function getPriceWithoutTaxForDisplay(array|object $product): float
    {
        // Return base price without tax for user display
        return getProductBasePrice($product);
    }
}

if (!function_exists('getAdminPriceWithTax')) {
    function getAdminPriceWithTax(array|object $product): float
    {
        // Return final price with tax for admin calculations
        return calculateFinalPriceWithTax($product);
    }
}

if (!function_exists('getCustomerDisplayPrice')) {
    function getCustomerDisplayPrice(array|object $product): float
    {
        // Price shown to customer (without tax for hidden mode)
        $basePrice = getProductBasePrice($product);
        $discountAmount = getProductDiscountAmount($product, $basePrice);
        
        return round($basePrice - $discountAmount, 2);
    }
}

if (!function_exists('getTaxAmountForOrder')) {
    function getTaxAmountForOrder(array|object $product, float $quantity = 1): float
    {
        $taxAmount = calculateCorrectTax($product);
        return round($taxAmount * $quantity, 2);
    }
}

if (!function_exists('calculateCartTotalWithCorrectTax')) {
    function calculateCartTotalWithCorrectTax(array|object $product, float $price, float $quantity): array
    {
        $basePrice = getProductBasePrice($product);
        $discountAmount = getProductDiscountAmount($product, $basePrice);
        
        // Calculate taxable amount (discounted price)
        $taxableAmount = $basePrice - $discountAmount;
        
        // Calculate tax on discounted price
        $taxAmount = 0;
        if ($product->tax_model == 'exclude' || $product->tax_model == 'hidden') {
            $taxAmount = ($taxableAmount / 100) * $product->tax;
        }
        
        // Final calculation
        $subtotal = ($basePrice - $discountAmount) * $quantity;
        $totalTax = $taxAmount * $quantity;
        $totalPrice = $subtotal + $totalTax;
        
        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($totalTax, 2),
            'total_price' => round($totalPrice, 2),
            'display_price' => round(($basePrice - $discountAmount), 2), // Price shown to customer
            'taxable_amount' => round($taxableAmount, 2)
        ];
    }
}

if (!function_exists('calculateOrderDetailTax')) {
    function calculateOrderDetailTax(array|object $cartItem): array
    {
        // Calculate correct tax for order details based on cart item
        $product = (object)[
            'unit_price' => $cartItem['price'],
            'discount' => $cartItem['discount'] ?? 0,
            'discount_type' => $cartItem['discount_type'] ?? 'flat',
            'tax' => $cartItem['tax_rate'] ?? 0,
            'tax_model' => $cartItem['tax_model'] ?? 'exclude',
            'variation' => $cartItem['variations'] ?? null,
            'digitalVariation' => null
        ];
        
        $quantity = $cartItem['quantity'];
        $basePrice = getProductBasePrice($product);
        $discountAmount = getProductDiscountAmount($product, $basePrice);
        
        // Calculate taxable amount (discounted price)
        $taxableAmount = $basePrice - $discountAmount;
        
        // Calculate tax on discounted price
        $taxAmount = 0;
        if ($product->tax_model == 'exclude' || $product->tax_model == 'hidden') {
            $taxAmount = ($taxableAmount / 100) * $product->tax;
        }
        
        return [
            'product_price' => $basePrice,
            'discount_amount' => $discountAmount,
            'taxable_amount' => $taxableAmount,
            'tax_amount_per_unit' => $taxAmount,
            'total_tax' => round($taxAmount * $quantity, 2),
            'final_price_per_unit' => $taxableAmount + $taxAmount,
            'display_price_per_unit' => $taxableAmount, // What customer sees
        ];
    }
}
