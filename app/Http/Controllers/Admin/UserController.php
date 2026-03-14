<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 1. Truy vấn danh sách người dùng
        $query = User::query();

        // 2. Tìm kiếm theo fullname, email hoặc phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // 3. Lọc theo vai trò (role)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 4. Sắp xếp mới nhất trước
        $query->latest();

        // 5. Phân trang
        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage)->appends($request->query());

        // 6. Thống kê cho 4 Card
        $totalUsers = User::count();
        $newCustomersThisMonth = User::where('role', 'customer')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $totalShippers = User::where('role', 'shipper')->count();
        $lockedAccounts = User::where('status', 'locked')->count();

        return view('admin.users.index', compact(
            'users', 'totalUsers', 'newCustomersThisMonth', 'totalShippers', 'lockedAccounts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:admin,customer,shipper',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'fullname.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.max' => 'Ảnh đại diện không được quá 2MB.',
        ]);

        $data = $request->only(['fullname', 'email', 'phone', 'address', 'role']);
        $data['password'] = Hash::make($request->password);
        $data['status'] = 'active';

        // Upload avatar nếu có
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Thêm tài khoản mới thành công!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'fullname' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:admin,customer,shipper',
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'fullname.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.max' => 'Ảnh đại diện không được quá 2MB.',
        ]);

        $data = $request->only(['fullname', 'email', 'phone', 'address', 'role']);

        // Chỉ cập nhật mật khẩu nếu có nhập mới
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Upload avatar mới nếu có
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu tồn tại
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật tài khoản "' . $user->fullname . '" thành công!');
    }

    public function destroy(User $user)
    {
        // Xóa avatar nếu tồn tại
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã xóa tài khoản "' . $user->fullname . '" thành công!');
    }

    // Khóa / Mở khóa tài khoản
    public function toggleLock(User $user)
    {
        // Không cho phép Admin tự khóa chính mình (giả định user đang đăng nhập có id = 1)
        // Trong thực tế sẽ dùng: auth()->id() === $user->id
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể khóa tài khoản Admin!');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'locked' : 'active',
        ]);

        $message = $user->status === 'locked'
            ? 'Đã khóa tài khoản "' . $user->fullname . '".'
            : 'Đã mở khóa tài khoản "' . $user->fullname . '".';

        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }
}
