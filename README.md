####Portfolio

### Automated Reservation Management System (JavaScript)
Developed a centralized automation system using Google Apps Script (GAS) to parse incoming rental car reservation emails from various platforms and automatically input the parsed data into a Notion database via the Notion API.

<details>
<summary><b>View Code</b></summary>

```javascript
const NOTION_API_TOKEN = '-';
const DATABASE_ID = '-';
const PROCESSED_LABEL_NAME = 'GAS Processed';
const IGNORED_LABEL_NAME = 'GAS Ignored';
const REPLY_FORWARD_LABEL_NAME = 'GAS Reply/Forward';

const PROP_DATE = '問合日';
const PROP_NAME = '顧客名';
const PROP_TEL = '顧客電話';
const PROP_EMAIL = '顧客メール';
const PROP_SERVICE = '区分';
const PROP_PLAN = 'プラン';
const PROP_CLASS = 'クラス';
const PROP_ID = '予約番号';
const PROP_PAYMENT = 'チーム共有事項';
const PROP_CONTENT = '問い合わせ内容';

function processEmailsToNotion() {
  let processedLabel = GmailApp.getUserLabelByName(PROCESSED_LABEL_NAME);
  if (!processedLabel) {
    processedLabel = GmailApp.createLabel(PROCESSED_LABEL_NAME);
  }
  let ignoredLabel = GmailApp.getUserLabelByName(IGNORED_LABEL_NAME);
  if (!ignoredLabel) {
    ignoredLabel = GmailApp.createLabel(IGNORED_LABEL_NAME);
  }
  let replyLabel = GmailApp.getUserLabelByName(REPLY_FORWARD_LABEL_NAME);
  if (!replyLabel) {
    replyLabel = GmailApp.createLabel(REPLY_FORWARD_LABEL_NAME);
  }

  // 1週間以内の新着メールのみを対象にするようクエリを絞る（API制限対策）
  const query = '(in:inbox OR in:spam) newer_than:7d -label:"' + PROCESSED_LABEL_NAME + '" -label:"' + IGNORED_LABEL_NAME + '" -label:"' + REPLY_FORWARD_LABEL_NAME + '" -subject:"Re:" -subject:"Fwd:" -subject:"Fw:" -subject:"転送" (予約 OR 問い合わせ OR "skyticket" OR キャンセル OR 変更)';
  const threads = GmailApp.search(query, 0, 50);
  
  if (threads.length === 0) {
    console.log("対象メールなし");
    return;
  }

  console.log("処理対象: " + threads.length + "件");

  threads.forEach(thread => {
    // API呼び出しを最小限にするため、件名で大まかにフィルタリング
    const threadSubject = thread.getFirstMessageSubject();
    if (/^(Re|Fwd|Fw|転送)[:：]/i.test(threadSubject.trim())) {
      thread.addLabel(replyLabel);
      return;
    }

    const messages = thread.getMessages();
    let hadError = false;
    let anyCandidateFound = false; // 対象サービスの候補があったか

    for (const message of messages) {
      const subject = message.getSubject();
      const from = message.getFrom();
      
      // スタッフ間のやり取り（転送・返信）をスキップ
      if (/^(Re|Fwd|Fw|転送)[:：]/i.test(subject.trim())) {
        continue;
      }

      // キャンセル・変更の判定
      let statusPrefix = "";
      if (subject.includes("キャンセル")) {
        statusPrefix = "【キャンセル】";
      } else if (subject.includes("変更")) {
        statusPrefix = "【変更】";
      }

      // 該当サービスかどうかの事前判定（本文取得API呼び出しを節約）
      const isRakuten = subject.includes("楽天トラベル") && (subject.includes("予約受付") || subject.includes("キャンセル") || subject.includes("変更") || subject.includes("予約番号"));
      const isJalan = subject.includes("じゃらんnet") && (subject.includes("予約通知") || subject.includes("キャンセル") || subject.includes("変更"));
      const isSkyticket = subject.includes("skyticket") || subject.includes("スカイチケット");
      const isERenta = subject.includes("-レンタカー") || /@-\.com|@-\.com/i.test(from);
      const is- = subject.includes("-レンタカー") || subject.includes("-");
      const isTocoo = subject.includes("Tocoo");

      if (!isRakuten && !isJalan && !isSkyticket && !isERenta && !is- && !isTocoo) {
        continue; // 該当しない場合は本文を取得せず次へ
      }

      anyCandidateFound = true;

      const body = message.getPlainBody();
      const date = message.getDate();

      let data = null;
      if (isRakuten) {
        data = parseRakuten(body, date, statusPrefix);
      } else if (isJalan) {
        data = parseJalan(body, date, statusPrefix);
      } else if (isSkyticket) {
        data = parseSkyticket(body, date, statusPrefix);
      } else if (isERenta) {
        if (subject.includes("問い合わせがありました")) {
          data = parseOwnSiteInquiry(body, date, statusPrefix);
        } else {
          data = parseOwnSiteBooking(body, date, statusPrefix);
        }
      } else if (is- && (body.includes("Name") || subject.includes("enquiry") || body.includes("Reservation ID") || statusPrefix !== "")) {
        data = parse-Inquiry(body, date, statusPrefix);
      } else if (isTocoo) {
        data = parseTocoo(body, date, statusPrefix);
      }

      if (!data) {
        continue;
      }
      
      // 名前が取れない場合はこのメッセージのみスキップ（スレッドは継続）
      if (data.name === "情報なし" || data.name === "") {
        console.warn("解析失敗(名前不明): [" + data.serviceOption + "] " + subject);
        continue;
      }

      if (isAlreadyRegistered(data)) {
        console.log("重複スキップ: [" + data.serviceOption + "] " + data.name);
        continue;
      }

      const success = sendToNotion(data);
      if (success) {
        console.log("登録成功: [" + data.serviceOption + "] " + data.name);
      } else {
        console.error("Notion登録エラー: [" + data.serviceOption + "] " + data.name);
        hadError = true;
      }
    }

    // ラベル付与の方針:
    // - エラーが無ければラベル付与（処理済み/重複/対象外いずれも）
    // - エラーがあればラベルを付けずに再試行に回す
    if (!hadError && anyCandidateFound) {
      thread.addLabel(processedLabel);
    } else if (!hadError && !anyCandidateFound) {
      thread.addLabel(ignoredLabel);
    }
  });
}

function parseRakuten(body, date, statusPrefix) {
  const planRaw = extractValue(body, ["プラン名", "詳細車両クラス", "プラン"]);
  const classRaw = extractValue(body, ["車両クラス", "詳細車両クラス"]);
  
  let id = extractValue(body, ["予約番号", "予約受付番号"]);
  let name = extractValue(body, ["予約者氏名", "利用者氏名", "氏名"]);
  let questions = "楽天経由";

  if (statusPrefix) {
    if (id !== "情報なし") id = statusPrefix + " " + id;
    if (name !== "情報なし") name = statusPrefix + " " + name;
    questions = statusPrefix + "\n" + questions;
  }

  return {
    date: date,
    serviceOption: "予約 (楽天)",
    id: id,
    name: name,
    tel: extractValue(body, ["電話番号", "連絡先"]),
    email: "情報なし",
    planRaw: planRaw,
    classRaw: classRaw,
    planOption: mapPlan(planRaw),
    carClassOption: mapClass(classRaw),
    payment: extractValue(body, ["合計料金", "基本料金", "合計額"]),
    questions: questions
  };
}

function parseJalan(body, date, statusPrefix) {
  const planRaw = extractValue(body, ["プラン名", "プラン"]);
  const classRaw = extractValue(body, ["車両クラス", "車種"]);
  
  let id = extractValue(body, ["予約番号"]);
  let name = extractValue(body, ["予約者氏名", "氏名"]).replace(/様$/, "").trim();
  let questions = extractValue(body, ["質問回答", "オプション"]);

  if (statusPrefix) {
    if (id !== "情報なし") id = statusPrefix + " " + id;
    if (name !== "情報なし" && name !== "") name = statusPrefix + " " + name;
    questions = statusPrefix + (questions !== "情報なし" ? "\n" + questions : "");
  }

  return {
    date: date,
    serviceOption: "予約 (じゃらん)",
    id: id,
    name: name,
    tel: extractValue(body, ["電話番号", "連絡先"]),
    email: extractValue(body, ["メールアドレス"]),
    planRaw: planRaw,
    classRaw: classRaw,
    planOption: mapPlan(planRaw),
    carClassOption: mapClass(classRaw),
    payment: extractValue(body, ["合計金額", "お支払い金額"]),
    questions: questions
  };
}

function parseSkyticket(body, date, statusPrefix) {
  const planRaw = extractValue(body, ["プラン名", "プラン"]);
  const classRaw = extractValue(body, ["車両タイプ / クラス", "車両クラス", "クラス"]);
  
  let id = extractValue(body, ["予約番号", "申込番号"]);
  let name = extractValue(body, ["ご利用者名", "お名前", "氏名"]).replace(/様$/, "").trim();
  let questions = extractValue(body, ["オプション"]);

  if (statusPrefix) {
    if (id !== "情報なし") id = statusPrefix + " " + id;
    if (name !== "情報なし" && name !== "") name = statusPrefix + " " + name;
    questions = statusPrefix + (questions !== "情報なし" ? "\n" + questions : "");
  }

  return {
    date: date,
    serviceOption: "予約 (スカイ)",
    id: id,
    name: name,
    tel: extractValue(body, ["電話番号"]),
    email: extractValue(body, ["メールアドレス"]),
    planRaw: planRaw,
    classRaw: classRaw,
    planOption: mapPlan(planRaw),
    carClassOption: mapClass(classRaw),
    payment: extractValue(body, ["合計料金", "合計"]),
    questions: questions
  };
}

function parseOwnSiteBooking(body, date, statusPrefix) {
  const planRawPrimary = extractValue(body, ["プラン名", "プラン"]);
  const optionRaw = extractValue(body, ["オプション"]);
  const planRaw = (planRawPrimary !== "情報なし" && planRawPrimary !== "") ? planRawPrimary : optionRaw;
  const classRaw = extractValue(body, ["ご予約クラス", "クラス", "車種"]);
  
  let id = extractValue(body, ["予約番号", "予約ID"]);
  let name = extractValue(body, ["お名前", "氏名"]).replace(/様$/, "").trim();
  let questions = extractValue(body, ["コメント、連絡事項など", "コメント", "連絡事項"]);

  if (statusPrefix) {
    if (id !== "情報なし") id = statusPrefix + " " + id;
    if (name !== "情報なし" && name !== "") name = statusPrefix + " " + name;
    questions = statusPrefix + (questions !== "情報なし" ? "\n" + questions : "");
  }

  return {
    date: date,
    serviceOption: "予約 (-レンタ公式)",
    id: id,
    name: name,
    tel: extractValue(body, ["電話番号"]),
    email: extractValue(body, ["メールアドレス"]),
    planRaw: planRaw,
    classRaw: classRaw,
    planOption: mapPlan(planRaw),
    carClassOption: mapClass(classRaw),
    payment: extractValue(body, ["ご利用金額", "合計"]),
    questions: questions
  };
}

function parseOwnSiteInquiry(body, date, statusPrefix) {
  let name = extractValue(body, ["お名前", "氏名"]).replace(/様$/, "").trim();
  let questions = extractValue(body, ["お問い合わせ内容"]);

  if (statusPrefix) {
    if (name !== "情報なし" && name !== "") name = statusPrefix + " " + name;
    questions = statusPrefix + (questions !== "情報なし" ? "\n" + questions : "");
  }

  return {
    date: date,
    serviceOption: "メール問合せ (-レンタHP)",
    id: "",
    name: name,
    tel: extractValue(body, ["お電話番号", "電話番号"]),
    email: extractValue(body, ["メールアドレス"]),
    planRaw: "",
    classRaw: "",
    planOption: null,
    carClassOption: null,
    payment: "",
    questions: questions
  };
}

function parse-Inquiry(body, date, statusPrefix) {
  let id = extractValue(body, ["Reservation ID", "No"]);
  let name = extractValue(body, ["Name", "Customer Name", "Customer name"]);
  let questions = extractValue(body, ["Message", "Inquiry", "Message Body"]);

  if (statusPrefix) {
    if (id !== "情報なし") id = statusPrefix + " " + id;
    if (name !== "情報なし" && name !== "") name = statusPrefix + " " + name;
    questions = statusPrefix + (questions !== "情報なし" ? "\n" + questions : "");
  }

  return {
    date: date,
    serviceOption: "-お問い合わせ",
    id: id,
    name: name,
    tel: extractValue(body, ["Phone number", "Tel", "Phone"]),
    email: extractValue(body, ["Mail", "Email", "Email address"]),
    planRaw: "",
    classRaw: "",
    planOption: null,
    carClassOption: null,
    payment: "",
    questions: questions
  };
}

function parseTocoo(body, date, statusPrefix) {
  let id = extractValue(body, ["予約番号", "Reservation Number"]);
  let name = extractValue(body, ["氏名", "Name"]);
  let questions = "";

  if (statusPrefix) {
    if (id !== "情報なし") id = statusPrefix + " " + id;
    if (name !== "情報なし" && name !== "") name = statusPrefix + " " + name;
    questions = statusPrefix;
  }

  return {
    date: date,
    serviceOption: "予約 (トクー)",
    id: id,
    name: name,
    tel: extractValue(body, ["電話番号", "Tel"]),
    email: extractValue(body, ["メールアドレス", "Email"]),
    planRaw: "",
    classRaw: "",
    planOption: null,
    carClassOption: null,
    payment: "",
    questions: questions
  };
}

function extractValue(body, labels) {
  if (!Array.isArray(labels)) labels = [labels];
  for (let label of labels) {
    const pattern = new RegExp(`(?:^|\\n|\\r)[\\s　・\\[\\(]*${label}[\\s　\\]\\)]*[:：]+[\\s　]*(.*)`);
    const match = body.match(pattern);
    if (match && match[1] && match[1].trim() !== "") {
      let val = match[1].split(/\r|\n/)[0].trim();
      if (val !== "") return val;
    }
  }
  return "情報なし";
}

function mapPlan(text) {
  if (!text || text === "情報なし") return null;
  if (text.includes("免責") || text.includes("補償")) return "免責補償加入";
  if (text.includes("リミテッド")) return "リミテッド";
  if (text.includes("通常")) return "通常プラン";
  if (text.includes("冬")) return "冬キャンペーン";
  if (text.includes("秋")) return "秋キャンペーン";
  if (text.includes("羽田")) return "羽田プラン";
  if (text.includes("NCS")) return "NCS";
  if (text.includes("キャンピング")) return "キャンピングカー";
  return null;
}

function mapClass(text) {
  if (!text || text === "情報なし") return null;
  const t = text.toUpperCase();
  if (t.includes("C1") && t.includes("喫煙")) return "C1 (喫煙)";
  if (t.includes("C1")) return "C1 (禁煙)";
  if (t.includes("K1") || t.includes("軽")) return "K1";
  if (t.includes("S2")) return "S2";
  if (t.includes("W1")) return "W1";
  if (t.includes("W2")) return "W2";
  if (t.includes("V1")) return "V1";
  if (t.includes("V2")) return "V2";
  if (t.includes("S1")) return "S1";
  return null;
}

function isAlreadyRegistered(data) {
  const url = `https://api.notion.com/v1/databases/${DATABASE_ID}/query`;
  let filter;

  if (data.id && data.id !== "情報なし") {
    filter = { "property": PROP_ID, "rich_text": { "equals": data.id } };
  } else if (data.email && data.email.includes("@")) {
    filter = { "and": [{ "property": PROP_NAME, "title": { "equals": data.name } }, { "property": PROP_EMAIL, "email": { "equals": data.email } }] };
  } else {
    filter = { "property": PROP_NAME, "title": { "equals": data.name } };
  }

  const options = {
    "method": "post",
    "headers": { "Authorization": "Bearer " + NOTION_API_TOKEN, "Notion-Version": "2022-06-28", "Content-Type": "application/json" },
    "payload": JSON.stringify({ "filter": filter }),
    "muteHttpExceptions": true
  };

  try {
    const response = UrlFetchApp.fetch(url, options);
    const result = JSON.parse(response.getContentText());
    return result.results && result.results.length > 0;
  } catch (e) {
    return false;
  }
}

function sendToNotion(data) {
  const url = '[https://api.notion.com/v1/pages](https://api.notion.com/v1/pages)';
  const properties = {};

  if (data.date) {
    // Utilities.formatDate の第2引数に明示的にタイムゾーン ("Asia/Tokyo") を指定し、オフセット(+09:00)付きで日時（24時間制）を送る
    const dateStr = Utilities.formatDate(data.date, "Asia/Tokyo", "yyyy-MM-dd'T'HH:mmXXX");
    properties[PROP_DATE] = { "date": { "start": dateStr } };
  }

  properties[PROP_NAME] = { "title": [{ "text": { "content": data.name || "名称未取得" } }] };

  if (data.tel && data.tel !== "情報なし") {
    // 電話番号をサニタイズ（数字と+、-のみにする）
    const cleanTel = String(data.tel).replace(/[^\d\+\-]/g, '');
    properties[PROP_TEL] = { "phone_number": cleanTel };
  }

  if (data.email && data.email.includes("@")) {
    properties[PROP_EMAIL] = { "email": data.email };
  }

  if (data.id && data.id !== "情報なし") {
    // rich_text を確実に文字列化
    properties[PROP_ID] = { "rich_text": [{ "text": { "content": String(data.id) } }] };
  }

  if (data.serviceOption) {
    properties[PROP_SERVICE] = { "select": { "name": data.serviceOption } };
  }

  if (data.planOption) {
    properties[PROP_PLAN] = { "select": { "name": data.planOption } };
  }

  if (data.carClassOption) {
    // multi_select の name を確実に文字列化（前後空白削除付き）
    properties[PROP_CLASS] = { "multi_select": [{ "name": String(data.carClassOption).trim() }] };
  }

  let paymentInfo = data.payment !== "情報なし" ? `決済額: ${data.payment}` : "";
  // rich_text を確実に文字列化
  properties[PROP_PAYMENT] = { "rich_text": [{ "text": { "content": String(paymentInfo) } }] };

  let contentText = "";
  if (data.questions && data.questions !== "情報なし" && data.questions !== "") contentText += `内容: ${data.questions}\n`;
  if (!data.planOption && data.planRaw !== "情報なし" && data.planRaw !== "") contentText += `元プラン: ${data.planRaw}\n`;
  if (!data.carClassOption && data.classRaw !== "情報なし" && data.classRaw !== "") contentText += `元クラス: ${data.classRaw}\n`;
  
  // rich_text を文字列化
  properties[PROP_CONTENT] = { "rich_text": [{ "text": { "content": String(contentText.trim()) } }] };

  const options = {
    "method": "post",
    "headers": { "Authorization": "Bearer " + NOTION_API_TOKEN, "Notion-Version": "2022-06-28", "Content-Type": "application/json" },
    "payload": JSON.stringify({ "parent": { "database_id": DATABASE_ID }, "properties": properties }),
    "muteHttpExceptions": true
  };

  const response = UrlFetchApp.fetch(url, options);
  
  const status = response.getResponseCode();
  if (status < 200 || status >= 300) {
    console.error("Notion API Error: " + response.getContentText());
  }

  return status >= 200 && status < 300;
}

function createTimeDrivenTriggers() {
  // 既存のトリガーがある場合は削除して重複を防ぐ
  const allTriggers = ScriptApp.getProjectTriggers();
  for (let i = 0; i < allTriggers.length; i++) {
    if (allTriggers[i].getHandlerFunction() === 'processEmailsToNotion') {
      ScriptApp.deleteTrigger(allTriggers[i]);
    }
  }
  
  // 15分ごとに processEmailsToNotion を実行するトリガーを作成
  ScriptApp.newTrigger('processEmailsToNotion')
      .timeBased()
      .everyMinutes(15)
      .create();
      
  console.log("15分間隔の定期実行トリガーを作成しました。");
}
```
</details>

### New Employee Training Quiz Web App (PHP)
Built an interactive web-based quiz application using PHP and JavaScript to measure and record the training progress of new employees at a rental car office, featuring automated email notifications for test results.

<details>
<summary><b>View Code</b></summary>

```php
<?php
$admin_email = "-@gmail.com"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_quiz') {
    $quiz_category = htmlspecialchars($_POST['quiz_category'] ?? '不明', ENT_QUOTES, 'UTF-8');
    $user_phone = htmlspecialchars($_POST['user_phone'] ?? '未入力', ENT_QUOTES, 'UTF-8');
    $correct_count = (int)($_POST['correct_count'] ?? 0);
    $total_questions = (int)($_POST['total_questions'] ?? 6);
    
    $results_json = $_POST['results_data'] ?? '[]';
    $results_output = json_decode($results_json, true);

    $pass_threshold = ceil($total_questions * 0.8);
    $is_passed = ($correct_count >= $pass_threshold);
    $status_text = $is_passed ? '合格' : '不合格';

    $subject = '【クイズ結果: ' . $quiz_category . '】電話番号: ' . $user_phone . ' (' . $status_text . ')';
    
    $message = "クイズの回答が送信されました。\n\n";
    $message .= "受講カテゴリ: " . $quiz_category . "\n";
    $message .= "電話番号: " . $user_phone . "\n";
    $message .= "結果: {$correct_count} / {$total_questions}問正解 ({$status_text})\n";
    $message .= "--------------------------------------\n";
    
    if (is_array($results_output)) {
        foreach ($results_output as $res) {
            $q_num = $res['q_num'] ?? '';
            $user_ans = $res['user_ans'] ?? '未回答';
            $is_c = !empty($res['is_correct']);
            
            $message .= $q_num . ": " . ($is_c ? "〇 正解" : "× 不正解") . "\n";
            $message .= "ユーザーの回答: " . $user_ans . "\n\n";
        }
    }
    
    $server_name = isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== "" ? $_SERVER['SERVER_NAME'] : 'quiz-sys.local';
    $headers = "From: noreply@" . $server_name . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    @mail($admin_email, $subject, $message, $headers);
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'success']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>教育シートクイズ</title>
<style>
@import url('[https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap](https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap)');
body {
    background-color: #121212;
    color: #e0e0e0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans JP", sans-serif;
    margin: 0;
    padding: 0;
    min-height: 100vh;
}
.quiz-wrapper {
    max-width: 680px;
    margin: 40px auto;
    background-color: #1b1b1b;
    padding: 32px 24px;
    border-radius: 20px;
    line-height: 1.6;
    box-sizing: border-box;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
.quiz-wrapper * { box-sizing: border-box; }
.info-banner {
    background: #262626;
    border-left: 5px solid #555555;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 32px;
    font-size: 0.95rem;
}
.info-banner strong {
    font-size: 1.15rem; color: #ffffff; display: block; margin-bottom: 6px;
}
.chatbot-banner {
    background: #262626; border: 1px solid #3a3a3a; border-radius: 16px;
    padding: 20px 24px; margin-bottom: 32px; display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 16px;
}
.chatbot-text strong {
    display: block; color: #ffffff; font-size: 1rem; margin-bottom: 4px;
}
.chatbot-text span { color: #aaaaaa; font-size: 0.85rem; }
.chatbot-btn {
    background: #3a3a3a; color: #e0e0e0; border: 1px solid #555555;
    padding: 10px 20px; border-radius: 20px; text-decoration: none; font-weight: 500;
    font-size: 0.9rem; transition: all 0.2s ease; cursor: pointer; display: inline-block; text-align: center;
}
.chatbot-btn:hover { background: #4a4a4a; color: #ffffff; }

.quiz-card {
    background: #262626; border: 1px solid #3a3a3a; border-radius: 16px;
    padding: 32px 28px; margin-bottom: 24px; display: none;
    animation: fadeIn 0.4s ease;
}
.quiz-card.active { display: block; }

.category-btn {
    display: block; width: 100%; background: #2d3748; color: #ffffff;
    padding: 20px; border: 2px solid #4a5568; border-radius: 16px; font-size: 1.1rem;
    font-weight: bold; margin-bottom: 16px; cursor: pointer; transition: all 0.2s; text-align: left;
}
.category-btn:hover { background: #3a4759; border-color: #63b3ed; transform: translateY(-2px); }
.category-desc { display: block; font-size: 0.85rem; color: #a0aec0; font-weight: normal; margin-top: 6px; }

.question-title {
    font-weight: 700; color: #ffffff; margin-bottom: 20px; display: block; font-size: 1.1rem;
}
.radio-item { display: block; margin: 12px 0; position: relative; }
.radio-item input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
.radio-label {
    display: flex; align-items: center; padding: 16px 20px; background: #262626;
    border: 1px solid #3a3a3a; border-radius: 12px; cursor: pointer; font-weight: 500;
    color: #e0e0e0; transition: all 0.2s ease;
}
.radio-label:hover { background: #333333; }
.radio-item input[type="radio"]:checked + .radio-label {
    background: #3a3a3a; color: #ffffff; border-color: #63b3ed; border-width: 2px; padding: 15px 19px;
}

.explanation-area {
    margin-top: 24px; padding: 20px; border-radius: 12px; display: none; animation: slideDown 0.3s ease;
}
.explanation-area.correct { background: #2f3e35; border: 1px solid #48bb78; }
.explanation-area.wrong { background: #4a2b2b; border: 1px solid #fc8181; }

.exp-title { font-weight: bold; margin-bottom: 8px; font-size: 1.1rem; }
.correct-title { color: #68d391; }
.wrong-title { color: #fc8181; }
.exp-correct-ans { font-weight: bold; margin-bottom: 12px; display: block; }
.exp-text { color: #e2e8f0; font-size: 0.95rem; line-height: 1.6; }

.action-btn {
    background: #333333; color: #ffffff; padding: 16px 40px; border: 1px solid #555555;
    border-radius: 24px; cursor: pointer; font-weight: 500; font-size: 1.05rem; display: block;
    margin: 32px auto 0; transition: all 0.2s ease;
}
.action-btn:hover { background: #444444; }
.action-btn:disabled { background: #222222; color: #666666; cursor: not-allowed; border-color: #333333; }

.user-info-card { background: #2d3748; border: 1px solid #4a5568; }
input[type="tel"] {
    width: 100%; padding: 16px 20px; border: 1px solid #3a3a3a; border-radius: 12px;
    font-size: 1rem; font-family: inherit; background: #1b1b1b; color: #ffffff; box-sizing: border-box;
}
input[type="tel"]:focus { outline: none; border-color: #63b3ed; border-width: 2px; padding: 15px 19px; }

.final-result-card {
    background: #262626; border: 2px solid #48bb78; border-radius: 16px; padding: 32px;
    margin-bottom: 24px; display: none; text-align: center;
}
.final-result-card.failed { border-color: #fc8181; }
.res-heading { font-size: 1.5rem; font-weight: bold; margin-bottom: 16px; }
.res-score { font-size: 1.2rem; margin-bottom: 32px; }

.loader {
    border: 3px solid #333; border-top: 3px solid #fff; border-radius: 50%;
    width: 20px; height: 20px; animation: spin 1s linear infinite; display: inline-block; margin-right: 8px; vertical-align: middle;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

/* -------------------------------- */
/* PDF一覧エリア */
/* -------------------------------- */
.pdf-wrapper {
    max-width: 680px;
    margin: 40px auto;
    background-color: #1b1b1b;
    padding: 32px 24px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    box-sizing: border-box;
}
.pdf-title {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: bold;
    margin-bottom: 24px;
    text-align: center;
    border-bottom: 2px solid #3a3a3a;
    padding-bottom: 16px;
}
.pdf-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 16px;
}
.pdf-item {
    background: #262626;
    border: 1px solid #3a3a3a;
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    transition: all 0.2s ease;
    cursor: pointer;
}
.pdf-item:hover {
    background: #333333;
    transform: translateY(-2px);
    border-color: #fbd38d;
}
.pdf-item img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    margin-bottom: 10px;
    object-fit: cover;
    aspect-ratio: 3 / 4;
}
.pdf-item span {
    color: #e2e8f0;
    font-size: 0.9rem;
    font-weight: bold;
    display: block;
}
</style>
</head>
<body>

<div class="quiz-wrapper">
    <div id="quiz-header">
        <div class="info-banner">
            <strong>教育シート 確認クイズ</strong><br>
            マニュアル資料と動画の内容をもとに回答してください。
            <span id="progress-text" style="display:block; float:right; font-weight:bold; color:#fbd38d; margin-top:-24px;"></span>
        </div>
        
        <div class="chatbot-banner" id="chatbot-banner-area">
            <div class="chatbot-text">
                <strong>不明点はこちら</strong><br>
                <span>NotebookLMに質問</span>
            </div>
            <a href="[https://notebooklm.google.com/notebook/-)" target="_blank" class="chatbot-btn">質問する ↗</a>
        </div>
    </div>

    <div id="category-select-container" class="quiz-card active">
        <span class="question-title">どちらのクイズを受講しますか？</span>
        <button class="category-btn" onclick="startQuiz('part1')">
            項目1〜6：送迎・返却・清掃・電話対応
            <span class="category-desc">5/6問以上で合格</span>
        </button>
        <button class="category-btn" onclick="startQuiz('part2')">
            項目7〜8：車輛傷チェック・免許証情報
            <span class="category-desc">6/8問以上で合格</span>
        </button>
        <button class="category-btn" onclick="startQuiz('part3')">
            項目9〜16：受付・レジ・スケジュール
            <span class="category-desc">8/10問以上で合格</span>
        </button>
        <button class="category-btn" onclick="startQuiz('part4')">
            項目18〜22：予約入力・車検スケジュール
            <span class="category-desc">7/8問以上で合格</span>
        </button>
    </div>

    <div id="quiz-container"></div>
    
    <div id="submit-container" class="quiz-card user-info-card">
        <span class="question-title" style="color: #e2e8f0;">情報入力</span>
        <p style="font-size: 0.95rem; color: #a0aec0; margin-bottom: 20px;">全問終了<br>結果を通知・保存するために電話番号を入力してください。</p>
        <input type="tel" id="user_phone" placeholder="例: 09012345678" required>
        <button id="submit-btn" class="action-btn" style="margin-top: 24px;">結果を見る</button>
    </div>

    <div id="final-result-container" class="final-result-card">
        <div id="res-heading" class="res-heading"></div>
        <div id="res-score" class="res-score"></div>
        <p style="color: #a0aec0; font-size: 0.95rem;">各問題の解説は回答時に表示された通りです。</p>
        <button onclick="location.reload();" class="chatbot-btn" style="margin-top:20px; padding:12px 32px; font-size:1rem;">トップに戻る</button>
    </div>
</div>

<div class="pdf-wrapper">
    <div class="pdf-title">📑 マニュアル・資料一覧（全22項目）</div>
    <div class="pdf-grid">
        <?php for($i=1; $i<=22; $i++): ?>
        <div class="pdf-item" onclick="alert('項目<?php echo $i; ?>のPDFを開く処理（リンク）を追加します')">
            <img src="./dummy-image.jpg" alt="項目<?php echo $i; ?>" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22[http://www.w3.org/2000/svg%22](http://www.w3.org/2000/svg%22) width=%22150%22 height=%22200%22 viewBox=%220 0 150 200%22%3E%3Crect width=%22150%22 height=%22200%22 fill=%22%23333%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 fill=%22%23777%22 font-size=%2214%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3EIMAGE%3C/text%3E%3C/svg%3E'">
            <span>項目<?php echo $i; ?></span>
        </div>
        <?php endfor; ?>
    </div>
</div>

<script>
const allQuizData = {
    part1: {
        title: "項目1〜6：送迎・返却・清掃・電話対応",
        questions: [
            {
                num: "Q1",
                q: "第1ターミナルの送迎で「4階 C1出口」を案内する最大の理由は何ですか？",
                choices: ["1階の到着ロビーが工事中のため", "空港警察の駐車取り締まりが厳しいため", "4階の方がエレベーターに近いため"],
                ans: "空港警察の駐車取り締まりが厳しいため",
                exp: "第1ターミナルの1階は厳しく取り締まられているため、4階を案内します。"
            },
            {
                num: "Q2",
                q: "洗車プランのお客様が、返却時に清掃が間に合わなかった場合に徴収する清掃代はいくらですか？",
                choices: ["1,100円", "3,300円", "5,500円"],
                ans: "5,500円",
                exp: "返却時に清掃が間に合わなかった場合は、特別清掃代（5,500円）が徴収されます。"
            },
            {
                num: "Q3",
                q: "返却時のルームクリーニングで、忘れ物「第1位」として特に注意すべきものは何ですか？",
                choices: ["スマートフォンの充電器", "ETCカード", "傘や飲み物"],
                ans: "ETCカード",
                exp: "お客様が抜き忘れるケースで最も多いのがETCカードです。確実なチェックが必要です。"
            },
            {
                num: "Q4",
                q: "貸出前のエアコン点検において、正常と判断する温度の基準（証拠記録）は何度以下ですか？",
                choices: ["15度以下", "12度以下", "8度以下"],
                ans: "8度以下",
                exp: "エアコンの吹き出し口の温度は8度以下を正常とし、証拠写真として記録します。"
            },
            {
                num: "Q5",
                q: "バッテリーチェッカーでの診断後、結果の印刷（レシート出力）は何枚必要ですか？",
                choices: ["1枚", "2枚", "3枚"],
                ans: "2枚",
                exp: "バッテリー診断のレシートは、店舗保管用とお客様お渡し用の2枚出力が必要です。"
            },
            {
                num: "Q6",
                q: "最新のルールにおける、電話での予約・問い合わせ対応の最終時間は何時ですか？",
                choices: ["20:00", "20:45", "21:00"],
                ans: "20:00",
                exp: "最新のルールでは、電話対応の最終時間は20:00となっています。"
            }
        ]
    },
    
    part2: {
        title: "項目7〜8：車輛傷チェック・免許証情報",
        questions: [
            {
                num: "Q1",
                q: "複数人で車両点検を行う際、2人目以降の点検者が持つべき心構えとして正しいものはどれですか？",
                choices: ["1人目の点検内容を信じ、素早く確認する。", "1人目の点検内容を疑い、自分の目で再度厳格に確認する。", "1人目が見た箇所は省き、それ以外の箇所の点検に集中する。"],
                ans: "1人目の点検内容を疑い、自分の目で再度厳格に確認する。",
                exp: "「2人目は1人目を疑う」ことで、見落としによるトラブルを防ぎ、2人の目で「絶対に問題ない」ことを確認します。"
            },
            {
                num: "Q2",
                q: "返却時に、貸出前にはなかった傷が増えていることが判明した場合の原則的な対応はどれですか？",
                choices: ["その場でお客様に指摘し、規定のNOC（休業補償）を徴収する。", "後日状況を調査してからお客様に請求メールを送る。", "小さな傷であれば「お互い様」ということでそのまま返車完了とする。"],
                ans: "その場でお客様に指摘し、規定のNOC（休業補償）を徴収する。",
                exp: "基本的にはその場で料金徴収を行い、事故報告書の記載を依頼します。判断に迷う微妙な傷の場合は営業所に相談します。"
            },
            {
                num: "Q3",
                q: "フロントガラスの傷や飛び石を確認する際、最も推奨される判別方法はどれですか？",
                choices: ["ライトを当てて様々な角度から目視だけで確認する。", "霧吹きで水をかけて傷の跡が浮き出るか確認する。", "爪で表面をこすったり、叩いたりして引っ掛かりがないか確認する。"],
                ans: "爪で表面をこすったり、叩いたりして引っ掛かりがないか確認する。",
                exp: "目視だけでなく、爪を使って触覚で確認することで、小さなリペア跡や傷の見落としを防ぎます。"
            },
            {
                num: "Q4",
                q: "日本で運転可能な国際免許証の形式は、どの条約に基づくものだけですか？",
                choices: ["1949年ジュネーブ条約", "1968年ウィーン条約", "1971年パリ条約"],
                ans: "1949年ジュネーブ条約",
                exp: "ウィーン条約締結の国際免許証などは日本では使用できず、1949年形式のものか厳格に確認する必要があります。"
            },
            {
                num: "Q5",
                q: "国際免許証の有効期限をESシステムに入力する際、正しい計算方法はどれですか？",
                choices: ["免許の効力発生日から「丸1年（365日後）」", "免許の効力発生日（ISSUED ATの日付）から「1年後マイナス1日」", "入国スタンプの日付から「1年間」"],
                ans: "免許の効力発生日（ISSUED ATの日付）から「1年後マイナス1日」",
                exp: "例えば2025年3月18日発行であれば、2026年3月17日までが有効期間となります。"
            },
            {
                num: "Q6",
                q: "10人乗りのハイエース等を国際免許証で運転するために、免許証内のスタンプが必須な箇所はどこですか？",
                choices: ["「B」欄", "「C」欄", "「D」欄"],
                ans: "「D」欄",
                exp: "9人乗り以下の乗用車はBライセンスで可能ですが、10人乗り以上の車両にはDライセンスのスタンプ、または台湾免許等の場合は「大貨車」の記載が必須です。"
            },
            {
                num: "Q7",
                q: "副運転者がいる場合、ESシステムへのデータ登録やスキャンのルールとして正しいものはどれですか？",
                choices: ["代表者（主運転者）1名分のコピーだけ保管すればよい。", "運転する全員分の免許証コピーを提出してもらい、スキャンデータをまとめてシステムに保管する。", "副運転者は名前だけ口頭確認してシステムにメモしておけばよい。"],
                ans: "運転する全員分の免許証コピーを提出してもらい、スキャンデータをまとめてシステムに保管する。",
                exp: "登録されていない方の運転による事故は「保険対象外」となるため、全員分の情報を正しく保管し、判別できるようにしておく必要があります。"
            }
        ]
    },
    
    part3: {
        title: "項目9〜16：受付・レジ締め・スケジュール・電話対応",
        questions: [
            {
                num: "Q1",
                q: "貸出受付時、お客様に署名をいただいた「規約同意書」の正しい取り扱いはどれですか？",
                choices: ["店舗で原本をファイルに長期保管する。", "署名後の書類を写真撮影し、原本はお客様に渡す。", "原本を本部へ郵送する。"],
                ans: "署名後の書類を写真撮影し、原本はお客様に渡す。",
                exp: "ハイエース等の特定車両も同様に、証拠として写真を残した上で原本をお客様へお渡しします。"
            },
            {
                num: "Q2",
                q: "万が一の事故やトラブルの際、お客様に連絡してもらう「3箇所」はどこですか？",
                choices: ["警察（110番）、JAF、当店", "警察（110番）、保険会社（0120-256-110）、当店", "保険会社、旅行代理店、当店"],
                ans: "警察（110番）、保険会社（0120-256-110）、当店",
                exp: "保険会社の番号は事故用（末尾110）とトラブル用（末尾110）があり、これら3箇所への連絡が欠けると保険が適用されない場合があります。"
            },
            {
                num: "Q3",
                q: "請求書を発行する際、支払方法（現金・カード等）はシステムのどこに記載しますか？",
                choices: ["顧客名の横のメモ欄", "精算金額の横の項目", "詳細の下にある空白（備考欄）"],
                ans: "詳細の下にある空白（備考欄）",
                exp: "「精算開始」後に表示される詳細の下部の空白に、支払方法を明記する必要があります。"
            },
            {
                num: "Q4",
                q: "車両が返却された際、ESシステムで最初に行う「返車処理」で入力すべき項目は何ですか？",
                choices: ["実際の返却日時と、点検表に記載された返却時の走行距離", "ガソリンの残量と内外装の傷の有無", "お客様のサインと担当者名"],
                ans: "実際の返却日時と、点検表に記載された返却時の走行距離",
                exp: "ナンバーを確認して「返車開始」を押し、点検表の数値を正確に転記します。"
            },
            {
                num: "Q5",
                q: "遅番のレジ締め作業において、レジ内に残しておく「釣銭」の金額はいくらですか？",
                choices: ["5万円", "10万円", "15万円"],
                ans: "10万円",
                exp: "現金売上分は封筒に入れて保管し、翌日のために釣銭10万円を準備してチェック表に記入します。"
            },
            {
                num: "Q6",
                q: "翌々日分の「発着票（スケジュール表）」を作成する際、ESシステムのTODO検索で設定すべき時間は何時ですか？",
                choices: ["21時", "22時", "23時"],
                ans: "23時",
                exp: "2日先の予定までを正確に抽出するため、日付を2日先に設定し、時間は「23:00」で検索します。"
            },
            {
                num: "Q7",
                q: "スケジュール表（Excel）の作成において、返却予定の行に適用する文字色のルールはどれですか？",
                choices: ["青色の文字にする", "緑色の文字にする", "赤色の文字にする"],
                ans: "赤色の文字にする",
                exp: "貸出と返却を視覚的に区別するため、返却は必ず赤文字で入力します。"
            },
            {
                num: "Q8",
                q: "第1ターミナルへ到着されたお客様への送迎案内として、正しいフロアと出口の組み合わせはどれですか？",
                choices: ["到着階1階のC1（中央口）", "出発階4階のC1（中央口）", "到着階1階の南口"],
                ans: "出発階4階のC1（中央口）",
                exp: "1階にもC1出口があるため、必ず「4階の出発階」であることを強調して案内します。"
            },
            {
                num: "Q9",
                q: "最新の業務ルールにおける、電話での予約・問い合わせ対応の「最終受付時間」は何時ですか？",
                choices: ["20時00分", "20時45分", "21時00分"],
                ans: "20時45分",
                exp: "店舗は22時（電話対応21時）まで営業していますが、最終の電話受付は20:45までとなっています。"
            },
            {
                num: "Q10",
                q: "出発まで24時間を切っている予約の問い合わせを受けた際、受付可能な条件（正直な理由）は何ですか？",
                choices: ["予約が空いていれば誰でも受付可能", "総額15,000円以上、長期、または閑散期であること", "リピーターのお客様であること"],
                ans: "総額15,000円以上、長期、または閑散期であること",
                exp: "大手と異なり車両準備に時間を要するため、基本は24時間前までですが、条件を満たせば受付可能です。"
            }
        ]
    },
    
    part4: {
        title: "項目18〜22：予約入力・管理・車検スケジュール",
        questions: [
            {
                num: "Q1",
                q: "エクセルで作成する「予定表（発着票）」において、-レンタカー経由の予約は「入庫」欄にどのように記載しますか？",
                choices: ["「レンタカー」と入力する", "「-」と入力する", "空欄のままにする"],
                ans: "「-」と入力する",
                exp: "-レンタカー経由の場合は「レンタカー」、-レンタカー経由の場合は「-」と入力して区別します。"
            },
            {
                num: "Q2",
                q: "楽天トラベルの管理画面（RAX）で予約を確認する際、新しい予約が入っているか一目で判断する基準は何ですか？",
                choices: ["「未確認が〇件あります」というコメントが出ているか確認する。", "メールボックスの新着アラートを見る。", "カレンダーが赤く光っているかを見る。"],
                ans: "「未確認が〇件あります」というコメントが出ているか確認する。",
                exp: "未確認の予約がある場合は通知が出るため、速やかに詳細を確認して「予約確定」ボタンを押す必要があります。"
            },
            {
                num: "Q3",
                q: "飛行機を利用しないお客様（自車来店など）が予約された際、防犯上の理由から提出を求める書類は何ですか？",
                choices: ["住民票の写し（3ヶ月以内）", "公共料金の支払い書コピー（3ヶ月以内）、またはクレジットカードのコピー", "パスポートのコピー"],
                ans: "公共料金の支払い書コピー（3ヶ月以内）、またはクレジットカードのコピー",
                exp: "飛行機利用のないお客様には、当社規定によりこれらの本人確認書類のいずれかをご提出いただきます。"
            },
            {
                num: "Q4",
                q: "事故の連絡を受けた際、運転者が「貸出時に免許証コピーを提出していない人」だった場合の重大なリスクは何ですか？",
                choices: ["保険の免責額が2倍になる", "保険対象外となり、保険が適用されない", "警察の現場検証に通常より時間がかかる"],
                ans: "保険対象外となり、保険が適用されない",
                exp: "登録されていない方の運転による事故は保険が一切効かないため、必ず土橋店長へ報告が必要です。"
            },
            {
                num: "Q5",
                q: "事故発生時の初動対応として、お客様に案内する「3つの連絡先」のうち、保険会社の事故受付専用ダイヤルはどれですか？",
                choices: ["0120-365-110", "0120-256-110", "0120-110-110"],
                ans: "0120-256-110",
                exp: "事故は末尾110、ロードサービス（事故以外のトラブル）は末尾110（0120-365-110）と使い分けます。"
            },
            {
                num: "Q6",
                q: "ESシステムに整備経費（トヨタからの請求書など）を入力する際、金額入力の必須ルールは何ですか？",
                choices: ["すべて税込み価格でまとめて1行に入力する", "工賃と部品代を必ず別にし、すべて「税別」で入力してから最後に消費税を登録する", "消費税は入力せず本体価格のみを登録する"],
                ans: "工賃と部品代を必ず別にし、すべて「税別」で入力してから最後に消費税を登録する",
                exp: "経費を細かく管理するため、項目を分けて入力し、最終的な請求額と一致させる必要があります。"
            },
            {
                num: "Q7",
                q: "整備入力時、次回の「点検満了日」を設定する際の基準はどうなっていますか？",
                choices: ["車検満了日の「1ヶ月前」を設定する", "車検満了日と同じ日付を設定する", "車検満了日の「前日」を設定し、ステッカーの日付と一致させる"],
                ans: "車検満了日の「前日」を設定し、ステッカーの日付と一致させる",
                exp: "点検満了日はステッカーの記載通りに設定します。もし日付がズレる場合は、備考欄に「点検満了日はステッカー通り」と記載して後で確認できるようにします。"
            },
            {
                num: "Q8",
                q: "整備入力の「備考」欄には、具体的にどのような内容を記載すべきですか？",
                choices: ["「定期点検実施済み」の定型文だけ記載する", "担当整備士の名前と時間を記載する", "交換部品（オイル、エレメント、ワイパーゴム、タイヤ等）を細かく記載する"],
                ans: "交換部品（オイル、エレメント、ワイパーゴム、タイヤ等）を細かく記載する",
                exp: "これらは車両トラブルの元になる重要事項であるため、管理者が把握できるように詳細な履歴を残します。"
            }
        ]
    }
};

let currentCategoryStr = ''; 
let currentCategoryTitle = '';
let activeQuizData = []; 

let currentIndex = 0;
let correctCount = 0;
let resultsData = []; 

const categoryContainer = document.getElementById('category-select-container');
const quizContainer = document.getElementById('quiz-container');
const submitContainer = document.getElementById('submit-container');
const finalResultContainer = document.getElementById('final-result-container');
const progressText = document.getElementById('progress-text');
const chatbotBannerArea = document.getElementById('chatbot-banner-area');

window.startQuiz = function(categoryKey) {
    categoryContainer.style.display = 'none';
    chatbotBannerArea.style.display = 'none';
    
    currentCategoryStr = categoryKey;
    currentCategoryTitle = allQuizData[categoryKey].title;
    activeQuizData = allQuizData[categoryKey].questions;
    
    currentIndex = 0;
    correctCount = 0;
    resultsData = [];
    
    renderQuestion(currentIndex);
};

function renderQuestion(index) {
    progressText.innerText = `${activeQuizData[index].num} / Q${activeQuizData.length}`;
    
    const item = activeQuizData[index];
    
    let choicesHtml = '';
    item.choices.forEach((choice, i) => {
        choicesHtml += `
            <span class="radio-item">
                <input type="radio" name="q_${index}" value="${choice}" id="choice_${index}_${i}">
                <label class="radio-label" for="choice_${index}_${i}">${choice}</label>
            </span>
        `;
    });

    const cardHtml = `
        <div class="quiz-card active" id="card_${index}">
            <div style="font-size: 0.85rem; color: #63b3ed; margin-bottom: 8px;">${currentCategoryTitle}</div>
            <span class="question-title">${item.num}. ${item.q}</span>
            <div id="choices_wrap_${index}">
                ${choicesHtml}
            </div>
            
            <button id="answer_btn_${index}" class="action-btn" onclick="checkAnswer(${index})" disabled>回答する</button>
            
            <div id="exp_area_${index}" class="explanation-area">
                <div id="exp_title_${index}" class="exp-title"></div>
                <div id="exp_ans_text_${index}" class="exp-correct-ans"></div>
                <div class="exp-text">${item.exp}</div>
            </div>
            
            <button id="next_btn_${index}" class="action-btn" style="display:none;" onclick="goNext()">次の問題へ</button>
        </div>
    `;
    
    quizContainer.innerHTML = cardHtml;
    
    const radios = document.querySelectorAll(`input[name="q_${index}"]`);
    const answerBtn = document.getElementById(`answer_btn_${index}`);
    radios.forEach(r => {
        r.addEventListener('change', () => {
            answerBtn.disabled = false;
        });
    });
}

window.checkAnswer = function(index) {
    const item = activeQuizData[index];
    const selected = document.querySelector(`input[name="q_${index}"]:checked`);
    if (!selected) return;
    
    const userAns = selected.value;
    const isCorrect = (userAns === item.ans);
    
    if (isCorrect) correctCount++;
    
    resultsData.push({
        q_num: item.num,
        user_ans: userAns,
        is_correct: isCorrect
    });
    
    const expArea = document.getElementById(`exp_area_${index}`);
    const expTitle = document.getElementById(`exp_title_${index}`);
    const expAnsText = document.getElementById(`exp_ans_text_${index}`);
    
    if (isCorrect) {
        expArea.className = "explanation-area correct";
        expTitle.className = "exp-title correct-title";
        expTitle.innerText = "〇 正解！";
        expAnsText.innerHTML = "あなたの回答: " + userAns;
    } else {
        expArea.className = "explanation-area wrong";
        expTitle.className = "exp-title wrong-title";
        expTitle.innerText = "× 不正解";
        expAnsText.innerHTML = "あなたの回答: " + userAns + "<br><span style='color:#68d391; margin-top:6px; display:inline-block;'>正解: " + item.ans + "</span>";
    }
    
    const radios = document.querySelectorAll(`input[name="q_${index}"]`);
    radios.forEach(r => r.disabled = true);
    
    document.getElementById(`answer_btn_${index}`).style.display = 'none';
    expArea.style.display = 'block';
    
    const nextBtn = document.getElementById(`next_btn_${index}`);
    if (index === activeQuizData.length - 1) {
        nextBtn.innerText = "次に進む";
    }
    nextBtn.style.display = 'block';
};

window.goNext = function() {
    currentIndex++;
    if (currentIndex < activeQuizData.length) {
        renderQuestion(currentIndex);
    } else {
        quizContainer.style.display = 'none';
        progressText.innerText = "全問回答完了！";
        submitContainer.style.display = 'block';
    }
};

document.getElementById('submit-btn').addEventListener('click', function() {
    const phoneInput = document.getElementById('user_phone');
    if (!phoneInput.value) {
        alert("電話番号を入力してください");
        phoneInput.focus();
        return;
    }
    
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="loader"></span> 送信中...';
    
    const formData = new URLSearchParams();
    formData.append('action', 'submit_quiz');
    formData.append('quiz_category', currentCategoryTitle);
    formData.append('user_phone', phoneInput.value);
    formData.append('correct_count', correctCount);
    formData.append('total_questions', activeQuizData.length);
    formData.append('results_data', JSON.stringify(resultsData));

    fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showFinalResult();
        } else {
            alert("エラーが発生しました。");
            btn.disabled = false;
            btn.innerText = "スコアを見る";
        }
    })
    .catch(error => {
        console.error(error);
        showFinalResult();
    });
});

function showFinalResult() {
    document.getElementById('quiz-header').style.display = 'none';
    submitContainer.style.display = 'none';
    
    const passThreshold = Math.ceil(activeQuizData.length * 0.8);
    const isPassed = (correctCount >= passThreshold);
    
    const headObj = document.getElementById('res-heading');
    if (isPassed) {
        finalResultContainer.className = "final-result-card pass";
        headObj.style.color = "#68d391";
        headObj.innerText = "合格";
    } else {
        finalResultContainer.className = "final-result-card failed";
        headObj.style.color = "#fc8181";
        headObj.innerText = "不合格";
    }
    
    document.getElementById('res-score').innerHTML = `${activeQuizData.length}問中 <strong>${correctCount}問</strong> 正解`;
    finalResultContainer.style.display = "block";
}

</script>
</body>
</html>
```
</details>

### Creative (Films)
**Instagram:** [@tk.haru](https://instagram.com/tk.haru)

---
<div align="center">
  <i>Always eager to learn and create new things. Feel free to reach out!</i>
</div>
