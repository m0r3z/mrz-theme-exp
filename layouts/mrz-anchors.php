<?php
/**
 * Section du parent « Sous-menu d'ancres » : des liens vers les sections de la page,
 * collés sous le header en option. Le lien de la section à l'écran est mis en évidence
 * (assets/js/anchors.js). Apparence : variables --mrz-anchors-*.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_items = array_filter(
	get_sub_field( 'items' ) ?: array(), // phpcs:ignore Universal.Operators.DisallowShortTernary
	static function ( $item ) {
		return ! empty( $item['label'] ) && ! empty( $item['anchor'] );
	}
);
if ( ! $mrz_items ) {
	return;
}

wp_enqueue_script( 'mrz-anchors' );

$mrz_classes = array( 'center' === get_sub_field( 'align' ) ? 'mrz-anchors--center' : 'mrz-anchors--left' );
if ( get_sub_field( 'sticky' ) ) {
	$mrz_classes[] = 'mrz-anchors--sticky';
}

mrz_theme_section_open( 'mrz-anchors', array( 'class' => $mrz_classes ) );
?>
<nav class="mrz-anchors" aria-label="<?php esc_attr_e( 'On this page', 'mrz-theme-exp' ); ?>" data-mrz-anchors>
	<ul class="mrz-anchors__list">
		<?php foreach ( $mrz_items as $mrz_item ) : ?>
			<li><a class="mrz-anchors__link" href="#<?php echo esc_attr( sanitize_title( $mrz_item['anchor'] ) ); ?>"><?php echo esc_html( $mrz_item['label'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php
mrz_theme_section_close();
