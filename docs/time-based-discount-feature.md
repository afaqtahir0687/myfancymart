# Time-Based Discount Feature Documentation

## Overview
This feature allows administrators to set discounts with specific start and end dates. The system automatically handles discount activation, expiration, and displays countdown timers to customers.

## Features Implemented

### 1. Database Schema
Added new fields to `products` table:
- `discount_start_date` (DATE, nullable) - When discount becomes active
- `discount_end_date` (DATE, nullable) - When discount expires
- `discount_is_active` (BOOLEAN, default true) - Enable/disable discount temporarily
- `discount_note` (TEXT, nullable) - Internal notes about discount

### 2. Admin Interface Updates

#### Product Add Form (`/admin/products/add`)
- **Discount Start Date**: Date picker for when discount starts
- **Discount End Date**: Date picker for when discount expires
- **Discount Active**: Dropdown to enable/disable discount
- **Discount Note**: Textarea for internal notes

#### Product Edit Form (`/admin/products/edit/{id}`)
- Same fields as add form, populated with existing values
- All fields are editable and save properly

### 3. Helper Functions Created (`app/Utils/DiscountHelper.php`)

#### Core Functions:
```php
// Check if discount is currently active
isDiscountActive($product): bool

// Get remaining days for discount
getDiscountDaysRemaining($product): int

// Get discount status text
getDiscountStatusText($product): string

// Get current discount amount
getProductDiscountAmount($product): float

// Get discounted price
getProductDiscountedPrice($product): float

// Get discount percentage
getDiscountPercentage($product): float

// Format discount for display
formatDiscountDisplay($product): string

// Get discount badge HTML
getDiscountBadgeHtml($product): string

// Get countdown data for JavaScript
getDiscountCountdownData($product): array
```

### 4. Frontend Display Updates

#### Product Detail Page
- **Discount Badge**: Shows discount amount with percentage
- **Countdown Timer**: Shows remaining days with color coding:
  - Red: ≤ 3 days remaining
  - Yellow: 4-7 days remaining  
  - Blue: 8+ days remaining
- **Price Display**: Shows original price crossed out when discount active
- **Status Messages**: "Discount ends today", "Discount ends in X days", etc.

#### CSS Styling (`/public/assets/front-end/css/discount-countdown.css`)
- Responsive design for mobile/desktop
- Animated countdown badges
- Color-coded urgency indicators
- Pulse animations for urgent discounts

### 5. Business Logic

#### Discount Activation Rules:
1. **Start Date**: Discount becomes active on this date (if set)
2. **End Date**: Discount expires at end of this date
3. **Active Flag**: Can disable discount temporarily without changing dates
4. **Legacy Support**: Products without dates use original behavior

#### Price Calculation:
- Discount only applies when `isDiscountActive()` returns true
- Tax calculated on discounted price (not original price)
- Resell profit calculated from base price (before discount)
- Cart totals include time-based discounts automatically

### 6. Automation

#### Cron Job (`php artisan discounts:expire`)
- **Daily Check**: Runs automatically to expire discounts
- **Expired Products**: Sets `discount_is_active = false`
- **Notifications**: Logs expired and expiring discounts
- **Warnings**: Alerts for discounts expiring in 3 days

### 7. API Integration

#### Flutter/Mobile App Support:
All helper functions work with API responses:
```json
{
  "discount": 15.00,
  "discount_type": "flat",
  "discount_start_date": "2024-04-21",
  "discount_end_date": "2024-04-30",
  "discount_is_active": true,
  "discount_days_remaining": 9,
  "discount_status": "Discount ends in 9 days",
  "discount_percentage": 15.00
}
```

## Implementation Guide

### Step 1: Database Migration
```bash
php artisan migrate
```

### Step 2: Update Existing Products
```sql
UPDATE products SET 
  discount_is_active = 1,
  discount_start_date = NULL,
  discount_end_date = NULL
WHERE discount > 0;
```

### Step 3: Configure Cron Job
```bash
# Add to app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('discounts:expire')->dailyAt('23:59');
}

# Run manually
php artisan discounts:expire
```

### Step 4: Test Scenarios

#### Test Case 1: Immediate Discount
- Set discount amount: $20
- Leave start/end dates empty
- Result: Discount active immediately

#### Test Case 2: Future Discount
- Set discount amount: $15
- Start date: tomorrow
- End date: +7 days
- Result: Discount becomes active tomorrow

#### Test Case 3: Expired Discount
- Set end date: yesterday
- Run cron job
- Result: Discount deactivated, price returns to normal

#### Test Case 4: Disabled Discount
- Set valid dates but `discount_is_active = false`
- Result: Discount not applied even though dates are valid

## User Experience

### Admin Side:
1. **Add Product**: See new date fields for discount scheduling
2. **Edit Product**: Modify discount dates and status
3. **Product Listing**: See discount status with days remaining
4. **Notifications**: Get alerts for expiring discounts

### Customer Side:
1. **Product Detail**: See discount countdown and amount
2. ** urgency Indicators**: Color-coded badges for time sensitivity
3. **Price Display**: Clear original vs discounted pricing
4. **Cart**: Correct totals with time-based discounts
5. **Checkout**: Accurate pricing throughout process

## Benefits

### For Business:
- **Marketing Control**: Schedule sales in advance
- **Inventory Management**: Time-sensitive promotions
- **Automated Expiration**: No manual intervention needed
- **Reporting**: Track discount performance

### For Customers:
- **Clear Communication**: Know exactly when discounts end
- **Urgency Indicators**: Visual cues for limited-time offers
- **Trust Building**: Transparent pricing and timing
- **Better Planning**: Can purchase before discounts expire

## Technical Considerations

### Performance:
- Database indexes on date fields for fast queries
- Efficient helper functions with minimal database calls
- Cached discount status where possible

### Security:
- Date validation prevents invalid inputs
- Admin permissions for discount management
- Audit trail through discount notes

### Scalability:
- Works with existing product catalog
- No breaking changes to core functionality
- Backward compatible with legacy discounts

---

**Implementation Date**: April 2026
**Version**: 1.0
**Developer**: Cascade AI Assistant
**Database**: Laravel Migration Included
**Frontend**: Blade Templates + CSS Included
