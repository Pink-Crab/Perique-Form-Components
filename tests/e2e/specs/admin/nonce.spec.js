const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=nonce';

test.describe( 'Nonce - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'nonce renders hidden input with correct name', async ( {
		page,
	} ) => {
		const nonce = page.locator(
			'input[type="hidden"][name="test_form_nonce"]'
		);
		await expect( nonce ).toBeAttached();
		// wp_nonce_field generates a value - just check it's not empty.
		const value = await nonce.getAttribute( 'value' );
		expect( value ).toBeTruthy();
		expect( value.length ).toBeGreaterThan( 0 );
	} );

	test( 'nonce renders _wp_http_referer hidden input', async ( {
		page,
	} ) => {
		// wp_nonce_field outputs a _wp_http_referer per call.
		const referers = page.locator(
			'input[type="hidden"][name="_wp_http_referer"]'
		);
		await expect( referers ).toHaveCount( 2 );
	} );

	test( 'second nonce has different name', async ( { page } ) => {
		const nonce = page.locator(
			'input[type="hidden"][name="_settings_nonce"]'
		);
		await expect( nonce ).toBeAttached();
		const value = await nonce.getAttribute( 'value' );
		expect( value ).toBeTruthy();
	} );

	test( 'nonce values are valid tokens', async ( { page } ) => {
		const nonce1 = await page
			.locator( 'input[name="test_form_nonce"]' )
			.getAttribute( 'value' );
		const nonce2 = await page
			.locator( 'input[name="_settings_nonce"]' )
			.getAttribute( 'value' );

		// WordPress nonces are hex strings, typically 10 chars.
		expect( nonce1 ).toMatch( /^[a-f0-9]+$/ );
		expect( nonce2 ).toMatch( /^[a-f0-9]+$/ );

		// Different actions should produce different nonces.
		expect( nonce1 ).not.toEqual( nonce2 );
	} );
} );
