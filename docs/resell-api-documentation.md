# Resell API Documentation

## Overview
The resell functionality allows customers to resell products they've purchased at their own price. The system calculates profit and commission automatically.

## Authentication
All API endpoints require customer authentication. Include the authentication token in the request headers.

```
Authorization: Bearer {customer_token}
Content-Type: application/json
```

## 1. Resell Product API

### Endpoint
```
POST /api/v1/resell-product
```

### Description
Adds a product to cart for reselling with customer-defined pricing.

### Request Body
```json
{
  "product_id": "integer (required) - ID of the product to resell",
  "resell_price": "number (required) - Customer's selling price",
  "quantity": "integer (optional) - Quantity, defaults to 1"
}
```

### Example Request
```json
{
  "product_id": 123,
  "resell_price": 150.00,
  "quantity": 1
}
```

### Response Format

#### Success Response (200)
```json
{
  "success": true,
  "message": "Product added to cart for resell",
  "redirect_url": "https://yourapp.com/checkout-details",
  "data": {
    "id": 456,
    "product_id": 123,
    "customer_id": 789,
    "cart_group_id": "unique_cart_group_id",
    "quantity": 1,
    "price": 150.00,
    "is_resell": true,
    "commission_rate": 0,
    "resell_commission": 0,
    "resell_profit": 50.00,
    "product_type": "physical",
    "seller_id": 1,
    "seller_is": "admin",
    "slug": "product-slug",
    "name": "Product Name",
    "thumbnail": "product-image.jpg"
  }
}
```

#### Error Responses

**Product Not Found (404)**
```json
{
  "success": false,
  "message": "Product not found"
}
```

**Not Authenticated (401)**
```json
{
  "success": false,
  "message": "Please login to resell"
}
```

**Validation Error (422)**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "product_id": ["The product id field is required."],
    "resell_price": ["The resell price field is required."]
  }
}
```

## 2. Cart with Resell Items

### Get Cart Endpoint
```
GET /api/v1/cart
```

### Response with Resell Items
```json
{
  "success": true,
  "data": [
    {
      "id": 456,
      "product_id": 123,
      "customer_id": 789,
      "quantity": 1,
      "price": 150.00,
      "discount": 0,
      "is_checked": true,
      "is_resell": true,
      "resell_profit": 50.00,
      "resell_commission": 0,
      "commission_rate": 0,
      "product_type": "physical",
      "seller_id": 1,
      "seller_is": "admin",
      "slug": "product-slug",
      "name": "Product Name",
      "thumbnail": "product-image.jpg",
      "allProducts": {
        "id": 123,
        "name": "Product Name",
        "unit_price": 100.00,
        "discount": 10.00,
        "discount_type": "flat",
        "tax": 15.00,
        "tax_model": "exclude",
        "product_type": "physical",
        "current_stock": 50,
        "minimum_order_qty": 1
      }
    }
  ]
}
```

## 3. Order Summary with Resell Calculations

### Get Order Summary Endpoint
```
GET /api/v1/order-summary
```

### Response with Resell Calculations
```json
{
  "success": true,
  "data": {
    "sub_total": 150.00,
    "total_tax": 0,
    "total_shipping_cost": 0,
    "total_discount_on_product": 0,
    "resell_profit": 50.00,
    "resell_commission": 0,
    "coupon_discount": 0,
    "referral_discount": 0,
    "total_amount": 200.00,
    "total_saved_amount": 0
  }
}
```

## 4. Resell Profit Calculation Logic

### Formula
```
resell_profit = resell_price - product_unit_price
resell_commission = 0 (currently disabled)
total_with_resell = sub_total + resell_profit + resell_commission
```

### Example Calculation
- Product Unit Price: $100.00
- Customer Resell Price: $150.00
- Resell Profit: $150.00 - $100.00 = $50.00
- Resell Commission: $0.00
- Total: $150.00 + $50.00 = $200.00

## 5. Product Details for Resell

### Get Product Details Endpoint
```
GET /api/v1/products/{slug}
```

### Response with Resell Information
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "Product Name",
    "slug": "product-slug",
    "unit_price": 100.00,
    "discount": 10.00,
    "discount_type": "flat",
    "tax": 15.00,
    "tax_model": "exclude",
    "product_type": "physical",
    "current_stock": 50,
    "minimum_order_qty": 1,
    "thumbnail": "product-image.jpg",
    "images": ["image1.jpg", "image2.jpg"],
    "variation": [
      {
        "type": "Size",
        "price": 110.00,
        "qty": 25
      }
    ],
    "can_resell": true,
    "resell_info": {
      "base_price": 100.00,
      "min_resell_price": 100.00,
      "max_resell_price": 999999.99
    }
  }
}
```

## 6. Flutter Implementation Guide

### Step 1: Check Product Details
```dart
Future<Map<String, dynamic>> getProductDetails(String slug) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/products/$slug'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
    },
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body)['data'];
  } else {
    throw Exception('Failed to load product details');
  }
}
```

### Step 2: Resell Product
```dart
Future<Map<String, dynamic>> resellProduct(
  int productId, 
  double resellPrice, 
  {int quantity = 1}
) async {
  final response = await http.post(
    Uri.parse('$baseUrl/api/v1/resell-product'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
    },
    body: json.encode({
      'product_id': productId,
      'resell_price': resellPrice,
      'quantity': quantity,
    }),
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body);
  } else {
    throw Exception('Failed to resell product');
  }
}
```

### Step 3: Get Cart with Resell Items
```dart
Future<List<Map<String, dynamic>>> getCart() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/cart'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
    },
  );
  
  if (response.statusCode == 200) {
    return List<Map<String, dynamic>>.from(
      json.decode(response.body)['data']
    );
  } else {
    throw Exception('Failed to load cart');
  }
}
```

### Step 4: Calculate Order Summary
```dart
Future<Map<String, dynamic>> getOrderSummary() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/order-summary'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
    },
  );
  
  if (response.statusCode == 200) {
    return Map<String, dynamic>.from(
      json.decode(response.body)['data']
    );
  } else {
    throw Exception('Failed to load order summary');
  }
}
```

## 7. UI Flow for Flutter

### Product Detail Screen
1. Load product details
2. Check if `can_resell` is true
3. Show "Resell Now" button
4. On button click, show resell modal with:
   - Current product price
   - Input for resell price
   - Calculate and show potential profit
   - Confirm button

### Resell Modal
```dart
void showResellModal(Map<String, dynamic> product) {
  showDialog(
    context: context,
    builder: (BuildContext context) {
      double resellPrice = 0.0;
      double basePrice = product['unit_price'];
      
      return AlertDialog(
        title: Text('Resell Product'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text('Base Price: \$${basePrice.toStringAsFixed(2)}'),
            TextField(
              decoration: InputDecoration(labelText: 'Resell Price'),
              keyboardType: TextInputType.number,
              onChanged: (value) {
                resellPrice = double.tryParse(value) ?? 0.0;
              },
            ),
            Text('Profit: \$${(resellPrice - basePrice).toStringAsFixed(2)}'),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              try {
                await resellProduct(
                  product['id'], 
                  resellPrice
                );
                Navigator.pop(context);
                // Navigate to cart or checkout
              } catch (e) {
                // Show error message
              }
            },
            child: Text('Resell Now'),
          ),
        ],
      );
    },
  );
}
```

### Cart Screen
1. Load cart items
2. Display items with `is_resell` flag
3. Show resell profit for resell items
4. Calculate and display total including resell profit

### Checkout Screen
1. Load order summary
2. Display resell profit separately
3. Include resell profit in total calculation

## 8. Error Handling

### Common Error Codes
- `401`: Not authenticated
- `404`: Product not found
- `422`: Validation error
- `500`: Server error

### Error Response Format
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## 9. Testing

### Test Cases
1. **Resell Product**: Test with valid product ID and price
2. **Invalid Product**: Test with non-existent product ID
3. **Invalid Price**: Test with negative or zero price
4. **Unauthenticated**: Test without authentication token
5. **Cart Integration**: Verify resell items appear in cart
6. **Checkout Flow**: Test complete checkout with resell items

## 10. Important Notes

1. **Authentication**: All endpoints require customer authentication
2. **Price Validation**: Resell price must be greater than 0
3. **Product Availability**: Product must exist and be active
4. **Profit Calculation**: Profit is calculated as `resell_price - unit_price`
5. **Commission**: Currently set to 0 but can be enabled in future
6. **Tax**: Tax is calculated on discounted price, not resell price
7. **Stock**: Resell doesn't affect original product stock

## 11. Webhook Support

### Order Status Updates
If you need real-time updates, implement webhooks for:
- Order created
- Order status changed
- Payment completed

Contact the backend team to configure webhook endpoints.

---

**Last Updated**: April 2026
**Version**: 1.0
**Contact**: For support, contact the backend development team
