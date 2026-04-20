<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder tạo tài khoản Admin mặc định cho hệ thống Greenly.
 *
 * Cách chạy:
 *   php artisan db:seed --class=AdminSeeder
 *   hoặc  php artisan db:seed   (chạy toàn bộ DatabaseSeeder)
 *
 * Ghi chú:
 *  - Dùng updateOrCreate theo email -> chạy nhiều lần không bị trùng
 *  - Nếu tài khoản đã tồn tại với đúng email, chỉ cập nhật role/status (KHÔNG đè mật khẩu)
 *    để tránh ghi đè mật khẩu admin đã đổi thủ công sau này.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'nantibiame@gmail.com';

        $user = User::where('email', $email)->first();

        if ($user) {
            // Đảm bảo quyền admin và trạng thái active, KHÔNG đụng vào mật khẩu hiện tại
            $user->update([
                'role'   => 'admin',
                'status' => 'active',
            ]);
            $this->command->info("Tài khoản Admin đã tồn tại (id={$user->id}). Đã cập nhật quyền + trạng thái.");
            return;
        }

        // Tạo mới tài khoản Admin mặc định
        User::create([
            'fullname' => 'Quản trị viên Greenly',
            'email'    => $email,
            'phone'    => '0896720844',
            'password' => Hash::make('Admin@123'), // Mặc định - nên đổi ngay sau khi đăng nhập
            'role'     => 'admin',
            'status'   => 'active',
            'address'  => 'Hà Nội, Việt Nam',
        ]);

        $this->command->info('Đã tạo tài khoản Admin mặc định: ' . $email . ' / Admin@123');
        $this->command->warn('  ⚠  Vui lòng đăng nhập và đổi mật khẩu ngay sau khi seed.');
    }
}
