const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=special-inputs';

test.describe( 'Special Inputs - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	// ===== COLOR =====

	test( 'color basic renders with value and style classes', async ( {
		page,
	} ) => {
		const input = page.locator( 'input[name="color_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'color' );
		await expect( input ).toHaveValue( '#ff5733' );
		await expect( input ).toHaveClass(
			/pc-form__element__field--color_input/
		);
		await expect(
			page.locator( 'label[for="color_basic"]' )
		).toHaveText( 'Color' );
	} );

	test( 'color full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="color_full"]' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'off' );
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveValue( '#00ff00' );
		await expect( input ).toHaveAttribute( 'list' );

		const listId = await input.getAttribute( 'list' );
		const datalist = page.locator( `datalist#${ listId }` );
		await expect( datalist ).toBeAttached();
		await expect( datalist.locator( 'option' ) ).toHaveCount( 3 );
	} );

	test( 'color notification, before/after and wrapper data', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_color_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-info/ );
		await expect( wrapper ).toHaveAttribute(
			'data-type',
			'color-picker'
		);
		await expect(
			wrapper.locator( '.color-swatch' )
		).toHaveText( 'Swatch' );
		await expect(
			wrapper.locator( '.color-hex' )
		).toHaveText( '#hex' );
	} );

	// ===== CHECKBOX =====

	test( 'checkbox unchecked', async ( { page } ) => {
		const input = page.locator(
			'input[name="checkbox_unchecked"]'
		);
		await expect( input ).toHaveAttribute( 'type', 'checkbox' );
		await expect( input ).not.toBeChecked();
		await expect( input ).toHaveClass(
			/pc-form__element__field--checkbox_input/
		);
		await expect(
			page.locator( 'label[for="checkbox_unchecked"]' )
		).toHaveText( 'Unchecked' );
	} );

	test( 'checkbox checked', async ( { page } ) => {
		const input = page.locator(
			'input[name="checkbox_checked"]'
		);
		await expect( input ).toBeChecked();
	} );

	test( 'checkbox disabled and checked', async ( { page } ) => {
		const input = page.locator(
			'input[name="checkbox_disabled"]'
		);
		await expect( input ).toBeChecked();
		await expect( input ).toBeDisabled();
	} );

	test( 'checkbox with value, notification and before content', async ( {
		page,
	} ) => {
		const input = page.locator(
			'input[name="checkbox_value"]'
		);
		await expect( input ).toBeChecked();
		await expect( input ).toHaveValue( 'agree' );

		const wrapper = page.locator(
			'#form-field_checkbox_value'
		);
		await expect( wrapper ).toHaveClass( /notification-warning/ );
		await expect(
			wrapper.locator( '.cb-before' )
		).toHaveText( 'Terms' );
	} );

	// ===== RADIO =====

	test( 'radio unchecked', async ( { page } ) => {
		const input = page.locator(
			'input[name="radio_unchecked"]'
		);
		await expect( input ).toHaveAttribute( 'type', 'radio' );
		await expect( input ).not.toBeChecked();
		await expect( input ).toHaveClass(
			/pc-form__element__field--radio_input/
		);
	} );

	test( 'radio checked', async ( { page } ) => {
		const input = page.locator( 'input[name="radio_checked"]' );
		await expect( input ).toBeChecked();
	} );

	test( 'radio disabled and checked', async ( { page } ) => {
		const input = page.locator(
			'input[name="radio_disabled"]'
		);
		await expect( input ).toBeChecked();
		await expect( input ).toBeDisabled();
	} );

	test( 'radio with value and notification', async ( { page } ) => {
		const input = page.locator(
			'input[name="radio_value"]'
		);
		await expect( input ).toBeChecked();
		await expect( input ).toHaveValue( 'option_a' );

		const wrapper = page.locator(
			'#form-field_radio_value'
		);
		await expect( wrapper ).toHaveClass( /notification-error/ );
	} );
} );
