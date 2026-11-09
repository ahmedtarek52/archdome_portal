<?php

if (! defined('ABSPATH')) {
    exit;
}

$items = array_slice(is_array($attributes['items'] ?? null) ? $attributes['items'] : [], 0, 8);
$items = array_values(array_filter($items, static fn(mixed $item): bool =>
    is_array($item)
    && is_scalar($item['value'] ?? null)
    && is_scalar($item['label'] ?? null)
    && '' !== trim((string) $item['value'])
    && '' !== trim((string) $item['label'])
));

if (! $items) {
    return;
}

$heading = is_string($attributes['heading'] ?? null) ? trim(wp_strip_all_tags($attributes['heading'])) : '';
$eyebrow = is_string($attributes['eyebrow'] ?? null) ? trim($attributes['eyebrow']) : '';
$text = is_string($attributes['text'] ?? null) ? trim($attributes['text']) : '';
$heading_tag = absint($attributes['headingLevel'] ?? 2) === 3 ? 'h3' : 'h2';
$icon_paths = [
    'building' => ['M4 22V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v18', 'M2 22h20', 'M8 6h2', 'M14 6h2', 'M8 10h2', 'M14 10h2', 'M8 14h2', 'M14 14h2', 'M10 22v-4h4v4'],
    'home' => ['m3 10 9-7 9 7', 'M5 9v12h14V9', 'M9 21v-7h6v7'],
    'location' => ['M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z', 'M12 7.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z'],
    'key' => ['M21 7a5 5 0 1 1-9.6 2H3v4h3v3h3v3h4v-8.4A5 5 0 0 1 21 7Z', 'M17 7h.01'],
    'area' => ['M4 4h16v16H4z', 'M8 16h8V8'],
];
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-statistics']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="archdome-statistics__inner site-container<?php echo ($heading || $eyebrow || $text) ? '' : ' archdome-statistics__inner--only-stats'; ?>">
        <?php if ($heading || $eyebrow || $text) { ?><div class="archdome-statistics__intro">
            <?php if ($eyebrow) { ?><p class="archdome-statistics__eyebrow"><?php echo esc_html($eyebrow); ?></p><?php } ?>
            <?php if ($heading) { ?><<?php echo tag_escape($heading_tag); ?> class="archdome-statistics__heading"><?php echo esc_html($heading); ?></<?php echo tag_escape($heading_tag); ?>><?php } ?>
            <?php if ($text) { ?><p class="archdome-statistics__text"><?php echo esc_html($text); ?></p><?php } ?>
        </div><?php } ?>
        <dl class="archdome-statistics__list">
            <?php foreach ($items as $item) { ?>
                <div class="archdome-statistics__item">
                    <?php $icon = sanitize_key(is_string($item['icon'] ?? null) ? $item['icon'] : ''); ?>
                    <?php if (isset($icon_paths[$icon])) { ?>
                        <span class="archdome-statistics__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                                <?php foreach ($icon_paths[$icon] as $path) { ?><path d="<?php echo esc_attr($path); ?>" /><?php } ?>
                            </svg>
                        </span>
                    <?php } ?>
                    <dt><?php echo esc_html((string) $item['label']); ?></dt>
                    <dd dir="auto"><?php echo esc_html((string) $item['value']); ?></dd>
                </div>
            <?php } ?>
        </dl>
    </div>
</section>
