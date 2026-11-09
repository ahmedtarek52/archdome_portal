<?php

if (! defined('ABSPATH')) {
    exit;
}

$wrapper = get_block_wrapper_attributes(['class' => 'archdome-about']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    <div class="archdome-about__inner site-container">
        <div class="archdome-about__copy">
            <?php if (! empty($attributes['eyebrow'])) { ?><p class="eyebrow"><?php echo wp_kses_post($attributes['eyebrow']); ?></p><?php } ?>
            <?php if (! empty($attributes['heading'])) { ?><h2><?php echo wp_kses_post($attributes['heading']); ?></h2><?php } ?>
            <?php if (! empty($attributes['text'])) { ?><div class="archdome-about__text"><?php echo wpautop(wp_kses_post($attributes['text'])); ?></div><?php } ?>
            <?php if (! empty($attributes['linkLabel']) && ! empty($attributes['linkUrl'])) { ?><a class="button" href="<?php echo esc_url($attributes['linkUrl']); ?>"><?php echo esc_html($attributes['linkLabel']); ?></a><?php } ?>
        </div>
        <?php if (! empty($attributes['mediaId'])) { ?>
            <figure class="archdome-about__media"><?php echo wp_get_attachment_image(absint($attributes['mediaId']), 'large'); ?></figure>
        <?php } ?>
    </div>
</section>
