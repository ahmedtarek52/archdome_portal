<?php

/**
 * Hero block renderer.
 */
if (! defined('ABSPATH')) {
    exit;
}

$heading = wp_kses_post($attributes['heading'] ?? '');
$heading_level = absint($attributes['headingLevel'] ?? 1) === 2 ? 'h2' : 'h1';
$media_id = absint($attributes['mediaId'] ?? 0);
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-hero']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    <?php if ($media_id) { ?>
        <div class="archdome-hero__media" aria-hidden="true">
            <?php echo wp_get_attachment_image($media_id, 'archdome-hero', false, ['class' => 'archdome-hero__image', 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '']); ?>
        </div>
    <?php } ?>
    <div class="archdome-hero__overlay" aria-hidden="true"></div>
    <div class="archdome-hero__inner site-container">
        <div class="archdome-hero__content">
            <?php if (! empty($attributes['eyebrow'])) { ?>
                <p class="archdome-hero__eyebrow"><?php echo wp_kses_post($attributes['eyebrow']); ?></p>
            <?php } ?>
            <?php if ($heading) { ?>
                <<?php echo esc_attr($heading_level); ?> class="archdome-hero__title"><?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></<?php echo esc_attr($heading_level); ?>>
            <?php } ?>
            <?php if (! empty($attributes['text'])) { ?>
                <p class="archdome-hero__text"><?php echo esc_html($attributes['text']); ?></p>
            <?php } ?>
            <?php if ((! empty($attributes['primaryLabel']) && ! empty($attributes['primaryUrl'])) || (! empty($attributes['secondaryLabel']) && ! empty($attributes['secondaryUrl']))) { ?>
                <div class="archdome-hero__actions">
                    <?php if (! empty($attributes['primaryLabel']) && ! empty($attributes['primaryUrl'])) { ?>
                        <a class="button" href="<?php echo esc_url($attributes['primaryUrl']); ?>"><?php echo esc_html($attributes['primaryLabel']); ?></a>
                    <?php } ?>
                    <?php if (! empty($attributes['secondaryLabel']) && ! empty($attributes['secondaryUrl'])) { ?>
                        <a class="button button--light" href="<?php echo esc_url($attributes['secondaryUrl']); ?>"><?php echo esc_html($attributes['secondaryLabel']); ?></a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
