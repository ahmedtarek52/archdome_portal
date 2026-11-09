# SEO and Performance

Status: targets for implementation and release validation

## SEO ownership

WordPress/theme provides semantic HTML, clean archives, headings, excerpts, responsive media, and stable content identifiers. An approved SEO plugin should own editable titles/descriptions, canonical tags, Open Graph/social metadata, XML sitemap policy, and most schema. Avoid duplicate metadata or JSON-LD.

## URL and indexing policy

- Replace the inspected date-based permalink structure with a human-readable post-name policy only after backup and redirect review.
- CPT rewrites should be stable; final Arabic/English slugs depend on the multilingual/SEO plugin decision.
- Public canonical archives/singles may be indexed when complete.
- Internal search, preview, admin, and noncanonical filter combinations should be `noindex` as configured by the approved SEO layer.
- Pagination uses crawlable links and self-canonical pages; do not canonicalize every paginated page to page one.
- XML sitemaps include only public, canonical, approved content and the correct translated alternates.

## Structured data

Candidate types: `Organization`, `WebSite`, `BreadcrumbList`, `Article`/`NewsArticle`, and defensible `Residence`/`Apartment`/`Product`-like property representations based on actual content. Select types only after SEO review; do not invent prices, availability, addresses, ratings, or offers. One owner generates each graph.

## Performance targets

Measured on representative production-like mobile runs and monitored with field data when available:

| Metric | Release target |
|---|---:|
| LCP | <= 2.5 s at p75 |
| INP | <= 200 ms at p75 |
| CLS | <= 0.1 at p75 |
| Lighthouse Performance | >= 90 on agreed representative pages |
| Lighthouse Accessibility/SEO/Best Practices | >= 95, with issues reviewed |
| Initial page transfer | <= 1.5 MB typical; <= 2.0 MB image-led detail page |
| Initial JS, compressed | <= 75 KB theme-authored |
| Initial CSS, compressed | <= 60 KB theme-authored |
| Webfonts, compressed | <= 200 KB initial critical language set |

Lab scores are diagnostics, not substitutes for Core Web Vitals field data.

## Loading strategy

- Server-render the initial experience.
- Identify one LCP image; provide dimensions, responsive sources, suitable compression, and priority only when truly above the fold.
- Lazy-load below-fold images/iframes; do not lazy-load the LCP candidate.
- Prefer AVIF/WebP generation when the hosting/media pipeline supports it while retaining safe fallbacks.
- Subset/self-host approved fonts, minimize weights, use `font-display: swap`, and preload only the critical face(s).
- Keep shared CSS small; block/page assets load on demand.
- Defer noncritical JS and third-party tags; maps/video load after consent or interaction where appropriate.

## Query and cache rules

- Bound all custom queries, request only necessary fields, prime caches intentionally, and avoid per-card queries.
- Use taxonomy/meta queries only with documented indexes/data volume expectations.
- Cache expensive derived results with clear invalidation on relevant content changes.
- Page/object caching and CDN behavior require hosting approval; authenticated/editor experiences must bypass unsafe caches.

## Measurement matrix

Test Arabic and English versions of Home, development archive/single, property archive/single, an Insight, Contact, and 404 at mobile and desktop profiles. Capture query count/time, transfer by type, image waste, console/network failures, Lighthouse, and WebPageTest or approved equivalent. Retest after third-party integrations.

## Content and technical checks

Unique titles/descriptions, one meaningful H1, descriptive links, valid canonicals/hreflang, correct status codes, redirect chains avoided, sitemap/robots validation, structured-data validation, image alt review, social preview checks, and no staging indexing.

## Approval dependencies

SEO plugin, permalink migration, translated slug strategy, caching/CDN, analytics/consent, image transformation pipeline, and final performance test conditions.

