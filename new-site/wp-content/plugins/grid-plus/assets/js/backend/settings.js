/**
 * Created by phuongth on 12/14/2016.
 */
var GridPlusSetting = GridPlusSetting || {};
(function ($) {
    "use strict";
    GridPlusSetting = {
        vars: {
            grid_stack: $('.grid-stack'),
            custom_content_numb: 0
        },
        init: function () {
            $('.grid-plus-wrap').css('opacity', 1);
            GridPlusSetting.grid = null;
            GridPlusSetting.is_search = 0;
            GridPlusSetting.options = {
                cellHeight: 10,
                verticalMargin: 20,
                height: 0,
                animate: true,
                acceptWidgets: true,
                removable: true,
                width: 12,
                resizable: {
                    handles: 'e, se, s, sw, w'
                }
            };
            GridPlusSetting.select_categories = null;

            GridPlusSetting.initControl();
            GridPlusSetting.registerEvent();
            GridPlusSetting.animation();
            GridPlusSetting.initDepend();
            GridPlusSetting.initSaveCtrS();
            GridPlusSetting.registerGallery();

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

            //register color picker
            //$('.colorpicker-element').colorpicker();
            $('.colorpicker').wpColorPicker();

            //register ace editor
            GridPlusSetting.layout_custom_css = ace.edit("layout_custom_css");
            GridPlusSetting.layout_custom_css.getSession().setMode("ace/mode/css");
            GridPlusSetting.layout_custom_css.setAutoScrollEditorIntoView(true);

            if (typeof grid_script_data != 'undefined' && grid_script_data.grid_id != '') {
                GridPlusSetting.processEditLayout();
            }
            //GridPlusSetting.initEditorChange();
            GridPlusSetting.execCustomContent();

        },

        initControl: function () {
            GridPlusSetting.layout_id = $('#layout_id');
            GridPlusSetting.layout_shortcode = $('#layout_shortcode');
            GridPlusSetting.layout_name = $('#layout_name');
            GridPlusSetting.layout_type = $('#layout_type');
            GridPlusSetting.layout_col = $('#layout_col');
            GridPlusSetting.layout_gutter = $('#layout_gutter');
            GridPlusSetting.layout_item_per_page = $('#layout_item_per_page');
            GridPlusSetting.layout_total_items = $('#layout_total_items');
            GridPlusSetting.layout_fix_item_height = $('#layout_fix_item_height');
            GridPlusSetting.layout_crop_image = $('#layout_crop_image');
            GridPlusSetting.layout_disable_link = $('#layout_disable_link');
            GridPlusSetting.layout_custom_content_enable = $('#layout_custom_content_enable');
            GridPlusSetting.layout_custom_content = $('#layout_custom_content');

            GridPlusSetting.layout_carousel_rtl = $('#layout_carousel_rtl');
            GridPlusSetting.layout_carousel_total_items = $('#layout_carousel_total_items');
            GridPlusSetting.layout_carousel_desktop_large_col = $('#layout_carousel_desktop_large_col');
            GridPlusSetting.layout_carousel_desktop_large_width = $('#layout_carousel_desktop_large_width');
            GridPlusSetting.layout_carousel_desktop_medium_col = $('#layout_carousel_desktop_medium_col');
            GridPlusSetting.layout_carousel_desktop_medium_width = $('#layout_carousel_desktop_medium_width');
            GridPlusSetting.layout_carousel_desktop_small_col = $('#layout_carousel_desktop_small_col');
            GridPlusSetting.layout_carousel_desktop_small_width = $('#layout_carousel_desktop_small_width');
            GridPlusSetting.layout_carousel_tablet_col = $('#layout_carousel_tablet_col');
            GridPlusSetting.layout_carousel_tablet_width = $('#layout_carousel_tablet_width');
            GridPlusSetting.layout_carousel_tablet_small_col = $('#layout_carousel_tablet_small_col');
            GridPlusSetting.layout_carousel_tablet_small_width = $('#layout_carousel_tablet_small_width');
            GridPlusSetting.layout_carousel_mobile_col = $('#layout_carousel_mobile_col');
            GridPlusSetting.layout_carousel_mobile_width = $('#layout_carousel_mobile_width');

            GridPlusSetting.layout_carousel_next_text = $('#layout_carousel_next_text');
            GridPlusSetting.layout_carousel_prev_text = $('#layout_carousel_prev_text');
            GridPlusSetting.layout_carousel_width_ratio = $('#layout_carousel_width_ratio');
            GridPlusSetting.layout_carousel_height_ratio = $('#layout_carousel_height_ratio');
            GridPlusSetting.layout_loop = $('#layout_loop');
            GridPlusSetting.layout_center = $('#layout_center');
            GridPlusSetting.layout_autoplay = $('#layout_autoplay');
            GridPlusSetting.layout_autoplay_hover_pause = $('#layout_autoplay_hover_pause');
            GridPlusSetting.layout_autoplay_time = $('#layout_autoplay_time');
            GridPlusSetting.layout_show_dot = $('#layout_show_dot');
            GridPlusSetting.layout_show_nav = $('#layout_show_nav');
            GridPlusSetting.layout_carousel_nav_position = $('#layout_carousel_nav_position');
            GridPlusSetting.layout_carousel_nav_style = $('#layout_carousel_nav_style');

            GridPlusSetting.layout_justified_row_height = $('#layout_justified_row_height');

            GridPlusSetting.layout_source = $('#layout_source');
            GridPlusSetting.source_type = $('#source_type');
            GridPlusSetting.attachment_type = $('#attachment_type');
            GridPlusSetting.grid_gallery = $('#grid_gallery');
            GridPlusSetting.custom_urls = $('#custom_urls');
            GridPlusSetting.layout_category = $('#layout_category');
            GridPlusSetting.layout_show_category = $('#layout_show_category');
            GridPlusSetting.layout_cate_multi_line = $('#layout_cate_multi_line');
            GridPlusSetting.layout_authors = $('#layout_authors');
            GridPlusSetting.layout_include_ids = $('#layout_include_ids');
            GridPlusSetting.layout_exclude_ids = $('#layout_exclude_ids');

            GridPlusSetting.layout_order = $('#layout_order');
            GridPlusSetting.layout_order_by = $('#layout_order_by');

            GridPlusSetting.layout_category_color = $('#layout_category_color');
            GridPlusSetting.layout_category_hover_color = $('#layout_category_hover_color');
            GridPlusSetting.layout_no_image_bg_color = $('#layout_no_image_bg_color');
            GridPlusSetting.layout_bg_color = $('#layout_bg_color');
            GridPlusSetting.layout_icon_color = $('#layout_icon_color');
            GridPlusSetting.layout_icon_hover_color = $('#layout_icon_hover_color');
            GridPlusSetting.layout_title_color = $('#layout_title_color');
            GridPlusSetting.layout_title_hover_color = $('#layout_title_hover_color');
            GridPlusSetting.layout_excerpt_color = $('#layout_excerpt_color');

            GridPlusSetting.layout_animation_type = $('#layout_animation_type');
            GridPlusSetting.layout_pagination_type = $('#layout_pagination_type');
            GridPlusSetting.layout_page_prev_text = $('#layout_page_prev_text');
            GridPlusSetting.layout_page_next_text = $('#layout_page_next_text');
            GridPlusSetting.layout_page_loadmore_text = $('#layout_page_loadmore_text');
            if(GridPlusSetting.layout_type.val() == 'metro') {
                GridPlusSetting.layout_item_per_page = $('#metro_layout_item_per_page');
                GridPlusSetting.layout_total_items = $('#metro_layout_total_items');
                GridPlusSetting.layout_pagination_type = $('#metro_layout_pagination_type');
                GridPlusSetting.layout_page_prev_text = $('#metro_layout_page_prev_text');
                GridPlusSetting.layout_page_next_text = $('#metro_layout_page_next_text');
                GridPlusSetting.layout_page_loadmore_text = $('#metro_layout_page_loadmore_text');
            }
            if(GridPlusSetting.layout_source.val() == 'attachment' && GridPlusSetting.attachment_type.val() == 'choose_source') {
                GridPlusSetting.layout_authors = $('#attachment_layout_authors');
                GridPlusSetting.layout_order = $('#attachment_layout_order');
                GridPlusSetting.layout_order_by = $('#attachment_layout_order_by');
            }
            $('optgroup', GridPlusSetting.layout_category).remove();
            GridPlusSetting.layout_category.append($('optgroup[data-post-type="' + GridPlusSetting.layout_source.val() + '"]', '#layout_category_filter').clone());

            GridPlusSetting.initSelectize();
        },

        initGridMoveAndResize: function ($layout_type) {
            if (typeof GridPlusSetting.grid != 'undefined' && GridPlusSetting.grid != null) {
                if ($layout_type == 'grid' || $layout_type == 'masonry') {
                    GridPlusSetting.grid.disable();
                }
                if ($layout_type == 'metro') {
                    GridPlusSetting.grid.enable();
                }
            }
            if ($layout_type == 'grid') {
                $('.change-ratio', '.action-groups').show();
            } else {
                $('.change-ratio', '.action-groups').hide();
            }
            if ($layout_type == 'metro') {
                $('.change-item-ratio', '.grid-stack-item').show();
            } else {
                $('.change-item-ratio', '.grid-stack-item').hide();
            }
        },

        registerEvent: function () {
            /** add grid stack item event  **/
            $('a.add-item').on('click', function () {
                GridPlusSetting.showPopupSkin(function ($item_style) {
                    var $item_info = GridPlusSetting.estimateItemInfo(),
                        $width = $item_info.width,
                        $height = $item_info.height,
                        $system_col = $item_info.system_col,
                        $col = $item_info.col;

                    GridPlusSetting.options.width = $system_col;
                    GridPlusSetting.grid.setGridWidth($system_col, false);

                    GridPlusSetting.processAddItem($width, $height, $col, $item_style);
                    GridPlusSetting.processUpdateItemPerPage(1);
                });
            });

            /** change grid stack item skin for all  **/
            $('a.change-item-style').on('click', function () {
                GridPlusSetting.showPopupSkin(function ($item_style) {
                    $('.grid-stack-item').each(function () {
                        GridPlusSetting.processInitItemStyle($(this), $item_style);
                    })
                });
            });

            /** save grid event **/
            $('a.save-layout', '.grid-plus-container').off('click').on('click', function () {
                GridPlusSetting.processSaveGrid(this);
            });

            /** skins select event **/
            $('.grid-post-item', '.list-skins').each(function () {
                $(this).append('<a class="select-skin" href="javascript:;"><i class="fa fa-square-o"></i>Select</a>');
                $('a.select-skin', this).off('click').on('click', function () {
                    $('a.select-skin').html('<i class="fa fa-square-o"></i>Select');
                    $(this).html('<i class="fa fa-check-square-o"></i>Selected');
                    $('.grid-post-item', '.list-skins').attr('data-skin-selected', 0);
                    $(this).parent().attr('data-skin-selected', 1);
                });
            });

            /** generate layout event **/
            $('a.generate-layout').on('click', function () {
                GridPlusUtil.showLoading('Processing generate layout ...');
                GridPlusSetting.vars.grid_stack.fadeOut(function () {
                    GridPlusSetting.processGenerateLayout();
                    GridPlusSetting.vars.grid_stack.fadeIn(function () {
                        GridPlusUtil.closeLoading(0);
                    });
                })
            });

            $('a.search-layout').on('click', function () {
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
                        if (GridPlusSetting.is_search == 0) {
                            GridPlusSetting.is_search = 1;
                            GridPlusSetting.processSearch('Searching grid ......', $ajax_url, $grid_name);
                        }

                    }
                });

            });

            $('.refresh-layout').on('click', function () {
                if (GridPlusSetting.is_search == 0) {
                    GridPlusSetting.is_search = 1;
                    var $ajax_url = $(this).parent().attr('data-ajax-url');
                    GridPlusSetting.processSearch('Refresh grid ......', $ajax_url, '');
                }
            });

            $('.change-ratio', '.action-groups').on('click', function () {
                var template = wp.template('bg-prompt-change-height-dialog'),
                    $height_ratio = GridPlusSetting.vars.grid_stack.attr('data-height-ratio'),
                    $width_ratio = GridPlusSetting.vars.grid_stack.attr('data-width-ratio');
                if (typeof $height_ratio == 'undefined' || $height_ratio == '') {
                    $height_ratio = 1;
                }
                if (typeof $width_ratio == 'undefined' || $width_ratio == '') {
                    $width_ratio = 1;
                }
                $('body').append(template({'height_ratio': $height_ratio, 'width_ratio': $width_ratio}));

                $('a.close-popup', '.bg-popup-change-height').on('click', function () {
                    $('.bg-popup-change-height').remove();
                });

                $('.apply-change-height').on('click', function () {
                    var $height_ratio = $('.txt_height', '.bg-popup-change-height').val(),
                        $width_ratio = $('.txt_width', '.bg-popup-change-height').val(),
                        $item_width = $('.grid-stack-item-content', '.grid-stack-item:first-child').width(),
                        $gs_height = 0,
                        $height = 0;
                    $height_ratio = parseInt($height_ratio) <= 0 ? 1 : $height_ratio;
                    $width_ratio = parseInt($width_ratio) <= 0 ? 1 : $width_ratio;
                    if ($height_ratio != '' && $width_ratio != '') {
                        GridPlusSetting.vars.grid_stack.attr('data-height-ratio', $height_ratio);
                        GridPlusSetting.vars.grid_stack.attr('data-width-ratio', $width_ratio);
                        $item_width = parseInt($item_width);
                        $height_ratio = parseInt($height_ratio);
                        $width_ratio = parseInt($width_ratio);
                        $height = Math.floor($item_width * ($height_ratio / $width_ratio));
                        $gs_height = $height / GridPlusSetting.options.cellHeight;
                        GridPlusSetting.processChangeHeight($gs_height);
                    }
                    $('.bg-popup-change-height').remove();
                });
            });

            /** change grid name */
            GridPlusSetting.layout_name.keyup(function () {
                GridPlusSetting.layout_shortcode.text('[grid_plus name="' + $(this).val() + '"]');
            });

            GridPlusSetting.registerChangeTab();
        },

        initSelectize: function () {

            var $default_value,
                $option = {
                    plugins: ['remove_button', 'drag_drop'],
                    searchField: 'text',
                    delimiter: ',',
                    persist: false
                };

            $('select:not(.manual)', '.grid-plus-wrap').each(function () {
                $default_value = $(this).attr('data-selected');
                if (typeof $default_value != 'undefined') {
                    $option.items = $default_value.split(',');
                } else {
                    $option.items = [];
                }
                $(this).selectize($option);
            });

            $default_value = GridPlusSetting.layout_category.attr('data-selected');
            if (typeof $default_value != 'undefined') {
                $option.items = $default_value.split(',');
            } else {
                $option.items = [];
            }
            GridPlusSetting.select_categories = GridPlusSetting.layout_category.selectize($option);

            if (typeof GridPlusSetting.select_categories[0] != 'undefined') {
                GridPlusSetting.select_categories = GridPlusSetting.select_categories[0].selectize;
            }

            GridPlusSetting.layout_exclude_ids.selectize({
                plugins: ['remove_button'],
                valueField: 'value',
                labelField: 'label',
                searchField: 'label',
                delimiter: ',',
                options: [],
                create: false,
                load: function (query, callback) {
                    if (!query.length) return callback();
                    $.ajax({
                        url: GridPlusSetting.layout_exclude_ids.attr('data-url') + '?action=grid_plus_get_posts',
                        type: 'GET',
                        data: {
                            title: query,
                            post_type: GridPlusSetting.layout_source.val()
                        },
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            callback($.parseJSON(res));
                        }
                    });
                }
            });
            GridPlusSetting.layout_include_ids.selectize({
                plugins: ['remove_button'],
                valueField: 'value',
                labelField: 'label',
                searchField: 'label',
                delimiter: ',',
                options: [],
                create: false,
                load: function (query, callback) {
                    if (!query.length) return callback();
                    $.ajax({
                        url: GridPlusSetting.layout_include_ids.attr('data-url') + '?action=grid_plus_get_posts',
                        type: 'GET',
                        data: {
                            title: query,
                            post_type: GridPlusSetting.layout_source.val()
                        },
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            callback($.parseJSON(res));
                        }
                    });
                }
            });

            GridPlusSetting.layout_custom_content.selectize({
                plugins: ['remove_button'],
                valueField: 'value',
                labelField: 'label',
                searchField: 'label',
                delimiter: ',',
                options: [],
                create: false,
                load: function (query, callback) {
                    if (!query.length) return callback();
                    $.ajax({
                        url: GridPlusSetting.layout_custom_content.attr('data-url') + '?action=grid_plus_get_posts',
                        type: 'GET',
                        data: {
                            title: query,
                            post_type: GridPlusSetting.layout_custom_content.attr('data-post-type')
                        },
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            callback($.parseJSON(res));
                        }
                    });
                }
            })
        },

        initSaveCtrS: function () {
            $(window).bind('keydown', function(event) {
                if (event.ctrlKey || event.metaKey) {
                    if('s' === String.fromCharCode(event.which).toLowerCase()) {
                        event.preventDefault();
                        $('a.save-layout', '#layout-config').trigger('click');
                        return false;
                    }
                }
            });

        },

        initDepend: function () {
            $('[data-depend-control]').each(function () {
                var $parent_id = $(this).attr('data-depend-control'),
                    $parent = $('#' + $parent_id),
                    $parent_value = '',
                    $depend_value = ',' + $(this).attr('data-depend-value') + ',',
                    $depend_dif_value = ',' + $(this).attr('data-depend-dif-value') + ',';
                if (typeof $parent != 'undefined' && $parent.length > 0) {
                    $parent_value = $parent.val();
                    if ($parent.is(':checkbox')) {
                        $parent_value = $parent.is(':checked');
                    }
                    if ($parent.is(':radio')) {
                        $parent_value = $('#' + $parent_id + ':checked').val();
                    }
                    if ($depend_value.indexOf(',' + $parent_value + ',') >= 0) {
                        $(this).css({'display': 'inline-block'});
                    } else if ($depend_dif_value != ',undefined,' && $depend_dif_value.indexOf(',' + $parent_value + ',') < 0) {
                        $(this).css({'display': 'inline-block'});
                    } else {
                        $(this).css({'display': 'none'});
                    }
                    $parent.off('change').on('change', function (event) {
                        event.preventDefault();
                        if($(this).attr('id') == 'layout_type' || $(this).attr('id') == 'layout_custom_content_enable') {
                            GridPlusSetting.execCustomContent();
                        }
                        if($(this).attr('id') == 'layout_type') {
                            var $layout_type = $(this).val();
                            if ($layout_type == 'grid') {
                                $('.change-ratio', '.action-groups').show();
                            } else {
                                $('.change-ratio', '.action-groups').hide();
                            }
                            if ($layout_type == 'metro') {
                                $('.change-item-ratio', '.grid-stack-item').show();
                            } else {
                                $('.change-item-ratio', '.grid-stack-item').hide();
                            }
                            GridPlusSetting.initGridMoveAndResize($layout_type);
                        }

                        if($(this).attr('id') == 'layout_source') {
                            var $post_type = $(this).val();
                            GridPlusSetting.select_categories.destroy();
                            $('optgroup', GridPlusSetting.layout_category).remove();
                            GridPlusSetting.layout_category.append($('optgroup[data-post-type="' + $post_type + '"]', '#layout_category_filter').clone());
                            GridPlusSetting.initSelectize();
                        }

                        GridPlusSetting.initDepend();
                    });
                }
            });
        },

        initGridStack: function ($layout_type, $col, $item_per_page, $gutter) {
            var $padding = $('select[name="padding"]').val(),
                $item_info = GridPlusSetting.estimateItemInfo(),
                $width = $item_info.width,
                $height = $item_info.height,
                $system_col = $item_info.system_col;

            $gutter = parseInt($gutter);
            GridPlusSetting.options.verticalMargin = $gutter;
            GridPlusSetting.options.width = $system_col;
            GridPlusSetting.vars.grid_stack.attr('data-gutter', 'gutter-' + $gutter);
            GridPlusSetting.vars.grid_stack.attr('data-layout', $layout_type);

            GridPlusSetting.vars.grid_stack.gridstack(GridPlusSetting.options);
            GridPlusSetting.grid = GridPlusSetting.vars.grid_stack.data('gridstack');
            GridPlusSetting.grid.verticalMargin($gutter, true);
            GridPlusSetting.grid.removeAll();
            for (var $i = 0; $i < $item_per_page; $i++) {
                GridPlusSetting.processAddItem($width, $height, $col);
            }
        },

        initGridStackEdit: function ($grid_data, $gutter, $col, $height_ratio, $width_ratio, $layout_type) {

            var $grid_items = GridStackUI.Utils.sort($grid_data, 1),
                $el = null;

            GridPlusSetting.vars.grid_stack.attr('data-height-ratio', $height_ratio);
            GridPlusSetting.vars.grid_stack.attr('data-width-ratio', $width_ratio);
            GridPlusSetting.vars.grid_stack.attr('data-gutter', 'gutter-' + $gutter);
            GridPlusSetting.vars.grid_stack.attr('data-layout', $layout_type);

            if ($layout_type == 'grid' || $layout_type == 'masonry' || $col==5) {
                GridPlusSetting.options.width = $col;
                GridPlusSetting.vars.grid_stack.addClass('grid-stack-' + $col);
            } else {
                GridPlusSetting.options.width = 12;
            }
            if ($layout_type == 'metro') {
                $('.change-item-ratio', '.grid-stack-item').show();
            } else {
                $('.change-item-ratio', '.grid-stack-item').hide();
            }

            GridPlusSetting.options.verticalMargin = $gutter;
            GridPlusSetting.vars.grid_stack.gridstack(GridPlusSetting.options);
            GridPlusSetting.grid = GridPlusSetting.vars.grid_stack.data('gridstack');
            GridPlusSetting.grid.verticalMargin($gutter, false);
            GridPlusSetting.grid.removeAll();
            _.each($grid_items, function (node) {
                $el = GridPlusSetting.grid.addWidget($('<div><div class="grid-stack-item-content" /></div>'),
                    node.x, node.y, node.width, node.height);

                GridPlusSetting.registerRemoveGridStackItem($el);
                GridPlusSetting.registerChangeItemStyle($el);
                GridPlusSetting.registerChangeItemRatio($el);
                GridPlusSetting.processInitItemStyle($el, node.skin, node.item_width_ratio, node.item_height_ratio);
            }, this);
        },

        registerRemoveGridStackItem: function (el) {
            $('.grid-stack-item-content', el).append('<a class="remove-item" title="Remove item"><i class="fa fa-trash-o"></i></a>');
            $('a', el).off('click').on('click', function () {
                GridPlusSetting.grid.removeWidget(el);
                GridPlusSetting.processUpdateItemPerPage(-1);
            });
        },

        registerChangeItemRatio: function (el) {
            var $itemRatio = '<div class="change-item-ratio"><a href="javascript:;" title="Change item ratio"><i class="fa fa-qrcode"></i></a>';
            $('.grid-stack-item-content', el).append($itemRatio);
            $('.change-item-ratio a', el).off('click').on('click', function () {
                var grid_stack_item = $(this).closest('.grid-stack-item'),
                    grid_stack_item_contant = grid_stack_item,
                    template = wp.template('bg-prompt-change-item-height-dialog'),
                    $height_ratio = grid_stack_item.attr('data-item-height-ratio'),
                    $width_ratio = grid_stack_item.attr('data-item-width-ratio');
                if (typeof $height_ratio == 'undefined' || $height_ratio == '') {
                    $height_ratio = 1;
                }
                if (typeof $width_ratio == 'undefined' || $width_ratio == '') {
                    $width_ratio = 1;
                }
                $('body').append(template({'height_ratio': $height_ratio, 'width_ratio': $width_ratio}));

                $('a.close-popup', '.bg-popup-change-item-height').on('click', function () {
                    $('.bg-popup-change-item-height').remove();
                });

                $('.apply-change-item-height', '.bg-popup-change-item-height').on('click', function () {
                    var $height_ratio = $('.txt_height', '.bg-popup-change-item-height').val(),
                        $width_ratio = $('.txt_width', '.bg-popup-change-item-height').val(),
                        $item_width = grid_stack_item_contant.width(),
                        $item_height = grid_stack_item_contant.height(),
                        $item_gs_height = grid_stack_item.attr('data-gs-height'),
                        $height_unit = Math.floor($item_height / $item_gs_height),
                        $gs_height = 0,
                        $height = 0;
                    $height_ratio = parseInt($height_ratio) <= 0 ? 1 : $height_ratio;
                    $width_ratio = parseInt($width_ratio) <= 0 ? 1 : $width_ratio;
                    if ($height_ratio != '' && $width_ratio != '') {
                        grid_stack_item.attr('data-item-height-ratio', $height_ratio);
                        grid_stack_item.attr('data-item-width-ratio', $width_ratio);
                        $item_width = parseInt($item_width);
                        $height_ratio = parseInt($height_ratio);
                        $width_ratio = parseInt($width_ratio);
                        $height = Math.floor($item_width * ($height_ratio / $width_ratio));
                        $gs_height = Math.floor($height / $height_unit);
                        console.log($height, $height_unit);
                        GridPlusSetting.processChangeItemHeight(grid_stack_item, $gs_height);
                    }
                    $('.bg-popup-change-item-height').remove();
                });
            });
        },
        registerChangeItemStyle: function (el) {
            var $skins = '<div class="change-style"><a href="javascript:;" title="Change skin"><i class="fa fa-paint-brush"></i></a>';
            $('.grid-stack-item-content', el).append($skins);
            $('.change-style a', el).off('click').on('click', function () {
                GridPlusSetting.showPopupSkin(function ($item_style) {
                    GridPlusSetting.processInitItemStyle(el, $item_style);
                });
            });
        },

        registerChangeTab: function () {
            var $tab_wrap_class = '.grid-plus-wrap .nav-tabs',
                $content_wrap_class = '.grid-plus-wrap .content-wrap';
            $('a', $tab_wrap_class).on('click', function () {
                var $section_id = $(this).attr('data-section-id');
                if (typeof $section_id != 'undefined') {
                    $('li', $tab_wrap_class).removeClass('tab-current');
                    var $data_external = $(this).attr('data-external');
                    if (typeof $data_external != 'undefined') {
                        $('a[data-section-id="' + $section_id + '"]', $tab_wrap_class).parent().addClass('tab-current');
                    } else {
                        $(this).parent().addClass('tab-current');
                    }

                    $('section', $content_wrap_class).hide();
                    $('#' + $section_id, $content_wrap_class).show();
                }
            });
        },
        registerGallery: function() {
            $('.sf-field-gallery-inner','.grid-plus-wrap').each(function () {
                var field = new SF_GalleryClass($(this));
                field.init();
            });
        },

        estimateItemInfo: function () {
            var $col = parseInt(GridPlusSetting.layout_col.val()),
                $width = Math.floor(12 / $col),
                $height = Math.floor(12 / $col),
                $layout_type = GridPlusSetting.layout_type.val(),
                $gutter = GridPlusSetting.layout_gutter.val(),
                $grid_stack_width = $('.grid-stack-container').width(),
                $item_width = Math.floor(parseInt($grid_stack_width) / $col),
                $height_ratio = GridPlusSetting.vars.grid_stack.attr('data-height-ratio'),
                $width_ratio = GridPlusSetting.vars.grid_stack.attr('data-width-ratio'),
                $system_col = 12;

            if ($layout_type == 'grid' || $layout_type == 'masonry' || $col==5) {
                $width = 1;
                $system_col = $col;
            }

            if ($layout_type == 'grid' ) {
                if (typeof $height_ratio != 'undefined' && typeof $width_ratio != 'undefined' && $width_ratio != '' && $height_ratio != '') {
                    $height = Math.floor($item_width * ($height_ratio / $width_ratio) / GridPlusSetting.options.cellHeight);
                } else {
                    $height = Math.floor($item_width / GridPlusSetting.options.cellHeight);
                }
            }else{
                switch ($col) {
                    case 2:
                    {
                        if ($gutter > 0) {
                            $height = 15;
                        } else {
                            $height = 40;
                        }
                        break;
                    }
                    case 3:
                    case 4:
                    {
                        if ($gutter > 0) {
                            $height = 15;
                        } else {
                            $height = 30;
                        }
                        break;
                    }
                    case 5:
                    case 6:
                    {
                        if ($gutter > 0) {
                            $height = 10;
                        } else {
                            $height = 20;
                        }
                        break;
                    }
                }
            }
            return {'width': $width, 'height': $height, 'system_col': $system_col, 'col': $col};
        },

        processSaveGrid: function (el) {
            if (GridPlusSetting.layout_type.val() != 'carousel' && GridPlusSetting.layout_type.val() != 'justified' ) {
                if (typeof GridPlusSetting.grid == 'undefined' || $('.grid-stack-item', '.grid-stack').length == 0) {
                    GridPlusUtil.popupAlert('fa fa-exclamation-triangle', 'Please generate layout before save');
                    return;
                }

                if (!GridPlusSetting.isValidateLayout()) {
                    GridPlusUtil.popupAlert('fa fa-exclamation-triangle', 'Please generate layout when change layout type before save');
                    return;
                }
            }
            GridPlusUtil.showLoading('Save Grid Settings');

            if(GridPlusSetting.layout_type.val() == 'metro') {
                GridPlusSetting.layout_item_per_page = $('#metro_layout_item_per_page');
                GridPlusSetting.layout_total_items = $('#metro_layout_total_items');
                GridPlusSetting.layout_pagination_type = $('#metro_layout_pagination_type');
                GridPlusSetting.layout_page_prev_text = $('#metro_layout_page_prev_text');
                GridPlusSetting.layout_page_next_text = $('#metro_layout_page_next_text');
                GridPlusSetting.layout_page_loadmore_text = $('#metro_layout_page_loadmore_text');
            } else {
                GridPlusSetting.layout_item_per_page = $('#layout_item_per_page');
                GridPlusSetting.layout_total_items = $('#layout_total_items');
                GridPlusSetting.layout_pagination_type = $('#layout_pagination_type');
                GridPlusSetting.layout_page_prev_text = $('#layout_page_prev_text');
                GridPlusSetting.layout_page_next_text = $('#layout_page_next_text');
                GridPlusSetting.layout_page_loadmore_text = $('#layout_page_loadmore_text');
            }
            if(GridPlusSetting.layout_source.val() == 'attachment' && GridPlusSetting.attachment_type.val() == 'choose_source') {
                GridPlusSetting.layout_authors = $('#attachment_layout_authors');
                GridPlusSetting.layout_order = $('#attachment_layout_order');
                GridPlusSetting.layout_order_by = $('#attachment_layout_order_by');
            }
            var $main_skin = 'thumbnail';
            if($('.grid-post-item[data-skin-selected="1"]',".list-skins").length > 0) {
                $main_skin = $('.grid-post-item[data-skin-selected="1"]',".list-skins").closest(".skin-item").attr("data-skin");
            }

            var $source_filter = 'all';
            if(GridPlusSetting.layout_source.val() == 'post') {
                $source_filter = $('#layout_source_filter').val();
                GridPlusSetting.layout_order = $('#layout_post_order');
                GridPlusSetting.layout_order_by = $('#layout_post_order_by');
            } else if(GridPlusSetting.layout_source.val() == 'product') {
                $source_filter = $('#layout_product_source_filter').val();
                GridPlusSetting.layout_order = $('#layout_product_order');
                GridPlusSetting.layout_order_by = $('#layout_product_order_by');
            } else {
                GridPlusSetting.layout_order = $('#layout_order');
                GridPlusSetting.layout_order_by = $('#layout_order_by');
            }

            var $carousel_next_text = GridPlusSetting.layout_carousel_next_text.val(),
                $carousel_prev_text = GridPlusSetting.layout_carousel_prev_text.val(),
                $loadmore_text = GridPlusSetting.layout_page_loadmore_text.val(),
                $pagination_next_text = GridPlusSetting.layout_page_next_text.val(),
                $pagination_prev_text = GridPlusSetting.layout_page_prev_text.val();

            if(typeof $carousel_next_text !='undefined'){
                $carousel_next_text = $carousel_next_text.replace(/"/g, '&quot;');
            }
            if(typeof $carousel_prev_text !='undefined'){
                $carousel_prev_text = $carousel_prev_text.replace(/"/g, '&quot;');
            }
            if(typeof $loadmore_text !='undefined'){
                $loadmore_text = $loadmore_text.replace(/"/g, '&quot;');
            }
            if(typeof $pagination_next_text !='undefined'){
                $pagination_next_text = $pagination_next_text.replace(/"/g, '&quot;');
            }
            if(typeof $pagination_prev_text !='undefined'){
                $pagination_prev_text = $pagination_prev_text.replace(/"/g, '&quot;');
            }

            var  $height_ratio = GridPlusSetting.vars.grid_stack.attr('data-height-ratio'),
                $width_ratio = GridPlusSetting.vars.grid_stack.attr('data-width-ratio');

            if(typeof $height_ratio =='undefined' || $height_ratio=='' || $height_ratio == 0 ){
                $height_ratio = 1;
            }
            if(typeof $width_ratio =='undefined' || $width_ratio=='' || $width_ratio == 0 ){
                $width_ratio = 1;
            }
            if(GridPlusSetting.layout_gutter.val() < 0) {
                GridPlusSetting.layout_gutter.val(0);
            }
            if(GridPlusSetting.layout_gutter.val() > 70) {
                GridPlusSetting.layout_gutter.val(70);
            }
            GridPlusSetting.vars.custom_content_numb = 0;
            var $grid_height = GridPlusSetting.vars.grid_stack.height(),
                $grid_layout = _.map($('.grid-stack > .grid-stack-item'), function (el) {
                    el = $(el);
                    var node = el.data('_gridstack_node');
                    if('custom-content' == el.attr('data-skin')) {
                        GridPlusSetting.vars.custom_content_numb++;
                    }
                    var width_ratio = el.attr('data-item-width-ratio'),
                        height_ratio = el.attr('data-item-height-ratio');
                    if(typeof(width_ratio) == 'undefined' || typeof(height_ratio) == 'undefined') {
                        width_ratio = el.children('.grid-stack-item-content').width();
                        height_ratio = el.height();
                    }
                    width_ratio = width_ratio <= 0 ? 1 : width_ratio;
                    height_ratio = height_ratio <= 0 ? 1 : height_ratio;
                    return {
                        x: node.x,
                        y: node.y,
                        width: node.width,
                        height: node.height,
                        skin: el.attr('data-skin'),
                        skin_css: el.attr('data-skin-css'),
                        template: el.attr('data-skin-template'),
                        item_width_ratio: width_ratio,
                        item_height_ratio: height_ratio
                    };
                }, this),
                $grid_config = {
                    'id': GridPlusSetting.layout_id.val(),
                    'name': GridPlusSetting.layout_name.val(),
                    'height': $grid_height,
                    'height_ratio': $height_ratio,
                    'width_ratio': $width_ratio,
                    'type': GridPlusSetting.layout_type.val(),
                    'columns': GridPlusSetting.layout_col.val(),
                    'gutter': GridPlusSetting.layout_gutter.val(),
                    'item_per_page': GridPlusSetting.layout_item_per_page.val(),
                    'total_item': GridPlusSetting.layout_total_items.val(),
                    'fix_item_height': GridPlusSetting.layout_fix_item_height.is(":checked"),
                    'crop_image': GridPlusSetting.layout_crop_image.is(":checked"),
                    'disable_link': GridPlusSetting.layout_disable_link.is(":checked"),
                    'custom_content_enable': GridPlusSetting.layout_custom_content_enable.is(":checked"),
                    'custom_content': GridPlusSetting.layout_custom_content.val(),
                    'custom_content_numb': GridPlusSetting.vars.custom_content_numb,
                    'loop': GridPlusSetting.layout_loop.is(":checked"),
                    'center': GridPlusSetting.layout_center.is(":checked"),
                    'justified_row_height' : GridPlusSetting.layout_justified_row_height.val(),
                    'main_skin': $main_skin,
                    'carousel_rtl': GridPlusSetting.layout_carousel_rtl.is(":checked"),
                    'carousel_height_ratio': GridPlusSetting.layout_carousel_height_ratio.val(),
                    'carousel_width_ratio': GridPlusSetting.layout_carousel_width_ratio.val(),
                    'carousel_next_text': $carousel_next_text,
                    'carousel_prev_text': $carousel_prev_text,
                    'carousel_total_items':  GridPlusSetting.layout_carousel_total_items.val(),
                    'carousel_desktop_large_col':  GridPlusSetting.layout_carousel_desktop_large_col.val(),
                    'carousel_desktop_large_width': GridPlusSetting.layout_carousel_desktop_large_width.val(),
                    'carousel_desktop_medium_col': GridPlusSetting.layout_carousel_desktop_medium_col.val(),
                    'carousel_desktop_medium_width': GridPlusSetting.layout_carousel_desktop_medium_width.val(),
                    'carousel_desktop_small_col': GridPlusSetting.layout_carousel_desktop_small_col.val(),
                    'carousel_desktop_small_width': GridPlusSetting.layout_carousel_desktop_small_width.val(),
                    'carousel_tablet_col': GridPlusSetting.layout_carousel_tablet_col.val(),
                    'carousel_tablet_width': GridPlusSetting.layout_carousel_tablet_width.val(),
                    'carousel_tablet_small_col': GridPlusSetting.layout_carousel_tablet_small_col.val(),
                    'carousel_tablet_small_width': GridPlusSetting.layout_carousel_tablet_small_width.val(),
                    'carousel_mobile_col': GridPlusSetting.layout_carousel_mobile_col.val(),
                    'carousel_mobile_width': GridPlusSetting.layout_carousel_mobile_width.val(),
                    'autoplay': GridPlusSetting.layout_autoplay.is(":checked"),
                    'autoplay_hover_pause': GridPlusSetting.layout_autoplay_hover_pause.is(":checked"),
                    'autoplay_time': GridPlusSetting.layout_autoplay_time.val(),
                    'show_dot': GridPlusSetting.layout_show_dot.is(":checked"),
                    'show_nav': GridPlusSetting.layout_show_nav.is(":checked"),
                    'carousel_nav_position': GridPlusSetting.layout_carousel_nav_position.val(),
                    'carousel_nav_style': GridPlusSetting.layout_carousel_nav_style.val(),
                    'animation_type': GridPlusSetting.layout_animation_type.val(),
                    'pagination_type': GridPlusSetting.layout_pagination_type.val(),
                    'page_next_text': $pagination_next_text,
                    'page_prev_text': $pagination_prev_text,
                    'page_loadmore_text': $loadmore_text,
                    'category_color': GridPlusSetting.layout_category_color.val(),
                    'category_hover_color': GridPlusSetting.layout_category_hover_color.val(),
                    'no_image_background_color': GridPlusSetting.layout_no_image_bg_color.val(),
                    'background_color': GridPlusSetting.layout_bg_color.val(),
                    'icon_color': GridPlusSetting.layout_icon_color.val(),
                    'icon_hover_color': GridPlusSetting.layout_icon_hover_color.val(),
                    'title_color': GridPlusSetting.layout_title_color.val(),
                    'title_hover_color': GridPlusSetting.layout_title_hover_color.val(),
                    'excerpt_color': GridPlusSetting.layout_excerpt_color.val(),
                    'custom_css': GridPlusSetting.layout_custom_css.getValue()
                },
                $grid_data_source = {
                    'post_type': GridPlusSetting.layout_source.val(),
                    'source_type': GridPlusSetting.source_type.val(),
                    'grid_gallery': GridPlusSetting.grid_gallery.val(),
                    'categories': GridPlusSetting.layout_category.val(),
                    'show_category': GridPlusSetting.layout_show_category.val(),
                    'cate_multi_line': GridPlusSetting.layout_cate_multi_line.is(":checked"),
                    'authors': GridPlusSetting.layout_authors.val(),
                    'include_ids': GridPlusSetting.layout_include_ids.val(),
                    'exclude_ids': GridPlusSetting.layout_exclude_ids.val(),
                    'source_filter': $source_filter,
                    'attachment_type': GridPlusSetting.attachment_type.val(),
                    'custom_urls': GridPlusSetting.custom_urls.val(),
                    'order': GridPlusSetting.layout_order.val(),
                    'order_by': GridPlusSetting.layout_order_by.val()
                },
                $ajax_url = $(el).parent().attr('data-ajax-url');
            $grid_layout = _.sortBy($grid_layout, function (grid) {
                return grid.y;
            });
            $grid_layout = GridStackUI.Utils.sort($grid_layout);
            $.ajax({
                url: $ajax_url,
                type: 'POST',
                data: ({
                    action: 'grid_plus_save_layout',
                    grid_config: $grid_config,
                    grid_data_source: $grid_data_source,
                    grid_layout: $grid_layout
                }),
                success: function (data) {
                    data = JSON.parse(data);
                    if (typeof data.code != 'undefined' && data.code == '-1') {
                        GridPlusUtil.closeLoading(0);
                        GridPlusUtil.popupAlert('fa fa-exclamation-triangle', data.message);
                    } else {
                        GridPlusSetting.layout_id.val(data.id);
                        GridPlusSetting.layout_shortcode.val('[grid_plus name="' + GridPlusSetting.layout_name.val() + '"]');
                        GridPlusUtil.changeLoadingStatus('fa fa-check-square-o', 'Grid setting saved');
                        GridPlusUtil.closeLoading();
                    }
                },
                error: function () {
                    GridPlusUtil.changeLoadingStatus('fa fa-exclamation-triangle', 'Have error when save information');
                    GridPlusUtil.closeLoading();
                }
            });
        },

        processGenerateLayout: function () {
            var $layout_name = $('#layout_name').val(),
                $layout_type = $('#layout_type').val(),
                $layout_col = $('#layout_col').val(),
                $layout_item_per_page = $('#layout_item_per_page').val(),
                $layout_items = $('#layout_total_items').val(),
                $layout_gutter = $('#layout_gutter').val(),
                $layout_pagination_type = $('#layout_pagination_type').val(),
                $grid_stack_class = 'grid-stack-' + $layout_col;
            if($layout_pagination_type == 'show_all') {
                $layout_item_per_page = $layout_col*2;
            }
            $('#grid_id', '.grid-plus-layout').val();
            $('#grid_name', '.grid-plus-layout').val($layout_name);
            $('#grid_type', '.grid-plus-layout').val($layout_type);
            $('#grid_col', '.grid-plus-layout').val($layout_col);
            $('#grid_gutter', '.grid-plus-layout').val($layout_gutter);
            $('#grid_item_per_page', '.grid-plus-layout').val($layout_item_per_page);
            $('#grid_items', '.grid-plus-layout').val($layout_items);

            for (var $i = 1; $i <= 12; $i++) {
                GridPlusSetting.vars.grid_stack.removeClass('grid-stack-' + $i);
            }
            if ($layout_type != 'metro' || $layout_col == 5) {
                GridPlusSetting.vars.grid_stack.addClass($grid_stack_class);
            }
            $layout_col = parseInt($layout_col);

            $layout_item_per_page = parseInt($layout_item_per_page);

            GridPlusSetting.initGridStack($layout_type, $layout_col, $layout_item_per_page, $layout_gutter);

            GridPlusSetting.initGridMoveAndResize($layout_type);
        },

        processAddItem: function ($width, $height, $col, $item_style) {
            var $latest_item = $('.grid-stack-item:last-child'),
                $gs_y = 0,
                $gs_x = 0,
                $gs_width = $width,
                $x = 0,
                $y = 0,
                $total_item = $('.grid-stack-item').length;

            if (typeof $latest_item != 'undefined' && $latest_item.length > 0) {
                $gs_y = $latest_item.attr('data-gs-y');
                $gs_x = $latest_item.attr('data-gs-x');
                $gs_width = $latest_item.attr('data-gs-width');

                if ($total_item % $col == 0) {
                    $x = 0;
                    $y = Math.floor($total_item / $col) * $height;
                } else {
                    $y = $gs_y;
                    $x = parseInt($gs_x) + parseInt($gs_width);
                }
            }
            var node = {
                x: $x,
                y: $y,
                width: $width,
                height: $height
            };

            if (typeof GridPlusSetting.grid == 'undefined' || GridPlusSetting.grid == null) {
                GridPlusSetting.vars.grid_stack.gridstack(GridPlusSetting.options);
                GridPlusSetting.grid = GridPlusSetting.vars.grid_stack.data('gridstack');
            }
            var $el = GridPlusSetting.grid.addWidget($('<div><div class="grid-stack-item-content stack-item-has-bg"/></div>'),
                node.x, node.y, node.width, node.height, false);

            if (typeof $item_style == 'undefined' || $item_style == '') {
                var $skin_selected = $('.grid-post-item[data-skin-selected="1"]', '.list-skins');
                $item_style = 'thumbnail';
                if ($skin_selected.length > 0) {
                    $item_style = $skin_selected.closest('.skin-item').attr('data-skin');
                }
            }
            GridPlusSetting.registerRemoveGridStackItem($el);
            GridPlusSetting.registerChangeItemStyle($el);
            GridPlusSetting.registerChangeItemRatio($el);
            GridPlusSetting.processInitItemStyle($el, $item_style, 1, 1);
        },

        processInitItemStyle: function ($el, $style, $width_ratio, $height_ratio) {
            var $grid_stack_item = $('.grid-stack-item-content', $el),
                $skin = $('li[data-skin="' + $style + '"]', '.list-skins'),
                $post_item = $('.grid-post-item', $skin),
                $skin_slug = '',
                $skin_template = '',
                $skin_css = '',
                $data_img = '';

            if ($post_item.length == 0) {
                $skin = $('li:first-child', '.list-skins');
                $post_item = $('.grid-post-item:first-child', '.list-skins');
            }
            $skin_slug = $skin.attr('data-skin');
            $skin_css = $skin.attr('data-skin-css');
            $skin_template = $skin.attr('data-skin-template');
            $post_item = $post_item.clone();
            $data_img = $('.thumbnail-image', $post_item).attr('data-img');

            if (typeof $data_img != 'undefined' && $data_img != '') {
                $('.thumbnail-image img', $post_item).remove();
                $('.thumbnail-image', $post_item).css('background-image', 'url("' + $data_img + '")');
            }
            $('a.select-skin', $post_item).remove();

            $el.fadeOut(function () {
                if ($('.grid-post-item', $el).length > 0) {
                    $('.grid-post-item', $el).remove();
                }
                $el.attr('data-skin', $skin_slug);
                $el.attr('data-skin-css', $skin_css);
                $el.attr('data-skin-template', $skin_template);
                $el.attr('data-item-height-ratio', $height_ratio);
                $el.attr('data-item-width-ratio', $width_ratio);
                $grid_stack_item.append($post_item);
                $el.fadeIn();
            });
        },

        processUpdateItemPerPage: function ($number_change) {
            var $item_per_page = $('#layout_item_per_page').val();
            $('#layout_item_per_page').val(( parseInt($item_per_page) + $number_change ));
            $('#grid_item_per_page', '.grid-plus-layout').val(( parseInt($item_per_page) + $number_change ));
        },

        processEditLayout: function () {
            GridPlusUtil.showLoading('Loading layout information');
            $.ajax({
                url: grid_script_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'grid_plus_get_info',
                    grid_id: grid_script_data.grid_id
                }),
                success: function (data) {
                    var $grid_info = JSON.parse(data);
                    if (typeof $grid_info.code == '-1') {
                        GridPlusUtil.closeLoading(0);
                        GridPlusUtil.popupAlert('fa fa-exclamation-triangle', $grid_info.message);
                        return;
                    }
                    if (typeof $grid_info.grid_config!='undefined') {
                        var $skin = $('.skin-item[data-skin="' + $grid_info.grid_config.main_skin + '"]', '.list-skins');
                        if($skin.length > 0){
                            $('.grid-post-item',$skin).attr('data-skin-selected',1);
                            $('.select-skin',$skin).html('<i class="fa fa-check-square-o"></i>Selected');
                        }
                    }

                    GridPlusSetting.initGridStackEdit($grid_info.grid_layout, $grid_info.grid_config.gutter, $grid_info.grid_config.columns, $grid_info.grid_config.height_ratio, $grid_info.grid_config.width_ratio, $grid_info.grid_config.type);

                    GridPlusSetting.initGridMoveAndResize($grid_info.grid_config.type);

                    GridPlusUtil.changeLoadingStatus('fa fa-check-square-o', 'Information has been loaded');
                    GridPlusUtil.closeLoading(500);
                },
                error: function () {
                    GridPlusUtil.changeLoadingStatus('fa fa-exclamation-triangle', 'Have error when load information');
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
            GridPlusSetting.registerGridAction();
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
                    GridPlusSetting.processRebindListGrid(data);
                    GridPlusSetting.is_search = 0;
                },
                error: function () {
                    GridPlusUtil.changeLoadingStatus('fa fa-exclamation-triangle', 'Have error when delete information');
                    GridPlusUtil.closeLoading();
                    GridPlusSetting.is_search = 0;
                }
            });
        },

        processChangeHeight: function ($height) {
            var $diff_height = 0,
                $y = 0,
                $width = 0,
                $data_height = 0;
            $('.grid-stack-item').each(function () {
                $data_height = parseInt($(this).attr('data-gs-height'));
                $diff_height = $height - $data_height;
                $y = parseInt($(this).attr('data-gs-y'));
                $width = parseInt($(this).attr('data-gs-width'));
                $(this).attr('data-gs-height', $height);
                if ($y > 0) {
                    $(this).attr('data-gs-y', ($y + $diff_height));
                }
                if (typeof GridPlusSetting.grid != 'undefined') {
                    GridPlusSetting.grid.resize($(this), $width, $height)
                }
            });
            if (typeof GridPlusSetting.grid != 'undefined') {
                GridPlusSetting.grid.batchUpdate();
                GridPlusSetting.grid.commit();
            }
        },
        processChangeItemHeight: function ($item, $height) {
            var $diff_height = 0,
                $y = 0,
                $width = 0,
                $data_height = 0;
            $data_height = parseInt($item.attr('data-gs-height'));
            $diff_height = $height - $data_height;
            $y = parseInt($item.attr('data-gs-y'));
            $width = parseInt($item.attr('data-gs-width'));
            $item.attr('data-gs-height', $height);
            if ($y > 0) {
                $(this).attr('data-gs-y', ($y + $diff_height));
            }
            if (typeof GridPlusSetting.grid != 'undefined') {
                GridPlusSetting.grid.resize($item, $width, $height)
            }
        },

        isValidateLayout: function () {
            var $layout_type = GridPlusSetting.layout_type.val(),
                $col = GridPlusSetting.layout_col,
                $items = $('.grid-stack-item');

            if ($layout_type == 'grid' || $layout_type == 'masonry') {
                for (var $i = 0; $i < $items.length; $i++) {
                    if ($($items[$i]).prev().length > 0 && parseInt($($items[$i]).attr('data-gs-width')) != parseInt($($items[$i]).prev().attr('data-gs-width'))) {
                        return false;
                    }
                }
            }
            return true;
        },

        showPopupSkin: function (select_callback) {
            var $list_skin = $('#list_skins').clone(true),
                $container = $('<div class="grid-plus-container"></div>'),
                $popup_skin = $('<div class="bg-popup-skins"></div>');
            $('li', $list_skin).removeClass('col-md-3').addClass('col-md-4');
            $container.append($list_skin);
            $popup_skin.append($container);
            $container.append('<a href="javascript:;" class="close-popup"><i class="fa fa-times"></i></a>');
            $('.select-skin i', $container).removeClass('fa fa-check-square-o').addClass('fa fa-square-o');
            $('body').append($popup_skin);

            $('.grid-plus-container .grid-plus-layout', $popup_skin).perfectScrollbar({
                wheelSpeed: 0.5,
                suppressScrollX: true
            });

            $('a.close-popup', $popup_skin).on('click', function () {
                $popup_skin.remove();
            });

            $('a.select-skin', $popup_skin).off('click').on('click', function (e) {
                $popup_skin.remove();

                if (select_callback) {
                    select_callback($(this).closest('.skin-item').attr('data-skin'));
                }

                e.preventDefault();
                return false;

            });
        },

        animation: function () {
            $('#layout_animation_type').on('change', function () {
                var $animation_wrap = $('.animation-wrap');
                $animation_wrap.attr('class', 'animation-wrap animated ' + $(this).val());
                setTimeout(function () {
                    $animation_wrap.attr('class', 'animation-wrap');
                }, 1000);
            });
            $('.preview-animation').on('click', function () {
                $('#layout_animation_type').trigger('change');
            });
        },
        /*
         initEditorChange: function () {
         setTimeout(
         function() {
         if (typeof(tinymce) !== 'undefined') {
         for ( var i = 0; i < tinymce.editors.length; i++ ) {
         GridPlusSetting.editorOnChange(i);
         }
         }
         }, 1000
         );
         },
         editorOnChange: function (i) {
         if (tinymce.editors[i].isChangeEvent == null) {
         tinymce.editors[i].isChangeEvent = true;
         tinymce.editors[i].on('change', function (e) {
         if (e.lastLevel != null) {
         this.save();
         }
         });
         }
         },*/
        execCustomContent: function () {
            var layout = $('#layout_type').val(),
                custom_content_enable = $('#layout_custom_content_enable').is(':checked');
            var skin = $('[data-skin="custom-content"]', '.list-skins');
            if(layout != 'metro' || (custom_content_enable != 'true' && !custom_content_enable)) {
                skin.css('display', 'none');
                if($('.grid-post-item', skin).attr('data-skin-selected') == '1') {
                    $('.select-skin', '.list-skins [data-skin="thumbnail"]').trigger('click');
                }
            } else {
                skin.css('display', 'inline-block');
            }
        }
    };
    $(document).ready(function () {
        GridPlusSetting.init();
    });
})(jQuery);