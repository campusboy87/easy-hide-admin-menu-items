<?php
/**
 * Class Review
 *
 * @package EHAMI
 */

namespace EHAMI;

/**
 * Class for handling review notifications.
 */
class Review {

	/**
	 * Initializes the class and hooks necessary actions.
	 */
	public function init() {

		$ehami_data_install = get_option( 'ehami_data_install', [] );

		// Check if the condition is met before adding hooks.
		if ( is_array( $ehami_data_install ) && strtotime( gmdate( 'Y-m-d' ) ) >= strtotime( $ehami_data_install['date'] ) ) {
			add_action( 'admin_head', [ $this, 'add_admin_inline_style' ] );
			add_action( 'admin_footer', [ $this, 'add_admin_footer_script' ] );
			add_action( 'admin_notices', [ $this, 'admin_notice' ] );
		}

		// Add Ajax actions.
		add_action( 'wp_ajax_leave_feedback', [ $this, 'leave_feedback_ajax_callback' ] );
		add_action( 'wp_ajax_remind', [ $this, 'remind_ajax_callback' ] );
		add_action( 'wp_ajax_already', [ $this, 'already_ajax_callback' ] );
	}

	/**
	 * Adds inline styles to the admin head.
	 */
	public function add_admin_inline_style() {
		?>
		<style>
			.ehami_review__container {
				display: grid;
				grid-template-columns: 98px 1fr;
				gap: 15px;
				margin: 10px 0;
			}

			.ehami_review__img {
				width: auto;
				max-width: 100%;
			}

			.ehami_review__later {
				margin: 10px 0 0 0;
			}

			.wp-core-ui .ehami_review__link, .ehami_review__link {
				display: inline-grid;
				grid-template-columns: 1fr auto;
				align-items: center;
				gap: 5px;
			}
		</style>
		<?php
	}

	/**
	 * Adds JavaScript script to the admin footer.
	 */
	public function add_admin_footer_script() {
		?>
		<script>
			(function ($) {

				var nonceReview = '<?php echo esc_attr( wp_create_nonce( 'ehami_nonce_review' ) ); ?>';

				// Leave feedback Ajax action
				$.leave_feedback_ajax_callback = function () {
					var data = {
						'action': 'leave_feedback',
						'name': 'leave_feedback_ajax_callback',
						'nonce': nonceReview
					};

					$.post(ajaxurl, data, function (response) {
						$("#ehami_review .notice-dismiss").trigger("click");
					});
				};

				// Remind Ajax action
				$.remind_ajax_callback = function () {
					var data = {
						'action': 'remind',
						'name': 'remind_ajax_callback',
						'nonce': nonceReview
					};

					$.post(ajaxurl, data, function (response) {
						$("#ehami_review .notice-dismiss").trigger("click");
					});
				};

				// Already Ajax action
				$.already_ajax_callback = function () {
					var data = {
						'action': 'already',
						'name': 'already_ajax_callback',
						'nonce': nonceReview
					};

					$.post(ajaxurl, data, function (response) {
						$("#ehami_review .notice-dismiss").trigger("click");
					});
				};

				// Attach click handlers to your buttons
				$('#ehami_leave_feedback').click(function (e) {
					$.leave_feedback_ajax_callback();
				});

				$('#ehami_remind').click(function (e) {
					e.preventDefault();
					$.remind_ajax_callback();
				});

				$('#ehami_already').click(function (e) {
					e.preventDefault();
					$.already_ajax_callback();
				});

			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Displays an admin notice encouraging users to leave a review.
	 */
	public function admin_notice() {
		?>

		<div id="ehami_review" class="notice notice-info is-dismissible">

			<div class="ehami_review__container">

				<div>

					<img class="ehami_review__img"
					     src="https://ps.w.org/easy-hide-admin-menu-items/assets/icon-128x128.png" alt="">

				</div>

				<div>

					<?php echo esc_html__( 'Hello!', 'ehami' ); ?>

					<br>

					<?php
					/* translators: %s is a placeholder for the plugin name */
					printf( esc_html__( 'We are very pleased that you are using the %s plugin within a few days.', 'ehami' ), '<b>Easy Hide Admin Menu Items</b>' );
					?>

					<br>

					<?php echo esc_html__( 'Please rate plugin. It will help us a lot.', 'ehami' ); ?>

					<div class="ehami_review__later">

						<a id="ehami_already" class="button button-secondary ehami_review__link" href="#">

							<?php echo esc_html__( 'Don\'t show again!', 'ehami' ); ?>

							<span class="dashicons dashicons-smiley hami_review__span"></span>

						</a>

						<a id="ehami_remind" class="button button-secondary ehami_review__link" href="#">

							<?php echo esc_html__( 'Remind me later', 'ehami' ); ?>

							<span class="dashicons dashicons-backup hami_review__span"></span>

						</a>

						<a id="ehami_leave_feedback" class="button button-primary ehami_review__link"
						   href="https://wordpress.org/support/plugin/easy-hide-admin-menu-items/reviews/#new-post"
						   target="_blank">

							<?php echo esc_html__( 'Leave feedback', 'ehami' ); ?>

							<span class="dashicons dashicons-format-status hami_review__span"></span>

						</a>

					</div>

				</div>

			</div>

		</div>

		<?php
	}

	/**
	 * Ajax callback for various actions.
	 *
	 * @param string $status The status to be updated.
	 * @param string $date_modifier The date modifier for updating the date.
	 */
	private function review_ajax_callback( string $status, string $date_modifier ) {

		check_ajax_referer( 'ehami_nonce_review', 'nonce' );

		// Get the current option value.
		$ehami_data_install = get_option( 'ehami_data_install', [] );

		// Update the values.
		$ehami_data_install['date']   = gmdate( 'Y-m-d', strtotime( $date_modifier ) );
		$ehami_data_install['status'] = $status;

		// Update the option.
		update_option( 'ehami_data_install', $ehami_data_install );

		wp_die();
	}

	/**
	 * Leave feedback Ajax callback.
	 */
	public function leave_feedback_ajax_callback() {
		$this->review_ajax_callback( 'feedback_submitted', '+5 years' );
		wp_die();
	}

	/**
	 * Remind Ajax callback.
	 */
	public function remind_ajax_callback() {
		$this->review_ajax_callback( 'remind_submitted', '+1 days' );
		wp_die();
	}

	/**
	 * Already Ajax callback.
	 */
	public function already_ajax_callback() {
		$this->review_ajax_callback( 'already_submitted', '+4 years' );
		wp_die();
	}
}
