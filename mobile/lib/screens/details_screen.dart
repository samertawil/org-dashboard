import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'package:intl/intl.dart';

class DetailsScreen extends StatefulWidget {
  final String type;
  final dynamic data;
  final String createdAt;

  const DetailsScreen({
    super.key,
    required this.type,
    required this.data,
    required this.createdAt,
  });

  @override
  State<DetailsScreen> createState() => _DetailsScreenState();
}

class _DetailsScreenState extends State<DetailsScreen> {
  final ApiService _apiService = ApiService();
  final TextEditingController _commentController = TextEditingController();
  bool _isSending = false;
  late List<dynamic> _comments;

  @override
  void initState() {
    super.initState();
    _comments = List.from(widget.data['comments'] ?? []);
  }

  Future<void> _sendComment() async {
    if (_commentController.text.trim().isEmpty) return;

    setState(() => _isSending = true);
    try {
      final newComment = await _apiService.postComment(widget.data['id'], _commentController.text);
      setState(() {
        _comments.insert(0, newComment);
        _commentController.clear();
        _isSending = false;
      });
    } catch (e) {
      setState(() => _isSending = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('خطأ في إرسال التعليق: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final creatorName = widget.data['creator']?['name'] ?? 'نظام المؤسسة';
    final attachments = widget.data['attachments'] as List? ?? [];

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('تفاصيل النشاط', style: TextStyle(color: Colors.black)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Gallery
                  if (attachments.isNotEmpty)
                    SizedBox(
                      height: 250,
                      child: PageView.builder(
                        itemCount: attachments.length,
                        itemBuilder: (context, index) {
                          final path = attachments[index]['attchment_path'];
                          return Image.network(
                            'https://app.afscgaza.org/storage/$path',
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) => Container(
                              color: Colors.grey.shade200,
                              child: const Icon(Icons.image_not_supported, size: 50),
                            ),
                          );
                        },
                      ),
                    ),

                  Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          widget.type == 'activity' ? (widget.data['name'] ?? '') : 'طلب شراء #${widget.data['request_number']}',
                          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            CircleAvatar(
                              radius: 15,
                              child: Text(creatorName[0].toUpperCase()),
                            ),
                            const SizedBox(width: 8),
                            Text(creatorName, style: const TextStyle(fontWeight: FontWeight.w600)),
                            const Spacer(),
                            Text(DateFormat('yyyy-MM-dd').format(DateTime.parse(widget.createdAt)), style: const TextStyle(color: Colors.grey)),
                          ],
                        ),
                        const Divider(height: 32),
                        const Text('الوصف', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 8),
                        Text(
                          widget.data['description'] ?? 'لا يوجد وصف متاح لهذا النشاط.',
                          style: const TextStyle(fontSize: 16, color: Colors.black87, height: 1.5),
                        ),
                        const SizedBox(height: 24),
                        
                        // Details Grid
                        _buildSectionTitle('المستفيدين والطرود'),
                        const SizedBox(height: 12),
                        _buildInfoGrid(),
                        
                        const Divider(height: 48),
                        _buildSectionTitle('التعليقات (${_comments.length})'),
                        const SizedBox(height: 16),
                        
                        // Comments List
                        ListView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: _comments.length,
                          itemBuilder: (context, index) {
                            final comment = _comments[index];
                            return _buildCommentItem(comment);
                          },
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          
          // Comment Input Field
          Container(
            padding: EdgeInsets.only(
              bottom: MediaQuery.of(context).padding.bottom + 8,
              left: 16,
              right: 16,
              top: 8,
            ),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 4, offset: Offset(0, -2))],
            ),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _commentController,
                    decoration: InputDecoration(
                      hintText: 'اكتب تعليقاً...',
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(24), borderSide: BorderSide.none),
                      filled: true,
                      fillColor: Colors.grey.shade100,
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                _isSending
                    ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(strokeWidth: 2))
                    : IconButton(
                        icon: const Icon(Icons.send, color: Colors.blue),
                        onPressed: _sendComment,
                      ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold));
  }

  Widget _buildInfoGrid() {
    final beneficiaries = widget.data['beneficiaries'] as List? ?? [];
    final parcels = widget.data['parcels'] as List? ?? [];

    return Wrap(
      spacing: 16,
      runSpacing: 16,
      children: [
        for (var b in beneficiaries)
          _buildInfoItem(Icons.people, 'مستفيدين', '${b['beneficiaries_count']}', Colors.indigo),
        for (var p in parcels)
          _buildInfoItem(Icons.inventory_2, 'طرود', '${p['distributed_parcels_count']}', Colors.amber),
        if (widget.data['cost'] != null)
          _buildInfoItem(Icons.monetization_on, 'التكلفة', '\$${widget.data['cost']}', Colors.green),
      ],
    );
  }

  Widget _buildInfoItem(IconData icon, String label, String value, Color color) {
    return Container(
      width: (MediaQuery.of(context).size.width / 2) - 24,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: color.withOpacity(0.05),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withOpacity(0.1)),
      ),
      child: Row(
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(width: 12),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
              Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildCommentItem(dynamic comment) {
    final name = comment['creator']?['name'] ?? 'مستخدم';
    final text = comment['comment'] ?? '';
    final time = comment['created_at'] != null ? DateFormat('MM-dd HH:mm').format(DateTime.parse(comment['created_at'])) : '';

    return Padding(
      padding: const EdgeInsets.only(bottom: 16.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          CircleAvatar(
            radius: 18,
            backgroundColor: Colors.blue.shade50,
            child: Text(name[0].toUpperCase(), style: const TextStyle(fontSize: 14)),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.mainAxisAlignment,
                    children: [
                      Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                      const Spacer(),
                      Text(time, style: const TextStyle(color: Colors.grey, fontSize: 10)),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Text(text, style: const TextStyle(fontSize: 14)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
