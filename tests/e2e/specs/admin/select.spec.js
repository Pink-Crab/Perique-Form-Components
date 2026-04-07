const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=select';

test.describe( 'Select - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'basic select renders with options, label and style classes', async ( {
		page,
	} ) => {
		const select = page.locator( 'select[name="select_basic"]' );
		await expect( select ).toBeVisible();
		await expect( select ).toHaveClass(
			/pc-form__element__field--select/
		);

		const options = select.locator( 'option' );
		await expect( options ).toHaveCount( 4 );
		await expect( options.nth( 0 ) ).toHaveText(
			'Choose an option'
		);
		await expect( options.nth( 1 ) ).toHaveText( 'Option One' );
		await expect( options.nth( 2 ) ).toHaveText( 'Option Two' );
		await expect( options.nth( 3 ) ).toHaveText( 'Option Three' );

		await expect(
			page.locator( 'label[for="select_basic"]' )
		).toHaveText( 'Basic Select' );

		const wrapper = page.locator( '#form-field_select_basic' );
		await expect( wrapper ).toHaveClass(
			/pc-form__element--select/
		);
	} );

	test( 'pre-selected value', async ( { page } ) => {
		const select = page.locator(
			'select[name="select_preselected"]'
		);
		await expect( select ).toHaveValue( 'b' );

		const selectedOption = select.locator( 'option[selected]' );
		await expect( selectedOption ).toHaveText( 'Bravo' );
	} );

	test( 'optgroups render correctly', async ( { page } ) => {
		const select = page.locator(
			'select[name="select_optgroups"]'
		);
		await expect( select ).toBeVisible();

		const optgroups = select.locator( 'optgroup' );
		await expect( optgroups ).toHaveCount( 2 );
		await expect( optgroups.nth( 0 ) ).toHaveAttribute(
			'label',
			'Fruits'
		);
		await expect( optgroups.nth( 1 ) ).toHaveAttribute(
			'label',
			'Vegetables'
		);

		const fruitOptions = optgroups.nth( 0 ).locator( 'option' );
		await expect( fruitOptions ).toHaveCount( 2 );
		await expect( fruitOptions.nth( 0 ) ).toHaveText( 'Apple' );
		await expect( fruitOptions.nth( 1 ) ).toHaveText( 'Banana' );

		const vegOptions = optgroups.nth( 1 ).locator( 'option' );
		await expect( vegOptions ).toHaveCount( 2 );
		await expect( vegOptions.nth( 0 ) ).toHaveText( 'Carrot' );
		await expect( vegOptions.nth( 1 ) ).toHaveText( 'Pea' );
	} );

	test( 'disabled select', async ( { page } ) => {
		const select = page.locator(
			'select[name="select_disabled"]'
		);
		await expect( select ).toBeDisabled();
	} );

	test( 'required select', async ( { page } ) => {
		const select = page.locator(
			'select[name="select_required"]'
		);
		await expect( select ).toHaveAttribute( 'required', '' );
	} );

	test( 'size attribute', async ( { page } ) => {
		const select = page.locator(
			'select[name="select_size"]'
		);
		await expect( select ).toHaveAttribute( 'size', '3' );
	} );

	test( 'autocomplete attribute', async ( { page } ) => {
		const select = page.locator(
			'select[name="select_autocomplete"]'
		);
		await expect( select ).toHaveAttribute(
			'autocomplete',
			'country'
		);
	} );

	test( 'notification adds classes', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_select_notification'
		);
		await expect( wrapper ).toHaveClass( /notification-error/ );
	} );

	test( 'before/after, wrapper data and custom id', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_select_wrapped'
		);
		await expect(
			wrapper.locator( '.select-before' )
		).toHaveText( 'Choose:' );
		await expect(
			wrapper.locator( '.select-after' )
		).toHaveText( 'Done' );
		await expect( wrapper ).toHaveAttribute(
			'data-field',
			'select'
		);

		const select = page.locator(
			'select[name="select_wrapped"]'
		);
		await expect( select ).toHaveAttribute(
			'id',
			'custom-select-id'
		);
	} );

	test( 'multiple select with pre-selected values', async ( { page } ) => {
		const select = page.locator(
			'select[name="select_multiple[]"]'
		);
		await expect( select ).toBeVisible();
		await expect( select ).toHaveAttribute( 'multiple', '' );
		await expect( select ).toHaveAttribute( 'size', '5' );

		// Check PHP and Go are pre-selected.
		const selectedOptions = select.locator( 'option[selected]' );
		await expect( selectedOptions ).toHaveCount( 2 );
		await expect( selectedOptions.nth( 0 ) ).toHaveText( 'PHP' );
		await expect( selectedOptions.nth( 1 ) ).toHaveText( 'Go' );

		// Check non-selected options don't have selected attribute.
		const jsOption = select.locator( 'option[value="js"]' );
		await expect( jsOption ).not.toHaveAttribute( 'selected', '' );
	} );

	test( 'no wrapper', async ( { page } ) => {
		const select = page.locator(
			'select[name="select_no_wrapper"]'
		);
		await expect( select ).toBeAttached();
		const wrapper = page.locator(
			'#form-field_select_no_wrapper'
		);
		await expect( wrapper ).toHaveCount( 0 );
	} );

	test( 'custom style applies custom classes', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_select_custom_style'
		);
		await expect( wrapper ).toHaveClass( /custom-wrapper/ );
		await expect( wrapper ).toHaveClass(
			/custom-wrapper--select/
		);

		const select = page.locator(
			'select[name="select_custom_style"]'
		);
		await expect( select ).toHaveClass( /custom-field/ );
		await expect( select ).toHaveClass(
			/custom-field--select/
		);
		await expect( wrapper ).not.toHaveClass( /pc-form__element/ );
	} );
} );
