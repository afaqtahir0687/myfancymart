<?php

if (!function_exists('isDiscountActive')) {
    /**
     * Check if product discount is currently active based on dates
     */
    function isDiscountActive($product): bool
    {
        if (!$product || !isset($product->discount)) {
            return false;
        }
        
        // Check if discount is enabled
        if (isset($product->discount_is_active) && !$product->discount_is_active) {
            return false;
        }
        
        // Check if discount amount is valid
        if ($product->discount <= 0) {
            return false;
        }
        
        $today = date('Y-m-d');
        
        // If no dates are set, use legacy behavior
        if (!isset($product->discount_start_date) && !isset($product->discount_end_date)) {
            return true;
        }
        
        // Check start date
        if (isset($product->discount_start_date) && $product->discount_start_date) {
            if ($today < $product->discount_start_date) {
                return false;
            }
        }
        
        // Check end date
        if (isset($product->discount_end_date) && $product->discount_end_date) {
            if ($today > $product->discount_end_date) {
                return false;
            }
        }
        
        return true;
    }
}

if (!function_exists('getDiscountDaysRemaining')) {
    /**
     * Get remaining days for product discount
     */
    function getDiscountDaysRemaining($product): int
    {
        if (!$product || !isDiscountActive($product)) {
            return 0;
        }
        
        // If no end date is set, return 0 (no limit)
        if (!isset($product->discount_end_date) || !$product->discount_end_date) {
            return 0;
        }
        
        $today = new DateTime(date('Y-m-d'));
        $endDate = new DateTime($product->discount_end_date);
        
        if ($today > $endDate) {
            return 0;
        }
        
        $interval = $today->diff($endDate);
        return (int)$interval->days + 1; // Include today
    }
}

if (!function_exists('getDiscountStatusText')) {
    /**
     * Get discount status text for display
     */
    function getDiscountStatusText($product): string
    {
        if (!$product) {
            return translate('discount_not_available');
        }
        
        if ($product->discount <= 0) {
            return translate('no_discount');
        }
        
        // Check if discount is active
        if (!isDiscountActive($product)) {
            return translate('discount_expired');
        }
        
        $daysRemaining = getDiscountDaysRemaining($product);
        
        if ($daysRemaining > 0) {
            if ($daysRemaining == 1) {
                return translate('discount_ends_today');
            } elseif ($daysRemaining <= 7) {
                return translate('discount_ends_in') . ' ' . $daysRemaining . ' ' . translate('days');
            } else {
                return translate('discount_active');
            }
        }
        
        return translate('discount_active');
    }
}

if (!function_exists('getProductDiscountAmount')) {
    /**
     * Get current discount amount for product
     */
    function getProductDiscountAmount($product): float
    {
        if (!isDiscountActive($product)) {
            return 0;
        }
        
        return (float)($product->discount ?? 0);
    }
}

if (!function_exists('getProductDiscountedPrice')) {
    /**
     * Get discounted price for product
     */
    function getProductDiscountedPrice($product): float
    {
        if (!$product) {
            return 0;
        }
        
        $basePrice = (float)($product->unit_price ?? 0);
        $discountAmount = getProductDiscountAmount($product);
        
        return max(0, $basePrice - $discountAmount);
    }
}

if (!function_exists('getDiscountPercentage')) {
    /**
     * Get discount percentage for display
     */
    function getDiscountPercentage($product): float
    {
        if (!$product || !isDiscountActive($product)) {
            return 0;
        }
        
        $basePrice = (float)($product->unit_price ?? 0);
        $discountAmount = getProductDiscountAmount($product);
        
        if ($basePrice <= 0) {
            return 0;
        }
        
        return round(($discountAmount / $basePrice) * 100, 2);
    }
}

if (!function_exists('formatDiscountDisplay')) {
    /**
     * Format discount for display with percentage
     */
    function formatDiscountDisplay($product): string
    {
        if (!isDiscountActive($product)) {
            return '';
        }
        
        $discountAmount = getProductDiscountAmount($product);
        $percentage = getDiscountPercentage($product);
        
        if ($product->discount_type == 'percent') {
            return $percentage . '% ' . translate('off');
        } else {
            return currencyConverter($discountAmount) . ' ' . translate('off');
        }
    }
}

if (!function_exists('getDiscountBadgeHtml')) {
    /**
     * Get discount badge HTML for product display
     */
    function getDiscountBadgeHtml($product): string
    {
        if (!isDiscountActive($product)) {
            return '';
        }
        
        $daysRemaining = getDiscountDaysRemaining($product);
        $discountText = formatDiscountDisplay($product);
        
        $badgeClass = 'badge-danger';
        if ($daysRemaining <= 3) {
            $badgeClass = 'badge-warning';
        } elseif ($daysRemaining <= 7) {
            $badgeClass = 'badge-info';
        }
        
        $html = '<div class="discount-badge ' . $badgeClass . '">';
        $html .= '<span class="discount-amount">' . $discountText . '</span>';
        
        if ($daysRemaining > 0 && $daysRemaining <= 30) {
            $html .= '<span class="discount-timer">' . translate('ends_in') . ' ' . $daysRemaining . ' ' . translate('days') . '</span>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('getDiscountCountdownData')) {
    /**
     * Get countdown data for JavaScript
     */
    function getDiscountCountdownData($product): array
    {
        if (!$product || !isDiscountActive($product) || !isset($product->discount_end_date)) {
            return [];
        }
        
        return [
            'end_date' => $product->discount_end_date,
            'days_remaining' => getDiscountDaysRemaining($product),
            'is_active' => true,
            'message' => getDiscountStatusText($product)
        ];
    }
}
