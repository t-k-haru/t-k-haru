<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shift OS — 美容・サロンデモ</title>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XMFZYV50TL"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XMFZYV50TL');</script>
<style>
:root{--bg:#fdf8f5;--surface:#fff;--surface2:#f5ede8;--border:#e8ddd6;--text:#1f1310;--text2:#6b5040;--text3:#b09880;--accent:#8b4513;--accent-light:#fdf0e8;--accent2:#c0392b;--green:#27ae60;--green-light:#eafaf1;--orange:#e67e22;--radius:10px;--shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.05);}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:-apple-system,BlinkMacSystemFont,'Helvetica Neue',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
.header{background:var(--surface);border-bottom:1px solid var(--border);padding:0 16px;display:flex;align-items:center;gap:10px;height:52px;position:sticky;top:44px;z-index:100;}
.logo{font-size:15px;font-weight:700;color:var(--accent);}
.logo-sub{color:var(--text3);font-weight:400;font-size:11px;margin-left:6px;}
.badge-industry{background:var(--accent-light);color:var(--accent);font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;}
.view-toggle{margin-left:auto;display:flex;gap:4px;background:var(--surface2);border-radius:8px;padding:3px;}
.view-btn{padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;border:none;background:transparent;color:var(--text2);font-family:inherit;}
.view-btn.active{background:var(--surface);color:var(--text);box-shadow:0 1px 3px rgba(0,0,0,.1);}
.tabs{background:var(--surface);border-bottom:1px solid var(--border);padding:0 16px;display:flex;overflow-x:auto;scrollbar-width:none;}
.tabs::-webkit-scrollbar{display:none;}
.tab{padding:12px 14px;font-size:13px;font-weight:500;color:var(--text2);border-bottom:2px solid transparent;cursor:pointer;white-space:nowrap;}
.tab.active{color:var(--accent);border-bottom-color:var(--accent);}
.main{max-width:1100px;margin:0 auto;padding:20px 16px;}
.panel{display:none;}.panel.active{display:block;}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.card-header{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px;}
.card-title{font-size:13px;font-weight:600;}
.card-body{padding:14px 16px;}
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
@media(max-width:700px){.kpi-grid{grid-template-columns:repeat(2,1fr);}}
.kpi{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;box-shadow:var(--shadow);}
.kpi-label{font-size:10px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;}
.kpi-value{font-size:26px;font-weight:700;line-height:1;}
.kpi-sub{font-size:11px;color:var(--text2);margin-top:4px;}
.kpi.brown .kpi-value{color:var(--accent);}
.kpi.green .kpi-value{color:var(--green);}
.kpi.orange .kpi-value{color:var(--orange);}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
@media(max-width:680px){.grid-2{grid-template-columns:1fr;}}
table{width:100%;border-collapse:collapse;font-size:13px;}
th{background:var(--surface2);color:var(--text2);font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;padding:9px 12px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap;}
td{padding:10px 12px;border-bottom:1px solid var(--border);color:var(--text);}
tr:last-child td{border-bottom:none;}
.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;white-space:nowrap;}
.badge-brown{background:var(--accent-light);color:var(--accent);}
.badge-green{background:var(--green-light);color:var(--green);}
.badge-gray{background:var(--surface2);color:var(--text3);}
.badge-gold{background:#fff8e1;color:#f39c12;}
.btn{display:inline-flex;align-items:center;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;font-family:inherit;}
.btn-primary{background:var(--accent);color:#fff;}
.btn-outline{background:transparent;color:var(--accent);border:1px solid var(--accent)!important;}
.btn-sm{padding:5px 12px;font-size:11px;}
.seat-block{display:flex;flex-direction:column;align-items:center;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px;font-size:11px;cursor:pointer;transition:all .15s;}
.seat-block:hover{border-color:var(--accent);}
.seat-block.occupied{background:var(--accent-light);border-color:var(--accent);}
.seat-block.empty{background:#f0fff4;border-color:var(--green);}
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.85);color:#fff;padding:10px 20px;border-radius:999px;font-size:12px;z-index:9999;display:none;white-space:nowrap;}
.stars{color:#f39c12;letter-spacing:1px;}
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
<div class="header">
  <div class="logo">Shift OS <span class="logo-sub">シフト管理システム</span></div>
  <span class="badge-industry">美容・サロン</span>
  <div class="view-toggle">
    <button class="view-btn active" id="btn-admin" onclick="switchView('admin')">オーナー</button>
    <button class="view-btn" id="btn-emp" onclick="switchView('emp')">スタイリスト</button>
  </div>
</div>
<nav class="tabs" id="admin-tabs">
  <div class="tab active" onclick="switchTab('dashboard')">ダッシュボード</div>
  <div class="tab" onclick="switchTab('seats')">席・稼働管理</div>
  <div class="tab" onclick="switchTab('staff')">スタイリスト</div>
  <div class="tab" onclick="switchTab('timecard')">タイムカード</div>
</nav>
<nav class="tabs" id="emp-tabs" style="display:none;">
  <div class="tab active" onclick="switchEmpTab('mypage')">マイページ</div>
  <div class="tab" onclick="switchEmpTab('myshift')">シフト申請</div>
  <div class="tab" onclick="switchEmpTab('mytimecard')">タイムカード</div>
</nav>
<div class="main">

<!-- ダッシュボード -->
<div class="panel active" id="panel-dashboard">
  <div class="kpi-grid">
    <div class="kpi brown"><div class="kpi-label">今月売上</div><div class="kpi-value" style="font-size:20px;">¥2,840k</div><div class="kpi-sub">先月比 +12%</div></div>
    <div class="kpi brown"><div class="kpi-label">席稼働率</div><div class="kpi-value">78%</div><div class="kpi-sub">目標80%</div></div>
    <div class="kpi green"><div class="kpi-label">今月人件費</div><div class="kpi-value" style="font-size:20px;">¥860k</div><div class="kpi-sub">売上比 30.3%</div></div>
    <div class="kpi orange"><div class="kpi-label">指名来客数</div><div class="kpi-value">248</div><div class="kpi-sub">今月累計</div></div>
  </div>
  <div class="grid-2" style="margin-bottom:16px;">
    <div class="card">
      <div class="card-header"><span class="card-title">スタイリスト別 今週シフト</span><button class="btn btn-sm btn-outline" onclick="toast('PDFを出力します')">出力</button></div>
      <div style="overflow-x:auto;">
        <table>
          <thead><tr><th>スタイリスト</th><th>レベル</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th><th>日</th></tr></thead>
          <tbody>
            <tr><td><strong>橋本 彩</strong></td><td><span class="badge badge-gold">トップ</span></td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#f5ede8;font-size:11px;color:#b09880;">休み</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#f5ede8;font-size:11px;color:#b09880;">休み</td></tr>
            <tr><td><strong>中村 葵</strong></td><td><span class="badge badge-brown">上級</span></td><td style="text-align:center;background:#f5ede8;font-size:11px;color:#b09880;">休み</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#f5ede8;font-size:11px;color:#b09880;">休み</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td></tr>
            <tr><td><strong>田村 ひな</strong></td><td><span class="badge badge-gray">アシスタント</span></td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#f5ede8;font-size:11px;color:#b09880;">休み</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#f5ede8;font-size:11px;color:#b09880;">休み</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td><td style="text-align:center;background:#fdf0e8;font-size:11px;font-weight:600;color:#8b4513;">出勤</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">指名ランキング（今月）</span></div>
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid var(--border);">
          <div style="width:24px;height:24px;border-radius:50%;background:#f39c12;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0;">1</div>
          <div style="flex:1;"><div style="font-size:12px;font-weight:600;">橋本 彩</div><div style="font-size:10px;color:var(--text3);">指名 112件</div></div>
          <div style="text-align:right;"><div style="font-size:13px;font-weight:700;color:var(--accent);">¥1,240k</div><div class="stars" style="font-size:10px;">★★★★★</div></div>
        </div>
        <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid var(--border);">
          <div style="width:24px;height:24px;border-radius:50%;background:#95a5a6;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0;">2</div>
          <div style="flex:1;"><div style="font-size:12px;font-weight:600;">中村 葵</div><div style="font-size:10px;color:var(--text3);">指名 86件</div></div>
          <div style="text-align:right;"><div style="font-size:13px;font-weight:700;color:var(--accent);">¥920k</div><div class="stars" style="font-size:10px;">★★★★☆</div></div>
        </div>
        <div style="display:flex;align-items:center;gap:12px;padding:8px 0;">
          <div style="width:24px;height:24px;border-radius:50%;background:#cd7f32;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0;">3</div>
          <div style="flex:1;"><div style="font-size:12px;font-weight:600;">田村 ひな</div><div style="font-size:10px;color:var(--text3);">指名 50件（練習中）</div></div>
          <div style="text-align:right;"><div style="font-size:13px;font-weight:700;color:var(--text2);">¥480k</div><div class="stars" style="font-size:10px;">★★★☆☆</div></div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">今月の人件費 vs 売上</span></div>
    <div class="card-body">
      <div style="display:flex;align-items:flex-end;gap:8px;height:80px;margin-bottom:8px;">
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;opacity:.5;height:42px;"></div><div style="font-size:9px;color:var(--text3);">2月</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;opacity:.65;height:56px;"></div><div style="font-size:9px;color:var(--text3);">3月</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;opacity:.75;height:48px;"></div><div style="font-size:9px;color:var(--text3);">4月</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;height:72px;"></div><div style="font-size:9px;font-weight:600;color:var(--accent);">5月</div></div>
        <div style="width:1px;background:var(--border);height:80px;"></div>
        <div style="font-size:11px;color:var(--text2);padding-bottom:20px;">← 売上: ¥2,840k<br>人件費比: 30.3%</div>
      </div>
    </div>
  </div>
</div>

<!-- 席・稼働管理 -->
<div class="panel" id="panel-seats">
  <div class="card" style="margin-bottom:14px;">
    <div class="card-header"><span class="card-title">本日の席稼働（全6席）</span><span style="font-size:12px;color:var(--text3);">14:30 現在</span></div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:10px;">
        <div class="seat-block occupied" onclick="toast('席1: 橋本 彩 — カット・カラー 14:00〜15:30')"><div style="font-size:18px;margin-bottom:4px;">💺</div><div style="font-weight:700;font-size:11px;">席1</div><div style="font-size:10px;color:var(--accent);margin-top:2px;">橋本 彩</div><div style="font-size:9px;color:var(--text3);">〜15:30</div></div>
        <div class="seat-block empty" onclick="toast('席2: 空席 — クリックして予約を入れる')"><div style="font-size:18px;margin-bottom:4px;">💺</div><div style="font-weight:700;font-size:11px;">席2</div><div style="font-size:10px;color:var(--green);margin-top:2px;">空き</div><div style="font-size:9px;color:var(--text3);">次: 15:00</div></div>
        <div class="seat-block occupied" onclick="toast('席3: 中村 葵 — パーマ 13:30〜16:00')"><div style="font-size:18px;margin-bottom:4px;">💺</div><div style="font-weight:700;font-size:11px;">席3</div><div style="font-size:10px;color:var(--accent);margin-top:2px;">中村 葵</div><div style="font-size:9px;color:var(--text3);">〜16:00</div></div>
        <div class="seat-block empty" onclick="toast('席4: 空席')"><div style="font-size:18px;margin-bottom:4px;">💺</div><div style="font-weight:700;font-size:11px;">席4</div><div style="font-size:10px;color:var(--green);margin-top:2px;">空き</div><div style="font-size:9px;color:var(--text3);">終日可</div></div>
        <div class="seat-block occupied" onclick="toast('席5: 田村 ひな — カット 14:00〜15:00')"><div style="font-size:18px;margin-bottom:4px;">💺</div><div style="font-weight:700;font-size:11px;">席5</div><div style="font-size:10px;color:var(--accent);margin-top:2px;">田村 ひな</div><div style="font-size:9px;color:var(--text3);">〜15:00</div></div>
        <div class="seat-block empty" onclick="toast('席6: シャンプー台 — 空き')"><div style="font-size:18px;margin-bottom:4px;">🚿</div><div style="font-weight:700;font-size:11px;">シャンプー</div><div style="font-size:10px;color:var(--green);margin-top:2px;">空き</div><div style="font-size:9px;color:var(--text3);">—</div></div>
      </div>
      <div style="display:flex;gap:16px;margin-top:12px;font-size:11px;color:var(--text2);">
        <span style="display:flex;align-items:center;gap:6px;"><span style="width:10px;height:10px;border-radius:2px;background:var(--accent-light);border:1px solid var(--accent);display:inline-block;"></span>使用中 (3席)</span>
        <span style="display:flex;align-items:center;gap:6px;"><span style="width:10px;height:10px;border-radius:2px;background:#f0fff4;border:1px solid var(--green);display:inline-block;"></span>空き (3席)</span>
        <strong style="color:var(--accent);margin-left:auto;">稼働率 50%（この時間帯）</strong>
      </div>
    </div>
  </div>
</div>

<!-- スタイリスト -->
<div class="panel" id="panel-staff">
  <div class="card">
    <div class="card-header"><span class="card-title">スタイリスト管理</span><button class="btn btn-sm btn-primary" onclick="toast('スタイリストを追加しました')">＋ 追加</button></div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>氏名</th><th>レベル</th><th>対応技術</th><th>時給</th><th>指名料</th><th>今月指名</th><th>今月売上貢献</th></tr></thead>
        <tbody>
          <tr><td><strong>橋本 彩</strong></td><td><span class="badge badge-gold">トップスタイリスト</span></td><td>カット・カラー・パーマ</td><td>¥1,800</td><td>¥550</td><td>112件</td><td style="font-weight:700;color:var(--accent);">¥1,240k</td></tr>
          <tr><td><strong>中村 葵</strong></td><td><span class="badge badge-brown">スタイリスト</span></td><td>カット・カラー</td><td>¥1,500</td><td>¥330</td><td>86件</td><td style="font-weight:700;color:var(--accent);">¥920k</td></tr>
          <tr><td><strong>田村 ひな</strong></td><td><span class="badge badge-gray">アシスタント</span></td><td>カット（練習中）</td><td>¥1,100</td><td>—</td><td>50件</td><td style="color:var(--text2);">¥480k</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- タイムカード -->
<div class="panel" id="panel-timecard">
  <div class="card">
    <div class="card-header"><span class="card-title">本日の出退勤</span></div>
    <table>
      <thead><tr><th>氏名</th><th>出勤</th><th>休憩</th><th>退勤</th><th>指名件数</th><th>状態</th></tr></thead>
      <tbody>
        <tr><td><strong>橋本 彩</strong></td><td>09:55</td><td>30分</td><td>—</td><td>4件</td><td><span class="badge badge-brown">勤務中</span></td></tr>
        <tr><td><strong>中村 葵</strong></td><td>10:02</td><td>30分</td><td>—</td><td>3件</td><td><span class="badge badge-brown">勤務中</span></td></tr>
        <tr><td><strong>田村 ひな</strong></td><td>09:45</td><td>—</td><td>—</td><td>2件</td><td><span class="badge badge-brown">勤務中</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- スタイリスト：マイページ -->
<div class="panel" id="panel-mypage" style="display:none;">
  <div class="grid-2">
    <div class="card card-body">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;"><div style="width:44px;height:44px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;">橋</div><div><div style="font-size:14px;font-weight:700;">橋本 彩</div><div style="font-size:11px;color:var(--text3);">トップスタイリスト</div></div></div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
        <div style="text-align:center;background:var(--surface2);border-radius:8px;padding:10px;"><div style="font-size:20px;font-weight:700;color:var(--accent);">112</div><div style="font-size:10px;color:var(--text3);">今月指名</div></div>
        <div style="text-align:center;background:var(--surface2);border-radius:8px;padding:10px;"><div style="font-size:18px;font-weight:700;color:var(--green);">¥1,240k</div><div style="font-size:10px;color:var(--text3);">売上貢献</div></div>
        <div style="text-align:center;background:var(--surface2);border-radius:8px;padding:10px;"><div style="font-size:20px;font-weight:700;color:var(--orange);">5日</div><div style="font-size:10px;color:var(--text3);">有給残</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">今週の出勤予定</span></div>
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);"><span style="font-size:12px;">月</span><span style="font-size:12px;font-weight:600;color:var(--accent);">10:00〜19:00（指名3件予約済）</span></div>
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);"><span style="font-size:12px;">火</span><span style="font-size:12px;font-weight:600;color:var(--accent);">10:00〜19:00</span></div>
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);"><span style="font-size:12px;">水</span><span style="font-size:12px;color:var(--text3);">休み</span></div>
        <div style="display:flex;justify-content:space-between;padding:7px 0;"><span style="font-size:12px;">木</span><span style="font-size:12px;font-weight:600;color:var(--accent);">10:00〜19:00（指名5件予約済）</span></div>
      </div>
    </div>
  </div>
</div>

<!-- シフト申請 -->
<div class="panel" id="panel-myshift" style="display:none;">
  <div class="card card-body">
    <div style="font-size:14px;font-weight:600;margin-bottom:12px;">来月のシフト希望</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;">
      <button onclick="toast('希望を選択しました')" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;background:var(--accent);color:#fff;cursor:pointer;font-family:inherit;">出勤希望</button>
      <button onclick="toast('希望を選択しました')" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;background:var(--surface);color:var(--text2);cursor:pointer;font-family:inherit;">休み希望</button>
      <button onclick="toast('有給申請をしました')" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;background:var(--surface);color:var(--text2);cursor:pointer;font-family:inherit;">有給申請</button>
      <button onclick="toast('遅出を申請しました')" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:12px;font-weight:600;background:var(--surface);color:var(--text2);cursor:pointer;font-family:inherit;">遅出（13:00〜）</button>
    </div>
    <button onclick="toast('シフト希望を申請しました')" class="btn btn-primary">申請する</button>
  </div>
</div>

<!-- タイムカード（スタイリスト） -->
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
      <button onclick="clockBreak()" id="btn-break" class="btn" style="width:100%;justify-content:center;background:#fff8e1;color:var(--orange);border:1px solid var(--orange)!important;opacity:.4;" disabled>休憩</button>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">打刻履歴</span></div>
      <div id="tc-log" style="padding:12px 16px;font-size:12px;color:var(--text2);"></div>
    </div>
  </div>
</div>

</div>
<div class="toast" id="toast"></div>
<script>
var tcState={in:false,inTime:null,log:['6/14 出勤09:58 休憩30分 退勤19:05 指名7件','6/13 出勤10:00 退勤19:02 指名8件']};
function switchView(v){
  document.getElementById('btn-admin').className='view-btn'+(v==='admin'?' active':'');
  document.getElementById('btn-emp').className='view-btn'+(v==='emp'?' active':'');
  document.getElementById('admin-tabs').style.display=v==='admin'?'flex':'none';
  document.getElementById('emp-tabs').style.display=v==='emp'?'flex':'none';
  ['dashboard','seats','staff','timecard'].forEach(function(x){var p=document.getElementById('panel-'+x);if(p){p.classList.remove('active');p.style.display='none';}});
  ['mypage','myshift','mytimecard'].forEach(function(x){var p=document.getElementById('panel-'+x);if(p){p.style.display='none';}});
  if(v==='admin') switchTab('dashboard');
  else switchEmpTab('mypage');
}
function switchTab(t){
  var tabs=['dashboard','seats','staff','timecard'];
  document.querySelectorAll('#admin-tabs .tab').forEach((el,i)=>{el.classList.toggle('active',tabs[i]===t);});
  tabs.forEach(x=>{var p=document.getElementById('panel-'+x);if(p){p.classList.toggle('active',x===t);p.style.display=x===t?'block':'none';}});
}
function switchEmpTab(t){
  var tabs=['mypage','myshift','mytimecard'];
  document.querySelectorAll('#emp-tabs .tab').forEach((el,i)=>{el.classList.toggle('active',tabs[i]===t);});
  tabs.forEach(x=>{var p=document.getElementById('panel-'+x);if(p){p.style.display=x===t?'block':'none';}});
  if(t==='mytimecard') startClock();
}
function clockIn(){tcState.in=true;tcState.inTime=new Date();document.getElementById('clock-status').textContent='勤務中';document.getElementById('clock-status').style.background=getComputedStyle(document.documentElement).getPropertyValue('--accent-light');document.getElementById('clock-status').style.color=getComputedStyle(document.documentElement).getPropertyValue('--accent');document.getElementById('btn-in').disabled=true;document.getElementById('btn-in').style.opacity='.4';document.getElementById('btn-out').disabled=false;document.getElementById('btn-out').style.opacity='1';document.getElementById('btn-break').disabled=false;document.getElementById('btn-break').style.opacity='1';toast('出勤打刻しました');}
function clockOut(){if(!tcState.in)return;tcState.in=false;var now=new Date();var diff=Math.round((now-tcState.inTime)/60000);tcState.log.unshift((now.getMonth()+1)+'/'+(now.getDate())+' 出勤'+fmt(tcState.inTime)+' 退勤'+fmt(now)+' '+Math.floor(diff/60)+'h'+('0'+(diff%60)).slice(-2)+'m');renderTcLog();document.getElementById('clock-status').textContent='勤務外';document.getElementById('clock-status').style.background='';document.getElementById('clock-status').style.color='';document.getElementById('btn-in').disabled=false;document.getElementById('btn-in').style.opacity='1';document.getElementById('btn-out').disabled=true;document.getElementById('btn-out').style.opacity='.4';document.getElementById('btn-break').disabled=true;document.getElementById('btn-break').style.opacity='.4';document.getElementById('clock-elapsed').textContent='';toast('退勤打刻しました');}
function clockBreak(){var b=document.getElementById('btn-break');var starting=b.textContent==='休憩';b.textContent=starting?'休憩終了':'休憩';b.style.background=starting?'var(--orange)':'#fff8e1';b.style.color=starting?'#fff':'var(--orange)';toast(starting?'休憩を開始しました':'休憩を終了しました');}
function fmt(d){return('0'+d.getHours()).slice(-2)+':'+('0'+d.getMinutes()).slice(-2);}
var clockInterval=null;
function startClock(){if(clockInterval)return;clockInterval=setInterval(function(){var now=new Date();document.getElementById('clock-now').textContent=('0'+now.getHours()).slice(-2)+':'+('0'+now.getMinutes()).slice(-2)+':'+('0'+now.getSeconds()).slice(-2);if(tcState.in&&tcState.inTime){var d=Math.floor((now-tcState.inTime)/1000);document.getElementById('clock-elapsed').textContent='経過 '+Math.floor(d/3600)+'h'+('0'+Math.floor((d%3600)/60)).slice(-2)+'m';}},1000);}
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
