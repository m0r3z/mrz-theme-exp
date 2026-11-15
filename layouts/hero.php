<?php
/**
 * Layout Hero : titre, sous-titre, boutons, image de fond.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_image   = (int) get_sub_field( 'image' );
$mrz_height  = get_sub_field( 'height' );
$mrz_align   = get_sub_field( 'align' );
$mrz_buttons = (array) get_sub_field( 'buttons' );

$mrz_heights = array(
	'auto'  => '',
	'large' => 'min-h-[70vh]',
	'full'  => 'min-h-svh',
);

mrz_theme_section_open(
	'hero',
	array(
		'image' => $mrz_image,
		'class' => 'flex items-center ' . ( $mrz_heights[ $mrz_height ] ?? $mrz_heights['large'] ),
	)
);
?>
<div class="mrz-hero__inner <?php echo 'left' === $mrz_align ? 'text-left' : 'mx-auto text-center'; ?>">
	<?php if ( get_sub_field( 'title' ) ) : ?>
		<h1 class="mrz-hero__title"><?php echo esc_html( get_sub_field( 'title' ) ); ?></h1>
	<?php endif; ?>
	<?php if ( get_sub_field( 'subtitle' ) ) : ?>
		<p class="mrz-hero__subtitle"><?php echo esc_html( get_sub_field( 'subtitle' ) ); ?></p>
	<?php endif; ?>
	<?php if ( $mrz_buttons ) : ?>
		<div class="mt-8 flex flex-wrap gap-4 <?php echo 'left' === $mrz_align ? '' : 'justify-center'; ?>">
			<?php
			foreach ( $mrz_buttons as $mrz_button ) {
				mrz_theme_button( $mrz_button['link'] ?? null, $mrz_button['style'] ?? 'primary' );
			}
			?>
		</div>
	<?php endif; ?>
</div>
<?php
mrz_theme_section_close();
