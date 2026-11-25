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

	// CSS des intégrations, branchés sur les variables du thème.
	$child_dependencies = array( 'mrz-theme' );

	// Partout : une grille [products] peut se trouver sur n'importe quelle page (3 Ko).
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'mrz-theme-woocommerce', MRZ_THEME_URI . '/assets/css/woocommerce.css', array( 'mrz-theme' ), mrz_theme_asset_version( 'assets/css/woocommerce.css' ) );
		wp_add_inline_style( 'mrz-theme-woocommerce', mrz_theme_shop_inline_css() );
		$child_dependencies[] = 'mrz-theme-woocommerce';
	}

	/*
	 * Thème enfant, chargé en dernier pour passer devant tout le reste :
	 * style.css (variables du site), puis chaque fichier de son dossier css/
	 * par ordre alphabétique (header.css, footer.css…). Aucun enqueue à écrire.
	 */
	if ( is_child_theme() ) {
		$child_dir = get_stylesheet_directory();

		wp_enqueue_style( 'mrz-theme-child', get_stylesheet_uri(), $child_dependencies, (string) filemtime( $child_dir . '/style.css' ) );

		$child_files = glob( $child_dir . '/css/*.css' );
		foreach ( $child_files ? $child_files : array() as $child_file ) {
			$name = basename( $child_file, '.css' );
			wp_enqueue_style( 'mrz-theme-child-' . sanitize_key( $name ), get_stylesheet_directory_uri() . '/css/' . rawurlencode( basename( $child_file ) ), array( 'mrz-theme-child' ), (string) filemtime( $child_file ) );
		}
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

/*
 * Gravity Forms : thème « Orbital » imposé à tous les formulaires. C'est le seul
 * réglable par variables CSS (--gf-*), que src/css/gravity-forms.css (compilé dans dist/main.css) branche
 * sur celles du thème. L'ancien thème 2.5 a des valeurs en dur.
 */
add_filter(
	'gform_form_theme_slug',
	static function () {
		return 'orbital';
	}
);

// Sans style propre au formulaire, les couleurs par défaut d'Orbital ne passent pas devant celles du thème.
add_filter(
	'gform_pre_render',
	static function ( $form ) {
		if ( is_array( $form ) && isset( $form['styles'] ) && empty( $form['styles'] ) ) {
			$form['styles'] = false;
		}

		return $form;
	}
);
