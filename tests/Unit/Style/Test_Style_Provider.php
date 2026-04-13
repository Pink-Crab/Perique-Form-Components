<?php

declare(strict_types=1);

/**
 * Unit tests for Style_Provider
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Style;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Style\Style;
use PinkCrab\Form_Components\Style\Default_Style;
use PinkCrab\Form_Components\Style\Style_Provider;

/**
 * @group unit
 * @group style
 */
class Test_Style_Provider extends WP_UnitTestCase {

	public function tear_down(): void {
		parent::tear_down();
		// Reset to default style after each test
		Style_Provider::set_default_style( Default_Style::class );
	}

	/** @testdox It should be possible to get the default style */
	public function test_get_default_style(): void {
		$style = Style_Provider::get_default_style();
		$this->assertInstanceOf( Style::class, $style );
		$this->assertInstanceOf( Default_Style::class, $style );
	}

	/** @testdox It should be possible to set a custom default style */
	public function test_set_default_style(): void {
		$custom_style = new class implements Style {
			public function form_class(): string { return 'custom-form'; }
			public function element_wrapper_class(): string { return 'custom-wrapper-%s'; }
			public function field_class(): string { return 'custom-field-%s'; }
			public function notification_template(): string { return 'custom-notification-%s'; }
			public function notification_wrapper_class(): string { return 'custom-notification-wrapper-%s'; }
			public function button_class(): string { return 'custom-button'; }
			public function field_control_class(): string { return 'custom-control %s'; }
			public function group_option_class(): string { return '%s__custom-option'; }
			public function label_class(): string { return 'custom-label'; }
			public function legend_class(): string { return 'custom-legend'; }
			public function description_class(): string { return 'custom-description'; }
		};

		Style_Provider::set_default_style( get_class( $custom_style ) );
		$style = Style_Provider::get_default_style();
		$this->assertEquals( 'custom-form', $style->form_class() );
	}

	/** @testdox Setting a non-Style class should throw InvalidArgumentException */
	public function test_set_invalid_style_throws(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Defined style must implement Style interface' );
		Style_Provider::set_default_style( \stdClass::class );
	}
}
