<?php
$hosts = [
    'smtp.lolipop.ne.jp',
    'mail.lolipop.ne.jp', 
    'lolipop.ne.jp',
    'smtp.lolipop.jp',
    'localhost',
    '127.0.0.1',
];
foreach ($hosts as $h) {
    $ip = gethostbyname($h);
    echo "$h => $ip\n";
}

// sendmailパスも確認
echo "\nsendmail: " . (is_executable('/usr/sbin/sendmail') ? '存在' : '不在') . "\n";
echo "mail(): " . (function_exists('mail') ? '有効' : '無効') . "\n";
echo "php.ini sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";
