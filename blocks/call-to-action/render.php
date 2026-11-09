<?php

/**
 * Call-to-action block renderer.
 */
if (! defined('ABSPATH')) {
    exit;
}

$tone = 'light' === ($attributes['tone'] ?? '') ? 'light' : 'brand';
$background_id = absint($attributes['backgroundImageId'] ?? 0);
$has_background = $background_id && wp_attachment_is_image($background_id);
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-cta archdome-cta--' . $tone . ($has_background ? ' archdome-cta--has-image' : '')]);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    <?php if ($has_background) { ?>
        <?php echo wp_get_attachment_image($background_id, 'full', false, ['class' => 'archdome-cta__background', 'alt' => '', 'loading' => 'lazy']); ?>
        <div class="archdome-cta__overlay" aria-hidden="true"></div>
    <?php } ?>
    <div class="archdome-cta__inner site-container">
        <div class="archdome-cta__copy">
            <?php if (! empty($attributes['heading'])) { ?>
                <h2 class="archdome-cta__title"><?php echo wp_kses_post($attributes['heading']); ?></h2>
            <?php } ?>
            <?php if (! empty($attributes['text'])) { ?>
                <p><?php echo esc_html($attributes['text']); ?></p>
            <?php } ?>
        </div>
        <div class="archdome-cta__actions">
            <?php if (! empty($attributes['primaryLabel']) && ! empty($attributes['primaryUrl'])) { ?>
                <a class="button" href="<?php echo esc_url($attributes['primaryUrl']); ?>"><?php echo esc_html($attributes['primaryLabel']); ?></a>
            <?php } ?>
            <?php if (! empty($attributes['secondaryLabel']) && ! empty($attributes['secondaryUrl'])) { ?>
                <a class="button button--secondary" href="<?php echo esc_url($attributes['secondaryUrl']); ?>"><?php echo esc_html($attributes['secondaryLabel']); ?></a>
            <?php } ?>
        </div>
    </div>
</section>
