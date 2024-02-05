<?php

namespace EHAMI;

class Uninstall {

	/**
	 * @var Settings $settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function init() {
		if ( $this->settings->remove_all_data_on_uninstall ) {
			$this->init_deletion_sites_options();
			$this->init_deletion_users_options();
			delete_option( 'ehami_data_install' );
		}
	}

	private function init_deletion_sites_options() {
		if ( is_multisite() ) {
			foreach ( $this->get_site_ids() as $blog_id ) {
				delete_blog_option( $blog_id, $this->settings::OPTION_NAME );
			}
		} else {
			delete_option( $this->settings::OPTION_NAME );
		}
	}

	private function init_deletion_users_options() {
		if ( is_multisite() ) {
			foreach ( $this->get_site_ids() as $blog_id ) {
				$this->delete_users_options( $blog_id );
			}
		} else {
			$this->delete_users_options( get_current_blog_id() );
		}
	}

	private function delete_users_options( $blog_id ) {
		$meta_key = $this->settings::USER_ITEMS_META_KEY;

		$user_ids = get_users(
			array(
				'blog_id'  => $blog_id,
				'fields'   => 'ID',
				'meta_key' => $meta_key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_compare' => 'EXISTS',
			)
		);

		foreach ( $user_ids as $user_id ) {
			delete_user_meta( $user_id, $meta_key );
		}
	}

	private function get_site_ids(): array {
		return get_sites( array( 'fields' => 'ids' ) );
	}
}
