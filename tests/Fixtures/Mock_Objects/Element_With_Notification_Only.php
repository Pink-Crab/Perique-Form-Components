<?php

declare(strict_types=1);

/**
 * Stub element that uses the Notification trait but NOT Form_Style.
 * Used to test the Notification_Component guard clause.
 *
 * Implements Element directly (not extending Field which has Form_Style).
 */

namespace PinkCrab\Form_Components\Tests\Fixtures\Mock_Objects;

use PinkCrab\Form_Components\Style\Style;
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Element\Field_Traits\Wrapper_Attributes;
use PinkCrab\Form_Components\Element\Field\Attribute\Notification;

class Element_With_Notification_Only implements Element {

	use Wrapper_Attributes, Notification;

	/** @var string */
	protected $name;

	/** @var array<string, mixed> */
	protected $attributes = array();

	public function __construct( string $name ) {
		$this->name = $name;
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_type(): string {
		return 'mock';
	}

	// Satisfy the Notification trait's abstract methods
	public function get_style(): Style {
		return new \PinkCrab\Form_Components\Style\Default_Style();
	}

	public function add_class( string $class_name ): Element {
		return $this;
	}

	public function get_attribute( string $attribute ) {
		return $this->attributes[ $attribute ] ?? null;
	}

	public function has_attributes(): bool {
		return ! empty( $this->attributes );
	}
}
