/**
 * MRZ Theme Experience : lightbox (vanilla, sans dépendance, <dialog> natif).
 *
 * S'ouvre sur : les images de la section Galerie (data-mrz-lightbox), les liens vers une
 * image ou une vidéo YouTube ou Vimeo dans un texte (.mrz-prose), les galeries natives.
 * Image : affichée en entier ; si elle est plus grande que l'écran, un clic (ou le bouton
 * loupe) la montre en taille réelle, on s'y déplace en faisant défiler.
 * Vidéo : lecteur chargé seulement à l'ouverture (youtube-nocookie, Vimeo sans traceurs).
 * Clavier : flèches, Échap ; focus rendu au lien à la fermeture. Glissé au doigt.
 * Chargé seulement sur les pages qui en ont besoin (inc/lightbox.php).
 */
( function () {
	'use strict';

	var i18n = window.mrzLightbox || {};
	var imagePattern = /\.(jpe?g|png|gif|webp|avif)(\?.*)?$/i;

	/* Lien vers une vidéo : adresse du lecteur intégré, ou null. */
	function videoEmbed( href ) {
		var match = href.match( /(?:youtube\.com\/watch\?(?:.*&)?v=|youtu\.be\/)([\w-]{11})/ );
		if ( match ) {
			return 'https://www.youtube-nocookie.com/embed/' + match[ 1 ] + '?autoplay=1&rel=0';
		}
		match = href.match( /vimeo\.com\/(\d+)/ );
		if ( match ) {
			return 'https://player.vimeo.com/video/' + match[ 1 ] + '?autoplay=1&dnt=1';
		}
		return null;
	}

	function format( text, first, second ) {
		return String( text || '' ).replace( '%1$s', first ).replace( '%2$s', second );
	}

	/* Liens concernés, rangés par groupe (une galerie, un texte). */
	var groups = {};
	var links = [];

	document.querySelectorAll( '[data-mrz-lightbox], .mrz-prose a[href], .gallery a[href]' ).forEach( function ( link ) {
		if ( links.indexOf( link ) > -1 ) {
			return;
		}
		var href = link.getAttribute( 'href' ) || '';
		var isImage = imagePattern.test( href );
		var embed = isImage ? null : videoEmbed( href );
		if ( ! link.hasAttribute( 'data-mrz-lightbox' ) && ! isImage && ! embed ) {
			return;
		}
		var container = link.closest( '.gallery, .mrz-prose' );
		var group = link.getAttribute( 'data-mrz-lightbox' ) || ( container ? 'group-' + Array.prototype.indexOf.call( document.querySelectorAll( '.gallery, .mrz-prose' ), container ) : 'single' );
		var image = link.querySelector( 'img' );
		var figure = link.closest( 'figure' );
		var caption = link.getAttribute( 'data-caption' ) || ( figure && figure.querySelector( 'figcaption' ) ? figure.querySelector( 'figcaption' ).textContent.trim() : '' );

		groups[ group ] = groups[ group ] || [];
		groups[ group ].push( {
			link: link,
			href: href,
			embed: embed,
			alt: image ? image.getAttribute( 'alt' ) || '' : link.textContent.trim(),
			caption: caption,
		} );
		links.push( link );
	} );

	if ( ! links.length ) {
		return;
	}

	var dialog = null;
	var stage, counter, captionBox, zoomButton, prevButton, nextButton;
	var items = [];
	var current = 0;
	var opener = null;

	function button( className, label, path ) {
		var element = document.createElement( 'button' );
		element.type = 'button';
		element.className = 'mrz-lightbox__button ' + className;
		element.setAttribute( 'aria-label', label );
		element.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="' + path + '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		return element;
	}

	function build() {
		dialog = document.createElement( 'dialog' );
		dialog.className = 'mrz-lightbox';
		dialog.setAttribute( 'aria-label', i18n.label || 'Viewer' );

		var bar = document.createElement( 'div' );
		bar.className = 'mrz-lightbox__bar';
		counter = document.createElement( 'p' );
		counter.className = 'mrz-lightbox__counter';
		counter.setAttribute( 'aria-live', 'polite' );
		zoomButton = button( 'mrz-lightbox__zoom', i18n.zoomIn || 'Zoom', 'M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zM20 20l-4-4M11 8v6M8 11h6' );
		var closeButton = button( 'mrz-lightbox__close', i18n.close || 'Close', 'M6 6l12 12M18 6L6 18' );
		bar.append( counter, zoomButton, closeButton );

		stage = document.createElement( 'div' );
		stage.className = 'mrz-lightbox__stage';

		captionBox = document.createElement( 'p' );
		captionBox.className = 'mrz-lightbox__caption';

		prevButton = button( 'mrz-lightbox__prev', i18n.previous || 'Previous', 'M15 6l-6 6 6 6' );
		nextButton = button( 'mrz-lightbox__next', i18n.next || 'Next', 'M9 6l6 6-6 6' );

		dialog.append( bar, stage, captionBox, prevButton, nextButton );
		document.body.appendChild( dialog );

		closeButton.addEventListener( 'click', close );
		prevButton.addEventListener( 'click', function () {
			show( current - 1 );
		} );
		nextButton.addEventListener( 'click', function () {
			show( current + 1 );
		} );
		zoomButton.addEventListener( 'click', toggleZoom );

		// Échap (cancel) ou fermeture native : nettoyage. Fermer appelle aussi cleanup() directement,
		// l'événement close pouvant arriver tard.
		dialog.addEventListener( 'cancel', function ( event ) {
			event.preventDefault();
			close();
		} );
		dialog.addEventListener( 'close', cleanup );
		// Clic sur le fond (le <dialog> lui-même, hors image et commandes) : fermeture.
		dialog.addEventListener( 'click', function ( event ) {
			if ( event.target === dialog || event.target === stage ) {
				close();
			}
		} );
		dialog.addEventListener( 'keydown', function ( event ) {
			if ( 'ArrowLeft' === event.key ) {
				show( current - 1 );
			} else if ( 'ArrowRight' === event.key ) {
				show( current + 1 );
			}
		} );

		// Glissé au doigt : image suivante ou précédente (sauf en taille réelle, où l'on se déplace).
		var startX = null;
		stage.addEventListener( 'pointerdown', function ( event ) {
			startX = 'touch' === event.pointerType ? event.clientX : null;
		} );
		stage.addEventListener( 'pointerup', function ( event ) {
			if ( null === startX || dialog.classList.contains( 'is-zoomed' ) ) {
				return;
			}
			var distance = event.clientX - startX;
			if ( Math.abs( distance ) > 50 ) {
				show( current + ( distance < 0 ? 1 : -1 ) );
			}
			startX = null;
		} );
	}

	/* Taille réelle possible seulement si l'image est réduite pour tenir dans l'écran. */
	function updateZoom() {
		var image = stage.querySelector( 'img' );
		var canZoom = !! image && image.complete && ( image.naturalWidth > image.clientWidth + 1 || image.naturalHeight > image.clientHeight + 1 );
		zoomButton.hidden = ! canZoom && ! dialog.classList.contains( 'is-zoomed' );
		stage.classList.toggle( 'can-zoom', canZoom );
	}

	function toggleZoom() {
		var zoomed = dialog.classList.toggle( 'is-zoomed' );
		zoomButton.setAttribute( 'aria-label', zoomed ? i18n.zoomOut || 'Fit' : i18n.zoomIn || 'Zoom' );
		zoomButton.setAttribute( 'aria-pressed', zoomed ? 'true' : 'false' );
		if ( ! zoomed ) {
			updateZoom();
		}
	}

	function show( index ) {
		current = ( index + items.length ) % items.length;
		var item = items[ current ];
		dialog.classList.remove( 'is-zoomed' );
		zoomButton.setAttribute( 'aria-pressed', 'false' );
		zoomButton.setAttribute( 'aria-label', i18n.zoomIn || 'Zoom' );
		zoomButton.hidden = true;
		stage.textContent = '';

		if ( item.embed ) {
			var frame = document.createElement( 'iframe' );
			frame.className = 'mrz-lightbox__video';
			frame.src = item.embed;
			frame.title = item.alt || i18n.video || 'Video';
			frame.allow = 'autoplay; fullscreen; picture-in-picture';
			frame.setAttribute( 'allowfullscreen', '' );
			stage.appendChild( frame );
		} else {
			var image = document.createElement( 'img' );
			image.className = 'mrz-lightbox__image';
			image.alt = item.alt;
			image.decoding = 'async';
			image.addEventListener( 'load', updateZoom );
			image.addEventListener( 'click', function () {
				if ( stage.classList.contains( 'can-zoom' ) || dialog.classList.contains( 'is-zoomed' ) ) {
					toggleZoom();
				}
			} );
			image.src = item.href;
			stage.appendChild( image );
		}

		captionBox.textContent = item.caption;
		captionBox.hidden = ! item.caption;
		counter.textContent = items.length > 1 ? format( i18n.counter || '%1$s / %2$s', current + 1, items.length ) : '';
		prevButton.hidden = items.length < 2;
		nextButton.hidden = items.length < 2;
	}

	function open( group, index, link ) {
		if ( ! dialog ) {
			build();
		}
		items = groups[ group ];
		opener = link;
		document.documentElement.classList.add( 'mrz-lightbox-open' );
		show( index );
		dialog.showModal();
		// Focus sur Fermer : présent pour toute image ou vidéo (la loupe peut disparaître).
		dialog.querySelector( '.mrz-lightbox__close' ).focus();
	}

	function close() {
		if ( dialog && dialog.open ) {
			dialog.close();
		}
		cleanup();
	}

	function cleanup() {
		if ( ! opener ) {
			return; // Déjà fait.
		}
		// Arrête une vidéo en cours et rend le focus au lien d'origine.
		stage.textContent = '';
		document.documentElement.classList.remove( 'mrz-lightbox-open' );
		var link = opener;
		opener = null;
		link.focus();
	}

	Object.keys( groups ).forEach( function ( group ) {
		groups[ group ].forEach( function ( item, index ) {
			item.link.addEventListener( 'click', function ( event ) {
				if ( event.metaKey || event.ctrlKey || event.shiftKey ) {
					return; // Ouvrir dans un nouvel onglet reste possible.
				}
				event.preventDefault();
				open( group, index, item.link );
			} );
		} );
	} );

	window.addEventListener( 'resize', function () {
		if ( dialog && dialog.open && ! dialog.classList.contains( 'is-zoomed' ) ) {
			updateZoom();
		}
	} );
}() );
