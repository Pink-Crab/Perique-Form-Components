const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=typed-inputs';

test.describe( 'Typed Inputs - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	// ===== EMAIL =====

	test( 'email basic renders with label, value, placeholder and style classes', async ( {
		page,
	} ) => {
		const input = page.locator( 'input[name="email_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'email' );
		await expect( input ).toHaveValue( 'test@test.com' );
		await expect( input ).toHaveAttribute(
			'placeholder',
			'user@example.com'
		);
		await expect( input ).toHaveClass(
			/pc-form__element__field--email_input/
		);

		await expect(
			page.locator( 'label[for="email_basic"]' )
		).toHaveText( 'Email' );

		const wrapper = page.locator( '#form-field_email_basic' );
		await expect( wrapper ).toHaveClass(
			/pc-form__element--email_input/
		);
	} );

	test( 'email full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="email_full"]' );
		await expect( input ).toHaveAttribute(
			'pattern',
			'[a-z]+@[a-z]+\\.[a-z]+'
		);
		await expect( input ).toHaveAttribute( 'autocomplete', 'email' );
		await expect( input ).toHaveAttribute( 'size', '40' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toHaveAttribute( 'minlength', '5' );
		await expect( input ).toHaveAttribute( 'maxlength', '100' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'list' );

		const listId = await input.getAttribute( 'list' );
		const datalist = page.locator( `datalist#${ listId }` );
		await expect( datalist ).toBeAttached();
		await expect( datalist.locator( 'option' ) ).toHaveCount( 2 );
	} );

	test( 'email disabled', async ( { page } ) => {
		const input = page.locator( 'input[name="email_disabled"]' );
		await expect( input ).toBeDisabled();
	} );

	test( 'email custom style', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_email_custom_style'
		);
		await expect( wrapper ).toHaveClass( /custom-wrapper/ );
		await expect( wrapper ).toHaveClass(
			/custom-wrapper--email_input/
		);

		const input = page.locator(
			'input[name="email_custom_style"]'
		);
		await expect( input ).toHaveClass( /custom-field/ );
		await expect( input ).toHaveClass(
			/custom-field--email_input/
		);
	} );

	test( 'email notification adds error class', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_email_notification'
		);
		await expect( wrapper ).toHaveClass( /notification-error/ );

		const input = page.locator(
			'input[name="email_notification"]'
		);
		await expect( input ).toHaveClass( /notification-error/ );
	} );

	test( 'email before/after content', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_email_wrapped'
		);
		await expect(
			wrapper.locator( '.email-before' )
		).toHaveText( '@' );
		await expect(
			wrapper.locator( '.email-after' )
		).toHaveText( '.com' );
	} );

	test( 'email custom id and wrapper attrs', async ( { page } ) => {
		const input = page.locator(
			'input[name="email_ids"]'
		);
		await expect( input ).toHaveAttribute( 'id', 'custom-email-id' );

		const wrapper = page.locator( '#custom-email-wrapper' );
		await expect( wrapper ).toBeVisible();
		await expect( wrapper ).toHaveAttribute(
			'data-validate',
			'email'
		);
	} );

	test( 'email no wrapper', async ( { page } ) => {
		const input = page.locator(
			'input[name="email_no_wrapper"]'
		);
		await expect( input ).toBeAttached();

		const wrapper = page.locator(
			'#form-field_email_no_wrapper'
		);
		await expect( wrapper ).toHaveCount( 0 );
	} );

	// ===== PASSWORD =====

	test( 'password basic renders', async ( { page } ) => {
		const input = page.locator( 'input[name="password_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'password' );
		await expect( input ).toHaveAttribute(
			'placeholder',
			'Enter password'
		);
		await expect( input ).toHaveClass(
			/pc-form__element__field--password_input/
		);
		await expect(
			page.locator( 'label[for="password_basic"]' )
		).toHaveText( 'Password' );
	} );

	test( 'password full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="password_full"]' );
		await expect( input ).toHaveAttribute(
			'autocomplete',
			'new-password'
		);
		await expect( input ).toHaveAttribute( 'minlength', '8' );
		await expect( input ).toHaveAttribute( 'maxlength', '128' );
		await expect( input ).toHaveAttribute( 'pattern' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toHaveAttribute( 'size', '30' );
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveAttribute( 'required', '' );
	} );

	test( 'password notification and before/after', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_password_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-warning/ );
		await expect(
			wrapper.locator( '.pw-before' )
		).toHaveText( 'Lock' );
		await expect(
			wrapper.locator( '.pw-after' )
		).toHaveText( 'Strength' );

		const input = page.locator(
			'input[name="password_extras"]'
		);
		await expect( input ).toHaveAttribute( 'id', 'custom-pw-id' );
		await expect( wrapper ).toHaveAttribute(
			'data-strength',
			'weak'
		);
	} );

	test( 'password no wrapper', async ( { page } ) => {
		const input = page.locator(
			'input[name="password_no_wrapper"]'
		);
		await expect( input ).toBeAttached();
		const wrapper = page.locator(
			'#form-field_password_no_wrapper'
		);
		await expect( wrapper ).toHaveCount( 0 );
	} );

	// ===== SEARCH =====

	test( 'search basic renders', async ( { page } ) => {
		const input = page.locator( 'input[name="search_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'search' );
		await expect( input ).toHaveAttribute(
			'placeholder',
			'Search...'
		);
		await expect( input ).toHaveClass(
			/pc-form__element__field--search_input/
		);
	} );

	test( 'search full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="search_full"]' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'off' );
		await expect( input ).toHaveAttribute(
			'pattern',
			'[a-zA-Z0-9]+'
		);
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'minlength', '2' );
		await expect( input ).toHaveAttribute( 'maxlength', '200' );
		await expect( input ).toHaveAttribute( 'inputmode', 'search' );
		await expect( input ).toHaveAttribute( 'spellcheck', 'false' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'search notification and before content', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_search_notification'
		);
		await expect( wrapper ).toHaveClass( /notification-info/ );
		await expect(
			wrapper.locator( '.search-icon' )
		).toHaveText( 'Q' );
		await expect( wrapper ).toHaveAttribute(
			'data-type',
			'search'
		);
	} );

	// ===== TEL =====

	test( 'tel basic renders', async ( { page } ) => {
		const input = page.locator( 'input[name="tel_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'tel' );
		await expect( input ).toHaveAttribute(
			'placeholder',
			'+44 7700 900000'
		);
		await expect( input ).toHaveClass(
			/pc-form__element__field--tel_input/
		);
	} );

	test( 'tel full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="tel_full"]' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'tel' );
		await expect( input ).toHaveAttribute( 'pattern' );
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'minlength', '5' );
		await expect( input ).toHaveAttribute( 'maxlength', '20' );
		await expect( input ).toHaveAttribute( 'inputmode', 'tel' );
		await expect( input ).toHaveAttribute( 'spellcheck', 'false' );
		await expect( input ).toHaveAttribute( 'size', '20' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'tel notification and before content', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_tel_notification'
		);
		await expect( wrapper ).toHaveClass( /notification-success/ );
		await expect(
			wrapper.locator( '.tel-prefix' )
		).toHaveText( '+44' );
	} );

	// ===== URL =====

	test( 'url basic renders', async ( { page } ) => {
		const input = page.locator( 'input[name="url_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'url' );
		await expect( input ).toHaveAttribute(
			'placeholder',
			'https://example.com'
		);
		await expect( input ).toHaveClass(
			/pc-form__element__field--url_input/
		);
	} );

	test( 'url full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="url_full"]' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'url' );
		await expect( input ).toHaveAttribute(
			'pattern',
			'https?://.+'
		);
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'minlength', '10' );
		await expect( input ).toHaveAttribute( 'maxlength', '2048' );
		await expect( input ).toHaveAttribute( 'inputmode', 'url' );
		await expect( input ).toHaveAttribute( 'spellcheck', 'false' );
		await expect( input ).toHaveAttribute( 'size', '50' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'url notification and after content', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_url_notification'
		);
		await expect( wrapper ).toHaveClass( /notification-error/ );
		await expect(
			wrapper.locator( '.url-hint' )
		).toHaveText( 'Must start with https://' );
	} );

	// ===== HIDDEN =====

	test( 'hidden input renders with value and no wrapper', async ( {
		page,
	} ) => {
		const input = page.locator( 'input[name="hidden_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'hidden' );
		await expect( input ).toHaveValue( 'hidden_value_123' );
	} );
} );
