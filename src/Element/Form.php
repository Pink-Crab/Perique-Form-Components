<?php

declare( strict_types=1 );

/**
 * Form element.
 *
 * Container element that renders a <form> tag wrapping child elements.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Form
 */

namespace PinkCrab\Form_Components\Element;

use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Style\Style;
use PinkCrab\Form_Components\Style\Style_Provider;
use PinkCrab\Form_Components\Element\Field_Traits\Fields;
use PinkCrab\Form_Components\Element\Field_Traits\Form_Style;
use PinkCrab\Form_Components\Element\Field_Traits\Element_Wrap;
use PinkCrab\Form_Components\Element\Field_Traits\Wrapper_Attributes;

use function PinkCrab\FunctionConstructors\GeneralFunctions\pipe;

class Form implements Element {

	use Wrapper_Attributes, Element_Wrap, Fields, Form_Style;

	/**
	 * The form name/identifier.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * The HTTP method.
	 *
	 * @var string
	 */
	protected string $method = 'POST';

	/**
	 * The form action URL.
	 *
	 * @var string
	 */
	protected string $action = '';

	/**
	 * The form encoding type.
	 *
	 * @var string|null
	 */
	protected ?string $enctype = null;

	/**
	 * @param string     $name  The form name/identifier.
	 * @param Style|null $style Optional custom style.
	 */
	public function __construct( string $name, ?Style $style = null ) {
		$this->name = esc_attr( \sanitize_title( $name ) );

		// Set the style.
		if ( null !== $style ) {
			$this->style( $style );
		} else {
			$this->set_style( Style_Provider::get_default_style() );
		}

		// Set default wrapper/form attributes.
		$this->wrapper_id( 'form-' . $this->name );
	}

	/**
	 * Get a single wrapper attribute with style classes injected for 'class'.
	 *
	 * @param string $attribute
	 * @return string|int|float|bool|null
	 */
	public function get_wrapper_attribute( string $attribute ) {
		if ( 'class' === $attribute ) {
			$existing = \array_key_exists( 'class', $this->wrapper_attributes )
				? Esc::attribute( $this->wrapper_attributes['class'] )
				: null;
			$style = esc_attr( $this->get_style()->form_class() );
			return $existing ? $style . ' ' . $existing : $style;
		}
		return \array_key_exists( $attribute, $this->wrapper_attributes )
			? Esc::attribute( $this->wrapper_attributes[ $attribute ] )
			: null;
	}

	/**
	 * Get all wrapper attributes with style classes injected.
	 *
	 * @return array<string, string|int|float|bool|null>
	 */
	public function get_wrapper_attributes(): array {
		$attributes          = $this->wrapper_attributes;
		$style_class         = esc_attr( $this->get_style()->form_class() );
		$existing            = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$attributes['class'] = $existing ? $style_class . ' ' . $existing : $style_class;
		return $attributes;
	}

	/**
	 * Static constructor.
	 *
	 * @param string $name
	 * @return static
	 */
	public static function make( string $name ): static {
		return new static( $name ); // @phpstan-ignore new.static
	}

	/**
	 * Set the HTTP method.
	 *
	 * @param string $method
	 * @return static
	 */
	public function method( string $method ): self {
		$this->method = strtoupper( $method );
		return $this;
	}

	/**
	 * Get the HTTP method.
	 *
	 * @return string
	 */
	public function get_method(): string {
		return $this->method;
	}

	/**
	 * Set the form action URL.
	 *
	 * @param string $action
	 * @return static
	 */
	public function action( string $action ): self {
		$this->action = $action;
		return $this;
	}

	/**
	 * Get the form action URL.
	 *
	 * @return string
	 */
	public function get_action(): string {
		return $this->action;
	}

	/**
	 * Set the form encoding type.
	 *
	 * @param string $enctype
	 * @return static
	 */
	public function enctype( string $enctype ): self {
		$this->enctype = $enctype;
		return $this;
	}

	/**
	 * Get the form encoding type.
	 *
	 * @return string|null
	 */
	public function get_enctype(): ?string {
		return $this->enctype;
	}

	/** @inheritDoc */
	public function get_name(): string {
		return $this->name;
	}

	/** @inheritDoc */
	public function get_type(): string {
		return 'form';
	}
}
