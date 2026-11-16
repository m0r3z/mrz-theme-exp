<?php
/**
 * Section du parent « Texte libre » : titre et texte de l’éditeur classique.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

mrz_theme_section_open( 'mrz-text' );
?>
<div class="mrz-prose mx-auto max-w-3xl">
	<?php if ( get_sub_field( 'title' ) ) : ?>
		<h2><?php echo esc_html( get_sub_field( 'title' ) ); ?></h2>
	<?php endif; ?>
	<?php echo wp_kses_post( get_sub_field( 'content' ) ); ?>
</div>
<?php
mrz_theme_section_close();
