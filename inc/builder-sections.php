<?php
/**
 * Sections du parent, injectées dans le constructeur de chaque site.
 *
 * Le constructeur (champ field_mrz_builder) est déclaré par le thème enfant.
 * Le parent y ajoute ses sections au chargement : une section ajoutée ici
 * apparaît sur tous les sites à la mise à jour.
 *
 * Règles, car le contenu des pages est rattaché aux noms et aux clés :
 * - noms (mrz_*) et clés (layout_mrz_*, field_mrz_*) figés, jamais renommés ;
 * - une section n'est jamais supprimée : la masquer du menu, garder son template.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Réglages communs d'une section (fond, espacement, largeur, ancre, image),
 * lus par mrz_theme_section_open().
 *
 * @param string $prefix Préfixe des clés, propre à chaque section.
 * @return array Champ ACF de type groupe.
 */
function mrz_theme_section_settings_field( $prefix ) {
	return array(
		'key'        => $prefix . '_settings',
		'name'       => 'settings',
		'label'      => __( 'Section settings', 'mrz-theme-exp' ),
		'type'       => 'group',
		'layout'     => 'block',
		'sub_fields' => array(
			array(
				'key'           => $prefix . '_settings_background',
				'name'          => 'background',
				'label'         => __( 'Background', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'none'    => __( 'None', 'mrz-theme-exp' ),
					'light'   => __( 'Light', 'mrz-theme-exp' ),
					'dark'    => __( 'Dark', 'mrz-theme-exp' ),
					'primary' => __( 'Primary color', 'mrz-theme-exp' ),
				),
				'default_value' => 'none',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_spacing',
				'name'          => 'spacing',
				'label'         => __( 'Spacing', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'normal'  => __( 'Normal', 'mrz-theme-exp' ),
					'compact' => __( 'Compact', 'mrz-theme-exp' ),
					'large'   => __( 'Large', 'mrz-theme-exp' ),
					'none'    => __( 'None', 'mrz-theme-exp' ),
				),
				'default_value' => 'normal',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_width',
				'name'          => 'width',
				'label'         => __( 'Width', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'container' => __( 'Container', 'mrz-theme-exp' ),
					'wide'      => __( 'Wide', 'mrz-theme-exp' ),
					'full'      => __( 'Full width', 'mrz-theme-exp' ),
				),
				'default_value' => 'container',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'     => $prefix . '_settings_anchor',
				'name'    => 'anchor',
				'label'   => __( 'Anchor', 'mrz-theme-exp' ),
				'type'    => 'text',
				'wrapper' => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_background_image',
				'name'          => 'background_image',
				'label'         => __( 'Background image', 'mrz-theme-exp' ),
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
			),
		),
	);
}

/**
 * Groupe « Réglages de section », inactif, à cloner dans les sections de l'enfant
 * (champ clone sur group_mrz_section_settings) pour leur donner les réglages communs.
 */
function mrz_theme_register_section_settings_group() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_mrz_section_settings',
			'title'    => __( 'Section settings (to clone)', 'mrz-theme-exp' ),
			'fields'   => array( mrz_theme_section_settings_field( 'field_mrz_section' ) ),
			'location' => array(),
			'active'   => false,
		)
	);
}
add_action( 'acf/init', 'mrz_theme_register_section_settings_group' );

/**
 * Sections fournies par le parent, au format des layouts ACF.
 *
 * @return array
 */
function mrz_theme_parent_layouts() {
	$layouts = array(
		'layout_mrz_text'           => array(
			'key'        => 'layout_mrz_text',
			'name'       => 'mrz_text',
			'label'      => __( 'Free text', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'   => 'field_mrz_text_title',
					'name'  => 'title',
					'label' => __( 'Title', 'mrz-theme-exp' ),
					'type'  => 'text',
				),
				array(
					'key'          => 'field_mrz_text_content',
					'name'         => 'content',
					'label'        => __( 'Text', 'mrz-theme-exp' ),
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 1,
				),
				mrz_theme_section_settings_field( 'field_mrz_text' ),
			),
		),
		'layout_mrz_html'           => array(
			'key'        => 'layout_mrz_html',
			'name'       => 'mrz_html',
			'label'      => __( 'Raw HTML', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_html_code',
					'name'         => 'code',
					'label'        => __( 'HTML code', 'mrz-theme-exp' ),
					'type'         => 'textarea',
					'instructions' => __( 'Displayed as is, scripts included. Reserved for administrators.', 'mrz-theme-exp' ),
					'rows'         => 10,
					'new_lines'    => '',
				),
			),
		),
		'layout_mrz_global_section' => array(
			'key'        => 'layout_mrz_global_section',
			'name'       => 'mrz_global_section',
			'label'      => __( 'Global section', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'           => 'field_mrz_global_section_section',
					'name'          => 'section',
					'label'         => __( 'Global section', 'mrz-theme-exp' ),
					'type'          => 'post_object',
					'post_type'     => array( 'mrz_section' ),
					'return_format' => 'id',
					'ui'            => 1,
				),
			),
		),
	);

	// ACF rattache chaque sous-champ à sa section par parent_layout.
	foreach ( $layouts as $key => $layout ) {
		$layouts[ $key ] += array(
			'min' => '',
			'max' => '',
		);
		foreach ( $layouts[ $key ]['sub_fields'] as $index => $sub_field ) {
			$sub_field['parent_layout']                 = $key;
			$layouts[ $key ]['sub_fields'][ $index ] = mrz_theme_validate_field( $sub_field );
		}
	}

	return $layouts;
}

/**
 * Complète un champ déclaré en PHP comme ACF le fait au chargement (_name, prefix…),
 * sous-champs compris : sans cela, les valeurs d'un groupe ne s'enregistrent pas.
 *
 * @param array $field Champ.
 * @return array
 */
function mrz_theme_validate_field( $field ) {
	if ( ! function_exists( 'acf_validate_field' ) ) {
		return $field;
	}

	$field = acf_validate_field( $field );

	if ( ! empty( $field['sub_fields'] ) ) {
		foreach ( $field['sub_fields'] as $index => $sub_field ) {
			$sub_field['parent']                = $field['key'];
			$field['sub_fields'][ $index ] = mrz_theme_validate_field( $sub_field );
		}
	}

	return $field;
}

/**
 * Faut-il injecter les sections du parent ? Jamais pendant l'édition ou
 * l'enregistrement d'un groupe de champs : ACF les recopierait dans le groupe
 * (base et JSON) de l'enfant.
 *
 * @return bool
 */
function mrz_theme_should_inject_layouts() {
	// ACF coupe ses filtres quand il enregistre un groupe de champs.
	if ( function_exists( 'acf_is_filter_enabled' ) && ! acf_is_filter_enabled( 'local' ) ) {
		return false;
	}

	if ( ! is_admin() ) {
		return true;
	}

	// Écrans des groupes de champs (édition, liste, synchronisation JSON).
	global $typenow;
	if ( 'acf-field-group' === $typenow ) {
		return false;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'acf-field-group' === $screen->post_type ) {
		return false;
	}

	// Requêtes AJAX de l'éditeur de groupes (ajout de champ, déplacement…).
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lecture seule du nom de l'action.
	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
	if ( wp_doing_ajax() && ( str_starts_with( $action, 'acf/field_group' ) || str_starts_with( $action, 'acf/field' ) ) ) {
		return false;
	}

	return true;
}

/**
 * Ajoute les sections du parent au constructeur chargé.
 *
 * @param array $field Champ flexible.
 * @return array
 */
function mrz_theme_inject_parent_layouts( $field ) {
	if ( 'field_mrz_builder' !== $field['key'] || ! mrz_theme_should_inject_layouts() ) {
		return $field;
	}

	$layouts = (array) $field['layouts'];
	foreach ( mrz_theme_parent_layouts() as $key => $layout ) {
		if ( ! isset( $layouts[ $key ] ) ) {
			$layouts[ $key ] = $layout;
		}
	}
	$field['layouts'] = $layouts;

	return $field;
}
add_filter( 'acf/load_field/type=flexible_content', 'mrz_theme_inject_parent_layouts', 20 );

/**
 * HTML brut : champ réservé aux comptes qui peuvent publier du HTML non filtré.
 *
 * @param array $field Champ.
 * @return array|false
 */
function mrz_theme_restrict_html_field( $field ) {
	return current_user_can( 'unfiltered_html' ) ? $field : false;
}
add_filter( 'acf/prepare_field/key=field_mrz_html_code', 'mrz_theme_restrict_html_field' );
