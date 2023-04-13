<?php

declare( strict_types=1 );

/**
 * Text Input Component
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

namespace PinkCrab\Form_Components\Component\Field;

use PinkCrab\Form_Components\Util\Arrays;
use PinkCrab\Form_Components\Util\Attributes;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\Datalist;
use PinkCrab\Form_Components\Component\Field\Abstract_Field_Component;
use PinkCrab\Form_Components\Element\Field\Input\Abstract_Input as Input;

class Input_Component extends Abstract_Field_Component {

	/** @var Input */
	protected $field;

	/** @var string */
	protected $input_type;

	/**
	 * Creates an instance of the component.
	 *
	 * @param \PinkCrab\Form_Components\Element\Field\Input\Abstract_Input $field
	 * @param array<string, string|int|float|null> $attributes
	 */
	public function __construct( Input $field, array $attributes = array() ) {
		$this->field      = $field;
		$this->input_type = $this->field->get_input_type();

		$this->set_attributes( Attributes::combine( $field->get_attributes(), $attributes, array( 'class' ) ) );

		$this->before_field = $this->field->get_before();
		$this->after_field  = $this->field->get_after();
	}

	/** @inheritDoc */
	protected function base_attributes(): array {
		$attributes = array(
			'class' => "form-control {$this->input_type}-input",
		);

		// If field uses the datalist trait and passed attribute doesnt include list.
		if ( usesTrait( Datalist::class )( $this->field )
		&& ! isset( $attributes['list'] )
		) {
			$attributes['list'] = $this->field->get_datalist_key();
		}

		return $attributes;
	}




}
