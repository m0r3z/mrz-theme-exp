<?php
/**
 * Section du parent « Bandeau défilant » : textes ou logos qui défilent en continu,
 * en CSS. Bouton pause toujours affiché (RGAA 13.8), arrêt au survol et au focus,
 * aucune animation si le visiteur limite les animations (les éléments s'affichent à la
 * suite). Apparence : variables --mrz-marquee-*.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_items = array_values(
	array_filter(
		get_sub_field( 'items' ) ?: array(), // phpcs:ignore Universal.Operators.DisallowShortTernary
		static function ( $item ) {
			return ! empty( $item['text'] ) || ! empty( $item['image'] );
		}
	)
);
if ( ! $mrz_items ) {
	return;
}

wp_enqueue_script( 'mrz-marquee' );

$mrz_speed     = in_array( get_sub_field( 'speed' ), array( 'slow', 'fast' ), true ) ? get_sub_field( 'speed' ) : 'normal';
$mrz_direction = 'right' === get_sub_field( 'direction' ) ? 'right' : 'left';
$mrz_separator = (string) get_sub_field( 'separator' );

/*
 * Assez d'éléments pour remplir un grand écran : la liste est répétée. Seule la
 * première occurrence de chaque élément est lue par les lecteurs d'écran.
 */
$mrz_repeat = max( 1, (int) ceil( 12 / count( $mrz_items ) ) );

/**
 * Une liste du bandeau.
 *
 * @param array  $items     Éléments.
 * @param int    $repeat    Nombre de répétitions.
 * @param string $separator Séparateur entre deux textes.
 * @param bool   $hidden    Liste entière masquée (copie pour la boucle).
 */
$mrz_list = static function ( $items, $repeat, $separator, $hidden ) {
	echo '<ul class="mrz-marquee__list"' . ( $hidden ? ' aria-hidden="true" inert' : '' ) . '>';
	for ( $round = 0; $round < $repeat; $round++ ) {
		foreach ( $items as $item ) {
			$copy = $hidden || $round > 0;
			echo '<li class="mrz-marquee__item"' . ( $copy ? ' aria-hidden="true"' : '' ) . '>';
			if ( ! empty( $item['image'] ) ) {
				// Logo : le texte de l'élément sert d'alternative (sinon celle de la médiathèque).
				$attributes = array(
					'class'   => 'mrz-marquee__logo',
					'loading' => 'lazy',
				);
				if ( ! empty( $item['text'] ) ) {
					$attributes['alt'] = $item['text'];
				}
				$content = wp_get_attachment_image( (int) $item['image'], 'medium', false, $attributes );
			} else {
				$content = '<span class="mrz-marquee__text">' . esc_html( $item['text'] ) . '</span>';
			}
			if ( ! empty( $item['link']['url'] ) ) {
				$target  = ! empty( $item['link']['target'] ) ? $item['link']['target'] : '_self';
				$content = sprintf(
					'<a href="%1$s" target="%2$s"%3$s%4$s>%5$s</a>',
					esc_url( $item['link']['url'] ),
					esc_attr( $target ),
					'_blank' === $target ? ' rel="noopener"' : '',
					$copy ? ' tabindex="-1"' : '',
					$content
				);
			}
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus.
			if ( $separator && empty( $item['image'] ) ) {
				echo '<span class="mrz-marquee__separator" aria-hidden="true">' . esc_html( $separator ) . '</span>';
			}
			echo '</li>';
		}
	}
	echo '</ul>';
};

mrz_theme_section_open( 'mrz-marquee', array( 'class' => array( 'mrz-marquee--' . $mrz_speed, 'mrz-marquee--' . $mrz_direction ) ) );
?>
<div class="mrz-marquee" data-mrz-marquee>
	<div class="mrz-marquee__track">
		<?php
		$mrz_list( $mrz_items, $mrz_repeat, $mrz_separator, false );
		$mrz_list( $mrz_items, $mrz_repeat, $mrz_separator, true );
		?>
	</div>
	<button type="button" class="mrz-marquee__pause" data-mrz-marquee-pause aria-label="<?php esc_attr_e( 'Pause the scrolling band', 'mrz-theme-exp' ); ?>">
		<svg class="mrz-marquee__icon-pause" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8 5v14M16 5v14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
		<svg class="mrz-marquee__icon-play" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8 5l11 7-11 7z" fill="currentColor"/></svg>
	</button>
</div>
<?php
mrz_theme_section_close();
