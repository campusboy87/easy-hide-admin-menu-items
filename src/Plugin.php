<?php

namespace EHAMI;

class Plugin {

	/**
	 * @var string
	 */
	public $path;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $basename;

	/**
	 * @var array{name: string, version: string}
	 */
	public $info;

	/**
	 * @var Settings
	 */
	public $settings;

	public function __construct( string $main_file_path ) {
		$this->path     = dirname( $main_file_path );
		$this->url      = plugins_url( '', $main_file_path );
		$this->basename = plugin_basename( $main_file_path );
		$this->info     = get_file_data( $main_file_path, [
			'name'    => 'Plugin Name',
			'version' => 'Version',
		] );
	}

	public function init() {
		load_plugin_textdomain( 'ehami', false, basename( $this->path ) . '/languages/' );

		$this->settings = new Settings();

		$ajax = new Ajax();
		$ajax->init();

		$user_interface = new Switcher();
		$user_interface->init();

		$assets = new Assets();
		$assets->init();

		$settings_page = new Settings_Page();
		$settings_page->init();
	}

}
