<?php
/**
 * Section du parent « Footer » : logo centré (facultatif), phrase, bouton et
 * réseaux sociaux à gauche, colonnes de liens à droite, ligne du bas.
 * À placer dans une section globale, emplacement « Footer » (elle n'est proposée
 * que là). Apparence : variables --mrz-footer-* (src/css/tokens.css).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_logo    = (int) get_sub_field( 'logo' );
$mrz_title   = (string) get_sub_field( 'title' );
$mrz_button  = get_sub_field( 'button' );
$mrz_columns = get_sub_field( 'columns' ) ?: array(); // phpcs:ignore Universal.Operators.DisallowShortTernary
$mrz_socials = get_sub_field( 'socials' ) ?: array(); // phpcs:ignore Universal.Operators.DisallowShortTernary
$mrz_bottom  = (string) get_sub_field( 'bottom' );

mrz_theme_section_open( 'mrz-footer' );
?>
<?php if ( $mrz_logo ) : ?>
	<a class="mrz-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<?php echo wp_get_attachment_image( $mrz_logo, 'medium', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
	</a>
<?php endif; ?>

<div class="mrz-footer__main">
	<div class="mrz-footer__cta">
		<?php if ( $mrz_title ) : ?>
			<p class="mrz-footer__title"><?php echo wp_kses( $mrz_title, array( 'br' => array() ) ); ?></p>
		<?php endif; ?>
		<?php mrz_theme_button( $mrz_button ); ?>

		<?php if ( $mrz_socials ) : ?>
			<ul class="mrz-footer__socials">
				<?php
				foreach ( $mrz_socials as $mrz_social ) :
					$mrz_icon_url = wp_get_attachment_url( (int) ( $mrz_social['icon'] ?? 0 ) );
					if ( empty( $mrz_social['url'] ) || ! $mrz_icon_url ) {
						continue;
					}
					?>
					<li>
						<a href="<?php echo esc_url( $mrz_social['url'] ); ?>" target="_blank" rel="noopener">
							<?php // Icône en masque : elle prend la couleur du texte, sur n'importe quel fond. ?>
							<span class="mrz-footer__social-icon" style="--mrz-footer-social-icon:url('<?php echo esc_url( $mrz_icon_url ); ?>')" aria-hidden="true"></span>
							<span class="screen-reader-text"><?php echo esc_html( $mrz_social['name'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<?php if ( $mrz_columns ) : ?>
		<div class="mrz-footer__columns">
			<?php foreach ( $mrz_columns as $mrz_column ) : ?>
				<nav class="mrz-footer__column"<?php echo ! empty( $mrz_column['title'] ) ? ' aria-label="' . esc_attr( $mrz_column['title'] ) . '"' : ''; ?>>
					<?php if ( ! empty( $mrz_column['title'] ) ) : ?>
						<h2 class="mrz-footer__heading"><?php echo esc_html( $mrz_column['title'] ); ?></h2>
					<?php endif; ?>
					<?php echo wp_kses_post( $mrz_column['links'] ?? '' ); ?>
				</nav>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<?php if ( $mrz_bottom ) : ?>
	<div class="mrz-footer__bottom"><?php echo wp_kses_post( mrz_theme_text_tags( $mrz_bottom ) ); ?></div>
<?php endif; ?>
<?php
mrz_theme_section_close();
