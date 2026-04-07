const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=groups';

test.describe( 'Groups - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	// ===== CHECKBOX GROUP =====

	test( 'checkbox group renders with options, selections and style classes', async ( {
		page,
	} ) => {
		const section = page.locator( '.e2e-checkbox-group-section' );

		const legend = section.locator( 'legend' ).first();
		await expect( legend ).toHaveText( 'Favourite Colours' );

		const checkboxes = section.locator(
			'input[type="checkbox"][name="checkbox_colours[]"]'
		);
		await expect( checkboxes ).toHaveCount( 3 );

		// Pre-checked values.
		await expect(
			section.locator( 'input[value="red"]' )
		).toBeChecked();
		await expect(
			section.locator( 'input[value="green"]' )
		).not.toBeChecked();
		await expect(
			section.locator( 'input[value="blue"]' )
		).toBeChecked();

		// Wrapper style classes.
		const wrapper = page.locator(
			'#form-field_checkbox_colours'
		);
		await expect( wrapper ).toHaveClass(
			/pc-form__element--checkbox_group/
		);
	} );

	test( 'checkbox group disabled', async ( { page } ) => {
		const section = page.locator( '.e2e-checkbox-group-section' );
		const checkboxes = section.locator(
			'input[type="checkbox"][name="checkbox_disabled[]"]'
		);
		await expect( checkboxes ).toHaveCount( 2 );

		await expect( checkboxes.nth( 0 ) ).toBeDisabled();
		await expect( checkboxes.nth( 1 ) ).toBeDisabled();
		await expect( checkboxes.nth( 0 ) ).toBeChecked();
	} );

	test( 'checkbox group notification, before/after and wrapper data', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_checkbox_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-info/ );
		await expect( wrapper ).toHaveAttribute(
			'data-group',
			'checkboxes'
		);
		await expect(
			wrapper.locator( '.cb-group-before' )
		).toHaveText( 'Options:' );
		await expect(
			wrapper.locator( '.cb-group-after' )
		).toHaveText( 'End' );
	} );

	test( 'checkbox group custom style', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_checkbox_custom_style'
		);
		await expect( wrapper ).toHaveClass( /custom-wrapper/ );
		await expect( wrapper ).toHaveClass(
			/custom-wrapper--checkbox_group/
		);
	} );

	// ===== RADIO GROUP =====

	test( 'radio group renders with options, selection and style classes', async ( {
		page,
	} ) => {
		const section = page.locator( '.e2e-radio-group-section' );

		const legend = section.locator( 'legend' ).first();
		await expect( legend ).toHaveText( 'Size' );

		const radios = section.locator(
			'input[type="radio"][name="radio_size"]'
		);
		await expect( radios ).toHaveCount( 3 );

		// Pre-selected value.
		await expect(
			section.locator( 'input[value="medium"]' )
		).toBeChecked();
		await expect(
			section.locator( 'input[value="small"]' )
		).not.toBeChecked();

		const wrapper = page.locator( '#form-field_radio_size' );
		await expect( wrapper ).toHaveClass(
			/pc-form__element--radio_group/
		);
	} );

	test( 'radio group disabled', async ( { page } ) => {
		const radios = page.locator(
			'input[type="radio"][name="radio_disabled"]'
		);
		await expect( radios ).toHaveCount( 2 );
		await expect( radios.nth( 0 ) ).toBeDisabled();
		await expect( radios.nth( 1 ) ).toBeDisabled();
	} );

	test( 'radio group required', async ( { page } ) => {
		const radios = page.locator(
			'input[type="radio"][name="radio_required"]'
		);
		await expect( radios ).toHaveCount( 2 );
	} );

	test( 'radio group notification, before/after and wrapper data', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_radio_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-error/ );
		await expect( wrapper ).toHaveAttribute(
			'data-group',
			'radios'
		);
		await expect(
			wrapper.locator( '.radio-group-before' )
		).toHaveText( 'Pick:' );
		await expect(
			wrapper.locator( '.radio-group-after' )
		).toHaveText( 'Done' );
	} );

	test( 'radio group custom style', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_radio_custom_style'
		);
		await expect( wrapper ).toHaveClass( /custom-wrapper/ );
		await expect( wrapper ).toHaveClass(
			/custom-wrapper--radio_group/
		);
	} );
} );
