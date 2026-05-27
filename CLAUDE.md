# CamHacker

Live webcam aggregator with 1365 webcams in 60 countries.

## Tech Stack
- PHP 7.2+
- Bootstrap 5.3
- Leaflet.js (interactive map)
- JSON file database (migrated from MySQL)

## Project Structure
- `index.php` — Homepage
- `includes/config.php` — Configuration
- `includes/database.php` — JSON data access
- `includes/functions.php` — Shared functions
- `admin/` — Admin dashboard (htpasswd protected)
- `assets/` — CSS, JS, images

## Local Development
- **Path:** `~/.../_Live Sites/camhacker.com/www`
- **Local URL:** http://camhacker.localhost:8080

## Hosting & Deploy
- **VPS:** ssh root@187.77.96.225
- **VPS Path:** /var/www/camhacker.com
- **Production:** https://camhacker.com
- **Deploy:** rsync via Mission Control dashboard
- **SSL:** Let's Encrypt via Cloudflare (Strict mode)
- **GitHub:** mediageni/camhacker.com

## Mission Control
Registered in Mission Control dashboard at http://localhost:8080

## Google Analytics & Search Console
- **GA4 Property ID:** 368068249 — tracked in Mission Control
- **Search Console:** Verified (sc-domain:camhacker.com)
- **Data access:** Mission Control SEO dashboard at http://localhost:8080/seo.php
- **API:** `http://localhost:8080/api.php?action=google-data` (cached GA4 + GSC + Core Web Vitals)
- **Real-time:** `http://localhost:8080/api.php` POST `action=google-realtime&domain=camhacker.com`
- **Available metrics:** traffic, search queries, top pages, impressions, clicks, CTR, Core Web Vitals
- **SEO Skills available:** `/seo-audit`, `/seo-check`, `/on-page-seo`, `/technical-seo`, `/keyword-cluster`, `/content-strategy`, `/content-brief`, `/schema-markup`, `/broken-links`, `/internal-linking`, `/ai-visibility`

## Notes
- SEO URLs: /cam/123, /country/united-states
- AdSense: ca-pub-6630109012927307

## VPS Resource Limits (CRITICAL)
- **VPS specs:** 2 vCPUs, 8GB RAM — shared across ALL sites
- **NEVER** add always-running background processes (queue workers, watchers, supervisord) without checking `uptime` and CPU steal first
- **NEVER** set PHP-FPM max_children above 3 (8.3) or 2 (8.4)
- **NEVER** add cron jobs running more frequently than every 30 minutes
- Queue workers: use `--stop-when-empty` flag, never run 24/7
- Before deploying resource-heavy features: `ssh root@187.77.96.225 "uptime && top -bn1 | grep Cpu"`

## Active Work: Image-based SEO rewrite (started 2026-05-27)

Goal: a UNIQUE, image-based H1 + meta title + meta description for every cam page, to fix Bing + Google "duplicate titles/descriptions" warnings and lift CTR. Each cam in `data/webcams.json` gets three new fields: `h1`, `meta_title`, `meta_description`. `cam.php` uses them (`$h1Heading`, `$pageTitle`, `$pageDescription`) and falls back to the old `title_seo`/`description_seo` template when absent, so it is safe mid-rollout.

**Progress: 115 / 669 done (2026-05-27).** Live dashboard: `/admin/seo-progress.php` (admin login admin / hit2bits!), pending IDs at `?pending=txt`. A cam is "done" when it has a non-empty `meta_title`.

**How to resume (say "continue the camhacker SEO rollout"):** work in daylight batches because I have to visually read each cam frame.
1. `php tools/seo/pick_cams.php data/webcams.json 14` — picks 14 daylight + bright cams that lack SEO copy, downloads frames to `/tmp/sample_<id>.jpg`, prints `id|city|state|country|brightness|url`.
2. Open each `/tmp/sample_<id>.jpg` (Read tool) and look at it. The same image is viewable live at `https://camhacker.com/cam-image/<id>.jpg`.
3. Write a batch JSON `[{"id":N,"h1":...,"meta_title":...,"meta_description":...}, ...]`.
4. Apply to PRODUCTION (source of truth — live view_count mutates there, never overwrite the whole file): `scp batch.json root@187.77.96.225:/root/` then `ssh root@187.77.96.225 'cd /var/www/camhacker.com && php /root/apply_seo.php data/webcams.json /root/batch.json && find /var/cache/nginx/fastcgi -type f -delete'`. Also apply to the local copy: `php tools/seo/apply_seo.php data/webcams.json batch.json`.

**Voice rules (Balanced, locked):** title `{City} Live IP Camera: {Scene}, {Country}` ≤ ~57 chars (cam.php appends ` | CamHacker`); H1 natural + scene-led, includes the variant "live cam", NOT a copy of the title; description 140-160 chars, scene + location + a head term (live public IP camera / live streaming) + soft CTA. NO em-dashes. NO "unsecured/hacked". NO competitor brand names (insecam, camspy, etc). Keyword cluster from real Bing+Google data: live cams / live cameras / cctv live / ip cams / live camera online / 24-7. Obscured/blank/dark lens or persistently offline -> unique location-only fallback copy (don't invent a scene). Private-interior or identifiable-person close-up cams -> neutral generic copy, NEVER "peek inside" voyeur invitations.

**Mislabeled cams (view != stored location) auto-skipped in `tools/seo/pick_cams.php` `$skip`:** 123 (really Plymouth UK), 125 (really a US town), 142 (really St Peter's Rome), 250 (really Harrisonburg VA), 3065 (really a Mediterranean village). Fix their city/country/coords in the data to enable them. Inland cities labelling coastal cams (Florence/Imola/Pernik/Milan->Amalfi) are softened to the region instead of skipped.

## OG / social card images
- `cam-image.php` + nginx/htaccess rewrite serve `/cam-image/<id>.jpg` = the cam's current frame resized to 1200x630 (cached 30 min in `cache/og/`, gitignored). `includes/header.php` points `og:image` + `twitter:image` at it. This is what makes links unfurl with a picture on Reddit/X/etc (third-party query-string image URLs failed; same-domain `.jpg` works). It is also how Claude "sees" each cam.

## Daily X promo email (free, no X API)
- X API was abandoned: no free tier since 2026-02-06, and posts with a URL cost $0.20 each. Instead `tools/social/daily_email.php` (VPS cron `0 7 * * *` = 09:00 CEST) emails the best daylight cam (image + ready-to-paste caption + link) to vidal.dewit@gmail.com; Vidal posts to @camhacker manually (free). Gmail SMTP creds in `/root/camhacker-mail.env` (VPS) + Mission Control `dashboard/config.php` `$camhackerMail`. One-off: `php tools/social/daily_email.php /root/camhacker-mail.env data/webcams.json <camId>`.
- Bing Webmaster API key (for keyword data) is in Mission Control `config.php` `$bingApiKey`.
