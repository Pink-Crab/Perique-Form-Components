<?php
/**
 * Custom Style class for E2E testing style system overrides.
 * Included by field views that test custom styles.
 */

use PinkCrab\Form_Components\Style\Style;

if ( ! class_exists( 'E2E_Custom_Style' ) ) {
	class E2E_Custom_Style implements Style {
		public function form_class(): string {
			return 'custom-form';
		}
		public function element_wrapper_class(): string {
			return 'custom-wrapper custom-wrapper--%s';
		}
		public function field_class(): string {
			return 'custom-field custom-field--%s';
		}
		public function notification_template(): string {
			return 'custom-notification-%s';
		}
		public function notification_wrapper_class(): string {
			return 'custom-notification-wrap custom-notification-wrap--%s';
		}
		public function button_class(): string {
			return 'custom-button';
		}
		public function field_control_class(): string {
			return 'custom-control %s';
		}
		public function group_option_class(): string {
			return 'custom-%s-option';
		}
		public function label_class(): string {
			return 'custom-label';
		}
		public function legend_class(): string {
			return 'custom-legend';
		}
		public function description_class(): string {
			return 'custom-description';
		}
	}
}
