<?php
/**
 * Article seul (repli pour tous les types de contenus).
 * Deux dispositions (Customizer > MRZ Theme > Blog) : classique (titre, puis image à la
 * une) ou bannière (titre posé sur l'image, header transparent par-dessus).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_theme_banner = mrz_theme_is_post_banner();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $mrz_theme_banner ? 'mrz-post--banner' : 'mrz-container py-16' ); ?>>
	<?php if ( $mrz_theme_banner ) : ?>
		<?php
		// Première section de la page : son image est l'image principale (chargement prioritaire).
		$GLOBALS['mrz_theme_section_index'] = ( $GLOBALS['mrz_theme_section_index'] ?? 0 ) + 1;
		?>
		<header class="mrz-section mrz-post-banner mrz-bg--none mrz-space--large mrz-height--large mrz-has-bg-image mrz-has-image">
			<?php mrz_theme_section_background_image( get_post_thumbnail_id() ); ?>
			<div class="mrz-container">
				<div class="mx-auto max-w-3xl">
					<p class="mb-3 text-sm text-muted"><?php mrz_theme_posted_on(); ?></p>
					<h1 class="mrz-page-title"><?php the_title(); ?></h1>
				</div>
			</div>
		</header>
		<div class="mrz-container py-16">
	<?php else : ?>
		<header class="mx-auto mb-10 max-w-3xl">
			<p class="mb-3 text-sm text-muted"><?php mrz_theme_posted_on(); ?></p>
			<h1 class="mrz-page-title"><?php the_title(); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="mx-auto mb-12 max-w-5xl overflow-hidden rounded-base">
				<?php the_post_thumbnail( 'large', array( 'class' => 'h-auto w-full' ) ); ?>
			</figure>
		<?php endif; ?>
	<?php endif; ?>

	<div class="mrz-prose mx-auto max-w-3xl">
		<?php
		the_content();
		wp_link_pages();
		?>
	</div>

	<footer class="mx-auto mt-12 max-w-3xl">
		<?php the_tags( '<p class="mrz-tags text-sm text-muted">', ', ', '</p>' ); ?>
		<?php
		// Articles précédent et suivant : réglage Customizer > MRZ Theme > Blog.
		if ( mrz_theme_option( 'blog_post_nav' ) ) {
			the_post_navigation(
				array(
					'class'     => 'mrz-post-nav',
					'prev_text' => '<span class="text-sm text-muted">' . esc_html__( 'Previous', 'mrz-theme-exp' ) . '</span><br>%title',
					'next_text' => '<span class="text-sm text-muted">' . esc_html__( 'Next', 'mrz-theme-exp' ) . '</span><br>%title',
				)
			);
		}

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</footer>
	<?php if ( $mrz_theme_banner ) : ?>
		</div>
	<?php endif; ?>
</article>
