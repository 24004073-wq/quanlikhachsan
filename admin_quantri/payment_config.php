<?php
function loadPaymentQrConfig(): array
{
    $configPath = dirname(__DIR__) . '/config/payment.local.json';
    $setupMessage = 'Chưa cấu hình tài khoản nhận tiền. Sao chép config/payment.example.json thành config/payment.local.json rồi điền thông tin ngân hàng.';

    if (!is_file($configPath)) {
        return [
            'configured' => false,
            'message' => $setupMessage,
        ];
    }

    $configJson = file_get_contents($configPath);
    $config = json_decode($configJson, true);
    if (!is_array($config)) {
        return [
            'configured' => false,
            'message' => 'Tệp config/payment.local.json không phải JSON hợp lệ. Hãy sửa theo cấu trúc trong config/payment.example.json.',
        ];
    }

    $bankName = trim((string)($config['bank_name'] ?? ''));
    $bankBin = trim((string)($config['bank_bin'] ?? ''));
    $accountNumber = trim((string)($config['account_number'] ?? ''));
    $accountName = trim((string)($config['account_name'] ?? ''));

    if (
        $bankName === ''
        || !preg_match('/^\d{6}$/', $bankBin)
        || !preg_match('/^\d{5,20}$/', $accountNumber)
        || $accountName === ''
    ) {
        return [
            'configured' => false,
            'message' => 'Thông tin nhận tiền chưa đầy đủ hoặc không hợp lệ. Kiểm tra tên ngân hàng, BIN 6 số, số tài khoản và tên chủ tài khoản trong config/payment.local.json.',
        ];
    }

    return [
        'configured' => true,
        'message' => '',
        'bank_name' => $bankName,
        'bank_bin' => $bankBin,
        'account_number' => $accountNumber,
        'account_name' => $accountName,
    ];
}
