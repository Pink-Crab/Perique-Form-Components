<?php
/**
 * The Button component.
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 *
 * @var Button $field
 * @var string $type
 * @var string $text
 * @var string $before_field
 * @var string $after_field
 * @var string $field_attributes
 * @var string $wrapper_attributes
 */
?>
<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start( $wrapper_attributes, $before_field ) ); ?> 
	<button 
		type="<?php echo esc_attr( $type ); ?>" 
		name="<?php echo esc_attr( $field->get_name() ); ?>"
		<?php echo $field_attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?> 
	><?php echo wp_kses_post( $text ); ?></button>
<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End( $after_field ) ); ?>
