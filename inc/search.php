<?php
/**
 * Recherche et extrait des pages construites.
 *
 * Le texte d'une page construite vit dans les champs ACF du constructeur, pas dans
 * post_content : la recherche de WordPress ne le trouve pas et l'extrait est vide.
 * Le thème recopie ce texte, sans balises, dans une meta cachée (_mrz_search_text)
 * à chaque enregistrement du constructeur. La recherche lit aussi cette meta, et
 * l'extrait automatique en part quand la page n'en a pas.
 *
 * post_content et post_excerpt ne sont jamais modifiés.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Version de l'index : à monter quand le texte extrait change (nouveau type de champ lu),
 * pour que chaque site recalcule ses pages à la mise à jour du thème.
 */
const MRZ_THEME_SEARCH_INDEX_VERSION = 1;

/**
 * Meta qui porte le texte d'une page construite.
 */
const MRZ_THEME_SEARCH_META = '_mrz_search_text';

/**
 * Définitions des champs du constructeur, par clé (sections du parent et de l'enfant,
 * sous-champs compris).
 *
 * @return array Clé => champ.
 */
function mrz_theme_builder_field_definitions() {
	static $definitions = null;

	if ( null !== $definitions ) {
		return $definitions;
	}

	$definitions = array();
	$builder     = function_exists( 'acf_get_field' ) ? acf_get_field( 'field_mrz_builder' ) : false;
	if ( ! $builder || empty( $builder['layouts'] ) ) {
		return $definitions;
	}

	$collect = static function ( $fields ) use ( &$collect, &$definitions ) {
		foreach ( (array) $fields as $field ) {
			if ( empty( $field['key'] ) ) {
				continue;
			}
			$definitions[ $field['key'] ] = $field;
			if ( ! empty( $field['sub_fields'] ) ) {
				$collect( $field['sub_fields'] );
			}
		}
	};

	foreach ( $builder['layouts'] as $layout ) {
		$collect( $layout['sub_fields'] ?? array() );
	}

	return $definitions;
}

/**
 * Texte lisible d'une valeur brute du constructeur (rangée, répéteur, groupe).
 * Seuls les champs de texte comptent : titres, textes, intitulés des liens.
 * Les réglages de section, les ancres, les choix et les médias sont ignorés.
 *
 * @param array $values      Valeurs brutes, par clé de champ.
 * @param array $definitions Définitions des champs, par clé.
 * @return array Morceaux de texte.
 */
function mrz_theme_builder_values_text( $values, $definitions ) {
	$parts = array();

	foreach ( (array) $values as $key => $value ) {
		$field = $definitions[ $key ] ?? null;
		// Réglages de section et ancres (identifiants techniques, jamais lus par le visiteur).
		if ( ! $field || in_array( $field['name'] ?? '', array( 'settings', 'anchor' ), true ) ) {
			continue;
		}

		switch ( $field['type'] ) {
			case 'text':
			case 'textarea':
			case 'wysiwyg':
				$parts[] = (string) $value;
				break;
			case 'link':
				$parts[] = is_array( $value ) ? (string) ( $value['title'] ?? '' ) : '';
				break;
			case 'group':
				$parts = array_merge( $parts, mrz_theme_builder_values_text( $value, $definitions ) );
				break;
			case 'repeater':
				foreach ( (array) $value as $row ) {
					$parts = array_merge( $parts, mrz_theme_builder_values_text( $row, $definitions ) );
				}
				break;
		}
	}

	return $parts;
}

/**
 * Texte d'une page construite, sans balises ni shortcodes.
 *
 * @param int $post_id Contenu.
 * @return string
 */
function mrz_theme_builder_text( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$rows = get_field( 'field_mrz_builder', $post_id, false );
	if ( ! is_array( $rows ) ) {
		return '';
	}

	$definitions = mrz_theme_builder_field_definitions();
	$parts       = array();
	foreach ( $rows as $row ) {
		$parts = array_merge( $parts, mrz_theme_builder_values_text( $row, $definitions ) );
	}

	// Espaces entre les balises : « <h2>Titre</h2><p>Texte » ne doit pas donner « TitreTexte ».
	$text = str_replace( '<', ' <', implode( ' ', $parts ) );
	$text = wp_strip_all_tags( strip_shortcodes( $text ) );
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

	return trim( (string) preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * Les sections du parent sont-elles chargées ? Elles ne le sont pas sur les écrans des
 * groupes de champs (mrz_theme_should_inject_layouts()) : calculer là viderait l'index.
 *
 * @return bool
 */
function mrz_theme_search_index_ready() {
	return isset( mrz_theme_builder_field_definitions()['field_mrz_text_title'] );
}

/**
 * Recalcule le texte d'une page construite (meta supprimée si la page n'a plus de texte).
 *
 * @param int $post_id Contenu.
 */
function mrz_theme_update_search_text( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id || wp_is_post_revision( $post_id ) || ! mrz_theme_search_index_ready() ) {
		return;
	}

	$text = mrz_theme_builder_text( $post_id );
	if ( '' === $text ) {
		delete_post_meta( $post_id, MRZ_THEME_SEARCH_META );
	} else {
		update_post_meta( $post_id, MRZ_THEME_SEARCH_META, wp_slash( $text ) );
	}
}

/**
 * Pages dont le constructeur a changé pendant la requête, recalculées en fin de requête.
 * Couvre l'enregistrement dans l'admin comme update_field() (code, WP-CLI, IA).
 *
 * @param int|null $post_id Contenu à marquer, ou null pour lire la liste.
 * @return array
 */
function mrz_theme_search_dirty_posts( $post_id = null ) {
	static $posts = array();

	if ( null !== $post_id ) {
		$posts[ (int) $post_id ] = true;
	}

	return $posts;
}

/**
 * Marque la page dont le constructeur est enregistré. ACF passe ici après avoir écrit
 * les rangées, avant la liste des sections : le calcul attend donc la fin de la requête.
 *
 * @param mixed      $value   Valeur enregistrée.
 * @param int|string $post_id Contenu (ou « term_3 », « options »…).
 * @return mixed Valeur inchangée.
 */
function mrz_theme_mark_search_dirty( $value, $post_id ) {
	if ( is_numeric( $post_id ) && (int) $post_id > 0 ) {
		mrz_theme_search_dirty_posts( (int) $post_id );
	}

	return $value;
}
add_filter( 'acf/update_value/key=field_mrz_builder', 'mrz_theme_mark_search_dirty', 99, 2 );

/**
 * Recalcule les pages marquées.
 */
function mrz_theme_flush_search_text() {
	foreach ( array_keys( mrz_theme_search_dirty_posts() ) as $post_id ) {
		mrz_theme_update_search_text( $post_id );
	}
}
// Enregistrement dans l'admin : tout de suite après ACF. Ailleurs : en fin de requête.
add_action( 'acf/save_post', 'mrz_theme_flush_search_text', 20 );
add_action( 'shutdown', 'mrz_theme_flush_search_text', 0 );

/**
 * Pages déjà construites avant cette version de l'index : recalculées une fois, à la
 * première page d'admin après la mise à jour du thème.
 */
function mrz_theme_rebuild_search_index() {
	if ( (int) get_option( 'mrz_theme_search_index' ) >= MRZ_THEME_SEARCH_INDEX_VERSION || ! function_exists( 'get_field' ) || ! mrz_theme_search_index_ready() ) {
		return;
	}

	$post_ids = get_posts(
		array(
			'post_type'      => 'any',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => 'mrz_builder', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- une seule fois par site.
		)
	);
	foreach ( $post_ids as $post_id ) {
		mrz_theme_update_search_text( $post_id );
	}

	update_option( 'mrz_theme_search_index', MRZ_THEME_SEARCH_INDEX_VERSION );
}
add_action( 'admin_init', 'mrz_theme_rebuild_search_index' );

/**
 * Recherche : chaque mot cherché dans le contenu l'est aussi dans le texte du constructeur.
 * Le motif LIKE déjà préparé par WordPress est repris tel quel (rien n'est réécrit à la
 * main). Les mots exclus (« -mot ») ne lisent que les colonnes de WordPress.
 *
 * @param string   $search Clause WHERE de la recherche.
 * @param WP_Query $query  Requête.
 * @return string
 */
function mrz_theme_search_builder_text( $search, $query ) {
	global $wpdb;

	if ( '' === $search || ! $query->is_search() ) {
		return $search;
	}

	$column = preg_quote( $wpdb->posts . '.post_content', '/' );
	$exists = "(EXISTS (SELECT 1 FROM {$wpdb->postmeta} mrz_search WHERE mrz_search.post_id = {$wpdb->posts}.ID AND mrz_search.meta_key = '" . MRZ_THEME_SEARCH_META . "' AND mrz_search.meta_value LIKE $1))";

	return (string) preg_replace( "/\({$column} LIKE ('(?:[^'\\\\]|\\\\.)*')\)/", "$0 OR {$exists}", $search );
}
add_filter( 'posts_search', 'mrz_theme_search_builder_text', 10, 2 );

/**
 * Extrait automatique d'une page construite sans extrait : le début de son texte
 * (résultats de recherche, listes). Un extrait saisi passe toujours devant.
 *
 * @param string       $excerpt Extrait calculé par WordPress.
 * @param WP_Post|null $post    Contenu.
 * @return string
 */
function mrz_theme_builder_excerpt( $excerpt, $post = null ) {
	if ( '' !== trim( (string) $excerpt ) || ! $post instanceof WP_Post || '' !== $post->post_excerpt || post_password_required( $post ) ) {
		return $excerpt;
	}

	$text = (string) get_post_meta( $post->ID, MRZ_THEME_SEARCH_META, true );
	if ( '' === $text ) {
		return $excerpt;
	}

	// Mêmes longueur et suite que l'extrait de WordPress (filtres excerpt_length et excerpt_more).
	$length = (int) apply_filters( 'excerpt_length', 55 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- filtre du cœur.
	$more   = apply_filters( 'excerpt_more', ' [&hellip;]' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- filtre du cœur.

	return wp_trim_words( $text, $length, $more );
}
add_filter( 'get_the_excerpt', 'mrz_theme_builder_excerpt', 20, 2 );
