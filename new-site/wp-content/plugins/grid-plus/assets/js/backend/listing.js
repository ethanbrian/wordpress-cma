/**
 * Created by phuongth on 12/14/2016.
 */
var GridPlus = GridPlus || {};
(function ($) {
    "use strict";
    GridPlus = {
        init: function () {
            $('.grid_plus-wrap').css('opacity',1);
            GridPlus.is_search = 0;
            GridPlus.registerEvent();

            var clipboard = new Clipboard('.copy-clipboard');
            clipboard.on('success', function (e) {
                if($(e.trigger).find('i.fa-check').length > 0) {
                    $(e.trigger).find('i.fa-check').remove();
                }
                $(e.trigger).append('<i class="fa fa-check active"> Copied!</i>');
                e.clearSelection();
                setTimeout(function () {
                    $('i.active', e.trigger).fadeOut(function () {
                        $(this).remove();
                    });
                }, 2000);
            });
        },
        registerEvent: function () {
            $('a.search-layout').off('click').on('click', function () {
                var $input = $('input', $(this).parent());
                if ($input.hasClass('active')) {
                    $input.removeClass('active');
                } else {
                    $input.val('');
                    $input.addClass('active');
                    setTimeout(function () {
                        $input.focus();
                        $input.attr("placeholder", "Type grid name and enter to search");
                    }, 300);
                }
                $($input).on('keypress', function (event) {
                    var keycode = (event.keyCode ? event.keyCode : event.which);
                    if (keycode == '13') {
                        var $ajax_url = $(this).parent().parent().attr('data-ajax-url'),
                            $grid_name = $(this).val();
                        if (GridPlus.is_search == 0) {
                            GridPlus.is_search = 1;
                            GridPlus.processSearch('Searching grid ......', $ajax_url, $grid_name);
                        }

                    }
                });

            });

            $('a.export-grid').off('click').on('click',function(){
                GridPlus.processExportGrid(this);
            });

            $('a.import-grid').off('click').on('click',function(){
                $('div.form-import').toggle();
            });

            $('a.close-import').off('click').on('click',function(){
                $('div.form-import').hide();
            });

            $('.refresh-layout').off('click').on('click', function () {
                if (GridPlus.is_search == 0) {
                    GridPlus.is_search = 1;
                    var $ajax_url = $(this).parent().attr('data-ajax-url');
                    GridPlus.processSearch('Refresh grid ......', $ajax_url, '');
                }
            });
            $('.remove-all-data').off('click').on('click', function (e) {
                e.preventDefault();
                var el = $(this);
                GridPlusUtil.confirmDialog('Confirm', 'Confirm REMOVE all data of the Grid Plus plugin? </br>NOTE: You CAN NOT UNDO this action!', function () {
                    GridPlus.processRemoveAllData(el);
                });
            });
            GridPlus.registerGridAction();
        },

        registerGridAction: function () {
            /** clone grid event **/
            $('a.clone-grid').off('click').on('click', function () {
                GridPlus.processCloneLayout(this);
            });

            /** delete grid event **/
            $('a.delete-grid').off('click').on('click', function () {
                var el = $(this);
                GridPlusUtil.confirmDialog('Confirm', 'Confirm delete layout?', function () {
                    GridPlus.processDeleteLayout(el);
                });
            });
        },

        processRemoveAllData: function (el) {
            var $ajax_url = $(el).parent().attr('data-ajax-url');
            GridPlusUtil.showLoading('Removing all data from Grid Plus...');
            $.ajax({
                url: $ajax_url,
                type: 'POST',
                data: ({
                    action: 'grid_plus_remove_all'
                }),
                success: function (data) {
                    GridPlusUtil.changeLoadingStatus('fa fa-check-square-o', 'All data has beeb removed!');
                    GridPlusUtil.closeLoading(500);
                    data = JSON.parse(data);
                    GridPlus.processRebindListGrid(data);
                },
                error: function () {
                    GridPlusUtil.changeLoadingStatus('fa fa-exclamation-triangle', 'Have error when remove all data');
                    GridPlusUtil.closeLoading();
                }
            });
        },

        processDeleteLayout: function (el) {
            var $grid_id = $(el).parent().attr('data-id'),
                $ajax_url = $(el).parent().attr('data-ajax-url');
            GridPlusUtil.showLoading('Deleting layout information');
            $.ajax({
                url: $ajax_url,
                type: 'POST',
                data: ({
                    action: 'grid_plus_delete',
                    grid_id: $grid_id
                }),
                success: function (data) {
                    GridPlusUtil.changeLoadingStatus('fa fa-check-square-o', 'Information has been deleted');
                    GridPlusUtil.closeLoading(500);
                    data = JSON.parse(data);
                    GridPlus.processRebindListGrid(data);
                },
                error: function () {
                    GridPlusUtil.changeLoadingStatus('fa fa-exclamation-triangle', 'Have error when delete information');
                    GridPlusUtil.closeLoading();
                }
            });
        },

        processRebindListGrid: function ($items) {
            var template = wp.template('list-grid-template');
            $('tr', '#table_list_grid tbody').empty();
            if (typeof $items != 'undefined' && $items.length > 0) {
                $('tbody', '#table_list_grid').append(template($items));
            }
            GridPlus.registerGridAction();
        },

        processSearch: function ($loading_text, $ajax_url, $grid_name) {
            GridPlusUtil.showLoading($loading_text);
            $.ajax({
                url: $ajax_url,
                type: 'POST',
                data: ({
                    action: 'grid_plus_get_list',
                    grid_name: $grid_name
                }),
                success: function (data) {
                    GridPlusUtil.changeLoadingStatus('fa fa-check-square-o', 'Binding grid');
                    GridPlusUtil.closeLoading(500);
                    data = JSON.parse(data);
                    GridPlus.processRebindListGrid(data);
                    GridPlus.is_search = 0;
                },
                error: function () {
                    GridPlusUtil.changeLoadingStatus('fa fa-exclamation-triangle', 'Have error when delete information');
                    GridPlusUtil.closeLoading();
                    GridPlus.is_search = 0;
                }
            });
        },

        processExportGrid: function(el){
            var $ajax_url = $(el).parent().attr('data-ajax-url');
            GridPlusUtil.showLoading('Processing export grid data');
            $.ajax({
                url: $ajax_url,
                type: 'POST',
                data: ({
                    action: 'grid_plus_export'
                }),
                success: function (data) {
                    var $blob_data = [];
                    $blob_data.push(data);
                    var file = new File($blob_data, "grid-plus.json", {type: "text/plain;charset=utf-8"});
                    saveAs(file);
                    GridPlusUtil.closeLoading(500);
                },
                error: function () {
                    GridPlusUtil.changeLoadingStatus('fa fa-exclamation-triangle', 'Have error when exort grid');
                    GridPlusUtil.closeLoading();
                }
            });
        },

        processCloneLayout: function (el) {
            var $grid_id = $(el).parent().attr('data-id'),
                $ajax_utl = $(el).parent().attr('data-ajax-url');
            var $layout_name = prompt("Please enter layout name", "");
            if ($layout_name != '' && $layout_name != null) {
                var $clone_url = $(el).attr('data-clone-url') + $layout_name;
                window.location.href = $clone_url;
                return true;
            }else{
                el.preventDefault();
                return false;
            }
        }
    }
    $(document).ready(function () {
        GridPlus.init();
    });
})(jQuery);