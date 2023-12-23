<?php
namespace EHAMI;

$items = plugin()->settings->items;
?>

<form class="switch__container">
	<input id="switch-flat" class="switch" type="checkbox"<?php checked( plugin()->settings->status ); ?>>
	<label for="switch-flat" title="<?php esc_attr_e( 'Hide or show selected menu items', 'ehami' ); ?>"></label>
	<div class="switch__content">
		<?php
		if ( $items ) {
			foreach ( $items as $id => $text ) {
				printf(
					'<p data-id="%s">
						<span class="text">%s</span>
						<span class="dashicons dashicons-no ehami-restore-li" title="%s"></span>
					</p>',
					esc_attr( $id ),
					esc_html( $text ),
					esc_attr__( 'Remove from the list', 'ehami' )
				);
			}
		} else {
			printf( '<p class="no-items">%s</p>', esc_html__( 'No hidden menu items', 'ehami' ) );
		}
		?>
	</div>
</form>
