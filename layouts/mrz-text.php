<?php
/**
 * Section du parent « Texte libre » : titre et texte de l’éditeur classique.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

mrz_theme_section_open( 'mrz-text' );

$mrz_links = array_filter(
	array(
		'primary' => get_sub_field( 'link' ),
		'outline' => get_sub_field( 'link_2' ),
	),
	static function ( $link ) {
		return ! empty( $link['url'] );
	}
);
?>
<div class="mrz-prose<?php echo 'center' === get_sub_field( 'align' ) ? ' mrz-text--center' : ''; ?>">
	<?php
	if ( get_sub_field( 'eyebrow' ) ) {
		echo '<p class="mrz-eyebrow">' . esc_html( get_sub_field( 'eyebrow' ) ) . '</p>';
	}
	if ( get_sub_field( 'title' ) ) {
		$mrz_title_tag = mrz_theme_heading_tag( array_key_exists( (string) get_sub_field( 'title_tag' ), mrz_theme_title_tag_choices() ) ? get_sub_field( 'title_tag' ) : 'h2' );
		printf( '<%1$s>%2$s</%1$s>', $mrz_title_tag, mrz_theme_title_html( get_sub_field( 'title' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- balise choisie dans une liste fermée, titre filtré par wp_kses.
	}
	?>
	<?php
	/*
	 * Contenu de l'éditeur, déjà filtré par WordPress à l'enregistrement et mis en
	 * forme par ACF (shortcodes compris). Pas de wp_kses_post() ici : il retirerait
	 * les champs des formulaires insérés par shortcode (Gravity Forms).
	 */
	echo get_sub_field( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	if ( $mrz_links ) {
		echo '<div class="mrz-text__actions">';
		foreach ( $mrz_links as $mrz_style => $mrz_link ) {
			mrz_theme_button( $mrz_link, $mrz_style );
		}
		echo '</div>';
	}
	?>
</div>
<?php
mrz_theme_section_close();
