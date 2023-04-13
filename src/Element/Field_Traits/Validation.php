<?php

declare( strict_types=1 );

/**
 * Trait to add Validation to a field.
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

namespace PinkCrab\Form_Components\Element\Field_Traits;

use Respect\Validation\Validator;

trait Validation {

	/**
	 * The fields validation callback
	 *
	 * @var ?Validator
	 */
	protected $validator;

	/**
	 * Sets the validator.
	 *
	 * @param ?Validator $validator
	 * @return static
	 */
	public function validator( ?Validator $validator = null ): self {
		$this->validator = $validator;
		return $this;
	}

	/**
	 * Checks if the field has a validator
	 *
	 * @return bool
	 */
	public function has_validator(): bool {
		return ! is_null( $this->validator );
	}

	/**
	 * Returns the fields validator
	 *
	 * @return ?Validator
	 */
	public function get_validator(): ?Validator {
		return $this->validator;
	}

}
