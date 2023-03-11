(function( $ ) {
'use strict';
	
  $(function(){
 	 $(".status_change").change(function(){	
  		//var valueSelect = $(this).val();
  		var str = "";
   $( "select option:selected" ).each(function() {
      str += $( this ).val() + " ";
    });
  		alert("you changed status " + str);
  
	 })	 
   });	  
})( jQuery );	 
	 