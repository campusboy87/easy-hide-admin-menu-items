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
		'video'       => 'child-menu',
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

$current_separators_mode = plugin()->settings->separators_mode;
$separators_options      = [
	'default'   => __( 'No changes (default)', 'ehami' ),
	'hidden'    => __( 'Hide all separators', 'ehami' ),
	'highlight' => __( 'Highlight separators', 'ehami' ),
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
											<?php if ( ! empty( $field['video'] ) ) : ?>
												<span class="ehami-video-hint" data-video="<?php echo esc_attr( $field['video'] ); ?>">
													<span class="dashicons dashicons-video-alt3"></span>
												</span>
											<?php endif; ?>
										</div>

										<div class="ehami-settings__main-field-description">
											<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
										</div>
									</fieldset>
								</td>
							</tr>
						<?php } ?>

					<!-- Experimental: Menu Sorting -->
					<tr class="ehami-settings__separator-row">
						<td colspan="2"><hr></td>
					</tr>
					<tr>
						<th scope="row">
							<label for="ehami_enable_menu_sorting">
								<?php esc_html_e( 'Menu Sorting', 'ehami' ); ?>
							</label>
						</th>
						<td>
							<fieldset class="ehami-settings__main-fieldset">
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Menu Sorting', 'ehami' ); ?></span>
								</legend>

								<div class="ehami-settings__main-field">
									<label for="ehami_enable_menu_sorting">
										<input
												type="checkbox"
												id="ehami_enable_menu_sorting"
												name="enable_menu_sorting"
												value="1" <?php checked( plugin()->settings->enable_menu_sorting, true ); ?>
										>
										<?php esc_html_e( 'Enable drag-and-drop menu reordering', 'ehami' ); ?>
									</label>
									<span class="ehami-video-hint" data-video="menu-sorting">
										<span class="dashicons dashicons-video-alt3"></span>
									</span>
								</div>

								<div class="ehami-settings__main-field-description">
									<p class="description">
										<?php esc_html_e( 'Drag the handle icon on the right side of menu items to reorder them.', 'ehami' ); ?>
									</p>
									<p class="description">
										<strong><?php esc_html_e( 'Note:', 'ehami' ); ?></strong>
										<?php esc_html_e( 'your custom order is preserved even when this option is disabled. Use "Reset menu order" to restore the default order.', 'ehami' ); ?>
									</p>
									<p class="ehami-settings__experimental-badge">
										<span class="ehami-badge ehami-badge--experimental"><?php esc_html_e( 'Experimental', 'ehami' ); ?></span>
										<a href="#" id="ehami-reset-menu-order" class="ehami-reset-link">
											<span class="dashicons dashicons-image-rotate"></span>
											<?php esc_html_e( 'Reset menu order', 'ehami' ); ?>
										</a>
									</p>
								</div>
							</fieldset>
						</td>
					</tr>

					<!-- Experimental: Sidebar Width -->
					<tr>
						<th scope="row">
							<label for="ehami_enable_sidebar_resize">
								<?php esc_html_e( 'Sidebar Width', 'ehami' ); ?>
							</label>
						</th>
						<td>
							<fieldset class="ehami-settings__main-fieldset">
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Sidebar Width', 'ehami' ); ?></span>
								</legend>

								<div class="ehami-settings__main-field">
									<label for="ehami_enable_sidebar_resize">
										<input
												type="checkbox"
												id="ehami_enable_sidebar_resize"
												name="enable_sidebar_resize"
												value="1" <?php checked( plugin()->settings->enable_sidebar_resize, true ); ?>
										>
										<?php esc_html_e( 'Enable sidebar width resize', 'ehami' ); ?>
									</label>
									<span class="ehami-video-hint" data-video="sidebar-width">
										<span class="dashicons dashicons-video-alt3"></span>
									</span>
								</div>

								<div class="ehami-settings__main-field-description">
									<p class="description">
										<?php esc_html_e( 'Drag the right edge of the sidebar to adjust the menu width.', 'ehami' ); ?>
									</p>
									<p class="ehami-settings__experimental-badge">
										<span class="ehami-badge ehami-badge--experimental"><?php esc_html_e( 'Experimental', 'ehami' ); ?></span>
										<a href="#" id="ehami-reset-sidebar-width" class="ehami-reset-link">
											<span class="dashicons dashicons-image-rotate"></span>
											<?php esc_html_e( 'Reset sidebar width', 'ehami' ); ?>
										</a>
									</p>
								</div>
							</fieldset>
						</td>
					</tr>

					<!-- Experimental: Separators -->
					<tr>
						<th scope="row">
							<label for="ehami_separators_mode">
								<?php esc_html_e( 'Menu Separators', 'ehami' ); ?>
							</label>
						</th>
						<td>
							<fieldset class="ehami-settings__main-fieldset">
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Menu Separators', 'ehami' ); ?></span>
								</legend>

								<div class="ehami-settings__main-field">
									<select id="ehami_separators_mode" name="separators_mode">
										<?php foreach ( $separators_options as $value => $label ) : ?>
											<option
												value="<?php echo esc_attr( $value ); ?>"
												<?php selected( $current_separators_mode, $value ); ?>
											>
												<?php echo esc_html( $label ); ?>
											</option>
										<?php endforeach; ?>
									</select>
									<span class="ehami-video-hint" data-video="separators">
										<span class="dashicons dashicons-video-alt3"></span>
									</span>
								</div>

								<div class="ehami-settings__main-field-description">
									<p class="description">
										<?php esc_html_e( 'Control the visibility of separator lines between menu groups in the admin sidebar.', 'ehami' ); ?>
									</p>
									<p class="ehami-settings__experimental-badge">
										<span class="ehami-badge ehami-badge--experimental"><?php esc_html_e( 'Experimental', 'ehami' ); ?></span>
									</p>
								</div>
							</fieldset>
						</td>
					</tr>
						</tbody>
					</table>

					<div class="ehami-save-row">
						<?php submit_button( esc_html__( 'Save Settings', 'ehami' ), 'primary', 'submit_ehami_settings', false ); ?>
						<span class="ehami-inline-notice" aria-live="polite"></span>
					</div>
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
