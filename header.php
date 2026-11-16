<?php
/**
 * En-tête du site.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'mrz-theme-exp' ); ?></a>
<?php
do_action( 'mrz_before_header' );
if ( ! mrz_theme_page_option( '_mrz_hide_header' ) ) {
	get_template_part( 'parts/header/header', mrz_theme_option( 'header_layout' ) );
	get_template_part( 'parts/mobile-menu/offcanvas' );
}
do_action( 'mrz_after_header' );
?>
<main id="content" class="mrz-main">
<?php do_action( 'mrz_before_content' ); ?>
