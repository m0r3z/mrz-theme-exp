<?php
/**
 * Blog : réglages Customizer > MRZ Theme > Blog (article seul, liste des articles)
 * et sections de la page des articles.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Réglages Customizer > MRZ Theme > Blog.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mrz_theme_blog_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'mrz_blog',
		array(
			'title' => __( 'Blog', 'mrz-theme-exp' ),
			'panel' => 'mrz_theme',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'blog_single_layout', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_blog_single_layout',
		array(
			'label'       => __( 'Single post layout', 'mrz-theme-exp' ),
			'description' => __( 'Banner: the featured image fills the top of the post, with the title on it, and the header is transparent over it. A post without a featured image keeps the classic layout.', 'mrz-theme-exp' ),
			'section'     => 'mrz_blog',
			'type'        => 'radio',
			'choices'     => array(
				'classic' => __( 'Classic: title, then featured image', 'mrz-theme-exp' ),
				'banner'  => __( 'Banner: title on the featured image', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'blog_columns', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_blog_columns',
		array(
			'label'       => __( 'Posts per row', 'mrz-theme-exp' ),
			'description' => __( 'Blog page, categories, archives and search, on a computer. One per row on mobile.', 'mrz-theme-exp' ),
			'section'     => 'mrz_blog',
			'type'        => 'radio',
			'choices'     => array(
				'2' => '2',
				'3' => '3',
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'blog_excerpt', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_blog_excerpt',
		array(
			'label'   => __( 'Show the excerpt in the lists', 'mrz-theme-exp' ),
			'section' => 'mrz_blog',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'mrz_theme_blog_customize_register' );

/**
 * Article affiché en bannière : réglage « Bannière » et image à la une présente.
 * Seulement les articles : un CPT garde la disposition classique.
 *
 * @return bool
 */
function mrz_theme_is_post_banner() {
	return is_singular( 'post' )
		&& 'banner' === mrz_theme_option( 'blog_single_layout' )
		&& has_post_thumbnail( get_queried_object_id() );
}

/**
 * Page des articles (Réglages > Lecture) avec des sections dans le constructeur :
 * elles s'affichent au-dessus de la liste et portent le h1, comme la page Boutique.
 * Première page de la liste seulement.
 *
 * @return int ID de la page, 0 sinon.
 */
function mrz_theme_blog_sections_page() {
	$page_id = (int) get_option( 'page_for_posts' );

	if ( ! $page_id || ! is_home() || is_front_page() || is_paged() || ! mrz_theme_has_builder( $page_id ) ) {
		return 0;
	}

	return $page_id;
}

/**
 * Classes de la grille des articles selon le réglage « Articles par ligne ».
 * Écrites en entier pour que Tailwind les trouve.
 *
 * @return string
 */
function mrz_theme_blog_grid_class() {
	return '2' === (string) mrz_theme_option( 'blog_columns' )
		? 'grid gap-8 md:grid-cols-2'
		: 'grid gap-8 md:grid-cols-2 lg:grid-cols-3';
}
