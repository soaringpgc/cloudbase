<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/public/partials
 */
?>
<script language="JavaScript">
	var cb_admin_tab = "flights";
</script>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="flex-container">
   <div id="eflights">
    <h3 class="datetime"> Flight Editor</h3>
    <?php 	
        $request = new WP_REST_Request('GET', '/cloud_base/v1/flight_types');
//        $request->set_param( 'per_page', 20 );
        $response = rest_do_request($request);
		$flight_types = $response->get_data();
        
        $request = new WP_REST_Request('GET', '/cloud_base/v1/aircraft');
        $response = rest_do_request($request);
		$aircraft = $response->get_data();

		$request = new WP_REST_Request('GET', '/cloud_base/v1/pilots');
		$request->set_param( 'role', 'subscriber' );
        $response = rest_do_request($request);
		$pilots = $response->get_data();

		$request = new WP_REST_Request('GET', '/cloud_base/v1/pilots');
		$request->set_param( 'role', 'cfi_g' );
        $response = rest_do_request($request);
		$instructors = $response->get_data();
	
		$request = new WP_REST_Request('GET', '/cloud_base/v1/pilots');
		$request->set_param( 'role', 'tow_pilot' );
        $response = rest_do_request($request);
		$towpiltos = $response->get_data();
		
		$request = new WP_REST_Request('GET', '/cloud_base/v1/fees');
        $response = rest_do_request($request);
		$fees = $response->get_data();		
		
      if( current_user_can( 'read' ) ) {	
      
      echo ('<form id="editflights" action="#" ><div >
  	    <input type = "hidden"
          id = "id"
          size = "2"
          name = "id"/>   
        <div class="form-row"> 
        <label for="glider">Glider: </label>
        <select name="aircraft_id" id="aircraft_id" form="editflights" >
        <option value="" selected>Select Aircraft</option>');
     	foreach($aircraft as $key){ 	
     		if ($key->type == 'Glider'){
     			echo '<option value=' . $key->aircraft_id . '>'. $key->compitition_id . '</option>';
     		}
         };     

        echo ( ' <option value="0" >PVT</option>
        </select>  </div> <div class="form-row">   
        <label for="pilots">Pilot: </label>
        <select name="pilot_id" id="pilot_id" form="editflights">
        <option value="" selected>Select Member</option>');
     	foreach($pilots as $key){ 	
     		echo '<option value=' . $key->ID . '>'. $key->last_name . ', '. $key->first_name . '</option>';
         };   
         echo ( '</select> </div> 
         <div class="form-row">    
        <label for="instructor">Instructor: </label>
        <select name="instructors" id="instructors" form="editflights">
        <option value="" selected>None</option>');       
     	foreach($instructors as $key){ 	
     		echo '<option value=' . $key->ID . '>'. $key->last_name . ', '. $key->first_name . '</option>';
         };             
  
         echo ( '</select> </div> <div class="form-row"> 
        <label for="altitude">Altitude: </label>
        <select name="altitude" id="altitude" form="editflights">');
     	foreach($fees as $key){ 	
     		echo '<option value=' . $key->id . '>'. $key->altitude . '</option>';
         };             
         
        echo ('</select></div>
        <div class="form-row">  
       	 	<label for="launch">Launch:</label>
			<input type="time" id="launch" name="launch" size="5">
		</div> 

		<div class="form-row"> 
        	<label for="landing">Landing:</label>
			<input type="time" id="landing" name="landing" size="5">
		</div> 
		
        <div class="form-row"> 
        <label for="towpilot">Tow Pilot: </label>
        <select name="towpilot" id="towpilot" form="editflights">');
     	foreach($towpiltos as $key){ 		
     		echo '<option value=' . $key->ID . '>'. $key->last_name . ', '. $key->first_name . '</option>';
         };     
        echo ( '</select>  </div> 
        <div class="form-row">   
        <label for="towplane">Tug: </label>
        <select name="towplane" id="towplane" form="editflights">');
     	foreach($aircraft as $key){ 	
     		if ($key->type == 'Tow'){
     			echo '<option value=' . $key->aircraft_id . '>'. $key->registration . '</option>';
     		}
         };         
        echo ( '</select></div> ');
 ?>     
        <button  id="add"  class="view">ADD</button>
        <button  id="update"  class="edit">Update</button>
		</div></form> 
		
<?php     
         echo '
         <div id="editflights">
         <div><label for="cloud_base_notes">Notes: </label>
          <textarea  form="editflights" id="cloud_base_notes" name="cloud_base_notes"  rows=2 cols=10">
         </textarea></div></div> ' ;

     } else {
     	Echo 'Please log in. ';
     }
?>
</div>
    <div class="aside"> 
    <div  class="TowFee Table" id="flights">
        <div class="Title ">
            <p>Today's Flights <?php echo date("Y/m/d") ?></p>
        </div>
        <div class="Heading">
            <div class="Cell0">
                <p>Flight</p>
            </div>
           <div class="Cell">
                <p>Glider</p>
            </div>
            <div class="Cell2">
                <p>Pilot</p>
            </div>
            <div class="Cell">
                <p>Action</p>
            </div>
            <div class="Cell">
                <p>Time</p>
            </div>
             <div class="Cell">
                <p>Altitude</p>
            </div>
            <div class="Cell">
                <p>Tug</p>
            </div>
        </div>
    </div>
</div>
    </div>

<!-- 
	<div class="footer">Today's Flights </div>
  </div>
 -->
</div>


