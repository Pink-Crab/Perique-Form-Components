const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=numeric-inputs';

test.describe( 'Numeric Inputs - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	// ===== NUMBER =====

	test( 'number basic renders with value and style classes', async ( {
		page,
	} ) => {
		const input = page.locator( 'input[name="number_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'number' );
		await expect( input ).toHaveValue( '42' );
		await expect( input ).toHaveClass(
			/pc-form__element__field--number_input/
		);
		await expect(
			page.locator( 'label[for="number_basic"]' )
		).toHaveText( 'Number' );

		const wrapper = page.locator( '#form-field_number_basic' );
		await expect( wrapper ).toHaveClass(
			/pc-form__element--number_input/
		);
	} );

	test( 'number full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="number_full"]' );
		await expect( input ).toHaveAttribute( 'min', '0' );
		await expect( input ).toHaveAttribute( 'max', '1000' );
		await expect( input ).toHaveAttribute( 'step', '5' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'off' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toHaveAttribute(
			'placeholder',
			'0-1000'
		);
		await expect( input ).toHaveValue( '250' );
		await expect( input ).toHaveAttribute( 'list' );

		const listId = await input.getAttribute( 'list' );
		const datalist = page.locator( `datalist#${ listId }` );
		await expect( datalist ).toBeAttached();
		await expect( datalist.locator( 'option' ) ).toHaveCount( 4 );
	} );

	test( 'number custom style', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_number_custom_style'
		);
		await expect( wrapper ).toHaveClass( /custom-wrapper/ );
		await expect( wrapper ).toHaveClass(
			/custom-wrapper--number_input/
		);

		const input = page.locator(
			'input[name="number_custom_style"]'
		);
		await expect( input ).toHaveClass( /custom-field/ );
		await expect( input ).toHaveClass(
			/custom-field--number_input/
		);
	} );

	test( 'number notification, before/after, wrapper attrs and custom id', async ( {
		page,
	} ) => {
		const wrapper = page.locator( '#custom-number-wrapper' );
		await expect( wrapper ).toBeVisible();
		await expect( wrapper ).toHaveClass( /notification-error/ );
		await expect( wrapper ).toHaveAttribute(
			'data-currency',
			'usd'
		);
		await expect(
			wrapper.locator( '.number-before' )
		).toHaveText( '$' );
		await expect(
			wrapper.locator( '.number-after' )
		).toHaveText( '.00' );

		const input = page.locator(
			'input[name="number_extras"]'
		);
		await expect( input ).toHaveAttribute(
			'id',
			'custom-number-id'
		);
		await expect( input ).toHaveAttribute(
			'data-step-size',
			'5'
		);
	} );

	test( 'number no wrapper', async ( { page } ) => {
		const input = page.locator(
			'input[name="number_no_wrapper"]'
		);
		await expect( input ).toBeAttached();
		const wrapper = page.locator(
			'#form-field_number_no_wrapper'
		);
		await expect( wrapper ).toHaveCount( 0 );
	} );

	// ===== RANGE =====

	test( 'range basic renders', async ( { page } ) => {
		const input = page.locator( 'input[name="range_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'range' );
		await expect( input ).toHaveAttribute( 'min', '0' );
		await expect( input ).toHaveAttribute( 'max', '100' );
		await expect( input ).toHaveValue( '50' );
		await expect( input ).toHaveClass(
			/pc-form__element__field--range_input/
		);
	} );

	test( 'range full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="range_full"]' );
		await expect( input ).toHaveAttribute( 'min', '0' );
		await expect( input ).toHaveAttribute( 'max', '200' );
		await expect( input ).toHaveAttribute( 'step', '10' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'off' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveValue( '100' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'range notification, before/after and wrapper data', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_range_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-info/ );
		await expect( wrapper ).toHaveAttribute(
			'data-type',
			'slider'
		);
		await expect(
			wrapper.locator( '.range-min' )
		).toHaveText( '0' );
		await expect(
			wrapper.locator( '.range-max' )
		).toHaveText( '200' );
	} );
} );
