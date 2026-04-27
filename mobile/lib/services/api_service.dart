import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  // Replace with your actual development machine IP address
  static const String baseUrl = "https://app.afscgaza.org/api"; 

  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': email,
          'password': password,
          'device_name': 'mobile_app',
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', data['token']);
        return data;
      } else {
        final error = jsonDecode(response.body);
        throw Exception(error['message'] ?? error['details'] ?? error['error'] ?? 'Failed to login');
      }
    } catch (e) {
      throw Exception('Connection error: $e');
    }
  }

  Future<Map<String, dynamic>> fetchFeed({
    int page = 1,
    String? search,
    String? statusId,
    String? regionId,
    String? cityId,
    String? fromDate,
    String? toDate,
    String? type,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    String url = '$baseUrl/feed?page=$page';
    if (search != null && search.isNotEmpty) url += '&search=$search';
    if (statusId != null && statusId.isNotEmpty) url += '&status_id=$statusId';
    if (regionId != null && regionId.isNotEmpty) url += '&region_id=$regionId';
    if (cityId != null && cityId.isNotEmpty) url += '&city_id=$cityId';
    if (fromDate != null && fromDate.isNotEmpty) url += '&from_date=$fromDate';
    if (toDate != null && toDate.isNotEmpty) url += '&to_date=$toDate';
    if (type != null && type.isNotEmpty) url += '&type=$type';

    final response = await http.get(
      Uri.parse(url),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to load feed');
    }
  }

  Future<Map<String, dynamic>> fetchMetadata() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final response = await http.get(
      Uri.parse('$baseUrl/feed/metadata'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to load metadata');
    }
  }

  Future<Map<String, dynamic>> postComment(int activityId, String comment) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final response = await http.post(
      Uri.parse('$baseUrl/activities/$activityId/comments'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({'comment': comment}),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to post comment');
    }
  }

  Future<List<dynamic>> fetchComments(int activityId) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final response = await http.get(
      Uri.parse('$baseUrl/activities/$activityId/comments'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to load comments');
    }
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    await http.post(
      Uri.parse('$baseUrl/logout'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    await prefs.remove('token');
  }
}
