<?php
/**
 * PSR4 compatible autoloader.
 */

namespace EHAMI;

spl_autoload_register(
	static function ( $class ) {
		if ( strpos( $class, __NAMESPACE__ . '\\' ) !== 0 ) {
			return;
		}

		$path = str_replace( [ __NAMESPACE__, '\\' ], [ __DIR__ . '/src', '/' ], $class );

		require_once "$path.php";
	}
);
