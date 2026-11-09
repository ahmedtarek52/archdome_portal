# Content Model

Status: proposed; identifiers are stable unless changed through an approved decision

## Content types

| Record | WordPress type | Public | REST | Archive | Purpose |
|---|---|---:|---:|---:|---|
| Development | `development` CPT | Yes | Yes | Yes | A master real-estate project |
| Property | `property` CPT | Yes | Yes | Yes | A sellable/inquirable unit or type |
| Insight | Core `post` | Yes | Yes | Posts page | News, company, and market editorial |
| Page | Core `page` | Yes | Yes | No | Composed institutional/landing pages |

## Taxonomies

| Taxonomy | Objects | Hierarchical | Public/REST | Notes |
|---|---|---:|---:|---|
| `location` | development, property | Yes | Yes | Provisional taxonomy; promote to CPT only if rich unique pages require it |
| `property_type` | property | Yes | Yes | Controlled types |
| `amenity` | development, property | No | Yes | Shared controlled amenity vocabulary |
| `property_status` | property | No | Yes | Availability; terms require approval |
| `development_type` | development | Yes | Yes | Controlled project types |
| `development_status` | development | No | Yes | Project lifecycle; terms require approval |

Taxonomy translations and cross-language term relationships belong to the selected multilingual plugin.

## Development fields

| Key | Type | Required | Validation/source | REST/public use |
|---|---|---:|---|---|
| Core title/content/excerpt | core | Title yes | WordPress editor | Yes |
| Featured image | attachment | Recommended | Image attachment | Yes |
| `_archdome_gallery_ids` | integer array | No | Existing image attachment IDs, unique/order preserved | Yes/rendered gallery |
| `_archdome_key_stats` | structured array | No | Bounded label/value rows; sanitize text | Yes/rendered facts |
| `_archdome_master_plan_id` | integer | No | Existing media attachment | Yes |
| `_archdome_brochure_id` | integer | No | Existing approved document attachment | Yes |
| `_archdome_latitude` | decimal string | No | -90..90 | Only when map approved |
| `_archdome_longitude` | decimal string | No | -180..180 | Only when map approved |
| `_archdome_featured` | boolean | No | Boolean | Queryable selection |
| `_archdome_inquiry_label` | string | No | Short translated editorial text | Public CTA context |

## Property fields

| Key | Type | Required | Validation/source | REST/public use |
|---|---|---:|---|---|
| Core title/content/excerpt | core | Title yes | WordPress editor | Yes |
| Featured image | attachment | Recommended | Image attachment | Yes |
| `_archdome_development_id` | integer | Recommended | Published/existing `development` ID | Relationship/public |
| `_archdome_price` | decimal string | No | Non-negative normalized decimal | Public only per policy |
| `_archdome_price_on_request` | boolean | No | Boolean; overrides visible numeric price | Public |
| `_archdome_currency` | string enum | No | Approved ISO 4217 values | Public |
| `_archdome_bedrooms` | integer | No | Non-negative bounded integer | Public/filterable |
| `_archdome_bathrooms` | decimal string | No | Non-negative, allowed increments | Public/filterable |
| `_archdome_area` | decimal string | No | Positive normalized decimal | Public/filterable |
| `_archdome_area_unit` | enum | No | `sqm` or approved units | Public |
| `_archdome_gallery_ids` | integer array | No | Existing image IDs | Public |
| `_archdome_floor_plan_id` | integer | No | Existing image/PDF attachment | Public |
| `_archdome_featured` | boolean | No | Boolean | Queryable selection |
| `_archdome_reference` | string | No | Unique requirement must be confirmed | Public only per policy |

## Relationships and source-of-truth rules

- A property stores one canonical development ID; developments query related properties. Do not store a reciprocal list.
- Both records may use `location`, but property display should inherit the development location when no explicit property location is assigned. Editors must see this behavior clearly.
- Status/type/amenity values use taxonomy terms, never free-text duplicates in block attributes.
- Blocks store selection and presentation options, not duplicated domain fields.
- A translated record is a distinct post ID connected by the multilingual plugin.

## Admin editing

Use registered post meta with auth callbacks and dedicated meta boxes or suitable native editor panels. Every mutation requires capability and nonce checks, autosave/revision guards, validation, sanitization, and clear errors. Repeaters must have bounded row counts. Media values store attachment IDs, not URLs.

## Global settings

Global contact details, office addresses, social links, default inquiry routing, legal links, and logo variants require a defined settings owner. Recommended implementation is a focused Settings API page with registered options and granular sanitization; multilingual global text depends on the plugin decision. Do not implement until fields and ownership are approved.

## Migration and lifecycle

- Register identifiers early and avoid changing them after content entry.
- Store a content-schema version option for idempotent migrations.
- Rewrite rules flush only on theme/plugin activation or explicit migration, never every request.
- Deleting a development must not cascade-delete properties; show orphaned relationship warnings.
- Export/backup the database and uploads before schema or multilingual migrations.

## Open questions

Pricing visibility/currency, property granularity, statuses/types, rich locations, agent/contact model, brochure privacy, map coordinates, specifications, reference uniqueness, global-settings translations, and revision support for metadata all need business approval.

