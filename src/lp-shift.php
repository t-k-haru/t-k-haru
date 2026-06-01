<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slp_consult'])) {
    $email   = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $purpose = htmlspecialchars($_POST['purpose'] ?? '', ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8');

    if (!$email || !$purpose) {
        echo json_encode(['ok' => false, 'error' => 'メールアドレスと目的は必須です']);
        exit;
    }

    // ── DB保存（バックアップ）────────────────────────────────────────
    $db_saved = false;
    try {
        $pdo = new PDO(
            'mysql:host=mysql80-3.lolipop.lan;dbname=LAA1708822-attendance;charset=utf8mb4',
            'LAA1708822', '__REDACTED_DB_PASSWORD__',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $pdo->exec("CREATE TABLE IF NOT EXISTS lp_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            purpose VARCHAR(255) NOT NULL,
            message TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $stmt = $pdo->prepare("INSERT INTO lp_inquiries (email, purpose, message) VALUES (?, ?, ?)");
        $stmt->execute([$email, $purpose, $message]);
        $db_saved = true;
    } catch (Exception $e) {
        // DB保存失敗は無視
    }

    // ── SMTP認証送信 ─────────────────────────────────────────────────
    function slp_smtp_send($from, $from_name, $to, $subject, $body_text) {
        $smtp_host = 'ssl://smtp.lolipop.jp';
        $smtp_port = 465;
        $smtp_user = 'info@shift.nobushi.jp';
        $smtp_pass = '__REDACTED_SMTP_PASSWORD__';

        $subject_enc = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $body_b64    = chunk_split(base64_encode($body_text));
        $date        = date('r');
        $boundary    = ''; // plain textのみ

        $raw  = "Date: {$date}\r\n";
        $raw .= "From: =?UTF-8?B?" . base64_encode($from_name) . "?= <{$from}>\r\n";
        $raw .= "To: {$to}\r\n";
        $raw .= "Subject: {$subject_enc}\r\n";
        $raw .= "MIME-Version: 1.0\r\n";
        $raw .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $raw .= "Content-Transfer-Encoding: base64\r\n";
        $raw .= "\r\n";
        $raw .= $body_b64;

        $errno = 0; $errstr = '';
        $sock = @stream_socket_client("{$smtp_host}:{$smtp_port}", $errno, $errstr, 15);
        if (!$sock) return ['ok' => false, 'err' => "connect: {$errstr}"];

        stream_set_timeout($sock, 10);

        $read = function() use ($sock) {
            $buf = '';
            while ($line = fgets($sock, 512)) {
                $buf .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }
            return $buf;
        };

        $read(); // 220 greeting

        fwrite($sock, "EHLO shift.nobushi.jp\r\n"); $read();
        fwrite($sock, "AUTH LOGIN\r\n");             $read();
        fwrite($sock, base64_encode($smtp_user) . "\r\n"); $read();
        fwrite($sock, base64_encode($smtp_pass) . "\r\n");
        $auth_resp = $read();
        if (strpos($auth_resp, '235') === false) {
            fclose($sock);
            return ['ok' => false, 'err' => "auth: {$auth_resp}"];
        }

        fwrite($sock, "MAIL FROM:<{$from}>\r\n");   $read();
        fwrite($sock, "RCPT TO:<{$to}>\r\n");        $read();
        fwrite($sock, "DATA\r\n");                   $read();
        fwrite($sock, $raw . "\r\n.\r\n");
        $data_resp = $read();
        fwrite($sock, "QUIT\r\n");
        fclose($sock);

        return strpos($data_resp, '250') !== false
            ? ['ok' => true]
            : ['ok' => false, 'err' => "data: {$data_resp}"];
    }

    $to      = 'tkharu25@icloud.com';
    $subject = '【Shift OS】お問い合せフォームからのお問い合わせ';
    $body    = "■ メールアドレス\n{$email}\n\n■ 目的\n{$purpose}\n\n■ メッセージ\n{$message}\n";

    $smtp = slp_smtp_send('info@shift.nobushi.jp', 'Shift OS', $to, $subject, $body);

    if ($smtp['ok'] || $db_saved) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'error' => '送信に失敗しました。時間をおいて再度お試しください。']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shift OS | カスタムシフト管理システム</title>
<meta name="description" content="シフト管理・タイムカード・勤怠集計をあなたの業務専用にカスタム。飲食・介護・美容・小売など全業種対応。月額¥11,000（先行価格）、初期費用0円。">
<meta property="og:title" content="Shift OS | カスタムシフト管理システム">
<meta property="og:description" content="シフト管理・タイムカード・勤怠集計をあなたの業務専用にカスタム。月額¥11,000（先行価格）、初期費用0円。">
<meta property="og:url" content="https://shift.nobushi.jp/">
<meta property="og:type" content="website">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XMFZYV50TL"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XMFZYV50TL');
</script>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '2727820647598093');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=2727820647598093&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
<style>
html, body { margin: 0; padding: 0; background: #000; scroll-behavior: smooth; }
*::-webkit-scrollbar { display: none; }
* { scrollbar-width: none; }
.slp * { box-sizing: border-box; }
.slp {
  font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Neue', sans-serif;
  color: #fff !important;
  max-width: 720px;
  margin: 0 auto;
  padding: 0 20px;
}
.slp section { padding: 64px 0; border-bottom: 1px solid rgba(255,255,255,.1); }
.slp section:last-child { border-bottom: none; }
.slp-tag {
  display: inline-block;
  background: #fff; color: #000;
  font-size: 11px; font-weight: 700;
  padding: 4px 12px; border-radius: 999px;
  letter-spacing: .5px; margin-bottom: 16px;
}
.slp h1 {
  font-size: clamp(26px, 5vw, 42px);
  font-weight: 700; line-height: 1.2;
  margin: 0 0 16px; letter-spacing: -.02em;
  color: #fff !important;
}
.slp h2 {
  font-size: clamp(20px, 3.5vw, 28px);
  font-weight: 700; margin: 0 0 28px;
  letter-spacing: -.01em; color: #fff !important;
}
.slp p { color: rgba(255,255,255,.6) !important; line-height: 1.7; font-size: 15px; margin: 0 0 16px; }
.slp-hero { text-align: center; padding: 64px 0 48px; }
.slp-hero p { max-width: 460px; margin: 0 auto 28px; font-size: 15px; color: rgba(255,255,255,.6) !important; }
.slp-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 48px; }
.slp-btn-p {
  background: #fff; color: #000 !important;
  padding: 14px 36px; border-radius: 999px;
  font-size: 15px; font-weight: 700;
  text-decoration: none; display: inline-block;
  border: none; cursor: pointer;
}
.slp-btn-p:hover { background: #ddd; }
.slp-btn-s {
  background: transparent; color: #fff !important;
  border: 1px solid rgba(255,255,255,.35);
  padding: 14px 36px; border-radius: 999px;
  font-size: 15px; font-weight: 700;
  text-decoration: none; display: inline-block; cursor: pointer;
}
.slp-btn-s:hover { border-color: #fff; }
.slp-pain-list { list-style: none; padding: 0; margin: 0; }
.slp-pain-item {
  display: flex; align-items: flex-start; gap: 14px;
  padding: 16px 0; border-bottom: 1px solid rgba(255,255,255,.08);
}
.slp-pain-item:last-child { border-bottom: none; }
.slp-pain-x { font-size: 17px; color: #fff; flex-shrink: 0; line-height: 1.5; font-weight: 700; }
.slp-pain-text strong { display: block; font-size: 14px; font-weight: 700; margin-bottom: 3px; color: #fff !important; }
.slp-pain-text span { font-size: 12px; color: rgba(255,255,255,.45) !important; }
.slp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
.slp-card {
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
  border-radius: 12px; padding: 24px;
}
.slp-card h3 { font-size: 15px; font-weight: 700; margin: 0 0 8px; color: #fff !important; }
.slp-card p { font-size: 13px; color: rgba(255,255,255,.55) !important; margin: 0; line-height: 1.65; }
.slp-pricing-card {
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);
  border-radius: 16px; padding: 32px; max-width: 480px; margin: 0 auto;
}
.slp-price-main { font-size: clamp(40px, 7vw, 56px); font-weight: 800; line-height: 1; margin: 0 0 4px; color: #fff !important; }
.slp-price-unit { font-size: 16px; font-weight: 400; color: rgba(255,255,255,.5) !important; }
.slp-price-note { font-size: 13px; color: rgba(255,255,255,.45) !important; margin: 4px 0 20px; }
.slp-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px; }
.slp-pill {
  background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);
  border-radius: 999px; padding: 5px 14px;
  font-size: 12px; font-weight: 600; color: rgba(255,255,255,.75) !important;
}
.slp-table-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.08);
  font-size: 13px; color: rgba(255,255,255,.55) !important;
}
.slp-table-row:last-child { border-bottom: none; }
.slp-table-val { font-weight: 700; color: #fff !important; }
.slp-cta-center { text-align: center; margin-top: 28px; }
.slp-faq-item { padding: 20px 0; border-bottom: 1px solid rgba(255,255,255,.08); }
.slp-faq-item:last-child { border-bottom: none; }
.slp-faq-q { font-size: 14px; font-weight: 700; margin: 0 0 8px; color: #fff !important; }
.slp-faq-a { font-size: 13px; color: rgba(255,255,255,.55) !important; margin: 0; line-height: 1.65; }
.slp-section-note { font-size: 14px; color: rgba(255,255,255,.55) !important; margin: 0 0 28px; line-height: 1.6; }
.slp-terms-link {
  color: rgba(255,255,255,.5) !important; font-size: 12px;
  text-decoration: underline; cursor: pointer; background: none;
  border: none; padding: 0; font-family: inherit;
}
.slp-terms-link:hover { color: #fff !important; }
.slp-header {
  position: sticky; top: 0; z-index: 100;
  background: rgba(0,0,0,.75); backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255,255,255,.1);
  padding: 0 24px; display: flex; align-items: center;
  justify-content: space-between; height: 52px;
  font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Neue', sans-serif;
}
.slp-header-brand { font-size: 14px; font-weight: 700; color: #fff; text-decoration: none; white-space: nowrap; }
.slp-header-cta {
  padding: 7px 16px; border-radius: 999px; font-size: 13px; font-weight: 700;
  background: #fff; color: #000 !important; text-decoration: none; white-space: nowrap;
}
@media (max-width: 600px) {
  .slp section { padding: 48px 0; }
  .slp-hero { padding: 48px 0 40px; }
  .slp-pricing-card { padding: 24px; }
}
@media (max-width: 480px) {
  .slp-header { padding: 0 14px; }
}
#slp-demo-wrap p { color: #6b6560 !important; margin: 0; }
</style>
</head>
<body>
<div class="slp">
<main id="main-content">
<header class="slp-header">
  <a href="#" class="slp-header-brand" style="text-decoration:none;display:inline-flex;align-items:center;">
<span style="font-size:16px;font-weight:700;letter-spacing:-.5px;color:#185FA5;">Shift<span style="font-weight:400;color:#185FA5;margin-left:2px;">OS</span></span>
  </a>
  <div style="display:flex;gap:8px;align-items:center;flex-shrink:0;">
    <button onclick="slpOpenConsult()" class="slp-btn-s" style="padding:7px 16px;font-size:13px;border:1px solid rgba(255,255,255,.5);cursor:pointer;">無料相談</button>
    <a href="https://buy.stripe.com/14A5kC6dc7z77r0etTafS0Z" target="_blank" rel="noopener noreferrer" class="slp-header-cta">申し込む</a>
  </div>
</header>

<section class="slp-hero">
  <span class="slp-tag" style="background:#185FA5;color:#fff;">カスタムシフト管理システム</span>
  <h1>あなたの業務に合わせた<br>シフト管理を。</h1>
  <!-- インタラクティブデモ（Macスクリーン） -->
  <div style="margin-bottom:32px;" id="slp-demo-wrap">
    <div style="max-width:680px;margin:0 auto;">
      <!-- ブラウザ風フレーム（モダンSafari / Liquid Glass） -->
      <div style="background:rgba(245,245,247,.95);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-radius:12px;overflow:hidden;box-shadow:0 12px 48px rgba(0,0,0,.35),0 0 0 1px rgba(0,0,0,.1),inset 0 1px 0 rgba(255,255,255,.8);">
        <!-- Safari風ツールバー -->
        <div style="background:rgba(248,248,250,.92);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-bottom:1px solid rgba(0,0,0,.08);padding:8px 12px;display:flex;align-items:center;gap:8px;">
          <!-- ウィンドウコントロール -->
          <div style="display:flex;gap:5px;flex-shrink:0;">
            <div style="width:11px;height:11px;border-radius:50%;background:#ff5f57;box-shadow:inset 0 0 0 .5px rgba(0,0,0,.1);"></div>
            <div style="width:11px;height:11px;border-radius:50%;background:#febc2e;box-shadow:inset 0 0 0 .5px rgba(0,0,0,.1);"></div>
            <div style="width:11px;height:11px;border-radius:50%;background:#28c840;box-shadow:inset 0 0 0 .5px rgba(0,0,0,.1);"></div>
          </div>
          <!-- ナビゲーション -->
          <div style="display:flex;gap:4px;flex-shrink:0;">
            <button aria-label="前に戻る" style="width:26px;height:22px;border:none;background:transparent;cursor:pointer;border-radius:5px;display:flex;align-items:center;justify-content:center;" onmouseenter="this.style.background='rgba(0,0,0,.06)'" onmouseleave="this.style.background='transparent'">
              <svg width="8" height="12" viewBox="0 0 8 12" fill="none"><path d="M7 1L2 6l5 5" stroke="rgba(0,0,0,.5)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button aria-label="次に進む" style="width:26px;height:22px;border:none;background:transparent;cursor:pointer;border-radius:5px;display:flex;align-items:center;justify-content:center;" onmouseenter="this.style.background='rgba(0,0,0,.06)'" onmouseleave="this.style.background='transparent'">
              <svg width="8" height="12" viewBox="0 0 8 12" fill="none"><path d="M1 1l5 5-5 5" stroke="rgba(0,0,0,.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </div>
          <!-- URLバー（Liquid Glass） -->
          <div style="flex:1;background:rgba(255,255,255,.7);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border-radius:7px;height:26px;display:flex;align-items:center;justify-content:center;gap:6px;border:1px solid rgba(0,0,0,.08);box-shadow:0 1px 3px rgba(0,0,0,.06),inset 0 1px 0 rgba(255,255,255,.9);">
            
            <span style="font-size:11px;color:rgba(0,0,0,.55);font-family:-apple-system,sans-serif;letter-spacing:-.1px;">shift.nobushi.jp</span>
          </div>
          <!-- 右ボタン群 -->
          <div style="display:flex;gap:2px;flex-shrink:0;">
            <button aria-label="新しいタブ" style="width:26px;height:22px;border:none;background:transparent;cursor:pointer;border-radius:5px;display:flex;align-items:center;justify-content:center;" onmouseenter="this.style.background='rgba(0,0,0,.06)'" onmouseleave="this.style.background='transparent'">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="rgba(0,0,0,.4)" stroke-width="1.2"/><path d="M4 6h4M6 4v4" stroke="rgba(0,0,0,.4)" stroke-width="1.2" stroke-linecap="round"/></svg>
            </button>
            <button aria-label="メニューを開く" style="width:26px;height:22px;border:none;background:transparent;cursor:pointer;border-radius:5px;display:flex;align-items:center;justify-content:center;" onmouseenter="this.style.background='rgba(0,0,0,.06)'" onmouseleave="this.style.background='transparent'">
              <svg width="3" height="11" viewBox="0 0 3 11" fill="rgba(0,0,0,.4)"><circle cx="1.5" cy="1.5" r="1.5"/><circle cx="1.5" cy="5.5" r="1.5"/><circle cx="1.5" cy="9.5" r="1.5"/></svg>
            </button>
          </div>
        </div>
        <!-- スクリーン（固定高さ、縦スクロール内包） -->
        <div style="background:#f5f4f0;overflow:hidden;height:500px;display:flex;flex-direction:column;">
          <!-- アプリヘッダー -->
          <div style="background:rgba(255,255,255,.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-bottom:1px solid rgba(0,0,0,.07);padding:10px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <span style="font-size:14px;font-weight:700;color:#1a1814;letter-spacing:-.3px;">Shift OS</span>
            <div style="display:flex;gap:4px;">
              <button onclick="sdemoView('admin')" id="sv-admin" style="padding:4px 12px;border-radius:6px;font-size:11px;font-weight:600;border:none;cursor:pointer;background:#2c5f2e;color:#fff;">管理者</button>
              <button onclick="sdemoView('emp')" id="sv-emp" style="padding:4px 12px;border-radius:6px;font-size:11px;font-weight:600;border:none;cursor:pointer;background:#f0ede8;color:#6b6560;">スタッフ</button>
            </div>
          </div>
          <!-- タブ（管理者） -->
          <div id="stabs-admin" style="background:rgba(248,248,250,.9);border-bottom:1px solid rgba(0,0,0,.07);display:flex;flex-shrink:0;">
            <button onclick="sdemoTab('dashboard')" id="stab-dashboard" style="background:transparent;padding:8px 16px;font-size:11px;font-weight:600;border:none;border-bottom:2px solid #2c5f2e;color:#2c5f2e;background:#fff;cursor:pointer;white-space:nowrap;">ダッシュボード</button>
            <button onclick="sdemoTab('adjust')" id="stab-adjust" style="background:transparent;padding:8px 16px;font-size:11px;font-weight:500;border:none;border-bottom:2px solid transparent;color:#6b6560;background:#fff;cursor:pointer;white-space:nowrap;">シフト調整</button>
            <button onclick="sdemoTab('approval')" id="stab-approval" style="background:transparent;padding:8px 16px;font-size:11px;font-weight:500;border:none;border-bottom:2px solid transparent;color:#6b6560;background:#fff;cursor:pointer;white-space:nowrap;">承認</button>
            <button onclick="sdemoTab('timecard-admin')" id="stab-timecard-admin" style="background:transparent;padding:8px 16px;font-size:11px;font-weight:500;border:none;border-bottom:2px solid transparent;color:#6b6560;background:#fff;cursor:pointer;white-space:nowrap;">タイムカード</button>
          </div>
          <!-- タブ（スタッフ） -->
          <div id="stabs-emp" style="display:none;background:rgba(248,248,250,.9);border-bottom:1px solid rgba(0,0,0,.07);flex-shrink:0;">
            <button onclick="sdemoEmpTab('mypage')" id="stab-mypage" style="background:transparent;padding:8px 16px;font-size:11px;font-weight:600;border:none;border-bottom:2px solid #2c5f2e;color:#2c5f2e;background:#fff;cursor:pointer;white-space:nowrap;">マイページ</button>
            <button onclick="sdemoEmpTab('request')" id="stab-request" style="background:transparent;padding:8px 16px;font-size:11px;font-weight:500;border:none;border-bottom:2px solid transparent;color:#6b6560;background:#fff;cursor:pointer;white-space:nowrap;">シフト申請</button>
            <button onclick="sdemoEmpTab('emp-timecard')" id="stab-emp-timecard" style="background:transparent;padding:8px 16px;font-size:11px;font-weight:500;border:none;border-bottom:2px solid transparent;color:#6b6560;background:#fff;cursor:pointer;white-space:nowrap;">タイムカード</button>
          </div>
          <!-- パネル：横スクロールのみ、縦は固定 -->
          <div style="overflow-x:auto;overflow-y:hidden;flex:1;" id="sdemo-scroll">
            <div style="padding:12px 14px;min-width:640px;box-sizing:border-box;">

            <!-- ダッシュボード -->
            <div id="spanel-dashboard" style="height:100%;">
              <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:10px;">
                <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.9);border-radius:10px;padding:10px 12px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
                  <div style="font-size:9px;font-weight:600;color:#9e9890;letter-spacing:.3px;margin-bottom:4px;text-transform:uppercase;">今月申請</div>
                  <div style="font-size:22px;font-weight:700;color:#2c5f2e;line-height:1;">42</div>
                  <div style="font-size:10px;color:#6b6560;margin-top:2px;">確定 36件</div>
                </div>
                <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.9);border-radius:10px;padding:10px 12px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
                  <div style="font-size:9px;font-weight:600;color:#9e9890;letter-spacing:.3px;margin-bottom:4px;text-transform:uppercase;">承認待ち</div>
                  <div id="sdemo-pending" style="font-size:22px;font-weight:700;color:#c8873a;line-height:1;">6</div>
                  <div style="font-size:10px;color:#6b6560;margin-top:2px;">要対応</div>
                </div>
                <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.9);border-radius:10px;padding:10px 12px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
                  <div style="font-size:9px;font-weight:600;color:#9e9890;letter-spacing:.3px;margin-bottom:4px;text-transform:uppercase;">今月人件費</div>
                  <div id="sdemo-cost" style="font-size:18px;font-weight:700;color:#1a1814;line-height:1.1;">¥486,000</div>
                  <div style="font-size:10px;color:#6b6560;margin-top:2px;">概算</div>
                </div>
                <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.9);border-radius:10px;padding:10px 12px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
                  <div style="font-size:9px;font-weight:600;color:#9e9890;letter-spacing:.3px;margin-bottom:4px;text-transform:uppercase;">スタッフ</div>
                  <div style="font-size:22px;font-weight:700;color:#1a1814;line-height:1;">8</div>
                  <div style="font-size:10px;color:#6b6560;margin-top:2px;">名</div>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                <!-- 人件費グラフ -->
                <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.9);border-radius:10px;padding:10px 12px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
                  <div style="font-size:10px;font-weight:600;color:#1a1814;margin-bottom:8px;">人件費推移</div>
                  <div style="display:flex;align-items:flex-end;gap:4px;height:60px;">
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;">
                      <div style="width:100%;background:#2c5f2e;border-radius:3px 3px 0 0;height:38px;opacity:.5;"></div>
                      <div style="font-size:8px;color:#9e9890;">2月</div>
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;">
                      <div style="width:100%;background:#2c5f2e;border-radius:3px 3px 0 0;height:44px;opacity:.65;"></div>
                      <div style="font-size:8px;color:#9e9890;">3月</div>
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;">
                      <div style="width:100%;background:#2c5f2e;border-radius:3px 3px 0 0;height:40px;opacity:.75;"></div>
                      <div style="font-size:8px;color:#9e9890;">4月</div>
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;">
                      <div style="width:100%;background:#2c5f2e;border-radius:3px 3px 0 0;height:52px;"></div>
                      <div style="font-size:8px;font-weight:600;color:#2c5f2e;">5月</div>
                    </div>
                  </div>
                  <div style="font-size:9px;color:#6b6560;margin-top:4px;">¥486,000（先月比+6%）</div>
                </div>
                <!-- 出勤率グラフ -->
                <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.9);border-radius:10px;padding:10px 12px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
                  <div style="font-size:10px;font-weight:600;color:#1a1814;margin-bottom:8px;">出勤率（今月）</div>
                  <div style="position:relative;width:64px;height:64px;margin:0 auto 6px;">
                    <svg viewBox="0 0 36 36" width="64" height="64">
                      <path d="M18 2.0845a15.9155 15.9155 0 0 1 0 31.831 15.9155 15.9155 0 0 1 0-31.831" fill="none" stroke="#f0ede8" stroke-width="4"/>
                      <path d="M18 2.0845a15.9155 15.9155 0 0 1 0 31.831 15.9155 15.9155 0 0 1 0-31.831" fill="none" stroke="#2c5f2e" stroke-width="4" stroke-dasharray="92,100" stroke-linecap="round"/>
                      <text x="18" y="20.5" text-anchor="middle" font-size="8" font-weight="700" fill="#1a1814">92%</text>
                    </svg>
                  </div>
                  <div style="display:flex;justify-content:space-between;font-size:9px;"><span style="color:#6b6560;">目標</span><span style="font-weight:600;color:#2c5f2e;">95%</span></div>
                </div>
                <!-- 今週シフト -->
                <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.9);border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.05);">
                  <div style="padding:8px 10px;border-bottom:1px solid rgba(0,0,0,.06);display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:10px;font-weight:600;color:#1a1814;">今週シフト</span>
                    <button onclick="sdemoToast('CSVをダウンロードしました')" style="font-size:9px;padding:2px 7px;border:1px solid #e2ddd6;border-radius:4px;background:#f5f4f0;color:#6b6560;cursor:pointer;font-family:inherit;">CSV</button>
                  </div>
                  <table style="width:100%;border-collapse:collapse;">
                    <thead><tr style="background:#f8f8f6;"><th style="padding:5px 8px;font-size:9px;color:#9e9890;font-weight:600;text-align:left;border-bottom:1px solid #e2ddd6;">氏名</th><th style="padding:5px;font-size:9px;color:#9e9890;text-align:center;border-bottom:1px solid #e2ddd6;">月</th><th style="padding:5px;font-size:9px;color:#9e9890;text-align:center;border-bottom:1px solid #e2ddd6;">火</th><th style="padding:5px;font-size:9px;color:#c0392b;text-align:center;border-bottom:1px solid #e2ddd6;">土</th></tr></thead>
                    <tbody id="sdemo-shift-tbody"></tbody>
                  </table>
                </div>
              </div>
              <!-- アクティビティ -->
              <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.9);border-radius:10px;margin-top:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.05);">
                <div style="padding:8px 12px;border-bottom:1px solid rgba(0,0,0,.06);font-size:10px;font-weight:600;color:#1a1814;">最近のアクティビティ</div>
                <div style="padding:8px 12px;" id="sdemo-activity"></div>
              </div>
            </div>


            <!-- シフト調整 -->
            <div id="spanel-adjust" style="display:none;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <div style="font-size:12px;font-weight:600;color:#1a1814;">シフト調整 <span style="font-size:10px;font-weight:400;color:#9e9890;">— セルをクリックして変更</span></div>
                <button onclick="sdemoSaveAdjust()" style="padding:5px 14px;background:#2c5f2e;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;font-family:inherit;">保存</button>
              </div>
              <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;overflow:hidden;">
                <table style="width:100%;border-collapse:collapse;">
                  <thead><tr style="background:#f8f8f6;"><th style="padding:7px 10px;font-size:9px;color:#9e9890;font-weight:600;text-align:left;border-bottom:1px solid #e2ddd6;">氏名</th><th style="padding:7px 8px;font-size:9px;color:#9e9890;font-weight:600;text-align:center;border-bottom:1px solid #e2ddd6;">5/26(月)</th><th style="padding:7px 8px;font-size:9px;color:#9e9890;font-weight:600;text-align:center;border-bottom:1px solid #e2ddd6;">5/27(火)</th><th style="padding:7px 8px;font-size:9px;color:#9e9890;font-weight:600;text-align:center;border-bottom:1px solid #e2ddd6;">5/28(水)</th><th style="padding:7px 8px;font-size:9px;color:#c0392b;font-weight:600;text-align:center;border-bottom:1px solid #e2ddd6;">5/31(土)</th><th style="padding:7px 8px;font-size:9px;color:#9e9890;font-weight:600;text-align:center;border-bottom:1px solid #e2ddd6;">人件費</th></tr></thead>
                  <tbody id="sdemo-adjust-tbody"></tbody>
                </table>
              </div>
              <div id="sdemo-adjust-msg" style="display:none;margin-top:8px;font-size:11px;color:#2c5f2e;font-weight:600;"></div>
            </div>

            <!-- 承認 -->
            <div id="spanel-approval" style="display:none;">
              <div style="font-size:12px;font-weight:600;color:#1a1814;margin-bottom:10px;">承認待ちのシフト申請</div>
              <div id="sdemo-approval-list"></div>
              <div id="sdemo-approval-empty" style="display:none;text-align:center;padding:40px 0;color:#9e9890;font-size:12px;">承認待ちはありません</div>
            </div>

            <!-- タイムカード管理（休憩・修正申請） -->
            <div id="spanel-timecard-admin" style="display:none;">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;overflow:hidden;">
                  <div style="padding:7px 10px;border-bottom:1px solid #e2ddd6;font-size:10px;font-weight:600;color:#1a1814;">今日の出退勤</div>
                  <table style="width:100%;border-collapse:collapse;">
                    <thead><tr style="background:#f8f8f6;"><th style="padding:5px 8px;font-size:9px;color:#9e9890;font-weight:600;text-align:left;border-bottom:1px solid #e2ddd6;">氏名</th><th style="padding:5px;font-size:9px;color:#9e9890;text-align:center;border-bottom:1px solid #e2ddd6;">出勤</th><th style="padding:5px;font-size:9px;color:#9e9890;text-align:center;border-bottom:1px solid #e2ddd6;">休憩</th><th style="padding:5px;font-size:9px;color:#9e9890;text-align:center;border-bottom:1px solid #e2ddd6;">退勤</th><th style="padding:5px;font-size:9px;color:#9e9890;text-align:center;border-bottom:1px solid #e2ddd6;">状態</th></tr></thead>
                    <tbody>
                      <tr style="border-bottom:1px solid #f0ede8;"><td style="padding:7px 8px;font-size:11px;font-weight:600;color:#1a1814;">田中 さくら</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#6b6560;">09:02</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#6b6560;">60分</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#9e9890;">—</td><td style="padding:7px 5px;text-align:center;"><span style="background:#eaf2ea;color:#2c5f2e;font-size:9px;font-weight:600;padding:2px 6px;border-radius:4px;">勤務中</span></td></tr>
                      <tr style="border-bottom:1px solid #f0ede8;"><td style="padding:7px 8px;font-size:11px;font-weight:600;color:#1a1814;">山田 健太</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#6b6560;">08:58</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#6b6560;">45分</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#6b6560;">17:45</td><td style="padding:7px 5px;text-align:center;"><span style="background:#f0ede8;color:#9e9890;font-size:9px;font-weight:600;padding:2px 6px;border-radius:4px;">退勤済</span></td></tr>
                      <tr><td style="padding:7px 8px;font-size:11px;font-weight:600;color:#1a1814;">鈴木 美咲</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#c8873a;">未打刻</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#9e9890;">—</td><td style="padding:7px 5px;font-size:10px;text-align:center;color:#9e9890;">—</td><td style="padding:7px 5px;text-align:center;"><span style="background:#fdf3e7;color:#c8873a;font-size:9px;font-weight:600;padding:2px 6px;border-radius:4px;">未出勤</span></td></tr>
                    </tbody>
                  </table>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                  <!-- 修正申請 -->
                  <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;padding:10px;">
                    <div style="font-size:10px;font-weight:600;color:#1a1814;margin-bottom:8px;">修正申請（承認待ち）</div>
                    <div id="sdemo-correction-list">
                      <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f0ede8;">
                        <div><div style="font-size:10px;font-weight:600;color:#1a1814;">佐藤 拓也</div><div style="font-size:9px;color:#9e9890;">5/13 出勤 08:45→09:00</div></div>
                        <div style="display:flex;gap:4px;">
                          <button onclick="sdemoApproveCorrectionDemo(this)" style="padding:3px 8px;background:#2c5f2e;color:#fff;border:none;border-radius:4px;font-size:9px;font-weight:600;cursor:pointer;font-family:inherit;">承認</button>
                          <button onclick="this.closest('div[style*=flex]').parentNode.remove()" style="padding:3px 8px;background:#f0ede8;color:#9e9890;border:none;border-radius:4px;font-size:9px;cursor:pointer;font-family:inherit;">却下</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- 今月集計 -->
                  <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;padding:10px;">
                    <div style="font-size:10px;font-weight:600;color:#9e9890;margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px;">今月集計</div>
                    <div style="display:flex;justify-content:space-between;font-size:11px;padding:4px 0;border-bottom:1px solid #f0ede8;"><span style="color:#6b6560;">総勤務時間</span><span style="font-weight:600;color:#1a1814;">312.5h</span></div>
                    <div style="display:flex;justify-content:space-between;font-size:11px;padding:4px 0;border-bottom:1px solid #f0ede8;"><span style="color:#6b6560;">うち休憩</span><span style="font-weight:600;color:#6b6560;">41.5h</span></div>
                    <div style="display:flex;justify-content:space-between;font-size:11px;padding:4px 0;border-bottom:1px solid #f0ede8;"><span style="color:#6b6560;">残業</span><span style="font-weight:600;color:#c8873a;">24.0h</span></div>
                    <div style="display:flex;justify-content:space-between;font-size:11px;padding:4px 0;"><span style="color:#6b6560;">人件費</span><span style="font-weight:600;color:#2c5f2e;">¥486,000</span></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- マイページ -->
            <div id="spanel-mypage" style="display:none;">
              <div style="display:grid;grid-template-columns:220px 1fr;gap:10px;">
                <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;padding:12px;">
                  <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:#7c5c8c;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;flex-shrink:0;">田</div>
                    <div><div style="font-size:12px;font-weight:700;color:#1a1814;">田中 さくら</div><div style="font-size:10px;color:#9e9890;margin-top:1px;">EMP001</div></div>
                  </div>
                  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;">
                    <div style="text-align:center;background:#f8f8f6;border-radius:7px;padding:8px 2px;"><div style="font-size:16px;font-weight:700;color:#2c5f2e;">36</div><div style="font-size:9px;color:#9e9890;margin-top:1px;">確定</div></div>
                    <div style="text-align:center;background:#f8f8f6;border-radius:7px;padding:8px 2px;"><div style="font-size:16px;font-weight:700;color:#c8873a;">8</div><div style="font-size:9px;color:#9e9890;margin-top:1px;">有給残</div></div>
                    <div style="text-align:center;background:#f8f8f6;border-radius:7px;padding:8px 2px;"><div style="font-size:13px;font-weight:700;color:#1a1814;">148k</div><div style="font-size:9px;color:#9e9890;margin-top:1px;">見込み</div></div>
                  </div>
                </div>
                <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;overflow:hidden;">
                  <div style="padding:8px 12px;border-bottom:1px solid #e2ddd6;font-size:11px;font-weight:600;color:#1a1814;">今週のシフト</div>
                  <div style="padding:6px 12px;">
                    <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f0ede8;"><span style="font-size:11px;color:#6b6560;">月</span><span style="font-size:11px;font-weight:600;color:#2c5f2e;">通常 09:00〜18:00</span></div>
                    <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f0ede8;"><span style="font-size:11px;color:#6b6560;">火</span><span style="font-size:11px;font-weight:600;color:#2c5f2e;">早番 08:00〜17:00</span></div>
                    <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f0ede8;"><span style="font-size:11px;color:#9e9890;">水</span><span style="font-size:11px;color:#9e9890;">休み</span></div>
                    <div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="font-size:11px;color:#6b6560;">木</span><span style="font-size:11px;font-weight:600;color:#2c5f2e;">通常 09:00〜18:00</span></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- シフト申請 -->
            <div id="spanel-request" style="display:none;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:6px;">
                <div style="font-size:12px;font-weight:600;color:#1a1814;">来月の希望シフト <span style="font-size:10px;font-weight:400;color:#9e9890;">— 日付をクリック</span></div>
                <div style="display:inline-flex;align-items:center;gap:4px;background:#fdf3e7;border:1px solid #f0d8b8;border-radius:6px;padding:3px 9px;font-size:10px;font-weight:700;color:#c8873a;white-space:nowrap;">⏰ 修正期限まで 5日</div>
              </div>
              <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px;max-width:400px;margin-bottom:10px;" id="sdemo-cal"></div>
              <!-- 時間入力エリア（日付クリック後に表示） -->
              <div id="sdemo-time-entry" style="display:none;background:#fff;border:1px solid #e2ddd6;border-radius:8px;padding:10px 12px;max-width:360px;">
                <div id="sdemo-time-date-label" style="font-size:11px;font-weight:700;color:#1a1814;margin-bottom:8px;"></div>
                <div style="display:flex;align-items:flex-end;gap:8px;flex-wrap:wrap;">
                  <div>
                    <div style="font-size:9px;color:#9e9890;margin-bottom:3px;font-weight:600;">開始</div>
                    <input type="time" id="sdemo-time-from" value="09:00" onchange="sdemoAutoSaveTime()"
                           style="padding:5px 8px;border:1px solid #e2ddd6;border-radius:6px;font-size:12px;font-family:inherit;background:#fff;color:#1a1814;width:100px;">
                  </div>
                  <span style="font-size:12px;color:#9e9890;margin-bottom:7px;">〜</span>
                  <div>
                    <div style="font-size:9px;color:#9e9890;margin-bottom:3px;font-weight:600;">終了</div>
                    <input type="time" id="sdemo-time-to" value="18:00" onchange="sdemoAutoSaveTime()"
                           style="padding:5px 8px;border:1px solid #e2ddd6;border-radius:6px;font-size:12px;font-family:inherit;background:#fff;color:#1a1814;width:100px;">
                  </div>
                  <div id="sdemo-time-saved" style="display:none;font-size:10px;color:#2c5f2e;font-weight:600;margin-bottom:7px;">✓ 保存</div>
                </div>
              </div>
            </div>

            <!-- スタッフタイムカード（休憩・修正申請） -->
            <div id="spanel-emp-timecard" style="display:none;">
              <div style="display:grid;grid-template-columns:240px 1fr;gap:12px;">
                <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;padding:16px;text-align:center;">
                  <div style="font-size:10px;color:#9e9890;margin-bottom:6px;font-weight:600;">田中 さくら</div>
                  <div id="sdemo-tc-now" style="font-size:26px;font-weight:300;font-family:-apple-system,sans-serif;color:#1a1814;margin-bottom:4px;letter-spacing:-.5px;">--:--:--</div>
                  <div id="sdemo-tc-status-label" style="display:inline-block;background:#f0ede8;color:#9e9890;font-size:10px;font-weight:600;padding:3px 12px;border-radius:999px;margin-bottom:10px;">勤務外</div>
                  <div id="sdemo-tc-elapsed" style="font-size:11px;color:#9e9890;margin-bottom:10px;min-height:16px;"></div>
                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:8px;">
                    <button onclick="sdemoClockIn()" id="sdemo-btn-in" style="padding:8px;background:#2c5f2e;color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:600;cursor:pointer;font-family:inherit;">出勤</button>
                    <button onclick="sdemoClockOut()" id="sdemo-btn-out" disabled style="padding:8px;background:#f0ede8;color:#9e9890;border:none;border-radius:7px;font-size:11px;font-weight:600;cursor:not-allowed;font-family:inherit;">退勤</button>
                  </div>
                  <button onclick="sdemoBreak()" id="sdemo-btn-break" disabled style="width:100%;padding:7px;background:#fdf3e7;color:#c8873a;border:1px solid #f0d8b8;border-radius:7px;font-size:11px;font-weight:600;cursor:not-allowed;font-family:inherit;">休憩</button>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                  <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;overflow:hidden;flex:1;">
                    <div style="padding:7px 12px;border-bottom:1px solid #e2ddd6;font-size:10px;font-weight:600;color:#1a1814;">打刻履歴</div>
                    <div id="sdemo-tc-log" style="padding:8px 12px;font-size:11px;color:#374151;"></div>
                  </div>
                  <div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;padding:10px;">
                    <div style="font-size:10px;font-weight:600;color:#1a1814;margin-bottom:8px;">打刻修正申請</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:5px;">
                      <div>
                        <div style="font-size:9px;color:#9e9890;margin-bottom:2px;">対象日</div>
                        <select id="sdemo-correction-date" style="width:100%;padding:4px 6px;border:1px solid #e2ddd6;border-radius:5px;font-size:10px;font-family:inherit;background:#fff;color:#1a1814;">
                          <option>5/12(月)</option><option>5/13(火)</option><option>5/14(水)</option>
                        </select>
                      </div>
                      <div>
                        <div style="font-size:9px;color:#9e9890;margin-bottom:2px;">種別</div>
                        <select id="sdemo-correction-type" onchange="sdemoToggleCorrectionFields()" style="width:100%;padding:4px 6px;border:1px solid #e2ddd6;border-radius:5px;font-size:10px;font-family:inherit;background:#fff;color:#1a1814;">
                          <option value="in">出勤時刻</option><option value="out">退勤時刻</option><option value="break">休憩時間</option>
                        </select>
                      </div>
                    </div>
                    <div id="sdemo-corr-time-fields" style="display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:6px;">
                      <div>
                        <div style="font-size:9px;color:#9e9890;margin-bottom:2px;">誤った時刻</div>
                        <input type="time" id="sdemo-corr-from" value="09:15" style="width:100%;padding:4px 6px;border:1px solid #e2ddd6;border-radius:5px;font-size:10px;font-family:inherit;background:#fff;color:#1a1814;">
                      </div>
                      <div>
                        <div style="font-size:9px;color:#9e9890;margin-bottom:2px;">正しい時刻</div>
                        <input type="time" id="sdemo-corr-to" value="09:00" style="width:100%;padding:4px 6px;border:1px solid #e2ddd6;border-radius:5px;font-size:10px;font-family:inherit;background:#fff;color:#1a1814;">
                      </div>
                    </div>
                    <div id="sdemo-corr-break-field" style="display:none;margin-bottom:6px;">
                      <div style="font-size:9px;color:#9e9890;margin-bottom:2px;">正しい休憩時間（分）</div>
                      <input type="number" id="sdemo-corr-break" value="60" min="0" max="480" style="width:100%;padding:4px 6px;border:1px solid #e2ddd6;border-radius:5px;font-size:10px;font-family:inherit;background:#fff;color:#1a1814;">
                    </div>
                    <div>
                      <div style="font-size:9px;color:#9e9890;margin-bottom:2px;">修正理由</div>
                      <input type="text" id="sdemo-corr-reason" placeholder="例：打刻漏れのため" style="width:100%;padding:4px 6px;border:1px solid #e2ddd6;border-radius:5px;font-size:10px;font-family:inherit;background:#fff;color:#1a1814;margin-bottom:6px;">
                    </div>
                    <button onclick="sdemoSubmitCorrection()" style="width:100%;padding:6px;background:#185FA5;color:#fff;border:none;border-radius:5px;font-size:10px;font-weight:600;cursor:pointer;font-family:inherit;">申請する</button>
                    <div id="sdemo-correction-msg" style="display:none;font-size:10px;color:#2c5f2e;margin-top:5px;font-weight:600;">申請しました。管理者の承認をお待ちください。</div>
                  </div>
                </div>
              </div>
            </div>

            </div><!-- padding div -->
          </div><!-- sdemo-scroll -->
        </div><!-- スクリーン -->
      </div><!-- フレーム -->

    </div>
    <div id="sdemo-toast" style="display:none;position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.85);color:#fff;padding:8px 18px;border-radius:999px;font-size:12px;z-index:9998;white-space:nowrap;pointer-events:none;"></div>
  </div>

  <script>
  // --- デモ状態 ---
  var sdemoShifts = [
    {name:'田中 さくら',shifts:['通常','早番','休み'],confirmed:true},
    {name:'山田 健太',shifts:['早番','通常','通常'],confirmed:false},
    {name:'鈴木 美咲',shifts:['休み','通常','早番'],confirmed:true}
  ];
  var sdemoApprovals = [
    {name:'山田 健太',day:'6/16(月)',shift:'遅番 13:00'},
    {name:'鈴木 美咲',day:'6/17(火)',shift:'夜勤 22:00'},
    {name:'佐藤 拓也',day:'6/18(水)',shift:'有給'},
  ];
  var sdemoActivities = [
    {dot:'green',text:'田中さくら — シフト申請を提出',time:'今日 09:14'},
    {dot:'green',text:'管理者 — 6月分シフトを確定',time:'今日 08:52'},
    {dot:'amber',text:'山田健太 — シフト変更を申請',time:'昨日 17:33'},
    {dot:'',text:'佐藤拓也 — ログイン',time:'昨日 09:01'},
  ];
  var sdemoTcState = {in:false,inTime:null,log:['6/14 出勤 09:05 退勤 17:58 (8h53m)','6/13 出勤 08:58 退勤 18:10 (9h12m)','6/12 休み']};
  var sdemoTcInterval = null;
  var sdemoSelDay = null;
  var sdemoSelShift = '通常';
  var sdemoCurrentView = 'admin';
  var sdemoCurrentTab = 'dashboard';
  var sdemoCurrentEmpTab = 'mypage';

  function sdemoView(v) {
    sdemoCurrentView = v;
    document.getElementById('sv-admin').style.background = v==='admin'?'#2c5f2e':'#f0ede8';
    document.getElementById('sv-admin').style.color = v==='admin'?'#fff':'#6b6560';
    document.getElementById('sv-emp').style.background = v==='emp'?'#2c5f2e':'#f0ede8';
    document.getElementById('sv-emp').style.color = v==='emp'?'#fff':'#6b6560';
    document.getElementById('stabs-admin').style.display = v==='admin'?'flex':'none';
    document.getElementById('stabs-emp').style.display = v==='emp'?'flex':'none';
    ['dashboard','adjust','approval','timecard-admin'].forEach(function(t){ document.getElementById('spanel-'+t).style.display='none'; });
    ['mypage','request','emp-timecard'].forEach(function(t){ document.getElementById('spanel-'+t).style.display='none'; });
    if (v==='admin') { sdemoTab('dashboard'); }
    else { sdemoEmpTab('mypage'); }
  }

  function sdemoTab(t) {
    sdemoCurrentTab = t;
    ['dashboard','adjust','approval','timecard-admin'].forEach(function(x){
      document.getElementById('spanel-'+x).style.display = x===t?'block':'none';
      var btn = document.getElementById('stab-'+x);
      if(btn){ btn.style.borderBottomColor=x===t?'#2c5f2e':'transparent'; btn.style.color=x===t?'#2c5f2e':'#6b6560'; btn.style.fontWeight=x===t?'600':'500'; }
    });
    if(t==='approval') sdemoRenderApprovals();
    if(t==='dashboard') { sdemoRenderShifts(); sdemoRenderActivity(); }
    if(t==='adjust') sdemoRenderAdjust();
    document.getElementById('sdemo-scroll').scrollLeft=0;
  }

  function sdemoEmpTab(t) {
    sdemoCurrentEmpTab = t;
    ['mypage','request','emp-timecard'].forEach(function(x){
      document.getElementById('spanel-'+x).style.display = x===t?'block':'none';
      var btn = document.getElementById('stab-'+x);
      if(btn){ btn.style.borderBottomColor=x===t?'#2c5f2e':'transparent'; btn.style.color=x===t?'#2c5f2e':'#6b6560'; btn.style.fontWeight=x===t?'600':'500'; }
    });
    if(t==='request') sdemoRenderCal();
    if(t==='emp-timecard') { sdemoRenderTcLog(); sdemoStartClock(); }
    else { if(sdemoTcInterval){ clearInterval(sdemoTcInterval); sdemoTcInterval=null; } }
    document.getElementById('sdemo-scroll').scrollTop=0;
  }

  var shiftCycle = ['通常','早番','遅番','夜勤','休み','有給'];
  function sdemoRenderShifts() {
    var tb = document.getElementById('sdemo-shift-tbody');
    if(!tb) return;
    var cost = 0;
    tb.innerHTML = sdemoShifts.map(function(s){
      var wages = {通常:1200,早番:1200,遅番:1200,夜勤:1500,休み:0,有給:0};
      s.shifts.forEach(function(sh){ cost += (wages[sh]||0)*8; });
      var cells = s.shifts.map(function(sh,i){
        var c = sh==='休み'||sh==='有給'?'#9e9890':'#2c5f2e';
        return '<td style="padding:6px;text-align:center;border-left:1px solid #f0ede8;cursor:pointer;" onclick="sdemoToggleShift('+sdemoShifts.indexOf(s)+','+i+')"><span style="font-size:9px;font-weight:600;color:'+c+';">'+sh+'</span></td>';
      }).join('');
      var badge = s.confirmed?'<span style="font-size:8px;background:#eaf2ea;color:#2c5f2e;padding:1px 4px;border-radius:3px;margin-left:4px;">確定</span>':'';
      return '<tr style="border-bottom:1px solid #f0ede8;"><td style="padding:7px 8px;font-size:10px;font-weight:600;white-space:nowrap;">'+s.name+badge+'</td>'+cells+'</tr>';
    }).join('');
    document.getElementById('sdemo-cost').textContent='¥'+Math.round(cost).toLocaleString();
  }

  function sdemoToggleShift(si,di) {
    var cur = sdemoShifts[si].shifts[di];
    var idx = shiftCycle.indexOf(cur);
    sdemoShifts[si].shifts[di] = shiftCycle[(idx+1)%shiftCycle.length];
    sdemoRenderShifts();
    sdemoToast('シフトを変更しました');
  }

  function sdemoRenderActivity() {
    var el = document.getElementById('sdemo-activity');
    if(!el) return;
    var colors = {green:'#2c5f2e',amber:'#c8873a','':'#9e9890'};
    el.innerHTML = sdemoActivities.map(function(a){
      return '<div style="display:flex;gap:8px;align-items:flex-start;padding:6px 0;border-bottom:1px solid #f5f4f0;">'
        +'<div style="width:7px;height:7px;border-radius:50%;background:'+(colors[a.dot]||'#9e9890')+';flex-shrink:0;margin-top:3px;"></div>'
        +'<div><div style="font-size:10px;font-weight:500;color:#1a1814;">'+a.text+'</div>'
        +'<div style="font-size:9px;color:#9e9890;margin-top:1px;">'+a.time+'</div></div></div>';
    }).join('');
  }

  function sdemoRenderApprovals() {
    var el = document.getElementById('sdemo-approval-list');
    var emp = document.getElementById('sdemo-approval-empty');
    if(sdemoApprovals.length===0){ el.style.display='none'; emp.style.display='block'; return; }
    emp.style.display='none'; el.style.display='block';
    el.innerHTML = sdemoApprovals.map(function(a,i){
      return '<div style="background:#fff;border:1px solid #e2ddd6;border-radius:10px;padding:10px 12px;margin-bottom:8px;">'
        +'<div style="font-size:11px;font-weight:700;margin-bottom:3px;">'+a.name+'</div>'
        +'<div style="font-size:10px;color:#6b6560;margin-bottom:8px;">'+a.day+' / '+a.shift+'</div>'
        +'<div style="display:flex;gap:6px;">'
        +'<button onclick="sdemoApprove('+i+',true)" style="flex:1;padding:6px;background:#2c5f2e;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">承認</button>'
        +'<button onclick="sdemoApprove('+i+',false)" style="flex:1;padding:6px;background:#f0ede8;color:#6b6560;border:none;border-radius:6px;font-size:11px;cursor:pointer;">却下</button>'
        +'</div></div>';
    }).join('');
  }

  function sdemoApprove(i,ok) {
    var name = sdemoApprovals[i].name;
    sdemoApprovals.splice(i,1);
    var p = parseInt(document.getElementById('sdemo-pending').textContent);
    document.getElementById('sdemo-pending').textContent = Math.max(0,p-1);
    sdemoActivities.unshift({dot:ok?'green':'',text:name+' — シフト申請を'+(ok?'承認':'却下'),time:'たった今'});
    sdemoRenderApprovals();
    sdemoToast(ok?'承認しました':'却下しました');
  }

  function sdemoRenderCal() {
    var el = document.getElementById('sdemo-cal');
    if(!el) return;
    var days = ['月','火','水','木','金','土','日'];
    var headers = days.map(function(d,i){
      return '<div style="text-align:center;font-size:9px;font-weight:600;color:'+(i>=5?'#c0392b':'#9e9890')+';">'+d+'</div>';
    }).join('');
    var cells = '';
    for(var i=1;i<=21;i++){
      var dow = (i-1)%7;
      var isWeekend = dow>=5;
      var isSelected = sdemoSelDay===i;
      cells += '<button onclick="sdemoSelectDay('+i+')" style="aspect-ratio:1;border-radius:8px;border:'+(isSelected?'2px solid #2c5f2e':'1px solid #e2ddd6')+';background:'+(isSelected?'#eaf2ea':'#fff')+';font-size:10px;font-weight:'+(isSelected?'700':'400')+';color:'+(isWeekend?'#c0392b':(isSelected?'#2c5f2e':'#1a1814'))+';cursor:pointer;">'+i+'</button>';
    }
    el.innerHTML = headers + cells;
  }

  var sdemoDaySaved = {};  // 保存済み日付を管理

  function sdemoSelectDay(d) {
    sdemoSelDay = d;
    sdemoRenderCal();
    var entry = document.getElementById('sdemo-time-entry');
    entry.style.display = 'block';
    document.getElementById('sdemo-time-date-label').textContent = '6/' + d + ' の希望時間';
    document.getElementById('sdemo-time-saved').style.display = 'none';
    // 保存済みの時間があれば復元
    if (sdemoDaySaved[d]) {
      document.getElementById('sdemo-time-from').value = sdemoDaySaved[d].from;
      document.getElementById('sdemo-time-to').value   = sdemoDaySaved[d].to;
    } else {
      document.getElementById('sdemo-time-from').value = '09:00';
      document.getElementById('sdemo-time-to').value   = '18:00';
    }
  }

  function sdemoAutoSaveTime() {
    if (!sdemoSelDay) return;
    var from = document.getElementById('sdemo-time-from').value;
    var to   = document.getElementById('sdemo-time-to').value;
    var isNew = !sdemoDaySaved[sdemoSelDay];
    sdemoDaySaved[sdemoSelDay] = { from: from, to: to };
    // 新規保存のときだけ承認待ちを増やす
    if (isNew) {
      var p = parseInt(document.getElementById('sdemo-pending').textContent);
      document.getElementById('sdemo-pending').textContent = p + 1;
    }
    var saved = document.getElementById('sdemo-time-saved');
    saved.style.display = 'inline';
    sdemoToast('6/' + sdemoSelDay + ' ' + from + '〜' + to + ' を保存しました');
    setTimeout(function(){ saved.style.display = 'none'; }, 2000);
  }

  function sdemoStartClock() {
    if(sdemoTcInterval) clearInterval(sdemoTcInterval);
    sdemoTcInterval = setInterval(function(){
      var now = new Date();
      document.getElementById('sdemo-tc-now').textContent =
        ('0'+now.getHours()).slice(-2)+':'+('0'+now.getMinutes()).slice(-2)+':'+('0'+now.getSeconds()).slice(-2);
      if(sdemoTcState.in && sdemoTcState.inTime){
        var diff = Math.floor((now-sdemoTcState.inTime)/1000);
        var h = Math.floor(diff/3600), m = Math.floor((diff%3600)/60), s = diff%60;
        document.getElementById('sdemo-tc-elapsed').textContent='勤務時間 '+h+'h'+('0'+m).slice(-2)+'m'+('0'+s).slice(-2)+'s';
      }
    },1000);
  }

  function sdemoBreak() {
    var btn = document.getElementById('sdemo-btn-break');
    if (!sdemoTcState.in) return;
    if (btn.textContent === '休憩') {
      btn.textContent = '休憩終了';
      btn.style.background = '#c8873a';
      btn.style.color = '#fff';
      document.getElementById('sdemo-tc-status-label').textContent = '休憩中';
      document.getElementById('sdemo-tc-status-label').style.background = '#fdf3e7';
      document.getElementById('sdemo-tc-status-label').style.color = '#c8873a';
      sdemoToast('休憩を開始しました');
    } else {
      btn.textContent = '休憩';
      btn.style.background = '#fdf3e7';
      btn.style.color = '#c8873a';
      document.getElementById('sdemo-tc-status-label').textContent = '勤務中';
      document.getElementById('sdemo-tc-status-label').style.background = '#eaf2ea';
      document.getElementById('sdemo-tc-status-label').style.color = '#2c5f2e';
      sdemoToast('休憩を終了しました');
    }
  }

  function sdemoToggleCorrectionFields() {
    var type = document.getElementById('sdemo-correction-type').value;
    document.getElementById('sdemo-corr-time-fields').style.display = type === 'break' ? 'none' : 'grid';
    document.getElementById('sdemo-corr-break-field').style.display = type === 'break' ? 'block' : 'none';
  }
  function sdemoSubmitCorrection() {
    var date = document.getElementById('sdemo-correction-date').value;
    var typeEl = document.getElementById('sdemo-correction-type');
    var typeText = typeEl.options[typeEl.selectedIndex].text;
    var msg = document.getElementById('sdemo-correction-msg');
    msg.style.display = 'block';
    sdemoToast(date + ' ' + typeText + 'の修正申請を送信しました');
    setTimeout(function(){ msg.style.display='none'; }, 3000);
  }

  function sdemoApproveCorrectionDemo(btn) {
    btn.closest('div').parentNode.parentNode.innerHTML = '<div style="text-align:center;padding:10px;font-size:10px;color:#2c5f2e;">修正申請を承認しました</div>';
    sdemoToast('修正申請を承認しました');
  }

  function sdemoClockIn() {
    if(sdemoTcState.in) return;
    sdemoTcState.in=true; sdemoTcState.inTime=new Date();
    document.getElementById('sdemo-tc-status-label').textContent='勤務中';
    document.getElementById('sdemo-tc-status-label').style.background='#eaf2ea';
    document.getElementById('sdemo-tc-status-label').style.color='#2c5f2e';
    document.getElementById('sdemo-btn-in').disabled=true;
    document.getElementById('sdemo-btn-in').style.background='#ccc';
    document.getElementById('sdemo-btn-in').style.cursor='not-allowed';
    document.getElementById('sdemo-btn-out').disabled=false;
    document.getElementById('sdemo-btn-out').style.background='#c0392b';
    document.getElementById('sdemo-btn-out').style.color='#fff';
    document.getElementById('sdemo-btn-out').style.cursor='pointer';
    var brk = document.getElementById('sdemo-btn-break');
    if(brk){brk.disabled=false;brk.style.cursor='pointer';}
    sdemoToast('出勤打刻しました');
  }

  function sdemoClockOut() {
    if(!sdemoTcState.in) return;
    var now=new Date(), inT=sdemoTcState.inTime;
    var fmt=function(d){return('0'+d.getHours()).slice(-2)+':'+('0'+d.getMinutes()).slice(-2);};
    var diff=Math.floor((now-inT)/60000), h=Math.floor(diff/60), m=diff%60;
    var today=(now.getMonth()+1)+'/'+(now.getDate());
    sdemoTcState.log.unshift(today+' 出勤 '+fmt(inT)+' 退勤 '+fmt(now)+' ('+h+'h'+('0'+m).slice(-2)+'m)');
    sdemoTcState.in=false; sdemoTcState.inTime=null;
    document.getElementById('sdemo-tc-status-label').textContent='勤務外';
    document.getElementById('sdemo-tc-status-label').style.background='#f0ede8';
    document.getElementById('sdemo-tc-status-label').style.color='#9e9890';
    document.getElementById('sdemo-tc-elapsed').textContent='';
    document.getElementById('sdemo-btn-in').disabled=false;
    document.getElementById('sdemo-btn-in').style.background='#2c5f2e';
    document.getElementById('sdemo-btn-in').style.cursor='pointer';
    document.getElementById('sdemo-btn-out').disabled=true;
    document.getElementById('sdemo-btn-out').style.background='#f0ede8';
    document.getElementById('sdemo-btn-out').style.color='#9e9890';
    document.getElementById('sdemo-btn-out').style.cursor='not-allowed';
    sdemoRenderTcLog();
    sdemoToast('退勤打刻しました');
  }

  function sdemoRenderTcLog() {
    var el=document.getElementById('sdemo-tc-log');
    if(!el) return;
    el.innerHTML=sdemoTcState.log.map(function(l){
      return '<div style="padding:5px 0;border-bottom:1px solid #f0ede8;color:#374151;">'+l+'</div>';
    }).join('');
  }

  var sdemoCalOffset = 0;
  var sdemoAdjustShifts = [
    {name:'田中 さくら', wage:1200, days:['通常','早番','通常','休み']},
    {name:'山田 健太',   wage:1150, days:['早番','通常','休み','通常']},
    {name:'鈴木 美咲',   wage:1300, days:['休み','通常','通常','早番']},
    {name:'佐藤 拓也',   wage:1100, days:['通常','休み','遅番','通常']},
  ];
  var adjustCycle = ['通常','早番','遅番','夜勤','休み','有給'];

  function sdemoCalNav(d) {
    sdemoCalOffset += d;
    sdemoRenderCalGrid();
  }

  function sdemoRenderCalGrid() {
    var base = new Date(2026, 4, 1);
    base.setMonth(base.getMonth() + sdemoCalOffset);
    var y = base.getFullYear(), m = base.getMonth();
    var months = ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'];
    document.getElementById('sdemo-cal-month').textContent = y + '年' + months[m];
    var firstDow = new Date(y, m, 1).getDay(); // 0=Sun
    firstDow = firstDow === 0 ? 6 : firstDow - 1; // Mon=0
    var daysInMonth = new Date(y, m+1, 0).getDate();
    var calData = [
      {d:3,type:'confirmed',label:'通常'},{d:4,type:'confirmed',label:'早番'},
      {d:5,type:'off',label:'休み'},{d:10,type:'pending',label:'遅番'},
      {d:11,type:'confirmed',label:'通常'},{d:17,type:'pending',label:'申請中'},
      {d:18,type:'off',label:'休み'},{d:24,type:'confirmed',label:'通常'},
    ];
    var grid = document.getElementById('sdemo-cal-grid');
    var html = '';
    for (var i=0; i<firstDow; i++) html += '<div style="padding:4px;min-height:44px;border-right:1px solid #f0ede8;border-bottom:1px solid #f0ede8;"></div>';
    for (var d=1; d<=daysInMonth; d++) {
      var dow = (firstDow + d - 1) % 7;
      var isWeekend = dow >= 5;
      var info = calData.find(function(c){ return c.d===d; });
      var bg = info ? (info.type==='confirmed'?'#eaf2ea':info.type==='pending'?'#fdf3e7':'#f5f4f0') : '#fff';
      var borderC = info ? (info.type==='confirmed'?'1px solid #d4e8d4':info.type==='pending'?'1px solid #f0d8b8':'none') : 'none';
      var textC = info ? (info.type==='confirmed'?'#2c5f2e':info.type==='pending'?'#c8873a':'#9e9890') : (isWeekend?'#c0392b':'#1a1814');
      html += '<div style="padding:4px 6px;min-height:44px;background:'+bg+';border:'+borderC+';border-right:1px solid #f0ede8;border-bottom:1px solid #f0ede8;cursor:pointer;" onclick="sdemoToast(\''+d+'日のシフトを確認\')">'
        + '<div style="font-size:10px;font-weight:600;color:'+textC+';">'+d+'</div>'
        + (info ? '<div style="font-size:9px;color:'+textC+';margin-top:2px;">'+info.label+'</div>' : '')
        + '</div>';
    }
    var remaining = 7 - ((firstDow + daysInMonth) % 7);
    if (remaining < 7) for (var i=0; i<remaining; i++) html += '<div style="padding:4px;min-height:44px;background:#fafafa;border-right:1px solid #f0ede8;border-bottom:1px solid #f0ede8;"></div>';
    grid.innerHTML = html;
  }

  function sdemoRenderAdjust() {
    var tb = document.getElementById('sdemo-adjust-tbody');
    if (!tb) return;
    tb.innerHTML = sdemoAdjustShifts.map(function(s, si) {
      var cost = s.days.reduce(function(acc, sh){ return acc + (sh==='休み'||sh==='有給'?0:sh==='夜勤'?s.wage*1.25*8:s.wage*8); }, 0);
      var cells = s.days.map(function(sh, di) {
        var isOff = sh==='休み'||sh==='有給';
        var bg = isOff?'#f8f8f6':'#fff';
        var color = isOff?'#9e9890':sh==='夜勤'?'#185FA5':sh==='遅番'?'#c8873a':'#2c5f2e';
        return '<td style="padding:8px;text-align:center;border-left:1px solid #f0ede8;background:'+bg+';cursor:pointer;" onclick="sdemoToggleAdjust('+si+','+di+')">'
          +'<span style="font-size:11px;font-weight:600;color:'+color+';">'+sh+'</span></td>';
      }).join('');
      return '<tr style="border-bottom:1px solid #f0ede8;">'
        +'<td style="padding:8px 12px;"><div style="font-size:12px;font-weight:600;color:#1a1814;">'+s.name+'</div>'
        +'<div style="font-size:10px;color:#9e9890;margin-top:1px;">¥'+s.wage.toLocaleString()+'/h</div></td>'
        + cells
        +'<td style="padding:8px 12px;text-align:right;font-size:11px;font-weight:600;color:#1a1814;border-left:1px solid #f0ede8;">¥'+Math.round(cost).toLocaleString()+'</td>'
        +'</tr>';
    }).join('');
  }

  function sdemoToggleAdjust(si, di) {
    var cur = sdemoAdjustShifts[si].days[di];
    var idx = adjustCycle.indexOf(cur);
    sdemoAdjustShifts[si].days[di] = adjustCycle[(idx+1)%adjustCycle.length];
    sdemoRenderAdjust();
  }

  function sdemoSaveAdjust() {
    var el = document.getElementById('sdemo-adjust-msg');
    el.textContent = '変更を保存しました';
    el.style.display = 'block';
    setTimeout(function(){ el.style.display='none'; }, 2000);
    sdemoToast('シフトを保存しました');
  }

  var toastTimer=null;
  function sdemoToast(msg) {
    var el=document.getElementById('sdemo-toast');
    el.textContent=msg; el.style.display='block';
    if(toastTimer) clearTimeout(toastTimer);
    toastTimer=setTimeout(function(){ el.style.display='none'; },2000);
  }

  // 初期化：デモがビューポートに入ったときだけ実行（TBT削減）
  var _sdemoInited = false;
  function _sdemoInit() {
    if (_sdemoInited) return;
    _sdemoInited = true;
    setTimeout(function() { sdemoRenderShifts(); sdemoRenderActivity(); }, 0);
    setTimeout(function() { sdemoRenderTcLog(); }, 50);
    setTimeout(function() { sdemoRenderAdjust(); }, 100);
  }
  if ('IntersectionObserver' in window) {
    var _obs = new IntersectionObserver(function(entries) {
      if (entries[0].isIntersecting) { _sdemoInit(); _obs.disconnect(); }
    }, { rootMargin: '200px' });
    var _demoEl = document.getElementById('sdemo-scroll');
    if (_demoEl) _obs.observe(_demoEl);
  } else {
    // フォールバック：旧ブラウザはload後に実行
    window.addEventListener('load', _sdemoInit);
  }
  </script>
  <!-- 汎用デモバー -->
  <a href="https://shift.nobushi.jp/demo-generic/" target="_blank" rel="noopener"
     onclick="if(typeof gtag!=='undefined')gtag('event','demo_bar_click',{event_label:'generic'});"
     style="display:flex;align-items:center;justify-content:space-between;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:10px;padding:14px 20px;text-decoration:none;margin-bottom:24px;transition:background .15s;"
     onmouseenter="this.style.background='rgba(255,255,255,.1)'" onmouseleave="this.style.background='rgba(255,255,255,.06)'">
    <div style="display:flex;align-items:center;gap:12px;">
      <span style="background:#185FA5;color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;white-space:nowrap;">即時利用可</span>
      <span style="color:#fff;font-size:14px;font-weight:600;">汎用デモを試す — 今すぐ体験</span>
    </div>
    <span style="color:rgba(255,255,255,.5);font-size:14px;">→</span>
  </a>
  <!-- スマホ下：価格・ボタン -->
  <p style="margin:0 auto 20px;max-width:460px;text-align:center;">シフト管理からタイムカード・勤怠集計まで、<br>あなたの業務専用にカスタム。<br><strong style="color:#fff;">¥11,000(先行利用価格)/月、全部込み。</strong></p>
  <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(24,95,165,.25);border:1px solid rgba(24,95,165,.6);border-radius:8px;padding:8px 14px;margin-bottom:20px;flex-wrap:wrap;">
    <span style="background:#185FA5;color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;letter-spacing:.5px;white-space:nowrap;">先行利用プログラム</span>
    <span style="font-size:12px;color:rgba(255,255,255,.75);">5月末まで・定員達し次第終了。今申し込むと<strong style="color:#fff;">最低3年間¥11,000保証。</strong></span>
  </div>
  <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px;justify-content:center;">
    <button onclick="slpOpenConsult()" class="slp-btn-p" style="border:none;cursor:pointer;">無料試用</button>
    <a href="#slp-plan-apply" class="slp-btn-s">申し込む</a>
  </div>

</section>

<section>
  <h2>こんな悩みありませんか？</h2>
  <ul class="slp-pain-list">
    <li class="slp-pain-item"><span class="slp-pain-x">×</span><div class="slp-pain-text"><strong>LINEやExcel、高価なサービスで管理している</strong><span>煩雑な管理を手動で行っていて時間がかかる</span></div></li>
    <li class="slp-pain-item"><span class="slp-pain-x">×</span><div class="slp-pain-text"><strong>出勤確認が漏れる</strong><span>手作業や機能不足によるミスが発生</span></div></li>
    <li class="slp-pain-item"><span class="slp-pain-x">×</span><div class="slp-pain-text"><strong>人件費が締め日まで分からない</strong><span>月末にならないと正確な金額がわからない</span></div></li>
    <li class="slp-pain-item"><span class="slp-pain-x">×</span><div class="slp-pain-text"><strong>市販のSaaSは機能が合わない</strong><span>使わない機能に月額を払い続けている</span></div></li>
  </ul>
</section>

<section>
  <h2>全部、解決できます。</h2>
  <div class="slp-grid">
    <div class="slp-card"><h3>あなたの業務専用に設計</h3><p>3交代制・深夜割増・土日出勤契約・ポジション管理など、業種とルールに合わせてカスタム。市販SaaSにできない「ちょうどいい」を実現します。</p></div>
    <div class="slp-card"><h3>PC・スマホ両対応</h3><p>管理者はPCで人件費・FL比率・シフトを一元管理。スタッフはスマホでシフト申請と見込み賃金を確認できます。</p></div>
    <div class="slp-card"><h3>タイムカード・勤怠管理</h3><p>スマホから出退勤をワンタップで打刻。月次の勤務時間・残業・人件費を自動集計します。</p></div>
    <div class="slp-card"><h3>機能はずっと追加できる</h3><p>タイムカード・有給管理・シフトテンプレート・給与明細出力など、必要な機能を月額の中で追加し続けられます。</p></div>
  </div>
  <div style="text-align:center;margin-top:24px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
    <button onclick="slpOpenConsult()" class="slp-btn-p" style="border:none;cursor:pointer;">無料試用</button>
    <a href="#industry-demos" class="slp-btn-s" onclick="document.getElementById('industry-demos').scrollIntoView({behavior:'smooth'});return false;">デモを試す</a>
  </div>
</section>

<section id="industry-demos">
  <h2>業種別デモ</h2>
  <p>あなたの業種に近いデモを今すぐ試せます。ログイン不要。</p>
  <div class="slp-grid">
    <a href="https://shift.nobushi.jp/demo/" target="_blank" rel="noopener" style="text-decoration:none;"
       onclick="if(typeof gtag!=='undefined')gtag('event','demo_click',{event_label:'restaurant'});">
      <div class="slp-card" style="cursor:pointer;transition:background .15s;" onmouseenter="this.style.background='rgba(255,255,255,.1)'" onmouseleave="this.style.background='rgba(255,255,255,.06)'">
        <div style="font-size:11px;font-weight:700;color:#185FA5;margin-bottom:8px;letter-spacing:.3px;text-transform:uppercase;">飲食店</div>
        <h3 style="margin-bottom:6px;">飲食・F&Bデモ</h3>
        <p>ホール・キッチン・深夜割増・FL比率管理。飲食業に特化した人件費最適化。</p>
        <div style="margin-top:12px;font-size:12px;color:#185FA5;font-weight:600;">デモを見る →</div>
      </div>
    </a>
    <a href="https://shift.nobushi.jp/demo-care/" target="_blank" rel="noopener" style="text-decoration:none;"
       onclick="if(typeof gtag!=='undefined')gtag('event','demo_click',{event_label:'care'});">
      <div class="slp-card" style="cursor:pointer;transition:background .15s;" onmouseenter="this.style.background='rgba(255,255,255,.1)'" onmouseleave="this.style.background='rgba(255,255,255,.06)'">
        <div style="font-size:11px;font-weight:700;color:#185FA5;margin-bottom:8px;letter-spacing:.3px;text-transform:uppercase;">介護・医療</div>
        <h3 style="margin-bottom:6px;">介護・医療施設デモ</h3>
        <p>3交代制・夜勤管理・資格別配置・夜勤明け自動処理。施設運営に特化した設計。</p>
        <div style="margin-top:12px;font-size:12px;color:#185FA5;font-weight:600;">デモを見る →</div>
      </div>
    </a>
    <a href="https://shift.nobushi.jp/demo-beauty/" target="_blank" rel="noopener" style="text-decoration:none;"
       onclick="if(typeof gtag!=='undefined')gtag('event','demo_click',{event_label:'beauty'});">
      <div class="slp-card" style="cursor:pointer;transition:background .15s;" onmouseenter="this.style.background='rgba(255,255,255,.1)'" onmouseleave="this.style.background='rgba(255,255,255,.06)'">
        <div style="font-size:11px;font-weight:700;color:#185FA5;margin-bottom:8px;letter-spacing:.3px;text-transform:uppercase;">美容・サロン</div>
        <h3 style="margin-bottom:6px;">美容・サロンデモ</h3>
        <p>スタイリスト指名管理・席稼働率・技術レベル別シフト。売上連動の人員最適化。</p>
        <div style="margin-top:12px;font-size:12px;color:#185FA5;font-weight:600;">デモを見る →</div>
      </div>
    </a>
    <a href="https://shift.nobushi.jp/demo-retail/" target="_blank" rel="noopener" style="text-decoration:none;"
       onclick="if(typeof gtag!=='undefined')gtag('event','demo_click',{event_label:'retail'});">
      <div class="slp-card" style="cursor:pointer;transition:background .15s;" onmouseenter="this.style.background='rgba(255,255,255,.1)'" onmouseleave="this.style.background='rgba(255,255,255,.06)'">
        <div style="font-size:11px;font-weight:700;color:#185FA5;margin-bottom:8px;letter-spacing:.3px;text-transform:uppercase;">小売・販売</div>
        <h3 style="margin-bottom:6px;">小売・販売デモ</h3>
        <p>レジ・フロア・バックヤード別ポジション管理。来客数に応じた最小人員配置。</p>
        <div style="margin-top:12px;font-size:12px;color:#185FA5;font-weight:600;">デモを見る →</div>
      </div>
    </a>
  </div>
  <div style="text-align:center;margin-top:24px;">
    <a href="https://shift.nobushi.jp/demo-generic/" target="_blank" rel="noopener" class="slp-btn-s"
       onclick="if(typeof gtag!=='undefined')gtag('event','demo_click',{event_label:'generic_mid'});">汎用デモ（どの業種でも使える）を試す</a>
  </div>
</section>

<section id="slp-plan-apply">
  <h2>料金</h2>
  <div class="slp-pricing-card">
    <div class="slp-price-main">¥11,000<span class="slp-price-unit"> /月</span></div>
    <p class="slp-price-note">初月からずっと同じ料金</p>
    <!-- 先行利用プログラム情報（青字） -->
    <div style="border:1px solid rgba(24,95,165,.45);border-radius:8px;padding:12px 14px;margin-bottom:20px;background:rgba(24,95,165,.15);">
      <div style="display:flex;align-items:center;gap:6px;margin-bottom:7px;flex-wrap:wrap;">
        <span style="background:#185FA5;color:#fff;font-size:10px;font-weight:700;padding:2px 9px;border-radius:999px;letter-spacing:.4px;white-space:nowrap;">先行利用プログラム</span>
        <span style="color:#5b9bd5;font-size:11px;font-weight:600;white-space:nowrap;">5月末まで・定員達し次第終了</span>
      </div>
      <p style="color:#185FA5 !important;font-size:13px;font-weight:700;margin:0 0 2px;line-height:1.5;">今申し込むと <span style="font-size:15px;color:#4a9ede !important;">最低3年間 ¥11,000/月</span> を価格保証</p>
      <p style="color:rgba(90,155,213,.75) !important;font-size:11px;margin:0;">将来の値上げ対象外。申し込みから3年間、料金は変わりません。</p>
    </div>
    <div class="slp-pills">
      <span class="slp-pill">カスタム開発込み</span>
      <span class="slp-pill">保守・バグ修正込み</span>
      <span class="slp-pill">機能追加込み</span>
      <span class="slp-pill">サーバー代込み</span>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,.1);padding-top:16px;">
      <div class="slp-table-row"><span>カスタム開発</span><span class="slp-table-val">込み</span></div>
      <div class="slp-table-row"><span>保守・バグ修正</span><span class="slp-table-val">込み</span></div>
      <div class="slp-table-row"><span>機能追加（要望対応）</span><span class="slp-table-val">込み</span></div>
      <div class="slp-table-row"><span>サーバー代</span><span class="slp-table-val">込み</span></div>
      <div class="slp-table-row"><span>解約</span><span class="slp-table-val">いつでも可</span></div>
      <div class="slp-table-row"><span style="color:#5b9bd5;">先行利用・価格保証期間</span><span class="slp-table-val" style="color:#5b9bd5;">最低3年</span></div>
    </div>
  </div>
  <div class="slp-cta-center" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
    <button onclick="slpOpenConsult()" class="slp-btn-s" style="border:1px solid rgba(255,255,255,.5);cursor:pointer;">まずはご試用</button>
    <a href="https://buy.stripe.com/14A5kC6dc7z77r0etTafS0Z" target="_blank" rel="noopener noreferrer" class="slp-btn-p">申し込む</a>
  </div>
  <p style="text-align:center;font-size:12px;color:rgba(255,255,255,.4) !important;margin:8px 0 0;">
    申し込みをもって
    <button onclick="document.getElementById('slp-terms-modal').style.display='flex'" style="color:rgba(255,255,255,.5);font-size:12px;text-decoration:underline;cursor:pointer;background:none;border:none;padding:0;font-family:inherit;">利用規約</button>
    に同意したことになります。
  </p>
</section>

<section id="rep">
  <h2>Shift OS 代表</h2>
  <div style="display:flex;align-items:flex-start;gap:20px;">
    <picture>
      <source srcset="/icon.webp" type="image/webp">
      <img
        src="/icon.png"
        alt="プロフィール"
        width="144" height="144"
        style="width:72px;height:72px;border-radius:50%;object-fit:cover;flex-shrink:0;border:1px solid rgba(255,255,255,.15);"
      >
    </picture>
    <div>
      <p style="color:#fff !important;font-size:15px;font-weight:700;margin:0 0 6px;">Haru Takayanagi</p>
      <p style="font-size:13px;margin:0 0 14px;line-height:1.7;">
        東京都市大学在学。TeamLab Inc.や複数のIT企業でクリエイティブ・システム開発・業務効率化を担当。
      </p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="https://www.instagram.com/tk.haru" target="_blank" rel="noopener noreferrer"
          style="display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.6) !important;font-size:13px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,.2);padding-bottom:1px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          Instagram
        </a>
        <a href="https://www.linkedin.com/in/tkharu/en" target="_blank" rel="noopener noreferrer"
          style="display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.6) !important;font-size:13px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,.2);padding-bottom:1px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
          LinkedIn
        </a>
      </div>
    </div>
  </div>
</section>

<section>
  <h2>よくあるご質問</h2>
  <div class="slp-faq-item"><p class="slp-faq-q">どんな業種でも使えますか？</p><p class="slp-faq-a">はい。飲食・小売・美容・介護・事務など、様々な業種に対応いたします。</p></div>
  <div class="slp-faq-item"><p class="slp-faq-q">カスタムの範囲はどこまでですか？</p><p class="slp-faq-a">シフト管理に関わる機能であれば基本的に対応します。月額の中で要望を言い続けられるので、使いながら少しずつ改善していけます。</p></div>
  <div class="slp-faq-item"><p class="slp-faq-q">解約はできますか？</p><p class="slp-faq-a">ページ下部よりいつでも解約可能です。解約翌月からの請求は発生しません。</p></div>
</section>

<section>
  <h2>お支払管理・ご解約</h2>
  <p class="slp-section-note">お名前・メールアドレス変更・ご解約等はこちらから。</p>
  <div style="text-align:center;">
    <a href="https://billing.stripe.com/p/login/14A6oG6dc1aJeTsbhHafS00" class="slp-btn-s" target="_blank" rel="noopener noreferrer">お支払管理</a>
  </div>
</section>
</div>

<!-- 無料相談モーダル -->
</main>
<div id="slp-consult-modal" style="font-family:-apple-system,BlinkMacSystemFont,'Helvetica Neue',sans-serif;display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,.75);padding:20px;"><style>#slp-consult-modal input,#slp-consult-modal select,#slp-consult-modal textarea,#slp-consult-modal button,#slp-consult-modal label{font-family:-apple-system,BlinkMacSystemFont,\'Helvetica Neue\',sans-serif!important;}</style>
  <div style="background:#111;border:1px solid rgba(255,255,255,.15);border-radius:16px;max-width:480px;width:100%;max-height:90vh;display:flex;flex-direction:column;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid rgba(255,255,255,.1);">
      <h3 style="margin:0;font-size:16px;font-weight:700;color:#fff;">お問い合せ</h3>
      <button onclick="document.getElementById('slp-consult-modal').style.display='none'" style="background:none;border:none;color:rgba(255,255,255,.5);font-size:24px;cursor:pointer;padding:0;line-height:1;">&times;</button>
    </div>
    <div style="overflow-y:auto;padding:20px 24px;">
      <div id="slp-form-area">
        <div style="margin-bottom:16px;">
          <label style="display:block;font-size:12px;font-weight:600;color:rgba(255,255,255,.6);margin-bottom:6px;">メールアドレス <span style="color:#ef4444;">*</span></label>
          <input id="slp-email" type="email" placeholder="your@email.com" style="width:100%;padding:10px 12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.2);border-radius:8px;color:#fff;font-size:14px;outline:none;box-sizing:border-box;">
        </div>
        <div style="margin-bottom:16px;">
          <label style="display:block;font-size:12px;font-weight:600;color:rgba(255,255,255,.6);margin-bottom:6px;">目的 <span style="color:#ef4444;">*</span></label>
          <select id="slp-purpose" style="width:100%;padding:10px 12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.2);border-radius:8px;color:#fff;font-size:14px;outline:none;box-sizing:border-box;appearance:none;">
            <option value="" style="background:#111;">選択してください</option>
            <option value="無料試用の開始" style="background:#111;" selected>無料試用の開始</option>
            <option value="料金・プランについて" style="background:#111;">料金・プランについて</option>
            <option value="カスタム内容の相談" style="background:#111;">カスタム内容の相談</option>
            <option value="既存システムからの移行" style="background:#111;">既存システムからの移行</option>
            <option value="その他" style="background:#111;">その他</option>
          </select>
        </div>
        <div style="margin-bottom:16px;">
          <label style="display:block;font-size:12px;font-weight:600;color:rgba(255,255,255,.6);margin-bottom:6px;">メッセージ（任意）</label>
          <textarea id="slp-message" rows="4" placeholder="" style="width:100%;padding:10px 12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.2);border-radius:8px;color:#fff;font-size:14px;outline:none;box-sizing:border-box;resize:vertical;font-family:inherit;"></textarea>
        </div>
        <div id="slp-form-error" style="display:none;color:#ef4444;font-size:13px;margin-bottom:12px;"></div>
        <button id="slp-submit-btn" onclick="slpSubmitForm()" style="width:100%;padding:13px;background:#fff;color:#000;border:none;border-radius:999px;font-size:15px;font-weight:700;cursor:pointer;">送信する</button>
        <button onclick="document.getElementById('slp-consult-modal').style.display='none'" style="width:100%;padding:11px;background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:999px;font-size:13px;cursor:pointer;margin-top:8px;">閉じる</button>
      </div>
      <div id="slp-form-thanks" style="display:none;text-align:center;padding:20px 0;">
        <p style="font-size:32px;margin:0 0 12px;">✓</p>
        <p style="color:#fff !important;font-size:16px;font-weight:700;margin:0 0 8px;">送信しました</p>
        <p style="font-size:13px;margin:0 0 20px;">内容を確認のうえ、1営業日以内にご連絡いたします。</p>
        <button onclick="document.getElementById('slp-consult-modal').style.display='none';document.getElementById('slp-form-thanks').style.display='none';document.getElementById('slp-form-area').style.display='block';" style="padding:10px 32px;background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:999px;font-size:14px;cursor:pointer;">閉じる</button>
      </div>
    </div>
  </div>
</div>

<!-- 利用規約モーダル -->
<div id="slp-terms-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,.7);padding:20px;">
  <div style="background:#111;border:1px solid rgba(255,255,255,.15);border-radius:16px;max-width:560px;width:100%;max-height:80vh;display:flex;flex-direction:column;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid rgba(255,255,255,.1);">
      <h3 style="margin:0;font-size:16px;font-weight:700;color:#fff;">Shift OS 利用規約</h3>
      <button onclick="document.getElementById('slp-terms-modal').style.display='none'" style="background:none;border:none;color:rgba(255,255,255,.5);font-size:24px;cursor:pointer;padding:0;line-height:1;">&times;</button>
    </div>
    <div style="overflow-y:auto;padding:20px 24px;font-size:13px;color:rgba(255,255,255,.6);line-height:1.8;">
      <p style="font-weight:700;color:#fff !important;margin:0 0 12px;">第1条（サービスの提供）</p>
      <p style="margin:0 0 16px;">Shift OSは、カスタムシフト管理システムの開発・保守・運用サービスを月額¥11,000（税込）にて提供します。</p>

      <p style="font-weight:700;color:#fff !important;margin:0 0 12px;">第2条（契約期間・解約）</p>
      <p style="margin:0 0 16px;">契約は申し込み日より開始し、解約申請月の翌月末をもって終了します。解約はお支払管理ページよりいつでも申請できます。</p>

      <p style="font-weight:700;color:#fff !important;margin:0 0 12px;">第3条（料金・支払い）</p>
      <p style="margin:0 0 16px;">月額料金は毎月自動で請求されます。先行利用プログラム適用者は、申し込みから最低3年間、¥11,000/月の価格が保証されます。</p>

      <p style="font-weight:700;color:#fff !important;margin:0 0 12px;">第4条（カスタム開発の範囲）</p>
      <p style="margin:0 0 16px;">シフト管理に関わる機能であれば月額の範囲内で対応します。大規模な設計変更や別システムとの連携については別途協議します。</p>

      <p style="font-weight:700;color:#fff !important;margin:0 0 12px;">第5条（知的財産権）</p>
      <p style="margin:0 0 16px;">提供するシステムの著作権はShift OSに帰属します。ご契約中は利用権を付与します。</p>

      <p style="font-weight:700;color:#fff !important;margin:0 0 12px;">第6条（免責事項）</p>
      <p style="margin:0 0 16px;">サーバー障害・天災等によるサービス停止について、当社は責任を負いません。ただし迅速な復旧に努めます。</p>

      <p style="font-weight:700;color:#fff !important;margin:0 0 12px;">第7条（個人情報）</p>
      <p style="margin:0 0 0;">取得した個人情報はサービス提供の目的以外には使用しません。</p>
    </div>
    <div style="padding:16px 24px;border-top:1px solid rgba(255,255,255,.1);text-align:center;">
      <button onclick="document.getElementById('slp-terms-modal').style.display='none'" style="background:#fff;color:#000;border:none;padding:10px 32px;border-radius:999px;font-size:14px;font-weight:700;cursor:pointer;">閉じる</button>
    </div>
  </div>
</div>

<script>
// モーダル背景クリックで閉じる
document.getElementById('slp-consult-modal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});

function slpOpenConsult() {
  // フォームをリセット
  document.getElementById('slp-form-area').style.display = 'block';
  document.getElementById('slp-form-thanks').style.display = 'none';
  document.getElementById('slp-form-error').style.display = 'none';
  var btn = document.getElementById('slp-submit-btn');
  btn.textContent = '送信する';
  btn.disabled = false;
  document.getElementById('slp-purpose').value = '無料試用の開始';
  document.getElementById('slp-consult-modal').style.display = 'flex';
}
function slpSubmitForm() {
  var email   = document.getElementById('slp-email').value.trim();
  var purpose = document.getElementById('slp-purpose').value;
  var message = document.getElementById('slp-message').value.trim();
  var errEl   = document.getElementById('slp-form-error');
  var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  if (!email || !emailRe.test(email)) {
    errEl.textContent = '正しいメールアドレスを入力してください。';
    errEl.style.display = 'block';
    return;
  }
  if (!purpose) {
    errEl.textContent = '目的を選択してください。';
    errEl.style.display = 'block';
    return;
  }
  errEl.style.display = 'none';

  var btn = document.getElementById('slp-submit-btn');
  btn.textContent = '送信中...';
  btn.disabled = true;

  var fd = new FormData();
  fd.append('slp_consult', '1');
  fd.append('email', email);
  fd.append('purpose', purpose);
  fd.append('message', message);

  fetch(location.href, { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      if (d.ok) {
        // GA4イベント送信
        if(typeof gtag !== 'undefined') {
          gtag('event', 'form_submit', {
            event_category: 'lead',
            event_label: '無料相談フォーム'
          });
        }
        // Meta Pixel Leadイベント
        if(typeof fbq !== 'undefined') {
          fbq('track', 'Lead');
        }
        document.getElementById('slp-form-area').style.display = 'none';
        document.getElementById('slp-form-thanks').style.display = 'block';
      } else {
        errEl.textContent = d.error || '送信に失敗しました。';
        errEl.style.display = 'block';
        btn.textContent = '送信する';
        btn.disabled = false;
      }
    })
    .catch(function() {
      errEl.textContent = '送信に失敗しました。時間をおいて再度お試しください。';
      errEl.style.display = 'block';
      btn.textContent = '送信する';
      btn.disabled = false;
    });
}
</script>

<script>
document.querySelectorAll('a[href*="demo"]').forEach(function(el) {
  el.addEventListener('click', function() {
    if(typeof gtag !== 'undefined') gtag('event', 'demo_click', { event_label: el.href });
  });
});
document.querySelectorAll('a[href*="stripe"]').forEach(function(el) {
  el.addEventListener('click', function() {
    if(typeof gtag !== 'undefined') gtag('event', 'purchase_click', {
      event_category: 'conversion',
      event_label: '申し込む'
    });
  });
});
</script>
</body>
</html>
