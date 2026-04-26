import 'package:provider/provider.dart';
import '../services/language_provider.dart';
import '../services/translations.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'details_screen.dart';
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
  bool _isLoadMore = false;
  int _currentPage = 1;
  int _lastPage = 1;
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _loadFeed();
    _scrollController.addListener(_scrollListener);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollListener() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      if (!_isLoading && !_isLoadMore && _currentPage < _lastPage) {
        _loadMore();
      }
    }
  }

  Future<void> _loadFeed() async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiService.fetchFeed(page: 1);
      setState(() {
        _items = response['items'];
        _currentPage = response['meta']['current_page'];
        _lastPage = response['meta']['last_page'];
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        final t = AppTranslations.of(context)!;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('${t.translate('error_loading')}: $e')),
        );
      }
    }
  }

  Future<void> _loadMore() async {
    setState(() => _isLoadMore = true);
    try {
      final response = await _apiService.fetchFeed(page: _currentPage + 1);
      setState(() {
        _items.addAll(response['items']);
        _currentPage = response['meta']['current_page'];
        _lastPage = response['meta']['last_page'];
        _isLoadMore = false;
      });
    } catch (e) {
      setState(() => _isLoadMore = false);
    }
  }

  String _getTimeAgo(String dateString, AppTranslations t) {
    DateTime dateTime = DateTime.parse(dateString);
    Duration diff = DateTime.now().difference(dateTime);
    
    if (diff.inDays > 7) return DateFormat('yyyy-MM-dd').format(dateTime);
    if (diff.inDays > 0) return '${t.translate('ago')} ${diff.inDays} ${t.translate('days')}';
    if (diff.inHours > 0) return '${t.translate('ago')} ${diff.inHours} ${t.translate('hours')}';
    if (diff.inMinutes > 0) return '${t.translate('ago')} ${diff.inMinutes} ${t.translate('minutes')}';
    return t.translate('now');
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
    final t = AppTranslations.of(context)!;

    return Scaffold(
      backgroundColor: const Color(0xFFF0F2F5),
      appBar: AppBar(
        title: Text(t.translate('timeline'), style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.blue)),
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
                controller: _scrollController,
                itemCount: _items.length + (_isLoadMore ? 1 : 0),
                itemBuilder: (context, index) {
                  if (index == _items.length) {
                    return const Padding(
                      padding: EdgeInsets.all(16.0),
                      child: Center(child: CircularProgressIndicator()),
                    );
                  }
                  try {
                    final item = _items[index];
                    final type = item['feed_type'];
                    final data = item['data'];
                    
                    if (data == null) return const SizedBox.shrink();
                    
                    return InkWell(
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => DetailsScreen(type: type, data: data, createdAt: item['created_at']),
                        ),
                      ),
                      child: _buildSocialCard(type, data, item['created_at']),
                    );
                  } catch (e) {
                    return ListTile(
                      title: Text(t.translate('error_loading')),
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
    final t = AppTranslations.of(context)!;
    dynamic status = data['activity_status'] ?? data['status'];
    String statusName = '...';
    
    if (status is Map) {
      statusName = status['status_name'] ?? status['name'] ?? '...';
    } else if (data['status_info'] != null && data['status_info'] is Map) {
      statusName = data['status_info']['name'] ?? '...';
    }

    final creatorName = data['creator']?['name'] ?? '...';

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
                  child: Text(creatorName.isNotEmpty ? creatorName[0].toUpperCase() : '?', style: const TextStyle(fontWeight: FontWeight.bold)),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(creatorName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                      Row(
                        children: [
                          Text(_getTimeAgo(createdAt, t), style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
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
          
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  type == 'activity' ? (data['name'] ?? '') : '${t.translate('purchase_request')} #${data['request_number']}',
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
                    _buildMiniBadge(Icons.people, '${b['beneficiaries_count']} ${t.translate('beneficiaries')}', Colors.indigo)
                  ),

                if (data['parcels'] != null && data['parcels'] is List)
                  ...(data['parcels'] as List).map((p) => 
                    _buildMiniBadge(Icons.inventory_2, '${p['distributed_parcels_count']} ${t.translate('parcels')}', Colors.amber)
                  ),
              ],
            ),
          ),

          const SizedBox(height: 12),
          Divider(height: 1, color: Colors.grey.shade200),

          Row(
            children: [
              _buildActionButton(Icons.remove_red_eye_outlined, t.translate('view'), onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => DetailsScreen(type: type, data: data, createdAt: createdAt),
                  ),
                );
              }),
              _buildActionButton(Icons.chat_bubble_outline, t.translate('comment')),
              _buildActionButton(Icons.share_outlined, t.translate('share')),
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

  Widget _buildActionButton(IconData icon, String label, {VoidCallback? onTap}) {
    return Expanded(
      child: InkWell(
        onTap: onTap ?? () {},
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
    final t = AppTranslations.of(context)!;
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(t.translate('logout')),
        content: Text(t.translate('are_you_sure')),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: Text(t.translate('cancel'))),
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
            child: Text(t.translate('yes')),
          ),
        ],
      ),
    );
  }
}
