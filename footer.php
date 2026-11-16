<?php
/**
 * Pied de page.
 *
 * Le thème n'affiche pas de footer par défaut : le footer d'un site est une
 * section globale placée à l'emplacement « Footer », que le thème enveloppe
 * dans la balise <footer>.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

do_action( 'mrz_after_content' );
?>
</main>
<?php
do_action( 'mrz_before_footer' );

if ( has_action( 'mrz_footer' ) ) {
	echo '<footer class="mrz-footer">';
	do_action( 'mrz_footer' );
	echo '</footer>';
}

do_action( 'mrz_after_footer' );
wp_footer();
?>
</body>
</html>
