<?php
/**
 * The Button component template.
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Button $field              The button element being rendered.
 * @var string $type               The button type attribute (submit, button, reset).
 * @var string $text               The button label text.
 * @var string $before_field       HTML rendered before the field's inner content.
 * @var string $after_field        HTML rendered after the field's inner content.
 * @var string $field_attributes   Pre-escaped HTML attributes for the button element.
 * @var string $wrapper_attributes Pre-escaped HTML attributes for the wrapper element.
 * @var bool   $show_wrapper       Whether to render the surrounding wrapper div.
 */
?>
<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start( $wrapper_attributes ) ); ?>
<?php endif; ?>
	<?php if ( null !== $before_field && '' !== $before_field ) : ?>
		<?php echo wp_kses_post( $before_field ); ?>
	<?php endif; ?>
	<button
		type="<?php echo esc_attr( $type ); ?>"
		name="<?php echo esc_attr( $field->get_name() ); ?>"
		<?php echo $field_attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?>
	><?php echo wp_kses_post( $text ); ?></button>
	<?php if ( null !== $after_field && '' !== $after_field ) : ?>
		<?php echo wp_kses_post( $after_field ); ?>
	<?php endif; ?>
<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End() ); ?>
<?php endif; ?>
