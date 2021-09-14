var $iaz = jQuery.noConflict();
(function( $iaz ) {
    'use strict';

    $iaz.datetimepicker.setLocale(pcfmefrontend.datetimepicker_lang);


	var datepicker_format = pcfmefrontend.datepicker_format;

    switch(datepicker_format) {
          case "01":
            var dtformat = 'd/m/Y';
          break;
          case "02":
            var dtformat = 'd-m-Y';
          break;
          case "03":
            var dtformat = 'd F Y';
          break;
          case "04":
            var dtformat = 'm/d/Y';
          break;
          case "05":
            var dtformat = 'm-d-Y';
          break;

          case "06":
            var dtformat = 'F d Y';
          break;


          default:
           var dtformat = 'd/m/Y';
    }

    var gdays ='';
    
    if ((pcfmefrontend.days_to_exclude) && (pcfmefrontend.days_to_exclude != '')) {

    	gdays = pcfmefrontend.days_to_exclude.split(',');
    	
    }

    gdays = '['+ gdays +']';
    

	$iaz(function() {


		if (jQuery('.pcfme-datepicker').length) {
			jQuery('.pcfme-datepicker').datetimepicker({
				format:dtformat,
				timepicker:false,
				disabledWeekDays: gdays,
				dayOfWeekStart: pcfmefrontend.dt_week_starts_on
			});
		}

	    var dateToday = new Date(); 
	    if (jQuery('.pcfme-datepicker-disable-past').length) {
	   	    jQuery('.pcfme-datepicker-disable-past').datetimepicker({
	   		    format:dtformat,
	            minDate: dateToday,
	   		    timepicker:false,
	   		    disabledWeekDays: gdays,
				dayOfWeekStart: pcfmefrontend.dt_week_starts_on
	   	    });
	    }



	   if (jQuery('.pcfme-daterangepicker').length) {
		  jQuery('.pcfme-daterangepicker').dateRangePicker({
            format: 'DD/MM/YYYY',
            language: pcfmefrontend.datetimepicker_lang,
            startOfWeek:pcfmefrontend.week_starts_on
            
          });
	   }

	   var dateToday = new Date(); 
	   if (jQuery('.pcfme-daterangepicker-disable-past').length) {
		  jQuery('.pcfme-daterangepicker-disable-past').dateRangePicker({
            format: 'DD/MM/YYYY',
		    startDate: dateToday,
		    language: pcfmefrontend.datetimepicker_lang,
		    startOfWeek:pcfmefrontend.week_starts_on
		    
          });
	   }



	    if (jQuery('.pcfme-datetimerangepicker').length) {
		  jQuery('.pcfme-datetimerangepicker').dateRangePicker({
                separator : ' - ',
                format: 'DD/MM/YYYY HH:mm',
                language: pcfmefrontend.datetimepicker_lang,
                startOfWeek:pcfmefrontend.week_starts_on,
                time: {
		          enabled: true
	            }
            
          });
	    }

	    var dateToday = new Date(); 
	    if (jQuery('.pcfme-datetimerangepicker-disable-past').length) {

		    jQuery('.pcfme-datetimerangepicker-disable-past').dateRangePicker({
                separator : ' - ',
                format: 'DD/MM/YYYY HH:mm',
		        startDate: dateToday,
		        language: pcfmefrontend.datetimepicker_lang,
		        startOfWeek:pcfmefrontend.week_starts_on,
		        time: {
		         enabled: true
	            }
		    
            });
	    }
        

        
	   
    });


	$iaz(function() {
		if (jQuery('.pcfme-datetimepicker').length) {
			jQuery('.pcfme-datetimepicker').datetimepicker({
				format:''+ dtformat + ' H:i',
				dayOfWeekStart: pcfmefrontend.dt_week_starts_on
			});
		}

		var dateToday = new Date(); 
		if (jQuery('.pcfme-datetimepicker-disable-past').length) {
			jQuery('.pcfme-datetimepicker-disable-past').datetimepicker({
				minDate: dateToday,
				format:''+ dtformat + ' H:i',
				dayOfWeekStart: pcfmefrontend.dt_week_starts_on
			});
		}

	});


	$iaz(function() {
		if (jQuery('.pcfme-timepicker').length) {

			jQuery('.pcfme-timepicker').datetimepicker({
				format:'H:i',
				datepicker:false,
				dayOfWeekStart: pcfmefrontend.dt_week_starts_on,
				step:pcfmefrontend.timepicker_interval,
				allowTimes: function getArr() {

					if ((pcfmefrontend.allowed_times) && (pcfmefrontend.allowed_times != '')) {

						var gTimes = pcfmefrontend.allowed_times.split(',');
						return gTimes;


					}

				}()

			});


		}
	   
	});


	
   	
	$iaz(function() {

		if ($iaz('.pcfme-multiselect').length) {
			$iaz('.pcfme-multiselect').select2({});
		}

		if ($iaz('.pcfme-singleselect').length) {
			$iaz('.pcfme-singleselect').select2({});
		}

		if ($iaz('.parent_hidden').length) {
			$iaz('.parent_hidden').hide();
		}

    });
	
	
	$iaz(function() {
       $iaz('.pcfme-opener').on('change',function(){
		 var this_obj=$iaz(this);
		 var id= this_obj.attr('id');
         var name= this_obj.attr('name');
         var uval = this_obj.val();
		 

		 if (this_obj.hasClass('pcfme-singleselect')){
			
			
			$iaz('.open_by_'+ id +'_'+ uval).closest('.form-row').show();
                        //hide other   
            $iaz("[class^='open_by_"+ id +"_'],[class*=' open_by_"+ id +"_']").not('.open_by_'+ id +'_'+uval).closest('.form-row').hide();
			
		 } else if (this_obj.hasClass('pcfme-multiselect')){
			
			
			$iaz('.open_by_'+ id +'_'+uval ).closest('.form-row').show();
                        //hide other   
            $iaz("[class^='open_by_"+ id +"_'],[class*=' open_by_"+ id +"_']").not('.open_by_'+ id +'_'+uval).closest('.form-row').hide();
			
		 } else if (this_obj.attr('type')=='checkbox') {
			
			if (this_obj.is(':checked')) {
				$iaz('.open_by_'+id ).closest('.form-row').show();
			} else {
				$iaz('.open_by_'+id ).closest('.form-row').hide();
			}
			
		 } else if ( this_obj.attr('type')=='radio'){
                
                $iaz('.open_by_'+ id +'_'+uval ).closest('.form-row').show();
                        //hide other   
                $iaz("[class^='open_by_"+ id +"_'],[class*=' open_by_"+ id +"_']").not('.open_by_'+ id +'_'+uval).closest('.form-row').hide();				
                       
          } else if ( this_obj.attr('type')=='text'){
			    $iaz('.open_by_'+ id +'_'+uval ).closest('.form-row').show();
                        //hide other   
                 $iaz("[class^='open_by_"+ id +"_'],[class*=' open_by_"+ id +"_']").not('.open_by_'+ id +'_'+uval).closest('.form-row').hide();
		  
		  } else if ( this_obj.attr('type')=='tel'){
			     
			     $iaz('.open_by_'+ id +'_'+uval ).closest('.form-row').show();
                        //hide other   
                 $iaz("[class^='open_by_"+ id +"_'],[class*=' open_by_"+ id +"_']").not('.open_by_'+ id +'_'+uval).closest('.form-row').hide();
		  } else if ( this_obj.attr('type')=='number'){
			     
			     $iaz('.open_by_'+ id +'_'+uval ).closest('.form-row').show();
                        //hide other   
                 $iaz("[class^='open_by_"+ id +"_'],[class*=' open_by_"+ id +"_']").not('.open_by_'+ id +'_'+uval).closest('.form-row').hide();
		  } else if ( this_obj.attr('type')=='password'){
			     
			     $iaz('.open_by_'+ id +'_'+uval ).closest('.form-row').show();
                        //hide other   
                 $iaz("[class^='open_by_"+ id +"_'],[class*=' open_by_"+ id +"_']").not('.open_by_'+ id +'_'+uval).closest('.form-row').hide();
		  } else if (this_obj.is("textarea")){
			     
			     $iaz('.open_by_'+ id +'_'+uval ).closest('.form-row').show();
                        //hide other   
                 $iaz("[class^='open_by_"+ id +"_'],[class*=' open_by_"+ id +"_']").not('.open_by_'+ id +'_'+uval).closest('.form-row').hide();
		  }
	    
		
      });
	  
	  $iaz('.pcfme-opener').trigger('change');
	  
	  $iaz('.pcfme-hider').on('change',function(){
		   var this_obj=$iaz(this);
           var id= this_obj.attr('id');
           var name= this_obj.attr('name');
           var hval = this_obj.val();
		   
		   if (this_obj.hasClass('pcfme-singleselect')){
                        
                        $iaz('.hide_by_'+ id +'_'+hval ).closest('.form-row').hide();
                        //hide other   
                        $iaz("[class^='hide_by_"+ id +"_'],[class*=' hide_by_"+ id+"_']").not('.hide_by_'+ id +'_'+hval).closest('.form-row').show();
                         
            } else if (this_obj.hasClass('pcfme-multiselect')){
                        
                        $iaz('.hide_by_'+ id +'_'+hval ).closest('.form-row').hide();
                        //hide other   
                        $iaz("[class^='hide_by_"+ id +"_'],[class*=' hide_by_"+ id+"_']").not('.hide_by_'+ id +'_'+hval).closest('.form-row').show();
                         
            } else if (this_obj.attr('type')=='checkbox') {
			
			  if (this_obj.is(':checked')) {
				
				$iaz('.hide_by_'+id ).closest('.form-row').hide();
			  } else {
				    
				$iaz('.hide_by_'+id ).closest('.form-row').show();
			  }
		    
			} else if ( this_obj.attr('type')=='radio'){
                         
                $iaz('.hide_by_'+ id +'_'+hval ).closest('.form-row').hide();
                        //hide other   
                $iaz("[class^='hide_by_"+ id +"_'],[class*=' hide_by_"+ id+"_']").not('.hide_by_'+ id +'_'+hval).closest('.form-row').show();       
            } else if ( this_obj.attr('type')=='text'){
                         
                $iaz('.hide_by_'+ id +'_'+hval ).closest('.form-row').hide();
                        //hide other   
                $iaz("[class^='hide_by_"+ id +"_'],[class*=' hide_by_"+ id+"_']").not('.hide_by_'+ id +'_'+hval).closest('.form-row').show();       
            
			} else if ( this_obj.attr('type')=='tel'){
                         
                $iaz('.hide_by_'+ id +'_'+hval ).closest('.form-row').hide();
                        //hide other   
                $iaz("[class^='hide_by_"+ id +"_'],[class*=' hide_by_"+ id+"_']").not('.hide_by_'+ id +'_'+hval).closest('.form-row').show();       
            }  else if ( this_obj.attr('type')=='number'){
                         
                $iaz('.hide_by_'+ id +'_'+hval ).closest('.form-row').hide();
                        //hide other   
                $iaz("[class^='hide_by_"+ id +"_'],[class*=' hide_by_"+ id+"_']").not('.hide_by_'+ id +'_'+hval).closest('.form-row').show();       
            }  else if ( this_obj.attr('type')=='password'){
                         
                $iaz('.hide_by_'+ id +'_'+hval ).closest('.form-row').hide();
                        //hide other   
                $iaz("[class^='hide_by_"+ id +"_'],[class*=' hide_by_"+ id+"_']").not('.hide_by_'+ id +'_'+hval).closest('.form-row').show();       
            }   else if (this_obj.is("textarea")) {
                         
                $iaz('.hide_by_'+ id +'_'+hval ).closest('.form-row').hide();
                        //hide other   
                $iaz("[class^='hide_by_"+ id +"_'],[class*=' hide_by_"+ id+"_']").not('.hide_by_'+ id +'_'+hval).closest('.form-row').show();       
            }
	  });
	  
	   $iaz('.pcfme-hider').trigger('change');
	});
})(jQuery);