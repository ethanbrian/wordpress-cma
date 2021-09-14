<?php
echo '<h3>' . get_the_title( $plan_id ) . '</h3>' ;
$user_id = get_current_user_id() ;
$post_id = sumo_get_member_post_id( $user_id ) ;
if ( WC()->version < 3.0 ) {
    $select_two = '<input type="text" name="sumo_select_user" id="sumo_select_user" value="">' ;
} else {
    $select_two = '<select name="sumo_select_user" style="width:300px" id="sumo_select_user"></select>' ;
}
echo '<p>'
 . __( 'Select Users' , 'sumomemberships' ) . ''
 . $select_two . '</p><p>'
 . '<input type="button" id="sumo_link_users_for_smp" class="button" value="' . __( 'Link' , 'sumomemberships' ) . '"></p>' ;

$linked_users = get_post_meta( $post_id , 'sumo_linked_users_of_' . $plan_id , true ) ;
echo '<h4>' . __( 'Linked Users' , 'sumomemberships' ) . '</h4>' ;
if ( is_array( $linked_users ) && ! empty( $linked_users ) ) {
    echo '<table class="shop_table shop_table_responsive my_account_orders">'
    . '<thead>'
    . '<tr>'
    . '<th>' . __( 'Name' , '' ) . '</th>'
    . '<th>' . __( 'Email' , '' ) . '</th>'
    . '<th></th>'
    . '</tr>'
    . '</thead>'
    . '<tbody>' ;

    foreach ( $linked_users as $eachlinkeduser ) {
        if ( $eachlinkeduser ) {
            $userdata = get_userdata( $eachlinkeduser ) ;
            if ( is_object( $userdata->data ) ) {
                echo '<tr>'
                . '<td>' . $userdata->data->user_login . '</td>'
                . '<td>' . $userdata->data->user_email . '</td>'
                . '<td><input type="button" class="sumo_remove_linked_user_from_smp_myaccount button" data-post_id="' . $post_id . '" data-plan_id="' . $plan_id . '" data-user_id="' . $eachlinkeduser . '" value="' . __( 'Remove' , '' ) . '"></td>'
                . '</tr>' ;
            }
        }
    }
    echo '</tbody>'
    . '</table>' ;
} else {
    echo __( 'No Users linked with this plan' , 'sumomemberships' ) ;
}
?>
<script type="text/javascript">
    jQuery( document ).ready( function () {
<?php if ( WC()->version < 3.0 ) { ?>
            jQuery( "#sumo_select_user" ).select2( {
                placeholder : "Enter atleast 3 characters" ,
                allowClear : true ,
                enable : false ,
                maximumSelectionSize : 1 ,
                readonly : false ,
                multiple : false ,
                initSelection : function ( data , callback ) {
                    var data_show = {
                        id : data.val() ,
                        text : data.attr( 'data-selected' )
                    } ;
                    if ( data.val() > 0 ) {
                        return callback( data_show ) ;
                    }
                } ,
                minimumInputLength : 3 ,
                tags : [ ] ,
                ajax : {
                    url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                    dataType : 'json' ,
                    type : "GET" ,
                    quietMillis : 250 ,
                    data : function ( term ) {
                        return {
                            term : term ,
                            action : "sumo_search_wordpress_users"
                        } ;
                    } ,
                    results : function ( data ) {
                        var terms = [ ] ;
                        if ( data ) {
                            jQuery.each( data , function ( id , text ) {
                                terms.push( {
                                    id : id ,
                                    text : text
                                } ) ;
                            } ) ;
                        }
                        return { results : terms } ;
                    }
                }
            } ) ;
<?php } else {
    ?>
            jQuery( "#sumo_select_user" ).select2( {
                placeholder : "Enter atleast 3 characters" ,
                allowClear : true ,
                minimumInputLength : 3 ,
                escapeMarkup : function ( m ) {
                    return m ;
                } ,
                ajax : {
                    url : '<?php echo admin_url( 'admin-ajax.php' ) ; ?>' ,
                    dataType : 'json' ,
                    quietMillis : 250 ,
                    data : function ( params ) {
                        return {
                            term : params.term ,
                            action : 'sumo_search_wordpress_users'
                        } ;
                    } ,
                    processResults : function ( data ) {
                        var terms = [ ] ;
                        if ( data ) {
                            jQuery.each( data , function ( id , text ) {
                                terms.push( {
                                    id : id ,
                                    text : text
                                } ) ;
                            } ) ;
                        }
                        return {
                            results : terms
                        } ;
                    } ,
                    cache : true
                }
            } ) ;
<?php }
?>

        jQuery( '#sumo_link_users_for_smp' ).click( function () {
            var users = jQuery( '#sumo_select_user' ).val() ;
            if ( users ) {
                var dataparam = ( {
                    action : 'sumo_link_membership_plan_from_myaccount_page' ,
                    plan_id : "<?php echo $plan_id ; ?>" ,
                    users : users ,
                    postid : "<?php echo $post_id ; ?>"
                } ) ;
                jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function ( response ) {
                    if ( response ) {
                        window.location.reload( true ) ;
                    }
                } ) ;
            }
        } ) ;

        jQuery( '.sumo_remove_linked_user_from_smp_myaccount' ).click( function () {
            var user_id = jQuery( this ).attr( 'data-user_id' ) ;
            var plan_id = jQuery( this ).attr( 'data-plan_id' ) ;
            var post_id = jQuery( this ).attr( 'data-post_id' ) ;
            var dataparam = ( {
                action : 'sumo_unlink_membership_plan' ,
                user_id : user_id ,
                plan_id : plan_id ,
                post_id : post_id
            } ) ;
            jQuery.post( "<?php echo admin_url( 'admin-ajax.php' ) ; ?>" , dataparam , function ( response ) {
                if ( response ) {
                    window.location.reload( true ) ;
                }
            } ) ;
        } ) ;
    } ) ;
</script>
<?php
