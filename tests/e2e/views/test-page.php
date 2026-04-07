<?php
/**
 * E2E Test Landing Page
 *
 * Links to each field type test tab.
 *
 * @var array<string> $tabs
 */
?>
<div class="wrap" id="form-component-test-page">
	<h1>Form Component E2E Tests</h1>
	<ul>
		<?php foreach ( $tabs as $tab ) : ?>
			<li>
				<a href="<?php echo esc_url( admin_url( "admin.php?page=form-component-tests&tab={$tab}" ) ); ?>">
					<?php echo esc_html( ucwords( str_replace( '-', ' ', $tab ) ) ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
