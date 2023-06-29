<?php

declare( strict_types=1 );

/**
 * Button Component
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

use PinkCrab\Form_Components\Style\Style;
use PinkCrab\Form_Components\Element\Button;
use PinkCrab\Form_Components\Style\Style_Provider;
use PinkCrab\Form_Components\Component\Field\Abstract_Field_Component;

class Button_Component extends Abstract_Field_Component {

	/** @var Button */
	protected $field;

	/** @var string */
	protected $type;

	/** @var string */
	protected $text;

	/** @var Style */
	protected $style;

	/**
	 * Creates an instance of the component.
	 *
	 * @param Button $field
	 * @param array<string, string|int|float|null> $attributes
	 */
	public function __construct( Button $field, array $attributes = array() ) {
		$this->field = $field;
		$this->type  = $this->field->get_type();
		$this->text  = \strtolower( $this->field->get_text() );

		$this->style = $field->get_style() ?? Style_Provider::get_default_style();

		$this->set_attributes( $attributes );

		$this->before_field = $this->field->get_before();
		$this->after_field  = $this->field->get_after();
	}
}
