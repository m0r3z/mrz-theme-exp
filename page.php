<?php
/**
 * Page : constructeur ACF s'il est rempli, sinon contenu classique.
 * Une page n'affiche jamais son titre : son contenu porte son propre h1.
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
		<div class="mrz-prose"><?php the_content(); ?></div>
	</article>
	<?php
}

get_footer();
