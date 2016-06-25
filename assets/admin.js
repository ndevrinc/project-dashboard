/* global jQuery, wp */

(function($) {
    $(document).ready(function() {
        if( $('select#pd-harvest').length > 0 ){
            $('select#pd-harvest').on('change', function(e){
                if( $(this).val() == '1' ){
                    $('.harvest-client').fadeIn();
                }else{
                    $('.harvest-client').fadeOut();
                }
            });
        }
    });

}(jQuery));