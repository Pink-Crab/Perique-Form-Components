const { test, expect } = require( '../../fixtures' );

const TAB_URL =
	'/wp-admin/admin.php?page=form-component-tests&tab=date-inputs';

test.describe( 'Date & Time Inputs - Kitchen Sink', () => {
	test.beforeEach( async ( { page } ) => {
		await page.goto( TAB_URL );
	} );

	// ===== DATE =====

	test( 'date basic renders with value and style classes', async ( {
		page,
	} ) => {
		const input = page.locator( 'input[name="date_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'date' );
		await expect( input ).toHaveValue( '2026-01-15' );
		await expect( input ).toHaveClass(
			/pc-form__element__field--date_input/
		);
		await expect(
			page.locator( 'label[for="date_basic"]' )
		).toHaveText( 'Date' );
	} );

	test( 'date full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="date_full"]' );
		await expect( input ).toHaveAttribute( 'min', '2020-01-01' );
		await expect( input ).toHaveAttribute( 'max', '2030-12-31' );
		await expect( input ).toHaveAttribute( 'step', '7' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'bday' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveValue( '2026-06-15' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'date notification, before/after and wrapper data', async ( {
		page,
	} ) => {
		const wrapper = page.locator(
			'#form-field_date_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-warning/ );
		await expect( wrapper ).toHaveAttribute(
			'data-format',
			'iso'
		);
		await expect(
			wrapper.locator( '.date-icon' )
		).toHaveText( 'Cal' );
		await expect(
			wrapper.locator( '.date-hint' )
		).toHaveText( 'YYYY-MM-DD' );

		const input = page.locator(
			'input[name="date_extras"]'
		);
		await expect( input ).toHaveAttribute(
			'id',
			'custom-date-id'
		);
	} );

	// ===== TIME =====

	test( 'time basic renders', async ( { page } ) => {
		const input = page.locator( 'input[name="time_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'time' );
		await expect( input ).toHaveValue( '14:30:00' );
		await expect( input ).toHaveClass(
			/pc-form__element__field--time_input/
		);
	} );

	test( 'time full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="time_full"]' );
		await expect( input ).toHaveAttribute( 'min', '09:00' );
		await expect( input ).toHaveAttribute( 'max', '17:00' );
		await expect( input ).toHaveAttribute( 'step', '900' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'off' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveAttribute(
			'inputmode',
			'numeric'
		);
		await expect( input ).toHaveValue( '12:00:00' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'time notification and before content', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_time_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-info/ );
		await expect( wrapper ).toHaveAttribute(
			'data-period',
			'business'
		);
		await expect(
			wrapper.locator( '.time-icon' )
		).toHaveText( 'Clock' );
	} );

	// ===== DATETIME =====

	test( 'datetime basic renders', async ( { page } ) => {
		const input = page.locator(
			'input[name="datetime_basic"]'
		);
		await expect( input ).toHaveAttribute(
			'type',
			'datetime-local'
		);
		await expect( input ).toHaveValue( '2026-01-15T14:30' );
		await expect( input ).toHaveClass(
			/pc-form__element__field--datetime-local_input/
		);
	} );

	test( 'datetime full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="datetime_full"]' );
		await expect( input ).toHaveAttribute(
			'min',
			'2020-01-01T00:00'
		);
		await expect( input ).toHaveAttribute(
			'max',
			'2030-12-31T23:59'
		);
		await expect( input ).toHaveAttribute( 'step', '3600' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'off' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toHaveValue( '2026-06-15T12:00' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'datetime notification and after content', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_datetime_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-success/ );
		await expect(
			wrapper.locator( '.datetime-tz' )
		).toHaveText( 'UTC' );
	} );

	// ===== MONTH =====

	test( 'month basic renders', async ( { page } ) => {
		const input = page.locator( 'input[name="month_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'month' );
		await expect( input ).toHaveValue( '2026-01' );
		await expect( input ).toHaveClass(
			/pc-form__element__field--month_input/
		);
	} );

	test( 'month full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="month_full"]' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'off' );
		await expect( input ).toHaveAttribute( 'min', '2020-01' );
		await expect( input ).toHaveAttribute( 'max', '2030-12' );
		await expect( input ).toHaveAttribute( 'step', '3' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveAttribute(
			'inputmode',
			'numeric'
		);
		await expect( input ).toHaveValue( '2026-06' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'month notification', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_month_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-error/ );
	} );

	// ===== WEEK =====

	test( 'week basic renders', async ( { page } ) => {
		const input = page.locator( 'input[name="week_basic"]' );
		await expect( input ).toHaveAttribute( 'type', 'week' );
		await expect( input ).toHaveValue( '2026-W03' );
		await expect( input ).toHaveClass(
			/pc-form__element__field--week_input/
		);
	} );

	test( 'week full exercises all traits', async ( { page } ) => {
		const input = page.locator( 'input[name="week_full"]' );
		await expect( input ).toHaveAttribute( 'autocomplete', 'off' );
		await expect( input ).toHaveAttribute( 'min', '2020-W01' );
		await expect( input ).toHaveAttribute( 'max', '2030-W52' );
		await expect( input ).toHaveAttribute( 'step', '2' );
		await expect( input ).toHaveAttribute( 'required', '' );
		await expect( input ).toHaveAttribute( 'readonly', '' );
		await expect( input ).toBeDisabled();
		await expect( input ).toHaveAttribute(
			'inputmode',
			'numeric'
		);
		await expect( input ).toHaveValue( '2026-W26' );
		await expect( input ).toHaveAttribute( 'list' );
	} );

	test( 'week notification', async ( { page } ) => {
		const wrapper = page.locator(
			'#form-field_week_extras'
		);
		await expect( wrapper ).toHaveClass( /notification-warning/ );
	} );
} );
