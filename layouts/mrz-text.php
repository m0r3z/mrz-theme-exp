<?php
/**
 * Section du parent « Texte libre » : titre et texte de l’éditeur classique.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

mrz_theme_section_open( 'mrz-text' );
?>
<div class="mrz-prose">
	<?php
	if ( get_sub_field( 'title' ) ) {
		$mrz_title_tag = 'h1' === get_sub_field( 'title_tag' ) ? 'h1' : 'h2';
		printf( '<%1$s>%2$s</%1$s>', $mrz_title_tag, esc_html( get_sub_field( 'title' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- balise choisie dans une liste fermée.
	}
	?>
	<?php
	/*
	 * Contenu de l'éditeur, déjà filtré par WordPress à l'enregistrement et mis en
	 * forme par ACF (shortcodes compris). Pas de wp_kses_post() ici : il retirerait
	 * les champs des formulaires insérés par shortcode (Gravity Forms).
	 */
	echo get_sub_field( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</div>
<?php
mrz_theme_section_close();
