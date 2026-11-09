<?php

/**
 * Shared presentation helpers.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Render the site identity using the custom logo or site name.
 */
function archdome_site_branding(): void
{
    if (has_custom_logo()) {
        the_custom_logo();

        return;
    }

    $site_name = get_bloginfo('name');

    if (is_front_page()) {
        printf(
            '<span class="site-branding__name">%s</span>',
            esc_html($site_name),
        );

        return;
    }

    printf(
        '<a class="site-branding__name" href="%1$s" rel="home">%2$s</a>',
        esc_url(home_url('/')),
        esc_html($site_name),
    );
}

/**
 * Return the public price label for a property.
 */
function archdome_property_price_label(int $post_id): string
{
    if ((bool) get_post_meta($post_id, '_archdome_price_on_request', true)) {
        return esc_html__('Price on request', 'archdome-portal');
    }

    $price = archdome_sanitize_non_negative_decimal(get_post_meta($post_id, '_archdome_price', true));

    if ($price === '') {
        return '';
    }

    $currency = archdome_sanitize_currency(get_post_meta($post_id, '_archdome_currency', true));
    $formatted = number_format_i18n((float) $price, str_contains($price, '.') ? 2 : 0);

    return trim($formatted . ' ' . $currency);
}

/**
 * Return the current-language contact page URL when configured.
 */
function archdome_contact_url(): string
{
    $pages = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'templates/contact.php',
        'fields' => 'ids',
        'suppress_filters' => false,
    ]);

    if (! $pages) {
        return '';
    }

    return (string) get_permalink($pages[0]);
}

/**
 * Render the location and contextual enquiry sections for a detail page.
 */
function archdome_render_detail_endcap(string $post_type): void
{
    $copy = $post_type === 'property' ? [
        'heading' => esc_html__('Interested in this property?', 'archdome-portal'),
        'text' => esc_html__('Contact the Archdome team to discuss availability and your requirements.', 'archdome-portal'),
    ] : [
        'heading' => esc_html__('Interested in this development?', 'archdome-portal'),
        'text' => esc_html__('Contact the Archdome team to discuss the development and available properties.', 'archdome-portal'),
    ];

    echo render_block([
        'blockName' => 'archdome/map-location',
        'attrs' => ['heading' => esc_html__('Location', 'archdome-portal')],
        'innerBlocks' => [],
        'innerHTML' => '',
        'innerContent' => [],
    ]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    echo render_block([
        'blockName' => 'archdome/inquiry',
        'attrs' => [
            'heading' => $copy['heading'],
            'text' => $copy['text'],
            'buttonLabel' => esc_html__('Enquire now', 'archdome-portal'),
        ],
        'innerBlocks' => [],
        'innerHTML' => '',
        'innerContent' => [],
    ]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
