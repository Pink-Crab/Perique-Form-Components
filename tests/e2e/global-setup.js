const { test: setup } = require( '@wordpress/e2e-test-utils-playwright' );

setup( 'authenticate', async ( { requestUtils } ) => {
	await requestUtils.setupRest();
} );
