( function ( wp ) {
	const { subscribe, select } = wp.data;

	const config = window.chwPageLayout || {};
	const BUILDER_TEMPLATE = config.builderTemplate || 'src/templates/template-page-builder.php';

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

		const postType = editor.getCurrentPostType();
		const template = editor.getEditedPostAttribute( 'template' ) || '';
		const isBuilderTemplate = postType === 'page' && template === BUILDER_TEMPLATE;
		// Pages always use full-width editor canvas so custom blocks are not
		// constrained to the blog column measure. Standard width applies to posts only.
		const useBuilderLayout = postType === 'page' || isBuilderTemplate;

		wrapper.classList.toggle( 'chw-layout-builder', useBuilderLayout );
		wrapper.classList.toggle( 'chw-layout-standard', ! useBuilderLayout );
	}

	wp.domReady( function () {
		applyLayoutClass();
		subscribe( applyLayoutClass );

		// Recover when the canvas iframe mounts or reloads (e.g. after inserting blocks).
		window.setInterval( applyLayoutClass, 750 );
	} );
} )( window.wp );
