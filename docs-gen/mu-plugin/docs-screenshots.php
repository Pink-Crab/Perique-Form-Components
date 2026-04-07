<?php
/**
 * MU-Plugin: Serves clean HTML pages for documentation screenshots.
 *
 * Renders form components via the Perique View engine, wrapped in
 * a minimal HTML document with the docs stylesheet.
 *
 * Access via: ?docs-screenshot={field-type}
 * e.g. ?docs-screenshot=text
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

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
 * Hookable class that intercepts docs-screenshot URLs and renders
 * clean pages for Playwright to screenshot.
 */
class Docs_Screenshot_Page implements Hookable {

	/** @var string Path to the plugin root */
	private static string $plugin_path = '';

	public static function set_plugin_path( string $path ): void {
		self::$plugin_path = $path;
	}

	private const PAGES = array(
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
	);

	/** @var View */
	private View $view;

	public function __construct( View $view ) {
		$this->view = $view;
	}

	public function register( Hook_Loader $loader ): void {
		$loader->action( 'template_redirect', [ $this, 'maybe_render_screenshot' ] );
	}

	public function maybe_render_screenshot(): void {
		if ( ! isset( $_GET['docs-screenshot'] ) ) {
			return;
		}

		$page = sanitize_key( $_GET['docs-screenshot'] );

		if ( 'index' === $page ) {
			$this->render_index();
			exit;
		}

		if ( ! in_array( $page, self::PAGES, true ) ) {
			wp_die( 'Unknown docs screenshot page: ' . esc_html( $page ) );
		}

		$this->render_page( $page );
		exit;
	}

	/**
	 * Render the index listing all available pages.
	 */
	private function render_index(): void {
		$this->output_head( 'Documentation Screenshots' );
		echo '<div class="doc-example">';
		echo '<h1>Available Pages</h1>';
		echo '<ul>';
		foreach ( self::PAGES as $page ) {
			$url = home_url( '?docs-screenshot=' . $page );
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( ucwords( str_replace( '-', ' ', $page ) ) ) . '</a></li>';
		}
		echo '</ul>';
		echo '</div>';
		$this->output_foot();
	}

	/**
	 * Render a single field type documentation page.
	 */
	private function render_page( string $page ): void {
		$title = ucwords( str_replace( '-', ' ', $page ) );
		$this->output_head( $title );
		$this->view->render( "fields/{$page}", array() );
		$this->output_foot();
	}

	/**
	 * Output the HTML head with inline CSS.
	 */
	private function output_head( string $title ): void {
		$css_path = self::$plugin_path . '/docs-gen/css/pc-form.css';
		$css      = file_exists( $css_path ) ? file_get_contents( $css_path ) : '';

		echo '<!DOCTYPE html>';
		echo '<html lang="en">';
		echo '<head>';
		echo '<meta charset="UTF-8">';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		echo '<title>' . esc_html( $title ) . ' - PinkCrab Form Components</title>';
		echo '<style>' . $css . '</style>'; // phpcs:ignore
		echo '</head>';
		echo '<body>';
	}

	/**
	 * Output the HTML foot.
	 */
	private function output_foot(): void {
		echo '</body>';
		echo '</html>';
	}
}

Docs_Screenshot_Page::set_plugin_path( $plugin_path );

( new App_Factory( $plugin_path ) )
	->set_base_view_path( $plugin_path . '/docs-gen/views' )
	->default_setup()
	->module( Form_Components::class )
	->registration_classes( array( Docs_Screenshot_Page::class ) )
	->boot();
