const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=button';

test.describe( 'Button - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'basic button renders with type, text and style classes', async ( {
		page,
	} ) => {
		const button = page.locator( 'button[name="btn_basic"]' );
		await expect( button ).toBeVisible();
		await expect( button ).toHaveAttribute( 'type', 'button' );
		await expect( button ).toHaveText( 'click me' );
		await expect( button ).toHaveClass( /pc-form__button/ );
	} );

	test( 'submit button', async ( { page } ) => {
		const button = page.locator( 'button[name="btn_submit"]' );
		await expect( button ).toHaveAttribute( 'type', 'submit' );
		await expect( button ).toHaveText( 'submit form' );
	} );

	test( 'reset button', async ( { page } ) => {
		const button = page.locator( 'button[name="btn_reset"]' );
		await expect( button ).toHaveAttribute( 'type', 'reset' );
		await expect( button ).toHaveText( 'reset form' );
	} );

	test( 'disabled button', async ( { page } ) => {
		const button = page.locator( 'button[name="btn_disabled"]' );
		await expect( button ).toBeDisabled();
	} );

	test( 'button with before/after content', async ( { page } ) => {
		const wrapper = page.locator(
			'[id^="form-button"]'
		).filter( { has: page.locator( 'button[name="btn_wrapped"]' ) } );

		await expect(
			wrapper.locator( '.btn-before' )
		).toHaveText( 'Icon' );
		await expect(
			wrapper.locator( '.btn-after' )
		).toHaveText( 'Help' );
	} );

	test( 'button with data attributes', async ( { page } ) => {
		const button = page.locator( 'button[name="btn_data"]' );
		await expect( button ).toHaveAttribute(
			'data-action',
			'save'
		);
		await expect( button ).toHaveAttribute(
			'data-target',
			'form-1'
		);
	} );

	test( 'button with custom style', async ( { page } ) => {
		const button = page.locator(
			'button[name="btn_custom_style"]'
		);
		await expect( button ).toHaveClass( /custom-button/ );
		await expect( button ).not.toHaveClass( /pc-form__button/ );
	} );
} );
