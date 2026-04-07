<?php
/**
 * The template for rendering a notification for an input field.
 *
 * @package PinkCrab\Form_Fields
 *
 * // Expected Variables
 * @var string $wrapper_class
 * @var string $notification
 */
?>
<div class="<?php echo esc_attr( $wrapper_class ); ?>"><?php echo wp_kses_post( $notification ); ?></div>

