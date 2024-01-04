<?php

declare(strict_types=1);

/**
 * Application tests for the Button Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Application\Element\Input;

use LDAP\Result;
use WP_UnitTestCase;
use Symfony\Component\DomCrawler\Crawler;
use PinkCrab\Form_Components\Element\Field\Input\Button;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Tests\Application\Perique_App_Helper;
use PinkCrab\Form_Components\Registration\Form_Component_Bootstrap;
use PinkCrab\Form_Components\Element\Field\Attribute\{Disabled, Read_Only, Required, Single_Value};

/**
 * @group application
 * @group element
 * @group input
 */
class Text_Button_Application extends WP_UnitTestCase {


	// use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use Perique_App_Helper;

	protected $app;

	public function setUp(): void {
		Form_Component_Bootstrap::init();
		parent::setUp();
		$this->app = $this->pre_populated_app_provider();
	}

	public function tearDown(): void {
		$this->unset_app_instance();
		$this->app = null;
		parent::tearDown();
	}

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Button::class;
	}

	protected function render_input( $input ): ?string {
		return $this->app::view()->render(
			'PHP/single-component',
			array( 'component' => new Input_Component( $input ) ),
			false
		);
	}

	public function extract_attributes( Crawler $crawler, array $attributes ): array {
		$nodes = $crawler->extract( $attributes );
		foreach ( $nodes as $key => $attribute ) {
			$nodes[ $key ] = array_combine( $attributes, $attribute );
		}
		return $nodes;
	}




	/** @testdox The button should be rendered as an input with a type of button */
	public function test_type(): void {
		$class  = $this->get_class_under_test();
		$button = new $class( 'test' );

		$attributes = $this->extract_attributes(
			( new Crawler( $this->render_input( $button ) ) )->filter( 'input' ),
			array( 'id', 'type' )
		);

		$this->assertCount( 1, $attributes );

		$attribute = reset( $attributes );
		$this->assertEquals( 'button', $attribute['type'] );
	}

	/** @testdox The button should be rendered with the correct value */
	public function test_value(): void {
		$class  = $this->get_class_under_test();
		$button = new $class( 'test' );
		$button->text( 'Test Button' );

		$attributes = $this->extract_attributes(
			( new Crawler( $this->render_input( $button ) ) )->filter( 'input' ),
			array( 'id', 'value' )
		);

		$this->assertCount( 1, $attributes );

		$attribute = reset( $attributes );
		$this->assertEquals( 'Test Button', $attribute['value'] );
	}
}
