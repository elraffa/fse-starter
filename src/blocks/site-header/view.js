/**
 * Site Header — frontend interactivity.
 * viewScript: runs only on the frontend, never in the editor.
 *
 * Handles:
 *  1. Sticky scroll shadow
 *  2. Right-side mobile drawer open / close
 *  3. Dimmed overlay (injected into <body>)
 *  4. Mobile sub-menu accordion
 *  5. Keyboard (Escape) and overlay-click close
 *  6. Viewport resize auto-close
 */
( () => {
	const header = document.getElementById( 'site-header' );
	if ( ! header ) return;

	// ── 1. Scroll shadow ──────────────────────────────────────────
	const syncScroll = () =>
		header.classList.toggle( 'is-scrolled', window.scrollY > 4 );
	window.addEventListener( 'scroll', syncScroll, { passive: true } );
	syncScroll();

	// ── 2. Mobile drawer setup ────────────────────────────────────
	const toggle = header.querySelector( '.site-header__toggle' );
	const drawer = document.getElementById( 'site-mobile-menu' );

	if ( ! toggle || ! drawer ) return;

	// ── 3. Overlay — injected as a direct <body> child so it is
	//    positioned relative to the viewport, not the header. ──────
	const overlay = document.createElement( 'div' );
	overlay.className = 'site-header-overlay';
	overlay.setAttribute( 'aria-hidden', 'true' );
	document.body.appendChild( overlay );

	// ── Open / close ──────────────────────────────────────────────
	const openMenu = () => {
		toggle.setAttribute( 'aria-expanded', 'true' );
		toggle.setAttribute( 'aria-label', 'Close navigation' );
		drawer.setAttribute( 'aria-hidden', 'false' );
		header.classList.add( 'menu-open' );
		document.body.classList.add( 'mobile-menu-open' );
		document.body.style.overflow = 'hidden';
	};

	const closeMenu = () => {
		toggle.setAttribute( 'aria-expanded', 'false' );
		toggle.setAttribute( 'aria-label', 'Open navigation' );
		drawer.setAttribute( 'aria-hidden', 'true' );
		header.classList.remove( 'menu-open' );
		document.body.classList.remove( 'mobile-menu-open' );
		document.body.style.overflow = '';
	};

	// Hamburger toggle
	toggle.addEventListener( 'click', () =>
		toggle.getAttribute( 'aria-expanded' ) === 'true'
			? closeMenu()
			: openMenu()
	);

	// Tap the overlay → close
	overlay.addEventListener( 'click', () => {
		closeMenu();
		toggle.focus();
	} );

	// Escape key
	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key === 'Escape' && header.classList.contains( 'menu-open' ) ) {
			closeMenu();
			toggle.focus();
		}
	} );

	// Close links inside drawer when navigating.
	// Skip parent items that toggle sub-menus — their click is handled by the accordion.
	drawer
		.querySelectorAll( 'a:not(.site-header__drawer-title)' )
		.forEach( ( a ) => {
			if ( a.parentElement.classList.contains( 'menu-item-has-children' ) ) return;
			a.addEventListener( 'click', closeMenu );
		} );

	// Auto-close when viewport grows past mobile breakpoint
	window
		.matchMedia( '(min-width: 768px)' )
		.addEventListener( 'change', ( e ) => {
			if ( e.matches ) closeMenu();
		} );

	// ── 4. Mobile sub-menu accordion ─────────────────────────────
	drawer
		.querySelectorAll(
			'.site-header__mobile-menu .menu-item-has-children > a'
		)
		.forEach( ( link ) => {
			link.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				link
					.closest( '.menu-item-has-children' )
					.classList.toggle( 'is-open' );
			} );
		} );
} )();
