<?php

/**
 * Archive taxonomy filters.
 *
 * Variables: $args['post_type'], $args['filters'].
 */
if (! defined('ABSPATH')) {
    exit;
}

$archive_post_type = sanitize_key($args['post_type'] ?? '');
$filters = is_array($args['filters'] ?? null) ? $args['filters'] : [];
$archive_url = get_post_type_archive_link($archive_post_type);

if (! $archive_url) {
    return;
}
?>
<form class="archive-filters" action="<?php echo esc_url($archive_url); ?>" method="get">
    <?php foreach ($filters as $parameter => $filter) {
        $taxonomy = sanitize_key($filter['taxonomy']);
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);
        if (is_wp_error($terms) || ! $terms) {
            continue;
        }
        $selected = absint(archdome_request_scalar($parameter));
        ?>
        <div class="archive-filters__field">
            <label for="filter-<?php echo esc_attr($parameter); ?>"><?php echo esc_html($filter['label']); ?></label>
            <select id="filter-<?php echo esc_attr($parameter); ?>" name="<?php echo esc_attr($parameter); ?>">
                <option value="0"><?php esc_html_e('Any', 'archdome-portal'); ?></option>
                <?php foreach ($terms as $term) { ?><option value="<?php echo esc_attr((string) $term->term_id); ?>" <?php selected($selected, $term->term_id); ?>><?php echo esc_html($term->name); ?></option><?php } ?>
            </select>
        </div>
    <?php } ?>
    <?php if ($archive_post_type === 'property') { ?>
        <div class="archive-filters__field"><label for="filter-bedrooms"><?php esc_html_e('Minimum bedrooms', 'archdome-portal'); ?></label><input id="filter-bedrooms" name="bedrooms_min" type="number" min="0" max="20" value="<?php $bedrooms = archdome_request_scalar('bedrooms_min');
        echo $bedrooms === '' ? '' : esc_attr((string) min(20, absint($bedrooms))); ?>"></div>
        <div class="archive-filters__field"><label for="filter-sort"><?php esc_html_e('Sort by', 'archdome-portal'); ?></label><select id="filter-sort" name="sort">
            <option value="newest"><?php esc_html_e('Newest', 'archdome-portal'); ?></option>
            <option value="price_asc" <?php selected(sanitize_key(archdome_request_scalar('sort')), 'price_asc'); ?>><?php esc_html_e('Price: low to high', 'archdome-portal'); ?></option>
            <option value="price_desc" <?php selected(sanitize_key(archdome_request_scalar('sort')), 'price_desc'); ?>><?php esc_html_e('Price: high to low', 'archdome-portal'); ?></option>
        </select></div>
    <?php } ?>
    <div class="archive-filters__actions"><button type="submit"><?php esc_html_e('Apply filters', 'archdome-portal'); ?></button><?php if (archdome_archive_filter_args()) { ?><a href="<?php echo esc_url($archive_url); ?>"><?php esc_html_e('Clear filters', 'archdome-portal'); ?></a><?php } ?></div>
</form>
