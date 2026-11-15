<?php
/**
 * Layout FAQ : accordéon natif (details/summary), sans JavaScript.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_items = (array) get_sub_field( 'items' );

mrz_theme_section_open( 'faq' );
mrz_theme_section_heading( get_sub_field( 'title' ) );
?>
<div class="mrz-faq mx-auto max-w-3xl">
	<?php foreach ( $mrz_items as $mrz_item ) : ?>
		<details class="mrz-faq__item">
			<summary class="mrz-faq__question"><?php echo esc_html( $mrz_item['question'] ?? '' ); ?></summary>
			<div class="mrz-faq__answer mrz-prose"><?php echo wp_kses_post( $mrz_item['answer'] ?? '' ); ?></div>
		</details>
	<?php endforeach; ?>
</div>
<?php
mrz_theme_section_close();
