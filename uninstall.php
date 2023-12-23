<?php

namespace EHAMI;

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once __DIR__ . '/autoload.php';

$settings  = new Settings();
$uninstall = new Uninstall( $settings );
$uninstall->init();
