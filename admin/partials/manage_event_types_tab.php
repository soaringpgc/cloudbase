<br>
<script language="JavaScript">
	var cb_admin_tab = "aircraft_event_types";
</script>

<div style="display:inline-block"  align:left id="flight_types"  class="flight_type editform"  >
<?php 			
if( current_user_can( 'manage_options' ) ) {	
  echo(' 
    <h3>Flight Type</h3><DIV>
    <form id="addflight_type" action="#" >
    	<div>
    	<input type = "hidden"
            id = "id"
            size = "2"
            value = ""
            name = "id"/>
        <div class="hform">         
       <label for="title" >Title:</label>    
        <input type = "text"
            id = "title"
            size = "8"
            title = "Type of flight." 
            name = "title"/>
        </div>
        
        
        <label for="interval" >interval:</label>    
        <input type = "text"
            id = "interval"
            size = "8"
            title = "Interval." 
            name = "title"/>
        </div>  
        <label for="interval_units" >Interval Units:</label>    
        <input type = "text"
            id = "interval_units"
            size = "8"
            title = "Interval Units(hours, days, months, years)." 
            name = "title"/>
        </div>  
        <label for="aircraft_type" >Aircraft_type:</label>    
        <input type = "text"
            id = "aircraft_type"
            size = "8"
            title = "Type of Aircraft this applies to." 
            name = "title"/>
        </div>            
        <input type = "hidden"
            id = "active"
            size = "2"
            value = ""
            name = "active"/> 
        <br style="clear:both;">
        <div>
          <button id="add" class="view">Add</button>
          <button id="update_cb" class="cb_edit">Update</button>
        </div>
       </div>
    </form> </DIV>');
}    
?>    

<div  class="Table">
    <div class="Title">
        <p>Aircraft Event Types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>ID</p>
        </div>
        <div class="Cell"  >
            <p>Title</p>
        </div>
        <div class="Cell"">
            <p>Interval</p>
        </div>
        <div class="Cell"  >
            <p>Interval Units</p>
        </div>
         <div class="Cell"  >
            <p>Aircraft_type_id</p>
        </div>
    </div>
</div>

</div>
   
    <h4>Instructions</h4>
<p>      
    Must have at least one event type.
</p><p>      
    To edit an existing item double click anywhere in that line. The data will be copied 
    to the form at the top of the page and the button will change to "Update" click on
    Update to save the new values.  
</p>     



