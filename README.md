# Yablonsky Cookie Consent for WordPress

**Yablonsky Cookie Consent for WordPress** is a self-hosted cookie consent banner and Google Consent Mode v2 integration plugin for WordPress websites.

Current release: **v1.0.5**

Developed and maintained by [Yablonsky.io](https://yablonsky.io/).

## Why this plugin exists

Many WordPress websites add Google Analytics, Google Tag Manager, and Google Ads scripts in several different places: themes, page builders, SEO plugins, custom header fields, or manual snippets.

This plugin is designed to provide one clear consent-controlled layer for common small business tracking setups.

## Main features

This development package currently contains the Phase 1 plugin skeleton:

- WordPress plugin bootstrap.
- Admin settings page under Settings.
- Settings model with safe defaults.
- Basic sanitization helpers.
- Placeholder frontend asset structure.
- Public documentation skeleton.

The next phases will add the public banner, settings modal, consent persistence, Google Consent Mode v2 defaults, and consent-controlled Google Tag Manager loading.

## Requirements

- WordPress 6.0 or later.
- PHP 7.4 or later.
- Administrator access.

## Compatibility

WooCommerce is not required.

This plugin is not a Google-certified CMP and does not implement IAB TCF for Google publisher products such as AdSense, Ad Manager, or AdMob.

## Installation

Upload the plugin ZIP through WordPress admin:

1. Go to Plugins.
2. Select Add New.
3. Select Upload Plugin.
4. Choose the ZIP file.
5. Install and activate.

Then open:

```text
Settings → Yablonsky Cookie Consent
```

## Security model

The plugin uses WordPress admin capability checks, WordPress Settings API, sanitization, and output escaping.

The plugin does not phone home, does not collect telemetry, and does not send data to external services by itself.

## What this plugin does NOT do

Yablonsky Cookie Consent for WordPress does not provide legal advice, guarantee GDPR or Google Ads approval, act as a Google-certified CMP, implement IAB TCF, scan all cookies automatically, or control scripts inserted by unrelated plugins.

## Roadmap

- Public cookie banner.
- Cookie settings modal.
- Necessary, Analytics, Marketing, and Functional categories.
- Google Consent Mode v2 support.
- Basic Consent Mode.
- Consent-controlled Google Tag Manager loading.
- Optional privacy-safe consent logging.
- Validation documentation.

## Author

Developed and maintained by [Yablonsky.io](https://yablonsky.io/).

For business automation, AI, SEO and WordPress-related projects, visit [Yablonsky.io](https://yablonsky.io/).

AI tools and automation platform: [ai.yablonsky.io](https://ai.yablonsky.io/)

## Disclaimer

Yablonsky Cookie Consent for WordPress is an independent open-source project and is not affiliated with, endorsed by, or sponsored by the WordPress Foundation, Automattic, WooCommerce, or related trademark owners.

WordPress, WooCommerce, and related product names are trademarks of their respective owners. They are used here only to describe compatibility.

## License

GPL-2.0-or-later.
