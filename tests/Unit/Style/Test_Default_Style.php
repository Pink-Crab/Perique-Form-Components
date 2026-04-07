<?php

declare(strict_types=1);

/**
 * Unit tests for Default_Style
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Style;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Style\Style;
use PinkCrab\Form_Components\Style\Default_Style;

/**
 * @group unit
 * @group style
 */
class Test_Default_Style extends WP_UnitTestCase {

	/** @testdox Default_Style should implement the Style interface */
	public function test_implements_style(): void {
		$style = new Default_Style();
		$this->assertInstanceOf( Style::class, $style );
	}

	/** @testdox form_class should return the expected value */
	public function test_form_class(): void {
		$style = new Default_Style();
		$this->assertEquals( 'pc-form', $style->form_class() );
	}

	/** @testdox element_wrapper_class should return a format string */
	public function test_element_wrapper_class(): void {
		$style = new Default_Style();
		$this->assertEquals( 'pc-form__element pc-form__element--%s', $style->element_wrapper_class() );
	}

	/** @testdox field_class should return a format string */
	public function test_field_class(): void {
		$style = new Default_Style();
		$this->assertEquals( 'pc-form__element__field pc-form__element__field--%s', $style->field_class() );
	}

	/** @testdox notification_template should return a format string */
	public function test_notification_template(): void {
		$style = new Default_Style();
		$this->assertEquals( 'notification-%s', $style->notification_template() );
	}

	/** @testdox notification_wrapper_class should return a format string */
	public function test_notification_wrapper_class(): void {
		$style = new Default_Style();
		$this->assertEquals( 'pc-form__notification pc-form__notification--%s', $style->notification_wrapper_class() );
	}

	/** @testdox button_class should return the expected value */
	public function test_button_class(): void {
		$style = new Default_Style();
		$this->assertEquals( 'pc-form__button', $style->button_class() );
	}

	/** @testdox field_control_class should return a format string */
	public function test_field_control_class(): void {
		$style = new Default_Style();
		$this->assertEquals( 'form-control %s', $style->field_control_class() );
	}

	/** @testdox group_option_class should return a format string */
	public function test_group_option_class(): void {
		$style = new Default_Style();
		$this->assertEquals( '%s__option', $style->group_option_class() );
	}

	/** @testdox label_class should return the expected value */
	public function test_label_class(): void {
		$style = new Default_Style();
		$this->assertEquals( 'pc-form__label', $style->label_class() );
	}

	/** @testdox legend_class should return the expected value */
	public function test_legend_class(): void {
		$style = new Default_Style();
		$this->assertEquals( 'pc-form__legend', $style->legend_class() );
	}
}
