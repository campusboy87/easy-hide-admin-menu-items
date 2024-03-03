<?php

namespace EHAMI;

$fields = [
	[
		'label'       => __( 'User-specific settings', 'ehami' ),
		'id'          => 'ehami_save_individually',
		'name'        => 'save_individually',
		'value'       => plugin()->settings->save_individually,
		'description' => __( 'Allow other users to set their own preferences.', 'ehami' ),
		'help'        => __( 'If checked, each user with access to the admin panel can save hidden menu settings individually for their own preferences.', 'ehami' ),
	],
	[
		'label'       => __( 'Child Menu', 'ehami' ),
		'id'          => 'ehami_hide_submenu',
		'name'        => 'hide_submenu',
		'value'       => plugin()->settings->hide_submenu,
		'description' => __( 'Allow hiding child admin menus', 'ehami' ),
		'help'        => __( 'If checked, you can hide the child menu. This may be useful when there are too many child menus causing distraction.', 'ehami' ),
	],
	[
		'label'       => __( 'Uninstall plugin', 'ehami' ),
		'id'          => 'ehami_remove_all_data_on_uninstall',
		'name'        => 'remove_all_data_on_uninstall',
		'value'       => plugin()->settings->remove_all_data_on_uninstall,
		'description' => __( 'Allow the deletion of all plugin data after it is deleted', 'ehami' ),
		'help'        => __( 'If checked, all data (option and user metadata) will be deleted when the plugin is deleted.', 'ehami' ),
	],
];
?>

<div id="ehami-settings-page" class="wrap ehami-settings">
	<h1 class="wp-heading-inline ehami-settings__heading">
		<?php esc_html_e( 'Easy Hide Admin Menu Items Settings', 'ehami' ); ?>
	</h1>
	<hr class="wp-header-end">

	<form method="post">

		<div class="metabox-holder ehami-settings__container">
			<div class="stuffbox ehami-settings__main">
				<h2 class="ehami-settings__main-title">
					<?php esc_html_e( 'Main Settings', 'ehami' ); ?>
				</h2>
				<div class="inside ehami-settings__main-content">
					<table class="form-table">
						<tbody>
						<?php foreach ( $fields as $field ) { ?>
							<tr>
								<th scope="row">
									<label for="<?php echo esc_attr( $field['id'] ); ?>">
										<?php echo esc_html( $field['label'] ); ?>
									</label>
								</th>
								<td>
									<fieldset class="ehami-settings__main-fieldset">
										<legend class="screen-reader-text">
											<span><?php echo esc_html( $field['label'] ); ?></span>
										</legend>

										<div class="ehami-settings__main-field">
											<label for="<?php echo esc_attr( $field['id'] ); ?>">
												<input
														type="checkbox"
														id="<?php echo esc_attr( $field['id'] ); ?>"
														name="<?php echo esc_attr( $field['name'] ); ?>"
														value="1" <?php checked( $field['value'], '1' ); ?>
												>
												<?php echo esc_html( $field['description'] ); ?>
											</label>
										</div>

										<div class="ehami-settings__main-field-description">
											<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
										</div>
									</fieldset>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>

					<?php submit_button( esc_html__( 'Save Settings', 'ehami' ), 'primary', 'submit_ehami_settings' ); ?>
				</div>

				<div class="ehami-settings-saving">
					<span class="spinner is-active"></span>
				</div>
			</div>

			<!-- Added a block for the author's message -->
			<div id="ehami-author-info" class="stuffbox ehami-settings__author">
				<h2 class="ehami-settings__author-title"><?php esc_html_e( 'Message From the Author', 'ehami' ); ?></h2>
				<div class="inside ehami-settings__author-content">

					<p>
						<?php esc_html_e( 'Thank you for using Easy Hide Admin Menu Items! Your feedback and comments are highly valuable to us and will assist us in improving the plugin further.', 'ehami' ); ?>
					</p>
					<p>
						<span class="dashicons dashicons-star-filled"></span>
						<span class="dashicons dashicons-star-filled"></span>
						<span class="dashicons dashicons-star-filled"></span>
						<span class="dashicons dashicons-star-filled"></span>
						<span class="dashicons dashicons-star-filled"></span>
					</p>
					<p>
						<?php esc_html_e( 'For support, please contact our helpdesk. If you encounter any issues or have any questions, feel free to', 'ehami' ); ?>
						<a href="https://wordpress.org/support/plugin/easy-hide-admin-menu-items/" target="_blank"> <?php esc_html_e( 'contact us here', 'ehami' ); ?></a>.
					</p>

					<p>
						<span class="dashicons dashicons-star-filled"></span>
						<span class="dashicons dashicons-star-filled"></span>
						<span class="dashicons dashicons-star-filled"></span>
						<span class="dashicons dashicons-star-filled"></span>
						<span class="dashicons dashicons-star-filled"></span>
					</p>

					<p>
					<?php esc_html_e( 'Please consider leaving a review for our plugin. Your reviews help us improve and serve you better.', 'ehami' ); ?>
						<a href="https://wordpress.org/support/plugin/easy-hide-admin-menu-items/reviews/#new-post" target="_blank">
							<?php esc_html_e( 'Leave a review', 'ehami' ); ?>
						</a>.
					</p>

				</div>
			</div>
		</div>
	</form>
</div>
