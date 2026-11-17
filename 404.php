<?php
/**
 * 404 : affiche la page choisie dans le Customizer, avec le vrai statut 404.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mrz_page_id = mrz_theme_translated_id( mrz_theme_option( 'page_404' ) );

if ( $mrz_page_id && 'publish' === get_post_status( $mrz_page_id ) ) {
	if ( mrz_theme_has_builder( $mrz_page_id ) ) {
		mrz_theme_render_builder( $mrz_page_id );
	} else {
		echo '<div class="mrz-container mrz-prose py-16">' . apply_filters( 'the_content', get_post_field( 'post_content', $mrz_page_id ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- contenu filtré par the_content.
	}
} else {
	?>
	<div class="mrz-container py-24 text-center">
		<h1 class="mrz-page-title"><?php esc_html_e( 'Page not found', 'mrz-theme-exp' ); ?></h1>
		<p class="mt-4 text-muted"><?php esc_html_e( 'The page you are looking for does not exist or has moved.', 'mrz-theme-exp' ); ?></p>
		<div class="mx-auto mt-8 max-w-md"><?php get_search_form(); ?></div>
		<p class="mt-8"><a class="mrz-btn mrz-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'mrz-theme-exp' ); ?></a></p>
	</div>
	<?php
}

get_footer();
