/**
 * Playwright script to generate documentation screenshots (headless).
 *
 * Standard DOM screenshots for all non-native-overlay examples.
 * For native pickers/selects, run screenshot-native.js separately.
 *
 * Usage:
 *   node screenshot.js [base-url]
 *
 * Default base URL: http://localhost:57893
 */

const { chromium } = require( 'playwright' );
const path = require( 'path' );
const fs = require( 'fs' );

const BASE_URL = process.argv[2] || 'http://localhost:57893';
const OUTPUT_DIR = path.join( __dirname, 'screenshots' );

const PAGES = [
	'text',
	'email',
	'password',
	'search',
	'tel',
	'url',
	'number',
	'range',
	'date',
	'time',
	'datetime',
	'month',
	'week',
	'color',
	'file',
	'hidden',
	'checkbox',
	'radio',
	'select',
	'textarea',
	'checkbox-group',
	'radio-group',
	'button',
	'form',
	'fieldset',
	'notifications',
];

/**
 * Examples handled by screenshot-native.js instead.
 */
const NATIVE_EXAMPLES = new Set( [
	'text/datalist',
	'select/basic',
	'select/optgroups',
	'date/basic',
	'date/value',
	'time/basic',
	'time/value',
	'datetime/basic',
	'datetime/value',
	'month/basic',
	'month/value',
	'week/basic',
	'week/value',
	'color/basic',
	'color/value',
] );

async function run() {
	// Clean and recreate output directory.
	if ( fs.existsSync( OUTPUT_DIR ) ) {
		fs.rmSync( OUTPUT_DIR, { recursive: true } );
	}
	fs.mkdirSync( OUTPUT_DIR, { recursive: true } );

	const browser = await chromium.launch( { headless: true } );
	const context = await browser.newContext( {
		viewport: { width: 600, height: 800 },
		deviceScaleFactor: 2,
	} );

	for ( const page_name of PAGES ) {
		const page = await context.newPage();
		const url = `${ BASE_URL }/?docs-screenshot=${ page_name }`;

		console.log( `Capturing: ${ page_name }` );

		try {
			await page.goto( url, { waitUntil: 'networkidle' } );

			const pageDir = path.join( OUTPUT_DIR, page_name );
			if ( ! fs.existsSync( pageDir ) ) {
				fs.mkdirSync( pageDir, { recursive: true } );
			}

			// Full page screenshot.
			await page.screenshot( {
				path: path.join( pageDir, 'full.png' ),
				fullPage: true,
			} );

			// Individual examples - skip native overlay ones.
			const examples = await page.$$( '.doc-example' );
			let captured = 0;
			let skipped = 0;
			for ( const example of examples ) {
				const id = await example.getAttribute( 'id' );
				if ( ! id ) continue;

				const key = `${ page_name }/${ id }`;
				if ( NATIVE_EXAMPLES.has( key ) ) {
					skipped++;
					continue;
				}

				await example.screenshot( {
					path: path.join( pageDir, `${ id }.png` ),
				} );
				captured++;
			}

			const msg = skipped > 0
				? `${ captured } captured, ${ skipped } deferred to screenshot-native.js`
				: `${ captured } captured`;
			console.log( `  -> ${ msg }` );
		} catch ( error ) {
			console.error( `  ERROR: ${ error.message }` );
		}

		await page.close();
	}

	await browser.close();
	console.log( `\nDone! Run screenshot-native.js for picker/select captures.` );
}

run().catch( ( error ) => {
	console.error( 'Fatal error:', error );
	process.exit( 1 );
} );
