<?php
/**
 * The Select component template
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Select $field
 * @var string $before_field
 * @var string $after_field
 * @var string $field_attributes
 * @var string $wrapper_attributes
 * @var bool $show_wrapper
 */

use function PinkCrab\FunctionConstructors\Objects\usesTrait;
?>
<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start( $wrapper_attributes, $before_field ) ); ?>
<?php endif; ?>
	<?php if ( $field->has_label() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Label_Component( $field->get_label(), $field->get_name(), $field->get_style()->label_class() ) ); ?>
	<?php endif; ?>

	<select
		name="<?php echo esc_attr( $field->get_name() ); ?><?php echo $field->is_multiple() ? '[]' : ''; ?>"
		<?php echo $field_attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?>
	>
		<?php foreach ( $field->get_options() as $value => $label ) : ?>
			<option
				value="<?php echo esc_attr( (string) $value ); ?>"
				<?php echo $field->is_selected( (string) $value ) ? 'selected' : ''; ?>
			><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>

		<?php if ( $field->has_optgroups() ) : ?>
			<?php foreach ( $field->get_optgroups() as $group_label => $group_options ) : ?>
				<optgroup label="<?php echo esc_attr( $group_label ); ?>">
					<?php foreach ( $group_options as $value => $label ) : ?>
						<option
							value="<?php echo esc_attr( (string) $value ); ?>"
							<?php echo $field->is_selected( (string) $value ) ? 'selected' : ''; ?>
						><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>

	<?php if ( usesTrait( PinkCrab\Form_Components\Element\Field\Attribute\Notification::class )( $field ) && $field->has_notification() ) : ?>
		<?php $this->component( new PinkCrab\Form_Components\Component\Field\Notification_Component( $field ) ); ?>
	<?php endif; ?>

<?php if ( $show_wrapper ) : ?>
	<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End( $after_field ) ); ?>
<?php endif; ?>
