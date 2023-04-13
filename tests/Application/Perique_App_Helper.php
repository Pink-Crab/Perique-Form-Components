<?php

declare(strict_types=1);

/**
 * Helper trait for all App tests
 * Includes clearing the internal state of an existing instance.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Form_Components\Tests\Application;

use PinkCrab\Perique\Application\App;
use Dice\Dice;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\Hooks;

trait Perique_App_Helper {

	/**
	 * Resets the any existing App isn'tance with default properties.
	 *
	 * @return void
	 */
	protected static function unset_app_instance(): void {
		// Clear COMPONENT_ALIASES from hooks.
		remove_all_filters( Hooks::COMPONENT_ALIASES );

		$app = new App();
		Objects::set_property( $app, 'app_config', null );
		Objects::set_property( $app, 'container', null );
		Objects::set_property( $app, 'registration', null );
		Objects::set_property( $app, 'loader', null );
		Objects::set_property( $app, 'booted', false );
		$app = null;
	}

	/**
	 * Returns an instance of app (not booted) populated with actual
	 * service objects.
	 *
	 * No registration classes are added, di has no rules, loader is empty
	 * but there is the settings from the Fixtures/Application added so we can
	 * use template paths in the App:view() tests.
	 *
	 * Is a plain and basic instance.
	 *
	 * @return App
	 */
	protected function pre_populated_app_provider(): App {
        return ( new \PinkCrab\Perique\Application\App_Factory(TEST_FIXTURES_DIR) )
            ->with_wp_dice( true )
            ->app_config( array( 'path' => array( 'view' => TEST_FIXTURES_DIR ) ) )
            ->boot();
	}

}
