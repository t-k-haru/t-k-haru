<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shift OS — 小売・販売デモ</title>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XMFZYV50TL"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XMFZYV50TL');</script>
<style>
:root{--bg:#f5f7fa;--surface:#fff;--surface2:#edf0f5;--border:#dde3ec;--text:#1a1f2e;--text2:#4a5568;--text3:#8896a8;--accent:#2563eb;--accent-light:#eff6ff;--green:#16a34a;--green-light:#f0fdf4;--orange:#ea580c;--orange-light:#fff7ed;--red:#dc2626;--red-light:#fef2f2;--radius:10px;--shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.06);}
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
.kpi.blue .kpi-value{color:var(--accent);}
.kpi.green .kpi-value{color:var(--green);}
.kpi.orange .kpi-value{color:var(--orange);}
.kpi.red .kpi-value{color:var(--red);}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;}
@media(max-width:680px){.grid-2,.grid-3{grid-template-columns:1fr;}}
table{width:100%;border-collapse:collapse;font-size:13px;}
th{background:var(--surface2);color:var(--text2);font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;padding:9px 12px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap;}
td{padding:10px 12px;border-bottom:1px solid var(--border);color:var(--text);}
tr:last-child td{border-bottom:none;}
.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;white-space:nowrap;}
.badge-blue{background:var(--accent-light);color:var(--accent);}
.badge-green{background:var(--green-light);color:var(--green);}
.badge-orange{background:var(--orange-light);color:var(--orange);}
.badge-red{background:var(--red-light);color:var(--red);}
.badge-gray{background:var(--surface2);color:var(--text3);}
.pos-badge{padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;}
.pos-register{background:#eff6ff;color:#1d4ed8;}
.pos-floor{background:#f0fdf4;color:#15803d;}
.pos-back{background:#fff7ed;color:#c2410c;}
.pos-manager{background:#faf5ff;color:#7c3aed;}
.btn{display:inline-flex;align-items:center;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;font-family:inherit;}
.btn-primary{background:var(--accent);color:#fff;}
.btn-outline{background:transparent;color:var(--accent);border:1px solid var(--accent)!important;}
.btn-sm{padding:5px 12px;font-size:11px;}
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.85);color:#fff;padding:10px 20px;border-radius:999px;font-size:12px;z-index:9999;display:none;white-space:nowrap;}
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
  <span class="badge-industry">小売・販売</span>
  <div class="view-toggle">
    <button class="view-btn active" id="btn-admin" onclick="switchView('admin')">店長</button>
    <button class="view-btn" id="btn-emp" onclick="switchView('emp')">スタッフ</button>
  </div>
</div>
<nav class="tabs" id="admin-tabs">
  <div class="tab active" onclick="switchTab('dashboard')">ダッシュボード</div>
  <div class="tab" onclick="switchTab('positions')">ポジション配置</div>
  <div class="tab" onclick="switchTab('staff')">スタッフ管理</div>
  <div class="tab" onclick="switchTab('timecard')">タイムカード</div>
</nav>
<nav class="tabs" id="emp-tabs" style="display:none;">
  <div class="tab active" onclick="switchEmpTab('mypage')">マイページ</div>
  <div class="tab" onclick="switchEmpTab('mytimecard')">タイムカード</div>
</nav>
<div class="main">

<!-- ダッシュボード -->
<div class="panel active" id="panel-dashboard">
  <div class="kpi-grid">
    <div class="kpi blue"><div class="kpi-label">今月シフト確定</div><div class="kpi-value">312</div><div class="kpi-sub">コマ数</div></div>
    <div class="kpi green"><div class="kpi-label">今月人件費</div><div class="kpi-value" style="font-size:20px;">¥1,380k</div><div class="kpi-sub">売上比 18.2%</div></div>
    <div class="kpi orange"><div class="kpi-label">在籍スタッフ</div><div class="kpi-value">18</div><div class="kpi-sub">名（パート含む）</div></div>
    <div class="kpi red"><div class="kpi-label">未配置コマ</div><div class="kpi-value">2</div><div class="kpi-sub">今週中に対応要</div></div>
  </div>
  <div class="grid-2" style="margin-bottom:16px;">
    <div class="card">
      <div class="card-header"><span class="card-title">今週のシフト（時間帯別）</span><button class="btn btn-sm btn-outline" onclick="toast('シフト表を出力します')">出力</button></div>
      <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="min-width:480px;">
          <thead><tr><th>氏名</th><th>ポジション</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th><th>日</th></tr></thead>
          <tbody>
            <tr><td><strong>山本 拓</strong></td><td><span class="pos-badge pos-manager">店長代理</span></td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;color:var(--text3);">休み</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--orange);">中番</td><td style="text-align:center;font-size:11px;color:var(--text3);">休み</td></tr>
            <tr><td><strong>松田 里奈</strong></td><td><span class="pos-badge pos-register">レジ</span></td><td style="text-align:center;font-size:11px;color:var(--text3);">休み</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;color:var(--text3);">休み</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--orange);">中番</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td></tr>
            <tr><td><strong>高橋 隼人</strong></td><td><span class="pos-badge pos-floor">フロア</span></td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--orange);">中番</td><td style="text-align:center;font-size:11px;color:var(--text3);">休み</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--orange);">中番</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;color:var(--text3);">休み</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--orange);">中番</td></tr>
            <tr><td><strong>伊藤 七海</strong></td><td><span class="pos-badge pos-back">バック</span></td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--orange);">中番</td><td style="text-align:center;font-size:11px;color:var(--text3);">休み</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--orange);">中番</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td><td style="text-align:center;font-size:11px;color:var(--text3);">休み</td><td style="text-align:center;font-size:11px;font-weight:600;color:var(--accent);">開店〜</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">時間帯別 最低人員チェック</span></div>
      <div class="card-body">
        <div style="margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:var(--green-light);border-radius:8px;border:1px solid var(--green);"><span style="font-size:12px;font-weight:600;">開店〜13:00</span><div style="display:flex;align-items:center;gap:8px;"><span style="font-size:11px;color:var(--text2);">必要3名 / 配置3名</span><span class="badge badge-green">OK</span></div></div>
        <div style="margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:var(--accent-light);border-radius:8px;border:1px solid var(--accent);"><span style="font-size:12px;font-weight:600;">13:00〜18:00</span><div style="display:flex;align-items:center;gap:8px;"><span style="font-size:11px;color:var(--text2);">必要4名 / 配置4名</span><span class="badge badge-blue">OK</span></div></div>
        <div style="margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:var(--red-light);border-radius:8px;border:1px solid var(--red);"><span style="font-size:12px;font-weight:600;">18:00〜閉店</span><div style="display:flex;align-items:center;gap:8px;"><span style="font-size:11px;color:var(--text2);">必要3名 / 配置2名</span><span class="badge badge-red">不足</span></div></div>
        <button class="btn btn-sm btn-primary" onclick="toast('シフト調整画面を開きます')" style="width:100%;justify-content:center;margin-top:4px;">18時以降のシフトを補充</button>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">曜日別 人件費と来客数（今月）</span></div>
    <div class="card-body">
      <div style="display:flex;align-items:flex-end;gap:6px;height:80px;margin-bottom:8px;">
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;opacity:.4;height:32px;"></div><div style="font-size:9px;color:var(--text3);">月</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;opacity:.45;height:36px;"></div><div style="font-size:9px;color:var(--text3);">火</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;opacity:.5;height:40px;"></div><div style="font-size:9px;color:var(--text3);">水</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;opacity:.55;height:44px;"></div><div style="font-size:9px;color:var(--text3);">木</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--accent);border-radius:4px 4px 0 0;opacity:.7;height:56px;"></div><div style="font-size:9px;color:var(--text3);">金</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--orange);border-radius:4px 4px 0 0;height:80px;"></div><div style="font-size:9px;font-weight:600;color:var(--orange);">土</div></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;background:var(--orange);border-radius:4px 4px 0 0;height:72px;"></div><div style="font-size:9px;font-weight:600;color:var(--orange);">日</div></div>
      </div>
      <div style="font-size:11px;color:var(--text2);">土日は来客数1.8倍 — 自動で最低人員基準を高く設定しています</div>
    </div>
  </div>
</div>

<!-- ポジション配置 -->
<div class="panel" id="panel-positions">
  <div class="card">
    <div class="card-header"><span class="card-title">本日のポジション配置（13:00〜18:00）</span><button class="btn btn-sm btn-primary" onclick="toast('配置を保存しました')">保存</button></div>
    <div class="card-body">
      <div class="grid-3" style="margin-bottom:16px;">
        <div style="background:var(--accent-light);border:1px solid var(--accent);border-radius:10px;padding:12px;"><div style="font-size:11px;font-weight:700;color:var(--accent);margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px;">レジ担当</div><div style="font-size:12px;font-weight:600;margin-bottom:4px;">松田 里奈</div><div style="font-size:12px;font-weight:600;margin-bottom:4px;">山本 拓</div><div style="font-size:11px;color:var(--text3);">時給差：¥200</div></div>
        <div style="background:var(--green-light);border:1px solid var(--green);border-radius:10px;padding:12px;"><div style="font-size:11px;font-weight:700;color:var(--green);margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px;">フロア</div><div style="font-size:12px;font-weight:600;margin-bottom:4px;">高橋 隼人</div><div style="font-size:12px;font-weight:600;margin-bottom:4px;">— （募集中）</div><div style="font-size:11px;color:var(--red);">⚠ 1名不足</div></div>
        <div style="background:var(--orange-light);border:1px solid var(--orange);border-radius:10px;padding:12px;"><div style="font-size:11px;font-weight:700;color:var(--orange);margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px;">バック・在庫</div><div style="font-size:12px;font-weight:600;margin-bottom:4px;">伊藤 七海</div><div style="font-size:11px;color:var(--text3);">在庫チェック担当</div></div>
      </div>
      <div style="background:var(--surface2);border-radius:8px;padding:12px;font-size:12px;color:var(--text2);">
        <strong style="color:var(--text);">自動ルール：</strong>レジ担当は有資格者（レジ研修済）のみ配置可。土日は最低2名体制で自動チェック。複数ポジション対応スタッフに優先配置通知を送ります。
      </div>
    </div>
  </div>
</div>

<!-- スタッフ管理 -->
<div class="panel" id="panel-staff">
  <div class="card">
    <div class="card-header"><span class="card-title">スタッフ一覧</span><button class="btn btn-sm btn-primary" onclick="toast('スタッフを追加しました')">＋ 追加</button></div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>氏名</th><th>雇用形態</th><th>時給</th><th>対応ポジション</th><th>今月シフト</th><th>今月人件費</th><th>状態</th></tr></thead>
        <tbody>
          <tr><td><strong>山本 拓</strong></td><td>正社員</td><td>¥1,600</td><td><span class="pos-badge pos-manager">店長代理</span> <span class="pos-badge pos-register">レジ</span></td><td>22コマ</td><td style="font-weight:600;">¥224k</td><td><span class="badge badge-green">在籍中</span></td></tr>
          <tr><td><strong>松田 里奈</strong></td><td>パート</td><td>¥1,200</td><td><span class="pos-badge pos-register">レジ</span></td><td>18コマ</td><td style="font-weight:600;">¥144k</td><td><span class="badge badge-green">在籍中</span></td></tr>
          <tr><td><strong>高橋 隼人</strong></td><td>パート</td><td>¥1,100</td><td><span class="pos-badge pos-floor">フロア</span></td><td>16コマ</td><td style="font-weight:600;">¥118k</td><td><span class="badge badge-green">在籍中</span></td></tr>
          <tr><td><strong>伊藤 七海</strong></td><td>パート</td><td>¥1,050</td><td><span class="pos-badge pos-back">バック</span> <span class="pos-badge pos-floor">フロア</span></td><td>14コマ</td><td style="font-weight:600;">¥98k</td><td><span class="badge badge-green">在籍中</span></td></tr>
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
      <thead><tr><th>氏名</th><th>ポジション</th><th>出勤</th><th>休憩</th><th>退勤</th><th>実働</th><th>状態</th></tr></thead>
      <tbody>
        <tr><td><strong>山本 拓</strong></td><td><span class="pos-badge pos-manager">店長代理</span></td><td>09:55</td><td>60分</td><td>—</td><td>勤務中</td><td><span class="badge badge-blue">勤務中</span></td></tr>
        <tr><td><strong>松田 里奈</strong></td><td><span class="pos-badge pos-register">レジ</span></td><td>10:02</td><td>30分</td><td>—</td><td>勤務中</td><td><span class="badge badge-blue">勤務中</span></td></tr>
        <tr><td><strong>高橋 隼人</strong></td><td><span class="pos-badge pos-floor">フロア</span></td><td>13:00</td><td>—</td><td>—</td><td>勤務中</td><td><span class="badge badge-blue">勤務中</span></td></tr>
        <tr><td><strong>伊藤 七海</strong></td><td><span class="pos-badge pos-back">バック</span></td><td>09:58</td><td>60分</td><td>18:05</td><td>7h7m</td><td><span class="badge badge-gray">退勤済</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- スタッフ：マイページ -->
<div class="panel" id="panel-mypage" style="display:none;">
  <div class="grid-2">
    <div class="card card-body">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;"><div style="width:44px;height:44px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;">高</div><div><div style="font-size:14px;font-weight:700;">高橋 隼人</div><div style="font-size:11px;color:var(--text3);">フロア担当 / パート</div></div></div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
        <div style="text-align:center;background:var(--surface2);border-radius:8px;padding:10px;"><div style="font-size:20px;font-weight:700;color:var(--accent);">16</div><div style="font-size:10px;color:var(--text3);">今月シフト</div></div>
        <div style="text-align:center;background:var(--surface2);border-radius:8px;padding:10px;"><div style="font-size:18px;font-weight:700;color:var(--green);">¥118,000</div><div style="font-size:10px;color:var(--text3);">今月給与見込み</div></div>
        <div style="text-align:center;background:var(--surface2);border-radius:8px;padding:10px;"><div style="font-size:20px;font-weight:700;color:var(--orange);">6日</div><div style="font-size:10px;color:var(--text3);">有給残</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">今週のシフト</span></div>
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:6px;padding:7px 0;flex-wrap:nowrap;border-bottom:1px solid var(--border);"><span style="font-size:12px;">月</span><span class="pos-badge pos-floor">フロア 13:00〜21:00</span></div>
        <div style="display:flex;justify-content:space-between;align-items:center;gap:6px;padding:7px 0;flex-wrap:nowrap;border-bottom:1px solid var(--border);"><span style="font-size:12px;">火</span><span style="font-size:12px;color:var(--text3);">休み</span></div>
        <div style="display:flex;justify-content:space-between;align-items:center;gap:6px;padding:7px 0;flex-wrap:nowrap;border-bottom:1px solid var(--border);"><span style="font-size:12px;">水</span><span class="pos-badge pos-floor">フロア 13:00〜21:00</span></div>
        <div style="display:flex;justify-content:space-between;align-items:center;gap:6px;padding:7px 0;flex-wrap:nowrap;"><span style="font-size:12px;">土</span><span class="pos-badge pos-floor">フロア 10:00〜19:00</span></div>
      </div>
    </div>
  </div>
</div>


<!-- スタッフタイムカード -->
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

</div>
<div class="toast" id="toast"></div>
<script>
var tcState={in:false,inTime:null,log:['6/14 フロア 出勤13:00 休憩30分 退勤21:02 実働7h32m','6/11 フロア 出勤10:00 休憩60分 退勤19:05 実働8h05m']};
function switchView(v){document.getElementById('btn-admin').className='view-btn'+(v==='admin'?' active':'');document.getElementById('btn-emp').className='view-btn'+(v==='emp'?' active':'');document.getElementById('admin-tabs').style.display=v==='admin'?'flex':'none';document.getElementById('emp-tabs').style.display=v==='emp'?'flex':'none';['dashboard','positions','staff','timecard'].forEach(function(x){var p=document.getElementById('panel-'+x);if(p){p.classList.remove('active');p.style.display='none';}});['mypage','mytimecard'].forEach(function(x){var p=document.getElementById('panel-'+x);if(p){p.style.display='none';}});if(v==='admin') switchTab('dashboard');else switchEmpTab('mypage');}
function switchTab(t){var tabs=['dashboard','positions','staff','timecard'];document.querySelectorAll('#admin-tabs .tab').forEach((el,i)=>{el.classList.toggle('active',tabs[i]===t);});tabs.forEach(x=>{var p=document.getElementById('panel-'+x);if(p){p.classList.toggle('active',x===t);p.style.display=x===t?'block':'none';}});}
function switchEmpTab(t){var tabs=['mypage','mytimecard'];document.querySelectorAll('#emp-tabs .tab').forEach((el,i)=>{el.classList.toggle('active',tabs[i]===t);});tabs.forEach(x=>{var p=document.getElementById('panel-'+x);if(p){p.style.display=x===t?'block':'none';}});if(t==='mytimecard')startClock();}
function clockIn(){tcState.in=true;tcState.inTime=new Date();document.getElementById('clock-status').textContent='勤務中';document.getElementById('clock-status').style.background='var(--accent-light)';document.getElementById('clock-status').style.color='var(--accent)';document.getElementById('btn-in').disabled=true;document.getElementById('btn-in').style.opacity='.4';document.getElementById('btn-out').disabled=false;document.getElementById('btn-out').style.opacity='1';document.getElementById('btn-break').disabled=false;document.getElementById('btn-break').style.opacity='1';toast('出勤打刻しました');}
function clockOut(){if(!tcState.in)return;tcState.in=false;var now=new Date();var diff=Math.round((now-tcState.inTime)/60000);tcState.log.unshift((now.getMonth()+1)+'/'+(now.getDate())+' 出勤'+fmt(tcState.inTime)+' 退勤'+fmt(now)+' '+Math.floor(diff/60)+'h'+('0'+(diff%60)).slice(-2)+'m');renderTcLog();document.getElementById('clock-status').textContent='勤務外';document.getElementById('clock-status').style.background='';document.getElementById('clock-status').style.color='';document.getElementById('btn-in').disabled=false;document.getElementById('btn-in').style.opacity='1';document.getElementById('btn-out').disabled=true;document.getElementById('btn-out').style.opacity='.4';document.getElementById('btn-break').disabled=true;document.getElementById('btn-break').style.opacity='.4';document.getElementById('clock-elapsed').textContent='';toast('退勤打刻しました');}
function clockBreak(){var b=document.getElementById('btn-break');var s=b.textContent==='休憩';b.textContent=s?'休憩終了':'休憩';b.style.background=s?'var(--orange)':'var(--orange-light)';b.style.color=s?'#fff':'var(--orange)';toast(s?'休憩を開始しました':'休憩を終了しました');}
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
