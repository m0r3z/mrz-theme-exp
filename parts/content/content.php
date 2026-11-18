<?php
/**
 * Carte d'un article dans une liste.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'mrz-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="mrz-card__media aspect-[16/10]">
			<?php the_post_thumbnail( 'medium_large', array( 'class' => 'h-full w-full object-cover' ) ); ?>
		</div>
	<?php endif; ?>
	<div class="flex flex-1 flex-col gap-3 p-6">
		<p class="text-sm text-muted"><?php mrz_theme_posted_on(); ?></p>
		<h2 class="text-xl font-title"><a class="mrz-stretched-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php if ( mrz_theme_option( 'blog_excerpt' ) ) : ?>
			<div class="text-muted"><?php the_excerpt(); ?></div>
		<?php endif; ?>
	</div>
</article>
