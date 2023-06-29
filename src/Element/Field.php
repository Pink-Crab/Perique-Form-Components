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
	 * Constructs an instance of the field.
	 *
	 * @param string $name
	 * @param Style $style
	 */
	public function __construct( string $name, ?Style $style = null ) {
		$this->name = esc_attr( \sanitize_title( $name ) );
		$this->set_defaults();

		// Set the style.
		$this->set_style( $style ?? Style_Provider::get_default_style() );

		// Set with a default wrapper id.
		$this->wrapper_id( 'form-field_' . $this->name );
		$this->add_wrapper_class( pipe( sprintf( $this->get_style()->element_wrapper_class(), $this->get_type() ), 'esc_attr' ) );

		// Set the field id and style.
		$this->add_class( pipe( sprintf( $this->get_style()->field_class(), $this->get_type() ), 'esc_attr' ) );
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


}
