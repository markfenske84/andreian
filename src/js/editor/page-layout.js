( function ( wp ) {
	const { subscribe, select } = wp.data;

	function getEditorDocument() {
		const iframe = document.querySelector( 'iframe[name="editor-canvas"]' );
		if ( iframe && iframe.contentDocument ) {
			return iframe.contentDocument;
		}
		return null;
	}

	function getIframeWrapper() {
		const doc = getEditorDocument();
		return doc ? doc.querySelector( '.editor-styles-wrapper' ) : null;
	}

	function applyLayoutClass() {
		const wrapper = getIframeWrapper();
		const editor = select( 'core/editor' );

		if ( ! wrapper || ! editor ) {
			return;
		}

		wrapper.classList.remove( 'andreian-layout-builder' );
		wrapper.classList.add( 'andreian-layout-standard' );
	}

	wp.domReady( function () {
		applyLayoutClass();
		subscribe( applyLayoutClass );

		// Recover when the canvas iframe mounts or reloads (e.g. after inserting blocks).
		window.setInterval( applyLayoutClass, 750 );
	} );
} )( window.wp );
