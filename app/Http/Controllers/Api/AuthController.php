<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Đăng ký tài khoản mới cho Mobile App
    public function register(Request $request)
    {
        // Validate dữ liệu với rules khắt khe
        $validator = Validator::make($request->all(), [
            'fullname'              => 'required|string|unique:users,fullname',
            'email'                 => ['required', 'email', 'unique:users,email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],
            'password'              => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[a-zA-Z\d\W_]+$/', 'confirmed'],
            'password_confirmation' => 'required',
        ], [
            'fullname.unique'    => 'Họ và tên đã được sử dụng.',
            'email.unique'       => 'Email này đã đăng ký.',
            'email.regex'        => 'Email phải nhập đúng dạng @gmail.com',
            'password.regex'     => 'Mật khẩu phải có 8 ký tự \'A, a, 0-9, @ - # %_ %...\'',
            'password.min'       => 'Mật khẩu phải có 8 ký tự \'A, a, 0-9, @ - # %_ %...\'',
            'password.confirmed' => 'Mật khẩu không khớp nhau, Vui lòng kiểm tra lại..',
        ]);

        // Trả lỗi validation nếu có
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Tạo tài khoản mới
        $user = User::create([
            'fullname' => $request->fullname,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký tài khoản thành công!',
            'data'    => [
                'id'       => $user->id,
                'fullname' => $user->fullname,
                'email'    => $user->email,
                'role'     => $user->role,
            ],
        ], 201);
    }

    // Đăng nhập tài khoản cho Mobile App
    public function login(Request $request)
    {
        // Validate email trước
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/', 'exists:users,email'],
            'password' => 'required',
        ], [
            'email.regex'  => 'Email phải đúng theo dạng ..@gmail.com',
            'email.exists' => 'Email chưa được đăng ký tài khoản',
        ]);

        // Trả lỗi validation nếu có
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Kiểm tra mật khẩu
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors'  => [
                    'password' => ['Mật khẩu chưa đúng, Vui lòng kiểm tra lại!'],
                ],
            ], 422);
        }

        // Tạo token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Xử lý đường dẫn tuyệt đối cho avatar (tránh lặp storage/storage)
        if ($user->avatar) {
            $avatarPath = $user->avatar;
            $avatarPath = str_replace('storage/storage/', 'storage/', $avatarPath);
            if (!str_starts_with($avatarPath, 'storage/') && !str_starts_with($avatarPath, 'http')) {
                $avatarPath = 'storage/' . $avatarPath;
            }
            $user->avatar = asset($avatarPath);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'user'  => $user,
                'token' => $token,
            ],
        ], 200);
    }
}
