<?php

declare( strict_types=1 );

/**
 * The Style interface
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

namespace PinkCrab\Form_Components\Style;

interface Style {
	/**
	 * Get the form class
	 *
	 * @return string
	 */
	public function form_class(): string;

	/**
	 * Field wrapper class template
	 *
	 * expects a single %s for the field name
	 *
	 * @return string
	 */
	public function element_wrapper_class(): string;

	/**
	 * Field class template
	 *
	 * expects a single %s for the field name
	 *
	 * @return string
	 */
	public function field_class(): string;

	/**
	 * Get the notification template.
	 *
	 * expects a single %s for the notification type
	 *
	 * @return string
	 */
	public function notification_template(): string;

	/**
	 * Get the notification wrapper class.
	 *
	 * expects a single %s for the notification type
	 *
	 * @return string
	 */
	public function notification_wrapper_class(): string;

	/**
	 * Button class
	 *
	 * @return string
	 */
	public function button_class(): string;

}
