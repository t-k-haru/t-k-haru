# Shift OS — カスタムシフト管理システム（ポートフォリオ）

> 飲食・介護・美容・小売など、業種ごとに専用カスタムできるシフト管理・タイムカード・勤怠集計の Web システム。
> 旧本番サービス（`shift.nobushi.jp`）のランディングページと業種別インタラクティブデモを、ポートフォリオとして公開しています。

🔗 **ライブデモ**: https://t-k-haru.github.io/t-k-haru/

---

## これは何か

「Shift OS」は、シフト管理・タイムカード・勤怠集計を業務専用にカスタマイズして提供していた SaaS のフロントエンドです。
本リポジトリには、誰でも触れる**ランディングページ**と**業種別の管理画面デモ**を収録しています（すべてブラウザ内で動作）。

> サーバーサイド（PHP バックエンド・Stripe Webhook 等の実装）は非公開リポジトリで管理しています。

## デモ一覧

| 業種 | デモ |
|---|---|
| 🍽️ 飲食店 | [`demo/`](docs/demo/) |
| 🏥 介護・医療施設 | [`demo-care/`](docs/demo-care/) |
| 🛍️ 小売・販売 | [`demo-retail/`](docs/demo-retail/) |
| 💇 美容・サロン | [`demo-beauty/`](docs/demo-beauty/) |
| 📋 汎用 | [`demo-generic/`](docs/demo-generic/) |

各デモは **管理者ビュー／従業員ビューの切り替え・タブ切り替え・グラフ描画**（[Chart.js](https://www.chartjs.org/)）まで、すべてクライアントサイドで動作します。

## 技術スタック

- **フロントエンド**: HTML / CSS / Vanilla JavaScript、Chart.js によるデータ可視化
- **バックエンド（非公開）**: PHP、Stripe Checkout / Webhook（署名検証）、PHPMailer による SMTP 送信、GA4 Measurement Protocol、MySQL

## ローカルで動かす

```bash
cd docs && python3 -m http.server 8000
# → http://localhost:8000/
```

## 構成

```
docs/                      GitHub Pages で配信する静的サイト
├── index.html             ランディングページ
├── demo/  demo-care/  demo-retail/  demo-beauty/  demo-generic/
└── icon.png  icon.webp  .nojekyll
```

## GitHub Pages の有効化

1. **Settings → Pages**
2. **Source**: `Deploy from a branch` → **Branch**: `main` / フォルダ `/docs`
3. 数分後に https://t-k-haru.github.io/t-k-haru/ で公開されます

---

<sub>ポートフォリオ用。デモ内の問い合わせフォーム・申し込みボタンは無効化済みで、トラッキングも送信しません。</sub>
