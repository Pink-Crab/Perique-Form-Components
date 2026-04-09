<?php

declare(strict_types=1);

/**
 * Unit tests for the Make helper class.
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Util;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Make;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Form_Components\Component\Field\Raw_HTML_Component;
use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Component\Form\Form_Component;
use PinkCrab\Form_Components\Component\Form\Fieldset_Component;
use PinkCrab\Form_Components\Component\Field\Custom_Field_Component;
use PinkCrab\Form_Components\Component\Partial\Nonce_Component;
use PinkCrab\Perique\Services\View\Component\Component;

/**
 * @group unit
 * @group util
 * @group make
 */
class Test_Make extends WP_UnitTestCase {

	/** @testdox Make::text() returns an Input_Component */
	public function test_text(): void {
		$component = Make::text( 'name' );
		$this->assertInstanceOf( Input_Component::class, $component );
	}

	/** @testdox Make::text() accepts a config callable */
	public function test_text_with_config(): void {
		$component = Make::text( 'name', fn( $f ) => $f->label( 'Name' )->required( true ) );
		$this->assertInstanceOf( Input_Component::class, $component );
	}

	/** @testdox Make::email() returns an Input_Component */
	public function test_email(): void {
		$this->assertInstanceOf( Input_Component::class, Make::email( 'email' ) );
	}

	/** @testdox Make::password() returns an Input_Component */
	public function test_password(): void {
		$this->assertInstanceOf( Input_Component::class, Make::password( 'pass' ) );
	}

	/** @testdox Make::search() returns an Input_Component */
	public function test_search(): void {
		$this->assertInstanceOf( Input_Component::class, Make::search( 'q' ) );
	}

	/** @testdox Make::tel() returns an Input_Component */
	public function test_tel(): void {
		$this->assertInstanceOf( Input_Component::class, Make::tel( 'phone' ) );
	}

	/** @testdox Make::url() returns an Input_Component */
	public function test_url(): void {
		$this->assertInstanceOf( Input_Component::class, Make::url( 'website' ) );
	}

	/** @testdox Make::number() returns an Input_Component */
	public function test_number(): void {
		$this->assertInstanceOf( Input_Component::class, Make::number( 'qty' ) );
	}

	/** @testdox Make::range() returns an Input_Component */
	public function test_range(): void {
		$this->assertInstanceOf( Input_Component::class, Make::range( 'vol' ) );
	}

	/** @testdox Make::date() returns an Input_Component */
	public function test_date(): void {
		$this->assertInstanceOf( Input_Component::class, Make::date( 'dob' ) );
	}

	/** @testdox Make::time() returns an Input_Component */
	public function test_time(): void {
		$this->assertInstanceOf( Input_Component::class, Make::time( 'start' ) );
	}

	/** @testdox Make::datetime() returns an Input_Component */
	public function test_datetime(): void {
		$this->assertInstanceOf( Input_Component::class, Make::datetime( 'event' ) );
	}

	/** @testdox Make::month() returns an Input_Component */
	public function test_month(): void {
		$this->assertInstanceOf( Input_Component::class, Make::month( 'month' ) );
	}

	/** @testdox Make::week() returns an Input_Component */
	public function test_week(): void {
		$this->assertInstanceOf( Input_Component::class, Make::week( 'week' ) );
	}

	/** @testdox Make::color() returns an Input_Component */
	public function test_color(): void {
		$this->assertInstanceOf( Input_Component::class, Make::color( 'colour' ) );
	}

	/** @testdox Make::file() returns an Input_Component */
	public function test_file(): void {
		$this->assertInstanceOf( Input_Component::class, Make::file( 'upload' ) );
	}

	/** @testdox Make::hidden() returns an Input_Component */
	public function test_hidden(): void {
		$this->assertInstanceOf( Input_Component::class, Make::hidden( 'token' ) );
	}

	/** @testdox Make::checkbox() returns an Input_Component */
	public function test_checkbox(): void {
		$this->assertInstanceOf( Input_Component::class, Make::checkbox( 'agree' ) );
	}

	/** @testdox Make::radio() returns an Input_Component */
	public function test_radio(): void {
		$this->assertInstanceOf( Input_Component::class, Make::radio( 'option' ) );
	}

	/** @testdox Make::submit() returns an Input_Component */
	public function test_submit(): void {
		$this->assertInstanceOf( Input_Component::class, Make::submit( 'go' ) );
	}

	/** @testdox Make::select() returns a Select_Component */
	public function test_select(): void {
		$component = Make::select( 'country', fn( $f ) => $f->options( [ 'gb' => 'UK' ] ) );
		$this->assertInstanceOf( Select_Component::class, $component );
	}

	/** @testdox Make::textarea() returns a Textarea_Component */
	public function test_textarea(): void {
		$this->assertInstanceOf( Textarea_Component::class, Make::textarea( 'bio' ) );
	}

	/** @testdox Make::checkbox_group() returns a Checkbox_Group_Component */
	public function test_checkbox_group(): void {
		$component = Make::checkbox_group( 'colours', fn( $f ) => $f->options( [ 'r' => 'Red' ] ) );
		$this->assertInstanceOf( Checkbox_Group_Component::class, $component );
	}

	/** @testdox Make::radio_group() returns a Radio_Group_Component */
	public function test_radio_group(): void {
		$component = Make::radio_group( 'size', fn( $f ) => $f->options( [ 's' => 'Small' ] ) );
		$this->assertInstanceOf( Radio_Group_Component::class, $component );
	}

	/** @testdox Make::button() returns a Button_Component */
	public function test_button(): void {
		$component = Make::button( 'submit', fn( $f ) => $f->type( 'submit' )->text( 'Go' ) );
		$this->assertInstanceOf( Button_Component::class, $component );
	}

	/** @testdox Make::nonce() returns a Nonce_Component */
	public function test_nonce(): void {
		$this->assertInstanceOf( Nonce_Component::class, Make::nonce( 'action' ) );
	}

	/** @testdox Make::nonce() accepts custom name */
	public function test_nonce_with_name(): void {
		$this->assertInstanceOf( Nonce_Component::class, Make::nonce( 'action', 'my_nonce' ) );
	}

	/** @testdox Make::raw_html() returns a Raw_HTML_Component */
	public function test_raw_html(): void {
		$this->assertInstanceOf( Raw_HTML_Component::class, Make::raw_html( 'intro', '<p>Hi</p>' ) );
	}

	/** @testdox Make::form() returns a Form_Component */
	public function test_form(): void {
		$component = Make::form( 'contact', fn( $f ) => $f->method( 'POST' ) );
		$this->assertInstanceOf( Form_Component::class, $component );
	}

	/** @testdox Make::fieldset() returns a Fieldset_Component */
	public function test_fieldset(): void {
		$component = Make::fieldset( 'info', fn( $f ) => $f->legend( 'Info' ) );
		$this->assertInstanceOf( Fieldset_Component::class, $component );
	}

	/** @testdox Make::custom() returns a Custom_Field_Component */
	public function test_custom(): void {
		$this->assertInstanceOf( Custom_Field_Component::class, Make::custom( 'widget' ) );
	}

	/** @testdox Make::custom() accepts a config callable */
	public function test_custom_with_config(): void {
		$component = Make::custom( 'widget', fn( $f ) => $f->label( 'My Widget' )->content( '<div>Hi</div>' ) );
		$this->assertInstanceOf( Custom_Field_Component::class, $component );
	}

	/** @testdox All Make methods return instances of Component */
	public function test_all_return_component(): void {
		$this->assertInstanceOf( Component::class, Make::text( 'a' ) );
		$this->assertInstanceOf( Component::class, Make::select( 'b' ) );
		$this->assertInstanceOf( Component::class, Make::textarea( 'c' ) );
		$this->assertInstanceOf( Component::class, Make::button( 'd' ) );
		$this->assertInstanceOf( Component::class, Make::nonce( 'e' ) );
		$this->assertInstanceOf( Component::class, Make::raw_html( 'f' ) );
		$this->assertInstanceOf( Component::class, Make::form( 'g' ) );
		$this->assertInstanceOf( Component::class, Make::fieldset( 'h' ) );
		$this->assertInstanceOf( Component::class, Make::custom( 'i' ) );
	}
}
