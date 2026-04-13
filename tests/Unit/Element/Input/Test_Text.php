<?php

declare(strict_types=1);

/**
 * Unit tests for the Text Input
 * Extends Abstract_Input
 * Extends Field
 * Implements Element
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Element\Input;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Single_Value,Autocomplete, Pattern, Datalist, Placeholder, Disabled, Read_Only,Required, Length};

/**
 * @group unit
 * @group element
 * @group input
 */
class Test_Text extends WP_UnitTestCase {

	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Field_Cases;
	use \PinkCrab\Form_Components\Tests\Unit\Element\Shared_Description_Cases;

	/** @inheritDoc */
	public function get_class_under_test(): string {
		return Text::class;
	}

    /** @testdox A Text input should return an input type of "TEXT" */
	public function test_type(): void {
		$text = new Text( 'test' );
		$this->assertEquals( 'text', $text->get_input_type() );
	}

	/** @testdox A Text input should return a type of text_input */
	public function test_element_type(): void {
		$text = new Text( 'test' );
		$this->assertEquals( 'text_input', $text->get_type() );
	}

	/** @testdox By default the text field should sanitize values as strings. */
	public function test_default_sanitizer(): void {
		$text = new Text( 'test' );
		$this->assertEquals( Sanitize::TEXT, $text->get_sanitizer() );
	}

	/** @testdox A Text field should allow a single value to be set */
	public function test_uses_single_value(): void {
		$text = new Text( 'test' );
		$this->assertTrue( usesTrait( Single_Value::class )( $text ) );
	}


	####################################################################
	######                    SHARED ATTRIBUTES                   ######
	####################################################################

	/**
     * The methods that should be defined by the traits to represent shared attributes.
     *
     * @return array<string, array<string>>
     */
    public function attribute_methods(): array {
        return array(
            'autocomplete' => array( 'autocomplete' ),
            'datalist_item' => array( 'datalist_item' ),
            'maxlength' => array( 'maxlength' ),
            'minlength' => array( 'minlength' ),
            'pattern' => array( 'pattern' ),
            'placeholder' => array( 'placeholder' ),
            'readonly' => array( 'readonly' ),
            'required' => array( 'required' ),
            'size' => array( 'size' ),
        );
    }

    /**
     * @testdox This input field has all attributes as defined by the shared traits
     * @dataProvider attribute_methods
     */
    public function test_has_attributes( string $method ): void {
        $text = new Text( 'test' );
        $this->assertTrue( method_exists( $text, $method ) );
    }

	####################################################################
	######                   AUTOCOMPLETE TRAIT                   ######
	####################################################################

	/** @testdox It should be possible to set and get the autocomplete attribute */
	public function test_autocomplete_set_and_get(): void {
		$text = new Text( 'test' );
		$text->autocomplete( 'email' );
		$this->assertTrue( $text->has_autocomplete() );
		$this->assertEquals( 'email', $text->get_autocomplete() );
	}

	/** @testdox Autocomplete should default to 'on' when called without argument */
	public function test_autocomplete_default(): void {
		$text = new Text( 'test' );
		$text->autocomplete();
		$this->assertEquals( 'on', $text->get_autocomplete() );
	}

	/** @testdox Passing null to autocomplete should remove the attribute */
	public function test_autocomplete_remove_with_null(): void {
		$text = new Text( 'test' );
		$text->autocomplete( 'on' );
		$this->assertTrue( $text->has_autocomplete() );
		$text->autocomplete( null );
		$this->assertFalse( $text->has_autocomplete() );
		$this->assertNull( $text->get_autocomplete() );
	}

	####################################################################
	######                     PATTERN TRAIT                      ######
	####################################################################

	/** @testdox It should be possible to set and get a validation pattern */
	public function test_pattern_set_and_get(): void {
		$text = new Text( 'test' );
		$text->pattern( '[A-Za-z]+' );
		$this->assertTrue( $text->has_pattern() );
		$this->assertEquals( '[A-Za-z]+', $text->get_pattern() );
	}

	/** @testdox Passing null to pattern should remove the attribute */
	public function test_pattern_remove_with_null(): void {
		$text = new Text( 'test' );
		$text->pattern( '[0-9]+' );
		$this->assertTrue( $text->has_pattern() );
		$text->pattern( null );
		$this->assertFalse( $text->has_pattern() );
		$this->assertNull( $text->get_pattern() );
	}

	####################################################################
	######                   PLACEHOLDER TRAIT                    ######
	####################################################################

	/** @testdox It should be possible to set and get a placeholder */
	public function test_placeholder_set_and_get(): void {
		$text = new Text( 'test' );
		$text->placeholder( 'Enter your name' );
		$this->assertTrue( $text->has_placeholder() );
		$this->assertEquals( 'Enter your name', $text->get_placeholder() );
	}

	/** @testdox Passing null to placeholder should remove the attribute */
	public function test_placeholder_remove_with_null(): void {
		$text = new Text( 'test' );
		$text->placeholder( 'Some text' );
		$this->assertTrue( $text->has_placeholder() );
		$text->placeholder( null );
		$this->assertFalse( $text->has_placeholder() );
		$this->assertNull( $text->get_placeholder() );
	}

	####################################################################
	######                    READ_ONLY TRAIT                     ######
	####################################################################

	/** @testdox It should be possible to set a field as readonly */
	public function test_readonly_set(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->is_readonly() );
		$text->readonly();
		$this->assertTrue( $text->is_readonly() );
	}

	/** @testdox It should be possible to unset readonly */
	public function test_readonly_unset(): void {
		$text = new Text( 'test' );
		$text->readonly( true );
		$this->assertTrue( $text->is_readonly() );
		$text->readonly( false );
		$this->assertFalse( $text->is_readonly() );
	}

	####################################################################
	######                    REQUIRED TRAIT                      ######
	####################################################################

	/** @testdox It should be possible to set a field as required */
	public function test_required_set(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->is_required() );
		$text->required();
		$this->assertTrue( $text->is_required() );
	}

	/** @testdox It should be possible to unset required */
	public function test_required_unset(): void {
		$text = new Text( 'test' );
		$text->required( true );
		$this->assertTrue( $text->is_required() );
		$text->required( false );
		$this->assertFalse( $text->is_required() );
	}

	####################################################################
	######                     LENGTH TRAIT                       ######
	####################################################################

	/** @testdox It should be possible to set and get the minlength attribute */
	public function test_minlength_set_and_get(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_minlength() );
		$this->assertNull( $text->get_minlength() );
		$text->minlength( 5 );
		$this->assertTrue( $text->has_minlength() );
		$this->assertEquals( '5', $text->get_minlength() );
	}

	/** @testdox Passing null to minlength should remove it */
	public function test_minlength_remove_with_null(): void {
		$text = new Text( 'test' );
		$text->minlength( 5 );
		$this->assertTrue( $text->has_minlength() );
		$text->minlength( null );
		$this->assertFalse( $text->has_minlength() );
	}

	/** @testdox It should be possible to set and get the maxlength attribute */
	public function test_maxlength_set_and_get(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_maxlength() );
		$this->assertNull( $text->get_maxlength() );
		$text->maxlength( 100 );
		$this->assertTrue( $text->has_maxlength() );
		$this->assertEquals( '100', $text->get_maxlength() );
	}

	/** @testdox Passing null to maxlength should remove it */
	public function test_maxlength_remove_with_null(): void {
		$text = new Text( 'test' );
		$text->maxlength( 100 );
		$this->assertTrue( $text->has_maxlength() );
		$text->maxlength( null );
		$this->assertFalse( $text->has_maxlength() );
	}

	####################################################################
	######                   INPUT_MODE TRAIT                     ######
	####################################################################

	/** @testdox It should be possible to set and get the inputmode attribute */
	public function test_inputmode_set_and_get(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_inputmode() );
		$this->assertNull( $text->get_inputmode() );
		$text->inputmode( 'numeric' );
		$this->assertTrue( $text->has_inputmode() );
		$this->assertEquals( 'numeric', $text->get_inputmode() );
	}

	/** @testdox It should be possible to clear the inputmode attribute */
	public function test_inputmode_clear(): void {
		$text = new Text( 'test' );
		$text->inputmode( 'numeric' );
		$this->assertTrue( $text->has_inputmode() );
		$text->clear_inputmode();
		$this->assertFalse( $text->has_inputmode() );
		$this->assertNull( $text->get_inputmode() );
	}

	####################################################################
	######                   SPELLCHECK TRAIT                     ######
	####################################################################

	/** @testdox It should be possible to set and get the spellcheck attribute */
	public function test_spellcheck_set_and_get(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_spellcheck() );
		$this->assertNull( $text->get_spellcheck() );
		$text->spellcheck( 'true' );
		$this->assertTrue( $text->has_spellcheck() );
		$this->assertEquals( 'true', $text->get_spellcheck() );
	}

	/** @testdox It should be possible to clear the spellcheck attribute */
	public function test_spellcheck_clear(): void {
		$text = new Text( 'test' );
		$text->spellcheck( 'true' );
		$this->assertTrue( $text->has_spellcheck() );
		$text->clear_spellcheck();
		$this->assertFalse( $text->has_spellcheck() );
		$this->assertNull( $text->get_spellcheck() );
	}

	####################################################################
	######                      SIZE TRAIT                        ######
	####################################################################

	/** @testdox It should be possible to set and get the size attribute */
	public function test_size_set_and_get(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_size() );
		$this->assertNull( $text->get_size() );
		$text->size( 20 );
		$this->assertTrue( $text->has_size() );
		$this->assertEquals( '20', $text->get_size() );
	}

	/** @testdox Passing null to size should remove it */
	public function test_size_remove_with_null(): void {
		$text = new Text( 'test' );
		$text->size( 20 );
		$this->assertTrue( $text->has_size() );
		$text->size( null );
		$this->assertFalse( $text->has_size() );
		$this->assertNull( $text->get_size() );
	}

	/** @testdox Passing a non-numeric value to size should remove it */
	public function test_size_non_numeric(): void {
		$text = new Text( 'test' );
		$text->size( 20 );
		$this->assertTrue( $text->has_size() );
		$text->size( 'abc' );
		$this->assertFalse( $text->has_size() );
	}

	####################################################################
	######                    DISABLED TRAIT                      ######
	####################################################################

	/** @testdox It should be possible to set a field as disabled */
	public function test_disabled_set(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->is_disabled() );
		$text->disabled();
		$this->assertTrue( $text->is_disabled() );
	}

	/** @testdox It should be possible to unset disabled */
	public function test_disabled_unset(): void {
		$text = new Text( 'test' );
		$text->disabled( true );
		$this->assertTrue( $text->is_disabled() );
		$text->disabled( false );
		$this->assertFalse( $text->is_disabled() );
	}

	####################################################################
	######                    DATALIST TRAIT                      ######
	####################################################################

	/** @testdox It should be possible to add datalist items */
	public function test_datalist_add_items(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_datalist_items() );
		$text->datalist_item( 'option1', 'Label 1' );
		$this->assertTrue( $text->has_datalist_items() );
		$items = $text->get_datalist_items();
		$this->assertArrayHasKey( 'option1', $items );
		$this->assertEquals( 'Label 1', $items['option1'] );
	}

	/** @testdox It should be possible to add multiple datalist items at once */
	public function test_datalist_items_array(): void {
		$text = new Text( 'test' );
		$text->datalist_items( array( 'opt1' => 'Label 1', 'opt2' => 'Label 2' ) );
		$this->assertTrue( $text->has_datalist_items() );
		$items = $text->get_datalist_items();
		$this->assertCount( 2, $items );
	}

	/** @testdox It should be possible to add datalist items as a flat list */
	public function test_datalist_items_flat_list(): void {
		$text = new Text( 'test' );
		$text->datalist_items( array( 'Option A', 'Option B', 'Option C' ) );
		$this->assertTrue( $text->has_datalist_items() );
		$items = $text->get_datalist_items();
		$this->assertCount( 3, $items );
	}

	/** @testdox It should be possible to set a custom datalist key */
	public function test_datalist_key(): void {
		$text = new Text( 'test' );
		$text->datalist_key( 'my_custom_list' );
		$this->assertEquals( 'my_custom_list', $text->get_datalist_key() );
	}

	/** @testdox The datalist key should auto-generate based on field name */
	public function test_datalist_key_auto_generated(): void {
		$text = new Text( 'test' );
		$key  = $text->get_datalist_key();
		$this->assertStringContainsString( 'test', $key );
	}

	####################################################################
	######                  NOTIFICATION TRAIT                    ######
	####################################################################

	/** @testdox It should be possible to set a notification on a field */
	public function test_notification_set(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_notification() );
		$text->notification( 'This is required' );
		$this->assertTrue( $text->has_notification() );
		$this->assertEquals( 'This is required', $text->get_notification() );
		$this->assertEquals( 'info', $text->get_notification_type() );
	}

	/** @testdox It should be possible to set an info notification */
	public function test_info_notification(): void {
		$text = new Text( 'test' );
		$text->info_notification( 'Info message' );
		$this->assertTrue( $text->has_notification() );
		$this->assertEquals( 'Info message', $text->get_notification() );
		$this->assertEquals( 'info', $text->get_notification_type() );
	}

	/** @testdox It should be possible to set a success notification */
	public function test_success_notification(): void {
		$text = new Text( 'test' );
		$text->success_notification( 'Success!' );
		$this->assertTrue( $text->has_notification() );
		$this->assertEquals( 'Success!', $text->get_notification() );
		$this->assertEquals( 'success', $text->get_notification_type() );
	}

	/** @testdox It should be possible to set a warning notification */
	public function test_warning_notification(): void {
		$text = new Text( 'test' );
		$text->warning_notification( 'Warning!' );
		$this->assertTrue( $text->has_notification() );
		$this->assertEquals( 'Warning!', $text->get_notification() );
		$this->assertEquals( 'warning', $text->get_notification_type() );
	}

	/** @testdox It should be possible to set an error notification */
	public function test_error_notification(): void {
		$text = new Text( 'test' );
		$text->error_notification( 'Error!' );
		$this->assertTrue( $text->has_notification() );
		$this->assertEquals( 'Error!', $text->get_notification() );
		$this->assertEquals( 'error', $text->get_notification_type() );
	}

	/** @testdox Changing a notification type should remove the previous notification classes */
	public function test_notification_type_change_removes_old(): void {
		$text = new Text( 'test' );
		$text->info_notification( 'Info' );
		$text->error_notification( 'Error' );
		$this->assertEquals( 'error', $text->get_notification_type() );
		$this->assertEquals( 'Error', $text->get_notification() );
	}

	/** @testdox A field with no notification should return empty string */
	public function test_no_notification_returns_empty(): void {
		$text = new Text( 'test' );
		$this->assertEquals( '', $text->get_notification() );
	}

	####################################################################
	######                     LABEL TRAIT                        ######
	####################################################################

	/** @testdox It should be possible to set and get a label */
	public function test_label_set_and_get(): void {
		$text = new Text( 'test' );
		$this->assertFalse( $text->has_label() );
		$this->assertNull( $text->get_label() );
		$text->label( 'My Label' );
		$this->assertTrue( $text->has_label() );
		$this->assertEquals( 'My Label', $text->get_label() );
	}
}
