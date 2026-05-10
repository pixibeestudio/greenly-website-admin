<?php

/*
|--------------------------------------------------------------------------
| Cấu hình MoMo Payment Gateway
|--------------------------------------------------------------------------
|
| File này tập trung mọi cấu hình liên quan đến tích hợp thanh toán MoMo.
| Dữ liệu được đọc từ .env, có giá trị mặc định là credentials Sandbox
| public do MoMo cung cấp cho mục đích test (https://developers.momo.vn).
|
*/

return [

    // Mã đối tác do MoMo cấp (Sandbox: MOMO)
    'partner_code' => env('MOMO_PARTNER_CODE', 'MOMO'),

    // Access Key dùng để ký request (Sandbox public)
    'access_key' => env('MOMO_ACCESS_KEY', 'F8BBA842ECF85'),

    // Secret Key dùng cho HMAC-SHA256 (Sandbox public)
    'secret_key' => env('MOMO_SECRET_KEY', 'K951B6PE1waDMi640xX08PD3vg6EkVlz'),

    // Endpoint API MoMo (Sandbox)
    'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),

    /*
    |--------------------------------------------------------------------------
    | URL Public cho IPN (Instant Payment Notification)
    |--------------------------------------------------------------------------
    | MoMo sẽ POST kết quả giao dịch vào URL này sau khi user thanh toán.
    | URL bắt buộc phải PUBLIC (Internet) → cần dùng ngrok khi dev local.
    | Ví dụ: https://abcd-1234.ngrok-free.app/api/momo/ipn
    */
    'ipn_url' => env('MOMO_IPN_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Redirect URL sau khi thanh toán
    |--------------------------------------------------------------------------
    | Sau khi user thanh toán xong trên app MoMo, MoMo redirect về URL này.
    | Với app Android, dùng deeplink scheme riêng để mở lại app Greenly.
    */
    'redirect_url' => env('MOMO_REDIRECT_URL', 'greenly://momo-callback'),

    /*
    |--------------------------------------------------------------------------
    | Loại request gửi đến MoMo
    |--------------------------------------------------------------------------
    | - captureWallet : Thanh toán qua ví MoMo (phổ biến nhất, hỗ trợ cả app + QR)
    | - payWithMethod : Thanh toán qua thẻ ATM/Visa
    | - payWithATM    : Chỉ ATM nội địa
    */
    'request_type' => env('MOMO_REQUEST_TYPE', 'captureWallet'),

    /*
    |--------------------------------------------------------------------------
    | Cấu hình QR Code Generator
    |--------------------------------------------------------------------------
    | Dùng dịch vụ qrserver.com để encode payUrl thành ảnh QR (free, không cần API key).
    | Frontend có thể dùng template này để render QR.
    */
    'qr_generator_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=',

    /*
    |--------------------------------------------------------------------------
    | Timeout khi gọi MoMo API (giây)
    |--------------------------------------------------------------------------
    */
    'http_timeout' => 15,

    /*
    |--------------------------------------------------------------------------
    | Bật ghi log chi tiết request/response để debug
    |--------------------------------------------------------------------------
    | Set false ở production để tránh log lộ thông tin nhạy cảm.
    */
    'debug_log' => env('MOMO_DEBUG_LOG', true),
];
