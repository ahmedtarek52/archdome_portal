# Gutenberg Blocks

Status: proposed inventory and contracts

## Shared contract

All custom blocks use namespace `archdome`, `apiVersion: 3`, `block.json`, a PHP `render.php`, editor-only `index.js`, scoped frontend/editor styles, and optional vanilla `frontend.js`. Registration occurs by scanning an explicit allowlist, not arbitrary directories. Public output must remain useful without JavaScript.

Every block must:

- expose only presentation/query controls appropriate to its responsibility;
- use Inspector Controls and core components with translated labels;
- render through PHP with context-appropriate escaping;
- provide meaningful empty/editor guidance and a quiet public empty state;
- declare assets in metadata so WordPress loads them only when used;
- preserve semantic heading order selected in context rather than hardcoding a visual rank;
- support RTL/LTR through logical CSS and direction-aware icons;
- meet keyboard, focus, contrast, reduced-motion, and responsive requirements.

## Block inventory

| Block | Name | Primary attributes | Data source | JS |
|---|---|---|---|---|
| Hero | `archdome/hero` | heading, text, mediaId, focalPoint, CTA links, overlay | Attributes/media | No |
| About | `archdome/about` | heading, text, mediaId, link | Attributes/media | No |
| Featured developments | `archdome/featured-developments` | heading, selection mode, IDs/limit, CTA | `development` query | No |
| Development grid | `archdome/development-grid` | heading, taxonomy filters, limit/order | `development` query | Optional filter phase |
| Featured properties | `archdome/featured-properties` | heading, IDs/limit, CTA | `property` query | No |
| Property grid | `archdome/property-grid` | heading, filters, limit/order | `property` query | Optional filter phase |
| Property search | `archdome/property-search` | enabled filter set, results target | GET query + property query | Yes, enhancement only |
| Locations | `archdome/locations` | heading, selected terms, layout | `location` terms | No |
| Statistics | `archdome/statistics` | heading/level, optional eyebrow and text, up to eight labelled values with optional icons | Attributes | Numeric counters animate on entry; reduced motion shows final values |
| Amenities | `archdome/amenities` | heading, context/selected terms | `amenity` terms | No |
| Gallery | `archdome/gallery` | heading, media IDs, crop behavior | Media | Only for approved dialog/carousel |
| Master plan | `archdome/master-plan` | heading, source mode/media ID | Context/meta or media | Optional zoom/dialog |
| Testimonials | `archdome/testimonials` | heading, quote items | Attributes; model decision later | No carousel by default |
| Insights | `archdome/insights` | heading, categories, limit, CTA | Core Posts query | No |
| Map/location | `archdome/map-location` | heading, context/source, consent text | Context/meta + provider | Yes after provider approval |
| Call to action | `archdome/call-to-action` | heading, text, primary/secondary links, tone | Attributes | No |
| Contact/inquiry | `archdome/inquiry` | heading, form mode, context source | Approved form adapter | Yes after provider approval |

## Attribute policy

- RichText attributes allow only necessary formats.
- Links store a URL and label; validate protocols and never infer language paths.
- Query blocks cap `limit` (recommended maximum 12) and sanitize order/orderby against allowlists.
- Manual selection stores post IDs; renderers resolve current translated records through the approved plugin adapter where applicable.
- Images store attachment IDs and optional focal point; alt text comes from the media library unless the image is decorative.
- Do not serialize property prices, amenities, statuses, or development relationships into block attributes.

## Rendering and states

| State | Editor | Public |
|---|---|---|
| No configuration | Actionable setup message | Section omitted or purposeful prompt |
| No query results | Explain active criteria | Translated no-results message/CTA |
| Missing media | Visible placeholder and selection control | Neutral placeholder only if useful |
| Invalid relationship | Warning with record ID | Omit dependent detail safely |
| Integration unavailable | Configuration notice | Accessible fallback/contact route |
| Loading | Editor spinner only where needed | Server HTML; no initial loading state |

## Registration and compatibility

Block categories and metadata are registered on `init`. Scripts use WordPress-provided packages and generated dependency metadata or an explicit dependency array; no public React bundle is introduced. Dynamic render callbacks must work in Query Loop/editor previews where context is available and fail safely otherwise.

## Acceptance criteria per block

- Inserts and edits without console/PHP errors.
- Saved attributes match the documented schema and deprecated versions migrate explicitly.
- Frontend output matches editor intent, with no raw unescaped values.
- Empty/missing data does not break layout.
- Keyboard and screen-reader semantics are documented and tested.
- CSS passes LTR/RTL review at supported widths.
- Assets load only when the block appears.
- Representative query count and render time stay inside performance budgets.

## Sequencing

Implement block infrastructure first, then global static blocks, development query blocks, property query/filter blocks, and integration-dependent blocks last. Maps and inquiry remain blocked until providers/privacy are approved.
