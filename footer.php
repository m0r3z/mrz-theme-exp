<?php
/**
 * Pied de page.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

do_action( 'mrz_after_content' );
?>
</main>
<?php do_action( 'mrz_before_footer' ); ?>
<footer class="mrz-footer">
	<div class="mrz-container grid gap-10 py-16 md:grid-cols-2 lg:grid-cols-4">
		<div class="mrz-footer__brand">
			<?php mrz_theme_logo( 'footer' ); ?>
			<?php if ( get_bloginfo( 'description' ) ) : ?>
				<p class="mt-4 text-sm opacity-80"><?php bloginfo( 'description' ); ?></p>
			<?php endif; ?>
		</div>
		<?php for ( $mrz_column = 1; $mrz_column <= 3; $mrz_column++ ) : ?>
			<?php if ( is_active_sidebar( 'footer-' . $mrz_column ) ) : ?>
				<div class="mrz-footer__column"><?php dynamic_sidebar( 'footer-' . $mrz_column ); ?></div>
			<?php endif; ?>
		<?php endfor; ?>
	</div>
	<div class="mrz-footer__bottom">
		<div class="mrz-container flex flex-col gap-4 py-6 text-sm md:flex-row md:items-center md:justify-between">
			<p><?php echo wp_kses_post( mrz_theme_copyright() ); ?></p>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location'  => 'footer',
						'container'       => 'nav',
						'container_class' => 'mrz-footer__nav',
						'container_aria_label' => __( 'Footer menu', 'mrz-theme-exp' ),
						'menu_class'      => 'mrz-footer__menu',
						'depth'           => 1,
						'fallback_cb'     => false,
					)
				);
			}
			?>
		</div>
	</div>
</footer>
<?php do_action( 'mrz_after_footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
