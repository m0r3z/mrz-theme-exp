<?php
/**
 * Réglages par page : meta box native « MRZ Theme » sur les contenus publics
 * (masquer le titre, le header ou le footer, forcer le header transparent).
 *
 * Meta box WordPress classique, sans ACF : les réglages tiennent même si ACF
 * est désactivé.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Réglages disponibles : clé de meta => type.
 *
 * @return array
 */
function mrz_theme_page_option_keys() {
	return array(
		'_mrz_hide_title'         => 'checkbox',
		'_mrz_hide_header'        => 'checkbox',
		'_mrz_hide_footer'        => 'checkbox',
		'_mrz_transparent_header' => 'choice',
	);
}

/**
 * Types de contenus qui reçoivent la meta box : tous les types publics sauf les médias.
 *
 * @return array
 */
function mrz_theme_page_option_post_types() {
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	unset( $post_types['attachment'] );

	return array_values( $post_types );
}

/**
 * Lit un réglage de la page affichée.
 *
 * @param string $key Clé de meta (_mrz_hide_title…).
 * @return string Valeur ('' si non réglé ou hors d'un contenu seul).
 */
function mrz_theme_page_option( $key ) {
	if ( ! is_singular() ) {
		return '';
	}

	return (string) get_post_meta( get_queried_object_id(), $key, true );
}

/**
 * Déclare la meta box.
 */
function mrz_theme_add_page_options_box() {
	add_meta_box(
		'mrz-theme-page-options',
		__( 'MRZ Theme', 'mrz-theme-exp' ),
		'mrz_theme_render_page_options_box',
		mrz_theme_page_option_post_types(),
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'mrz_theme_add_page_options_box' );

/**
 * Affiche la meta box.
 *
 * @param WP_Post $post Contenu édité.
 */
function mrz_theme_render_page_options_box( $post ) {
	wp_nonce_field( 'mrz_theme_page_options', 'mrz_theme_page_options_nonce' );

	$checkboxes = array(
		'_mrz_hide_title'  => __( 'Hide the title', 'mrz-theme-exp' ),
		'_mrz_hide_header' => __( 'Hide the header', 'mrz-theme-exp' ),
		'_mrz_hide_footer' => __( 'Hide the footer', 'mrz-theme-exp' ),
	);
	foreach ( $checkboxes as $key => $label ) {
		printf(
			'<p><label><input type="checkbox" name="%1$s" value="1"%2$s> %3$s</label></p>',
			esc_attr( $key ),
			checked( get_post_meta( $post->ID, $key, true ), '1', false ),
			esc_html( $label )
		);
	}

	$transparent = (string) get_post_meta( $post->ID, '_mrz_transparent_header', true );
	$choices     = array(
		''    => __( 'Default (theme setting)', 'mrz-theme-exp' ),
		'on'  => __( 'Transparent', 'mrz-theme-exp' ),
		'off' => __( 'Not transparent', 'mrz-theme-exp' ),
	);
	echo '<p><label for="mrz-transparent-header">' . esc_html__( 'Header', 'mrz-theme-exp' ) . '</label><br>';
	echo '<select id="mrz-transparent-header" name="_mrz_transparent_header" class="widefat">';
	foreach ( $choices as $value => $label ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $value ), selected( $transparent, $value, false ), esc_html( $label ) );
	}
	echo '</select></p>';
}

/**
 * Enregistre les réglages.
 *
 * @param int $post_id Contenu enregistré.
 */
function mrz_theme_save_page_options( $post_id ) {
	if ( ! isset( $_POST['mrz_theme_page_options_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mrz_theme_page_options_nonce'] ) ), 'mrz_theme_page_options' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( mrz_theme_page_option_keys() as $key => $type ) {
		if ( 'checkbox' === $type ) {
			$value = isset( $_POST[ $key ] ) ? '1' : '';
		} else {
			$value = isset( $_POST[ $key ] ) ? sanitize_key( wp_unslash( $_POST[ $key ] ) ) : '';
			$value = in_array( $value, array( 'on', 'off' ), true ) ? $value : '';
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post', 'mrz_theme_save_page_options' );

/**
 * Classes du body : l'enfant peut adapter son CSS à une page sans header ou sans footer.
 *
 * @param array $classes Classes.
 * @return array
 */
function mrz_theme_page_options_body_classes( $classes ) {
	if ( mrz_theme_page_option( '_mrz_hide_header' ) ) {
		$classes[] = 'mrz-no-header';
	}
	if ( mrz_theme_page_option( '_mrz_hide_footer' ) ) {
		$classes[] = 'mrz-no-footer';
	}

	return $classes;
}
add_filter( 'body_class', 'mrz_theme_page_options_body_classes' );
