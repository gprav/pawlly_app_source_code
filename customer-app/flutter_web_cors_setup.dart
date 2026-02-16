// Flutter Web CORS Setup
import 'dart:html' as html;
import 'package:flutter/foundation.dart';

class WebCorsService {
  // Configure CORS for Flutter Web
  static void setupCorsHeaders() {
    if (kIsWeb) {
      // Add CORS headers to HTML requests
      html.document.addEventListener('DOMContentLoaded', (event) {
        _addMetaTags();
      });
    }
  }
  
  static void _addMetaTags() {
    // Add meta tags for CORS
    final head = html.document.head;
    
    // Content Security Policy
    final cspMeta = html.MetaElement()
      ..name = 'Content-Security-Policy'
      ..content = "default-src 'self'; connect-src 'self' http://localhost:8000 http://localhost:8080 https://your-api-domain.com; script-src 'self' 'unsafe-inline' 'unsafe-eval';";
    head?.appendChild(cspMeta);
    
    // CORS meta tag
    final corsMeta = html.MetaElement()
      ..name = 'cross-origin'
      ..content = 'use-credentials';
    head?.appendChild(corsMeta);
  }
  
  // HTTP client with CORS handling for web
  static Future<html.HttpRequest> makeRequest(
    String method, 
    String url, 
    {Map<String, String>? headers, 
     String? body}
  ) async {
    final request = html.HttpRequest();
    
    // Set up the request
    request.open(method, url, async: true);
    
    // Set CORS headers
    request.setRequestHeader('Access-Control-Allow-Origin', '*');
    request.setRequestHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    request.setRequestHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
    
    // Add custom headers
    headers?.forEach((key, value) {
      request.setRequestHeader(key, value);
    });
    
    // Handle preflight request
    if (method.toUpperCase() == 'OPTIONS') {
      request.setRequestHeader('Access-Control-Max-Age', '86400');
    }
    
    // Send request
    if (body != null) {
      request.send(body);
    } else {
      request.send();
    }
    
    // Wait for response
    await request.onLoadEnd.first;
    return request;
  }
}

// Alternative: Configure web/index.html
/*
Add to web/index.html in the <head> section:

<meta name="Cross-Origin-Embedder-Policy" content="cross-origin">
<meta name="Cross-Origin-Opener-Policy" content="same-origin">

<script>
// Handle CORS for Flutter Web
window.addEventListener('DOMContentLoaded', (event) => {
  // Configure fetch with CORS
  const originalFetch = window.fetch;
  window.fetch = function(resource, init) {
    init = init || {};
    init.mode = 'cors';
    init.credentials = 'include';
    
    // Add custom headers
    init.headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      ...init.headers
    };
    
    return originalFetch(resource, init);
  };
});
</script>
*/