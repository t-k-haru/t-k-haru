<?php
// ============================================================
// Shift OS — Stripe Webhook Handler
// ============================================================

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

define('STRIPE_SECRET',      '__REDACTED_STRIPE_WEBHOOK_SECRET__');
define('GA4_MEASUREMENT_ID', 'G-Y6XF74H9N4');
define('GA4_API_SECRET',     '__REDACTED_GA4_API_SECRET__');

define('SMTP_HOST',     'smtp.lolipop.jp');
define('SMTP_PORT',     465);
define('SMTP_USER',     'info@shift.nobushi.jp');
define('SMTP_PASS',     '__REDACTED_SMTP_PASSWORD__');
define('FROM_EMAIL',    'info@shift.nobushi.jp');
define('FROM_NAME',     'Shift OS');
define('NOTIFY_EMAIL',  'tkharu25@icloud.com');

// ── 署名検証 ──────────────────────────────────────────────────
$payload    = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if (!verifySignature($payload, $sig_header, STRIPE_SECRET)) {
    http_response_code(400);
    exit('Bad signature');
}

// ── イベント処理 ───────────────────────────────────────────────
$event = json_decode($payload, true);

if (($event['type'] ?? '') === 'checkout.session.completed') {
    $session = $event['data']['object'];
    $email   = $session['customer_details']['email'] ?? '';
    $name    = $session['customer_details']['name']  ?? '';
    $amount  = ($session['amount_total'] ?? 0) / 100;
    $tx_id   = $session['id'] ?? '';

    if ($email) {
        sendHearingEmail($email, $name);
        sendNotifyEmail($email, $name, $amount);
        trackGA4($email, $amount, $tx_id);
    }
}

http_response_code(200);
echo json_encode(['status' => 'ok']);
exit;

// ============================================================
// 署名検証
// ============================================================
function verifySignature(string $payload, string $sig, string $secret): bool {
    if (!$sig) return false;
    $parts = [];
    foreach (explode(',', $sig) as $p) {
        [$k, $v] = explode('=', $p, 2);
        $parts[$k][] = $v;
    }
    $ts   = $parts['t'][0]  ?? null;
    $sigs = $parts['v1']    ?? [];
    if (!$ts || empty($sigs)) return false;
    if (abs(time() - (int)$ts) > 300) return false;
    $expected = hash_hmac('sha256', $ts . '.' . $payload, $secret);
    foreach ($sigs as $s) {
        if (hash_equals($expected, $s)) return true;
    }
    return false;
}

// ============================================================
// ヒアリングメール（顧客宛）
// ============================================================
function sendHearingEmail(string $to, string $name): void {
    $display = $name ?: 'お客様';
    $subject = '【Shift OS】カスタムシフト管理システムのお申し込みありがとうございます';
    $body    = <<<EOT
{$display} 様

このたびは Shift OS カスタムシフト管理システムにお申し込みいただき、誠にありがとうございます。
システムの構築に向けて、いくつか質問をいたします。

1. 業種・業態
   （例：居酒屋、カフェ、アパレル、美容室 など）

2. スタッフ規模
   （例：アルバイト8名、社員2名 など）

3. 現在のシフト管理方法
   （例：LINE、Excel、紙 など）

4. 解決したい課題・欲しい機能
   （例：深夜割増の自動計算、ポジション管理 など）
   ※ 特になければ構いません

ご返信いただき次第、要件定義に入ります。
すぐに汎用システムをご利用いただきたい場合は、その旨をお知らせください。

ご不明な点はいつでもご返信ください。
Shift OS Haru Takayanagi
EOT;

    sendMail($to, $subject, $body);
}

// ============================================================
// 申し込み通知メール（自分宛）
// ============================================================
function sendNotifyEmail(string $email, string $name, float $amount): void {
    $subject = '【Shift OS】新規申し込み: ' . ($name ?: $email);
    $body    = <<<EOT
新規申し込みがありました。

名前  : {$name}
メール: {$email}
金額  : ¥{$amount}

Stripe:
https://dashboard.stripe.com/
EOT;
    sendMail(NOTIFY_EMAIL, $subject, $body);
}

// ============================================================
// GA4 Measurement Protocol
// ============================================================
function trackGA4(string $email, float $amount, string $tx_id): void {
    $data = [
        'client_id' => hash('sha256', $email),
        'events'    => [[
            'name'   => 'purchase',
            'params' => [
                'transaction_id' => $tx_id,
                'value'          => $amount,
                'currency'       => 'JPY',
                'items'          => [[
                    'item_id'   => 'shift_os_monthly',
                    'item_name' => 'Shift OS 月額プラン',
                    'price'     => $amount,
                    'quantity'  => 1,
                ]],
            ],
        ]],
    ];
    $url = 'https://www.google-analytics.com/mp/collect?measurement_id='
         . GA4_MEASUREMENT_ID . '&api_secret=' . GA4_API_SECRET;
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

// ============================================================
// メール送信（iCloud SMTP経由）
// ============================================================
function sendMail(string $to, string $subject, string $body): void {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host        = SMTP_HOST;
        $mail->SMTPAuth    = true;
        $mail->Username    = SMTP_USER;
        $mail->Password    = SMTP_PASS;
        $mail->SMTPSecure  = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port        = SMTP_PORT;
        $mail->CharSet     = 'UTF-8';
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);
        $mail->Subject     = $subject;
        $mail->Body        = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log('Mailer Error: ' . $e->getMessage());
    }
}
