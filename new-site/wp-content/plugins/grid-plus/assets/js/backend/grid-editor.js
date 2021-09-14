(function($) {
    "use strict";
    if(typeof(tinymce) !== 'undefined') {
        tinymce.PluginManager.add('grid_custom_editor', function (editor, url) {
            editor.addButton('grid_custom_editor', {
                text: grid_custom_editor_var.menu_name,
                icon: 'schedule',
                type: 'menubutton',
                menu: grid_custom_editor_get_menu(editor)
            });
        });
    }
    function grid_custom_editor_get_menu(editor) {
        var menu = [],
            $i;
        for($i = 0; $i < grid_custom_editor_var.sub_menu.length; $i++) {
            var sub_menu_name = grid_custom_editor_var.sub_menu[$i];
            menu[$i] = grid_custom_editor_get_sub_menu(editor, sub_menu_name);
        }
        return menu;
    }
    function grid_custom_editor_get_sub_menu(editor, sub_menu_name) {
        return {
            text: sub_menu_name,
            icon: 'tag',
            onclick: function () {
                editor.insertContent('[grid_plus name="'+sub_menu_name+'"]');
            }
        };
    }
})(jQuery);