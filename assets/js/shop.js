/**
 * MRZ Theme Experience : boutique WooCommerce (vanilla).
 * - Panneau du panier : <dialog> ouvert par l'icône du panier, contenu chargé à
 *   l'ouverture par les fragments de WooCommerce (jamais dans le cache de page).
 * - Fiche produit : ajout au panier sans rechargement. Le formulaire est envoyé tel
 *   quel à WooCommerce (variations et champs des plugins compris), qui renvoie les fragments.
 * - Notification légère pour les messages de WooCommerce (ajout, rupture de stock).
 * Chargé seulement sur les pages qui s'en servent (inc/woocommerce.php).
 */
( function () {
	'use strict';

	var settings = window.mrzShop || {};
	var i18n = settings.i18n || {};
	var drawer = document.getElementById( 'mrz-minicart' );
	var toast = document.querySelector( '.mrz-toast' );
	var toastTimer = null;
	var opener = null;

	function endpoint( name ) {
		return ( settings.ajaxUrl || '' ).replace( '%%endpoint%%', name );
	}

	function post( name, body ) {
		return fetch( endpoint( name ), {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		} ).then( function ( response ) {
			if ( ! response.ok ) {
				throw new Error( response.status );
			}
			return response.json();
		} );
	}

	/* Fragments : chaque sélecteur est remplacé par son HTML (pastille, mini-panier). */
	function applyFragments( fragments ) {
		Object.keys( fragments || {} ).forEach( function ( selector ) {
			if ( 'div.mrz-shop-notices' === selector ) {
				return;
			}
			document.querySelectorAll( selector ).forEach( function ( element ) {
				element.outerHTML = fragments[ selector ];
			} );
		} );
		// Pour les plugins qui écoutent WooCommerce.
		if ( window.jQuery ) {
			window.jQuery( document.body ).trigger( 'wc_fragments_refreshed' );
		}
	}

	/* Messages de WooCommerce renvoyés avec les fragments : [ { type, html } ]. */
	function readNotices( fragments ) {
		var holder = document.createElement( 'div' );
		holder.innerHTML = ( fragments && fragments[ 'div.mrz-shop-notices' ] ) || '';
		return Array.prototype.map.call( holder.querySelectorAll( 'p[data-type]' ), function ( notice ) {
			return { type: notice.getAttribute( 'data-type' ), html: notice.innerHTML };
		} );
	}

	/* Notification : role="status" (annoncée sans prendre le focus), fermée après 6 s, en pause au survol et au focus. */
	function hideToast() {
		if ( toast ) {
			toast.classList.remove( 'is-visible' );
			toast.innerHTML = '';
		}
	}

	function startToastTimer() {
		clearTimeout( toastTimer );
		toastTimer = setTimeout( hideToast, 6000 );
	}

	function showToast( notices ) {
		if ( ! toast || ! notices.length ) {
			return;
		}
		toast.innerHTML = '';
		notices.forEach( function ( notice ) {
			var message = document.createElement( 'p' );
			message.className = 'mrz-toast__message mrz-toast__message--' + notice.type;
			message.innerHTML = notice.html;
			toast.appendChild( message );
		} );
		var close = document.createElement( 'button' );
		close.type = 'button';
		close.className = 'mrz-toast__close';
		close.innerHTML = '<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="20" height="20"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg><span class="screen-reader-text">' + ( i18n.close || 'Close' ) + '</span>';
		close.addEventListener( 'click', hideToast );
		toast.appendChild( close );
		toast.classList.add( 'is-visible' );
		startToastTimer();
	}

	if ( toast ) {
		toast.addEventListener( 'mouseenter', function () {
			clearTimeout( toastTimer );
		} );
		toast.addEventListener( 'mouseleave', startToastTimer );
		toast.addEventListener( 'focusin', function () {
			clearTimeout( toastTimer );
		} );
		toast.addEventListener( 'focusout', startToastTimer );
	}

	/* Panneau du panier. */
	function openDrawer( fragments ) {
		if ( ! drawer ) {
			return;
		}
		if ( ! drawer.open ) {
			opener = document.activeElement;
			drawer.showModal();
		}
		if ( fragments ) {
			applyFragments( fragments );
			return;
		}
		drawer.classList.add( 'is-loading' );
		post( 'get_refreshed_fragments', new FormData() )
			.then( function ( data ) {
				applyFragments( data.fragments );
			} )
			.catch( function () {
				// Panier inaccessible : la page du panier prend le relais.
				window.location.href = document.querySelector( '.mrz-cart' ).href;
			} )
			.finally( function () {
				drawer.classList.remove( 'is-loading' );
			} );
	}

	if ( drawer && settings.drawer ) {
		document.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( '.mrz-cart[aria-controls="mrz-minicart"]' );
			if ( link && ! event.metaKey && ! event.ctrlKey && ! event.shiftKey ) {
				event.preventDefault();
				openDrawer();
			}
		} );

		drawer.addEventListener( 'click', function ( event ) {
			// Clic sur le voile (hors du panneau) ou sur la croix.
			if ( event.target === drawer || event.target.closest( '[data-mrz-minicart-close]' ) ) {
				drawer.close();
				return;
			}

			// Retirer un article sans quitter la page.
			var remove = event.target.closest( '.remove_from_cart_button' );
			if ( remove ) {
				event.preventDefault();
				var body = new FormData();
				body.append( 'cart_item_key', remove.getAttribute( 'data-cart_item_key' ) );
				remove.closest( 'li' ).classList.add( 'is-removing' );
				post( 'remove_from_cart', body )
					.then( function ( data ) {
						applyFragments( data.fragments );
					} )
					.catch( function () {
						window.location.href = remove.href;
					} );
			}
		} );

		drawer.addEventListener( 'close', function () {
			if ( opener && opener.focus ) {
				opener.focus();
			}
		} );

		// Listes de produits : ajout en AJAX de WooCommerce (jQuery, déjà chargé dans ce cas).
		if ( window.jQuery ) {
			window.jQuery( document.body ).on( 'added_to_cart', function ( event, fragments ) {
				openDrawer( fragments );
			} );
		}
	}

	/* Fiche produit : ajout au panier sans rechargement. */
	if ( settings.ajaxSingle ) {
		document.addEventListener( 'submit', function ( event ) {
			var form = event.target.closest( '.product form.cart' );
			if ( ! form || 'get' === ( form.getAttribute( 'method' ) || '' ).toLowerCase() ) {
				return;
			}
			var button = event.submitter || form.querySelector( '[type="submit"]' );
			if ( button && ( button.classList.contains( 'disabled' ) || button.disabled ) ) {
				return; // Variation non choisie : WooCommerce affiche déjà son message.
			}

			event.preventDefault();
			var body = new FormData( form );
			// Le bouton porte l'ID du produit simple (name="add-to-cart") : FormData ne l'inclut pas.
			if ( button && button.name && ! body.has( button.name ) ) {
				body.append( button.name, button.value );
			}
			if ( ! body.has( 'add-to-cart' ) ) {
				form.submit();
				return;
			}

			if ( button ) {
				button.classList.add( 'loading' );
				button.setAttribute( 'aria-busy', 'true' );
			}

			post( 'get_refreshed_fragments', body )
				.then( function ( data ) {
					var notices = readNotices( data.fragments );
					var errors = notices.filter( function ( notice ) {
						return 'error' === notice.type;
					} );
					if ( ! errors.length && drawer && settings.drawer ) {
						openDrawer( data.fragments );
						return;
					}
					applyFragments( data.fragments );
					showToast( errors.length ? errors : notices );
				} )
				.catch( function () {
					showToast( [ { type: 'error', html: i18n.error || '' } ] );
				} )
				.finally( function () {
					if ( button ) {
						button.classList.remove( 'loading' );
						button.removeAttribute( 'aria-busy' );
					}
				} );
		} );
	}

	/*
	 * Boutons + et − autour du champ quantité (fiche produit, page Panier).
	 * Le champ reste : saisie au clavier possible, min, max et pas respectés par stepUp/stepDown.
	 */
	function addQuantityButtons() {
		document.querySelectorAll( '.quantity:not(.mrz-qty) input.qty[type="number"]' ).forEach( function ( input ) {
			var wrapper = input.closest( '.quantity' );
			wrapper.classList.add( 'mrz-qty' );

			[ [ 'decrease', '−', 'beforebegin' ], [ 'increase', '+', 'afterend' ] ].forEach( function ( item ) {
				var button = document.createElement( 'button' );
				button.type = 'button';
				button.className = 'mrz-qty__button mrz-qty__button--' + item[ 0 ];
				button.innerHTML = '<span aria-hidden="true">' + item[ 1 ] + '</span><span class="screen-reader-text">' + ( i18n[ item[ 0 ] ] || item[ 0 ] ) + '</span>';
				button.addEventListener( 'click', function () {
					var before = input.value;
					if ( 'increase' === item[ 0 ] ) {
						input.stepUp();
					} else {
						input.stepDown();
					}
					if ( input.value !== before ) {
						// « Mettre à jour le panier » s'active sur ces événements (cart.js de WooCommerce).
						input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
						input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
					}
				} );
				input.insertAdjacentElement( item[ 2 ], button );
			} );
		} );
	}

	if ( settings.quantityButtons ) {
		addQuantityButtons();
		// Panier mis à jour par WooCommerce : son HTML est remplacé.
		if ( window.jQuery ) {
			window.jQuery( document.body ).on( 'updated_wc_div updated_cart_totals', addQuantityButtons );
		}
	}

	/* Lien vers les avis (« 2 avis client ») : ouvre l'accordéon des avis. */
	function openDetailsFor( hash ) {
		var target = hash && document.getElementById( hash.slice( 1 ) );
		var details = target && target.closest( 'details' );
		if ( details ) {
			details.open = true;
		}
	}
	document.addEventListener( 'click', function ( event ) {
		var link = event.target.closest( 'a[href^="#"]' );
		if ( link ) {
			openDetailsFor( link.hash );
		}
	} );
	openDetailsFor( window.location.hash );
}() );
