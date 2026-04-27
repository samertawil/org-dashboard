import 'package:flutter/material.dart';

class AppTranslations {
  final Locale locale;
  AppTranslations(this.locale);

  static AppTranslations? of(BuildContext context) {
    return Localizations.of<AppTranslations>(context, AppTranslations);
  }

  static const Map<String, Map<String, String>> _localizedValues = {
    'en': {
      'login': 'Login',
      'email': 'Email',
      'password': 'Password',
      'enter_email': 'Enter your email',
      'enter_password': 'Enter your password',
      'login_error': 'Login error',
      'timeline': 'Timeline',
      'logout': 'Logout',
      'are_you_sure': 'Are you sure?',
      'cancel': 'Cancel',
      'yes': 'Yes',
      'view': 'View',
      'comment': 'Comment',
      'share': 'Share',
      'search': 'Search...',
      'details': 'Details',
      'description': 'Description',
      'beneficiaries': 'Beneficiaries',
      'parcels': 'Parcels',
      'cost': 'Cost',
      'comments': 'Comments',
      'write_comment': 'Write a comment...',
      'error_loading': 'Error loading data',
      'error_sending': 'Error sending comment',
      'no_description': 'No description available.',
      'purchase_request': 'Purchase Request',
      'activity': 'Activity',
      'ago': 'ago',
      'now': 'Now',
      'days': 'days',
      'hours': 'hours',
      'minutes': 'minutes',
      'filter': 'Filter',
    },
    'ar': {
      'login': 'تسجيل الدخول',
      'email': 'البريد الإلكتروني',
      'password': 'كلمة المرور',
      'enter_email': 'أدخل البريد الإلكتروني',
      'enter_password': 'أدخل كلمة المرور',
      'login_error': 'خطأ في تسجيل الدخول',
      'timeline': 'التايم لاين',
      'logout': 'تسجيل الخروج',
      'are_you_sure': 'هل أنت متأكد؟',
      'cancel': 'إلغاء',
      'yes': 'نعم',
      'view': 'عرض',
      'comment': 'تعليق',
      'share': 'مشاركة',
      'search': 'بحث...',
      'details': 'التفاصيل',
      'description': 'الوصف',
      'beneficiaries': 'المستفيدين',
      'parcels': 'الطرود',
      'cost': 'التكلفة',
      'comments': 'التعليقات',
      'write_comment': 'اكتب تعليقاً...',
      'error_loading': 'خطأ في تحميل البيانات',
      'error_sending': 'خطأ في إرسال التعليق',
      'no_description': 'لا يوجد وصف متاح.',
      'purchase_request': 'طلب شراء',
      'activity': 'نشاط',
      'ago': 'منذ',
      'now': 'الآن',
      'days': 'يوم',
      'hours': 'ساعة',
      'minutes': 'دقيقة',
      'filter': 'فلترة',
    },
  };

  String translate(String key) {
    return _localizedValues[locale.languageCode]?[key] ?? key;
  }
}

class AppTranslationsDelegate extends LocalizationsDelegate<AppTranslations> {
  const AppTranslationsDelegate();

  @override
  bool isSupported(Locale locale) => ['en', 'ar'].contains(locale.languageCode);

  @override
  Future<AppTranslations> load(Locale locale) async {
    return AppTranslations(locale);
  }

  @override
  bool shouldReload(AppTranslationsDelegate old) => false;
}
