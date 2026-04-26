<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
            }

            $token = $user->createToken($request->device_name)->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        } catch (Exception $e) {
            // هذا السطر سيمنع التحويل وسيظهر لك الخطأ الحقيقي
            return response()->json([
                'message' => 'خطأ بالسيرفر: ' . $e->getMessage()
            ], 500);
        }
    }
}
