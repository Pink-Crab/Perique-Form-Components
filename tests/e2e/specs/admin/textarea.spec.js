const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=textarea';

test.describe( 'Textarea - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'basic textarea with content, rows, cols and style classes', async ( {
		page,
	} ) => {
		const textarea = page.locator(
			'textarea[name="textarea_basic"]'
		);
		await expect( textarea ).toBeVisible();
		await expect( textarea ).toHaveValue( 'Default content' );
		await expect( textarea ).toHaveAttribute( 'rows', '5' );
		await expect( textarea ).toHaveAttribute( 'cols', '40' );
		await expect( textarea ).toHaveClass(
			/pc-form__element__field--textarea/
		);

		await expect(
			page.locator( 'label[for="textarea_basic"]' )
		).toHaveText( 'Textarea' );

		const wrapper = page.locator(
			'#form-field_textarea_basic'
		);
		await expect( wrapper ).toHaveClass(
			/pc-form__element--textarea/
		);
	} );

	test( 'empty textarea', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_empty"]'
		);
		await expect( textarea ).toHaveValue( '' );
		await expect( textarea ).toHaveAttribute( 'rows', '3' );
	} );

	test( 'placeholder', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_placeholder"]'
		);
		await expect( textarea ).toHaveAttribute(
			'placeholder',
			'Type here...'
		);
	} );

	test( 'disabled', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_disabled"]'
		);
		await expect( textarea ).toBeDisabled();
		await expect( textarea ).toHaveValue( 'Cannot edit' );
	} );

	test( 'readonly', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_readonly"]'
		);
		await expect( textarea ).toHaveAttribute( 'readonly', '' );
		await expect( textarea ).toHaveValue( 'Read only' );
	} );

	test( 'required', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_required"]'
		);
		await expect( textarea ).toHaveAttribute( 'required', '' );
	} );

	test( 'minlength and maxlength', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_length"]'
		);
		await expect( textarea ).toHaveAttribute( 'minlength', '10' );
		await expect( textarea ).toHaveAttribute(
			'maxlength',
			'500'
		);
	} );

	test( 'spellcheck', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_spellcheck"]'
		);
		await expect( textarea ).toHaveAttribute(
			'spellcheck',
			'false'
		);
	} );

	test( 'autocomplete', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_autocomplete"]'
		);
		await expect( textarea ).toHaveAttribute(
			'autocomplete',
			'off'
		);
	} );

	test( 'notification adds classes', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_textarea_notification'
		);
		await expect( wrapper ).toHaveClass( /notification-error/ );
	} );

	test( 'before/after, wrapper data, data attrs and custom id', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_textarea_wrapped'
		);
		await expect(
			wrapper.locator( '.ta-before' )
		).toHaveText( 'Description:' );
		await expect(
			wrapper.locator( '.ta-after' )
		).toHaveText( 'Max 500 chars' );
		await expect( wrapper ).toHaveAttribute(
			'data-field',
			'description'
		);

		const textarea = page.locator(
			'textarea[name="textarea_wrapped"]'
		);
		await expect( textarea ).toHaveAttribute(
			'id',
			'custom-textarea-id'
		);
		await expect( textarea ).toHaveAttribute(
			'data-editor',
			'plain'
		);
	} );

	test( 'no wrapper', async ( { page } ) => {
		const textarea = page.locator(
			'textarea[name="textarea_no_wrapper"]'
		);
		await expect( textarea ).toBeAttached();
		const wrapper = page.locator(
			'#form-field_textarea_no_wrapper'
		);
		await expect( wrapper ).toHaveCount( 0 );
	} );

	test( 'custom style applies custom classes', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_textarea_custom_style'
		);
		await expect( wrapper ).toHaveClass( /custom-wrapper/ );
		await expect( wrapper ).toHaveClass(
			/custom-wrapper--textarea/
		);

		const textarea = page.locator(
			'textarea[name="textarea_custom_style"]'
		);
		await expect( textarea ).toHaveClass( /custom-field/ );
		await expect( textarea ).toHaveClass(
			/custom-field--textarea/
		);
		await expect( textarea ).toHaveValue( 'Styled content' );
	} );
} );
