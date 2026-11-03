<?php
/**
 * PSR4 compatible autoloader.
 */

namespace EHAMI;

spl_autoload_register(
	static function ( $class_name ) {
		if ( strpos( $class_name, __NAMESPACE__ . '\\' ) !== 0 ) {
			return;
		}

		$path = str_replace( [ __NAMESPACE__, '\\' ], [ __DIR__ . '/src', '/' ], $class_name );

		require_once "$path.php";
	}
);
