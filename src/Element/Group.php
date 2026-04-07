<?php

declare( strict_types=1 );

/**
 * Group of elements
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
use PinkCrab\Form_Components\Style\{Style_Provider,Style};
use function PinkCrab\FunctionConstructors\GeneralFunctions\pipe;
use PinkCrab\Form_Components\Element\Field_Traits\{Element_Wrap, Fields, Form_Style};

class Group implements Element {

	use Element_Wrap, Fields, Form_Style;

	/**
	 * The name of the group
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Constructs an instance of the field.
	 *
	 * @param string $name
	 */
	public function __construct( string $name, ?Style $style = null ) {

		$this->name = esc_attr( \sanitize_title( $name ) );
		if ( null !== $style ) {
			$this->style( $style );
		} else {
			$this->set_style( Style_Provider::get_default_style() );
		}

		// Set with a default wrapper id.
		$this->wrapper_id( 'form-group_' . $this->name );
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
			$style = pipe( sprintf( $this->get_style()->element_wrapper_class(), 'group' ), 'esc_attr' );
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
		$style_class         = pipe( sprintf( $this->get_style()->element_wrapper_class(), 'group' ), 'esc_attr' );
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

	/** @inheritDoc */
	public function get_name(): string {
		return $this->name;
	}

	/** @inheritDoc */
	public function get_type(): string {
		return 'group';
	}


}
