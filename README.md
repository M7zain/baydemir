# Baydemir WordPress Theme

Modern, dark, construction/architecture theme built for **Baydemir İnşaat** and reusable for future clients.

## Stack

- WordPress 6.4+ hybrid theme with `theme.json` design tokens
- Custom Post Types: **Projeler** & **Hizmetler**
- Theme Customizer panel for branding, contact, hero, stats, CTA, social
- Built-in AJAX contact form
- One-click demo content installer

## Install

1. Copy this folder into `wp-content/themes/baydemir-theme` (or zip and upload).
2. Activate **Baydemir** under Appearance → Themes.
3. Go to **Baydemir → Panel** → **Örnek İçeriği Yükle**.
4. Customize under **Appearance → Customize → Baydemir Tema Ayarları**.
5. Optionally set a custom logo under Site Identity.

## Admin

- **Baydemir → Panel** — status overview, demo installer, quick links
- **Baydemir → Projeler** — inline project manager (cover, categories, details, gallery) without the WP post editor
- **Baydemir → Hakkımızda** — about page text, side image, feature cards
- **Projeler** list (CPT) — thumbnail, location, status badge, featured star toggle
- **Hizmetler** list — thumbnail, icon, menu order
- Project edit — details panel + gallery (add / remove / reorder)
- Service edit — visual icon picker

## Admin: Projects

**Baydemir → Projeler** (recommended)

| Field | Purpose |
| --- | --- |
| Proje Adı / Özet / Açıklama | Main project copy |
| Kapak Görseli | Card & hero image |
| Kategori | Konut, Villa, Ticari, Devam Eden |
| Lokasyon, Tip, Teslim, Daire | Detail page meta |
| Galeri | Image gallery on single project |
| Öne çıkar | Show on homepage featured row |

## Admin: Services

**Hizmetler → Yeni Ekle** — title, excerpt, image, icon, order.

## Reuse for new clients

1. Change company name, colors, contact in Customizer.
2. Replace logo & hero image.
3. Clear demo projects/services and add client content.
4. Update page copy in Pages / block editor.

## Placeholder images

Theme ships with local banner images under `assets/images/`. Replace anytime via Media Library + Customizer / featured images.

## Permalinks

After activation, visit **Settings → Permalinks** and save once (or run the demo installer, which flushes rewrites).
