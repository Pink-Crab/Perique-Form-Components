<?php

declare( strict_types=1 );

/**
 * Trait to make use of the form styles.
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

use PinkCrab\Form_Components\Style\Style;

trait Form_Style {

	/**
	 * @var Style|null
	 */
	protected $form_style;

	/**
	 * Whether the style was explicitly set (not just defaulted).
	 *
	 * @var bool
	 */
	protected bool $explicit_style = false;

	/**
	 * Sets the style for the form.
	 *
	 * @param Style $style
	 * @return static
	 */
	protected function set_style( Style $style ) {
		$this->form_style = $style;
		return $this;
	}

	/**
	 * Sets the style fluently and marks it as explicit.
	 *
	 * @param Style $style
	 * @return static
	 */
	public function style( Style $style ): self {
		$this->form_style     = $style;
		$this->explicit_style = true;
		return $this;
	}

	/**
	 * Check if a style was explicitly set.
	 *
	 * @return bool
	 */
	public function has_explicit_style(): bool {
		return $this->explicit_style;
	}

	/**
	 * Returns the fields style.
	 *
	 * @return Style
	 * @throws \RuntimeException If style has not been set.
	 */
	public function get_style(): Style {
		if ( $this->form_style === null ) {
			throw new \RuntimeException( 'Style has not been set.' );
		}
		return $this->form_style;
	}
}
