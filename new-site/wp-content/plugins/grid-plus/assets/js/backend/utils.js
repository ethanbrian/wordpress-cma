/**
 * Created by phuongth on 12/14/2016.
 */
var GridPlusUtil = GridPlusUtil || {};
(function ($) {
    "use strict";
    GridPlusUtil = {
        showLoading: function ($text) {
            var template = wp.template('bg-processing-template');
            $('body').append(template({'ico': 'fa fa-cog fa-spin', 'text': $text}));
        },

        changeLoadingStatus: function ($ico_class, $text) {
            $('i', '.bg-processing').removeClass('fa fa-cog fa-spin').addClass($ico_class);
            $('span', '.bg-processing').text($text);
        },

        closeLoading: function ($timeout) {
            if (typeof $timeout == 'undefined' || $timeout == null) {
                $timeout = 500;
            }
            if ($timeout == 0) {
                $('.bg-processing').remove();
            } else {
                setTimeout(function () {
                    $('.bg-processing').fadeOut(function () {
                        $('.bg-processing').remove();
                    });
                }, $timeout);
            }

        },

        popupAlert: function ($ico_class, $text) {
            var template = wp.template('bg-alert-template');
            $('body').append(template({'ico': $ico_class, 'text': $text}));
            $('a.btn-close', '.bg-alert-popup').on('click', function () {
                $('.bg-alert-popup').remove();
            });
        },

        confirmDialog: function ($title, $message, yes_callback, no_callback) {
            var template = wp.template('bg-confirm-dialog');
            $('body').append(template({ico: 'fa fa-question-circle', message: $message}));
            $("#grid-confirm-dialog").dialog({
                title: $title,
                resizable: false,
                modal: true,
                buttons: {
                    "Yes": function () {
                        if (yes_callback)
                            yes_callback();
                        $(this).dialog('destroy');
                    },
                    "No": function () {
                        if (no_callback)
                            no_callback();
                        $(this).dialog('destroy');
                    }
                }
            });
        }
    }
})(jQuery);