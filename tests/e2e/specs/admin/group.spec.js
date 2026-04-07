const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=group';

test.describe( 'Group - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'basic group renders as div with child fields', async ( {
		page,
	} ) => {
		const group = page.locator( '#form-group_basic_group' );
		await expect( group ).toBeVisible();

		await expect(
			group.locator( 'input[name="group_name"]' )
		).toBeVisible();
		await expect(
			group.locator( 'input[name="group_email"]' )
		).toBeVisible();
		await expect(
			group.locator( 'label[for="group_name"]' )
		).toHaveText( 'Name' );
	} );

	test( 'group with before/after content', async ( { page } ) => {
		const group = page.locator( '#form-group_wrapped_group' );
		await expect( group ).toBeVisible();

		await expect(
			group.locator( '.group-header' )
		).toHaveText( 'Section Header' );
		await expect(
			group.locator( '.group-footer' )
		).toHaveText( 'Section Footer' );
	} );

	test( 'group with wrapper data and custom class', async ( {
		page,
	} ) => {
		const group = page.locator( '#form-group_data_group' );
		await expect( group ).toHaveAttribute(
			'data-section',
			'personal'
		);
		await expect( group ).toHaveClass( /custom-group-class/ );
	} );

	test( 'group has style classes', async ( { page } ) => {
		const group = page.locator( '#form-group_basic_group' );
		await expect( group ).toHaveClass(
			/pc-form__element--group/
		);
	} );
} );
