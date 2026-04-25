# تطبيق الموبايل لـ ORG Dashboard

هذا المجلد يحتوي على الكود المصدري لتطبيق الموبايل المطور باستخدام **Flutter**.

## الإعدادات الحالية
- **الرابط الأساسي (API):** `https://app.afscgaza.org/api`
- **التقنية:** Flutter 3.x

## كيف تحصل على ملف APK لتجربته على موبايلك؟

### الخيار الأول: استخدام Codemagic (الأسهل بدون خبرة برمجة)
1. قم بإنشاء حساب في [Codemagic.io](https://codemagic.io).
2. قم بربط مستودع الكود الخاص بك (GitHub/GitLab) أو قم بضغط هذا المجلد (`mobile`) ورفعه.
3. اختر نوع التطبيق **Flutter App**.
4. في إعدادات الـ Workflow، اختر **Android** وتأكد من تفعيل خيار **Build APK**.
5. اضغط على **Start Build**.
6. بعد انتهاء العملية (حوالي 5-10 دقائق)، سيرسل لك الموقع رابطاً لتحميل ملف الـ APK مباشرة.

### الخيار الثاني: البناء المحلي (للمبرمجين)
إذا كان لديك Flutter مثبت على جهازك:
1. ادخل للمجلد عبر Terminal: `cd mobile`
2. نفذ الأمر: `flutter pub get`
3. نفذ الأمر: `flutter build apk --release`
4. ستجد الملف الناتج في المسار: `build/app/outputs/flutter-apk/app-release.apk`

## ملاحظات هامة
- تأكد أن السيرفر الأونلاين يحتوي على ملفات الـ API التالية:
  - `app/Http/Controllers/Api/AuthController.php`
  - `app/Http/Controllers/Api/FeedController.php`
  - `routes/api.php` المحدث.
- التطبيق يدعم تسجيل الدخول وجلب الـ Feed والتعليقات.
