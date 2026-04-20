<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable gửi mã OTP đến email admin.
 *
 * Được dùng cho cả 2 luồng:
 *  - Xác thực 2 lớp khi đăng nhập
 *  - Quên mật khẩu / đổi mật khẩu
 */
class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $purpose;      // 'login' hoặc 'reset_password'
    public int    $ttlSeconds;   // Số giây OTP còn hiệu lực
    public ?string $recipientName;

    public function __construct(string $code, string $purpose, int $ttlSeconds = 120, ?string $recipientName = null)
    {
        $this->code          = $code;
        $this->purpose       = $purpose;
        $this->ttlSeconds    = $ttlSeconds;
        $this->recipientName = $recipientName;
    }

    public function envelope(): Envelope
    {
        $subject = $this->purpose === 'reset_password'
            ? 'Mã xác thực đặt lại mật khẩu - Greenly Admin'
            : 'Mã xác thực đăng nhập - Greenly Admin';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'code'          => $this->code,
                'purpose'       => $this->purpose,
                'ttlSeconds'    => $this->ttlSeconds,
                'recipientName' => $this->recipientName,
            ],
        );
    }
}
