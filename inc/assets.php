<?php
/**
 * Chargement des CSS et JS.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Version d'un asset : date de modification du fichier, pour casser le cache à chaque build.
 *
 * @param string $relative_path Chemin depuis la racine du thème.
 * @return string
 */
function mrz_theme_asset_version( $relative_path ) {
	$file = MRZ_THEME_DIR . '/' . $relative_path;

	return file_exists( $file ) ? MRZ_THEME_VERSION . '.' . filemtime( $file ) : MRZ_THEME_VERSION;
}

/**
 * Assets du front.
 */
function mrz_theme_enqueue_assets() {
	wp_enqueue_style( 'mrz-theme', MRZ_THEME_URI . '/dist/main.css', array(), mrz_theme_asset_version( 'dist/main.css' ) );
	wp_add_inline_style( 'mrz-theme', mrz_theme_customizer_css() );

	// Le style.css du thème enfant est chargé automatiquement, après celui du parent.
	if ( is_child_theme() ) {
		wp_enqueue_style(
			'mrz-theme-child',
			get_stylesheet_uri(),
			array( 'mrz-theme' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		wp_enqueue_style( 'mrz-theme-woocommerce', MRZ_THEME_URI . '/assets/css/woocommerce.css', array( 'mrz-theme' ), mrz_theme_asset_version( 'assets/css/woocommerce.css' ) );
	}

	wp_enqueue_script(
		'mrz-theme',
		MRZ_THEME_URI . '/assets/js/main.js',
		array(),
		mrz_theme_asset_version( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_add_inline_script(
		'mrz-theme',
		'window.mrzTheme = ' . wp_json_encode(
			array(
				'i18n' => array(
					'submenu' => __( 'Open submenu', 'mrz-theme-exp' ),
				),
			)
		) . ';',
		'before'
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mrz_theme_enqueue_assets' );
