<?php
/**
 * The Base component template for All Input Fields
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Abstract_Field $field
 * @string $before_field
 * @string $after_field
 * @string $field_attributes
 * @string $wrapper_attributes
 */

use function PinkCrab\FunctionConstructors\Objects\usesTrait;
?>
<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start( $wrapper_attributes, $before_field ) ); ?> 
	<?php if ( $field->has_label() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Label_Component( $field->get_label(), $field->get_name() ) ); ?>
	<?php endif; ?>
	
	<input 
		type="<?php echo esc_attr( $input_type ); ?>" 
		name="<?php echo esc_attr( $field->get_name() ); ?>"
		<?php echo $field_attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?> 
		<?php echo $field->has_value() ? 'value="' . esc_attr( $field->get_value() ) . '"' : ''; ?>
	/>

	<?php if ( usesTrait( PinkCrab\Form_Components\Element\Field\Attribute\Datalist::class )( $field ) && $field->has_datalist_items() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Datalist_Component( $field->get_datalist_key(), $field->get_datalist_items() ) ); ?> 
	<?php endif; ?>

	<?php
	if ( 1 == 3 && usesTrait( PinkCrab\Form_Components\Element\Field\Attribute\Notification::class( $field ) ) && $field->has_notification() ) {
		$this->component( new PinkCrab\Form_Components\Component\Field\Notification_Component( $field ) ); }
	?>
		

		
<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End( $after_field ) ); ?>
