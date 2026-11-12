<?php
/**
 * Standard page template.
 */
get_header();
$is_about_page = is_page(['about', 'about-en']);
?>
<div class="content-layout<?php echo $is_about_page ? ' about-page' : ' site-container content-layout--reading'; ?>">
	<?php
    while (have_posts()) {
        the_post();
        ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
			<h1 class="screen-reader-text"><?php the_title(); ?></h1>
			<div class="entry__content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
    }
?>
</div>
<?php
get_footer();
