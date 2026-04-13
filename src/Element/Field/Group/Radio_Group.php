<?php

declare( strict_types=1 );

/**
 * Radio Group field element.
 *
 * Renders a group of radio buttons from an options array.
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
use PinkCrab\Form_Components\Element\Field\Attribute\{Label, Options, Notification, Disabled, Required, Description};

class Radio_Group extends Field {

	use Label, Options, Notification, Disabled, Required, Description;

	/**
	 * The selected value.
	 *
	 * @var string|null
	 */
	protected $selected = null;

	/** @inheritDoc */
	public function get_type(): string {
		return 'radio_group';
	}

	/** @inheritDoc */
	public function set_existing( $value ): self {
		if ( null !== $value ) {
			$this->selected = (string) $value;
		}
		return $this;
	}

	/**
	 * Set the selected value.
	 *
	 * @param string $selected
	 * @return static
	 */
	public function selected( string $selected ): self {
		$this->selected = $selected;
		return $this;
	}

	/**
	 * Get the selected value.
	 *
	 * @return string|null
	 */
	public function get_selected(): ?string {
		return $this->selected;
	}

	/**
	 * Check if a specific value is selected.
	 *
	 * @param string $value
	 * @return bool
	 */
	public function is_selected( string $value ): bool {
		return $this->selected === $value;
	}

	/**
	 * Check if any value is selected.
	 *
	 * @return bool
	 */
	public function has_selected(): bool {
		return null !== $this->selected;
	}
}
