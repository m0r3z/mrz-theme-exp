<?php
/**
 * Layout Grille de cartes.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_items   = (array) get_sub_field( 'items' );
$mrz_columns = array(
	'2' => 'md:grid-cols-2',
	'3' => 'md:grid-cols-2 lg:grid-cols-3',
	'4' => 'md:grid-cols-2 lg:grid-cols-4',
);

mrz_theme_section_open( 'cards' );
mrz_theme_section_heading( get_sub_field( 'title' ), get_sub_field( 'intro' ) );
?>
<div class="grid gap-8 <?php echo esc_attr( $mrz_columns[ get_sub_field( 'columns' ) ] ?? $mrz_columns['3'] ); ?>">
	<?php foreach ( $mrz_items as $mrz_item ) : ?>
		<article class="mrz-card">
			<?php if ( ! empty( $mrz_item['image'] ) ) : ?>
				<div class="mrz-card__media aspect-[4/3]">
					<?php echo wp_get_attachment_image( (int) $mrz_item['image'], 'medium_large', false, array( 'class' => 'h-full w-full object-cover' ) ); ?>
				</div>
			<?php endif; ?>
			<div class="flex flex-1 flex-col gap-3 p-6">
				<?php if ( ! empty( $mrz_item['title'] ) ) : ?>
					<h3 class="text-xl font-title">
						<?php if ( ! empty( $mrz_item['link']['url'] ) ) : ?>
							<a class="mrz-stretched-link" href="<?php echo esc_url( $mrz_item['link']['url'] ); ?>" target="<?php echo esc_attr( $mrz_item['link']['target'] ? $mrz_item['link']['target'] : '_self' ); ?>"><?php echo esc_html( $mrz_item['title'] ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $mrz_item['title'] ); ?>
						<?php endif; ?>
					</h3>
				<?php endif; ?>
				<?php if ( ! empty( $mrz_item['text'] ) ) : ?>
					<p class="text-muted"><?php echo esc_html( $mrz_item['text'] ); ?></p>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
<?php
mrz_theme_section_close();
