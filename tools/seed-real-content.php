<?php

/**
 * Import the verified public ArchDome content summarized in real-data.md.
 *
 * This importer intentionally omits unverified prices, availability, delivery
 * dates, construction percentages, people, email, and exact office address.
 * Existing demo posts are moved to draft so they remain recoverable.
 *
 * Usage: php tools/seed-real-content.php --apply
 */

if ('cli' !== PHP_SAPI || ! in_array('--apply', $argv, true)) {
    fwrite(STDERR, "Run from the command line with --apply.\n");
    exit(1);
}

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'archdome.test';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

require dirname(__DIR__, 4) . '/wp/wp-load.php';

if (! function_exists('pll_set_post_language')) {
    fwrite(STDERR, "Polylang must be active before real content is imported.\n");
    exit(1);
}

/** Find an existing record managed by this importer. */
function archdome_real_find_post(string $key, string $post_type): int
{
    $ids = get_posts([
        'post_type' => $post_type,
        'post_status' => ['publish', 'draft', 'private', 'pending'],
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_archdome_real_key',
        'meta_value' => $key,
        'suppress_filters' => true,
    ]);

    return $ids ? (int) $ids[0] : 0;
}

/** Create or refresh one real-content record. */
function archdome_real_upsert_post(string $key, string $post_type, string $language, array $data): int
{
    $post_id = archdome_real_find_post($key, $post_type);
    $payload = array_merge([
        'ID' => $post_id,
        'post_type' => $post_type,
        'post_status' => 'publish',
        'post_excerpt' => '',
        'post_content' => '',
    ], $data);
    $result = $post_id ? wp_update_post($payload, true) : wp_insert_post($payload, true);

    if (is_wp_error($result)) {
        throw new RuntimeException($result->get_error_message());
    }

    $post_id = (int) $result;
    update_post_meta($post_id, '_archdome_real_key', $key);
    update_post_meta($post_id, '_archdome_content_source', 'Public Facebook and LinkedIn content summarized in real-data.md');
    pll_set_post_language($post_id, $language);

    return $post_id;
}

/** Find a taxonomy term managed by this importer. */
function archdome_real_find_term(string $key, string $taxonomy): int
{
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'number' => 1,
        'meta_key' => '_archdome_real_key',
        'meta_value' => $key,
        'lang' => '',
    ]);

    return ! is_wp_error($terms) && $terms ? (int) $terms[0]->term_id : 0;
}

/** Create or refresh one translated taxonomy term. */
function archdome_real_upsert_term(string $key, string $taxonomy, string $language, array $copy): int
{
    [$name, $slug, $description] = array_pad($copy, 3, '');
    $term_id = archdome_real_find_term($key, $taxonomy);
    $result = $term_id
        ? wp_update_term($term_id, $taxonomy, compact('name', 'slug', 'description'))
        : wp_insert_term($name, $taxonomy, compact('slug', 'description'));

    if (is_wp_error($result)) {
        throw new RuntimeException($result->get_error_message());
    }

    $term_id = (int) $result['term_id'];
    update_term_meta($term_id, '_archdome_real_key', $key);
    pll_set_term_language($term_id, $language);

    return $term_id;
}

/** Create a linked Arabic and English term pair. */
function archdome_real_term_pair(string $key, string $taxonomy, array $copy): array
{
    $pair = [
        'ar' => archdome_real_upsert_term($key . '-ar', $taxonomy, 'ar', $copy['ar']),
        'en' => archdome_real_upsert_term($key . '-en', $taxonomy, 'en', $copy['en']),
    ];
    pll_save_term_translations($pair);

    return $pair;
}

/** Wrap trusted importer copy in a paragraph block. */
function archdome_real_paragraph(string $text): string
{
    return '<!-- wp:paragraph --><p>' . esc_html($text) . '</p><!-- /wp:paragraph -->';
}

$terms = [
    'new_cairo' => archdome_real_term_pair('new-cairo', 'location', [
        'ar' => ['القاهرة الجديدة', 'new-cairo-ar', 'السوق الرئيسي لمشروعات أركدوم السكنية والاستثمارية.'],
        'en' => ['New Cairo', 'new-cairo-en', 'The primary market for ArchDome residential and investment developments.'],
    ]),
    'beit_al_watan' => archdome_real_term_pair('beit-al-watan', 'location', [
        'ar' => ['بيت الوطن، القاهرة الجديدة', 'beit-al-watan-ar', 'منطقة تطوير رئيسية لمشروعات أركدوم في القاهرة الجديدة.'],
        'en' => ['Beit Al Watan, New Cairo', 'beit-al-watan-en', 'A principal ArchDome development area in New Cairo.'],
    ]),
    'north_house' => archdome_real_term_pair('north-house', 'location', [
        'ar' => ['النورث هاوس، القاهرة الجديدة', 'north-house-ar', 'منطقة سكنية في القاهرة الجديدة.'],
        'en' => ['North House, New Cairo', 'north-house-en', 'A residential area in New Cairo.'],
    ]),
    'residential' => archdome_real_term_pair('residential', 'development_type', [
        'ar' => ['سكني', 'residential-ar'],
        'en' => ['Residential', 'residential-en'],
    ]),
    'sold_out' => archdome_real_term_pair('sold-out', 'development_status', [
        'ar' => ['تم البيع بالكامل', 'sold-out-ar'],
        'en' => ['Sold out', 'sold-out-en'],
    ]),
];

$projects = [
    'f49' => [
        'location' => 'beit_al_watan',
        'ar' => [
            'title' => 'F49 — الحي الخامس، بيت الوطن',
            'slug' => 'f49-beit-al-watan-ar',
            'excerpt' => 'مشروع سكني واستثماري بواجهة بحرية وموقع مرتفع في الحي الخامس ببيت الوطن.',
            'body' => 'يجمع مشروع F49 بين الخصوصية والهدوء والتصميم الحديث وسهولة الوصول. يقع بالقرب من بالم هيلز والنادي الأهلي والعاصمة الإدارية الجديدة. المساحات المنشورة هي 188 و200 و250 مترًا مربعًا، بواقع شقتين في كل دور.',
            'stats' => [['label' => 'كود المشروع', 'value' => 'F49'], ['label' => 'المساحات المنشورة', 'value' => '188، 200، 250 م²'], ['label' => 'الوحدات في الدور', 'value' => 'شقتان'], ['label' => 'الواجهة', 'value' => 'بحرية']],
            'inquiry' => 'استفسر عن مشروع F49',
        ],
        'en' => [
            'title' => 'F49 — Fifth District, Beit Al Watan',
            'slug' => 'f49-beit-al-watan-en',
            'excerpt' => 'A residential and investment development with a north-facing frontage on an elevated Fifth District site.',
            'body' => 'F49 is positioned around privacy, quiet surroundings, modern design, and convenient access. It is near Palm Hills, Al Ahly Club, and the New Administrative Capital. Published unit areas are 188, 200, and 250 square metres, with two apartments per floor.',
            'stats' => [['label' => 'Project code', 'value' => 'F49'], ['label' => 'Published areas', 'value' => '188, 200, 250 m²'], ['label' => 'Units per floor', 'value' => '2 apartments'], ['label' => 'Orientation', 'value' => 'North-facing']],
            'inquiry' => 'Enquire about F49',
        ],
    ],
    'b120' => [
        'location' => 'north_house',
        'status' => 'sold_out',
        'ar' => [
            'title' => 'B120 — النورث هاوس',
            'slug' => 'b120-north-house-ar',
            'excerpt' => 'مشروع سكني في النورث هاوس أعلنت أركدوم بيع جميع وحداته.',
            'body' => 'يقع مشروع B120 في منطقة النورث هاوس بالقاهرة الجديدة. أعلنت أركدوم عبر قنواتها العامة بيع جميع وحدات المشروع بالكامل.',
            'stats' => [['label' => 'كود المشروع', 'value' => 'B120'], ['label' => 'حالة التسويق', 'value' => 'تم البيع بالكامل']],
            'inquiry' => 'تعرّف على مشروعات أركدوم',
        ],
        'en' => [
            'title' => 'B120 — North House',
            'slug' => 'b120-north-house-en',
            'excerpt' => 'A North House residential development publicly announced by ArchDome as fully sold.',
            'body' => 'B120 is a residential development in North House, New Cairo. ArchDome announced through its public channels that all units in the project had been sold.',
            'stats' => [['label' => 'Project code', 'value' => 'B120'], ['label' => 'Marketing status', 'value' => 'Sold out']],
            'inquiry' => 'Explore ArchDome developments',
        ],
    ],
    'd197' => [
        'location' => 'beit_al_watan',
        'ar' => [
            'title' => 'D197 — الحي التكميلي، بيت الوطن',
            'slug' => 'd197-beit-al-watan-ar',
            'excerpt' => 'مشروع في الحي التكميلي ببيت الوطن ظهر ضمن تحديثات التنفيذ العامة لأركدوم.',
            'body' => 'أظهر تحديث تنفيذ عام أعمال نجارة وشدة سقف الدور الثاني في مشروع D197. مرحلة التنفيذ الحالية ونسب الإنجاز ومواعيد التسليم والتوافر تحتاج إلى تأكيد مباشر من أركدوم.',
            'stats' => [['label' => 'كود المشروع', 'value' => 'D197'], ['label' => 'المنطقة', 'value' => 'الحي التكميلي، بيت الوطن']],
            'inquiry' => 'اطلب أحدث معلومات D197',
        ],
        'en' => [
            'title' => 'D197 — Complementary District, Beit Al Watan',
            'slug' => 'd197-beit-al-watan-en',
            'excerpt' => 'A Complementary District project featured in ArchDome public construction updates.',
            'body' => 'A public construction update showed second-floor slab carpentry and formwork at D197. The current stage, completion percentage, delivery timing, and availability require confirmation directly from ArchDome.',
            'stats' => [['label' => 'Project code', 'value' => 'D197'], ['label' => 'District', 'value' => 'Complementary District, Beit Al Watan']],
            'inquiry' => 'Request the latest D197 information',
        ],
    ],
    'd204' => [
        'location' => 'beit_al_watan',
        'ar' => [
            'title' => 'D204 — الحي التكميلي، بيت الوطن',
            'slug' => 'd204-beit-al-watan-ar',
            'excerpt' => 'مشروع في الحي التكميلي ببيت الوطن ظهر ضمن تحديثات التنفيذ العامة لأركدوم.',
            'body' => 'أظهر تحديث تنفيذ عام أعمال الشدة الخشبية للقواعد المسلحة في مشروع D204. مرحلة التنفيذ الحالية ونسب الإنجاز ومواعيد التسليم والتوافر تحتاج إلى تأكيد مباشر من أركدوم.',
            'stats' => [['label' => 'كود المشروع', 'value' => 'D204'], ['label' => 'المنطقة', 'value' => 'الحي التكميلي، بيت الوطن']],
            'inquiry' => 'اطلب أحدث معلومات D204',
        ],
        'en' => [
            'title' => 'D204 — Complementary District, Beit Al Watan',
            'slug' => 'd204-beit-al-watan-en',
            'excerpt' => 'A Complementary District project featured in ArchDome public construction updates.',
            'body' => 'A public construction update showed reinforced-foundation formwork at D204. The current stage, completion percentage, delivery timing, and availability require confirmation directly from ArchDome.',
            'stats' => [['label' => 'Project code', 'value' => 'D204'], ['label' => 'District', 'value' => 'Complementary District, Beit Al Watan']],
            'inquiry' => 'Request the latest D204 information',
        ],
    ],
];

$developments = [];

foreach ($projects as $key => $project) {
    foreach (['ar', 'en'] as $language) {
        $copy = $project[$language];
        $post_id = archdome_real_upsert_post('development-' . $key . '-' . $language, 'development', $language, [
            'post_title' => $copy['title'],
            'post_name' => $copy['slug'],
            'post_excerpt' => $copy['excerpt'],
            'post_content' => archdome_real_paragraph($copy['body']),
        ]);
        wp_set_object_terms($post_id, [$terms[$project['location']][$language]], 'location', false);
        wp_set_object_terms($post_id, [$terms['residential'][$language]], 'development_type', false);
        wp_set_object_terms($post_id, isset($project['status']) ? [$terms[$project['status']][$language]] : [], 'development_status', false);
        update_post_meta($post_id, '_archdome_key_stats', $copy['stats']);
        update_post_meta($post_id, '_archdome_inquiry_label', $copy['inquiry']);
        update_post_meta($post_id, '_archdome_featured', 'f49' === $key || 'b120' === $key);
        delete_post_thumbnail($post_id);
        $developments[$key][$language] = $post_id;
    }
    pll_save_post_translations($developments[$key]);
}

/** Update a page only when it is empty or still owned by the demo seeder. */
function archdome_real_update_page(string $path, string $language, string $content): bool
{
    $page = get_page_by_path($path, OBJECT, 'page');
    $page_id = $page instanceof WP_Post ? (int) (pll_get_post($page->ID, $language) ?: 0) : 0;

    if (! $page_id) {
        return false;
    }

    $current = (string) get_post_field('post_content', $page_id);

    if (trim(wp_strip_all_tags(do_blocks($current))) !== '' && ! get_post_meta($page_id, '_archdome_demo_page_content', true) && ! get_post_meta($page_id, '_archdome_real_page_content', true)) {
        return false;
    }

    wp_update_post(['ID' => $page_id, 'post_content' => $content]);
    delete_post_meta($page_id, '_archdome_demo_page_content');
    update_post_meta($page_id, '_archdome_real_page_content', '1');

    return true;
}

$about_copy = [
    'ar' => '<!-- wp:heading --><h2 class="wp-block-heading">نبني لنترك أثراً يدوم</h2><!-- /wp:heading -->' . archdome_real_paragraph('أركدوم للتطوير العقاري هي شركة متخصصة في تطوير المشروعات السكنية والاستثمارية في القاهرة الجديدة، برؤية هندسية حديثة تهدف إلى تقديم وحدات عالية الجودة وقيمة عقارية تدوم للأجيال.') . '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">قيمنا</h3><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li>الجودة في كل تفصيلة</li><li>الالتزام في التنفيذ</li><li>رؤية هندسية حديثة</li><li>الأمان والخصوصية</li><li>قيمة عقارية تدوم</li><li>مواقع مختارة بعناية</li></ul><!-- /wp:list -->',
    'en' => '<!-- wp:heading --><h2 class="wp-block-heading">Modern engineering. Lasting real-estate value.</h2><!-- /wp:heading -->' . archdome_real_paragraph('ArchDome Developments specializes in residential and investment projects in New Cairo, combining modern engineering, thoughtful design, and dependable execution to create lasting real-estate value.') . '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">What guides our work</h3><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li>Quality in every detail</li><li>Commitment in execution</li><li>Modern engineering vision</li><li>Safety and privacy</li><li>Lasting property value</li><li>Carefully selected locations</li></ul><!-- /wp:list -->',
];

$contact_copy = [
    'ar' => archdome_real_paragraph('تواصل مع فريق أركدوم للاستفسار عن مشروعاتنا السكنية والاستثمارية في القاهرة الجديدة وبيت الوطن. يرجى تأكيد الأسعار والتوافر وخطط السداد ومواعيد التسليم مباشرة مع فريق المبيعات.'),
    'en' => archdome_real_paragraph('Contact the ArchDome team about residential and investment developments in New Cairo and Beit Al Watan. Please confirm current prices, availability, payment plans, and delivery dates directly with the sales team.'),
];

foreach (['ar', 'en'] as $language) {
    archdome_real_update_page('about', $language, $about_copy[$language]);
    archdome_real_update_page('contact', $language, $contact_copy[$language]);
}

/** Refresh only homepages originally created by the project setup tool. */
function archdome_real_update_homepage(int $page_id, string $language): bool
{
    if (! $page_id || ! get_post_meta($page_id, '_archdome_home_seeded', true)) {
        return false;
    }

    $blocks = parse_blocks((string) get_post_field('post_content', $page_id));
    $copy = 'ar' === $language ? [
        'hero' => ['أركدوم للتطوير العقاري', 'نبني لنترك أثراً يدوم', 'مشروعات سكنية واستثمارية في القاهرة الجديدة برؤية هندسية حديثة وقيمة عقارية تدوم.'],
        'about' => ['عن أركدوم', 'دقة في التخطيط والتزام في التنفيذ', 'نطوّر مشروعات في القاهرة الجديدة مع اهتمام بالتصميم الحديث وجودة التنفيذ والأمان والخصوصية.'],
        'developments' => 'مشروعات أركدوم',
        'properties' => 'الوحدات',
        'all_properties' => 'عرض كل الوحدات',
        'cta' => ['هل تبحث عن وحدتك القادمة؟', 'تواصل مع فريق أركدوم لتأكيد أحدث معلومات المشروعات والتوافر.'],
    ] : [
        'hero' => ['ArchDome Developments', 'Modern homes. Lasting value.', 'Residential and investment developments in New Cairo, shaped by modern engineering and dependable execution.'],
        'about' => ['About ArchDome', 'Precision in planning. Commitment in execution.', 'We develop projects in New Cairo with a focus on modern design, construction quality, safety, and privacy.'],
        'developments' => 'ArchDome developments',
        'properties' => 'Properties',
        'all_properties' => 'View all properties',
        'cta' => ['Looking for your next home?', 'Contact the ArchDome team to confirm the latest project information and availability.'],
    ];
    $changed = false;
    $block_names = array_column($blocks, 'blockName');

    if (! in_array('archdome/featured-properties', $block_names, true)) {
        $property_block = parse_blocks(sprintf(
            '<!-- wp:archdome/featured-properties %s /-->',
            wp_json_encode([
                'align' => 'full',
                'heading' => $copy['properties'],
                'limit' => 3,
                'linkLabel' => $copy['all_properties'],
                'linkUrl' => trailingslashit(pll_home_url($language)) . 'properties/',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ))[0];
        $insert_after = array_search('archdome/featured-developments', $block_names, true);
        array_splice($blocks, false === $insert_after ? 2 : $insert_after + 1, 0, [$property_block]);
        $changed = true;
    }

    foreach ($blocks as &$block) {
        $name = $block['blockName'] ?? '';

        if ('archdome/hero' === $name) {
            [$block['attrs']['eyebrow'], $block['attrs']['heading'], $block['attrs']['text']] = $copy['hero'];
            $block['attrs']['mediaId'] = 0;
            $changed = true;
        } elseif ('archdome/about' === $name) {
            [$block['attrs']['eyebrow'], $block['attrs']['heading'], $block['attrs']['text']] = $copy['about'];
            $block['attrs']['mediaId'] = 0;
            $changed = true;
        } elseif ('archdome/featured-developments' === $name) {
            $block['attrs']['heading'] = $copy['developments'];
            $changed = true;
        } elseif ('archdome/featured-properties' === $name) {
            $block['attrs']['heading'] = $copy['properties'];
            $changed = true;
        } elseif ('archdome/call-to-action' === $name) {
            [$block['attrs']['heading'], $block['attrs']['text']] = $copy['cta'];
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
$updated_homepages = [];

if ($front_page_id) {
    foreach (pll_get_post_translations($front_page_id) as $language => $translated_id) {
        $updated_homepages[$language] = archdome_real_update_homepage((int) $translated_id, (string) $language);
    }
}

/** Replace a known placeholder option without overwriting editor-owned content. */
function archdome_real_replace_option(string $key, string $value, array $placeholders = []): bool
{
    $current = (string) get_option($key, '');

    if ($current !== '' && ! in_array($current, $placeholders, true)) {
        return false;
    }

    update_option($key, $value);

    return true;
}

archdome_real_replace_option('archdome_contact_email', '', ['demo@example.com']);
archdome_real_replace_option('archdome_contact_phone', '+20 122 113 0011', ['+20 2 0000 0000']);
archdome_real_replace_option('archdome_office_address', '', ["Demo office — New Cairo\nReplace before production launch"]);
archdome_real_replace_option('archdome_whatsapp_url', 'https://wa.me/201221130011');
archdome_real_replace_option('archdome_facebook_url', 'https://www.facebook.com/archdomedevelopments');
archdome_real_replace_option('archdome_instagram_url', 'https://www.instagram.com/archdomedevelopments/', ['https://example.com/archdome-demo-instagram']);
archdome_real_replace_option('archdome_linkedin_url', '', ['https://example.com/archdome-demo-linkedin']);

update_option('blogname', 'ArchDome Developments');

$demo_ids = get_posts([
    'post_type' => ['development', 'property', 'post'],
    'post_status' => ['publish', 'draft', 'private', 'pending'],
    'posts_per_page' => -1,
    'fields' => 'ids',
    'meta_key' => '_archdome_demo_record',
    'meta_value' => '1',
    'suppress_filters' => true,
]);
$drafted_demo_records = 0;

foreach ($demo_ids as $demo_id) {
    if ('draft' !== get_post_status($demo_id)) {
        wp_update_post(['ID' => (int) $demo_id, 'post_status' => 'draft']);
        ++$drafted_demo_records;
    }
}

flush_rewrite_rules(false);

echo wp_json_encode([
    'developments' => count($developments) * 2,
    'updated_homepages' => $updated_homepages,
    'drafted_demo_records' => $drafted_demo_records,
    'notice' => 'Only verified public facts were imported. Unconfirmed commercial and legal details remain empty.',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
