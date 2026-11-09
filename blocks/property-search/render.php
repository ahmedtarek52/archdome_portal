<?php
if (! defined('ABSPATH')) {
    exit;
}

$archive_url = get_post_type_archive_link('property');
if (! $archive_url) {
    return;
}

$selected = [
    'location' => absint(archdome_request_scalar('property_location')),
    'property_type' => absint(archdome_request_scalar('property_type')),
    'property_status' => absint(archdome_request_scalar('property_status')),
];
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-property-search']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="site-container">
<?php if (! empty($attributes['heading'])) { ?><h2 class="archdome-property-search__title"><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<form class="archdome-property-search__form" action="<?php echo esc_url($archive_url); ?>" method="get">
<?php foreach (['location' => esc_html__('Location', 'archdome-portal'), 'property_type' => esc_html__('Property type', 'archdome-portal'), 'property_status' => esc_html__('Property status', 'archdome-portal')] as $taxonomy => $label) {
    $query_name = $taxonomy === 'location' ? 'property_location' : $taxonomy;
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]); ?>
    <div class="archdome-property-search__field"><label for="archdome-<?php echo esc_attr($query_name); ?>"><?php echo esc_html($label); ?></label>
    <select id="archdome-<?php echo esc_attr($query_name); ?>" name="<?php echo esc_attr($query_name); ?>"><option value="0"><?php esc_html_e('Any', 'archdome-portal'); ?></option>
    <?php if (! is_wp_error($terms)) {
        foreach ($terms as $term) { ?><option value="<?php echo esc_attr((string) $term->term_id); ?>" <?php selected($selected[$taxonomy], $term->term_id); ?>><?php echo esc_html($term->name); ?></option><?php }
        } ?>
    </select></div>
<?php } ?>
<div class="archdome-property-search__field"><label for="archdome-bedrooms-min"><?php esc_html_e('Minimum bedrooms', 'archdome-portal'); ?></label><input id="archdome-bedrooms-min" name="bedrooms_min" type="number" min="0" max="20" value="<?php $bedrooms = archdome_request_scalar('bedrooms_min');
echo $bedrooms === '' ? '' : esc_attr((string) min(20, absint($bedrooms))); ?>"></div>
<div class="archdome-property-search__actions"><button type="submit"><?php esc_html_e('Search properties', 'archdome-portal'); ?></button></div>
</form></div></section>
