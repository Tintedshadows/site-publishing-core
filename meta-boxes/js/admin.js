/*jslint browser: true, plusplus: true */
(function ($, window, document) {
    'use strict';
    // execute when the DOM is ready
    $(document).ready(function () {
        // js 'change' event triggered on the wporg_field form field
        $('#wporg_field').on('change', function () {
            // jQuery post method, a shorthand for $.ajax with POST
            $.post(wporg_meta_box_obj.url,                        // or ajaxurl
                   {
                       action: 'wporg_ajax_change',               // POST data, action
                       wporg_field_value: $('#wporg_field').val(), // POST data, wporg_field_value
                       wporg_page_field_value: $('#wporg_page_field').val() // POST data, wporg_field_value
                   }, function (data) {
                        // handle response data
                        if (data === 'success') {
                            // perform our success logic
                        } else if (data === 'failure') {
                            // perform our failure logic
                        } else {
                            // do nothing
                        }
                    }
            );
        });
    });
}(jQuery, window, document));
