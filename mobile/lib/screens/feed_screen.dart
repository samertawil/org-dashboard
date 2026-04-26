import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'package:intl/intl.dart';

class FeedScreen extends StatefulWidget {
  const FeedScreen({super.key});

  @override
  State<FeedScreen> createState() => _FeedScreenState();
}

class _FeedScreenState extends State<FeedScreen> {
  final ApiService _apiService = ApiService();
  List<dynamic> _items = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadFeed();
  }

  Future<void> _loadFeed() async {
    try {
      final items = await _apiService.fetchFeed();
      setState(() {
        _items = items;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('خطأ في تحميل البيانات: $e')),
        );
      }
    }
  }

  String _getTimeAgo(String dateString) {
    DateTime dateTime = DateTime.parse(dateString);
    Duration diff = DateTime.now().difference(dateTime);
    
    if (diff.inDays > 7) return DateFormat('yyyy-MM-dd').format(dateTime);
    if (diff.inDays > 0) return 'منذ ${diff.inDays} يوم';
    if (diff.inHours > 0) return 'منذ ${diff.inHours} ساعة';
    if (diff.inMinutes > 0) return 'منذ ${diff.inMinutes} دقيقة';
    return 'الآن';
  }

  Color _getStatusColor(dynamic status) {
    if (status == null) return Colors.grey;
    
    String name = '';
    if (status is Map) {
      name = (status['status_name'] ?? status['name'] ?? '').toString().toLowerCase();
    } else {
      // If it's an ID (int)
      final id = status.toString();
      if (id == '27') return Colors.green; // Completed
      if (id == '26') return Colors.orange; // In Progress
      if (id == '25') return Colors.blue; // Planned
      if (id == '28') return Colors.red; // On Hold
      return Colors.blue;
    }

    if (name.contains('approved') || name.contains('تم') || name.contains('مكتمل') || name.contains('completed')) return Colors.green;
    if (name.contains('pending') || name.contains('قيد') || name.contains('progress')) return Colors.orange;
    return Colors.blue;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF0F2F5), // لون خلفية الفيسبوك
      appBar: AppBar(
        title: Text('Timeline (${_items.length})', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.blue)),
        backgroundColor: Colors.white,
        elevation: 0.5,
        centerTitle: false,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.blue),
            onPressed: _loadFeed,
          ),
          IconButton(
            icon: const Icon(Icons.logout, color: Colors.grey),
            onPressed: () => _logout(),
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadFeed,
              child: ListView.builder(
                itemCount: _items.length,
                itemBuilder: (context, index) {
                  try {
                    final item = _items[index];
                    final type = item['feed_type'];
                    final data = item['data'];
                    
                    if (data == null) return const SizedBox.shrink();
                    
                    return _buildSocialCard(type, data, item['created_at']);
                  } catch (e) {
                    return ListTile(
                      title: const Text('خطأ في عرض هذا المنشور'),
                      subtitle: Text(e.toString()),
                      tileColor: Colors.red.shade50,
                    );
                  }
                },
              ),
            ),
    );
  }

  Widget _buildSocialCard(String type, dynamic data, String createdAt) {
    // Fix: status might be an int or a Map. Let's be careful.
    dynamic status = data['activity_status'] ?? data['status'];
    String statusName = 'غير محدد';
    
    if (status is Map) {
      statusName = status['status_name'] ?? status['name'] ?? 'غير محدد';
    } else if (data['status_info'] != null && data['status_info'] is Map) {
      // In web app, we have status_info attribute
      statusName = data['status_info']['name'] ?? 'غير محدد';
    }

    final creatorName = data['creator']?['name'] ?? 'نظام المؤسسة';

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      color: Colors.white,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          Padding(
            padding: const EdgeInsets.all(12.0),
            child: Row(
              children: [
                CircleAvatar(
                  backgroundColor: Colors.blue.shade100,
                  child: Text(creatorName[0].toUpperCase(), style: const TextStyle(fontWeight: FontWeight.bold)),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(creatorName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                      Row(
                        children: [
                          Text(_getTimeAgo(createdAt), style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
                          const SizedBox(width: 4),
                          const Icon(Icons.public, size: 12, color: Colors.grey),
                        ],
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: _getStatusColor(status).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: Text(
                    statusName,
                    style: TextStyle(color: _getStatusColor(status), fontWeight: FontWeight.bold, fontSize: 11),
                  ),
                ),
              ],
            ),
          ),
          
          // Content Title & Description
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  type == 'activity' ? (data['name'] ?? '') : 'طلب شراء #${data['request_number']}',
                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                if (data['description'] != null)
                  Text(
                    data['description'],
                    style: const TextStyle(fontSize: 15, height: 1.4),
                  ),
              ],
            ),
          ),

          const SizedBox(height: 12),

          // Summary Box (Grey Box like web)
          Container(
            width: double.infinity,
            margin: const EdgeInsets.symmetric(horizontal: 12),
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.grey.shade50,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: Wrap(
              spacing: 12,
              runSpacing: 8,
              children: [
                if (type == 'activity' && data['cost'] != null && (double.tryParse(data['cost'].toString()) ?? 0) > 0)
                  _buildMiniBadge(Icons.monetization_on, '\$${data['cost']}', Colors.green),
                
                if (data['beneficiaries'] != null && data['beneficiaries'] is List)
                  ...(data['beneficiaries'] as List).map((b) => 
                    _buildMiniBadge(Icons.people, '${b['beneficiaries_count']} مستفيد', Colors.indigo)
                  ),

                if (data['parcels'] != null && data['parcels'] is List)
                  ...(data['parcels'] as List).map((p) => 
                    _buildMiniBadge(Icons.inventory_2, '${p['distributed_parcels_count']} طرد', Colors.amber)
                  ),
                  
                if (type == 'pr')
                  _buildMiniBadge(Icons.calendar_today, 'الموعد: ${data['request_date'] ?? '-'}', Colors.blue),
              ],
            ),
          ),

          const SizedBox(height: 12),

          // Divider
          Divider(height: 1, color: Colors.grey.shade200),

          // Action Buttons
          Row(
            children: [
              _buildActionButton(Icons.remove_red_eye_outlined, 'عرض'),
              _buildActionButton(Icons.chat_bubble_outline, 'تعليق'),
              _buildActionButton(Icons.share_outlined, 'مشاركة'),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMiniBadge(IconData icon, String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withOpacity(0.2)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(width: 4),
          Text(label, style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: color)),
        ],
      ),
    );
  }

  Widget _buildActionButton(IconData icon, String label) {
    return Expanded(
      child: InkWell(
        onTap: () {},
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 12),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, size: 20, color: Colors.grey.shade700),
              const SizedBox(width: 6),
              Text(label, style: TextStyle(color: Colors.grey.shade700, fontWeight: FontWeight.w600)),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _logout() async {
    // Confirmation Dialog
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('تسجيل الخروج'),
        content: const Text('هل أنت متأكد؟'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('إلغاء')),
          TextButton(
            onPressed: () async {
              await _apiService.logout();
              if (mounted) {
                Navigator.pushAndRemoveUntil(
                  context,
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                  (route) => false,
                );
              }
            },
            child: const Text('نعم'),
          ),
        ],
      ),
    );
  }
}
