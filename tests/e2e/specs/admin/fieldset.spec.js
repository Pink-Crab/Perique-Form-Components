const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=fieldset';

test.describe( 'Fieldset - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'fieldset renders with legend and child fields', async ( {
		page,
	} ) => {
		const fieldset = page.locator( '#fieldset-personal_info' );
		await expect( fieldset ).toBeVisible();

		// Rendered as <fieldset> tag.
		const tag = await fieldset.evaluate( ( el ) => el.tagName );
		expect( tag ).toBe( 'FIELDSET' );

		// Legend.
		await expect(
			fieldset.locator( 'legend' )
		).toHaveText( 'Personal Information' );

		// Child fields inside fieldset.
		await expect(
			fieldset.locator( 'input[name="first_name"]' )
		).toBeVisible();
		await expect(
			fieldset.locator( 'input[name="last_name"]' )
		).toBeVisible();
		await expect(
			fieldset.locator( 'input[name="fs_email"]' )
		).toBeVisible();
	} );

	test( 'fieldset without legend omits legend element', async ( {
		page,
	} ) => {
		const fieldset = page.locator( '#fieldset-address' );
		await expect( fieldset ).toBeVisible();
		await expect( fieldset.locator( 'legend' ) ).toHaveCount( 0 );
	} );

	test( 'disabled fieldset disables all child fields', async ( {
		page,
	} ) => {
		const fieldset = page.locator( '#fieldset-disabled_section' );
		await expect( fieldset ).toHaveAttribute( 'disabled', '' );

		// Child fields should be effectively disabled.
		await expect(
			fieldset.locator( 'legend' )
		).toHaveText( 'Disabled Section' );
	} );

	test( 'fieldset with before/after content', async ( { page } ) => {
		const fieldset = page.locator(
			'#fieldset-wrapped_fieldset'
		);
		await expect( fieldset ).toBeVisible();

		await expect(
			fieldset.locator( '.fs-before' )
		).toHaveText( 'Instructions here' );
		await expect(
			fieldset.locator( '.fs-after' )
		).toHaveText( 'End of section' );
	} );

	test( 'fieldset with wrapper data and custom class', async ( {
		page,
	} ) => {
		const fieldset = page.locator(
			'#fieldset-data_fieldset'
		);
		await expect( fieldset ).toHaveAttribute(
			'data-section',
			'contact'
		);
		await expect( fieldset ).toHaveClass(
			/custom-fieldset-class/
		);
	} );

	test( 'fieldset has style classes', async ( { page } ) => {
		const fieldset = page.locator( '#fieldset-personal_info' );
		await expect( fieldset ).toHaveClass(
			/pc-form__element--fieldset/
		);
	} );

	test( 'fieldset with custom style', async ( { page } ) => {
		const fieldset = page.locator(
			'#fieldset-custom_fieldset'
		);
		await expect( fieldset ).toHaveClass( /custom-wrapper/ );
		await expect( fieldset ).toHaveClass(
			/custom-wrapper--fieldset/
		);
		await expect( fieldset ).not.toHaveClass(
			/pc-form__element/
		);
	} );
} );
