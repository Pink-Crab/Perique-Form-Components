<?php

declare(strict_types=1);

/**
 * Step trait
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
 * @package PinkCrab\Form_Fields
 */

namespace PinkCrab\Form_Components\Element\Field\Attribute;

use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Util\Sanitize;

trait Step {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * Set step value.
	 *
	 * @param int|float|string|null $step
	 * @return static
	 */
	public function step( $step ): self {
		$this->attribute( 'step', Sanitize::number( $step ) );
		return $this;
	}

	/**
	 * Get the step value.
	 *
	 * @return ?string
	 */
	public function get_step(): ?string {
		return $this->has_step()
			? Esc::attribute( $this->get_attribute( 'step' ) )
			: null;
	}

	/**
	 * Has a defined step.
	 *
	 * @return bool
	 */
	public function has_step(): bool {
		return $this->has_attribute( 'step' );
	}

	/**
	 * Clears the step if its been set.
	 *
	 * @return static
	 */
	public function clear_step(): self {
		if ( $this->has_step() ) {
			$this->remove_attribute( 'step' );
		}
		return $this;
	}


}
