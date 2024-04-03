(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
         * 
         * The file is enqueued from inc/frontend/class-frontend.php.
	 */
	 $(function(){
	 
// 	 $(".calendar").click(function(){
//   		 $('.calendar').datepicker({dateFormat : 'yy-mm-dd'}); 
// //		 $('.calendar').datepicker("setDate", new Date());
//   	 });
// 	 $(document).on('click', ".calendar" , function(e){
//   		 $('.calendar').datepicker({dateFormat : 'yy-mm-dd'}); 
//   	 });

 $(".calendar").click(function() {
        $(this).datepicker().datepicker( "show", {dateFormat : 'yy-mm-dd'} )
    });


  	 $("#pilot_to_update").change(function(){	
  		var valueSelect = $(this).val();
  		fetch_pilot_signoffs(valueSelect);
  		cancel_update() ;		
	 })	  
	 $("#signOffToUpdate").change(function(){	
  		var valueSelect = $(this).val();
  		$('#signoff').val(valueSelect);
  		fetch_signoff_pilots(valueSelect);
  		cancel_update() ;		
	 })	 	 
	 
	 // load member data for self sign up page, Pilot_to_update will already be assigned. 
	 $(function() {
 		var valueSelect = $("#pilot_to_update").val();
 		if (valueSelect != 0 ){
  			fetch_pilot_signoffs(valueSelect);
  			cancel_update() ;	
  		}
	 })
//  After a pilot has been selected and a signoff has beed selected to update
//  this function populates the fields in preparation for the update.
//	 It populates the hidden record field with the signoff record number. It displays the
//   signoff type and hides the field to the sighoff can not be changes. It also displayes
//   the hidden delete field should the signoff need to be deleted. 
// 	
  	$("#responsecontainer").on('click' , 'input[name="tobeupdated-s"]', function(){
    	if ($(this).is(':checked')){
		// The radio button has been given the value of the mySQL row ID -- returned in $(this).val()
		// We next get the element of every item in the row 		
      		var signoffu = $(this).closest('div').next('div');      		
		//     We now fill in the value in the form 	
		    $("#record_id").val($(this).val()); 	
    		$("#signoff_type").hide();
    		$("#update_signoff").text(signoffu.text());
    		$("#update_signoff").css({'display': 'inline-block' ,  'text-decoration': 'underline', "font-weight": "bold"});

    		$(".show_delete").css({'display': 'inline-block',  "visibility": "visible" });
    			
 		//  In the handler for the form submit if the record_id is null we create a new record
 		//  if it is set we update the existing.  
    	
			// if user changes their mind with cancel.....
//       		if ($(this).val() != "cancel") {
			// and we change the submit button to  Update      
      			$("#submit").val("Update Changes");
//       		} else {    
//       			cancel_update() ;
//       		}
      	}
  	});		
  	$('#cancel').on('click', function(){ 
//     alert("button is clicked");
    restore_page_settings();
    });
// this function submits the update signoffs form to the REST endpoint It determines
// if it is an update, a delete or create new.   	
  	 $( '#update_signoffs').on( 'submit', function(e) { 	 
    	e.preventDefault();
    	var tobe_deleted = $( '#delete_signoff' ).val();
    	var record_id = $( '#record_id' ).val();
        var pilot =  $( '#pilot_to_update' ).val();
        var signoff = $( '#signoff_type' ).val();
        var authority = $( '#signatory' ).val();
        var selfsignoff = $( '#selfsignoff' ).val();
        var effective_date = $( '#effective_date' ).val();
        var data = {
            member_id: pilot,
            authority_id: authority,
            signoff_id: signoff,
            effective_date: effective_date,
            record_id: record_id
        };   // are we deleting an old record.    
//         console.log (data);
		if ($('#delete_signoff').prop('checked')){
			if( confirm("you realy want to delete?")){
				 $.ajax({
         			url:  signoff_public_vars.restURL + 'cloud_base/v1/sign_off',  
           			beforeSend: function (xhr) {
           				xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        
           			},
					method: "DELETE",
			        data: data,
			        success : function( response ) {
// 			           console.log( response );
			           alert('Sign Off Deleted.');
			           restore_page_settings();
			           fetch_pilot_signoffs(pilot);
			        },
			        fail : function( response ) {
// 			           console.log( response );
			           alert( response );
			           restore_page_settings();
			       }
			       
			   	});		 	 	
			} else{
				alert("never mind. ");
			}
		} else if ( record_id != "" ){ // update record
// 			alert ("updating record" + record_id);
			$.ajax({
         		url:  signoff_public_vars.restURL + 'cloud_base/v1/sign_off',  
            	beforeSend: function (xhr) {
            		xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        
            	},
            	method: "PUT",
        		data: data,
        		dataType : 'json',
            	success : function( response ) {
//                 	console.log( response );
            	    alert( 'success');
            	    restore_page_settings();
            	    fetch_pilot_signoffs(pilot);
           	 	},
            	fail : function( response ) {
//                 	console.log( response );
                	alert( response );
                	restore_page_settings();
            	}
        	});
		} else {      // must be creating a new record.  
// 		alert ("adding record");
        	$.ajax({      	
            	method: "POST",
         		url:  signoff_public_vars.restURL + 'cloud_base/v1/sign_off',  
           		 beforeSend: function (xhr) {
           		 	xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        
           		 },
        		 data: data,
        		 dataType : 'json',
            	 success : function( response ) {
//                 	console.log( response );
            	    alert( 'Signoff added' );
            	    restore_page_settings();
            	    fetch_pilot_signoffs(pilot);
           	 	 },
            	 error : function( response ) {
//                 	console.log( response );
                	alert( response);
                	restore_page_settings();
            	}
        	});
        };
        // updated pilot data, refresh the displayed signoffs. 
        fetch_pilot_signoffs(pilot)
    });
        
   // this function fetches the sign offs for individual members. 
   // It currently uses admin-ajax.php, but I need to update it to use REST.  
   function fetch_pilot_signoffs(pilot_to_update){	
  		// signatory will have the ID of the person signing off 
  		$('#signoff_form').removeClass('signoff_state');
  		$('#select_header').removeClass('signoff_state'); 
  		var signatory = $("#signatory").val();
  		var selfsignoff = $("#selfsignoff").val();
  		$.ajax({
         	url:  signoff_public_vars.restURL + 'cloud_base/v1/sign_off',  
            beforeSend: function (xhr) {
            	xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        //, _wpnonce: signoff_public_vars.nonce
            },																		// , current_user: signoff_public_vars.current_user_id 
            type:   'GET', 
        	data: { member_id: pilot_to_update, update: '1'},
        	dataType : 'json'
         })
  		.success( function( response ) { // response from the PHP action
                $(" #responsecontainer ").html(display_signoffs(response)
                 ); 
//                 console.log(response);
        })                  
        // something went wrong  
         .fail( function(response) {
         console.log(response);
        	  $(" #responsecontainer ").html( "<h2>Something went wrong.</h2><br>" );                  
        }) 		
  	}
  	function display_signoffs(response){
  	  	  var today = new Date();
  	      var html_string =  '<div class="div-table"><div class="div-table-row">'  
  	         +  ' <div  class="div-table-col0 hform">select</div>'
             +  ' <div  class="div-table-col160 hform" align="center">Sign Off</div>'
//              +  ' <div  class="div-table-col125 hform" align="center">Authority</div>'
             +  ' <div  class="div-table-col125">Expire Date</div>'
  	      	 + '</div>';
	 
            Object.keys( response ).forEach(key=>{
            	if( new Date(response[key].date_expire)  < today ){
            		html_string = html_string + '<div class="div-table-row Row-red">';
            	} else {
            		html_string = html_string + '<div class="div-table-row">';
            	}
//             	html_string = html_string + '<div class="div-table-row">';
//             	if ( signoff_public_vars.user_can == response[key].authority ){
            	html_string = html_string + '<div class="div-table-col0 hform" ><input type="radio" name="tobeupdated-s" value="' + response[key].id + '"/></div>';
				
				html_string = html_string + '<div class="div-table-col160 hform"  >'+ response[key].signoff_type + '</div>';
// 				html_string = html_string + '<div class="div-table-col125 hform"  >'+ response[key].authority  +  '</div>';						
				html_string = html_string + '<div class="div-table-col125b" >' + response[key].date_expire.substring(0,10) +  '</div></div>';
            });
//             html_string = html_string +' <div class="div-table-row"><div class="div-table-col160"><input type="submit" value="cancel" /></div></div></div> ';
			return(html_string );  		
  	}



// this function submits the update signoffs form to the REST endpoint It determines
// if it is an update, a delete or create new.   	
  	 $( '#batch_signoffs').on( 'submit', function(e) { 	 
    	e.preventDefault();
    	var checked = []
			$("input[name='tobeupdated[]']:checked").each(function ()
			{
			    checked.push(parseInt($(this).val()));
			});
        var signoff = $( '#signoff' ).val();
        var authority = $( '#signatory' ).val();
        var effective_date = $( '#effective_date' ).val();
		checked.forEach (function(record_id){
			$.ajax({
       			url:  signoff_public_vars.restURL + 'cloud_base/v1/sign_off',  
       			beforeSend: function (xhr) {
       				xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        
       			},
       			method: "PUT",
       			data: {
       			    authority_id: authority,
       				signoff_id: signoff,
       				effective_date: effective_date,
       				record_id: record_id      		
       			},
       	   	 cache: false,
       			dataType : 'json',
       			success : function( response ) {
//     	   	    	console.log( response );
//        			    alert( response);
//     	   		    restore_page_settings();
//     	   		    fetch_pilot_signoffs(pilot);
       	 		},
       			error : function(XMLHttpRequest, textStatus, errorThrown) { 
       					alert("Status: " + textStatus); alert("Error: " + errorThrown); 
//     	   	    	restore_page_settings();
       			}
       		});
        	
        });
        // updated pilot data, refresh the displayed signoffs. 
        fetch_signoff_pilots(signoff)
    });  	
  	function fetch_signoff_pilots(signOffToUpdate){	
  		// signatory will have the ID of the signoff 
//   		$('#signoff_form').removeClass('signoff_state');
//   		$('#select_header').removeClass('signoff_state'); 
  		var signatory = $("#signatory").val();
  		var selfsignoff = $("#selfsignoff").val();
  		$.ajax({
         	url:  signoff_public_vars.restURL + 'cloud_base/v1/sign_off',  
            beforeSend: function (xhr) {
            	xhr.setRequestHeader ('X-WP-Nonce', signoff_public_vars.nonce );        //, _wpnonce: signoff_public_vars.nonce
            },																		// , current_user: signoff_public_vars.current_user_id 
            type:   'GET', 
        	data: { batch: signOffToUpdate},
        	dataType : 'json'
         })
  		.success( function( response ) { // response from the PHP action
                $(" #batchcontainer ").html(batch_display_signoffs(response)
                 ); 
//                 console.log(response);
        })                  
        // something went wrong  
         .fail( function(response) {
         console.log(response);
        	  $(" #batchcontainer ").html( "<h2>Something went wrong.</h2><br>" );                  
        }) 		
  	}
  	  	
  	function batch_display_signoffs(response){
  		  var today = new Date();
  	      var html_string =  '<div class="div-table"><div class="div-table-row">'  
  	         +  ' <div  class="div-table-col0 hform">select</div>'
             +  ' <div  class="div-table-col160 hform" align="center">Pilot</div>'
//              +  ' <div  class="div-table-col125 hform" align="center">Authority</div>'
             +  ' <div  class="div-table-col125">Expire Date</div>'
  	      	 + '</div>';
  	      	  
            Object.keys( response ).forEach(key=>{
            if( new Date(response[key].date_expire)  < today ){
            	html_string = html_string + '<div class="div-table-row Row-red">';
            } else {
            	html_string = html_string + '<div class="div-table-row">';
            } // sending the id fo the signoff record not the member id 
            	html_string = html_string + '<div class="div-table-col0 hform" ><input type="checkbox" name="tobeupdated[]" value="' + response[key].id+ '"/></div>';				
				html_string = html_string + '<div class="div-table-col160 hform"  >'+ response[key].name + '</div>';
 				html_string = html_string + '<div class="div-table-col125b" >' + response[key].date_expire.substring(0,10) +  '</div></div>';
            });
			return(html_string );  		
  	}
  	
  	function restore_page_settings(){
  	    $('#delete_signoff').prop('checked', false);
        $("#submit").val("Add Signoff");
      	$("#record_id").val(""); 	
    	$("#signoff_type").show();
    	$("#update_signoff").text("");
      	$("#update_signoff").css({'display': 'hide' });
      	$(".show_delete").css({'display': 'hide',  "visibility": "hidden" });     
  	}
  	function cancel_update(){
  	// if a signoff update is canceled this resets the fields. 
  	    $('#signoff_form').removeClass('signoff_state'); 
  	    $('#select_header').removeClass('signoff_state'); 
  		$("#update_signoff").text("");
//   		$("#submit").val("cancel");
      	$("#record_id").val(""); 	
      	$("#signoff_type").show();
      	$("#update_signoff").css('display', 'hide');
      	$(".show_delete").css({'display': 'hide',  "visibility": "hidden" });  	
  	}

  }) // $(function) close
})( jQuery );
