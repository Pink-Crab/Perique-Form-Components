<?php

declare(strict_types=1);

/**
 * Application-level regression tests for issue #25.
 *
 * Renders fields through the real Component_Compiler + PHP_Engine
 * pipeline and asserts no PHP deprecation/notice fires when before()
 * / after() are unset (i.e. the field's get_before() / get_after()
 * return null and the template must NOT pass that null to wp_kses_post()).
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Form_Components
 */

namespace PinkCrab\Form_Components\Tests\Application;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Element\Field\Group\Checkbox_Group;
use PinkCrab\Form_Components\Element\Field\Group\Radio_Group;
use PinkCrab\Form_Components\Element\Field\Select;
use PinkCrab\Form_Components\Element\Field\Textarea;
use PinkCrab\Form_Components\Element\Custom_Field;
use PinkCrab\Form_Components\Element\Button;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Component\Field\Custom_Field_Component;
use PinkCrab\Form_Components\Component\Field\Button_Component;

class Test_Field_Rendering_Adornments extends WP_UnitTestCase {

	use Perique_App_Helper;

	public function setUp(): void {
		parent::setUp();
		self::unset_app_instance();
	}

	/**
	 * Sets up a strict deprecation handler around the closure and asserts
	 * no E_DEPRECATED / E_USER_DEPRECATED fires. Captures the rendered HTML
	 * so a stale "Deprecated" string in the output also fails the test.
	 *
	 * @param callable(): \PinkCrab\Perique\Services\View\Component\Component $build_component
	 * @return void
	 */
	private function assert_no_deprecation_when_rendering( callable $build_component ): void {
		$app       = $this->pre_populated_app_provider();
		$component = $build_component();

		set_error_handler(
			function ( $errno, $errstr ) {
				if ( E_DEPRECATED === $errno || E_USER_DEPRECATED === $errno ) {
					throw new \ErrorException( "Unexpected deprecation while rendering: {$errstr}", 0, $errno );
				}
				return false;
			}
		);

		try {
			$output = $app::view()->component( $component, false );
		} finally {
			restore_error_handler();
		}

		$this->assertIsString( $output );
		$this->assertStringNotContainsString( 'Deprecated', (string) $output );
	}

	/** @testdox Issue #25: Text input with show_wrapper(false) and no before()/after() renders without a wp_kses_post(null) deprecation. */
	public function test_text_input_no_wrapper_no_adornments(): void {
		$this->assert_no_deprecation_when_rendering(
			fn() => new Input_Component( Text::make( 'username' )->show_wrapper( false ) )
		);
	}

	/** @testdox Issue #25: Text input WITH wrapper but no before()/after() also renders without deprecation. */
	public function test_text_input_with_wrapper_no_adornments(): void {
		$this->assert_no_deprecation_when_rendering(
			fn() => new Input_Component( Text::make( 'username' ) )
		);
	}

	/** @testdox Issue #25: Textarea with no before()/after() renders without deprecation. */
	public function test_textarea_no_adornments(): void {
		$this->assert_no_deprecation_when_rendering(
			fn() => new Textarea_Component( Textarea::make( 'comments' )->show_wrapper( false ) )
		);
	}

	/** @testdox Issue #25: Select with no before()/after() renders without deprecation. */
	public function test_select_no_adornments(): void {
		$this->assert_no_deprecation_when_rendering(
			fn() => new Select_Component( Select::make( 'choice' )->options( array( 'a' => 'A' ) )->show_wrapper( false ) )
		);
	}

	/** @testdox Issue #25: Checkbox group with no before()/after() renders without deprecation. */
	public function test_checkbox_group_no_adornments(): void {
		$this->assert_no_deprecation_when_rendering(
			fn() => new Checkbox_Group_Component(
				Checkbox_Group::make( 'tags' )->options( array( 'a' => 'A' ) )->show_wrapper( false )
			)
		);
	}

	/** @testdox Issue #25: Radio group with no before()/after() renders without deprecation. */
	public function test_radio_group_no_adornments(): void {
		$this->assert_no_deprecation_when_rendering(
			fn() => new Radio_Group_Component(
				Radio_Group::make( 'priority' )->options( array( 'low' => 'Low' ) )->show_wrapper( false )
			)
		);
	}

	/** @testdox Issue #25: Custom_Field with no before()/after() renders without deprecation. */
	public function test_custom_field_no_adornments(): void {
		$this->assert_no_deprecation_when_rendering(
			fn() => new Custom_Field_Component( Custom_Field::make( 'cf' )->content( '<p>x</p>' )->show_wrapper( false ) )
		);
	}

	/** @testdox Issue #25: Button with no before()/after() renders without deprecation. */
	public function test_button_no_adornments(): void {
		$this->assert_no_deprecation_when_rendering(
			fn() => new Button_Component( Button::make( 'submit' )->text( 'Go' ) )
		);
	}

	/** @testdox Issue #25: Adornments still render correctly when before() / after() ARE set (regression check on the fix). */
	public function test_adornments_render_when_set(): void {
		$app = $this->pre_populated_app_provider();
		$field = Text::make( 'username' )
			->show_wrapper( false )
			->before( '<span class="ai-marker-before">BEFORE</span>' )
			->after( '<span class="ai-marker-after">AFTER</span>' );

		$output = $app::view()->component( new Input_Component( $field ), false );

		$this->assertStringContainsString( 'ai-marker-before', (string) $output );
		$this->assertStringContainsString( 'ai-marker-after', (string) $output );
	}
}
