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
		       console.log( response );
		       alert( 'Something went wrong.' );   
		   }			       
		});		
 	 });
//  	 $('.next').on('click', function(e) {
// //    	 e.preventDefault();
//  	  var passed_page = $('#page_number').text();
//  	  var page_num = passed_page.match(/\d+/)[0]; 	  
//  	  var page_num = parseInt(page_num) +1 ;
//  	 alert( page_num+1);
//    	 $.get("#", {pages: page_num });
//  	 
// 	 });
// 	 $('.previous').on('click', function(e) {
// // 	 e.preventDefault();
// 	  var page = $('#page_number').text();
//  	  var page_num = page.match(/\d+/)[0];
// 
// 	 alert('previous');
// //     	var idx = sections.index( sections.filter('.active') ) - 1;
// //     	idx = idx < 0 ? sections.length-1 : idx;
// //     	sections.eq(idx).trigger('click');
// 	 });	 
   });	  
})( jQuery );	 
	 