<?php

/**
 * Native admin fields for real-estate records.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register record-detail meta boxes.
 */
function archdome_register_meta_boxes(): void
{
    add_meta_box(
        'archdome-development-details',
        esc_html__('Development details', 'archdome-portal'),
        'archdome_render_development_meta_box',
        'development',
        'normal',
        'high',
    );

    add_meta_box(
        'archdome-property-details',
        esc_html__('Property details', 'archdome-portal'),
        'archdome_render_property_meta_box',
        'property',
        'normal',
        'high',
    );
}
add_action('add_meta_boxes', 'archdome_register_meta_boxes');

/**
 * Render a reusable labelled admin field.
 *
 * @param  string  $key  Metadata key.
 * @param  string  $label  Visible label.
 * @param  mixed  $value  Current value.
 * @param  string  $type  Input type.
 * @param  string  $description  Optional help text.
 * @param  string  $step  Optional numeric step.
 */
function archdome_admin_field(string $key, string $label, mixed $value, string $type = 'text', string $description = '', string $step = ''): void
{
    ?>
    <p class="archdome-admin-field">
        <label for="<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label><br>
        <input
            class="widefat"
            id="<?php echo esc_attr($key); ?>"
            name="<?php echo esc_attr($key); ?>"
            type="<?php echo esc_attr($type); ?>"
            value="<?php echo esc_attr((string) $value); ?>"
            <?php echo $step ? 'step="' . esc_attr($step) . '"' : ''; ?>
        >
        <?php if ($description) { ?>
            <span class="description"><?php echo esc_html($description); ?></span>
        <?php } ?>
    </p>
    <?php
}

/**
 * Render the Development fields.
 *
 * @param  WP_Post  $post  Current post.
 */
function archdome_render_development_meta_box(WP_Post $post): void
{
    wp_nonce_field('archdome_save_development', 'archdome_development_nonce');

    $gallery = implode(', ', archdome_sanitize_id_list(get_post_meta($post->ID, '_archdome_gallery_ids', true)));
    $stats = archdome_sanitize_key_stats(get_post_meta($post->ID, '_archdome_key_stats', true));
    $lines = array_map(
        static fn(array $row): string => $row['label'] . ' | ' . $row['value'],
        $stats,
    );

    archdome_admin_field('_archdome_gallery_ids', esc_html__('Gallery attachment IDs', 'archdome-portal'), $gallery, 'text', esc_html__('Comma-separated image attachment IDs, maximum 40.', 'archdome-portal'));
    archdome_admin_field('_archdome_master_plan_id', esc_html__('Master plan attachment ID', 'archdome-portal'), get_post_meta($post->ID, '_archdome_master_plan_id', true), 'number');
    archdome_admin_field('_archdome_brochure_id', esc_html__('Brochure attachment ID', 'archdome-portal'), get_post_meta($post->ID, '_archdome_brochure_id', true), 'number');
    archdome_admin_field('_archdome_latitude', esc_html__('Latitude', 'archdome-portal'), get_post_meta($post->ID, '_archdome_latitude', true), 'number', '', 'any');
    archdome_admin_field('_archdome_longitude', esc_html__('Longitude', 'archdome-portal'), get_post_meta($post->ID, '_archdome_longitude', true), 'number', '', 'any');
    archdome_admin_field('_archdome_inquiry_label', esc_html__('Inquiry call-to-action label', 'archdome-portal'), get_post_meta($post->ID, '_archdome_inquiry_label', true));
    ?>
    <p>
        <label for="_archdome_key_stats"><strong><?php esc_html_e('Key statistics', 'archdome-portal'); ?></strong></label><br>
        <textarea class="widefat" id="_archdome_key_stats" name="_archdome_key_stats" rows="6"><?php echo esc_textarea(implode("\n", $lines)); ?></textarea>
        <span class="description"><?php esc_html_e('One statistic per line using “Label | Value”, maximum 12.', 'archdome-portal'); ?></span>
    </p>
    <p>
        <label>
            <input type="checkbox" name="_archdome_featured" value="1" <?php checked((bool) get_post_meta($post->ID, '_archdome_featured', true)); ?>>
            <?php esc_html_e('Feature this development', 'archdome-portal'); ?>
        </label>
    </p>
    <?php
}

/**
 * Render the Property fields.
 *
 * @param  WP_Post  $post  Current post.
 */
function archdome_render_property_meta_box(WP_Post $post): void
{
    wp_nonce_field('archdome_save_property', 'archdome_property_nonce');

    $development_id = absint(get_post_meta($post->ID, '_archdome_development_id', true));
    $developments = get_posts(
        [
            'post_type' => 'development',
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => 200,
            'orderby' => 'title',
            'order' => 'ASC',
        ],
    );
    ?>
    <p>
        <label for="_archdome_development_id"><strong><?php esc_html_e('Related development', 'archdome-portal'); ?></strong></label><br>
        <select class="widefat" id="_archdome_development_id" name="_archdome_development_id">
            <option value="0"><?php esc_html_e('No related development', 'archdome-portal'); ?></option>
            <?php foreach ($developments as $development) { ?>
                <option value="<?php echo esc_attr((string) $development->ID); ?>" <?php selected($development_id, $development->ID); ?>>
                    <?php echo esc_html($development->post_title ?: sprintf(esc_html__('Untitled development #%d', 'archdome-portal'), $development->ID)); ?>
                </option>
            <?php } ?>
        </select>
    </p>
    <?php
    archdome_admin_field('_archdome_price', esc_html__('Price', 'archdome-portal'), get_post_meta($post->ID, '_archdome_price', true), 'number', '', 'any');
    archdome_admin_field('_archdome_currency', esc_html__('Currency code', 'archdome-portal'), get_post_meta($post->ID, '_archdome_currency', true), 'text', esc_html__('Three-letter ISO code, for example EGP.', 'archdome-portal'));
    archdome_admin_field('_archdome_bedrooms', esc_html__('Bedrooms', 'archdome-portal'), get_post_meta($post->ID, '_archdome_bedrooms', true), 'number');
    archdome_admin_field('_archdome_bathrooms', esc_html__('Bathrooms', 'archdome-portal'), get_post_meta($post->ID, '_archdome_bathrooms', true), 'number', '', 'any');
    archdome_admin_field('_archdome_area', esc_html__('Area', 'archdome-portal'), get_post_meta($post->ID, '_archdome_area', true), 'number', '', 'any');
    ?>
    <p>
        <label for="_archdome_area_unit"><strong><?php esc_html_e('Area unit', 'archdome-portal'); ?></strong></label><br>
        <select class="widefat" id="_archdome_area_unit" name="_archdome_area_unit">
            <option value="sqm" <?php selected(get_post_meta($post->ID, '_archdome_area_unit', true) ?: 'sqm', 'sqm'); ?>><?php esc_html_e('Square metres', 'archdome-portal'); ?></option>
            <option value="sqft" <?php selected(get_post_meta($post->ID, '_archdome_area_unit', true), 'sqft'); ?>><?php esc_html_e('Square feet', 'archdome-portal'); ?></option>
        </select>
    </p>
    <?php
    archdome_admin_field('_archdome_gallery_ids', esc_html__('Gallery attachment IDs', 'archdome-portal'), implode(', ', archdome_sanitize_id_list(get_post_meta($post->ID, '_archdome_gallery_ids', true))), 'text', esc_html__('Comma-separated image attachment IDs, maximum 40.', 'archdome-portal'));
    archdome_admin_field('_archdome_floor_plan_id', esc_html__('Floor plan attachment ID', 'archdome-portal'), get_post_meta($post->ID, '_archdome_floor_plan_id', true), 'number');
    archdome_admin_field('_archdome_reference', esc_html__('Property reference', 'archdome-portal'), get_post_meta($post->ID, '_archdome_reference', true));
    ?>
    <p>
        <label>
            <input type="checkbox" name="_archdome_price_on_request" value="1" <?php checked((bool) get_post_meta($post->ID, '_archdome_price_on_request', true)); ?>>
            <?php esc_html_e('Show price on request', 'archdome-portal'); ?>
        </label><br>
        <label>
            <input type="checkbox" name="_archdome_featured" value="1" <?php checked((bool) get_post_meta($post->ID, '_archdome_featured', true)); ?>>
            <?php esc_html_e('Feature this property', 'archdome-portal'); ?>
        </label>
    </p>
    <?php
}

/**
 * Determine whether an admin save is valid for Archdome metadata.
 */
function archdome_can_save_meta(int $post_id, string $nonce_name, string $action): bool
{
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return false;
    }

    if (! isset($_POST[$nonce_name]) || ! is_scalar($_POST[$nonce_name])) {
        return false;
    }

    $nonce = sanitize_text_field(wp_unslash((string) $_POST[$nonce_name]));

    return wp_verify_nonce($nonce, $action) && current_user_can('edit_post', $post_id);
}

/** Return a scalar admin form value, rejecting nested input. */
function archdome_admin_post_value(string $key): string
{
    if (! isset($_POST[$key]) || ! is_scalar($_POST[$key])) {
        return '';
    }

    return wp_unslash((string) $_POST[$key]);
}

/**
 * Update or remove a sanitized metadata value.
 *
 * @param  int  $post_id  Post ID.
 * @param  string  $key  Metadata key.
 * @param  mixed  $raw  Raw value.
 * @param  callable  $sanitize  Sanitizer.
 */
function archdome_save_meta_value(int $post_id, string $key, mixed $raw, callable $sanitize): void
{
    $value = $sanitize($raw);

    if ($value === '' || $value === [] || $value === 0) {
        delete_post_meta($post_id, $key);

        return;
    }

    update_post_meta($post_id, $key, $value);
}

/**
 * Save Development metadata.
 */
function archdome_save_development_meta(int $post_id): void
{
    if (! archdome_can_save_meta($post_id, 'archdome_development_nonce', 'archdome_save_development')) {
        return;
    }

    $fields = [
        '_archdome_gallery_ids' => 'archdome_sanitize_id_list',
        '_archdome_master_plan_id' => 'archdome_sanitize_attachment_id',
        '_archdome_brochure_id' => 'archdome_sanitize_attachment_id',
        '_archdome_latitude' => 'archdome_sanitize_latitude',
        '_archdome_longitude' => 'archdome_sanitize_longitude',
        '_archdome_inquiry_label' => 'sanitize_text_field',
    ];

    foreach ($fields as $key => $sanitize) {
        $raw = archdome_admin_post_value($key);
        archdome_save_meta_value($post_id, $key, $raw, $sanitize);
    }

    $stats_input = sanitize_textarea_field(archdome_admin_post_value('_archdome_key_stats'));
    $stats = [];

    foreach (array_slice(preg_split('/\R/', $stats_input) ?: [], 0, 12) as $line) {
        [$label, $value] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        $stats[] = compact('label', 'value');
    }

    archdome_save_meta_value($post_id, '_archdome_key_stats', $stats, 'archdome_sanitize_key_stats');
    update_post_meta($post_id, '_archdome_featured', isset($_POST['_archdome_featured']));
}
add_action('save_post_development', 'archdome_save_development_meta');

/**
 * Save Property metadata.
 */
function archdome_save_property_meta(int $post_id): void
{
    if (! archdome_can_save_meta($post_id, 'archdome_property_nonce', 'archdome_save_property')) {
        return;
    }

    $fields = [
        '_archdome_development_id' => 'archdome_sanitize_development_id',
        '_archdome_price' => 'archdome_sanitize_non_negative_decimal',
        '_archdome_currency' => 'archdome_sanitize_currency',
        '_archdome_bedrooms' => 'absint',
        '_archdome_bathrooms' => 'archdome_sanitize_non_negative_decimal',
        '_archdome_area' => 'archdome_sanitize_non_negative_decimal',
        '_archdome_area_unit' => 'archdome_sanitize_area_unit',
        '_archdome_gallery_ids' => 'archdome_sanitize_id_list',
        '_archdome_floor_plan_id' => 'archdome_sanitize_attachment_id',
        '_archdome_reference' => 'sanitize_text_field',
    ];

    foreach ($fields as $key => $sanitize) {
        $raw = archdome_admin_post_value($key);
        archdome_save_meta_value($post_id, $key, $raw, $sanitize);
    }

    update_post_meta($post_id, '_archdome_price_on_request', isset($_POST['_archdome_price_on_request']));
    update_post_meta($post_id, '_archdome_featured', isset($_POST['_archdome_featured']));
}
add_action('save_post_property', 'archdome_save_property_meta');
