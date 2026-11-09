<?php

/**
 * Idempotent local site-content setup.
 *
 * Usage: php tools/setup-site.php --apply
 */
if ('cli' !== PHP_SAPI || ! in_array('--apply', $argv, true)) {
    fwrite(STDERR, "Run from the command line with --apply.\n");
    exit(1);
}

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'archdome.test';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

require dirname(__DIR__, 4) . '/wp/wp-load.php';

if (! function_exists('pll_set_post_language')) {
    fwrite(STDERR, "Polylang must be active before site setup.\n");
    exit(1);
}

/** Ensure one page exists in the requested language. */
function archdome_setup_page(string $title, string $slug, string $language, string $template = 'default', string $status = 'publish'): int
{
    $page_by_path = get_page_by_path($slug, OBJECT, 'page');

    if ($page_by_path instanceof WP_Post && $language === pll_get_post_language($page_by_path->ID)) {
        if ($template !== 'default') {
            update_post_meta($page_by_path->ID, '_wp_page_template', $template);
        }

        return $page_by_path->ID;
    }

    $existing = get_posts([
        'post_type' => 'page',
        'post_status' => ['publish', 'draft', 'private', 'pending'],
        'posts_per_page' => -1,
        'suppress_filters' => true,
    ]);

    foreach ($existing as $page) {
        if ($language === pll_get_post_language($page->ID) && ($slug === $page->post_name || $title === $page->post_title)) {
            if ($template !== 'default') {
                update_post_meta($page->ID, '_wp_page_template', $template);
            }

            return $page->ID;
        }
    }

    $page_id = wp_insert_post([
        'post_type' => 'page',
        'post_status' => $status,
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => '',
    ], true);

    if (is_wp_error($page_id)) {
        throw new RuntimeException($page_id->get_error_message());
    }

    pll_set_post_language($page_id, $language);

    if ($template !== 'default') {
        update_post_meta($page_id, '_wp_page_template', $template);
    }

    return $page_id;
}

/** Link a bilingual pair. */
function archdome_setup_translation_pair(int $arabic_id, int $english_id): void
{
    pll_save_post_translations(['ar' => $arabic_id, 'en' => $english_id]);
}

$pages = [
    'about' => [
        'ar' => archdome_setup_page('عن أركدوم', 'about', 'ar'),
        'en' => archdome_setup_page('About Archdome', 'about-en', 'en'),
    ],
    'locations' => [
        'ar' => archdome_setup_page('المواقع', 'locations', 'ar', 'templates/locations.php'),
        'en' => archdome_setup_page('Locations', 'locations-en', 'en', 'templates/locations.php'),
    ],
    'insights' => [
        'ar' => archdome_setup_page('الأخبار والرؤى', 'insights', 'ar'),
        'en' => archdome_setup_page('Insights', 'insights-en', 'en'),
    ],
    'contact' => [
        'ar' => archdome_setup_page('تواصل معنا', 'contact', 'ar', 'templates/contact.php'),
        'en' => archdome_setup_page('Contact us', 'contact-en', 'en', 'templates/contact.php'),
    ],
];

foreach ($pages as $pair) {
    archdome_setup_translation_pair($pair['ar'], $pair['en']);
}

update_option('page_for_posts', $pages['insights']['ar']);

$front_page_id = (int) get_option('page_on_front');
$front_translations = $front_page_id ? pll_get_post_translations($front_page_id) : [];

if (! empty($front_translations['en']) && get_post_status($front_translations['en']) === 'trash') {
    wp_untrash_post($front_translations['en']);
    wp_update_post(['ID' => $front_translations['en'], 'post_name' => 'home-en']);
}

if (! empty($front_translations['en']) && get_post_status($front_translations['en']) !== 'publish') {
    wp_update_post(['ID' => $front_translations['en'], 'post_status' => 'publish']);
}

/** Return one self-closing dynamic block. */
function archdome_setup_dynamic_block(string $name, array $attributes): string
{
    $attributes = ['align' => 'full'] + $attributes;

    return sprintf(
        '<!-- wp:archdome/%1$s %2$s /-->',
        $name,
        wp_json_encode($attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    );
}

/**
 * Upgrade seeded homepage sections to use their supported full-width canvas.
 */
function archdome_setup_homepage_alignment(int $page_id): bool
{
    if (! get_post_meta($page_id, '_archdome_home_seeded', true)) {
        return false;
    }

    $content = (string) get_post_field('post_content', $page_id);
    $blocks = parse_blocks($content);
    $changed = false;

    foreach ($blocks as &$block) {
        if (str_starts_with((string) ($block['blockName'] ?? ''), 'archdome/') && 'full' !== ($block['attrs']['align'] ?? '')) {
            $block['attrs']['align'] = 'full';
            $changed = true;
        }
    }
    unset($block);

    if (! $changed) {
        return false;
    }

    $result = wp_update_post(['ID' => $page_id, 'post_content' => serialize_blocks($blocks)], true);

    if (is_wp_error($result)) {
        throw new RuntimeException($result->get_error_message());
    }

    return true;
}

/** Seed a useful homepage only when its existing content has no readable text. */
function archdome_setup_homepage(int $page_id, string $language, int $media_id, array $pages): bool
{
    $page = get_post($page_id);

    if (! $page instanceof WP_Post || get_post_meta($page_id, '_archdome_home_seeded', true)) {
        return false;
    }

    if (trim(wp_strip_all_tags(do_blocks($page->post_content))) !== '') {
        return false;
    }

    $base_url = trailingslashit(pll_home_url($language));
    $developments_url = $base_url . 'developments/';
    $properties_url = $base_url . 'properties/';
    $copy = $language === 'ar' ? [
        'eyebrow' => 'أركدوم',
        'hero_heading' => 'اكتشف مساحتك القادمة',
        'hero_text' => 'استكشف المشروعات والوحدات والمواقع المتاحة من خلال تجربة واضحة وسهلة.',
        'explore' => 'استكشف المشروعات',
        'contact' => 'تواصل معنا',
        'about_eyebrow' => 'عن أركدوم',
        'about_heading' => 'طريقة أوضح لاستكشاف العقارات',
        'about_text' => 'تعرّف على المشروعات والوحدات والمواقع، ثم انتقل إلى التفاصيل التي تساعدك على اتخاذ الخطوة التالية.',
        'about_link' => 'اعرف المزيد',
        'developments' => 'مشروعات مختارة',
        'all_developments' => 'عرض كل المشروعات',
        'properties' => 'وحدات مختارة',
        'all_properties' => 'عرض كل الوحدات',
        'locations' => 'استكشف المواقع',
        'insights' => 'آخر الأخبار والرؤى',
        'cta_heading' => 'هل تبحث عن مساحة تناسبك؟',
        'cta_text' => 'تواصل مع فريق أركدوم لمناقشة احتياجاتك والخطوة التالية.',
    ] : [
        'eyebrow' => 'Archdome',
        'hero_heading' => 'Discover your next space',
        'hero_text' => 'Explore available developments, properties, and locations through a clear, focused experience.',
        'explore' => 'Explore developments',
        'contact' => 'Contact us',
        'about_eyebrow' => 'About Archdome',
        'about_heading' => 'A clearer way to explore real estate',
        'about_text' => 'Browse developments, properties, and locations, then move into the detail you need for your next step.',
        'about_link' => 'Learn more',
        'developments' => 'Featured developments',
        'all_developments' => 'View all developments',
        'properties' => 'Featured properties',
        'all_properties' => 'View all properties',
        'locations' => 'Explore locations',
        'insights' => 'Latest insights',
        'cta_heading' => 'Looking for the right space?',
        'cta_text' => 'Contact the Archdome team to discuss your needs and the next step.',
    ];

    $blocks = [
        archdome_setup_dynamic_block('hero', [
            'eyebrow' => $copy['eyebrow'],
            'heading' => $copy['hero_heading'],
            'text' => $copy['hero_text'],
            'mediaId' => $media_id,
            'headingLevel' => 1,
            'primaryLabel' => $copy['explore'],
            'primaryUrl' => $developments_url,
            'secondaryLabel' => $copy['contact'],
            'secondaryUrl' => get_permalink($pages['contact'][$language]),
        ]),
        archdome_setup_dynamic_block('about', [
            'eyebrow' => $copy['about_eyebrow'],
            'heading' => $copy['about_heading'],
            'text' => $copy['about_text'],
            'mediaId' => $media_id,
            'linkLabel' => $copy['about_link'],
            'linkUrl' => get_permalink($pages['about'][$language]),
        ]),
        archdome_setup_dynamic_block('featured-developments', [
            'heading' => $copy['developments'],
            'limit' => 3,
            'linkLabel' => $copy['all_developments'],
            'linkUrl' => $developments_url,
        ]),
        archdome_setup_dynamic_block('featured-properties', [
            'heading' => $copy['properties'],
            'limit' => 3,
            'linkLabel' => $copy['all_properties'],
            'linkUrl' => $properties_url,
        ]),
        archdome_setup_dynamic_block('locations', ['heading' => $copy['locations'], 'limit' => 6]),
        archdome_setup_dynamic_block('insights', ['heading' => $copy['insights'], 'limit' => 3, 'showExcerpt' => true]),
        archdome_setup_dynamic_block('call-to-action', [
            'heading' => $copy['cta_heading'],
            'text' => $copy['cta_text'],
            'primaryLabel' => $copy['contact'],
            'primaryUrl' => get_permalink($pages['contact'][$language]),
            'tone' => 'brand',
        ]),
    ];

    $result = wp_update_post(['ID' => $page_id, 'post_content' => implode("\n\n", $blocks)], true);

    if (is_wp_error($result)) {
        throw new RuntimeException($result->get_error_message());
    }

    update_post_meta($page_id, '_archdome_home_seeded', '1');

    return true;
}

$home_media_id = 0;

if ($front_page_id) {
    foreach (parse_blocks((string) get_post_field('post_content', $front_page_id)) as $block) {
        if ('core/media-text' === ($block['blockName'] ?? '') && ! empty($block['attrs']['mediaId'])) {
            $home_media_id = absint($block['attrs']['mediaId']);
            break;
        }
    }
}

$seeded_homepages = [];
$aligned_homepages = [];

foreach (['ar', 'en'] as $language) {
    if (! empty($front_translations[$language])) {
        $seeded_homepages[$language] = archdome_setup_homepage((int) $front_translations[$language], $language, $home_media_id, $pages);
        $aligned_homepages[$language] = archdome_setup_homepage_alignment((int) $front_translations[$language]);
    }
}

/** Ensure a navigation menu and its items exist. */
function archdome_setup_menu(string $name, string $language, array $items): int
{
    $menu = wp_get_nav_menu_object($name);
    $menu_id = $menu ? (int) $menu->term_id : wp_create_nav_menu($name);

    if (is_wp_error($menu_id)) {
        throw new RuntimeException($menu_id->get_error_message());
    }

    pll_set_term_language($menu_id, $language);

    if (! wp_get_nav_menu_items($menu_id)) {
        foreach ($items as $item) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
            ]);
        }
    }

    return $menu_id;
}

$development_path = ltrim((string) wp_parse_url(get_post_type_archive_link('development'), PHP_URL_PATH), '/');
$property_path = ltrim((string) wp_parse_url(get_post_type_archive_link('property'), PHP_URL_PATH), '/');

$menu_items = [
    'ar' => [
        ['title' => 'الرئيسية', 'url' => pll_home_url('ar')],
        ['title' => 'عن أركدوم', 'url' => get_permalink($pages['about']['ar'])],
        ['title' => 'المشروعات', 'url' => home_url('/' . $development_path)],
        ['title' => 'الوحدات', 'url' => home_url('/' . $property_path)],
        ['title' => 'المواقع', 'url' => get_permalink($pages['locations']['ar'])],
        ['title' => 'الأخبار والرؤى', 'url' => get_permalink($pages['insights']['ar'])],
        ['title' => 'تواصل معنا', 'url' => get_permalink($pages['contact']['ar'])],
    ],
    'en' => [
        ['title' => 'Home', 'url' => pll_home_url('en')],
        ['title' => 'About', 'url' => get_permalink($pages['about']['en'])],
        ['title' => 'Developments', 'url' => trailingslashit(pll_home_url('en')) . $development_path],
        ['title' => 'Properties', 'url' => trailingslashit(pll_home_url('en')) . $property_path],
        ['title' => 'Locations', 'url' => get_permalink($pages['locations']['en'])],
        ['title' => 'Insights', 'url' => get_permalink($pages['insights']['en'])],
        ['title' => 'Contact', 'url' => get_permalink($pages['contact']['en'])],
    ],
];

$arabic_menu = archdome_setup_menu('Archdome Primary AR', 'ar', $menu_items['ar']);
$english_menu = archdome_setup_menu('Archdome Primary EN', 'en', $menu_items['en']);
pll_save_term_translations(['ar' => $arabic_menu, 'en' => $english_menu]);

$options = PLL()->options;
$options->set('redirect_lang', true);
PLL()->model->clean_languages_cache();
$nav_menus = $options->get('nav_menus');
$nav_menus['archdome_portal'] = [
    'primary' => ['ar' => $arabic_menu, 'en' => $english_menu],
    'footer' => ['ar' => $arabic_menu, 'en' => $english_menu],
];
$options->set('nav_menus', $nav_menus);

$theme_mods = get_option('theme_mods_archdome_portal', []);
$theme_mods['nav_menu_locations'] = ['primary' => $arabic_menu, 'footer' => $arabic_menu];
update_option('theme_mods_archdome_portal', $theme_mods);
flush_rewrite_rules(false);

echo wp_json_encode(['pages' => $pages, 'menus' => ['ar' => $arabic_menu, 'en' => $english_menu], 'seeded_homepages' => $seeded_homepages, 'aligned_homepages' => $aligned_homepages], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
