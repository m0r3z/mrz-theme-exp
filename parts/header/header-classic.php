<?php
/**
 * Header classique : logo à gauche, menu à droite.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="mrz-header mrz-header--classic">
	<div class="mrz-container flex items-center justify-between gap-8 py-4">
		<?php mrz_theme_logo(); ?>
		<div class="flex items-center gap-6">
			<?php mrz_theme_primary_nav(); ?>
			<?php mrz_theme_header_tools(); ?>
		</div>
	</div>
</header>
