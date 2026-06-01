# Shift OS

Custom shift-management, time-clock and attendance system. Landing page + five industry demos, archived as a portfolio (formerly `shift.nobushi.jp`).

**Live demo:** https://t-k-haru.github.io/t-k-haru/

## Demos
Restaurant · Care/medical · Retail · Beauty/salon · Generic — all client-side (view/tab switching, Chart.js).

## Stack
HTML / CSS / vanilla JS / Chart.js · PHP · Stripe Checkout & Webhook · PHPMailer (SMTP) · GA4 · MySQL.

## Layout
```
docs/      static site served by GitHub Pages (LP + demos)
src/       original source (PHP backend, demos, Stripe webhook); secrets redacted
backup/    full archive of the old site (so the server can be decommissioned)
```

## Run
```bash
cd docs && python3 -m http.server 8000   # http://localhost:8000/
```

## Notes
- Secrets (DB / SMTP / Stripe / GA4 keys, WP salts) are redacted to `__REDACTED_*__`.
- Demo contact form and payment links are disabled; analytics do not fire.
- The production database (customer data) is intentionally not included.

## GitHub Pages
Settings → Pages → Source: `main` / `/docs`.
