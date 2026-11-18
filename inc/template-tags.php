<?php
/**
 * Fonctions d'affichage utilisées par les templates.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Logo du site, avec le logo du header transparent s'il est défini.
 *
 * @param string $context header ou footer.
 */
function mrz_theme_logo( $context = 'header' ) {
	$logo_id        = (int) get_theme_mod( 'custom_logo' );
	$transparent_id = (int) mrz_theme_option( 'logo_transparent' );
	$site_name      = get_bloginfo( 'name' );

	echo '<a class="mrz-logo mrz-logo--' . esc_attr( $context ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';

	if ( $logo_id ) {
		echo wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class'         => 'mrz-logo__img',
				'alt'           => $site_name,
				'loading'       => 'eager',
				// Pas de priorité haute : le logo n'est presque jamais le LCP, l'image de la bannière l'est.
				'fetchpriority' => 'auto',
			)
		);

		if ( 'header' === $context && $transparent_id ) {
			echo wp_get_attachment_image(
				$transparent_id,
				'full',
				false,
				array(
					'class'   => 'mrz-logo__img mrz-logo__img--transparent',
					'alt'     => '',
					'loading' => 'eager',
				)
			);
		}
	} else {
		echo '<span class="mrz-logo__text">' . esc_html( $site_name ) . '</span>';
	}

	echo '</a>';
}

/**
 * Bouton Menu sur ordinateur : none (mobile seulement), replace (à la place du menu
 * principal) ou add (en plus). L'ancienne case cochée vaut replace.
 *
 * @return string
 */
function mrz_theme_menu_button_desktop() {
	$value = mrz_theme_option( 'menu_button_desktop' );
	if ( true === $value || '1' === $value || 1 === $value ) {
		return 'replace';
	}

	return in_array( $value, array( 'replace', 'add' ), true ) ? $value : 'none';
}

/**
 * Menu principal pour le desktop.
 *
 * @param string $extra_class Classes CSS ajoutées à la nav.
 */
function mrz_theme_primary_nav( $extra_class = '' ) {
	// Bouton Menu à la place du menu principal : le menu ne s'ouvre que dans le panneau.
	if ( ! has_nav_menu( 'primary' ) || 'replace' === mrz_theme_menu_button_desktop() ) {
		return;
	}

	echo '<nav class="mrz-nav mrz-desktop-only ' . esc_attr( $extra_class ) . '" aria-label="' . esc_attr__( 'Main menu', 'mrz-theme-exp' ) . '">';
	wp_nav_menu(
		array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'mrz-menu',
			'depth'          => 3,
			'fallback_cb'    => false,
		)
	);
	echo '</nav>';
}

/**
 * Menu secondaire du header (à droite), s'il est affecté dans Apparence > Menus.
 */
function mrz_theme_secondary_nav() {
	if ( ! has_nav_menu( 'secondary' ) ) {
		return;
	}

	echo '<nav class="mrz-nav mrz-nav--secondary mrz-desktop-only" aria-label="' . esc_attr__( 'Secondary menu', 'mrz-theme-exp' ) . '">';
	wp_nav_menu(
		array(
			'theme_location' => 'secondary',
			'container'      => false,
			'menu_class'     => 'mrz-menu mrz-menu--secondary',
			'depth'          => 2,
			'fallback_cb'    => false,
		)
	);
	echo '</nav>';
}

/**
 * Outils du header : menu secondaire, langues, recherche, compte, panier, bouton du menu mobile.
 */
function mrz_theme_header_tools() {
	echo '<div class="mrz-header__tools">';

	/**
	 * Début des outils du header : un bouton, un téléphone… sans copier le template.
	 */
	do_action( 'mrz_header_tools_start' );

	mrz_theme_secondary_nav();
	mrz_theme_language_switcher( 'mrz-desktop-only' );
	mrz_theme_header_search();
	mrz_theme_account_link();
	mrz_theme_cart_link();

	/**
	 * Fin des outils du header, juste avant le bouton du menu mobile.
	 */
	do_action( 'mrz_header_tools_end' );
	?>
	<button type="button" class="mrz-burger<?php echo 'none' === mrz_theme_menu_button_desktop() ? ' mrz-mobile-only' : ''; ?>" data-mrz-menu-toggle aria-controls="mrz-mobile-menu" aria-expanded="false">
		<svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
		<?php if ( mrz_theme_option( 'menu_button_label' ) ) : ?>
			<?php // Libellé visible : il sert aussi de nom au bouton (RGAA 11.9, l'état vient d'aria-expanded). ?>
			<span class="mrz-burger__label"><?php echo esc_html_x( 'Menu', 'menu button', 'mrz-theme-exp' ); ?></span>
		<?php else : ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'mrz-theme-exp' ); ?></span>
		<?php endif; ?>
	</button>
	<?php
	echo '</div>';
}

/**
 * Sélecteur de langue Polylang, si le plugin est actif.
 *
 * @param string $extra_class Classes CSS ajoutées à la liste.
 */
function mrz_theme_language_switcher( $extra_class = '' ) {
	// Rien à choisir sous deux langues.
	if ( ! function_exists( 'pll_the_languages' ) || ! function_exists( 'pll_languages_list' ) || count( pll_languages_list() ) < 2 ) {
		return;
	}

	$display    = mrz_theme_option( 'lang_switcher' );
	$show_flags = in_array( $display, array( 'flags', 'both' ), true );
	$show_codes = 'flags' !== $display;

	if ( defined( 'POLYLANG_VERSION' ) && version_compare( POLYLANG_VERSION, '3.9', '>=' ) ) {
		$args = array(
			'echo'                   => 0,
			'show_wrapper'           => false,
			'show_flags'             => $show_flags,
			'show_labels'            => $show_codes ? 'codes' : '',
			'hide_if_no_translation' => false,
		);
	} else {
		// Polylang avant 3.9 : anciens arguments.
		$args = array(
			'echo'                   => 0,
			'show_flags'             => (int) $show_flags,
			'show_names'             => (int) $show_codes,
			'display_names_as'       => 'slug',
			'hide_if_no_translation' => 0,
		);
	}

	$items = pll_the_languages( $args );

	if ( $items ) {
		// Les drapeaux de Polylang sont des images en data: : protocole autorisé ici seulement.
		echo '<ul class="mrz-lang ' . esc_attr( $extra_class ) . '">' . wp_kses( $items, 'post', array_merge( wp_allowed_protocols(), array( 'data' ) ) ) . '</ul>';
	}
}

/**
 * ID d'un contenu dans la langue de la page (Polylang), ou l'ID tel quel.
 *
 * @param int $post_id ID d'origine (section globale, page 404…).
 * @return int
 */
function mrz_theme_translated_id( $post_id ) {
	$post_id = absint( $post_id );

	if ( $post_id && function_exists( 'pll_get_post' ) ) {
		$translated = pll_get_post( $post_id );
		if ( $translated ) {
			return (int) $translated;
		}
	}

	return $post_id;
}

/**
 * Affiche une icône SVG de assets/icons/, en ligne pour hériter de la couleur du texte.
 *
 * get_theme_file_path() cherche d'abord dans le thème enfant : pour changer une
 * icône sur un site, déposer un fichier du même nom dans assets/icons/ de l'enfant.
 *
 * @param string $name Nom du fichier sans extension (cart, account, search).
 */
function mrz_theme_icon( $name ) {
	static $cache = array();

	if ( ! isset( $cache[ $name ] ) ) {
		$file           = get_theme_file_path( 'assets/icons/' . sanitize_file_name( $name ) . '.svg' );
		$svg            = is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fichier local du thème.
		$cache[ $name ] = preg_replace( '/<svg\b/', '<svg class="mrz-icon" aria-hidden="true" focusable="false"', $svg, 1 );
	}

	echo $cache[ $name ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG fourni par le thème.
}

/**
 * Recherche du header, si l'option est cochée : une icône qui déplie le
 * formulaire de recherche natif (details/summary, sans JS pour l'ouvrir).
 */
function mrz_theme_header_search() {
	if ( ! mrz_theme_option( 'header_search' ) ) {
		return;
	}
	?>
	<details class="mrz-header-search">
		<summary class="mrz-header-search__toggle">
			<?php mrz_theme_icon( 'search' ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Search', 'mrz-theme-exp' ); ?></span>
		</summary>
		<div class="mrz-header-search__panel"><?php get_search_form(); ?></div>
	</details>
	<?php
}

/**
 * Lien vers le compte client WooCommerce, si l'option est cochée.
 */
function mrz_theme_account_link() {
	if ( ! mrz_theme_option( 'header_account' ) || ! function_exists( 'wc_get_page_permalink' ) ) {
		return;
	}
	?>
	<a class="mrz-account" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
		<?php mrz_theme_icon( 'account' ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'My account', 'mrz-theme-exp' ); ?></span>
	</a>
	<?php
}

/**
 * Lien vers le panier WooCommerce avec le nombre d'articles.
 */
function mrz_theme_cart_link() {
	if ( ! function_exists( 'wc_get_cart_url' ) || ! WC()->cart ) {
		return;
	}
	?>
	<?php // Panneau du panier : le lien reste, pour les visiteurs sans JavaScript. ?>
	<a class="mrz-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>"<?php echo function_exists( 'mrz_theme_cart_drawer_enabled' ) && mrz_theme_cart_drawer_enabled() ? ' aria-haspopup="dialog" aria-controls="mrz-minicart"' : ''; ?>>
		<?php mrz_theme_icon( 'cart' ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'mrz-theme-exp' ); ?></span>
		<?php echo mrz_theme_cart_count(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML échappé dans la fonction. ?>
	</a>
	<?php
}

/**
 * Pastille du nombre d'articles (rafraîchie par les fragments WooCommerce).
 *
 * @return string
 */
function mrz_theme_cart_count() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

	return '<span class="mrz-cart__count"' . ( $count ? '' : ' hidden' ) . '>' . esc_html( $count ) . '</span>';
}

/**
 * Date et catégories d'un article.
 */
function mrz_theme_posted_on() {
	printf(
		'<time datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);

	if ( 'post' === get_post_type() ) {
		$categories = get_the_category_list( ', ' );
		if ( $categories ) {
			echo ' · <span class="mrz-meta__cats">' . wp_kses_post( $categories ) . '</span>';
		}
	}
}

/**
 * Titre de la page de liste (blog, archive, recherche).
 *
 * @return string
 */
function mrz_theme_listing_title() {
	if ( is_home() && ! is_front_page() ) {
		return get_the_title( (int) get_option( 'page_for_posts' ) );
	}
	if ( is_search() ) {
		/* translators: %s: search query. */
		return sprintf( __( 'Search results for "%s"', 'mrz-theme-exp' ), get_search_query() );
	}
	if ( is_archive() ) {
		return wp_strip_all_tags( get_the_archive_title() );
	}

	return __( 'Latest articles', 'mrz-theme-exp' );
}

/**
 * Bouton à partir d'un champ ACF « lien ».
 *
 * @param array|null $link  Tableau url, title, target.
 * @param string     $style primary, outline ou link (lien souligné suivi d'une flèche).
 */
function mrz_theme_button( $link, $style = 'primary' ) {
	if ( empty( $link['url'] ) ) {
		return;
	}

	$target = ! empty( $link['target'] ) ? $link['target'] : '_self';

	printf(
		'<a class="mrz-btn mrz-btn--%1$s" href="%2$s" target="%3$s"%4$s>%5$s</a>',
		esc_attr( in_array( $style, array( 'outline', 'link' ), true ) ? $style : 'primary' ),
		esc_url( $link['url'] ),
		esc_attr( $target ),
		'_blank' === $target ? ' rel="noopener"' : '',
		esc_html( $link['title'] ? $link['title'] : $link['url'] )
	);
}

/**
 * Remplace les balises {year} et {site} dans un texte (copyright d'un footer…).
 *
 * @param string $text Texte.
 * @return string
 */
function mrz_theme_text_tags( $text ) {
	return str_replace(
		array( '{year}', '{site}' ),
		array( wp_date( 'Y' ), get_bloginfo( 'name' ) ),
		(string) $text
	);
}
