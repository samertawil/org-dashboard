import 'package:provider/provider.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'services/language_provider.dart';
import 'services/translations.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final prefs = await SharedPreferences.getInstance();
  final bool isLoggedIn = prefs.getString('token') != null;

  runApp(
    ChangeNotifierProvider(
      create: (_) => LanguageProvider(),
      child: MyApp(isLoggedIn: isLoggedIn),
    ),
  );
}

class MyApp extends StatelessWidget {
  final bool isLoggedIn;

  const MyApp({super.key, required this.isLoggedIn});

  @override
  Widget build(BuildContext context) {
    final languageProvider = Provider.of<LanguageProvider>(context);

    return MaterialApp(
      title: 'AFSC Dashboard',
      debugShowCheckedModeBanner: false,
      locale: languageProvider.locale,
      supportedLocales: const [
        Locale('en'),
        Locale('ar'),
      ],
      localizationsDelegates: const [
        AppTranslationsDelegate(),
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: Colors.blue.shade900,
          primary: Colors.blue.shade800,
        ),
        useMaterial3: true,
        fontFamily: 'Roboto', 
      ),
      home: isLoggedIn ? const FeedScreen() : const LoginScreen(),
    );
  }
}
