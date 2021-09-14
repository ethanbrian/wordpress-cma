var GF_PFUI = GF_PFUI || {};
(function ($) {
	"use strict";
	GF_PFUI = {
		init : function() {
			GF_PFUI.gallery();
			setTimeout(function () {
				$('.editor-post-format select').trigger('change');
				$('[name="post_format"]:checked').trigger('change')
			},1000);

			$(document).on('change','.editor-post-format select',function (event) {
				GF_PFUI.switch_post_format_content($(this).val());
			});

			$('[name="post_format"]').on('change',function(){
				GF_PFUI.switch_post_format_content($(this).val());
			});


		},
		switch_post_format_content : function($post_format) {
			$('.tab-pane','.gf-post-formats-ui-tabs').removeClass('active');
			$('#tab-post-format-' + $post_format).addClass('active');
		},
		gallery: function() {
			$('.sf-field-gallery-inner','.gf-post-formats-ui-tabs').each(function () {
				var field = new SF_GalleryClass($(this));
				field.init();
			});
		}
	};
	$(document).ready(GF_PFUI.init);
})(jQuery);
