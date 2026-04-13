<?php

declare( strict_types=1 );

/**
 * Checkbox Group field element.
 *
 * Renders a group of checkboxes from an options array.
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

namespace PinkCrab\Form_Components\Element\Field\Group;

use PinkCrab\Form_Components\Element\Field;
use PinkCrab\Form_Components\Element\Field\Attribute\{Label, Options, Notification, Disabled, Description};

class Checkbox_Group extends Field {

	use Label, Options, Notification, Disabled, Description;

	/**
	 * The selected values.
	 *
	 * @var array<string>
	 */
	protected $selected = array();

	/** @inheritDoc */
	public function get_type(): string {
		return 'checkbox_group';
	}

	/** @inheritDoc */
	public function set_existing( $value ): self {
		if ( is_array( $value ) ) {
			$this->selected = array_map( 'strval', $value );
		}
		return $this;
	}

	/**
	 * Set the selected values.
	 *
	 * @param array<string> $selected
	 * @return static
	 */
	public function selected( array $selected ): self {
		$this->selected = array_map( 'strval', $selected );
		return $this;
	}

	/**
	 * Get the selected values.
	 *
	 * @return array<string>
	 */
	public function get_selected(): array {
		return $this->selected;
	}

	/**
	 * Check if a specific value is selected.
	 *
	 * @param string $value
	 * @return bool
	 */
	public function is_selected( string $value ): bool {
		return in_array( $value, $this->selected, true );
	}

	/**
	 * Check if any values are selected.
	 *
	 * @return bool
	 */
	public function has_selected(): bool {
		return ! empty( $this->selected );
	}
}
