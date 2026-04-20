<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service xử lý toàn bộ nghiệp vụ OTP:
 *  - Sinh mã 6 số ngẫu nhiên
 *  - Lưu HASH xuống DB (không lưu plaintext)
 *  - Gửi email qua SMTP
 *  - Xác thực với giới hạn lần thử
 *  - Chống spam: giới hạn 1 OTP mới mỗi 60 giây cho cùng email+type
 */
class OtpService
{
    /** OTP có hiệu lực trong 120 giây */
    public const TTL_SECONDS = 120;

    /** Khoảng cách tối thiểu giữa 2 lần gửi OTP cùng loại (chống spam) */
    public const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Sinh OTP mới, lưu DB, gửi email.
     *
     * @throws \RuntimeException  khi đang trong thời gian cooldown
     */
    public function sendOtp(string $email, string $type, ?string $recipientName = null, ?string $ipAddress = null): OtpCode
    {
        // 1. Kiểm tra cooldown - tránh spam / chi phí mail
        $recent = OtpCode::where('email', $email)
            ->where('type', $type)
            ->where('created_at', '>=', Carbon::now()->subSeconds(self::RESEND_COOLDOWN_SECONDS))
            ->latest('id')
            ->first();

        if ($recent) {
            $waitSec = self::RESEND_COOLDOWN_SECONDS - $recent->created_at->diffInSeconds(Carbon::now());
            throw new \RuntimeException("Vui lòng đợi {$waitSec} giây trước khi yêu cầu mã mới.");
        }

        // 2. Vô hiệu hóa các OTP cũ cùng email+type (chưa used) để chỉ có 1 OTP hoạt động tại một thời điểm
        OtpCode::where('email', $email)
            ->where('type', $type)
            ->whereNull('used_at')
            ->update(['used_at' => Carbon::now()]);

        // 3. Sinh OTP 6 chữ số
        $plainCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // 4. Lưu bản ghi mới
        $otp = OtpCode::create([
            'email'      => $email,
            'code_hash'  => Hash::make($plainCode),
            'type'       => $type,
            'attempts'   => 0,
            'expires_at' => Carbon::now()->addSeconds(self::TTL_SECONDS),
            'ip_address' => $ipAddress,
        ]);

        // 5. Gửi email (throw lên nếu lỗi SMTP để controller bắt và hiển thị cho user)
        try {
            Mail::to($email)->send(
                new OtpMail($plainCode, $type, self::TTL_SECONDS, $recipientName)
            );
        } catch (\Throwable $e) {
            Log::error('Gửi OTP email thất bại', [
                'email'   => $email,
                'type'    => $type,
                'message' => $e->getMessage(),
            ]);
            // Xoá bản ghi OTP vừa tạo vì gửi mail thất bại
            $otp->delete();
            throw new \RuntimeException('Không thể gửi email xác thực. Vui lòng kiểm tra cấu hình SMTP hoặc thử lại sau.');
        }

        return $otp;
    }

    /**
     * Xác thực OTP. Trả về true nếu đúng, false nếu sai.
     * Mỗi lần sai => tăng attempts. Quá MAX_ATTEMPTS => invalidate OTP.
     */
    public function verify(string $email, string $type, string $code): bool
    {
        // Lấy OTP mới nhất còn hiệu lực
        $otp = OtpCode::where('email', $email)
            ->where('type', $type)
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now())
            ->latest('id')
            ->first();

        if (!$otp) {
            return false;
        }

        // Kiểm tra số lần thử
        if ($otp->attempts >= OtpCode::MAX_ATTEMPTS) {
            $otp->update(['used_at' => Carbon::now()]); // Invalidate
            return false;
        }

        // So khớp hash
        if (!Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            return false;
        }

        // Thành công -> đánh dấu đã sử dụng
        $otp->update(['used_at' => Carbon::now()]);
        return true;
    }

    /**
     * Tính số giây còn lại trước khi có thể yêu cầu OTP mới.
     * Trả về 0 nếu có thể gửi ngay.
     */
    public function cooldownRemaining(string $email, string $type): int
    {
        $recent = OtpCode::where('email', $email)
            ->where('type', $type)
            ->where('created_at', '>=', Carbon::now()->subSeconds(self::RESEND_COOLDOWN_SECONDS))
            ->latest('id')
            ->first();

        if (!$recent) {
            return 0;
        }

        return max(0, self::RESEND_COOLDOWN_SECONDS - $recent->created_at->diffInSeconds(Carbon::now()));
    }
}
