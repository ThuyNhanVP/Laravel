<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Hiển thị form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|min:8',
            'password' => 'required',
        ]);

        // Lấy user theo username
        $user = User::where('username', $request->username)->first();

        if ($user && md5($request->password) === $user->password) {
            Auth::login($user);
            return response()->json(['success' => true, 'message' => 'Đăng nhập thành công!']);
        }


        return response()->json(['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng!']);
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'username' => 'required|string|min:8|unique:users,username',
            'password' => 'required|string|min:6',
            'phone' => ['required', 'regex:/^0(3|5|7|8|9)[0-9]{8}$/'],
        ]);

        // ⚠️ Nếu DB cũ đang dùng md5:
        // $hashed_password = md5($request->password);

        // Nếu bạn muốn nâng cấp sang bcrypt (nên dùng):
        $hashed_password = Hash::make($request->password);

        $user = User::create([
            'ho_ten' => $request->ho_ten,
            'username' => $request->username,
            'password' => $hashed_password,
            'SDT' => $request->phone,
            'promo_start' => now(),
            'promo_end' => now()->addHours(2),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công!'
        ]);
    }

}


