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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="flex-container">
  <div class="flex-main">
    <div class="main">
    <h3> Flight Editor</h3>
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
		
// 		var_dump($pilots);
// 		die;
      if( current_user_can( 'read' ) ) {	
      echo ('<form id="addFlight" action="#">

  	    <input type = "hidden"
          id = "id"
          size = "2"
          value = ""
          name = "id"/>        
        <label for="glider">Glider: </label>
        <select name="glider" id="glider" form="addFlight">');
     	foreach($aircraft as $key){ 	
     		if ($key->type == 'Glider'){
     			echo '<option value=' . $key->aircraft_id . '>'. $key->compitition_id . '</option>';
     		}
         };     

        echo ( '</select>
        
        <label for="altitude">Altitude: </label>
        <select name="altitude" id="altitude" form="addFlight">');
     	foreach($fees as $key){ 	
     		echo '<option value=' . $key->id . '>'. $key->altitude . '</option>';
         };   
          
        echo ( '</select>    

        <label for="pilots">Pilot: </label>
        <select name="pilot" id="pilot" form="addFlight">');
     	foreach($pilots as $key){ 	
     		echo '<option value=' . $key->id . '>'. $key->last_name . ', '. $key->first_name . '</option>';
         };   
         echo ( '</select>    

        <label for="instructor">Instructor: </label>
        <select name="instructors" id="instructors" form="addFlight">');
     	foreach($instructors as $key){ 	
     		echo '<option value=' . $key->id . '>'. $key->last_name . ', '. $key->first_name . '</option>';
         };   
          
        echo ( '</select>   
        <label for="take_off">Take Off:</label>
		<input type="time" id="take_off" name="take_off">
        <label for="landing">Landing:</label>
		<input type="time" id="landing" name="landing">
        
        <label for="towpilot">Tow Pilot: </label>
        <select name="towpilot" id="towpilot" form="addFlight">');
     	foreach($towpiltos as $key){ 		
     		echo '<option value=' . $key->id . '>'. $key->last_name . ', '. $key->first_name . '</option>';
         };     
        echo ( '</select>    
        <label for="towplane">Tow Plane: </label>
        <select name="towplane" id="towplane" form="addFlight">');
     	foreach($aircraft as $key){ 	
     		if ($key->type == 'Tow'){
     			echo '<option value=' . $key->aircraft_id . '>'. $key->registration . '</option>';
     		}
         };     
//         echo ( '</select>    
// 
//        <label for="flight_type">Type: </label>
//        <select name="flight_type" id="flight_type" form="addFlight">');
//      	foreach($flight_types as $key){ 	
//      		echo '<option value=' . $key->id . '>'. $key->title . '</option>';
//          };    
        echo ( '</select> 

      <button id="add" class="view">Add</button>
          <button id="update" class="edit">Update</button>
      </form> ');
      echo '<br><label for="cloud_base_notes">Notes: </label>
          <textarea  form="addFlight" id="cloud_base_notes" name="cloud_base_notes"  rows=2 cols=10">
         </textarea> ' ;

     } else {
     	Echo 'Please log in. ';
     }
?>
</div>
  <div class="aside"> Active Flights
  </div>
</div>
<!-- 
	<div class="footer">Today's Flights </div>
  </div>
 -->
</div>


<?php		
	 function display_flights(){
	 
	 return 'Flights';
	 
	 }
?>