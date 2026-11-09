<?php

/**
 * Development card.
 */
if (! defined('ABSPATH')) {
    exit;
}

$locations = get_the_terms(get_the_ID(), 'location');
$location = is_array($locations) && $locations ? $locations[0]->name : '';
$statuses = get_the_terms(get_the_ID(), 'development_status');
$status = is_array($statuses) && $statuses ? $statuses[0]->name : '';
?>
<article <?php post_class('archdome-card archdome-card--development'); ?>>
    <?php if (has_post_thumbnail()) { ?>
        <div class="archdome-card__media" aria-hidden="true"><?php the_post_thumbnail('archdome-card'); ?></div>
    <?php } else { ?>
        <div class="archdome-card__placeholder" aria-hidden="true"><?php esc_html_e('Image unavailable', 'archdome-portal'); ?></div>
    <?php } ?>
    <?php if ($location || $status) { ?><div class="archdome-card__status-bar">
        <?php if ($location) { ?><span class="archdome-card__location"><?php echo esc_html($location); ?></span><?php } ?>
        <?php if ($status) { ?><span class="archdome-card__status"><?php echo esc_html($status); ?></span><?php } ?>
    </div><?php } ?>
    <div class="archdome-card__body">
        <h3 class="archdome-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php if (has_excerpt()) { ?><div class="archdome-card__excerpt"><?php the_excerpt(); ?></div><?php } ?>
    </div>
</article>

