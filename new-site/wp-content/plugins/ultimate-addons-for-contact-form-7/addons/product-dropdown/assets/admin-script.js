jQuery( document ).ready( function(){
    
    jQuery('.tag-generator-panel-product-category').hide();
    jQuery('.tag-generator-panel-product-category #tag-generator-panel-product-category').attr('name','');
    
    jQuery( 'input[name="product_by"]' ).on('change', function(){
        var product_by = jQuery( this ).val();

        if( product_by == 'id' ){

            jQuery('.tag-generator-panel-product-category').hide();
            jQuery('.tag-generator-panel-product-category #tag-generator-panel-product-category').attr('name','');

            jQuery('.tag-generator-panel-product-id').show();
            jQuery('.tag-generator-panel-product-id #tag-generator-panel-product-id').attr('name','values');

        }else {

            jQuery('.tag-generator-panel-product-category').show();
            jQuery('.tag-generator-panel-product-category #tag-generator-panel-product-category').attr('name','values');

            jQuery('.tag-generator-panel-product-id').hide();
            jQuery('.tag-generator-panel-product-id #tag-generator-panel-product-id').attr('name','');

        }
    });
    
} );
