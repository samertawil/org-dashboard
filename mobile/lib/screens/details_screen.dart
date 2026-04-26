import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'package:intl/intl.dart';
import '../services/translations.dart';

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
  int _currentImageIndex = 0;

  @override
  void initState() {
    super.initState();
    _comments = List.from(widget.data['comments'] ?? []);
  }

  Future<void> _sendComment() async {
    final t = AppTranslations.of(context)!;
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
        SnackBar(content: Text('${t.translate('error_sending')}: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final t = AppTranslations.of(context)!;
    final creatorName = widget.data['creator']?['name'] ?? '...';
    final attachments = widget.data['attachments'] as List? ?? [];

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(t.translate('details'), style: const TextStyle(color: Colors.black)),
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
                    Stack(
                      children: [
                        SizedBox(
                          height: 300,
                          child: PageView.builder(
                            itemCount: attachments.length,
                            onPageChanged: (index) => setState(() => _currentImageIndex = index),
                            itemBuilder: (context, index) {
                              final item = attachments[index];
                              final String rawPath = item['attchment_path'] ?? item['path'] ?? '';
                              if (rawPath.isEmpty) return const SizedBox.shrink();
                              
                              final path = rawPath.startsWith('/') ? rawPath.substring(1) : rawPath;
                              final url = 'https://app.afscgaza.org/storage/$path';
                              
                              final ext = path.split('.').last.toLowerCase();
                              final isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].contains(ext);

                              if (!isImage) {
                                return Container(
                                  color: Colors.grey.shade100,
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      const Icon(Icons.insert_drive_file, size: 80, color: Colors.blue),
                                      const SizedBox(height: 16),
                                      Text(ext.toUpperCase(), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                                    ],
                                  ),
                                );
                              }

                              return InteractiveViewer(
                                child: Image.network(
                                  url,
                                  fit: BoxFit.contain, // Contain for details to see whole image
                                  loadingBuilder: (context, child, loadingProgress) {
                                    if (loadingProgress == null) return child;
                                    return const Center(child: CircularProgressIndicator());
                                  },
                                  errorBuilder: (context, error, stackTrace) => Container(
                                    color: Colors.grey.shade200,
                                    child: const Icon(Icons.image_not_supported, size: 50),
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                        if (attachments.length > 1)
                          Positioned(
                            bottom: 16,
                            right: 16,
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                              decoration: BoxDecoration(
                                color: Colors.black.withOpacity(0.6),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Text(
                                '${_currentImageIndex + 1} / ${attachments.length}',
                                style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
                              ),
                            ),
                          ),
                      ],
                    ),

                  Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          widget.type == 'activity' ? (widget.data['name'] ?? '') : '${t.translate('purchase_request')} #${widget.data['request_number']}',
                          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            CircleAvatar(
                              radius: 15,
                              child: Text(creatorName.isNotEmpty ? creatorName[0].toUpperCase() : '?'),
                            ),
                            const SizedBox(width: 8),
                            Text(creatorName, style: const TextStyle(fontWeight: FontWeight.w600)),
                            const Spacer(),
                            Text(DateFormat('yyyy-MM-dd').format(DateTime.parse(widget.createdAt)), style: const TextStyle(color: Colors.grey)),
                          ],
                        ),
                        const Divider(height: 32),
                        Text(t.translate('description'), style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 8),
                        Text(
                          widget.data['description'] ?? t.translate('no_description'),
                          style: const TextStyle(fontSize: 16, color: Colors.black87, height: 1.5),
                        ),
                        const SizedBox(height: 24),
                        
                        _buildSectionTitle(t.translate('beneficiaries')),
                        const SizedBox(height: 12),
                        _buildInfoGrid(t),
                        
                        const Divider(height: 48),
                        _buildSectionTitle('${t.translate('comments')} (${_comments.length})'),
                        const SizedBox(height: 16),
                        
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
                      hintText: t.translate('write_comment'),
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

  Widget _buildInfoGrid(AppTranslations t) {
    final beneficiaries = widget.data['beneficiaries'] as List? ?? [];
    final parcels = widget.data['parcels'] as List? ?? [];

    return Wrap(
      spacing: 16,
      runSpacing: 16,
      children: [
        for (var b in beneficiaries)
          _buildInfoItem(Icons.people, t.translate('beneficiaries'), '${b['beneficiaries_count']}', Colors.indigo),
        for (var p in parcels)
          _buildInfoItem(Icons.inventory_2, t.translate('parcels'), '${p['distributed_parcels_count']}', Colors.amber),
        if (widget.data['cost'] != null && (double.tryParse(widget.data['cost'].toString()) ?? 0) > 0)
          _buildInfoItem(Icons.monetization_on, t.translate('cost'), '\$${widget.data['cost']}', Colors.green),
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
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: TextStyle(fontSize: 12, color: Colors.grey.shade600), overflow: TextOverflow.ellipsis),
                Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCommentItem(dynamic comment) {
    final name = comment['creator']?['name'] ?? '...';
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
            child: Text(name.isNotEmpty ? name[0].toUpperCase() : '?', style: const TextStyle(fontSize: 14)),
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
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
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
