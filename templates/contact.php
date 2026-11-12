<?php
/**
 * Template Name: Contact
 */
get_header();
while (have_posts()) {
    the_post();
    $email = sanitize_email(get_option('archdome_contact_email', ''));
    $phone = sanitize_text_field(get_option('archdome_contact_phone', ''));
    $address = sanitize_textarea_field(get_option('archdome_office_address', ''));
    $whatsapp = esc_url(get_option('archdome_whatsapp_url', ''));
    $facebook = esc_url(get_option('archdome_facebook_url', ''));
    $instagram = esc_url(get_option('archdome_instagram_url', ''));
    $linkedin = esc_url(get_option('archdome_linkedin_url', ''));
    $context = absint(archdome_request_scalar('context'));
    if ($context && get_post_status($context) !== 'publish') {
        $context = 0;
    }
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('site-container content-layout contact-page'); ?>>
        <h1 class="screen-reader-text"><?php the_title(); ?></h1>
        <div class="contact-page__grid">
            <div class="entry__content"><?php the_content(); ?></div>
            <aside class="contact-details" aria-label="<?php esc_attr_e('Contact details', 'archdome-portal'); ?>">
                <?php if ($context) { ?><p class="contact-details__context"><?php esc_html_e('Enquiry about:', 'archdome-portal'); ?> <strong><?php echo esc_html(get_the_title($context)); ?></strong></p><?php } ?>
                <?php if ($email) { ?><p><strong><?php esc_html_e('Email', 'archdome-portal'); ?></strong><br><a dir="ltr" href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p><?php } ?>
                <?php if ($phone) {
                    $phone_href = preg_replace('/[^0-9+]/', '', $phone); ?><p><strong><?php esc_html_e('Phone', 'archdome-portal'); ?></strong><br><a dir="ltr" href="tel:<?php echo esc_attr($phone_href); ?>"><?php echo esc_html($phone); ?></a></p><?php } ?>
                <?php if ($whatsapp) { ?><p><a href="<?php echo esc_url($whatsapp); ?>"><?php esc_html_e('Chat on WhatsApp', 'archdome-portal'); ?></a></p><?php } ?>
                <?php if ($address) { ?><p><strong><?php esc_html_e('Office', 'archdome-portal'); ?></strong><br><?php echo nl2br(esc_html($address)); ?></p><?php } ?>
                <?php if ($facebook || $instagram || $linkedin) { ?><p><strong><?php esc_html_e('Follow ArchDome', 'archdome-portal'); ?></strong><br>
                    <?php if ($facebook) { ?><a href="<?php echo esc_url($facebook); ?>">Facebook</a><?php } ?>
                    <?php if ($instagram) { ?><?php echo $facebook ? ' · ' : ''; ?><a href="<?php echo esc_url($instagram); ?>">Instagram</a><?php } ?>
                    <?php if ($linkedin) { ?><?php echo ($facebook || $instagram) ? ' · ' : ''; ?><a href="<?php echo esc_url($linkedin); ?>">LinkedIn</a><?php } ?>
                </p><?php } ?>
                <?php if (! $email && ! $phone && ! $address && ! $whatsapp && ! $facebook && ! $instagram && ! $linkedin) { ?><p><?php esc_html_e('Contact details have not been configured yet.', 'archdome-portal'); ?></p><?php } ?>
            </aside>
        </div>
    </article>
<?php }
get_footer(); ?>
