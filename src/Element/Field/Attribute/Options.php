<?php

declare(strict_types=1);

/**
 * Adds options property with setters and getters.
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

trait Options {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * Holds the fields options.
	 *
	 * @var array<string, mixed>
	 */
	protected $options = array();


	/**
	 * Set the options.
	 *
	 * @param  array<string, mixed> $options
	 * @return static
	 */
	public function options( array $options ): self {
		$this->options = $options;
		return $this;
	}

	/**
	 * Get Options
	 *
	 * @return array<string, string>
	 */
	public function get_options(): array {
		return $this->options;
	}

}
