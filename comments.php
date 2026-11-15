<?php
/**
 * Commentaires.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="mrz-comments mt-16">
	<?php if ( have_comments() ) : ?>
		<h2 class="mb-6 text-2xl font-title">
			<?php
			/* translators: %d: number of comments. */
			echo esc_html( sprintf( _n( '%d comment', '%d comments', get_comments_number(), 'mrz-theme-exp' ), get_comments_number() ) );
			?>
		</h2>
		<ol class="mrz-comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="text-muted"><?php esc_html_e( 'Comments are closed.', 'mrz-theme-exp' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</section>
