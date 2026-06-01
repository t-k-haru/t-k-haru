<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shift OS — 介護・医療施設デモ</title>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XMFZYV50TL"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XMFZYV50TL');</script>
<style>
:root{--bg:#f0f4f8;--surface:#fff;--surface2:#e8eef4;--border:#d4dde8;--text:#1a2332;--text2:#4a6080;--text3:#8099b3;--accent:#1a6b8a;--accent-light:#e6f3f8;--accent2:#c0392b;--accent2-light:#fdf0ee;--green:#27ae60;--green-light:#eafaf1;--orange:#e67e22;--orange-light:#fef5e7;--radius:10px;--shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.06);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:-apple-system,BlinkMacSystemFont,'Helvetica Neue',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
.header{background:var(--surface);border-bottom:1px solid var(--border);padding:0 16px;display:flex;align-items:center;gap:10px;height:52px;position:sticky;top:44px;z-index:100;}
.logo{font-size:15px;font-weight:700;color:var(--accent);white-space:nowrap;}
.logo-sub{color:var(--text3);font-weight:400;font-size:11px;margin-left:6px;}
.badge-industry{background:var(--accent-light);color:var(--accent);font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;letter-spacing:.3px;}
.view-toggle{margin-left:auto;display:flex;gap:4px;background:var(--surface2);border-radius:8px;padding:3px;}
.view-btn{padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;border:none;background:transparent;color:var(--text2);font-family:inherit;transition:all .15s;}
.view-btn.active{background:var(--surface);color:var(--text);box-shadow:0 1px 3px rgba(0,0,0,.1);}
.tabs{background:var(--surface);border-bottom:1px solid var(--border);padding:0 16px;display:flex;overflow-x:auto;scrollbar-width:none;}
.tabs::-webkit-scrollbar{display:none;}
.tab{padding:12px 14px;font-size:13px;font-weight:500;color:var(--text2);border-bottom:2px solid transparent;cursor:pointer;white-space:nowrap;transition:color .15s,border-color .15s;}
.tab.active{color:var(--accent);border-bottom-color:var(--accent);}
.main{max-width:1100px;margin:0 auto;padding:20px 16px;}
.panel{display:none;}.panel.active{display:block;}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;min-width:0;}
.card-header{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;}
.card-title{font-size:13px;font-weight:600;}
.card-body{padding:14px 16px;}
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
@media(max-width:700px){.kpi-grid{grid-template-columns:repeat(2,1fr);}}
.kpi{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;box-shadow:var(--shadow);}
.kpi-label{font-size:10px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;}
.kpi-value{font-size:26px;font-weight:700;line-height:1;}
.kpi-sub{font-size:11px;color:var(--text2);margin-top:4px;}
.kpi.teal .kpi-value{color:var(--accent);}
.kpi.red .kpi-value{color:var(--accent2);}
.kpi.green .kpi-value{color:var(--green);}
.kpi.orange .kpi-value{color:var(--orange);}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;}
@media(max-width:680px){.grid-2,.grid-3{grid-template-columns:1fr;}}
table{width:100%;border-collapse:collapse;font-size:13px;}
th{background:var(--surface2);color:var(--text2);font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;padding:9px 12px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap;}
td{padding:10px 12px;border-bottom:1px solid var(--border);color:var(--text);}
tr:last-child td{border-bottom:none;}
.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;white-space:nowrap;}
.badge-green{background:var(--green-light);color:var(--green);}
.badge-red{background:var(--accent2-light);color:var(--accent2);}
.badge-blue{background:var(--accent-light);color:var(--accent);}
.badge-orange{background:var(--orange-light);color:var(--orange);}
.badge-gray{background:var(--surface2);color:var(--text3);}
.shift-cell{padding:6px 8px;border-radius:6px;font-size:11px;font-weight:600;text-align:center;white-space:nowrap;}
.shift-day{background:#e8f4f8;color:#1a6b8a;}
.shift-night{background:#2c3e50;color:#ecf0f1;}
.shift-late{background:#fef5e7;color:#e67e22;}
.shift-off{background:var(--surface2);color:var(--text3);}
.shift-akemei{background:#f0e6f6;color:#7d3c98;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;font-family:inherit;transition:opacity .15s;}
.btn:hover{opacity:.85;}
.btn-primary{background:var(--accent);color:#fff;}
.btn-outline{background:transparent;color:var(--accent);border:1px solid var(--accent)!important;}
.btn-sm{padding:5px 12px;font-size:11px;}
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.85);color:#fff;padding:10px 20px;border-radius:999px;font-size:12px;z-index:9999;display:none;white-space:nowrap;}
.alert-banner{background:var(--accent2-light);border:1px solid var(--accent2);border-radius:var(--radius);padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;}
.alert-dot{width:8px;height:8px;border-radius:50%;background:var(--accent2);flex-shrink:0;}
.demo-banner {
  position: sticky;
  top: 0;
  z-index: 999;
  background: rgba(10,10,10,.93);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255,255,255,.1);
  font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Neue', sans-serif;
}
.dcb-inner {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 16px;
  height: 44px;
  max-width: 1200px;
  margin: 0 auto;
}
.dcb-back {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  color: rgba(255,255,255,.5);
  text-decoration: none;
  font-size: 12px;
  white-space: nowrap;
  transition: color .15s;
  flex-shrink: 0;
}
.dcb-back:hover { color: #fff; }
.dcb-back { background: none; border: none; padding: 0; font-family: inherit; cursor: pointer; }
.dcb-back-sub { font-size: 11px; opacity: .7; }
@media (max-width: 600px) { .dcb-back-sub { display: none; } }
.dcb-badge {
  background: rgba(255,255,255,.12);
  color: rgba(255,255,255,.55);
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 999px;
  letter-spacing: .5px;
  flex-shrink: 0;
}
.dcb-actions {
  display: flex;
  gap: 8px;
  margin-left: auto;
  align-items: center;
  flex-shrink: 0;
}
.dcb-btn {
  display: inline-flex;
  align-items: center;
  padding: 6px 16px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  transition: background .15s, border-color .15s, color .15s;
}
.dcb-btn-ghost {
  color: rgba(255,255,255,.75);
  border: 1px solid rgba(255,255,255,.25);
  background: transparent;
}
.dcb-btn-ghost:hover { color: #fff; border-color: rgba(255,255,255,.6); }
.dcb-btn-primary { background: #fff; color: #000; border: 1px solid #fff; }
.dcb-btn-primary:hover { background: #ddd; }

/* ===== モバイルレスポンシブ ===== */
@media (max-width: 768px) {
  .header { height: 48px; padding: 0 12px; flex-wrap: wrap; gap: 6px; }
  .logo { font-size: 13px; }
  .view-toggle { padding: 2px; }
  .view-btn { padding: 4px 8px; font-size: 11px; }
  .badge-industry { display: none; }
  .tabs { padding: 0 10px; }
  .tab { padding: 10px 10px; font-size: 12px; }
  .main { padding: 14px 12px; }
  .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 14px; }
  .kpi { padding: 10px 12px; }
  .kpi-value { font-size: 20px; }
  .grid-2, .grid-3 { grid-template-columns: 1fr; }
  .card-header { padding: 10px 12px; }
  .card-body { padding: 10px 12px; }
  table { font-size: 12px; }
  th { padding: 7px 8px; font-size: 9px; }
  td { padding: 8px; }
  .btn { padding: 7px 12px; font-size: 11px; }
  .shift-cell { padding: 4px 6px; font-size: 10px; }
  .demo-banner .dcb-inner { padding: 0 12px; gap: 6px; }
}
@media (max-width: 480px) {
  .kpi-grid { grid-template-columns: 1fr 1fr; }
  .kpi-value { font-size: 18px; }
  .header { position: sticky; top: 44px; }
}
</style>
</head>
<body>
<div class="demo-banner">
  <div class="dcb-inner">
    <button onclick="dcbBack()" class="dcb-back">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2.5"
           stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      <span class="dcb-back-label">戻る<span class="dcb-back-sub">　無料相談で実際にお試しいただけます</span></span>
    </button>
    <span class="dcb-badge">デモ版</span>
    <div class="dcb-actions">
      <button onclick="slpOpenConsult()" class="dcb-btn dcb-btn-ghost" style="cursor:pointer;">無料試用</button>
      <a href="https://buy.stripe.com/14A5kC6dc7z77r0etTafS0Z" class="dcb-btn dcb-btn-primary"
         target="_blank" rel="noopener">申し込む</a>
    </div>
  </div>
</div>

<!-- ヘッダー -->
<div class="header">
  <div class="logo">Shift OS <span class="logo-sub">シフト管理システム</span></div>
  <span class="badge-industry">介護・医療施設</span>
  <div class="view-toggle">
    <button class="view-btn active" id="btn-admin" onclick="switchView('admin')">管理者</button>
    <button class="view-btn" id="btn-emp" onclick="switchView('emp')">スタッフ</button>
  </div>
</div>

<!-- 管理者タブ -->
<nav class="tabs admin-only" id="admin-tabs">
  <div class="tab active" onclick="switchTab('dashboard')">ダッシュボード</div>
  <div class="tab" onclick="switchTab('shift')">シフト管理</div>
  <div class="tab" onclick="switchTab('staff')">スタッフ・資格</div>
  <div class="tab" onclick="switchTab('timecard')">タイムカード</div>
</nav>

<!-- スタッフタブ -->
<nav class="tabs emp-only" id="emp-tabs" style="display:none;">
  <div class="tab active" onclick="switchEmpTab('myhome')">マイページ</div>
  <div class="tab" onclick="switchEmpTab('mytimecard')">タイムカード</div>
</nav>

<div class="main">

<!-- ダッシュボード -->
<div class="panel active" id="panel-dashboard">
  <div class="alert-banner">
    <div class="alert-dot"></div>
    <div style="font-size:12px;"><strong style="color:var(--accent2);">夜勤未配置：6/18(水) 22:00〜</strong> — スタッフが1名不足しています。</div>
    <button class="btn btn-sm btn-outline" onclick="toast('シフト調整画面を開きます')">対応する</button>
  </div>
  <div class="kpi-grid">
    <div class="kpi teal"><div class="kpi-label">今月の確定シフト</div><div class="kpi-value">148</div><div class="kpi-sub">うち夜勤 42件</div></div>
    <div class="kpi red"><div class="kpi-label">未配置（要対応）</div><div class="kpi-value">3</div><div class="kpi-sub">コマ数</div></div>
    <div class="kpi green"><div class="kpi-label">今月人件費</div><div class="kpi-value" style="font-size:20px;">¥1,240k</div><div class="kpi-sub">夜勤手当込み</div></div>
    <div class="kpi orange"><div class="kpi-label">稼働スタッフ</div><div class="kpi-value">12</div><div class="kpi-sub">名 / 3フロア</div></div>
  </div>
  <div class="grid-2" style="margin-bottom:16px;">
    <div class="card">
      <div class="card-header"><span class="card-title">今週のシフト（3交代制）</span><button class="btn btn-sm btn-outline" onclick="toast('PDFを出力します')">PDF出力</button></div>
      <div style="overflow-x:auto;">
        <table>
          <thead><tr><th>氏名</th><th>資格</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th><th>日</th></tr></thead>
          <tbody>
            <tr><td><strong>田中 美穂</strong></td><td><span class="badge badge-blue">介護福祉士</span></td><td><span class="shift-cell shift-day">日勤</span></td><td><span class="shift-cell shift-day">日勤</span></td><td><span class="shift-cell shift-off">休み</span></td><td><span class="shift-cell shift-night">夜勤</span></td><td><span class="shift-cell shift-akemei">明け</span></td><td><span class="shift-cell shift-off">休み</span></td><td><span class="shift-cell shift-day">日勤</span></td></tr>
            <tr><td><strong>鈴木 健一</strong></td><td><span class="badge badge-green">看護師</span></td><td><span class="shift-cell shift-night">夜勤</span></td><td><span class="shift-cell shift-akemei">明け</span></td><td><span class="shift-cell shift-off">休み</span></td><td><span class="shift-cell shift-day">日勤</span></td><td><span class="shift-cell shift-day">日勤</span></td><td><span class="shift-cell shift-late">遅番</span></td><td><span class="shift-cell shift-off">休み</span></td></tr>
            <tr><td><strong>佐藤 恵子</strong></td><td><span class="badge badge-blue">介護福祉士</span></td><td><span class="shift-cell shift-off">休み</span></td><td><span class="shift-cell shift-day">日勤</span></td><td><span class="shift-cell shift-day">日勤</span></td><td><span class="shift-cell shift-off">休み</span></td><td><span class="shift-cell shift-night">夜勤</span></td><td><span class="shift-cell shift-akemei">明け</span></td><td><span class="shift-cell shift-off">休み</span></td></tr>
            <tr><td><strong>山田 翔太</strong></td><td><span class="badge badge-orange">ヘルパー2級</span></td><td><span class="shift-cell shift-late">遅番</span></td><td><span class="shift-cell shift-off">休み</span></td><td><span class="shift-cell shift-day">日勤</span></td><td><span class="shift-cell shift-day">日勤</span></td><td><span class="shift-cell shift-off">休み</span></td><td><span class="shift-cell shift-night" style="background:#c0392b;color:#fff;">夜勤⚠</span></td><td><span class="shift-cell shift-akemei">明け</span></td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">フロア別配置状況</span></div>
      <div class="card-body">
        <div style="margin-bottom:12px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;"><span style="font-size:12px;font-weight:600;">A棟（認知症専門）</span><span class="badge badge-green">適正</span></div>
          <div style="background:var(--surface2);border-radius:4px;height:8px;"><div style="background:var(--green);height:100%;border-radius:4px;width:90%;"></div></div>
          <div style="font-size:10px;color:var(--text3);margin-top:2px;">必要3名 / 配置3名</div>
        </div>
        <div style="margin-bottom:12px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;"><span style="font-size:12px;font-weight:600;">B棟（一般介護）</span><span class="badge badge-orange">注意</span></div>
          <div style="background:var(--surface2);border-radius:4px;height:8px;"><div style="background:var(--orange);height:100%;border-radius:4px;width:67%;"></div></div>
          <div style="font-size:10px;color:var(--text3);margin-top:2px;">必要3名 / 配置2名</div>
        </div>
        <div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;"><span style="font-size:12px;font-weight:600;">C棟（リハビリ）</span><span class="badge badge-green">適正</span></div>
          <div style="background:var(--surface2);border-radius:4px;height:8px;"><div style="background:var(--green);height:100%;border-radius:4px;width:100%;"></div></div>
          <div style="font-size:10px;color:var(--text3);margin-top:2px;">必要2名 / 配置2名</div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">夜勤回数・労働時間サマリ（今月）</span></div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>氏名</th><th>夜勤回数</th><th>夜勤手当</th><th>総勤務時間</th><th>残業時間</th><th>有給残</th><th>状態</th></tr></thead>
        <tbody>
          <tr><td><strong>田中 美穂</strong></td><td>4回</td><td>¥16,000</td><td>168h</td><td>8h</td><td>10日</td><td><span class="badge badge-green">適正</span></td></tr>
          <tr><td><strong>鈴木 健一</strong></td><td>5回</td><td>¥20,000</td><td>185h</td><td>25h</td><td>5日</td><td><span class="badge badge-orange">残業多</span></td></tr>
          <tr><td><strong>佐藤 恵子</strong></td><td>3回</td><td>¥12,000</td><td>152h</td><td>0h</td><td>12日</td><td><span class="badge badge-green">適正</span></td></tr>
          <tr><td><strong>山田 翔太</strong></td><td>4回</td><td>¥16,000</td><td>160h</td><td>0h</td><td>8日</td><td><span class="badge badge-green">適正</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- シフト管理 -->
<div class="panel" id="panel-shift">
  <div class="card">
    <div class="card-header"><span class="card-title">夜勤シフト設定</span><button class="btn btn-sm btn-primary" onclick="toast('自動配置を実行しました')">AI自動配置</button></div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px;">
        <div style="background:var(--accent-light);border:1px solid var(--accent);border-radius:8px;padding:12px;text-align:center;"><div style="font-size:20px;font-weight:700;color:var(--accent);">7:00〜17:00</div><div style="font-size:11px;color:var(--text2);margin-top:4px;">日勤 / 8時間</div></div>
        <div style="background:#f0e6f6;border:1px solid #7d3c98;border-radius:8px;padding:12px;text-align:center;"><div style="font-size:20px;font-weight:700;color:#7d3c98;">16:00〜翌9:00</div><div style="font-size:11px;color:var(--text2);margin-top:4px;">夜勤 / 16時間</div></div>
        <div style="background:var(--orange-light);border:1px solid var(--orange);border-radius:8px;padding:12px;text-align:center;"><div style="font-size:20px;font-weight:700;color:var(--orange);">13:00〜22:00</div><div style="font-size:11px;color:var(--text2);margin-top:4px;">遅番 / 8時間</div></div>
      </div>
      <div style="font-size:12px;color:var(--text2);background:var(--surface2);border-radius:8px;padding:12px;">
        <strong style="color:var(--text);">法令対応：</strong>夜勤明け翌日の連続勤務を自動ブロック。月8回以上の夜勤でアラート。介護報酬に基づく最低人員基準を常時チェックします。
      </div>
    </div>
  </div>
</div>

<!-- スタッフ・資格 -->
<div class="panel" id="panel-staff">
  <div class="card">
    <div class="card-header"><span class="card-title">スタッフ資格管理</span><button class="btn btn-sm btn-primary" onclick="toast('スタッフを追加しました')">＋ 追加</button></div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>氏名</th><th>資格</th><th>雇用形態</th><th>時給</th><th>夜勤可否</th><th>担当フロア</th><th>資格更新</th></tr></thead>
        <tbody>
          <tr><td><strong>田中 美穂</strong></td><td><span class="badge badge-blue">介護福祉士</span></td><td>正社員</td><td>¥1,450</td><td><span class="badge badge-green">可</span></td><td>A・B棟</td><td>2027/3</td></tr>
          <tr><td><strong>鈴木 健一</strong></td><td><span class="badge badge-green">看護師</span></td><td>正社員</td><td>¥1,800</td><td><span class="badge badge-green">可</span></td><td>全棟</td><td>—</td></tr>
          <tr><td><strong>佐藤 恵子</strong></td><td><span class="badge badge-blue">介護福祉士</span></td><td>パート</td><td>¥1,350</td><td><span class="badge badge-orange">月2回まで</span></td><td>B・C棟</td><td>2026/9</td></tr>
          <tr><td><strong>山田 翔太</strong></td><td><span class="badge badge-orange">ヘルパー2級</span></td><td>パート</td><td>¥1,100</td><td><span class="badge badge-green">可</span></td><td>C棟</td><td>—</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- タイムカード -->
<div class="panel" id="panel-timecard">
  <div class="grid-2">
    <div class="card">
      <div class="card-header"><span class="card-title">本日の出退勤</span></div>
      <table>
        <thead><tr><th>氏名</th><th>出勤</th><th>休憩</th><th>退勤</th><th>実働</th><th>状態</th></tr></thead>
        <tbody>
          <tr><td><strong>田中 美穂</strong></td><td>07:02</td><td>60分</td><td>—</td><td>勤務中</td><td><span class="badge badge-green">勤務中</span></td></tr>
          <tr><td><strong>鈴木 健一</strong></td><td>前日16:00</td><td>120分</td><td>09:05</td><td>15h5m</td><td><span class="badge badge-blue">夜勤明け</span></td></tr>
          <tr><td><strong>佐藤 恵子</strong></td><td>—</td><td>—</td><td>—</td><td>—</td><td><span class="badge badge-gray">未出勤</span></td></tr>
        </tbody>
      </table>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">修正申請（承認待ち）</span></div>
      <div class="card-body" id="correction-list">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
          <div><div style="font-size:12px;font-weight:600;">山田 翔太</div><div style="font-size:11px;color:var(--text3);">6/15 退勤 21:45 → 22:00に修正</div></div>
          <div style="display:flex;gap:6px;"><button class="btn btn-sm btn-primary" onclick="approveCorrection(this)">承認</button><button class="btn btn-sm btn-outline" onclick="this.closest('div').parentNode.remove()">却下</button></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- スタッフ：マイページ -->
<div class="panel" id="panel-myhome" style="display:none;">
  <div class="grid-2">
    <div class="card card-body">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;"><div style="width:44px;height:44px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;">田</div><div><div style="font-size:14px;font-weight:700;">田中 美穂</div><div style="font-size:11px;color:var(--text3);margin-top:2px;">介護福祉士 / A・B棟</div></div></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div style="background:var(--surface2);border-radius:8px;padding:10px;text-align:center;"><div style="font-size:20px;font-weight:700;color:var(--accent);">4</div><div style="font-size:10px;color:var(--text3);">今月夜勤</div></div>
        <div style="background:var(--surface2);border-radius:8px;padding:10px;text-align:center;"><div style="font-size:20px;font-weight:700;color:var(--green);">10</div><div style="font-size:10px;color:var(--text3);">有給残日数</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">今週のシフト</span></div>
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);"><span style="font-size:12px;">月</span><span class="shift-cell shift-day">日勤 07:00〜17:00</span></div>
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);"><span style="font-size:12px;">火</span><span class="shift-cell shift-day">日勤 07:00〜17:00</span></div>
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);"><span style="font-size:12px;">水</span><span class="shift-cell shift-off">休み</span></div>
        <div style="display:flex;justify-content:space-between;padding:7px 0;"><span style="font-size:12px;">木</span><span class="shift-cell shift-night">夜勤 16:00〜翌9:00</span></div>
      </div>
    </div>
  </div>
</div>



<!-- スタッフ：タイムカード -->
<div class="panel" id="panel-mytimecard" style="display:none;">
  <div class="grid-2">
    <div class="card card-body" style="text-align:center;">
      <div style="font-size:32px;font-weight:300;letter-spacing:-1px;margin-bottom:6px;" id="clock-now">--:--:--</div>
      <div id="clock-status" style="display:inline-block;background:var(--surface2);color:var(--text3);font-size:11px;font-weight:600;padding:4px 16px;border-radius:999px;margin-bottom:16px;">勤務外</div>
      <div id="clock-elapsed" style="font-size:12px;color:var(--text3);margin-bottom:16px;min-height:18px;"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
        <button onclick="clockIn()" id="btn-in" class="btn btn-primary" style="justify-content:center;">出勤</button>
        <button onclick="clockOut()" id="btn-out" class="btn btn-outline" disabled style="justify-content:center;opacity:.4;">退勤</button>
      </div>
      <button onclick="clockBreak()" id="btn-break" class="btn" style="width:100%;justify-content:center;background:var(--orange-light);color:var(--orange);border:1px solid var(--orange)!important;opacity:.4;" disabled>休憩</button>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">打刻履歴</span></div>
      <div id="tc-log" style="padding:12px 16px;font-size:12px;color:var(--text2);"></div>
    </div>
  </div>
</div>

</div><!-- main -->
<div class="toast" id="toast"></div>

<script>
var currentView='admin';
var tcState={in:false,inTime:null,breaking:false,log:['6/14 出勤07:02 休憩60分 退勤17:08 実働9h06m','6/12 出勤07:00 夜勤 退勤翌09:05 実働15h05m']};

function switchView(v){
  currentView=v;
  document.getElementById('btn-admin').className='view-btn'+(v==='admin'?' active':'');
  document.getElementById('btn-emp').className='view-btn'+(v==='emp'?' active':'');
  document.getElementById('admin-tabs').style.display=v==='admin'?'flex':'none';
  document.getElementById('emp-tabs').style.display=v==='emp'?'flex':'none';
  // パネル全リセット
  ['dashboard','shift','staff','timecard'].forEach(function(x){var p=document.getElementById('panel-'+x);if(p){p.classList.remove('active');p.style.display='none';}});
  ['myhome','mytimecard'].forEach(function(x){var p=document.getElementById('panel-'+x);if(p){p.style.display='none';}});
  document.querySelectorAll('.admin-only').forEach(el=>el.style.display=v==='admin'?'':'none');
  document.querySelectorAll('.emp-only').forEach(el=>el.style.display=v==='emp'?'':'none');
  if(v==='admin') switchTab('dashboard');
  else switchEmpTab('myhome');
}
function switchTab(t){
  document.querySelectorAll('#admin-tabs .tab').forEach((el,i)=>{el.classList.toggle('active',['dashboard','shift','staff','timecard'][i]===t);});
  ['dashboard','shift','staff','timecard'].forEach(x=>{var p=document.getElementById('panel-'+x);if(p){p.classList.toggle('active',x===t);p.style.display=x===t?'block':'none';}});
  if(t==='mytimecard') startClock();
}
function switchEmpTab(t){
  document.querySelectorAll('#emp-tabs .tab').forEach((el,i)=>{el.classList.toggle('active',['myhome','mytimecard'][i]===t);});
  ['myhome','mytimecard'].forEach(function(x){var p=document.getElementById('panel-'+x);if(p){p.style.display=x===t?'block':'none';}});
  if(t==='mytimecard') startClock();
}
function selectShiftType(btn,type){

  btn.style.background='var(--accent)';btn.style.color='#fff';
}
function approveCorrection(btn){btn.closest('div').parentNode.remove();toast('修正申請を承認しました');}
function clockIn(){tcState.in=true;tcState.inTime=new Date();document.getElementById('clock-status').textContent='勤務中';document.getElementById('clock-status').style.background='var(--green-light)';document.getElementById('clock-status').style.color='var(--green)';document.getElementById('btn-in').disabled=true;document.getElementById('btn-in').style.opacity='.4';document.getElementById('btn-out').disabled=false;document.getElementById('btn-out').style.opacity='1';document.getElementById('btn-break').disabled=false;document.getElementById('btn-break').style.opacity='1';toast('出勤打刻しました');}
function clockOut(){tcState.in=false;var now=new Date();var diff=Math.round((now-tcState.inTime)/60000);tcState.log.unshift((now.getMonth()+1)+'/'+(now.getDate())+' 出勤'+('0'+tcState.inTime.getHours()).slice(-2)+':'+('0'+tcState.inTime.getMinutes()).slice(-2)+' 退勤'+('0'+now.getHours()).slice(-2)+':'+('0'+now.getMinutes()).slice(-2)+' 実働'+Math.floor(diff/60)+'h'+('0'+(diff%60)).slice(-2)+'m');renderTcLog();document.getElementById('clock-status').textContent='勤務外';document.getElementById('clock-status').style.background='var(--surface2)';document.getElementById('clock-status').style.color='var(--text3)';document.getElementById('btn-in').disabled=false;document.getElementById('btn-in').style.opacity='1';document.getElementById('btn-out').disabled=true;document.getElementById('btn-out').style.opacity='.4';document.getElementById('btn-break').disabled=true;document.getElementById('btn-break').style.opacity='.4';document.getElementById('clock-elapsed').textContent='';toast('退勤打刻しました');}
function clockBreak(){var btn=document.getElementById('btn-break');if(btn.textContent==='休憩'){btn.textContent='休憩終了';btn.style.background='var(--orange)';btn.style.color='#fff';document.getElementById('clock-status').textContent='休憩中';}else{btn.textContent='休憩';btn.style.background='var(--orange-light)';btn.style.color='var(--orange)';document.getElementById('clock-status').textContent='勤務中';}toast(btn.textContent==='休憩'?'休憩を終了しました':'休憩を開始しました');}
var clockInterval=null;
function startClock(){if(clockInterval)return;clockInterval=setInterval(function(){var now=new Date();document.getElementById('clock-now').textContent=('0'+now.getHours()).slice(-2)+':'+('0'+now.getMinutes()).slice(-2)+':'+('0'+now.getSeconds()).slice(-2);if(tcState.in&&tcState.inTime){var diff=Math.floor((now-tcState.inTime)/1000);document.getElementById('clock-elapsed').textContent='経過 '+Math.floor(diff/3600)+'h'+('0'+Math.floor((diff%3600)/60)).slice(-2)+'m'+('0'+(diff%60)).slice(-2)+'s';}},1000);}
function renderTcLog(){var el=document.getElementById('tc-log');if(el)el.innerHTML=tcState.log.map(l=>'<div style="padding:5px 0;border-bottom:1px solid var(--border);">'+l+'</div>').join('');}
function toast(msg){var el=document.getElementById('toast');el.textContent=msg;el.style.display='block';setTimeout(()=>el.style.display='none',2500);}
renderTcLog();
</script>
<script>function dcbBack(){ window.location.href='https://shift.nobushi.jp/'; }</script>

<!-- お問い合せモーダル -->
<div id="slp-consult-modal" style="font-family:-apple-system,BlinkMacSystemFont,'Helvetica Neue',sans-serif;display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,.75);padding:20px;"><style>#slp-consult-modal input,#slp-consult-modal select,#slp-consult-modal textarea,#slp-consult-modal button,#slp-consult-modal label{font-family:-apple-system,BlinkMacSystemFont,'Helvetica Neue',sans-serif!important;}</style>
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
<script>
document.getElementById('slp-consult-modal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});
function slpOpenConsult() {
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
  fetch('https://shift.nobushi.jp/', { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      if (d.ok) {
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

</body>
</html>
