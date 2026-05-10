# =====================================================================
# Script tu dong cap nhat URL ngrok cho .env va clear cache Laravel.
# Chay moi khi ngrok restart va doi URL.
#
# Cach dung:
#   .\scripts\update-ngrok.ps1 https://abcd-1234.ngrok-free.dev
# =====================================================================

param(
    [Parameter(Mandatory=$true, HelpMessage="URL ngrok HTTPS, vi du: https://abcd-1234.ngrok-free.dev")]
    [string]$NgrokUrl
)

# Validate input
if ($NgrokUrl -notmatch '^https://[a-z0-9-]+\.ngrok-free\.(app|dev)$') {
    Write-Host "[LOI] URL khong hop le. Phai co dang: https://xxx.ngrok-free.dev (khong co dau / o cuoi)" -ForegroundColor Red
    exit 1
}

# Di chuyen ve thu muc goc project (cha cua scripts/)
$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

Write-Host "=== Cap nhat URL ngrok: $NgrokUrl ===" -ForegroundColor Cyan

# 1. Doc va cap nhat .env
$envPath = Join-Path $projectRoot ".env"
if (-not (Test-Path $envPath)) {
    Write-Host "[LOI] Khong tim thay file .env" -ForegroundColor Red
    exit 1
}

$envContent = Get-Content $envPath -Raw

# Cap nhat APP_URL (ho tro ca http va https, ca localhost va IP)
$envContent = $envContent -replace '(?m)^APP_URL=.*$', "APP_URL=$NgrokUrl"

# Cap nhat MOMO_IPN_URL (luon them /api/momo/ipn)
$envContent = $envContent -replace '(?m)^MOMO_IPN_URL=.*$', "MOMO_IPN_URL=$NgrokUrl/api/momo/ipn"

# Ghi lai khong them BOM/newline thua
[System.IO.File]::WriteAllText($envPath, $envContent)

Write-Host "[OK] Da cap nhat APP_URL va MOMO_IPN_URL trong .env" -ForegroundColor Green

# 2. Hien thi gia tri moi de verify
Select-String -Path $envPath -Pattern "^APP_URL|^MOMO_IPN_URL" | ForEach-Object { Write-Host "  $($_.Line)" }

# 3. Clear cache Laravel
Write-Host ""
Write-Host "=== Clear cache Laravel ===" -ForegroundColor Cyan
& php artisan optimize:clear

Write-Host ""
Write-Host "=== HOAN TAT ===" -ForegroundColor Green
Write-Host "Hay RESTART 'php artisan serve' (Ctrl+C roi chay lai) de chac chan moi cau hinh duoc reload." -ForegroundColor Yellow
