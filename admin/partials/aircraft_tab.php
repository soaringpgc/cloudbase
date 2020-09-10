<br>
<script language="JavaScript">
	var cb_admin_tab = "aircraft";
</script>

<div style="display:inline-block"  align:left id="tow_fees"  class="TowFee" >
    <h3>Aircraft</h3>
    <form id="addTowFee" action="#" >
    	<div>
        <label for="registration">Registration
        </label>
        <input type = "text"
            id = "registration"
            size = "8"
            title = "Registration(N) number." 
            name = "registration"/>
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
        <select>        
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
         <br>
        <label for="compitition">Compitition
        </label>
        <input type = "text"
            id = "compitition"
            size = "8"
            title = "compitition ID." 
            name = "compitition"/>

        <label for="Captian">Captian
        </label>
        <input type = "text"
            id = "captian"
            size = "15"
            title = "Captian." 
            name = "captian"/>
         <label for annual_due>Annual due</label>
           	<input type = "text"
           		 class = "calendar"
                 id = "annual_due"
                 size = "10"
                 width = "100px"
                 value = "<?php echo date('Y-m-d') ?>"
                 title = "When is the annual due for this aircraft" 
                 name = "annual_due"/> 
         <label for registration_due>Registration due</label>
           	<input type = "text"
           		 class = "calendar"
                 id = "registration_due"
                 size = "10"
                 width = "100px"
                 value = "<?php echo date('Y-m-d') ?>"
                 title = "When is the registration due for this aircraft" 
                 name = "registration_due"/>                  
         
        <button id="add">Add</button>
<!-- 
		 <?php wp_nonce_field('tow_charge' ) ?> 
       <?php    submit_button();	 ?>
 -->
       </div>
    </form>

<div  class="TowFee Table">
    <div class="Title">
        <p>Aircraft </p>
    </div>
    <div class="Heading">
        <div class="Cell">
            <p>Registration</p>
        </div>
        <div class="Cell">
            <p>Type</p>
        </div>
         <div class="Cell">
            <p>Annual Due</p>
        </div>
        <div class="Cell">
            <p>Registration Due</p>
        </div>
        <div class="Cell">
            <p>Status</p>
        </div>
    </div>
    <section >
    <div id="aircraft-list"></div>
    </section>
</div>

</div>

    
    <h4>Instructions</h4>
    
    Tried to make this as flexible as possible. For normal tow fees; enter Altitude and
    Charge, leave Base Fee and Hourly blank. Altitude can be text such as "SRB" or "Self"
    Some clubs have a hook up fee that would be entered in the Base fee field. 
    
    For retrive enter "Retrive" under altitude, the basic charge under Base fee and 
    charge for each additional hour under Hourly. 
    
    To edit an existing item double click anywhere in that line input fields will 
    replace the display fields and each can be updated. Press "Enter" to have the new
    values accepted. 
    
    



