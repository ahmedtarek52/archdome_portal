<?php

/**
 * Multilingual compatibility and presentation.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register real-estate post types with Polylang.
 *
 * @param  string[]  $post_types  Existing translated post types.
 * @param  bool  $is_settings  Whether the list is for settings display.
 * @return string[]
 */
function archdome_polylang_post_types(array $post_types, bool $is_settings): array
{
    unset($is_settings);

    return array_values(array_unique(array_merge($post_types, ['development', 'property'])));
}
add_filter('pll_get_post_types', 'archdome_polylang_post_types', 10, 2);

/**
 * Register real-estate taxonomies with Polylang.
 *
 * @param  string[]  $taxonomies  Existing translated taxonomies.
 * @param  bool  $is_settings  Whether the list is for settings display.
 * @return string[]
 */
function archdome_polylang_taxonomies(array $taxonomies, bool $is_settings): array
{
    unset($is_settings);

    return array_values(
        array_unique(
            array_merge(
                $taxonomies,
                ['location', 'property_type', 'amenity', 'property_status', 'development_type', 'development_status'],
            ),
        ),
    );
}
add_filter('pll_get_taxonomies', 'archdome_polylang_taxonomies', 10, 2);

/**
 * Keep translated static homepages on Polylang's language-root URL.
 *
 * WordPress otherwise canonicalizes a request such as `/en/` to the physical
 * translated page slug. The language root is the public canonical URL exposed
 * by Polylang and used by the language switcher.
 *
 * @param  string|false  $redirect_url  Proposed canonical URL.
 */
function archdome_keep_language_home_canonical(string|false $redirect_url): string|false
{
    if (! function_exists('pll_languages_list') || ! function_exists('pll_home_url')) {
        return $redirect_url;
    }

    $request_path = wp_parse_url(home_url(wp_unslash($_SERVER['REQUEST_URI'] ?? '/')), PHP_URL_PATH);

    if (! is_string($request_path)) {
        return $redirect_url;
    }

    foreach (pll_languages_list(['fields' => 'slug']) as $language) {
        $home_path = wp_parse_url(pll_home_url($language), PHP_URL_PATH);

        if (is_string($home_path) && untrailingslashit($request_path) === untrailingslashit($home_path)) {
            return false;
        }
    }

    return $redirect_url;
}
add_filter('redirect_canonical', 'archdome_keep_language_home_canonical');

/**
 * Render the configured Polylang language switcher.
 */
function archdome_language_switcher(): void
{
    if (! function_exists('pll_the_languages')) {
        return;
    }

    $languages = pll_the_languages(
        [
            'raw' => 1,
            'hide_if_empty' => 0,
            'hide_current' => 0,
        ],
    );

    if (! is_array($languages) || count($languages) < 2) {
        return;
    }
    ?>
    <nav class="language-switcher" aria-label="<?php esc_attr_e('Languages', 'archdome-portal'); ?>">
        <ul class="language-switcher__list">
            <?php foreach ($languages as $language) { ?>
                <li>
                    <a
                        href="<?php echo esc_url($language['url']); ?>"
                        lang="<?php echo esc_attr($language['locale']); ?>"
                        hreflang="<?php echo esc_attr($language['locale']); ?>"
                        <?php echo $language['current_lang'] ? 'aria-current="page"' : ''; ?>
                    ><?php echo esc_html($language['name']); ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
    <?php
}
