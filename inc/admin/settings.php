<?php

/**
 * Global Archdome contact settings.
 */
if (! defined('ABSPATH')) {
    exit;
}

/** Register settings and fields. */
function archdome_register_settings(): void
{
    $settings = [
        'archdome_contact_email' => ['sanitize_email', esc_html__('Contact email', 'archdome-portal')],
        'archdome_contact_phone' => ['sanitize_text_field', esc_html__('Contact phone', 'archdome-portal')],
        'archdome_office_address' => ['sanitize_textarea_field', esc_html__('Office address', 'archdome-portal')],
        'archdome_whatsapp_url' => ['esc_url_raw', esc_html__('WhatsApp URL', 'archdome-portal')],
        'archdome_facebook_url' => ['esc_url_raw', esc_html__('Facebook URL', 'archdome-portal')],
        'archdome_instagram_url' => ['esc_url_raw', esc_html__('Instagram URL', 'archdome-portal')],
        'archdome_linkedin_url' => ['esc_url_raw', esc_html__('LinkedIn URL', 'archdome-portal')],
    ];

    add_settings_section('archdome_contact', esc_html__('Contact details', 'archdome-portal'), '__return_false', 'archdome-settings');

    foreach ($settings as $key => [$sanitize, $label]) {
        register_setting('archdome_settings', $key, ['type' => 'string', 'sanitize_callback' => $sanitize, 'default' => '']);
        add_settings_field($key, $label, 'archdome_render_setting_field', 'archdome-settings', 'archdome_contact', ['key' => $key, 'multiline' => $key === 'archdome_office_address']);
    }
}
add_action('admin_init', 'archdome_register_settings');

/** Add the theme settings screen. */
function archdome_add_settings_page(): void
{
    add_theme_page(
        esc_html__('Archdome settings', 'archdome-portal'),
        esc_html__('Archdome settings', 'archdome-portal'),
        'manage_options',
        'archdome-settings',
        'archdome_render_settings_page',
    );
}
add_action('admin_menu', 'archdome_add_settings_page');

/** Render one setting field. */
function archdome_render_setting_field(array $args): void
{
    $key = sanitize_key($args['key'] ?? '');
    $value = (string) get_option($key, '');

    if (! empty($args['multiline'])) {
        printf('<textarea class="large-text" rows="4" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr($key), esc_textarea($value));

        return;
    }

    printf('<input class="regular-text" id="%1$s" name="%1$s" type="text" value="%2$s">', esc_attr($key), esc_attr($value));
}

/** Render the settings page. */
function archdome_render_settings_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Archdome settings', 'archdome-portal'); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields('archdome_settings');
    do_settings_sections('archdome-settings');
    submit_button(); ?>
        </form>
    </div>
    <?php
}
