<?php

declare( strict_types=1 );

/**
 * Abstract class for all Form Fields.
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

use PinkCrab\Form_Components\Element\Field_Traits\{Attributes,Validation,Sanitizer,Element_Wrap, Form_Style};
use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Style\{Style_Provider, Style};
use function PinkCrab\FunctionConstructors\GeneralFunctions\pipe;

abstract class Field implements Element {

	use Attributes, Sanitizer, Validation, Element_Wrap, Form_Style;

	/**
	 * The fields name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Whether to render the wrapper div around the field.
	 *
	 * @var bool
	 */
	protected $show_wrapper = true;


	/**
	 * Constructs an instance of the field.
	 *
	 * @param string $name
	 * @param Style $style
	 */
	public function __construct( string $name, ?Style $style = null ) {
		// Stored verbatim — HTML form names legitimately contain `[` `]`
		// (PHP nested-array submission) and case can be meaningful. Templates
		// already escape via esc_attr() at the output boundary.
		$this->name = $name;
		$this->set_defaults();

		// Set the style.
		if ( null !== $style ) {
			$this->style( $style );
		} else {
			$this->set_style( Style_Provider::get_default_style() );
		}

		// Auto-generated wrapper id must be a valid HTML id, so slugify here.
		$this->wrapper_id( 'form-field_' . \sanitize_title( $name ) );
	}

	/**
	 * Compute the style class for the field element.
	 *
	 * @return string
	 */
	protected function get_style_field_class(): string {
		return pipe( sprintf( $this->get_style()->field_class(), $this->get_type() ), 'esc_attr' );
	}

	/**
	 * Compute the style class for the wrapper element.
	 *
	 * @return string
	 */
	protected function get_style_wrapper_class(): string {
		return pipe( sprintf( $this->get_style()->element_wrapper_class(), $this->get_type() ), 'esc_attr' );
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
			$style    = $this->get_style_field_class();
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
		$style_class         = $this->get_style_field_class();
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
			$style = $this->get_style_wrapper_class();
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
		$style_class         = $this->get_style_wrapper_class();
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
	 * Set defaults.
	 *
	 * Runs after the constructor, but before the field is rendered.
	 *
	 * @return void
	 */
	public function set_defaults(): void {
		// Override in child class.
	}

	/**
	 * Abstract method for setting the existing value
	 *
	 * @param mixed $value
	 * @return static
	 */
	abstract public function set_existing( $value ): self;

	/**
	 * Returns the field type.
	 *
	 * @return string
	 */
	abstract public function get_type(): string;

	/**
	 * Gets the value of name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Set whether to show the wrapper div.
	 *
	 * @param bool $show
	 * @return static
	 */
	public function show_wrapper( bool $show = true ): self {
		$this->show_wrapper = $show;
		return $this;
	}

	/**
	 * Check if the field should render its wrapper.
	 *
	 * @return bool
	 */
	public function has_wrapper(): bool {
		return $this->show_wrapper;
	}

}
