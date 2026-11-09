# Integrations, Security, and Privacy

Status: controls defined; providers unapproved

## Integration inventory

| Area | Needed decision | Data flow | Default until approved |
|---|---|---|---|
| Multilingual | Polylang vs WPML, editions/workflow | WP content/terms/options -> translation relationships | Theme only is language-ready; no switcher/relationships |
| Inquiry form | Native/provider, fields, recipients | Visitor -> site -> email/CRM | Do not implement submission |
| Email delivery | SMTP/API provider and sender domain | Site -> mail service -> recipients | No production assumption |
| CRM | Vendor, mapping, consent, dedupe | Form -> CRM API | No CRM calls |
| Spam | Honeypot/rate limit/CAPTCHA | Request metadata/content -> protection service | Server validation; external CAPTCHA unapproved |
| Maps | Provider/key/consent | Coordinates/address -> map provider/browser | Text address only |
| Analytics | Platform/events/retention | Browser/server events -> analytics | No tracking |
| Consent | Jurisdiction/categories/platform | User choice -> tag controls/storage | No nonessential tags |
| SEO | Plugin and schema ownership | Content -> metadata/sitemaps | Core output only during foundation |
| Cache/CDN/media | Hosting services/invalidation | Public assets/pages -> edge | No theme assumption |

## Multilingual recommendation

Both Polylang and WPML can own translated content relationships while the theme stays API-driven. Polylang is generally a leaner fit for a native WordPress workflow; WPML may suit teams needing its commercial translation-management ecosystem. Approval must consider licensing, editor workflow, taxonomy/media/options translation, REST/block compatibility, translated slugs, and deployment. Do not install or activate either without approval.

## Threat model and controls

- Unauthorized admin mutations: capability checks, nonces, registered meta auth callbacks.
- Stored/reflected XSS: strict sanitization and late contextual escaping; narrow `wp_kses` allowlists.
- CSRF: purpose-specific nonces plus authorization; nonce alone is not authorization.
- Injection: WordPress query APIs, allowlisted query vars/order values, prepared SQL only when unavoidable.
- Spam/abuse: honeypot/rate limits/provider after approval; generic public responses; server validation.
- Data leakage: server-owned recipients/secrets, restricted logs, no credentials/PII in client code or URLs.
- File abuse: media capability/MIME/size controls and no executable uploads via theme features.
- Redirect/SSRF: allowlisted internal redirects; never fetch arbitrary visitor URLs.
- Enumeration: avoid revealing private content, recipient details, or account existence.
- Dependency risk: Composer lock review, supported versions, vulnerability monitoring, minimal plugins.

## Inquiry data contract (provisional)

Potential fields: name, contact method, phone/email, message, consent, language, related content type/ID, and anti-abuse metadata. Only collect fields justified by the approved workflow. The server resolves record title/reference and destination from trusted data. Define controller/processor roles, legal basis, privacy notice, retention/deletion, access controls, export/deletion workflow, and breach handling before launch.

## Secrets and logging

Secrets live in environment variables or an approved secret manager, never theme source, Git, docs, rendered markup, or editor attributes. Logs redact personal data and tokens, have bounded retention/access, and distinguish operational diagnostics from analytics.

## Security verification

- Static review of input/output boundaries, nonces, capabilities, REST permissions, redirects, uploads, and headers.
- Abuse tests for form replay/rate, oversized/malformed input, invalid IDs, privilege changes, and failed providers.
- Dependency/advisory scan when network/tooling permits.
- Confirm directory indexing/debug display are off outside development and backups are not web-accessible.
- Verify least-privilege service credentials, key rotation, and incident owner.

## Approval checklist

Provider/contract, data fields, data locations, sub-processors, consent/legal copy, retention, deletion/export, recipients, roles, failure path, rate limits, secrets owner, monitoring, and rollback must be approved per integration.

