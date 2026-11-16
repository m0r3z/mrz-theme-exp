<?php
/**
 * Article seul (repli pour tous les types de contenus).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'mrz-container py-16' ); ?>>
	<header class="mx-auto mb-10 max-w-3xl">
		<p class="mb-3 text-sm text-muted"><?php mrz_theme_posted_on(); ?></p>
		<h1 class="mrz-page-title"><?php the_title(); ?></h1>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="mx-auto mb-12 max-w-5xl overflow-hidden rounded-base">
			<?php the_post_thumbnail( 'large', array( 'class' => 'h-auto w-full' ) ); ?>
		</figure>
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
		the_post_navigation(
			array(
				'class'     => 'mrz-post-nav',
				'prev_text' => '<span class="text-sm text-muted">' . esc_html__( 'Previous', 'mrz-theme-exp' ) . '</span><br>%title',
				'next_text' => '<span class="text-sm text-muted">' . esc_html__( 'Next', 'mrz-theme-exp' ) . '</span><br>%title',
			)
		);

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</footer>
</article>
