# Archdome Project Overview

Status: planning baseline for review  
Source of truth: `ARCHDOME_PRD.md`  
Last updated: 2026-09-20

## Product vision

Archdome will be an Arabic-first, bilingual real-estate website for prospective buyers, investors, existing clients, brokers, partners, candidates, media visitors, and WordPress editors. It must communicate architectural quality while making developments and available properties easy to discover and enquire about.

## Confirmed goals

- Present a premium, credible, modern real-estate brand.
- Let editors manage structured developments, properties, locations, amenities, statuses, and insights without editing code.
- Compose editorial pages with native Gutenberg blocks and render dynamic output in PHP.
- Support Arabic (`ar`, RTL) as the default and English (`en`, LTR) as the secondary language.
- Meet WCAG 2.2 AA and prioritize Core Web Vitals, SEO, security, and maintainability.
- Keep inquiry context attached to the relevant property or development.

## Phase-one sitemap

```text
Home
About Archdome
Developments
  Development detail
Properties
  Property detail
Locations
Insights
  Insight detail
Contact
Privacy and required legal pages
Search results (provisional)
404
Careers (approval required)
```

Primary navigation should expose Home, About, Developments, Properties, Locations, Insights, and Contact. The exact Arabic and English labels, order, and footer navigation require content approval.

## Primary journeys

| Audience | Entry | Main path | Success event |
|---|---|---|---|
| Buyer | Home, search, campaign | Development/property discovery -> detail -> inquiry | Contextual inquiry accepted |
| Investor | Home, About, Development | Brand evidence -> project data -> contact | Qualified contact request |
| Existing client | Search/direct link | Development updates or contact | Required information found |
| Broker/partner | About/Development | Portfolio review -> contact | Partnership contact |
| Candidate/media | About/Insights | Company information -> contact/careers | Relevant destination reached |
| Editor | WordPress admin | Create/update structured content -> preview -> publish | Correct bilingual page published |

## Functional scope

- Responsive site shell, navigation, footer, content templates, and 404.
- Query-driven archives and singles for `development` and `property`.
- Core Posts presented as Insights unless later evidence justifies a separate CPT.
- Native, dynamic Gutenberg blocks in the `archdome` namespace.
- Accessible property filters with URL state and server-rendered fallback.
- Contextual inquiry forms after provider, routing, retention, and spam controls are approved.
- Language-ready UI strings using `archdome-portal`; translated content relationships depend on an approved multilingual plugin.

## Non-functional targets

- WCAG 2.2 AA for supported templates and interactions.
- Responsive coverage from 320 CSS px through large desktop layouts.
- No critical or high-severity known security defects at release.
- Performance budgets and measured targets are defined in `08-SEO-PERFORMANCE.md`.
- All public interface strings are translatable; Arabic receives first-class content and visual QA.

## Out of scope

Transactions, payments, customer accounts, broker/CRM dashboards, inventory synchronization, native apps, headless/SPA architecture, a custom multilingual database, AI chatbots, recommendation engines, and unsupplied 3D tours.

## Current project state

- Bedrock is installed with WordPress 7.1.1 and PHP requirement `>=8.3`.
- The database is connected.
- `archdome_portal` 0.1.0 is active with the theme foundation, bilingual runtime, structured content registrations, native metadata editing, Gutenberg infrastructure, and five global dynamic blocks. Domain query blocks and feature templates remain next.
- Polylang 3.8 is installed through Composer and active; Arabic (`ar`) and English (`en`) are configured with Arabic as the default.
- The current permalink structure is date-based and still requires the approved migration before launch.
- WP-CLI is unavailable on PATH.
- No Git worktree was found from the theme through the project root.
- The referenced `PROJECT_ARCHITECTURE.md` is not available.

## Success criteria

Release requires approved bilingual content, correct template/block behavior, contextual inquiry completion, editor usability, responsive and browser QA, accessibility conformance, SEO validation, performance budgets met on representative pages, security/privacy approval, staging sign-off, and documented deployment/rollback.

## Assumptions

- The folder name `archdome_portal` and text domain `archdome-portal` are authoritative for implementation, despite the earlier PRD example `archdome`.
- Locations begin as a hierarchical taxonomy; this must be revisited if rich location landing pages require unique modular content.
- Core Posts serve Insights.
- Theme interface language can be completed independently of translated-content relationships.

## Open questions

1. Supply official vector logo variants, brand source files, and social references so the approved provisional colors can be verified.
2. Approve final Arabic and Latin font files and confirm self-hosting/licensing.
3. Confirm careers and site search for phase one.
4. Define inquiry provider, recipients, CRM, consent text, retention, and spam prevention.
5. Define map, analytics, consent, SEO, cache/CDN, and email-delivery services.
6. Provide approved Arabic and English content, legal text, photography, plans, brochures, and content owners.
7. Confirm supported browsers/devices and hosting/deployment workflow.
