(function( $ ) {
'use strict';
// if a status is changed in the recent squawk list grab the id and updated status
// and submit back to itself to update the record. 	
  $(function(){
 	 $(".status_change").change(function(){	
  		//var valueSelect = $(this).val();
        var id = $(this).parent().parent().children('#id').text();
  		var str = "";
   		$( "select option:selected" ).each(function() {
      		str += $( this ).val() + " ";
    	});
    	$.post("#", {id: id, status: str});
 	 })	 
   });	  
})( jQuery );	 
	 