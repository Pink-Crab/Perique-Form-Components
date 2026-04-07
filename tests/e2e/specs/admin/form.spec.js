const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=form';

test.describe( 'Form - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'basic form renders with method, action and style class', async ( {
		page,
	} ) => {
		const form = page.locator( '#form-basic_form' );
		await expect( form ).toBeVisible();
		await expect( form ).toHaveAttribute( 'method', 'POST' );
		await expect( form ).toHaveAttribute( 'action', '/submit' );
		await expect( form ).toHaveClass( /pc-form/ );
	} );

	test( 'basic form contains child fields', async ( { page } ) => {
		const form = page.locator( '#form-basic_form' );

		await expect(
			form.locator( 'input[name="form_name"]' )
		).toBeVisible();
		await expect(
			form.locator( 'input[name="form_email"]' )
		).toBeVisible();
		await expect(
			form.locator( 'textarea[name="form_message"]' )
		).toBeVisible();
		await expect(
			form.locator( 'button[name="form_submit"]' )
		).toBeVisible();

		// Labels inside form.
		await expect(
			form.locator( 'label[for="form_name"]' )
		).toHaveText( 'Name' );
		await expect(
			form.locator( 'label[for="form_email"]' )
		).toHaveText( 'Email' );
	} );

	test( 'form with nonce renders hidden nonce field', async ( {
		page,
	} ) => {
		const form = page.locator( '#form-nonce_form' );
		await expect( form ).toBeVisible();

		// Nonce hidden input inside form.
		const nonce = form.locator(
			'input[type="hidden"][name="form_nonce"]'
		);
		await expect( nonce ).toBeAttached();
		const value = await nonce.getAttribute( 'value' );
		expect( value ).toBeTruthy();

		// Hidden field also inside form.
		const hidden = form.locator(
			'input[type="hidden"][name="form_id"]'
		);
		await expect( hidden ).toHaveValue( '42' );
	} );

	test( 'form with GET method', async ( { page } ) => {
		const form = page.locator( '#form-search_form' );
		await expect( form ).toHaveAttribute( 'method', 'GET' );
		await expect( form ).toHaveAttribute( 'action', '/search' );

		await expect(
			form.locator( 'input[name="query"]' )
		).toBeVisible();
	} );

	test( 'form with enctype', async ( { page } ) => {
		const form = page.locator( '#form-upload_form' );
		await expect( form ).toHaveAttribute(
			'enctype',
			'multipart/form-data'
		);
		await expect( form ).toHaveAttribute( 'method', 'POST' );
		await expect( form ).toHaveAttribute( 'action', '/upload' );
	} );

	test( 'form with before/after content', async ( { page } ) => {
		const form = page.locator( '#form-wrapped_form' );
		await expect( form ).toBeVisible();

		await expect(
			form.locator( '.form-header' )
		).toHaveText( 'Form Header' );
		await expect(
			form.locator( '.form-footer' )
		).toHaveText( 'Form Footer' );
	} );

	test( 'form with custom style', async ( { page } ) => {
		const form = page.locator( '#form-custom_form' );
		await expect( form ).toHaveClass( /custom-form/ );
		await expect( form ).not.toHaveClass( /pc-form/ );
	} );

	test( 'complex form with select, raw HTML and multiple fields', async ( {
		page,
	} ) => {
		const form = page.locator( '#form-complex_form' );
		await expect( form ).toBeVisible();

		// Raw HTML intro.
		await expect(
			form.locator( '.form-intro' )
		).toHaveText( 'Fill in the form below' );

		// Select with options.
		const select = form.locator(
			'select[name="form_category"]'
		);
		await expect( select ).toBeVisible();
		await expect( select.locator( 'option' ) ).toHaveCount( 3 );

		// Text input.
		await expect(
			form.locator( 'input[name="form_subject"]' )
		).toBeVisible();

		// Submit button.
		await expect(
			form.locator( 'button[name="complex_submit"]' )
		).toBeVisible();
	} );
} );
