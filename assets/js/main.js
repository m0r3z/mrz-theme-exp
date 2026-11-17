/**
 * MRZ Theme Experience : JavaScript du front (vanilla, sans dépendance).
 * Header au défilement, menu mobile, sous-menus.
 */
( function () {
	'use strict';

	var body = document.body;

	/* Header : classe is-scrolled dès que la page défile. */
	var header = document.querySelector( '.mrz-header' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
		};
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );

		/* Hauteur du header en variable CSS : les ancres s'arrêtent sous un header sticky. */
		var setHeaderHeight = function () {
			document.documentElement.style.setProperty( '--mrz-header-height', header.offsetHeight + 'px' );
		};
		// Le ResizeObserver mesure dès l'affichage, sans forcer de recalcul au chargement.
		if ( 'ResizeObserver' in window ) {
			new ResizeObserver( setHeaderHeight ).observe( header );
		} else {
			window.requestAnimationFrame( setHeaderHeight );
		}
	}

	/* Menu mobile (panneau, plein écran ou sous le header). */
	var panel = document.getElementById( 'mrz-mobile-menu' );
	if ( ! panel ) {
		return;
	}

	var toggles = document.querySelectorAll( '[data-mrz-menu-toggle]' );
	var lastFocus = null;
	var closeTimer = null;
	var isDropdown = 'dropdown' === panel.getAttribute( 'data-mrz-style' );

	function setExpanded( value ) {
		toggles.forEach( function ( toggle ) {
			toggle.setAttribute( 'aria-expanded', value ? 'true' : 'false' );
		} );
	}

	function openMenu() {
		clearTimeout( closeTimer );
		lastFocus = document.activeElement;
		/* Sous le header : le menu commence au bas du header, là où il est à l'écran. */
		if ( isDropdown && header ) {
			panel.style.setProperty( '--mrz-menu-top', Math.max( 0, header.getBoundingClientRect().bottom ) + 'px' );
		}
		panel.hidden = false;
		requestAnimationFrame( function () {
			panel.classList.add( 'is-open' );
		} );
		body.classList.add( 'mrz-menu-open' );
		/*
		 * Plein écran : le bouton fermer se place exactement sur le burger, mesuré après le
		 * blocage du défilement et positionné par la gauche (même repère que la mesure).
		 */
		var burger = document.querySelector( '.mrz-burger' );
		if ( 'fullscreen' === panel.getAttribute( 'data-mrz-style' ) && burger ) {
			var box = burger.getBoundingClientRect();
			panel.style.setProperty( '--mrz-close-top', box.top + 'px' );
			panel.style.setProperty( '--mrz-close-left', box.left + 'px' );
			panel.style.setProperty( '--mrz-close-right', 'auto' );
		}
		setExpanded( true );
		var firstFocus = isDropdown ? panel.querySelector( 'nav a' ) : panel.querySelector( '.mrz-offcanvas__close' );
		if ( firstFocus ) {
			firstFocus.focus();
		}
	}

	function closeMenu() {
		if ( panel.hidden ) {
			return;
		}
		panel.classList.remove( 'is-open' );
		body.classList.remove( 'mrz-menu-open' );
		setExpanded( false );
		closeTimer = setTimeout( function () {
			panel.hidden = true;
		}, 300 );
		if ( lastFocus ) {
			lastFocus.focus();
		}
	}

	toggles.forEach( function ( toggle ) {
		toggle.addEventListener( 'click', function () {
			if ( panel.classList.contains( 'is-open' ) ) {
				closeMenu();
			} else {
				openMenu();
			}
		} );
	} );

	panel.querySelectorAll( '[data-mrz-close]' ).forEach( function ( element ) {
		element.addEventListener( 'click', closeMenu );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key ) {
			closeMenu();
		}
	} );

	/* Un lien vers une ancre de la page courante ferme le menu. */
	panel.querySelectorAll( 'a' ).forEach( function ( link ) {
		link.addEventListener( 'click', function () {
			if ( link.hash && link.pathname === window.location.pathname ) {
				closeMenu();
			}
		} );
	} );

	/* Sous-menus : un bouton ouvre ou ferme chaque niveau. */
	var label = ( window.mrzTheme && window.mrzTheme.i18n && window.mrzTheme.i18n.submenu ) || 'Open submenu';
	panel.querySelectorAll( '.menu-item-has-children > a' ).forEach( function ( link, index ) {
		var submenu = link.parentElement.querySelector( ':scope > .sub-menu' );
		if ( ! submenu ) {
			return;
		}

		submenu.id = 'mrz-submenu-' + index;
		submenu.hidden = true;

		var button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'mrz-submenu-toggle';
		button.setAttribute( 'aria-expanded', 'false' );
		button.setAttribute( 'aria-controls', submenu.id );
		button.setAttribute( 'aria-label', label );
		button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
		link.after( button );

		button.addEventListener( 'click', function () {
			var isOpen = 'true' === button.getAttribute( 'aria-expanded' );
			button.setAttribute( 'aria-expanded', isOpen ? 'false' : 'true' );
			submenu.hidden = isOpen;
		} );
	} );
}() );

/*
 * Sous-menus desktop. Le CSS les ouvre au survol et au clavier ; ce script ajoute :
 * l'ouverture au toucher (premier appui ouvre, second suit le lien), Échap pour
 * fermer, et l'ouverture vers la gauche quand le sous-menu sortirait de l'écran.
 */
( function () {
	var parents = document.querySelectorAll( '.mrz-nav .menu-item-has-children' );
	if ( ! parents.length ) {
		return;
	}

	var lastPointer = 'mouse';
	document.addEventListener( 'pointerdown', function ( event ) {
		lastPointer = event.pointerType;
	}, { passive: true } );

	function closeAll( except ) {
		document.querySelectorAll( '.mrz-nav .menu-item-has-children.is-open' ).forEach( function ( item ) {
			if ( ! except || ! item.contains( except ) ) {
				item.classList.remove( 'is-open' );
			}
		} );
	}

	function placeSubmenu( item ) {
		var submenu = item.querySelector( ':scope > .sub-menu' );
		if ( ! submenu ) {
			return;
		}
		/* Premier niveau : écart entre le lien et le bas du header. */
		var headerBox = item.closest( '.mrz-header' );
		if ( headerBox && ! item.parentElement.closest( '.sub-menu' ) ) {
			var gap = headerBox.getBoundingClientRect().bottom - item.getBoundingClientRect().bottom;
			item.style.setProperty( '--mrz-submenu-offset', Math.max( 0, gap ) + 'px' );
		}
		item.classList.remove( 'mrz-submenu--flip' );
		if ( submenu.getBoundingClientRect().right > document.documentElement.clientWidth ) {
			item.classList.add( 'mrz-submenu--flip' );
		}
	}

	parents.forEach( function ( item ) {
		var link = item.querySelector( ':scope > a' );

		item.addEventListener( 'mouseenter', function () {
			placeSubmenu( item );
		} );
		item.addEventListener( 'focusin', function () {
			placeSubmenu( item );
		} );
		item.addEventListener( 'mouseleave', function () {
			item.classList.remove( 'is-closed' );
		} );
		item.addEventListener( 'focusout', function ( event ) {
			if ( ! item.contains( event.relatedTarget ) ) {
				item.classList.remove( 'is-closed', 'is-open' );
			}
		} );

		if ( link ) {
			link.addEventListener( 'click', function ( event ) {
				if ( 'touch' === lastPointer && ! item.classList.contains( 'is-open' ) ) {
					event.preventDefault();
					closeAll( item );
					placeSubmenu( item );
					item.classList.add( 'is-open' );
				}
			} );
		}
	} );

	/* Un appui en dehors du menu referme les sous-menus ouverts au toucher. */
	document.addEventListener( 'click', function ( event ) {
		if ( ! event.target.closest( '.mrz-nav' ) ) {
			closeAll();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}
		var item = document.activeElement && document.activeElement.closest( '.mrz-nav .menu-item-has-children' );
		if ( ! item ) {
			return;
		}
		/* Ferme le sous-menu le plus proche et rend le focus à son lien. */
		if ( item.querySelector( ':scope > .sub-menu' ).contains( document.activeElement ) ) {
			item.querySelector( ':scope > a' ).focus();
		}
		item.classList.remove( 'is-open' );
		item.classList.add( 'is-closed' );
	} );
}() );

/* Recherche du header : focus sur le champ à l'ouverture, fermeture par Échap ou clic ailleurs. */
( function () {
	var search = document.querySelector( '.mrz-header-search' );
	if ( ! search ) {
		return;
	}

	search.addEventListener( 'toggle', function () {
		var input = search.querySelector( 'input[type="search"]' );
		if ( search.open && input ) {
			input.focus();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && search.open ) {
			search.open = false;
			search.querySelector( 'summary' ).focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( search.open && ! search.contains( event.target ) ) {
			search.open = false;
		}
	} );
}() );
