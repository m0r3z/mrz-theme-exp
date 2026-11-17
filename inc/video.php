<?php
/**
 * Vidéo de fond de section : l'image de fond s'affiche d'abord (image d'attente, LCP),
 * la vidéo ne se télécharge qu'une fois la page affichée, avec un bouton pause (RGAA 13.8).
 * Pas de vidéo sur mobile par défaut, ni si le visiteur limite les animations ou
 * économise ses données : l'image reste seule.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Déclare le script de la vidéo (chargé seulement par mrz_theme_section_background_video()).
 */
function mrz_theme_register_video_script() {
	wp_register_script(
		'mrz-video',
		MRZ_THEME_URI . '/assets/js/video.js',
		array(),
		mrz_theme_asset_version( 'assets/js/video.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_add_inline_script(
		'mrz-video',
		'window.mrzVideo = ' . wp_json_encode(
			array(
				'pause' => __( 'Pause the background video', 'mrz-theme-exp' ),
				'play'  => __( 'Play the background video', 'mrz-theme-exp' ),
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'mrz_theme_register_video_script' );

/**
 * Vidéo de fond et son bouton pause. La source est posée par assets/js/video.js
 * (data-src) : rien ne se télécharge avant l'affichage de la page.
 *
 * @param int  $video_id ID du fichier vidéo.
 * @param bool $mobile   Lire aussi la vidéo sur mobile.
 */
function mrz_theme_section_background_video( $video_id, $mobile = false ) {
	$url = wp_get_attachment_url( $video_id );
	if ( ! $url ) {
		return;
	}

	wp_enqueue_script( 'mrz-video' );

	printf(
		'<video class="mrz-section__video" muted loop playsinline preload="none" aria-hidden="true" data-mrz-video data-src="%1$s" data-type="%2$s"%3$s></video>',
		esc_url( $url ),
		esc_attr( (string) get_post_mime_type( $video_id ) ),
		$mobile ? ' data-mobile' : ''
	);
	printf(
		'<button type="button" class="mrz-section__video-toggle" data-mrz-video-toggle hidden aria-label="%1$s">%2$s</button>',
		esc_attr__( 'Pause the background video', 'mrz-theme-exp' ),
		'<svg class="mrz-section__video-icon-pause" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8 5v14M16 5v14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg><svg class="mrz-section__video-icon-play" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8 5l11 7-11 7z" fill="currentColor"/></svg>'
	);
}
