<?php

/**
 * Seed bilingual, clearly marked demo content for local design testing.
 *
 * Usage: php tools/seed-demo-content.php --apply
 *
 * @package Archdome_Portal
 */

if ('cli' !== PHP_SAPI || ! in_array('--apply', $argv, true)) {
    fwrite(STDERR, "Run from the command line with --apply.\n");
    exit(1);
}

$_SERVER['HTTP_HOST']   = $_SERVER['HTTP_HOST'] ?? 'archdome.test';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

require dirname(__DIR__, 4) . '/wp/wp-load.php';

if (! function_exists('pll_set_post_language')) {
    fwrite(STDERR, "Polylang must be active before demo content is seeded.\n");
    exit(1);
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

/** Find a post previously created by this seeder. */
function archdome_demo_find_post(string $key, string $post_type): int
{
    global $wpdb;

    $post_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT posts.ID
            FROM {$wpdb->posts} AS posts
            INNER JOIN {$wpdb->postmeta} AS demo_meta ON demo_meta.post_id = posts.ID
            WHERE posts.post_type = %s
                AND posts.post_status <> 'trash'
                AND demo_meta.meta_key = '_archdome_demo_key'
                AND demo_meta.meta_value = %s
            ORDER BY posts.ID ASC",
            $post_type,
            $key,
        ),
    );

    if (! $post_ids) {
        return 0;
    }

    foreach (array_slice($post_ids, 1) as $duplicate_id) {
        wp_trash_post((int) $duplicate_id);
    }

    return (int) $post_ids[0];
}

/** Create or refresh a marked demo post. */
function archdome_demo_upsert_post(string $key, string $post_type, string $language, array $data): int
{
    $post_id = archdome_demo_find_post($key, $post_type);
    $payload = array_merge([
        'ID'           => $post_id,
        'post_type'    => $post_type,
        'post_status'  => 'publish',
        'post_excerpt' => '',
        'post_content' => '',
    ], $data);

    $result = $post_id ? wp_update_post($payload, true) : wp_insert_post($payload, true);

    if (is_wp_error($result)) {
        throw new RuntimeException($result->get_error_message());
    }

    $post_id = (int) $result;
    update_post_meta($post_id, '_archdome_demo_key', $key);
    update_post_meta($post_id, '_archdome_demo_record', '1');
    pll_set_post_language($post_id, $language);

    return $post_id;
}

/** Find a marked demo taxonomy term. */
function archdome_demo_find_term(string $key, string $taxonomy): int
{
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'number'     => 1,
        'meta_key'   => '_archdome_demo_key',
        'meta_value' => $key,
        'lang'       => '',
    ]);

    return ! is_wp_error($terms) && $terms ? (int) $terms[0]->term_id : 0;
}

/** Create or refresh a translated demo term. */
function archdome_demo_upsert_term(string $key, string $taxonomy, string $language, string $name, string $slug, string $description = ''): int
{
    $term_id = archdome_demo_find_term($key, $taxonomy);

    if ($term_id) {
        $result = wp_update_term($term_id, $taxonomy, compact('name', 'slug', 'description'));
    } else {
        $result = wp_insert_term($name, $taxonomy, compact('slug', 'description'));
    }

    if (is_wp_error($result)) {
        throw new RuntimeException($result->get_error_message());
    }

    $term_id = (int) $result['term_id'];
    update_term_meta($term_id, '_archdome_demo_key', $key);
    update_term_meta($term_id, '_archdome_demo_record', '1');
    pll_set_term_language($term_id, $language);

    return $term_id;
}

/** Create a linked Arabic/English term pair. */
function archdome_demo_term_pair(string $key, string $taxonomy, array $copy): array
{
    $pair = [
        'ar' => archdome_demo_upsert_term($key . '-ar', $taxonomy, 'ar', $copy['ar'][0], $copy['ar'][1], $copy['ar'][2] ?? ''),
        'en' => archdome_demo_upsert_term($key . '-en', $taxonomy, 'en', $copy['en'][0], $copy['en'][1], $copy['en'][2] ?? ''),
    ];
    pll_save_term_translations($pair);

    return $pair;
}

/** Import a bundled demo image into the WordPress media library once. */
function archdome_demo_import_image(string $key, string $filename, string $title, string $alt, string $credit = ''): int
{
    $existing = archdome_demo_find_post('asset-' . $key, 'attachment');

    if ($existing && file_exists(get_attached_file($existing))) {
        return $existing;
    }

    $source = get_theme_file_path('/assets/demo/' . $filename);

    if (! file_exists($source)) {
        throw new RuntimeException("Missing demo asset: $filename");
    }

    $temporary = wp_tempnam($filename);

    if (! $temporary || ! copy($source, $temporary)) {
        throw new RuntimeException("Unable to prepare demo asset: $filename");
    }

    $attachment_id = media_handle_sideload([
        'name'     => $filename,
        'tmp_name' => $temporary,
    ], 0, $title, ['post_content' => $credit]);

    if (is_wp_error($attachment_id)) {
        @unlink($temporary);
        throw new RuntimeException($attachment_id->get_error_message());
    }

    update_post_meta($attachment_id, '_archdome_demo_key', 'asset-' . $key);
    update_post_meta($attachment_id, '_archdome_demo_record', '1');
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);

    return (int) $attachment_id;
}

/** Assign translated terms to a post. */
function archdome_demo_assign_terms(int $post_id, string $language, array $assignments): void
{
    foreach ($assignments as $taxonomy => $pairs) {
        $term_ids = array_map(static fn(array $pair): int => (int) $pair[$language], $pairs);
        wp_set_object_terms($post_id, $term_ids, $taxonomy, false);
    }
}

/** Populate a page only when it is empty or already managed by this demo seeder. */
function archdome_demo_fill_page(string $path, string $language, string $content): int
{
    $page = get_page_by_path($path, OBJECT, 'page');

    if (! $page instanceof WP_Post) {
        return 0;
    }

    $page_id = (int) (pll_get_post($page->ID, $language) ?: 0);

    if (! $page_id) {
        return 0;
    }

    $current = (string) get_post_field('post_content', $page_id);

    if (trim(wp_strip_all_tags(do_blocks($current))) !== '' && ! get_post_meta($page_id, '_archdome_demo_page_content', true)) {
        return $page_id;
    }

    wp_update_post(['ID' => $page_id, 'post_content' => $content]);
    update_post_meta($page_id, '_archdome_demo_page_content', '1');

    return $page_id;
}

$images = [
    'courtyard' => archdome_demo_import_image('courtyard', 'demo-courtyard.png', 'Demo residential courtyard', 'Landscaped courtyard in a fictional residential development'),
    'interior'  => archdome_demo_import_image('interior', 'demo-interior.png', 'Demo apartment interior', 'Bright open-plan interior in a fictional apartment'),
    'townhouse' => archdome_demo_import_image('townhouse', 'demo-townhouses.png', 'Demo townhouse street', 'Landscaped street in a fictional townhouse community'),
    'building'  => archdome_demo_import_image('building', 'demo-residential-building.jpg', 'Demo residential building', 'Contemporary residential building with a geometric facade', 'Photo by Marc Marken via Pexels: https://www.pexels.com/photo/modern-residential-building-16771573/'),
    'balcony'   => archdome_demo_import_image('balcony', 'demo-balcony-view.webp', 'Demo apartment balcony view', 'Balcony view over a fictional landscaped Cairo community; editorial crop of generated demo artwork'),
    'lounge'    => archdome_demo_import_image('lounge', 'demo-lounge-detail.webp', 'Demo apartment lounge detail', 'Living area in a fictional apartment; editorial crop of generated demo artwork'),
];

/** Replace placeholder homepage media only on pages created by the site seeder. */
function archdome_demo_update_home_media(int $page_id, array $images): bool
{
    if (! get_post_meta($page_id, '_archdome_home_seeded', true)) {
        return false;
    }

    $blocks = parse_blocks((string) get_post_field('post_content', $page_id));
    $changed = false;

    foreach ($blocks as &$block) {
        if (($block['blockName'] ?? '') === 'archdome/hero' && ($block['attrs']['mediaId'] ?? 0) !== $images['courtyard']) {
            $block['attrs']['mediaId'] = $images['courtyard'];
            $changed = true;
        }

        if (($block['blockName'] ?? '') === 'archdome/about' && ($block['attrs']['mediaId'] ?? 0) !== $images['interior']) {
            $block['attrs']['mediaId'] = $images['interior'];
            $changed = true;
        }
    }
    unset($block);

    if ($changed) {
        wp_update_post(['ID' => $page_id, 'post_content' => serialize_blocks($blocks)]);
    }

    return $changed;
}

$front_page_id = (int) get_option('page_on_front');

if ($front_page_id) {
    foreach (pll_get_post_translations($front_page_id) as $translated_home_id) {
        archdome_demo_update_home_media((int) $translated_home_id, $images);
    }
}

$floor_plan = get_posts([
    'post_type'        => 'attachment',
    'post_status'      => 'inherit',
    'posts_per_page'   => 1,
    'fields'           => 'ids',
    's'                => '800842047',
    'suppress_filters' => true,
]);
$floor_plan_id = $floor_plan ? (int) $floor_plan[0] : 0;

$terms = [
    'locations' => [
        'new-cairo' => archdome_demo_term_pair('new-cairo', 'location', [
            'ar' => ['القاهرة الجديدة', 'demo-new-cairo-ar', 'منطقة تجريبية للمحتوى في شرق القاهرة.'],
            'en' => ['New Cairo', 'demo-new-cairo-en', 'Demo location content for eastern Cairo.'],
        ]),
        'sheikh-zayed' => archdome_demo_term_pair('sheikh-zayed', 'location', [
            'ar' => ['الشيخ زايد', 'demo-sheikh-zayed-ar', 'منطقة تجريبية للمحتوى في غرب القاهرة.'],
            'en' => ['Sheikh Zayed', 'demo-sheikh-zayed-en', 'Demo location content for western Cairo.'],
        ]),
        'north-coast' => archdome_demo_term_pair('north-coast', 'location', [
            'ar' => ['الساحل الشمالي', 'demo-north-coast-ar', 'وجهة ساحلية تجريبية للاختبارات البصرية.'],
            'en' => ['North Coast', 'demo-north-coast-en', 'Demo coastal destination for visual testing.'],
        ]),
    ],
    'property_types' => [
        'apartment' => archdome_demo_term_pair('apartment', 'property_type', ['ar' => ['شقة', 'demo-apartment-ar'], 'en' => ['Apartment', 'demo-apartment-en']]),
        'duplex'    => archdome_demo_term_pair('duplex', 'property_type', ['ar' => ['دوبلكس', 'demo-duplex-ar'], 'en' => ['Duplex', 'demo-duplex-en']]),
        'townhouse' => archdome_demo_term_pair('townhouse', 'property_type', ['ar' => ['تاون هاوس', 'demo-townhouse-ar'], 'en' => ['Townhouse', 'demo-townhouse-en']]),
        'villa'     => archdome_demo_term_pair('villa', 'property_type', ['ar' => ['فيلا', 'demo-villa-ar'], 'en' => ['Villa', 'demo-villa-en']]),
    ],
    'amenities' => [
        'pool'     => archdome_demo_term_pair('pool', 'amenity', ['ar' => ['حمام سباحة', 'demo-pool-ar'], 'en' => ['Swimming pool', 'demo-pool-en']]),
        'gym'      => archdome_demo_term_pair('gym', 'amenity', ['ar' => ['صالة رياضية', 'demo-gym-ar'], 'en' => ['Fitness studio', 'demo-gym-en']]),
        'gardens'  => archdome_demo_term_pair('gardens', 'amenity', ['ar' => ['حدائق', 'demo-gardens-ar'], 'en' => ['Landscaped gardens', 'demo-gardens-en']]),
        'security' => archdome_demo_term_pair('security', 'amenity', ['ar' => ['أمن', 'demo-security-ar'], 'en' => ['Community security', 'demo-security-en']]),
    ],
    'property_statuses' => [
        'available' => archdome_demo_term_pair('available', 'property_status', ['ar' => ['متاح', 'demo-available-ar'], 'en' => ['Available', 'demo-available-en']]),
        'ready'     => archdome_demo_term_pair('ready', 'property_status', ['ar' => ['جاهز', 'demo-ready-ar'], 'en' => ['Ready to move', 'demo-ready-en']]),
    ],
    'development_types' => [
        'residential' => archdome_demo_term_pair('residential', 'development_type', ['ar' => ['سكني', 'demo-residential-ar'], 'en' => ['Residential', 'demo-residential-en']]),
        'mixed'       => archdome_demo_term_pair('mixed', 'development_type', ['ar' => ['متعدد الاستخدامات', 'demo-mixed-ar'], 'en' => ['Mixed use', 'demo-mixed-en']]),
    ],
    'development_statuses' => [
        'selling'  => archdome_demo_term_pair('selling', 'development_status', ['ar' => ['متاح للبيع', 'demo-selling-ar'], 'en' => ['Now selling', 'demo-selling-en']]),
        'upcoming' => archdome_demo_term_pair('upcoming', 'development_status', ['ar' => ['قريباً', 'demo-upcoming-ar'], 'en' => ['Upcoming', 'demo-upcoming-en']]),
    ],
];

$development_specs = [
    'garden-residence' => [
        'image' => 'courtyard', 'location' => 'new-cairo', 'type' => 'residential', 'status' => 'selling',
        'lat' => '30.0131', 'lng' => '31.4913',
        'ar' => ['حدائق ريزيدنس — تجريبي', 'مجتمع سكني تجريبي حول حدائق وممرات هادئة.', 'تصميم تجريبي يعرض كيفية تقديم معلومات المشروع ومساحاته وخدماته. جميع الأسماء والتفاصيل المعروضة لأغراض الاختبار فقط.'],
        'en' => ['Garden Residence — Demo', 'A fictional residential community shaped around gardens and quiet walkways.', 'Demo content showing how a development story, spaces, and amenities appear. All names and details are for testing only.'],
    ],
    'palm-courtyard' => [
        'image' => 'townhouse', 'location' => 'sheikh-zayed', 'type' => 'residential', 'status' => 'selling',
        'lat' => '30.0498', 'lng' => '30.9764',
        'ar' => ['بالم كورت — تجريبي', 'مجموعة من المنازل التجريبية بشوارع خضراء وتفاصيل معمارية دافئة.', 'محتوى تجريبي لاختبار صفحات المشروع والعلاقات بين المشروعات والوحدات.'],
        'en' => ['Palm Courtyard — Demo', 'A fictional collection of homes with green streets and warm architectural detail.', 'Demo content for testing development pages and the relationship between developments and properties.'],
    ],
    'coastline-terraces' => [
        'image' => 'building', 'location' => 'north-coast', 'type' => 'mixed', 'status' => 'upcoming',
        'lat' => '30.9530', 'lng' => '28.8990',
        'ar' => ['تراسات الساحل — تجريبي', 'وجهة ساحلية تجريبية بتكوينات معمارية مفتوحة وإطلالات واسعة.', 'يختبر هذا السجل حالات المشروعات القادمة والمحتوى المختصر وغياب بعض البيانات الاختيارية.'],
        'en' => ['Coastline Terraces — Demo', 'A fictional coastal destination with open architecture and broad views.', 'This record tests upcoming-development states, concise content, and optional-data handling.'],
    ],
];

$developments = [];

foreach ($development_specs as $key => $spec) {
    foreach (['ar', 'en'] as $language) {
        [$title, $excerpt, $text] = $spec[$language];
        $post_id = archdome_demo_upsert_post('development-' . $key . '-' . $language, 'development', $language, [
            'post_title'   => $title,
            'post_name'    => 'demo-' . $key . '-' . $language,
            'post_excerpt' => $excerpt,
            'post_content' => '<!-- wp:paragraph --><p>' . esc_html($text) . '</p><!-- /wp:paragraph -->',
        ]);
        set_post_thumbnail($post_id, $images[$spec['image']]);
        update_post_meta($post_id, '_archdome_gallery_ids', array_values($images));
        update_post_meta($post_id, '_archdome_key_stats', $language === 'ar' ? [
            ['label' => 'أنماط الوحدات', 'value' => '4'], ['label' => 'مساحات خضراء', 'value' => '35%'], ['label' => 'الحالة', 'value' => 'تجريبي'],
        ] : [
            ['label' => 'Home types', 'value' => '4'], ['label' => 'Landscape', 'value' => '35%'], ['label' => 'Status', 'value' => 'Demo'],
        ]);
        update_post_meta($post_id, '_archdome_latitude', $spec['lat']);
        update_post_meta($post_id, '_archdome_longitude', $spec['lng']);
        update_post_meta($post_id, '_archdome_featured', '1');
        update_post_meta($post_id, '_archdome_inquiry_label', $language === 'ar' ? 'استفسر عن المشروع' : 'Ask about this development');
        if ($floor_plan_id) {
            update_post_meta($post_id, '_archdome_master_plan_id', $floor_plan_id);
        }
        archdome_demo_assign_terms($post_id, $language, [
            'location'           => [$terms['locations'][$spec['location']]],
            'amenity'            => array_values($terms['amenities']),
            'development_type'   => [$terms['development_types'][$spec['type']]],
            'development_status' => [$terms['development_statuses'][$spec['status']]],
        ]);
        $developments[$key][$language] = $post_id;
    }
    pll_save_post_translations($developments[$key]);
}

$property_names = [
    ['Courtyard Apartment', 'شقة الفناء', 'apartment', 2, 2, 148, 6850000, 'garden-residence'],
    ['Parkside Duplex', 'دوبلكس الحديقة', 'duplex', 3, 3, 224, 9800000, 'garden-residence'],
    ['Garden Townhouse', 'تاون هاوس الحديقة', 'townhouse', 3, 3, 238, 11250000, 'palm-courtyard'],
    ['Skyline Residence', 'مسكن سكاي لاين', 'apartment', 3, 2, 176, 7900000, 'garden-residence'],
    ['Palm Villa', 'فيلا بالم', 'villa', 4, 4, 340, 16800000, 'palm-courtyard'],
    ['Terrace Penthouse', 'بنتهاوس التراس', 'apartment', 3, 3, 212, 12400000, 'coastline-terraces'],
    ['Poolside Apartment', 'شقة بجوار المسبح', 'apartment', 1, 1, 92, 4650000, 'garden-residence'],
    ['Corner Townhouse', 'تاون هاوس زاوية', 'townhouse', 4, 4, 265, 13800000, 'palm-courtyard'],
    ['Family Duplex', 'دوبلكس عائلي', 'duplex', 4, 3, 248, 10750000, 'garden-residence'],
    ['Garden Villa', 'فيلا الحديقة', 'villa', 5, 5, 410, 21500000, 'palm-courtyard'],
    ['City Apartment', 'شقة المدينة', 'apartment', 2, 2, 132, 6100000, 'garden-residence'],
    ['Roof Residence', 'مسكن السطح', 'apartment', 3, 3, 198, 11600000, 'coastline-terraces'],
    ['Courtyard Studio', 'استوديو الفناء', 'apartment', 1, 1, 68, 3350000, 'garden-residence'],
    ['Terrace Apartment', 'شقة التراس', 'apartment', 2, 2, 154, 7350000, 'garden-residence'],
    ['Family Garden Home', 'منزل عائلي بإطلالة على الحديقة', 'duplex', 3, 3, 216, 9250000, 'palm-courtyard'],
    ['Coastal View Residence', 'مسكن بإطلالة ساحلية', 'apartment', 2, 2, 142, 8450000, 'coastline-terraces'],
    ['Sunlit Corner Villa', 'فيلا الركن المشمس', 'villa', 4, 4, 328, 18750000, 'palm-courtyard'],
    ['Garden View Apartment', 'شقة بإطلالة على الحديقة', 'apartment', 2, 2, 138, 6650000, 'garden-residence'],
];
$image_keys = array_keys($images);
$properties = [];

foreach ($property_names as $index => [$english_name, $arabic_name, $type, $bedrooms, $bathrooms, $area, $price, $development_key]) {
    foreach (['ar', 'en'] as $language) {
        $title = ($language === 'ar' ? $arabic_name : $english_name) . ' — ' . ($language === 'ar' ? 'تجريبي' : 'Demo');
        $text  = $language === 'ar'
            ? 'وحدة سكنية تجريبية لاختبار عرض السعر والمواصفات والصور والمرافق ونموذج الاستفسار. البيانات غير حقيقية.'
            : 'A fictional home used to test pricing, specifications, galleries, amenities, related records, and enquiry context.';
        $key = sanitize_title($english_name);
        $post_id = archdome_demo_upsert_post('property-' . $key . '-' . $language, 'property', $language, [
            'post_title'   => $title,
            'post_name'    => 'demo-' . $key . '-' . $language,
            'post_excerpt' => $language === 'ar' ? "{$bedrooms} غرف نوم · {$area} متر مربع · بيانات تجريبية" : "{$bedrooms} bedrooms · {$area} sqm · Demo listing",
            'post_content' => '<!-- wp:paragraph --><p>' . esc_html($text) . '</p><!-- /wp:paragraph -->',
        ]);
        $image_id = $images[$image_keys[$index % count($image_keys)]];
        set_post_thumbnail($post_id, $image_id);
        update_post_meta($post_id, '_archdome_development_id', $developments[$development_key][$language]);
        update_post_meta($post_id, '_archdome_price', (string) $price);
        update_post_meta($post_id, '_archdome_currency', 'EGP');
        update_post_meta($post_id, '_archdome_bedrooms', $bedrooms);
        update_post_meta($post_id, '_archdome_bathrooms', (string) $bathrooms);
        update_post_meta($post_id, '_archdome_area', (string) $area);
        update_post_meta($post_id, '_archdome_area_unit', 'sqm');
        update_post_meta($post_id, '_archdome_gallery_ids', array_values($images));
        update_post_meta($post_id, '_archdome_featured', $index < 6 ? '1' : '0');
        update_post_meta($post_id, '_archdome_reference', sprintf('DEMO-%03d-%s', $index + 1, strtoupper($language)));
        if ($floor_plan_id) {
            update_post_meta($post_id, '_archdome_floor_plan_id', $floor_plan_id);
        }
        $location_key = $development_specs[$development_key]['location'];
        archdome_demo_assign_terms($post_id, $language, [
            'location'        => [$terms['locations'][$location_key]],
            'property_type'   => [$terms['property_types'][$type]],
            'property_status' => [$terms['property_statuses'][$index % 3 === 0 ? 'ready' : 'available']],
            'amenity'         => [$terms['amenities']['pool'], $terms['amenities']['gardens'], $terms['amenities']['security']],
        ]);
        $properties[$key][$language] = $post_id;
    }
    pll_save_post_translations($properties[$key]);
}

$insight_specs = [
    ['Designing communities around daily life', 'تصميم مجتمعات تتمحور حول الحياة اليومية'],
    ['How landscape shapes a residential experience', 'كيف تصنع المساحات الخضراء تجربة سكنية أفضل'],
    ['A practical guide to comparing floor plans', 'دليل عملي لمقارنة مخططات الوحدات'],
    ['What to look for on a property visit', 'ما الذي تبحث عنه عند زيارة عقار'],
    ['Understanding usable area and shared space', 'فهم المساحة القابلة للاستخدام والمساحات المشتركة'],
    ['Choosing between an apartment and a townhouse', 'الاختيار بين الشقة والتاون هاوس'],
    ['Planning a home around natural light', 'تخطيط المنزل للاستفادة من الضوء الطبيعي'],
    ['Questions to ask before choosing a community', 'أسئلة مهمة قبل اختيار مجتمع سكني'],
    ['Making the most of a compact floor plan', 'الاستفادة من المساحات الصغيرة في مخطط الوحدة'],
];
$insights = [];

foreach ($insight_specs as $index => [$english_title, $arabic_title]) {
    $slug = sanitize_title($english_title);
    foreach (['ar', 'en'] as $language) {
        $title = ($language === 'ar' ? $arabic_title : $english_title) . ' — ' . ($language === 'ar' ? 'تجريبي' : 'Demo');
        $body = $language === 'ar'
            ? '<!-- wp:paragraph --><p>مقال تجريبي لاختبار صفحات الأخبار وتنسيق النصوص والصور والروابط. هذا المحتوى ليس نصيحة عقارية أو مادة معتمدة للنشر.</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">نقطة اختبار رئيسية</h2><!-- /wp:heading --><!-- wp:paragraph --><p>استخدم هذه الصفحة لمراجعة طول السطر والمسافات والعناوين وسلوك الصور على مختلف الشاشات.</p><!-- /wp:paragraph -->'
            : '<!-- wp:paragraph --><p>A demo article for testing insight layouts, typography, imagery, and links. This is not property advice or approved production copy.</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">A useful test heading</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Use this page to review line length, spacing, headings, and media behavior across screen sizes.</p><!-- /wp:paragraph -->';
        $post_id = archdome_demo_upsert_post('insight-' . $slug . '-' . $language, 'post', $language, [
            'post_title'   => $title,
            'post_name'    => 'demo-' . $slug . '-' . $language,
            'post_excerpt' => $language === 'ar' ? 'ملخص تجريبي قصير لمراجعة بطاقة المقال.' : 'A short demo summary for reviewing the insight card.',
            'post_content' => $body,
            'post_date'    => sprintf('2026-%02d-15 10:00:00', 3 + $index),
        ]);
        set_post_thumbnail($post_id, $images[$image_keys[$index % count($image_keys)]]);
        $insights[$slug][$language] = $post_id;
    }
    pll_save_post_translations($insights[$slug]);
}

$page_copy = [
    'about' => [
        'ar' => '<!-- wp:heading --><h2 class="wp-block-heading">هذه صفحة تجريبية عن أركدوم</h2><!-- /wp:heading --><!-- wp:paragraph --><p>يعرض هذا النص التجريبي كيف تبدو قصة الشركة ورسالتها وقيمها داخل نظام التصميم. استبدله بالمحتوى العربي المعتمد قبل الإطلاق.</p><!-- /wp:paragraph --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">الرؤية</h3><!-- /wp:heading --><!-- wp:paragraph --><p>صياغة تجريبية لاختبار المكونات والمسافات.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">القيم</h3><!-- /wp:heading --><!-- wp:paragraph --><p>وضوح وجودة واهتمام بالتفاصيل — للاختبار فقط.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->',
        'en' => '<!-- wp:heading --><h2 class="wp-block-heading">This is a demo About page</h2><!-- /wp:heading --><!-- wp:paragraph --><p>This placeholder shows how a company story, mission, and values sit within the design system. Replace it with approved English copy before launch.</p><!-- /wp:paragraph --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Vision</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Demo wording for testing components and spacing.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Values</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Clarity, quality, and attention to detail — for testing only.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->',
    ],
    'locations' => [
        'ar' => '<!-- wp:paragraph --><p>استكشف مواقع تجريبية مرتبطة بالمشروعات والوحدات. جميع الأوصاف والبيانات هنا مخصصة لاختبار التصميم.</p><!-- /wp:paragraph -->',
        'en' => '<!-- wp:paragraph --><p>Explore demo locations connected to developments and properties. All descriptions and data are included for design testing.</p><!-- /wp:paragraph -->',
    ],
    'contact' => [
        'ar' => '<!-- wp:paragraph --><p>هذه بيانات تواصل تجريبية. استخدم الصفحة لاختبار سياق الاستفسار وتخطيط معلومات المكتب.</p><!-- /wp:paragraph -->',
        'en' => '<!-- wp:paragraph --><p>These are demo contact details. Use this page to test enquiry context and the office-information layout.</p><!-- /wp:paragraph -->',
    ],
];

foreach ($page_copy as $path => $translations) {
    foreach ($translations as $language => $content) {
        archdome_demo_fill_page($path, $language, $content);
    }
}

update_option('archdome_contact_email', 'demo@example.com');
update_option('archdome_contact_phone', '+20 2 0000 0000');
update_option('archdome_office_address', "Demo office — New Cairo\nReplace before production launch");
update_option('archdome_instagram_url', 'https://example.com/archdome-demo-instagram');
update_option('archdome_linkedin_url', 'https://example.com/archdome-demo-linkedin');

flush_rewrite_rules(false);

echo wp_json_encode([
    'images'       => $images,
    'developments' => count($developments) * 2,
    'properties'   => count($properties) * 2,
    'insights'     => count($insights) * 2,
    'notice'       => 'All seeded records are fictional and marked with _archdome_demo_record.',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
