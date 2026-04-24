# MyFancyMart Flutter App - Complete API Integration Guide

## 📱 Overview
This comprehensive guide provides everything Flutter developers need to integrate the MyFancyMart e-commerce app with reseller and wallet functionality.

## 🔐 Authentication

### Base URL
```
Production: https://fancymart.pk/api/v1
Development: http://your-domain.com/api/v1
```

### Login API
```http
POST /auth/login
```

#### Request Body
```json
{
  "email_or_phone": "meemwork0687@gmail.com",
  "password": "Af@q0687"
}
```

#### Response
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "id": 123,
    "name": "Customer Name",
    "email": "meemwork0687@gmail.com",
    "phone": "03123456789",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "is_active": true
  }
}
```

#### Flutter Implementation
```dart
class AuthService {
  static Future<Map<String, dynamic>> login(String emailOrPhone, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({
        'email_or_phone': emailOrPhone,
        'password': password,
      }),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      if (data['success']) {
        // Save token to secure storage
        await SecureStorage.saveToken(data['data']['token']);
        return data['data'];
      }
    }
    throw Exception('Login failed');
  }
}
```

## 🛍️ Product APIs

### Get Product Details
```http
GET /products/{slug}
```

#### Response
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "Girls Shoes",
    "slug": "girls-shoes",
    "unit_price": 100.00,
    "discount": 10.00,
    "discount_type": "flat",
    "tax": 15.00,
    "product_type": "physical",
    "current_stock": 50,
    "thumbnail": "product-image.jpg",
    "images": ["image1.jpg", "image2.jpg"],
    "can_resell": true,
    "resell_info": {
      "base_price": 100.00,
      "min_resell_price": 100.00,
      "max_resell_price": 999999.99
    }
  }
}
```

#### Flutter Implementation
```dart
class ProductService {
  static Future<Map<String, dynamic>> getProductDetails(String slug) async {
    final token = await SecureStorage.getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/products/$slug'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return json.decode(response.body)['data'];
    }
    throw Exception('Failed to load product');
  }
}
```

## 💰 Reseller APIs

### Add Product to Cart for Resell
```http
POST /reseller/add-to-cart
```

#### Request Body
```json
{
  "product_id": 123,
  "resell_price": 150.00,
  "quantity": 1
}
```

#### Response
```json
{
  "success": true,
  "message": "Product added to cart for resell",
  "data": {
    "id": 456,
    "product_id": 123,
    "quantity": 1,
    "price": 150.00,
    "is_resell": 1,
    "resell_profit": 50.00,
    "resell_commission": 0.00
  }
}
```

### Get Resell Cart
```http
GET /reseller/cart
```

#### Response
```json
{
  "success": true,
  "data": [
    {
      "id": 456,
      "product_id": 123,
      "quantity": 1,
      "price": 150.00,
      "is_resell": 1,
      "resell_profit": 50.00,
      "resell_commission": 0.00,
      "product": {
        "id": 123,
        "name": "Girls Shoes",
        "slug": "girls-shoes",
        "unit_price": 100.00,
        "thumbnail": "product-image.jpg"
      }
    }
  ]
}
```

### Get Order Summary
```http
GET /reseller/order-summary
```

#### Response
```json
{
  "success": true,
  "data": {
    "sub_total": 150.00,
    "total_shipping_cost": 2.00,
    "cash_handling_fee": 0.10,
    "resell_profit": 50.00,
    "resell_commission": 0.00,
    "total_amount": 202.10
  }
}
```

### Get Resellable Products
```http
GET /reseller/products
```

#### Response
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "name": "Girls Shoes",
      "slug": "girls-shoes",
      "unit_price": 100.00,
      "thumbnail": "product-image.jpg",
      "can_resell": true,
      "resell_info": {
        "base_price": 100.00,
        "min_resell_price": 100.00,
        "max_resell_price": 999999.99
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 50,
    "last_page": 3
  }
}
```

#### Flutter Reseller Service
```dart
class ResellerService {
  static Future<Map<String, dynamic>> addToCart(int productId, double resellPrice, int quantity) async {
    final token = await SecureStorage.getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/reseller/add-to-cart'),
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
      return json.decode(response.body)['data'];
    }
    throw Exception('Failed to add to cart');
  }

  static Future<List<dynamic>> getCart() async {
    final token = await SecureStorage.getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/reseller/cart'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return json.decode(response.body)['data'];
    }
    throw Exception('Failed to load cart');
  }

  static Future<Map<String, dynamic>> getOrderSummary() async {
    final token = await SecureStorage.getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/reseller/order-summary'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return json.decode(response.body)['data'];
    }
    throw Exception('Failed to load order summary');
  }
}
```

## 👛 Wallet APIs

### Get Wallet Summary
```http
GET /wallet/summary
```

#### Response
```json
{
  "success": true,
  "data": {
    "balance": 150.00,
    "total_earned": 500.00,
    "total_withdrawn": 200.00,
    "pending_withdrawal": 50.00,
    "status": "active"
  }
}
```

### Get Wallet Transactions
```http
GET /wallet/transactions?limit=20&page=1
```

#### Response
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "wallet_id": 123,
      "transaction_type": "credit",
      "amount": 50.00,
      "description": "Resell profit from order #789",
      "order_id": 789,
      "created_at": "2024-04-23T10:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 15,
    "last_page": 1
  }
}
```

### Request Withdrawal
```http
POST /wallet/withdraw
```

#### Request Body
```json
{
  "amount": 50.00,
  "withdrawal_method": "bank_transfer",
  "bank_name": "HBL",
  "account_number": "1234567890",
  "account_holder_name": "John Doe"
}
```

#### Response
```json
{
  "success": true,
  "message": "Withdrawal request submitted successfully",
  "data": {
    "id": 789,
    "amount": 50.00,
    "withdrawal_method": "bank_transfer",
    "approved": 0,
    "created_at": "2024-04-23T11:00:00.000000Z"
  }
}
```

### Get Withdrawal Requests
```http
GET /wallet/withdrawal-requests
```

#### Response
```json
{
  "success": true,
  "data": [
    {
      "id": 789,
      "amount": 50.00,
      "withdrawal_method": "bank_transfer",
      "bank_name": "HBL",
      "account_number": "1234567890",
      "approved": 0,
      "created_at": "2024-04-23T11:00:00.000000Z"
    }
  ]
}
```

#### Flutter Wallet Service
```dart
class WalletService {
  static Future<Map<String, dynamic>> getWalletSummary() async {
    final token = await SecureStorage.getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/wallet/summary'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return json.decode(response.body)['data'];
    }
    throw Exception('Failed to load wallet summary');
  }

  static Future<List<dynamic>> getTransactions({int page = 1, int limit = 20}) async {
    final token = await SecureStorage.getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/wallet/transactions?limit=$limit&page=$page'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return json.decode(response.body)['data'];
    }
    throw Exception('Failed to load transactions');
  }

  static Future<Map<String, dynamic>> requestWithdrawal({
    required double amount,
    required String method,
    Map<String, String>? methodFields,
  }) async {
    final token = await SecureStorage.getToken();
    Map<String, dynamic> requestData = {
      'amount': amount,
      'withdrawal_method': method,
    };
    
    if (methodFields != null) {
      requestData.addAll(methodFields);
    }

    final response = await http.post(
      Uri.parse('$baseUrl/wallet/withdraw'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: json.encode(requestData),
    );

    if (response.statusCode == 200) {
      return json.decode(response.body)['data'];
    }
    throw Exception('Withdrawal request failed');
  }
}
```

## 📱 Flutter UI Components

### Product Details Screen with Resell
```dart
class ProductDetailsScreen extends StatefulWidget {
  final String productSlug;
  
  const ProductDetailsScreen({Key? key, required this.productSlug}) : super(key: key);
  
  @override
  _ProductDetailsScreenState createState() => _ProductDetailsScreenState();
}

class _ProductDetailsScreenState extends State<ProductDetailsScreen> {
  Map<String, dynamic>? product;
  bool isLoading = true;
  TextEditingController _resellPriceController = TextEditingController();
  
  @override
  void initState() {
    super.initState();
    _fetchProductDetails();
  }
  
  Future<void> _fetchProductDetails() async {
    try {
      final data = await ProductService.getProductDetails(widget.productSlug);
      setState(() {
        product = data;
        isLoading = false;
        _resellPriceController.text = product!['unit_price'].toString();
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to load product')),
      );
    }
  }
  
  Future<void> _addToCartForResell() async {
    try {
      await ResellerService.addToCart(
        product!['id'],
        double.parse(_resellPriceController.text),
        1,
      );
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Added to cart for resell')),
      );
      Navigator.pushNamed(context, '/cart');
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to add to cart')),
      );
    }
  }
  
  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return Scaffold(body: Center(child: CircularProgressIndicator()));
    }
    
    return Scaffold(
      appBar: AppBar(title: Text(product!['name'])),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Image.network(product!['thumbnail']),
            SizedBox(height: 16),
            Text(product!['name'], style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
            Text('Original Price: \$${product!['unit_price']}'),
            SizedBox(height: 16),
            
            if (product!['can_resell']) ...[
              Text('Set Your Resell Price:', style: TextStyle(fontSize: 18)),
              TextField(
                controller: _resellPriceController,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  labelText: 'Resell Price',
                  prefixText: '\$',
                ),
              ),
              SizedBox(height: 16),
              
              Container(
                padding: EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.green.shade50,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Profit Preview', style: TextStyle(fontWeight: FontWeight.bold)),
                    Text('Your Price: \$${_resellPriceController.text}'),
                    Text('Base Price: \$${product!['unit_price']}'),
                    Text('Your Profit: \$${(double.tryParse(_resellPriceController.text) ?? 0) - product!['unit_price']}',
                         style: TextStyle(color: Colors.green, fontWeight: FontWeight.bold)),
                  ],
                ),
              ),
              SizedBox(height: 16),
              
              ElevatedButton(
                onPressed: _addToCartForResell,
                child: Text('Add to Cart for Resell'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green,
                  foregroundColor: Colors.white,
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
```

### Wallet Screen
```dart
class WalletScreen extends StatefulWidget {
  @override
  _WalletScreenState createState() => _WalletScreenState();
}

class _WalletScreenState extends State<WalletScreen> {
  Map<String, dynamic>? walletSummary;
  List<dynamic> transactions = [];
  bool isLoading = true;
  
  @override
  void initState() {
    super.initState();
    _loadWalletData();
  }
  
  Future<void> _loadWalletData() async {
    try {
      final summary = await WalletService.getWalletSummary();
      final txList = await WalletService.getTransactions();
      setState(() {
        walletSummary = summary;
        transactions = txList;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('My Wallet')),
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : Column(
              children: [
                // Wallet Summary Cards
                Container(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    children: [
                      _buildSummaryCard('Current Balance', walletSummary!['balance'], Colors.blue),
                      SizedBox(height: 8),
                      _buildSummaryCard('Total Earned', walletSummary!['total_earned'], Colors.green),
                      SizedBox(height: 8),
                      _buildSummaryCard('Pending Withdrawal', walletSummary!['pending_withdrawal'], Colors.orange),
                    ],
                  ),
                ),
                
                // Transactions List
                Expanded(
                  child: ListView.builder(
                    itemCount: transactions.length,
                    itemBuilder: (context, index) {
                      final tx = transactions[index];
                      return Card(
                        margin: EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                        child: ListTile(
                          leading: Icon(
                            tx['transaction_type'] == 'credit' 
                                ? Icons.arrow_downward 
                                : Icons.arrow_upward,
                            color: tx['transaction_type'] == 'credit' 
                                ? Colors.green 
                                : Colors.red,
                          ),
                          title: Text(tx['description']),
                          subtitle: Text(DateFormat('MMM dd, yyyy').format(DateTime.parse(tx['created_at']))),
                          trailing: Text(
                            '\$${tx['amount'].toStringAsFixed(2)}',
                            style: TextStyle(
                              color: tx['transaction_type'] == 'credit' 
                                  ? Colors.green 
                                  : Colors.red,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),
              ],
            ),
    );
  }
  
  Widget _buildSummaryCard(String title, double amount, Color color) {
    return Card(
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(title, style: TextStyle(fontSize: 16)),
            Text(
              '\$${amount.toStringAsFixed(2)}',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
```

### Withdrawal Request Screen
```dart
class WithdrawalScreen extends StatefulWidget {
  @override
  _WithdrawalScreenState createState() => _WithdrawalScreenState();
}

class _WithdrawalScreenState extends State<WithdrawalScreen> {
  final _formKey = GlobalKey<FormState>();
  final _amountController = TextEditingController();
  final _bankNameController = TextEditingController();
  final _accountNumberController = TextEditingController();
  final _accountHolderNameController = TextEditingController();
  String _selectedMethod = 'bank_transfer';
  bool _isLoading = false;
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Request Withdrawal')),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: EdgeInsets.all(16),
          children: [
            TextFormField(
              controller: _amountController,
              keyboardType: TextInputType.number,
              decoration: InputDecoration(
                labelText: 'Amount',
                prefixText: '\$',
              ),
              validator: (value) {
                if (value!.isEmpty) return 'Please enter amount';
                if (double.tryParse(value) == null) return 'Please enter valid amount';
                return null;
              },
            ),
            
            SizedBox(height: 16),
            
            DropdownButtonFormField<String>(
              value: _selectedMethod,
              decoration: InputDecoration(labelText: 'Withdrawal Method'),
              items: [
                DropdownMenuItem(value: 'bank_transfer', child: Text('Bank Transfer')),
                DropdownMenuItem(value: 'jazzcash', child: Text('JazzCash')),
                DropdownMenuItem(value: 'easypaisa', child: Text('EasyPaisa')),
              ],
              onChanged: (value) {
                setState(() => _selectedMethod = value!);
              },
            ),
            
            if (_selectedMethod == 'bank_transfer') ...[
              SizedBox(height: 16),
              TextFormField(
                controller: _bankNameController,
                decoration: InputDecoration(labelText: 'Bank Name'),
                validator: (value) => value!.isEmpty ? 'Please enter bank name' : null,
              ),
            ],
            
            SizedBox(height: 16),
            
            TextFormField(
              controller: _accountNumberController,
              decoration: InputDecoration(labelText: 'Account Number'),
              validator: (value) => value!.isEmpty ? 'Please enter account number' : null,
            ),
            
            if (_selectedMethod == 'bank_transfer') ...[
              SizedBox(height: 16),
              TextFormField(
                controller: _accountHolderNameController,
                decoration: InputDecoration(labelText: 'Account Holder Name'),
                validator: (value) => value!.isEmpty ? 'Please enter account holder name' : null,
              ),
            ],
            
            SizedBox(height: 32),
            
            ElevatedButton(
              onPressed: _isLoading ? null : _submitWithdrawal,
              child: _isLoading 
                  ? CircularProgressIndicator(color: Colors.white)
                  : Text('Submit Withdrawal Request'),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.blue,
                foregroundColor: Colors.white,
                padding: EdgeInsets.symmetric(vertical: 16),
              ),
            ),
          ],
        ),
      ),
    );
  }
  
  Future<void> _submitWithdrawal() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() => _isLoading = true);
    
    try {
      Map<String, String> methodFields = {
        'account_number': _accountNumberController.text,
      };
      
      if (_selectedMethod == 'bank_transfer') {
        methodFields.addAll({
          'bank_name': _bankNameController.text,
          'account_holder_name': _accountHolderNameController.text,
        });
      } else {
        methodFields['account_name'] = _accountHolderNameController.text;
      }
      
      await WalletService.requestWithdrawal(
        amount: double.parse(_amountController.text),
        method: _selectedMethod,
        methodFields: methodFields,
      );
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Withdrawal request submitted successfully')),
      );
      Navigator.pop(context);
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to submit withdrawal request')),
      );
    } finally {
      setState(() => _isLoading = false);
    }
  }
}
```

## 🔧 Configuration

### Dependencies (pubspec.yaml)
```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^0.13.5
  shared_preferences: ^2.0.15
  flutter_secure_storage: ^5.0.2
  intl: ^0.17.0
```

### API Service Configuration
```dart
class ApiConfig {
  static const String baseUrl = 'https://fancymart.pk/api/v1';
  
  static Map<String, String> getHeaders({String? token}) {
    Map<String, String> headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }
    
    return headers;
  }
}
```

### Secure Storage Helper
```dart
class SecureStorage {
  static const _tokenKey = 'auth_token';
  
  static Future<void> saveToken(String token) async {
    final storage = FlutterSecureStorage();
    await storage.write(key: _tokenKey, value: token);
  }
  
  static Future<String?> getToken() async {
    final storage = FlutterSecureStorage();
    return await storage.read(key: _tokenKey);
  }
  
  static Future<void> deleteToken() async {
    final storage = FlutterSecureStorage();
    await storage.delete(key: _tokenKey);
  }
}
```

## 🧪 Testing

### Postman Collection
```json
{
  "info": {
    "name": "MyFancyMart API",
    "description": "Complete API collection for MyFancyMart Flutter App"
  },
  "variable": [
    {
      "key": "baseUrl",
      "value": "https://fancymart.pk/api/v1"
    },
    {
      "key": "token",
      "value": ""
    }
  ],
  "item": [
    {
      "name": "Authentication",
      "item": [
        {
          "name": "Login",
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"email_or_phone\": \"meemwork0687@gmail.com\",\n  \"password\": \"Af@q0687\"\n}"
            },
            "url": {
              "raw": "{{baseUrl}}/auth/login",
              "host": ["{{baseUrl}}"],
              "path": ["auth", "login"]
            }
          },
          "event": [
            {
              "listen": "test",
              "script": {
                "exec": [
                  "if (pm.response.code === 200) {",
                  "    const response = pm.response.json();",
                  "    if (response.success && response.data.token) {",
                  "        pm.collectionVariables.set('token', response.data.token);",
                  "    }",
                  "}"
                ]
              }
            }
          ]
        }
      ]
    },
    {
      "name": "Reseller",
      "item": [
        {
          "name": "Get Products",
          "request": {
            "method": "GET",
            "header": [
              {
                "key": "Authorization",
                "value": "Bearer {{token}}"
              }
            ],
            "url": "{{baseUrl}}/reseller/products"
          }
        },
        {
          "name": "Add to Cart",
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Authorization",
                "value": "Bearer {{token}}"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"product_id\": 17,\n  \"resell_price\": 150.00,\n  \"quantity\": 1\n}"
            },
            "url": "{{baseUrl}}/reseller/add-to-cart"
          }
        }
      ]
    },
    {
      "name": "Wallet",
      "item": [
        {
          "name": "Get Summary",
          "request": {
            "method": "GET",
            "header": [
              {
                "key": "Authorization",
                "value": "Bearer {{token}}"
              }
            ],
            "url": "{{baseUrl}}/wallet/summary"
          }
        },
        {
          "name": "Request Withdrawal",
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Authorization",
                "value": "Bearer {{token}}"
              },
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"amount\": 50.00,\n  \"withdrawal_method\": \"bank_transfer\",\n  \"bank_name\": \"HBL\",\n  \"account_number\": \"1234567890\",\n  \"account_holder_name\": \"John Doe\"\n}"
            },
            "url": "{{baseUrl}}/wallet/withdraw"
          }
        }
      ]
    }
  ]
}
```

## 📋 Integration Checklist

### Before Starting
- [ ] Get API base URL and credentials
- [ ] Set up Flutter project with required dependencies
- [ ] Configure API service class
- [ ] Set up secure storage for tokens

### Authentication
- [ ] Implement login API
- [ ] Store token securely
- [ ] Handle token refresh
- [ ] Implement logout functionality

### Product Features
- [ ] Product listing
- [ ] Product details
- [ ] Search functionality
- [ ] Product variations

### Reseller Features
- [ ] Add to cart for resell
- [ ] Cart management
- [ ] Order summary with profit
- [ ] Checkout process

### Wallet Features
- [ ] Wallet summary
- [ ] Transaction history
- [ ] Withdrawal requests
- [ ] Multiple payment methods

### Testing
- [ ] Test all API endpoints
- [ ] Test error scenarios
- [ ] Test network connectivity
- [ ] Test with real data

## 🚀 Quick Start

1. **Clone the Flutter project**
2. **Add dependencies** to pubspec.yaml
3. **Copy API service classes** to your project
4. **Update base URL** in ApiConfig
5. **Run the app** and test with provided credentials

## 📞 Support

For any issues or questions:
- **API Documentation**: This guide
- **Testing**: Use Postman collection
- **Live Testing**: https://fancymart.pk

---

**This complete guide provides everything needed to integrate the MyFancyMart Flutter app with all reseller and wallet functionality!** 🎉
