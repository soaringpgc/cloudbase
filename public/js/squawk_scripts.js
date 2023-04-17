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
      		str += $( this ).val();
    	});
    	var statusData ={'id': id, 'status': str};
		if( str == "DELETE"){
				$.ajax({
         		url:  signoff_public_vars.restURL + 'cloud_base/v1/squawks',  
         		beforeSend: function (xhr) {
         			xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        
         		},
				method: 'DELETE',
		   	 		data: statusData,
		   	 		success : function( response ) {
		   	    	window.location.reload(true);
		   	 },
		   	 fail : function( response ) {
// 		   	    console.log( response );
		   	    alert( 'Something went wrong.' );   
		   	}			       
			});
		} else {			
			$.ajax({
        	 	url:  signoff_public_vars.restURL + 'cloud_base/v1/squawks',  
        	 	beforeSend: function (xhr) {
        	 		xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        
        	 	},
					method: "PUT",
			    	data: statusData,
			    	success : function( response ) {
			       window.location.reload(true);
			    },
			    fail : function( response ) {
// 			       console.log( response );
			       alert( 'Something went wrong.' );   
			   }			       
			});	
 		}	
 	 });
   });	  
})( jQuery );	 
	 