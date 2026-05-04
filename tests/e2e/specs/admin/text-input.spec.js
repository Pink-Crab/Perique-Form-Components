const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=text-input';

test.describe( 'Text Input - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'basic text input renders with label, value, placeholder and style classes', async ( {
		page,
	} ) => {
		const input = page.locator( 'input[name="text_basic"]' );
		await expect( input ).toBeVisible();
		await expect( input ).toHaveAttribute( 'type', 'text' );
		await expect( input ).toHaveValue( 'Hello World' );
		await expect( input ).toHaveAttribute(
			'placeholder',
			'Enter text...'
		);

		// Label.
		const label = page.locator( 'label[for="text_basic"]' );
		await expect( label ).toHaveText( 'Basic Text' );

		// Wrapper with default style classes.
		const wrapper = page.locator( '#form-field_text_basic' );
		await expect( wrapper ).toBeVisible();
		await expect( wrapper ).toHaveClass(
			/pc-form__element--text_input/
		);

		// Field style classes.
		await expect( input ).toHaveClass(
			/pc-form__element__field--text_input/
		);
		await expect( input ).toHaveClass( /form-control/ );
		await expect( input ).toHaveClass( /text-input/ );
	} );

	test( 'datalist renders with options', async ( { page } ) => {
		const input = page.locator( 'input[name="text_datalist"]' );
		await expect( input ).toBeVisible();
		await expect( input ).toHaveAttribute( 'list' );

		const listId = await input.getAttribute( 'list' );
		const datalist = page.locator( `datalist#${ listId }` );
		await expect( datalist ).toBeAttached();

		const options = datalist.locator( 'option' );
		await expect( options ).toHaveCount( 4 );
	} );

	test( 'before/after wrapper content', async ( { page } ) => {
		const wrapper = page.locator( '#form-field_text_wrapped' );
		await expect( wrapper ).toBeVisible();

		const before = wrapper.locator( '.before-content' );
		await expect( before ).toHaveText( 'Before' );

		const after = wrapper.locator( '.after-content' );
		await expect( after ).toHaveText( 'After' );
	} );

	test( 'no wrapper mode', async ( { page } ) => {
		const input = page.locator( 'input[name="text_no_wrapper"]' );
		await expect( input ).toBeAttached();

		const wrapper = page.locator( '#form-field_text_no_wrapper' );
		await expect( wrapper ).toHaveCount( 0 );
	} );

	test( 'issue #23: before/after render even when show_wrapper(false)', async ( { page } ) => {
		// Input is present but no wrapper div with the auto-generated id.
		const input = page.locator( 'input[name="text_no_wrapper_adornments"]' );
		await expect( input ).toBeAttached();
		await expect(
			page.locator( '#form-field_text_no_wrapper_adornments' )
		).toHaveCount( 0 );

		// Adornments still render (issue #23 — bug was that wrapper-off dropped them).
		const before = page.locator( '.e2e-no-wrap-before' );
		await expect( before ).toHaveText( 'BEFORE_NO_WRAP' );
		const after = page.locator( '.e2e-no-wrap-after' );
		await expect( after ).toHaveText( 'AFTER_NO_WRAP' );
	} );

	test( 'issue #23: bracketed (PHP nested-array) field name is preserved verbatim in name attribute', async ( {
		page,
	} ) => {
		// Old behaviour: sanitize_title() mangled "wm_loc_coordinates[0][latlong]"
		// into "wm_loc_coordinates0latlong". The fix preserves it.
		const input = page.locator(
			'input[name="wm_loc_coordinates[0][latlong]"]'
		);
		await expect( input ).toBeVisible();
		await expect( input ).toHaveAttribute(
			'name',
			'wm_loc_coordinates[0][latlong]'
		);
	} );

	test( 'data attributes', async ( { page } ) => {
		const input = page.locator( 'input[name="text_data_attrs"]' );
		await expect( input ).toHaveAttribute(
			'data-custom-key',
			'custom-value'
		);
		await expect( input ).toHaveAttribute( 'data-another', 'value2' );
	} );

	test( 'custom wrapper class', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_text_wrapper_class'
		);
		await expect( wrapper ).toHaveClass( /my-custom-wrapper/ );
	} );

	test( 'disabled attribute', async ( { page } ) => {
		const input = page.locator( 'input[name="text_disabled"]' );
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveValue( 'Cannot edit' );
	} );

	test( 'readonly attribute', async ( { page } ) => {
		const input = page.locator( 'input[name="text_readonly"]' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toHaveValue( 'Read only value' );
	} );

	test( 'required attribute', async ( { page } ) => {
		const input = page.locator( 'input[name="text_required"]' );
		await expect( input ).toHaveAttribute( 'required', '' );
	} );

	test( 'pattern attribute', async ( { page } ) => {
		const input = page.locator( 'input[name="text_pattern"]' );
		await expect( input ).toHaveAttribute(
			'pattern',
			'[A-Za-z]{3,}'
		);
	} );

	test( 'minlength and maxlength attributes', async ( { page } ) => {
		const input = page.locator( 'input[name="text_length"]' );
		await expect( input ).toHaveAttribute( 'minlength', '3' );
		await expect( input ).toHaveAttribute( 'maxlength', '50' );
	} );

	test( 'inputmode attribute', async ( { page } ) => {
		const input = page.locator( 'input[name="text_inputmode"]' );
		await expect( input ).toHaveAttribute( 'inputmode', 'numeric' );
	} );

	test( 'spellcheck attribute', async ( { page } ) => {
		const input = page.locator( 'input[name="text_spellcheck"]' );
		await expect( input ).toHaveAttribute( 'spellcheck', 'false' );
	} );

	test( 'size attribute', async ( { page } ) => {
		const input = page.locator( 'input[name="text_size"]' );
		await expect( input ).toHaveAttribute( 'size', '30' );
	} );

	test( 'autocomplete attribute', async ( { page } ) => {
		const input = page.locator( 'input[name="text_autocomplete"]' );
		await expect( input ).toHaveAttribute(
			'autocomplete',
			'given-name'
		);
	} );

	test( 'custom ID on field', async ( { page } ) => {
		const input = page.locator( 'input[name="text_custom_id"]' );
		await expect( input ).toHaveAttribute( 'id', 'my-custom-id' );
	} );

	test( 'raw attribute and attributes', async ( { page } ) => {
		const input = page.locator( 'input[name="text_raw_attrs"]' );
		await expect( input ).toHaveAttribute( 'tabindex', '5' );
		await expect( input ).toHaveAttribute(
			'aria-label',
			'Custom aria label'
		);
		await expect( input ).toHaveAttribute( 'title', 'Custom title' );
	} );

	test( 'custom wrapper ID', async ( { page } ) => {
		const wrapper = page.locator( '#my-wrapper-id' );
		await expect( wrapper ).toBeVisible();

		const input = wrapper.locator( 'input[name="text_wrapper_id"]' );
		await expect( input ).toBeVisible();
	} );

	test( 'wrapper data attributes', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_text_wrapper_data'
		);
		await expect( wrapper ).toHaveAttribute(
			'data-section',
			'form-top'
		);
		await expect( wrapper ).toHaveAttribute( 'data-index', '3' );
	} );

	test( 'info notification adds classes to field and wrapper', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_text_notification_info'
		);
		await expect( wrapper ).toHaveClass( /notification-info/ );

		const input = page.locator(
			'input[name="text_notification_info"]'
		);
		await expect( input ).toHaveClass( /notification-info/ );
	} );

	test( 'error notification adds classes to field and wrapper', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_text_notification_error'
		);
		await expect( wrapper ).toHaveClass( /notification-error/ );

		const input = page.locator(
			'input[name="text_notification_error"]'
		);
		await expect( input ).toHaveClass( /notification-error/ );
	} );

	test( 'success notification adds classes', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_text_notification_success'
		);
		await expect( wrapper ).toHaveClass( /notification-success/ );

		const input = page.locator(
			'input[name="text_notification_success"]'
		);
		await expect( input ).toHaveClass( /notification-success/ );
	} );

	test( 'warning notification adds classes', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_text_notification_warning'
		);
		await expect( wrapper ).toHaveClass( /notification-warning/ );

		const input = page.locator(
			'input[name="text_notification_warning"]'
		);
		await expect( input ).toHaveClass( /notification-warning/ );
	} );

	test( 'custom style applies custom classes to wrapper and field', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_text_custom_style'
		);
		await expect( wrapper ).toHaveClass( /custom-wrapper/ );
		await expect( wrapper ).toHaveClass(
			/custom-wrapper--text_input/
		);

		const input = page.locator(
			'input[name="text_custom_style"]'
		);
		await expect( input ).toHaveClass( /custom-field/ );
		await expect( input ).toHaveClass(
			/custom-field--text_input/
		);
		await expect( input ).toHaveValue( 'Styled value' );

		// Should NOT have default style classes.
		await expect( wrapper ).not.toHaveClass( /pc-form__element/ );
		await expect( input ).not.toHaveClass(
			/pc-form__element__field/
		);
	} );
} );
