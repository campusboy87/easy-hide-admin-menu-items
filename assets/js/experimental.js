(function ($) {

    var $select = $('#ehami_separators_mode');

    if ( ! $select.length ) {
        return;
    }

    $select.on('change', function () {
        applySeparatorsPreview( $(this).val() );
    });

    /**
     * Visual preview of separators mode — inline style only, no AJAX.
     * Saved to DB when user clicks "Save Settings".
     *
     * @param {string} mode  "default", "hidden", or "highlight"
     */
    function applySeparatorsPreview( mode ) {
        var styleId = 'ehami-separators-preview';
        var $style  = $( '#' + styleId );
        var css     = '';

        if ( mode === 'hidden' ) {
            css = '#adminmenu li.wp-menu-separator { display: none !important; }';
        } else if ( mode === 'highlight' ) {
            css  = '#adminmenu li.wp-menu-separator { height: auto !important; margin: 0 !important; }';
            css += '#adminmenu li.wp-menu-separator > .separator { height: 2px; margin: 8px 12px; background: rgba(255,255,255,0.08); border: none; }';
        }

        if ( $style.length ) {
            if ( css ) {
                $style.html( css );
            } else {
                $style.remove();
            }
        } else if ( css ) {
            $( '<style>', { id: styleId, html: css } ).appendTo( 'head' );
        }
    }

})(jQuery);
