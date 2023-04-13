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

use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Style\{Style_Provider,Style};
use function PinkCrab\FunctionConstructors\GeneralFunctions\pipe;
use PinkCrab\Form_Components\Element\Field_Traits\{Element_Wrap, Fields};

class Group implements Element {

	use Element_Wrap, Fields;

	/**
	 * The name of the group
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The from style.
	 *
	 * @var Style
	 */
	protected $form_style;


	/**
	 * Constructs an instance of the field.
	 *
	 * @param string $name
	 */
	public function __construct( string $name, ?Style $style = null ) {

		$this->name       = esc_attr( \sanitize_title( $name ) );
		$this->form_style = $style ?? Style_Provider::get_default_style();

		// Set with a default wrapper id.
		$this->wrapper_id( 'form-group_' . $this->name );
		$this->add_wrapper_class( pipe( sprintf( $this->form_style->element_wrapper_class(), 'group' ), 'esc_attr' ) );
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
