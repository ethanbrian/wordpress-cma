/* global sumo_membership_previous_order_tab_obj, ajaxurl */

jQuery( function ( $ ) {

    var previous_count ;

    var Previous_Orders_Tab = {
        init : function () {
            this.trigger_on_page_load() ;
            $( '.sm_date' ).datepicker( { dateFormat : 'yy-mm-dd' } ) ;
            $( 'p.submit' ).hide() ;
            $( document ).on( 'change' , '#sm_order_time' , this.toggle_time_format_selection ) ;
            $( document ).on( 'click' , '#sm_update_order' , this.check_privous_odrers ) ;
        } ,
        trigger_on_page_load : function () {
            $( '#sm_specific_row' ).css( 'display' , 'none' ) ;
        } ,
        toggle_time_format_selection : function ( event ) {
            event.preventDefault() ;
            var $this = $( event.currentTarget ) ;
            if ( $( $this ).val() == 'specific' ) {
                $( '#sm_specific_row' ).css( 'display' , 'table-row' ) ;
            } else {
                $( '#sm_specific_row' ).css( 'display' , 'none' ) ;
            }
        } ,
        check_privous_odrers : function ( event ) {
            event.preventDefault() ;
            $( '.perloader_image' ).show() ;
            $( "#sm_update_order" ).prop( 'disabled' , true ) ;
            var mycount ;
            var order_time = $( '#sm_order_time' ).val() ;
            var from_time = $( '#from_time' ).val() ;
            var to_time = $( '#to_time' ).val() ;

            var dataparam = ( {
                action : 'sm_add_old_order' ,
                sm_order_time : order_time ,
                sm_from_time : from_time ,
                sm_to_time : to_time

            } ) ;

            $.post( ajaxurl , dataparam ,
                    function ( response ) {
                        if ( response !== 'success' ) {
                            var j = 1 ;
                            var i , j , temparray , chunk = parseFloat( sumo_membership_previous_order_tab_obj.sm_chunk_count ) ;
                            for ( i = 0 , j = response.length ; i < j ; i += chunk ) {
                                temparray = response.slice( i , i + chunk ) ;
                                Previous_Orders_Tab.get_data_form_orders( temparray ) ;
                            }

                            $.when( Previous_Orders_Tab.get_data_form_orders( '' ) ).done( function ( a1 ) {
                                $( '#sm_update_order' ).prop( 'disabled' , false ) ;
                            } ) ;
                        } else {
                            var newresponse = response.replace( /\s/g , '' ) ;
                            if ( newresponse === 'success' ) {
                                $( '.submit .button-primary' ).trigger( 'click' ) ;
                            }
                        }
                    } , 'json' ) ;
        } ,
        get_data_form_orders : function ( id ) {
            return $.ajax( {
                type : 'POST' ,
                url : ajaxurl ,
                data : ( {
                    action : 'sm_chunk_previous_order_list' ,
                    ids : id ,
                } ) ,
                success : function ( response ) {
                    if ( response ) {
                        previous_count = response.count ;
                        $( '.perloader_image' ).hide() ;
                        if ( previous_count > 0 ) {
                            $( '#update_response' ).append( previous_count + ' ' + sumo_membership_previous_order_tab_obj.sm_updated_count ) ;
//                            setTimeout( function () {
//                                location.reload()
//                            } , '3500' ) ;
                        } else {
                            $( '#update_response' ).append( sumo_membership_previous_order_tab_obj.sm_empty_order_message ) ;
//                            setTimeout( function () {
//                                location.reload()
//                            } , '3500' ) ;
                        }
                    }
                } ,
                dataType : 'json' ,
                async : false
            } ) ;
        }
    } ;
    Previous_Orders_Tab.init() ;
} ) ;