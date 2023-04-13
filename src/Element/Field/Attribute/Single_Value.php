<?php

declare( strict_types=1 );

/**
 * The Value attribute added to inputs.
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

namespace PinkCrab\Form_Components\Element\Field\Attribute;

trait Single_Value {

	/**
	 * Holds the value of the field.
	 *
	 * @var string|int|float|null|bool
	 */
	protected $value;

	/**
	 * Sets the value of the field.
	 *
	 * @param string|int|float|null|bool $value
	 * @return static
	 */
	public function value( $value ): self {
		// If the field has a sanitizer, sanitize the value.
		$this->value = $value;
		return $this;
	}

	/**
	 * Returns the value of the field.
	 *
	 * @return string|int|float|null|bool
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Checks if the field has a value.
	 *
	 * @return bool
	 */
	public function has_value(): bool {
		return ! is_null( $this->value );
	}
}
