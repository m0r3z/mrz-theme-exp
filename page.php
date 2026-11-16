<?php
/**
 * Page : constructeur ACF s'il est rempli, sinon titre + contenu classique.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();

	if ( mrz_theme_has_builder() ) {
		mrz_theme_render_builder();
		continue;
	}
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'mrz-container py-16' ); ?>>
		<?php if ( ! mrz_theme_page_option( '_mrz_hide_title' ) ) : ?>
			<h1 class="mrz-page-title mb-10"><?php the_title(); ?></h1>
		<?php endif; ?>
		<div class="mrz-prose"><?php the_content(); ?></div>
	</article>
	<?php
}

get_footer();
