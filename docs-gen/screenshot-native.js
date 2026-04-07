/**
 * Screen capture for native OS overlays (selects, date pickers, color picker).
 *
 * Runs headed Chromium, opens the overlay, then uses import/xdotool
 * to capture the actual screen pixels.
 *
 * Usage:
 *   node screenshot-native.js [base-url]
 *
 * Default base URL: http://localhost:57893
 */

const { chromium } = require( 'playwright' );
const { execSync } = require( 'child_process' );
const path = require( 'path' );
const fs = require( 'fs' );

const BASE_URL = process.argv[2] || 'http://localhost:57893';
const FILTER = process.argv[3] || null; // e.g. "text/datalist" or "select"
const OUTPUT_DIR = path.join( __dirname, 'screenshots' );

/**
 * Each entry: page, example ID, how to trigger the overlay.
 */
const CAPTURES = [
	{ page: 'text',     id: 'datalist',  type: 'datalist', text: 'a' },
	{ page: 'select',   id: 'basic',     type: 'select' },
	{ page: 'select',   id: 'optgroups', type: 'select' },
	{ page: 'date',     id: 'basic',     type: 'input' },
	{ page: 'date',     id: 'value',     type: 'input' },
	{ page: 'time',     id: 'basic',     type: 'input' },
	{ page: 'time',     id: 'value',     type: 'input' },
	{ page: 'datetime', id: 'basic',     type: 'input' },
	{ page: 'datetime', id: 'value',     type: 'input' },
	{ page: 'month',    id: 'basic',     type: 'input' },
	{ page: 'month',    id: 'value',     type: 'input' },
	{ page: 'week',     id: 'basic',     type: 'input' },
	{ page: 'week',     id: 'value',     type: 'input' },
	{ page: 'color',    id: 'basic',     type: 'input' },
	{ page: 'color',    id: 'value',     type: 'input' },
];

/**
 * Perform a real OS-level mouse click on an element using xdotool.
 * Playwright clicks are simulated and don't trigger native dropdowns.
 *
 * @param {object} page     Playwright page
 * @param {object} element  Playwright element handle
 * @param {object} opts     { offsetX, fromRight }
 */
async function xdotoolClick( page, element, opts = {} ) {
	// Get the element's absolute screen position using getBoundingClientRect
	// plus the browser window/chrome offsets.
	const info = await element.evaluate( ( el ) => {
		const rect = el.getBoundingClientRect();
		return {
			rectX: rect.x,
			rectY: rect.y,
			rectW: rect.width,
			rectH: rect.height,
			screenX: window.screenX,
			screenY: window.screenY,
			outerW: window.outerWidth,
			outerH: window.outerHeight,
			innerW: window.innerWidth,
			innerH: window.innerHeight,
			dpr: window.devicePixelRatio,
		};
	} );

	console.log( `    debug: rect(${ Math.round(info.rectX) },${ Math.round(info.rectY) },${ Math.round(info.rectW) }x${ Math.round(info.rectH) }) screen(${ info.screenX },${ info.screenY }) outer(${ info.outerW }x${ info.outerH }) inner(${ info.innerW }x${ info.innerH }) dpr=${ info.dpr }` );

	const dpr = info.dpr;
	const chromeHeight = info.outerH - info.innerH;
	const borderLeft = ( info.outerW - info.innerW ) / 2;

	// Scale rect coordinates by DPR if the OS uses scaling.
	const contentX = info.screenX + borderLeft;
	const contentY = info.screenY + chromeHeight;

	let clickX, clickY;
	if ( opts.fromRight ) {
		clickX = Math.round( contentX + info.rectX / dpr + info.rectW / dpr + ( opts.offsetX || 0 ) );
	} else {
		clickX = Math.round( contentX + info.rectX / dpr + ( opts.offsetX || info.rectW / dpr / 2 ) );
	}
	clickY = Math.round( contentY + info.rectY / dpr + info.rectH / dpr / 2 );

	clickX -= 5;
	clickY += 2;
	console.log( `    xdotool click at (${ clickX },${ clickY })` );
	execSync( `xdotool mousemove ${ clickX } ${ clickY }` );
	await page.waitForTimeout( 200 );
	execSync( `xdotool click 1` );
}

/**
 * Capture a region of the screen using scrot, cropped to the example element
 * plus extra space below for dropdowns.
 *
 * @param {string} outputPath  Output file path
 * @param {object} page        Playwright page
 * @param {object} element     The example element to crop around
 */
async function screenCapture( outputPath, page, element ) {
	try {
		const box = await element.boundingBox();
		if ( ! box ) throw new Error( 'No bounding box' );

		// Get content area offset.
		const chromeInfo = await page.evaluate( () => ( {
			screenX: window.screenX,
			screenY: window.screenY,
			outerHeight: window.outerHeight,
			innerHeight: window.innerHeight,
		} ) );
		const chromeHeight = chromeInfo.outerHeight - chromeInfo.innerHeight;
		const contentX = chromeInfo.screenX;
		const contentY = chromeInfo.screenY + chromeHeight;

		// Get browser window bounds to clamp capture area.
		const windowId = execSync( 'xdotool getactivewindow' ).toString().trim();
		const geo = execSync( `xdotool getwindowgeometry --shell ${ windowId }` ).toString();
		const win = {};
		geo.split( '\n' ).forEach( ( line ) => {
			const [ k, v ] = line.split( '=' );
			if ( k && v ) win[ k.trim() ] = parseInt( v.trim() );
		} );
		const winBottom = ( win.Y || 0 ) + ( win.HEIGHT || 800 );

		// Capture the example area + space below for dropdown, clamped to window.
		const padLeft = 10;
		const padRight = 10;
		const padTop = 10;

		const x = Math.max( 0, Math.round( contentX + box.x - padLeft ) );
		const y = Math.max( 0, Math.round( contentY + box.y - padTop ) );
		const w = Math.round( box.width + padLeft + padRight );
		const maxH = winBottom - y;
		const h = Math.min( Math.round( box.height + padTop + 300 ), maxH );

		const tmpPath = outputPath + '.tmp.png';
		execSync( `scrot -a ${ x },${ y },${ w },${ h } "${ tmpPath }"` );
		execSync( `convert "${ tmpPath }" -crop ${ w }x${ h - 5 }+0+0 +repage "${ outputPath }"` );
		try { fs.unlinkSync( tmpPath ); } catch ( e ) {}
		return true;
	} catch ( err ) {
		console.log( `    FAILED: ${ err.message }` );
		return false;
	}
}

async function run() {
	const browser = await chromium.launch( { headless: false } );
	const context = await browser.newContext( {
		viewport: { width: 600, height: 1000 },
	} );

	// Filter captures if specified.
	const filtered = FILTER
		? CAPTURES.filter( ( c ) => `${ c.page }/${ c.id }` === FILTER || c.page === FILTER )
		: CAPTURES;

	if ( filtered.length === 0 ) {
		console.log( `No captures match filter: ${ FILTER }` );
		await browser.close();
		return;
	}

	// Group captures by page to avoid reloading.
	const byPage = new Map();
	for ( const cap of filtered ) {
		if ( ! byPage.has( cap.page ) ) {
			byPage.set( cap.page, [] );
		}
		byPage.get( cap.page ).push( cap );
	}

	for ( const [ pageName, caps ] of byPage ) {
		const page = await context.newPage();
		const url = `${ BASE_URL }/?docs-screenshot=${ pageName }`;

		console.log( `\nPage: ${ pageName }` );
		await page.goto( url, { waitUntil: 'networkidle' } );

		const pageDir = path.join( OUTPUT_DIR, pageName );
		if ( ! fs.existsSync( pageDir ) ) {
			fs.mkdirSync( pageDir, { recursive: true } );
		}

		for ( const cap of caps ) {
			const example = await page.$( `#${ cap.id }` );
			if ( ! example ) {
				console.log( `  #${ cap.id } - not found, skipping` );
				continue;
			}

			// Scroll into view and wait for layout to settle.
			await example.scrollIntoViewIfNeeded();
			await page.waitForTimeout( 1000 );

			// Open the overlay.
			if ( cap.type === 'select' ) {
				const select = await example.$( 'select' );
				if ( select ) {
					await select.focus();
					await page.waitForTimeout( 200 );
					await select.press( 'Space' );
				}
			} else if ( cap.type === 'datalist' ) {
				const input = await example.$( 'input' );
				if ( input ) {
					// Use xdotool for a real OS click - Playwright clicks don't trigger datalist.
					await xdotoolClick( page, input, { offsetX: -10, fromRight: true } );
				}
			} else {
				const input = await example.$( 'input' );
				if ( input ) {
					await xdotoolClick( page, input, { offsetX: -10, fromRight: true } );
				}
			}

			// Wait for the overlay to render.
			await page.waitForTimeout( 1000 );

			// Remove existing file to prevent scrot adding a counter suffix.
			const outputFile = path.join( pageDir, `${ cap.id }.png` );
			if ( fs.existsSync( outputFile ) ) {
				fs.unlinkSync( outputFile );
			}
			const ok = await screenCapture( outputFile, page, example );
			console.log( `  #${ cap.id } - ${ ok ? 'captured' : 'FAILED' }` );

			// Close the overlay.
			await page.keyboard.press( 'Escape' );
			await page.waitForTimeout( 500 );
		}

		await page.close();
	}

	await browser.close();
	console.log( '\nDone!' );
}

run().catch( ( error ) => {
	console.error( 'Fatal error:', error );
	process.exit( 1 );
} );
