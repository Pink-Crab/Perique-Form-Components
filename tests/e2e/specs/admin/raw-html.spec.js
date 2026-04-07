const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=raw-html';

test.describe( 'Raw HTML - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	test( 'raw HTML renders content', async ( { page } ) => {
		const raw = page.locator( '.e2e-raw-content' );
		await expect( raw ).toBeVisible();
		await expect( raw.locator( 'p' ) ).toHaveText(
			'Raw HTML Content'
		);
		await expect( raw.locator( 'span' ) ).toHaveText(
			'Nested span'
		);
	} );

	test( 'empty raw HTML renders nothing visible', async ( {
		page,
	} ) => {
		// The empty Raw_HTML should not produce visible content.
		const container = page.locator( '#e2e-raw-html' );
		await expect( container ).toBeVisible();
	} );
} );
