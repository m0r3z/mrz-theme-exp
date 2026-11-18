<?php
/**
 * Blog : réglages Customizer > MRZ Theme > Blog (article seul, liste des articles),
 * sections de la page des articles et catégories en cartouches.
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

/**
 * Catégories du blog en cartouches, entre le titre et les articles : « Tous » (page des
 * articles) puis chaque catégorie qui a des articles, celle affichée mise en évidence.
 * De simples liens vers les pages de catégorie, pas un filtre. Blog et catégories
 * seulement, et à partir de deux catégories.
 */
function mrz_theme_blog_categories() {
	if ( ! is_home() && ! is_category() ) {
		return;
	}

	$categories = get_categories(
		array(
			'hide_empty' => true,
			'orderby'    => 'name',
		)
	);
	if ( count( $categories ) < 2 ) {
		return;
	}

	$current   = is_category() ? get_queried_object_id() : 0;
	$posts_url = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );

	echo '<nav class="mrz-blog-categories" aria-label="' . esc_attr__( 'Categories', 'mrz-theme-exp' ) . '"><ul>';
	printf(
		'<li><a href="%1$s"%2$s>%3$s</a></li>',
		esc_url( $posts_url ),
		$current ? '' : ' aria-current="page"',
		esc_html__( 'All', 'mrz-theme-exp' )
	);
	foreach ( $categories as $category ) {
		printf(
			'<li><a href="%1$s"%2$s>%3$s</a></li>',
			esc_url( get_category_link( $category ) ),
			$current === $category->term_id ? ' aria-current="page"' : '',
			esc_html( $category->name )
		);
	}
	echo '</ul></nav>';
}
