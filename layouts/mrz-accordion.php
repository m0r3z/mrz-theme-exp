<?php
/**
 * Section du parent « Accordéon » : des éléments qu'on déplie, en <details> natif
 * (sans JavaScript : clavier et lecteurs d'écran gérés par le navigateur).
 * Apparence : variables --mrz-accordion-* (src/css/tokens.css).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_items = get_sub_field( 'items' ) ?: array(); // phpcs:ignore Universal.Operators.DisallowShortTernary
if ( ! $mrz_items ) {
	return;
}

// Un seul ouvert à la fois : même attribut name sur les <details> de la section (natif, sans JS).
$GLOBALS['mrz_theme_accordion_count'] = ( $GLOBALS['mrz_theme_accordion_count'] ?? 0 ) + 1;
$mrz_name = get_sub_field( 'single_open' ) ? ' name="mrz-accordion-' . (int) $GLOBALS['mrz_theme_accordion_count'] . '"' : '';

mrz_theme_section_open( 'mrz-accordion' );
mrz_theme_section_heading( (string) get_sub_field( 'title' ), (string) get_sub_field( 'intro' ) );
?>
<div class="mrz-accordion">
	<?php foreach ( $mrz_items as $mrz_index => $mrz_item ) : ?>
		<?php
		if ( empty( $mrz_item['question'] ) ) {
			continue;
		}
		?>
		<details class="mrz-accordion__item"<?php echo $mrz_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nom construit ici. ?><?php echo 0 === $mrz_index && get_sub_field( 'open_first' ) ? ' open' : ''; ?>>
			<summary class="mrz-accordion__question"><?php echo esc_html( $mrz_item['question'] ); ?></summary>
			<div class="mrz-accordion__answer mrz-prose">
				<?php echo $mrz_item['answer'] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- contenu de l'éditeur mis en forme par ACF. ?>
			</div>
		</details>
	<?php endforeach; ?>
</div>
<?php
mrz_theme_section_close();
