// Flutter HTTP Service with proper headers
import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api'; // Django backend
  static const String laravelUrl = 'http://localhost:8080/api'; // Laravel backend
  
  // Store JWT token
  static String? _accessToken;
  static String? _refreshToken;
  
  // Set tokens after login
  static void setTokens({required String access, required String refresh}) {
    _accessToken = access;
    _refreshToken = refresh;
  }
  
  // Clear tokens on logout
  static void clearTokens() {
    _accessToken = null;
    _refreshToken = null;
  }
  
  // Get common headers for all requests
  static Map<String, String> _getHeaders({bool requiresAuth = false}) {
    Map<String, String> headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    // Add Authorization header if token exists and auth is required
    if (requiresAuth && _accessToken != null) {
      headers['Authorization'] = 'Bearer $_accessToken';
    }
    
    // For mobile apps, these headers help with server compatibility
    headers['User-Agent'] = 'TailsTap-Flutter-App/2.2.9';
    headers['X-Requested-With'] = 'XMLHttpRequest';
    
    return headers;
  }
  
  // GET request
  static Future<http.Response> get(String endpoint, {bool requiresAuth = false}) async {
    final uri = Uri.parse('$baseUrl$endpoint');
    
    try {
      final response = await http.get(
        uri,
        headers: _getHeaders(requiresAuth: requiresAuth),
      );
      
      // Handle token expiration
      if (response.statusCode == 401 && requiresAuth) {
        final refreshed = await _refreshAccessToken();
        if (refreshed) {
          // Retry the request with new token
          return await http.get(
            uri,
            headers: _getHeaders(requiresAuth: requiresAuth),
          );
        }
      }
      
      return response;
    } catch (e) {
      throw Exception('Network error: $e');
    }
  }
  
  // POST request
  static Future<http.Response> post(
    String endpoint, 
    Map<String, dynamic> body, 
    {bool requiresAuth = false}
  ) async {
    final uri = Uri.parse('$baseUrl$endpoint');
    
    try {
      final response = await http.post(
        uri,
        headers: _getHeaders(requiresAuth: requiresAuth),
        body: jsonEncode(body),
      );
      
      // Handle token expiration
      if (response.statusCode == 401 && requiresAuth) {
        final refreshed = await _refreshAccessToken();
        if (refreshed) {
          return await http.post(
            uri,
            headers: _getHeaders(requiresAuth: requiresAuth),
            body: jsonEncode(body),
          );
        }
      }
      
      return response;
    } catch (e) {
      throw Exception('Network error: $e');
    }
  }
  
  // Refresh access token
  static Future<bool> _refreshAccessToken() async {
    if (_refreshToken == null) return false;
    
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/auth/refresh/'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'refresh': _refreshToken}),
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        _accessToken = data['access'];
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
  
  // Login method
  static Future<Map<String, dynamic>?> login(String username, String password) async {
    try {
      final response = await post('/auth/login/', {
        'username': username,
        'password': password,
      });
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        setTokens(
          access: data['access'],
          refresh: data['refresh'],
        );
        return data;
      }
      return null;
    } catch (e) {
      throw Exception('Login failed: $e');
    }
  }
  
  // Example API calls
  static Future<List<dynamic>> getServices() async {
    final response = await get('/services/', requiresAuth: true);
    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    }
    throw Exception('Failed to load services');
  }
  
  static Future<Map<String, dynamic>> createBooking(Map<String, dynamic> bookingData) async {
    final response = await post('/bookings/', bookingData, requiresAuth: true);
    if (response.statusCode == 201) {
      return jsonDecode(response.body);
    }
    throw Exception('Failed to create booking');
  }
}

// Usage example
class BookingService {
  static Future<void> makeBooking() async {
    try {
      // Login first
      final loginResult = await ApiService.login('user@example.com', 'password');
      
      if (loginResult != null) {
        // Make authenticated request
        final services = await ApiService.getServices();
        print('Services: $services');
        
        // Create booking
        final bookingData = {
          'service_id': 1,
          'date': '2024-08-20',
          'time': '10:00',
        };
        final booking = await ApiService.createBooking(bookingData);
        print('Booking created: $booking');
      }
    } catch (e) {
      print('Error: $e');
    }
  }
}