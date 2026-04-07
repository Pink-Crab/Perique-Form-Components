<?php
/**
 * The Form component template.
 *
 * @package Perique\form-fields
 *
 * @var Form $form
 * @var array<Component> $components
 * @var string $form_attributes
 * @var string $before_form
 * @var string $after_form
 */
?>
<form <?php echo $form_attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?>>
	<?php echo wp_kses_post( $before_form ); ?>
	<?php foreach ( $components as $component ) : ?>
		<?php $this->component( $component ); ?>
	<?php endforeach; ?>
	<?php echo wp_kses_post( $after_form ); ?>
</form>
