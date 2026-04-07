<?php
/**
 * MU-Plugin: Bootstraps Perique and registers a test admin page
 * for Playwright E2E testing of Form Components.
 *
 * Loaded by wp-env via .wp-env.json mappings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// Show PHP errors on screen for debugging.
ini_set( 'display_errors', '1' );
ini_set( 'display_startup_errors', '1' );
error_reporting( E_ALL );

$plugin_path = WP_PLUGIN_DIR . '/perique-form-components';

if ( ! file_exists( $plugin_path . '/vendor/autoload.php' ) ) {
	$plugins = glob( WP_PLUGIN_DIR . '/*/vendor/pinkcrab/perique-framework-core' );
	if ( ! empty( $plugins ) ) {
		$plugin_path = dirname( $plugins[0], 3 );
	}
}

require_once $plugin_path . '/vendor/autoload.php';

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Interfaces\Hookable;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Form_Components\Module\Form_Components;

/**
 * Hookable class that registers test admin pages.
 *
 * Routes to per-field-type view templates via ?tab= parameter.
 */
class Form_Test_Page implements Hookable {

	private const TABS = array(
		'text-input',
		'typed-inputs',
		'numeric-inputs',
		'date-inputs',
		'special-inputs',
		'select',
		'textarea',
		'groups',
		'raw-html',
		'button',
		'nonce',
		'form',
		'group',
		'fieldset',
	);

	/** @var View */
	private View $view;

	public function __construct( View $view ) {
		$this->view = $view;
	}

	public function register( Hook_Loader $loader ): void {
		$loader->action( 'admin_menu', [ $this, 'add_test_page' ] );
	}

	public function add_test_page(): void {
		add_menu_page(
			'Form Component Tests',
			'Form Tests',
			'manage_options',
			'form-component-tests',
			[ $this, 'render_test_page' ],
			'dashicons-forms',
			100
		);
	}

	public function render_test_page(): void {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : '';

		if ( in_array( $tab, self::TABS, true ) ) {
			$this->view->render( "fields/{$tab}", array() );
		} else {
			$this->view->render( 'test-page', array( 'tabs' => self::TABS ) );
		}
	}
}

( new App_Factory( $plugin_path ) )
	->set_base_view_path( $plugin_path . '/tests/e2e/views' )
	->default_setup()
	->module( Form_Components::class )
	->registration_classes( array( Form_Test_Page::class ) )
	->boot();
