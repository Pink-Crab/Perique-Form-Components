<?php
/**
 * The Fieldset component template.
 *
 * @package Perique\form-fields
 *
 * @var Fieldset $fieldset
 * @var array<Component> $components
 * @var string $fieldset_attributes
 * @var string|null $legend
 * @var string $before
 * @var string $after
 */
?>
<fieldset <?php echo $fieldset_attributes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?>>
	<?php if ( $legend ) : ?>
		<legend<?php echo $legend_class ? ' class="' . esc_attr( $legend_class ) . '"' : ''; ?>><?php echo esc_html( $legend ); ?></legend>
	<?php endif; ?>
	<?php echo wp_kses_post( $before ); ?>
	<?php foreach ( $components as $component ) : ?>
		<?php $this->component( $component ); ?>
	<?php endforeach; ?>
	<?php echo wp_kses_post( $after ); ?>
</fieldset>
