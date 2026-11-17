<?php
/**
 * Section du parent « Texte et média » : texte d'un côté, image ou contenu
 * (second texte, encadré coloré) de l'autre. Empilés sur mobile, texte en premier.
 * Apparence : variables --mrz-split-* (src/css/tokens.css).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_media_type = 'content' === get_sub_field( 'media_type' ) ? 'content' : 'image';
$mrz_image      = (int) get_sub_field( 'image' );
$mrz_aside      = (string) get_sub_field( 'aside_content' );
$mrz_ratio      = (string) get_sub_field( 'ratio' );
$mrz_sticky     = (string) get_sub_field( 'sticky' );

$mrz_classes = array( 'left' === get_sub_field( 'media_side' ) ? 'mrz-split--media-left' : 'mrz-split--media-right' );
if ( in_array( $mrz_ratio, array( 'text_wide', 'media_wide' ), true ) ) {
	$mrz_classes[] = 'mrz-split--' . str_replace( '_', '-', $mrz_ratio );
}
if ( 'top' === get_sub_field( 'vertical_align' ) ) {
	$mrz_classes[] = 'mrz-split--top';
}
if ( in_array( $mrz_sticky, array( 'media', 'text' ), true ) ) {
	$mrz_classes[] = 'mrz-split--sticky-' . $mrz_sticky;
}

$mrz_links = array_filter(
	array(
		'primary' => get_sub_field( 'link' ),
		'outline' => get_sub_field( 'link_2' ),
	),
	static function ( $link ) {
		return ! empty( $link['url'] );
	}
);

mrz_theme_section_open( 'mrz-split', array( 'class' => $mrz_classes ) );
?>
<div class="mrz-split__grid">
	<div class="mrz-split__text mrz-prose">
		<?php
		if ( get_sub_field( 'eyebrow' ) ) {
			echo '<p class="mrz-eyebrow">' . esc_html( get_sub_field( 'eyebrow' ) ) . '</p>';
		}
		if ( get_sub_field( 'title' ) ) {
			$mrz_title_tag = mrz_theme_heading_tag( 'h1' === get_sub_field( 'title_tag' ) ? 'h1' : 'h2' );
			printf( '<%1$s>%2$s</%1$s>', $mrz_title_tag, mrz_theme_title_html( get_sub_field( 'title' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- balise choisie dans une liste fermée, titre filtré par wp_kses.
		}
		// Contenu de l'éditeur mis en forme par ACF, shortcodes compris (comme le Texte libre).
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
	<?php if ( 'image' === $mrz_media_type && $mrz_image ) : ?>
		<div class="mrz-split__media">
			<?php
			echo wp_get_attachment_image(
				$mrz_image,
				'large',
				false,
				array(
					'class' => 'mrz-split__image',
					'sizes' => '(min-width: 768px) 50vw, 100vw',
					'style' => mrz_theme_image_ratio_style( $mrz_image, 'large' ),
				) + mrz_theme_section_image_loading()
			);
			?>
		</div>
	<?php elseif ( 'content' === $mrz_media_type && $mrz_aside ) : ?>
		<?php $mrz_background = sanitize_html_class( (string) get_sub_field( 'aside_background' ) ); ?>
		<div class="mrz-split__media">
			<div class="mrz-split__aside mrz-prose<?php echo $mrz_background && 'none' !== $mrz_background ? ' mrz-split__box mrz-bg--' . esc_attr( $mrz_background ) : ''; ?>">
				<?php echo $mrz_aside; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- contenu de l'éditeur mis en forme par ACF. ?>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php
mrz_theme_section_close();
