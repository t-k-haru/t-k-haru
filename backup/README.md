# Full site backup

`lolipop-full-site-backup.tar.gz` is a complete mirror of the old Lolipop document root (`shift.nobushi.jp`), kept so the server can be safely deleted.

- Custom app: `lp-shift.php`, `demo-*`, `webhook/`, icons, logo uploads.
- WordPress core + stock plugins/themes (akismet, Google Site Kit, twentytwenty*) — generic, re-downloadable from wordpress.org. The site was served directly by `lp-shift.php`, not WordPress.

Not included: the database (internal host, customer data) and `xmlrpc.php` / `wp-login.php` / `wp-comments-post.php` (blocked by WebDAV; standard files).

Secrets are redacted to `__REDACTED_*__`.

```bash
tar -xzf lolipop-full-site-backup.tar.gz -C ./site
```
