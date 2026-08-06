( function ( wp ) {
	const { subscribe, select } = wp.data;

	const config = window.chwPageLayout || {};
	const BUILDER_TEMPLATE = config.builderTemplate || 'src/templates/template-page-builder.php';

	function getWrapper() {
		return document.querySelector( '.editor-styles-wrapper' );
	}

	function applyLayoutClass() {
		const wrapper = getWrapper();

		if ( ! wrapper ) {
			return;
		}

		const editor = select( 'core/editor' );

		if ( ! editor ) {
			return;
		}

		const postType = editor.getCurrentPostType();
		const template = editor.getEditedPostAttribute( 'template' ) || '';
		const isBuilder = postType === 'page' && template === BUILDER_TEMPLATE;

		wrapper.classList.toggle( 'chw-layout-builder', isBuilder );
		wrapper.classList.toggle( 'chw-layout-standard', ! isBuilder );
	}

	wp.domReady( function () {
		applyLayoutClass();
		subscribe( applyLayoutClass );
	} );
} )( window.wp );
