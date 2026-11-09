# Demo Content

The local site contains a fictional bilingual dataset for design and functional testing.

## Dataset

- 3 Development concepts in Arabic and English.
- 18 Property concepts in Arabic and English, enough to exercise archive pagination and varied listing cards.
- 9 Insights in Arabic and English.
- Translated Locations, Property Types, Amenities, Property Statuses, Development Types, and Development Statuses.
- Six local demo images, featured images, galleries, floor/master plans, pricing, specifications, coordinates, relationships, and enquiry context.
- Demo copy on otherwise-empty About, Locations, and Contact pages.
- Safe example contact settings using `example.com`.

Every generated record and imported attachment has `_archdome_demo_record = 1`. Each object also has a stable `_archdome_demo_key`, making the seeder repeatable without creating additional records.

## Apply or refresh

From the Bedrock project directory:

```powershell
php web\app\themes\archdome_portal\tools\seed-demo-content.php --apply
```

The command updates only marked demo records. Existing user-authored records are preserved. Existing standard-page content is populated only when empty or already managed by the demo seeder.

## Important

All names, descriptions, prices, statistics, coordinates, contact details, and testimonials are fictional. Replace the dataset and review image licensing before production launch. Image provenance is recorded in `assets/demo/SOURCES.md`.
