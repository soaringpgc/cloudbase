(function( $ ) {
'use strict';
// if a status is changed in the recent squawk list grab the id and updated status
// and submit back to itself to update the record. 	
  $(function(){
  			var id =0;
  		 $("#pilotid").on("change", function(event){
  			id = $("#pilotid").find(":selected").val();
  			$("#member_id").val(id);
  			$("#ipilotid").val("");
  			 fetch_user(id);
  		});
  		 $("#ipilotid").on("change", function(event){
  		 	id = $("#ipilotid").find(":selected").val();
  		 	$("#member_id").val(id );
//    			alert("inactive member selected" + " " + $("#ipilotid").find(":selected").val());
  			$("#pilotid").val("");
  			 fetch_user(id);
  		});
  		
  		function fetch_user(id){		
  			var statusData ={ 'context': "edit"};				
  			$.ajax({
       		 	url:  signoff_public_vars.restURL + 'wp/v2/users/' +id,  
       		 	beforeSend: function (xhr) {
       		 		xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        
       		 		},
				method: "GET",
				data: statusData,
				success : function (response){
				    var roles = response.roles ;

				    $('input[name="member_status"][value="none"]').prop('checked', true);
				    $('input[name="duty"][value="exempt"]').prop('checked', true);
					$( "#cfi_g" ).prop( "checked", false );
					$( "#towpilot" ).prop( "checked", false );
					$( "#board" ).prop( "checked", false );


					$( "#treasurer" ).prop( "checked", false );
					$( "#chief_of_ops" ).prop( "checked", false );
					$( "#operations" ).prop( "checked", false );
					$( "#schedule_assist" ).prop( "checked", false );
					$( "#chief_flight" ).prop( "checked", false );
					$( "#chief_tow" ).prop( "checked", false );
					$( "#cfig_scheduler" ).prop( "checked", false );
					$( "#tow_scheduler" ).prop( "checked", false );
					roles.forEach (( element) => {
				   		switch(element){
				   			case "subscriber": 				      			
            					$('input[name=member_status][value="subscriber"]').prop('checked', true);
				   				break;
				   			case "inactive":
				   				$('input[name=member_status][value="inactive"]').prop('checked', true);
				   				break;
				   			case "candidate":
				   				$('input[name=member_status][value="canidate"]').prop('checked', true);
				   				break;
				   			case "field_manager":
				   				$('input[name=duty][value="fm"]').prop('checked', true);
				   				break;
				   			case "assistant_field_manager":
				   				$('input[name=duty][value="afm"]').prop('checked', true);
				   				break;
				   			case "cfi_g":
				   				$( "#cfi_g" ).prop( "checked", true );
				   				break;				      			
				   			case "tow_pilot":
				   				$( "#towpilot" ).prop( "checked", true );
				   				break;				      			
				   			case "board_member":
				   				$( "#board" ).prop( "checked", true );
				   				break;	
///////---------------
				   			case "treasurer":
				   				$( "#treasurer" ).prop( "checked", true );
				   				break;				      			
				   			case "chief_of_ops":
				   				$( "#chief_of_ops" ).prop( "checked", true );
				   				break;				      			
				   			case "operations":
				   				$( "#operations" ).prop( "checked", true );
				   				break;	
//-------------------				   				
				   			case "schedule_assist":
				   				$( "#schedule_assist" ).prop( "checked", true );
				   				break;				      			
				   			case "chief_flight":
				   				$( "#chief_flight" ).prop( "checked", true );
				   				break;				      			
				   			case "chief_tow":
				   				$( "#chief_tow" ).prop( "checked", true );
				   				break;	
//------------				   				
				   			case "cfig_scheduler":
				   				$( "#cfig_scheduler" ).prop( "checked", true );
				   				break;				      			
				   			case "tow_scheduler":
				   				$( "#tow_scheduler" ).prop( "checked", true );
				   				break;					   								      			
				   		}
				   	 });
				},
				fail : function( response ) {
 				    console.log( response );
				    alert( 'Something went wrong.' );   
				}			       
			});	 				
  		}		
   });	  
})( jQuery );	