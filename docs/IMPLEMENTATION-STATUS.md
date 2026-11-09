# Implementation Status

Last updated: 2026-09-20

## Current result

The local application foundation and provider-independent phase-one theme are implemented. Verified public company data and four identified developments have replaced the fictional records on the public site. The site is suitable for stakeholder review, but it is not production release-ready because several release inputs require client or infrastructure decisions.

## Implemented

- Bedrock/Composer WordPress foundation with the active `archdome_portal` classic PHP theme.
- Composer-managed Polylang 3.8, Arabic default/RTL, English/LTR, translated pages, translated menus, and direct `/en/` homepage routing.
- Accessible global shell, skip link, keyboard-capable responsive navigation, language switcher, footer, focus states, and reduced-motion handling.
- Development and Property post types; Location, Property Type, Amenity, Property Status, Development Type, and Development Status taxonomies.
- REST-visible registered metadata, guarded sanitization, permissions, nonces, native editing panels, global contact/social settings, and media/relationship fields.
- All 17 planned native server-rendered Gutenberg blocks with scoped editor/public assets.
- Bilingual editor-managed homepage compositions using reusable blocks and the existing uploaded image.
- Development/Property archives, validated GET filters, sorting, pagination, empty states, and `noindex` protection for search/filter result combinations.
- Development/Property details, galleries, plans, amenities, related records, provider-neutral location output, brochure links, and contextual enquiry routes.
- Insights archive/single, standard pages, Locations, Contact, Search, taxonomy, and 404 templates.
- Full-size plan links, responsive WordPress images, bounded queries, deferred theme JavaScript, logical CSS properties, and no frontend framework/build system.
- Idempotent local setup tool, translation catalog generator, and read-only frontend smoke-test tool.
- Idempotent real-content importer for the verified facts in `real-data.md`; it keeps uncertain commercial/legal fields empty and moves old demo records to recoverable drafts.
- Verified public telephone, WhatsApp, Facebook, and Instagram details on the Contact page.

## Verified locally

- PHP syntax, JavaScript syntax, block metadata JSON, Pint formatting, and Composer lint pass.
- All 17 custom blocks register successfully.
- Arabic and English homepages render one H1, the correct language and direction, and return 200 without a redirect chain.
- Arabic/English archives and core pages, search, filtered results, malformed nested query input, 404 handling, and the available Development detail route pass the automated smoke test.
- Composer/Pest test passes; this environment emits non-fatal cache-write warnings outside the writable theme directory.
- Translation template regenerated from 225 strings.

## Required before production release

These are not safe to invent or enable without an approved provider, content, asset, or infrastructure decision:

1. Supply and approve the official logo variants and licensed Arabic/English font files; verify final colors against brand originals.
2. Approve the imported bilingual copy; add confirmed Property inventory, complete Development data, real project imagery/alt text, legal content, and publish the Privacy Policy.
3. Select and configure the inquiry form/email or CRM provider, spam controls, consent language, retention/deletion policy, and controlled delivery tests.
4. Select the map provider and privacy/loading behavior if an interactive map is required. The current fallback safely displays location text and coordinates.
5. Approve an SEO plugin and ownership for editable descriptions, canonical/social metadata, and structured data; review the date-based Post permalink migration before changing existing URLs.
6. Select analytics/consent, caching/CDN, image transformation, monitoring, backups, and security-header ownership for the production environment.
7. Complete manual WCAG 2.2 AA keyboard, zoom, screen-reader, contrast, RTL/LTR, responsive, and cross-browser testing with representative final content.
8. Run production-like Lighthouse/Core Web Vitals and page-weight tests after final media and third-party integrations are installed.
9. Complete staging stakeholder review, content sign-off, backup/restore rehearsal, deployment rehearsal, and rollback verification.

## Verification commands

From the theme directory:

```powershell
php tools/setup-site.php --apply
php tools/smoke-test.php
node tools/build-pot.mjs
```

From the Bedrock project directory:

```powershell
vendor\bin\pint web\app\themes\archdome_portal
composer lint
composer test
```
