<?php
/**
 * Commande WP-CLI « wp mrz-theme sections » : la référence des sections du parent
 * (noms, clés, types, choix, valeurs par défaut), lue dans inc/builder-sections.php.
 *
 * docs/ia/sections.json en est la copie, régénérée par « --write » : la fiche ne peut
 * pas dériver du code. Chargé seulement sous WP-CLI.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chemin de la copie de la référence.
 */
const MRZ_THEME_SECTIONS_JSON = 'docs/ia/sections.json';

/**
 * Texte d'un libellé ou d'une aide, sans entités HTML (l'aide des titres est échappée pour ACF).
 *
 * @param string $text Texte.
 * @return string
 */
function mrz_theme_reference_text( $text ) {
	return html_entity_decode( (string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}

/**
 * Description d'un champ pour la référence : seulement ce qui sert à remplir la section.
 *
 * @param array $field Champ ACF.
 * @param array $names Noms des champs de la section, par clé (conditions d'affichage).
 * @return array
 */
function mrz_theme_reference_field( $field, $names ) {
	$entry = array(
		'name'  => $field['name'],
		'key'   => $field['key'],
		'type'  => $field['type'],
		'label' => mrz_theme_reference_text( $field['label'] ?? '' ),
	);

	if ( ! empty( $field['instructions'] ) ) {
		$entry['instructions'] = mrz_theme_reference_text( $field['instructions'] );
	}
	if ( ! empty( $field['choices'] ) ) {
		$entry['choices'] = array_map( 'mrz_theme_reference_text', $field['choices'] );
	}
	if ( isset( $field['default_value'] ) && '' !== $field['default_value'] && array() !== $field['default_value'] ) {
		$entry['default'] = $field['default_value'];
	}
	if ( ! empty( $field['return_format'] ) ) {
		$entry['return_format'] = $field['return_format'];
	}
	if ( ! empty( $field['post_type'] ) ) {
		$entry['post_type'] = array_values( (array) $field['post_type'] );
	}
	if ( ! empty( $field['conditional_logic'] ) && is_array( $field['conditional_logic'] ) ) {
		// Conditions ACF (OU de ET), avec le nom du champ à la place de sa clé.
		$entry['shown_if'] = array_map(
			static function ( $rules ) use ( $names ) {
				return array_map(
					static function ( $rule ) use ( $names ) {
						return array(
							'field'    => $names[ $rule['field'] ] ?? $rule['field'],
							'operator' => $rule['operator'],
							'value'    => $rule['value'] ?? '',
						);
					},
					(array) $rules
				);
			},
			$field['conditional_logic']
		);
	}
	if ( ! empty( $field['sub_fields'] ) ) {
		$entry['sub_fields'] = mrz_theme_reference_fields( $field['sub_fields'] );
	}

	return $entry;
}

/**
 * Description d'une liste de champs (une section, un répéteur, un groupe).
 *
 * @param array $fields Champs ACF.
 * @return array
 */
function mrz_theme_reference_fields( $fields ) {
	$names = array();
	foreach ( $fields as $field ) {
		$names[ $field['key'] ] = $field['name'];
	}

	$entries = array();
	foreach ( $fields as $field ) {
		$entries[] = mrz_theme_reference_field( $field, $names );
	}

	return $entries;
}

/**
 * Référence complète des sections du parent, en anglais (chaînes source) : le même
 * résultat quelle que soit la langue du site.
 *
 * @return array
 */
function mrz_theme_sections_reference() {
	$switched = switch_to_locale( 'en_US' );

	$global_only = (array) apply_filters( 'mrz_theme_global_only_layouts', array( 'mrz_footer' ) );
	$sections    = array();
	foreach ( mrz_theme_parent_layouts() as $layout ) {
		$fields       = array();
		$has_settings = false;
		foreach ( $layout['sub_fields'] as $field ) {
			if ( 'settings' === $field['name'] ) {
				$has_settings = true;
				continue;
			}
			$fields[] = $field;
		}

		$sections[] = array(
			'name'        => $layout['name'],
			'key'         => $layout['key'],
			'label'       => mrz_theme_reference_text( $layout['label'] ),
			'global_only' => in_array( $layout['name'], $global_only, true ),
			'settings'    => $has_settings,
			'fields'      => mrz_theme_reference_fields( $fields ),
		);
	}

	$settings = mrz_theme_section_settings_field( 'field_mrz_section' );

	$reference = array(
		'_generated' => 'Generated from inc/builder-sections.php by "wp mrz-theme sections --write". Do not edit by hand.',
		'builder'    => array(
			'field_key'  => 'field_mrz_builder',
			'field_name' => 'mrz_builder',
		),
		'settings'   => array(
			'name'        => 'settings',
			'description' => 'Common settings group, present in every section that has "settings": true. Keys below are those of the clone group; in a parent section they read field_<section>_settings_<name> (field_mrz_text_settings_background). Child sections get it by cloning group_mrz_section_settings.',
			'clone_group' => 'group_mrz_section_settings',
			'fields'      => mrz_theme_reference_fields( $settings['sub_fields'] ),
		),
		'sections'   => $sections,
	);

	if ( $switched ) {
		restore_previous_locale();
	}

	return $reference;
}

/**
 * La référence en JSON, telle qu'elle est écrite dans docs/ia/sections.json.
 *
 * @return string
 */
function mrz_theme_sections_reference_json() {
	return wp_json_encode( mrz_theme_sections_reference(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
}

/**
 * Sections du constructeur.
 *
 * ## OPTIONS
 *
 * [--write]
 * : Écrit docs/ia/sections.json. Seulement dans une copie Git du thème (site de
 *   développement) : sur un site client, le parent n'est jamais modifié.
 *
 * [--check]
 * : Compare docs/ia/sections.json au code. Échoue si la fiche n'est plus à jour.
 *
 * ## EXAMPLES
 *
 *     # Lire la référence (sur n'importe quel site)
 *     wp mrz-theme sections
 *
 *     # Régénérer la fiche après une modification de inc/builder-sections.php
 *     wp mrz-theme sections --write
 *
 * @param array $args       Arguments.
 * @param array $assoc_args Options.
 */
function mrz_theme_cli_sections( $args, $assoc_args ) {
	$json = mrz_theme_sections_reference_json();
	$file = MRZ_THEME_DIR . '/' . MRZ_THEME_SECTIONS_JSON;

	if ( ! empty( $assoc_args['check'] ) ) {
		$current = is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fichier local du thème.
		if ( $current !== $json ) {
			WP_CLI::error( MRZ_THEME_SECTIONS_JSON . ' is out of date: run "wp mrz-theme sections --write".' );
		}
		WP_CLI::success( MRZ_THEME_SECTIONS_JSON . ' is up to date.' );
		return;
	}

	if ( ! empty( $assoc_args['write'] ) ) {
		if ( ! is_dir( MRZ_THEME_DIR . '/.git' ) ) {
			WP_CLI::error( 'Not a Git copy of the theme: the parent theme is never modified on a site.' );
		}
		file_put_contents( $file, $json ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- outil de développement, copie Git seulement.
		WP_CLI::success( 'Written ' . MRZ_THEME_SECTIONS_JSON . '.' );
		return;
	}

	WP_CLI::line( rtrim( $json ) );
}

WP_CLI::add_command( 'mrz-theme sections', 'mrz_theme_cli_sections' );
