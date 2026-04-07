<?php

declare( strict_types=1 );

/**
 * Button element.
 *
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
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Style\{Style_Provider, Style};
use PinkCrab\Form_Components\Element\Field\Attribute\Disabled;
use function PinkCrab\FunctionConstructors\GeneralFunctions\pipe;
use PinkCrab\Form_Components\Element\Field_Traits\{Attributes,Validation,Sanitizer,Element_Wrap, Form_Style};

class Button implements Element {

	use Attributes, Element_Wrap, Form_Style, Disabled;

	/**
	 * The button name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The button type
	 *
	 * @var string
	 */
	protected $type = 'button';

	/**
	 * The button value
	 *
	 * @var string
	 */
	protected $text = '';


	/**
	 * Constructs an instance of the field.
	 *
	 * @param string $name
	 * @param Style $style
	 */
	public function __construct( string $name, ?Style $style = null ) {
		$this->name = esc_attr( \sanitize_title( $name ) );

		// Set the style.
		if ( null !== $style ) {
			$this->style( $style );
		} else {
			$this->set_style( Style_Provider::get_default_style() );
		}

		// Set with a default wrapper id.
		$this->wrapper_id( 'form-button' . $this->name );
	}

	/**
	 * Get a single attribute with style classes injected for 'class'.
	 *
	 * @param string $attribute
	 * @return string|int|float|bool|null
	 */
	public function get_attribute( string $attribute ) {
		if ( 'class' === $attribute ) {
			$existing = $this->attributes['class'] ?? null;
			$style    = esc_attr( $this->get_style()->button_class() );
			return $existing ? $style . ' ' . $existing : $style;
		}
		return \array_key_exists( $attribute, $this->attributes )
			? $this->attributes[ $attribute ]
			: null;
	}

	/**
	 * Get all attributes with style classes injected.
	 *
	 * @return array<string, string|int|float|bool|null>
	 */
	public function get_attributes(): array {
		$attributes          = $this->attributes;
		$style_class         = esc_attr( $this->get_style()->button_class() );
		$existing            = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$attributes['class'] = $existing ? $style_class . ' ' . $existing : $style_class;
		return $attributes;
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
			$style = pipe( sprintf( $this->get_style()->element_wrapper_class(), 'button' ), 'esc_attr' );
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
		$style_class         = pipe( sprintf( $this->get_style()->element_wrapper_class(), 'button' ), 'esc_attr' );
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
	 * Sets the button type
	 *
	 * @param string $type
	 * @return static
	 */
	public function type( string $type ): self {
		$this->type = $type;
		return $this;
	}

	/**
	 * Get the button type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Sets the button text
	 *
	 * @param string $text
	 * @return static
	 */
	public function text( string $text ): self {
		$this->text = $text;
		return $this;
	}

	/**
	 * Get the button text.
	 *
	 * @return string
	 */
	public function get_text(): string {
		return $this->text;
	}

	/**
	 * Gets the value of name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

}
