<?php
if (! defined('ABSPATH')) {
    exit;
}

$ids = archdome_sanitize_id_list($attributes['mediaIds'] ?? []);
$custom_columns = ! empty($attributes['customColumns']);
$columns = $custom_columns
    ? array_map(
        static fn(string $key): array => archdome_sanitize_id_list($attributes[$key] ?? []),
        ['firstColumnIds', 'middleColumnIds', 'lastColumnIds'],
    )
    : [];
$layout = ($attributes['layout'] ?? 'default') === 'editorial' ? 'editorial' : 'default';

if ($layout === 'editorial') {
    $fallback_ids = $custom_columns ? array_merge(...$columns) : $ids;
    $raw_ids = ! empty($attributes['editorialMediaIds'])
        ? $attributes['editorialMediaIds']
        : $fallback_ids;
    $editorial_ids = array_slice(array_pad(array_map('absint', (array) $raw_ids), 6, 0), 0, 6);
    $has_media = (bool) array_filter($editorial_ids, static fn(int $id): bool => (bool) get_post_mime_type($id));
    if (! $has_media) {
        return;
    }
} else {
    $groups = [];
    if ($custom_columns) {
        if (array_filter($columns)) {
            $groups[] = $columns;
        }
    } else {
        foreach (array_chunk($ids, 11) as $group) {
            $groups[] = [array_slice($group, 0, 4), array_slice($group, 4, 3), array_slice($group, 7, 4)];
        }
    }
    if (! $groups) {
        return;
    }
}

$classes = 'archdome-gallery archdome-gallery--' . $layout;
if (! empty($attributes['centerTitle'])) {
    $classes .= ' archdome-gallery--center-title';
}
if (empty($attributes['align'])) {
    $classes .= ' alignfull';
}
$wrapper = get_block_wrapper_attributes(['class' => $classes]);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><div class="archdome-gallery__inner">
<?php if (! empty($attributes['heading'])) { ?><h2 class="archdome-gallery__heading"><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<?php if ($layout === 'editorial') { ?>
<div class="archdome-gallery__rows">
<?php foreach (array_chunk($editorial_ids, 3) as $row_index => $row_ids) {
    $cards = [];
    foreach ($row_ids as $position => $id) {
        $mime = $id ? get_post_mime_type($id) : false;
        if ($mime && (str_starts_with($mime, 'image/') || str_starts_with($mime, 'video/'))) {
            $cards[] = ['id' => $id, 'mime' => $mime, 'position' => $position];
        }
    }
    if (! $cards) {
        continue;
    }
    ?>
<div class="archdome-gallery__row archdome-gallery__row--<?php echo (int) $row_index + 1; ?>">
<?php foreach ($cards as $card) {
    $id = $card['id'];
    $video = str_starts_with($card['mime'], 'video/');
    ?>
<figure class="archdome-gallery__card archdome-gallery__card--<?php echo (int) $card['position'] + 1; ?>">
<div class="archdome-gallery__media">
<?php if ($video) {
    $poster_id = get_post_thumbnail_id($id);
    $poster = $poster_id ? wp_get_attachment_image_url($poster_id, 'large') : false;
    $label = get_the_title($id) ?: __('Gallery video', 'archdome-portal');
    ?>
<video controls playsinline preload="metadata" aria-label="<?php echo esc_attr($label); ?>"<?php echo $poster ? ' poster="' . esc_url($poster) . '"' : ''; ?>><source src="<?php echo esc_url(wp_get_attachment_url($id)); ?>" type="<?php echo esc_attr($card['mime']); ?>"></video>
<button class="archdome-gallery__play" type="button" aria-label="<?php echo esc_attr(sprintf(__('Play video: %s', 'archdome-portal'), $label)); ?>"><span aria-hidden="true"></span></button>
<?php } else {
    echo wp_get_attachment_image($id, 'large', false, ['loading' => 'lazy']);
} ?>
</div>
<?php if (wp_get_attachment_caption($id)) { ?><figcaption><?php echo esc_html(wp_get_attachment_caption($id)); ?></figcaption><?php } ?>
</figure>
<?php } ?>
</div>
<?php } ?>
</div>
<?php } else { ?>
<?php foreach ($groups as $group_columns) { ?>
<div class="archdome-gallery__grid">
<?php foreach ($group_columns as $column_index => $column_ids) {
    if (! $column_ids) {
        continue;
    }
    ?>
<div style="--gallery-column: <?php echo (int) $column_index + 1; ?>" class="archdome-gallery__column<?php echo 1 === $column_index ? ' archdome-gallery__column--middle' : ''; ?>">
<?php foreach ($column_ids as $id) { ?><figure><?php echo wp_get_attachment_image($id, 'large'); ?><?php if (wp_get_attachment_caption($id)) { ?><figcaption><?php echo esc_html(wp_get_attachment_caption($id)); ?></figcaption><?php } ?></figure><?php } ?>
</div>
<?php } ?>
</div>
<?php } ?>
<?php } ?>
</div></section>
