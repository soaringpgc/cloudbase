<br>
<script language="JavaScript">
	var cb_admin_tab = "aircraft";
</script>

<div style="display:inline-block"  align:left id="aircrafts"  class="TowFee" >
    <h3>Aircraft Basic information</h3>
    <form id="addAircraft" action="#" >
    	<div>
    	<input type = "hidden"
            id = "aircraft_id"
            size = "2"
            value = ""
            name = "aircraft_id"/>
        <label for="registration">Registration
        </label>
        <input type = "text"
            id = "registration"
            size = "8"
            title = "Registration(N) number." 
            name = "registration"/>
        <label for="competition">Competition
        </label>
        <input type = "text"
            id = "competition_id"
            size = "8"
            title = "Competition ID." 
            name = "competition_id"/>           
        <label for="type">Make: </label>
        <input type = "text"
            size = "8"
            id = "make"
            title = "Make of Aircraft." />
        <label for="model">Model: </label>
        <input type = "text"
        	size ="8"
            id = "model"
            title = "Model of aircraft." />   
        <label for="type">Type: </label>
         <select name="type" id="type" form="addAircraft">
        	<?php
        	global $wpdb;
			$table_name = $wpdb->prefix . "cloud_base_aircraft_type";	
			$sql = "SELECT * FROM ". $table_name . "  ORDER BY title ASC ";
			$items = $wpdb->get_results( $sql, OBJECT);       	
       		foreach($items as $key){ 	
       			echo '<option value=' . $key->id . '>'. $key->title . '</option>';
            };
        	?>      
         </select>
      
        <button id="add" class="view">Add</button>
        <button id="update" class="edit">Update</button>
       </div>
    </form>

    <div  class="TowFee Table">
        <div class="Title">
            <p>Aircraft </p>
        </div>
        <div class="Heading">
           <div class="Cell">
                <p>Aircraft Id</p>
            </div>
            <div class="Cell">
                <p>Type</p>
            </div>
            <div class="Cell">
                <p>Registration</p>
            </div>
            <div class="Cell">
                <p>Competition</p>
            </div>
             <div class="Cell">
                <p>Make</p>
            </div>
            <div class="Cell">
                <p>Model</p>
            </div>
        </div>
    </div>
</div>

    <h4>Instructions</h4>
<p>      
    Enter basic aircraft information here. Registration, Competition ID Make and Model.
</p><p>        
    To edit an existing item double click anywhere in that line. The data will be copied 
    to the form at the top of the page and the button will change to "Update" click on
    Update to save the new values.  
</p><p>  
	Registration due, Inspection due and status are to be entered on other pages.  
</p>    



