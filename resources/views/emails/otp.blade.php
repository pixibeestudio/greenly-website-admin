<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Mã xác thực Greenly</title>
</head>
{{-- Email HTML: dùng inline CSS vì các client email (Gmail, Outlook...) không hỗ trợ Tailwind --}}
<body style="margin:0;padding:0;background-color:#f6f8f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f6f8f5;padding:40px 10px;">
    <tr>
        <td align="center">

            <!-- Card chính -->
            <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="background-color:#ffffff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.06);overflow:hidden;max-width:560px;width:100%;">

                <!-- HEADER gradient xanh lá -->
                <tr>
                    <td style="background:linear-gradient(135deg,#0f3d1f 0%,#1b5e20 50%,#2e7d32 100%);padding:32px 40px;text-align:center;color:#ffffff;">
                        <div style="display:inline-block;width:60px;height:60px;background-color:#f9a825;border-radius:50%;line-height:60px;font-size:28px;font-weight:bold;color:#ffffff;margin-bottom:12px;">🌱</div>
                        <h1 style="margin:0;font-size:24px;font-weight:700;letter-spacing:1px;">GREENLY ADMIN</h1>
                        <p style="margin:6px 0 0;font-size:12px;opacity:0.85;letter-spacing:2px;">XÁC THỰC BẢO MẬT</p>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:36px 40px 24px;color:#374151;">
                        <p style="margin:0 0 12px;font-size:15px;">Xin chào <strong>{{ $recipientName ?? 'Quản trị viên' }}</strong>,</p>

                        @if ($purpose === 'reset_password')
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#6b7280;">
                                Chúng tôi nhận được yêu cầu <strong>đặt lại mật khẩu</strong> cho tài khoản của bạn. Vui lòng sử dụng mã xác thực bên dưới để tiếp tục:
                            </p>
                        @else
                            <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#6b7280;">
                                Ai đó đang cố gắng <strong>đăng nhập</strong> vào tài khoản quản trị của bạn. Để hoàn tất đăng nhập, vui lòng nhập mã xác thực sau:
                            </p>
                        @endif

                        <!-- Mã OTP -->
                        <div style="text-align:center;margin:28px 0;">
                            <div style="display:inline-block;background-color:#f3faf4;border:2px dashed #2e7d32;border-radius:12px;padding:18px 32px;">
                                <p style="margin:0 0 6px;font-size:11px;color:#6b7280;letter-spacing:2px;font-weight:600;">MÃ XÁC THỰC CỦA BẠN</p>
                                <p style="margin:0;font-size:38px;font-weight:700;color:#1b5e20;letter-spacing:10px;font-family:'Courier New',monospace;">{{ $code }}</p>
                            </div>
                        </div>

                        <!-- Thời hạn -->
                        <div style="background-color:#fff8e1;border-left:4px solid #f9a825;padding:12px 16px;border-radius:6px;margin-bottom:24px;">
                            <p style="margin:0;font-size:13px;color:#92400e;">
                                ⏱ <strong>Mã có hiệu lực trong {{ $ttlSeconds }} giây</strong>. Sau thời gian này, bạn cần yêu cầu mã mới.
                            </p>
                        </div>

                        <p style="margin:0 0 12px;font-size:13px;color:#6b7280;line-height:1.6;">
                            Nếu bạn <strong>không phải là người thực hiện yêu cầu</strong> này, vui lòng bỏ qua email và kiểm tra bảo mật tài khoản ngay lập tức.
                        </p>

                        <p style="margin:24px 0 0;font-size:13px;color:#9ca3af;">
                            Trân trọng,<br>
                            <strong style="color:#2e7d32;">Đội ngũ Greenly</strong>
                        </p>
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background-color:#f9fafb;padding:20px 40px;text-align:center;border-top:1px solid #e5e7eb;">
                        <p style="margin:0;font-size:12px;color:#9ca3af;">
                            © {{ date('Y') }} Greenly. Sản phẩm sạch — Cuộc sống xanh.<br>
                            Đây là email tự động, vui lòng không trả lời.
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
