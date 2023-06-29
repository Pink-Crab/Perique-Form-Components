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

use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Style\{Style_Provider, Style};
use PinkCrab\Form_Components\Element\Field\Attribute\Disabled;
use function PinkCrab\FunctionConstructors\GeneralFunctions\pipe;
use PinkCrab\Form_Components\Element\Field_Traits\{Attributes,Validation,Sanitizer,Element_Wrap, Form_Style};

class Button implements Element {

	use Attributes, Element_Wrap, Form_Style, Disabled;

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
		$this->set_style( $style ?? Style_Provider::get_default_style() );

		// Set with a default wrapper id.
		$this->wrapper_id( 'form-button' . $this->name );
		$this->add_wrapper_class( pipe( sprintf( $this->get_style()->element_wrapper_class(), 'button' ), 'esc_attr' ) );

		// Set the field id and style.
		$this->add_class( esc_attr( $this->get_style()->button_class() ) );
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
