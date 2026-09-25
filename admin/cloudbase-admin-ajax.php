<?php
/* This file takes the AJAX request and processes it to build the results elements. 
	 */

/* 
 */
/* 
	Function to display the simple or detailed status. Simple is just a color coded
		list of aircraft. Detailed mode enables clicking on each glider to bring up
		details of it's status. It also addeds a menu that allows a summery list 
		of reports by total hours, 100 hour etc. Futhormore it list non-flying
		assets (airport licesnse etc). 
*/
function cb_status_summary(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
	
	$navhours = array("100h" => "100 Hour", "thour" => "Total Hr", "hreg" => "Registration", "tcheck"=>"Transponder", 'thookcount' => "Tost Hook CT" );
	if( current_user_can( 'read' ) ) {	      
	     $sql = "SELECT s.compitition_id as cid, s.aircraft_id as id, u.title as status, u.color as color, s.date_updated as udate FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND s.aircraft_type < 3 ORDER BY s.aircraft_type,  s.compitition_id";				
		  $items = $wpdb->get_results( $sql, OBJECT);
		  echo '<div ><div class="hform"> Fleet Status:</div><br>';
		  $ldate = '0000-00-00';
		  echo('<table class="centered><tr class="table-heading">');
 		  if ($_GET['details'] == 1){ 
		 	foreach($items as $item){
		 		if ($item->cid == "PVT"){
		 			continue;
		 		}
		 		echo ' <td><div hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"
		 		hx-vals=\'{"function":"cb_status_detail", "record_id":"'. $item->id .' "}\' 
		 		hx-trigger="click" 				
		 		hx-target="#equipment-detail" 
		 		class="hform" style="color:'.$item->color.'">'.$item->cid.'</div></td>';
		 	}	
		 	echo '</tr></table>';
		 	echo ('<nav class="navbar"><ul class="nav-list">') ;
				foreach($navhours as $key => $value ){					
 					echo('<li class="nav-item" 
 						hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"');
 					echo 'hx-vals=\'{ "function":"cb_status_hr_report", "hr_report":"' . $key. '"}\'  ';  
 					echo ('hx-trigger="click" 
 						hx-target="#equipment-detail">' .$value .  '</li>');
				}
			echo ('</ul></nav>');	
		    $sql = "SELECT * FROM {$table_name} WHERE valid_until is NULL AND aircraft_type > 2 ORDER BY registration" ;				
		  	$items = $wpdb->get_results( $sql, OBJECT);
		 	echo '<table class"centered"><tr>';
// 		 	if( false ) {	 
		 	if( current_user_can( 'edit_users' ) ) {	 
		 		foreach($items as $item){
		 			echo '<td>'.  $item->registration .'</td>';
		 		 	cb_modal_btn_t( $item->id, $item->registration,  $item->registration_due_date  );
		 		}	
		 	} else {
		 		foreach($items as $item){
		 			echo '<td >'.$item->registration .'</td>';
		 			echo '<td >'.$item->registration_due_date .'</td>';
		 		}	
		 	}		
			echo '</tr></table>';	
		 }  else {
		 	foreach($items as $item){
		 		if ($item->cid == "PVT"){
		 			continue;
		 		}
		 		if ($item->udate > $ldate){
		 			$ldate = $item->udate ;
		 		}
		 			echo ' <div class="hform" style="color:'.$item->color.'">'.$item->cid.'</div>';
 		 	}
 		 }
	}     
	wp_die();
}

/*
		displays status detail for each aircraft. Depending on viewer it may allow the 
		viewer to edit and update dates and hours. 
*/
function cb_status_detail(){ //
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";
	$flightSheet = $wpdb->prefix . "cloud_base_pdp_flight_sheet";	
// var_dump($_GET);
// die();
	if (!empty($_GET['record_id'])){
 	$sql = "SELECT *, s.id as id,  s.compitition_id as cid,u.title as astatus FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until IS NULL AND  s.aircraft_id = " .$_GET['record_id'] ;				
 	$item = $wpdb->get_row( $sql, OBJECT);	
	if(is_null($item->totalhours)){
		$item->totalhours = 0; 
	}		 	 		
 		if( current_user_can( 'cb_edit_maintenance') ) {	 		
			echo('<div class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col ">Registation Due</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
				echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
				echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';	
				modal_button( $item->aircraft_id, "status", $item->status, $item->astatus );
				echo '</div>';	
			echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Total Hours<sup>*</sup></div><div class="table-col ">Last 100 Hr </div><div class="table-col ">Time since 100</div></div>');
				echo '<div class="table-row" id="annualrow" name="annualrow" >';	
				cb_annual( $item->aircraft_id ,"annual", $item->annual_due_date, (int)$item->totalhours + accumulated_hours( $item->compitition_id,  $item->last_annual_date ) , $item->last_100_date, hours_since_100( $item->compitition_id, $item->last_100_date ), $item->last_annual_date );
				echo '</div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder Due</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
			echo '<div class="table-row">';
 				cb_modal_btn( $item->aircraft_id, "registration", $item->registration_due_date );
 				cb_modal_btn( $item->aircraft_id, "transponder_due", $item->transponder_due );
				call_100_hour_n(  $item->aircraft_id, "tost_replacement_date", $item->tost_replacement_date, tost_hook_count ($item->compitition_id, $item->tost_replacement_date));
// 			echo ' </div><div class="table-row-shade"><div class="table-col">Comments:</div>';
// 			echo ' <div class="table-col-a" >'.$item->comment.'</div></div>';
			echo '</div>';
		} else if( current_user_can( 'read' ) ) {	
			echo('<dis class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col ">Registation</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
				echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
				echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';
				echo ' <div class="table-col">'.$item->astatus.'</div></div>';				
				echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Total Hours<sup>*</div><div class="table-col ">Last 100 Hr</div><div class="table-col ">Time since 100</div></div>');
			echo ' <div class="table-col">'.$item->annual_due_date.'</div>';
				echo ' <div class="table-col">'.(int)$item->totalhours + accumulated_hours( $item->compitition_id,  $item->last_annual_date ) .'</div></div>';
				echo ' <div class="table-col">'.$item->last_100_date.'</div>';
				echo ' <div class="table-col">'.hours_since_100( $item->compitition_id, $item->last_100_date ).'</div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder Due</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
				echo ' <div class="table-col">'.$item->registration_due_date.'</div>';
				echo ' <div class="table-col">'.$item->transponder_due.'</div>';
				echo ' <div class="table-col">'.$item->tost_replacement_date.'</div>';
				echo ' <div class="table-col">'.tost_hook_count ($item->compitition_id, $item->tost_replacement_date).'</div></div>';
// 			echo ' <div class="table-row-shade"><div class="table-col">Comments:</div>';
// 			echo ' <div >'.$item->comment.'</div></div>';
			echo '</div></div></div>';
		}
			echo '<p><sup>*</sup>Total hours is hours at last annual + flight hours since annual date </p>';
			echo ('<br>');
	}
	wp_die();
 }
/*
	produces the vertical report of due dates by menu item. 
*/
function cb_status_hr_report(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
 	if (!empty($_GET['hr_report'])){
//  	$sql = "SELECT *, u.title as astatus FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL"  ;				
	    $sql = "SELECT *, s.compitition_id as cid, s.aircraft_id as id, u.title as status, u.color as color, s.date_updated as udate FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND s.aircraft_type < 3" ;				
 		$items =$wpdb->get_results( $sql, OBJECT);	
//  		var_dump($items);	
		switch ($_GET['hr_report'] ){
			case("100h" ):
				echo('<div class="table-row"><div class="table-col ">100 Hour</div></div>');
				echo ('<div class="table-row-shade"><div class="table-col ">Aircraft</div><div class="table-col ">Hours since 100 Hr</div><div class="table-col ">Last 100 Hr date</div></div>');
				 	foreach($items as $item){
				 		if ($item->cid == "PVT"){
		 					continue;
		 				}
						echo ' <div class="table-row"><div class="table-col">'.$item->compitition_id.'</div>';  
						echo ' <div class="table-col">'.hours_since_100($item->compitition_id, $item->last_100_date ) .'</div>';
						echo ' <div class="table-col">'.$item->last_100_date.'</div></div>';					 	
				 	}
				break;
			case("thour" ):
				echo('<div class="table-row"><div class="table-col ">Total Hour</div></div>');
				echo ('<div class="table-row-shade"><div class="table-col ">Aircraft</div><div class="table-col ">Total Hours</div></div>');
				 	foreach($items as $item){
				 		if ($item->cid == "PVT"){
		 					continue;
		 				}
						echo ' <div class="table-row"><div class="table-col">'.$item->compitition_id.'</div>';  
						echo ' <div class="table-col">'.(int)$item->totalhours + (int)$accumlated_hours.'</div></div>';
				 	}				
				break;
			case("hreg" ):
				echo('<div class="table-row"><div class="table-col ">Registration</div></div>');
				echo ('<div class="table-row-shade"><div class="table-col ">Aircraft</div><div class="table-col ">Registration Due</div></div>');
				 	foreach($items as $item){
				 		if ($item->cid == "PVT"){
		 					continue;
		 				}
						echo ' <div class="table-row"><div class="table-col">'.$item->compitition_id.'</div>';  
						echo ' <div class="table-col">'.$item->registration_due_date.'</div></div>';
				 	}	
				break;
			case("tcheck" ):
				echo('<div class="table-row"><div class="table-col ">Transponder</div></div>');
				echo ('<div class="table-row-shade"><div class="table-col ">Aircraft</div><div class="table-col ">Transponder Due</div></div>');
				 	foreach($items as $item){
				 		if ($item->cid == "PVT"){
		 					continue;
		 				}
						echo ' <div class="table-row"><div class="table-col">'.$item->compitition_id.'</div>';  
						echo ' <div class="table-col">'.$item->transponder_due.'</div></div>';
				 	}					
				break;
			case("thookcount" ):
				echo('<div class="table-row"><div class="table-col ">Tost Hook CT</div></div>');				

				echo ('<div class="table-row-shade"><div class="table-col ">Aircraft</div><div class="table-col ">Flights since Tost replacement</div><div class="table-col ">Last Tost date</div></div>');
				 	foreach($items as $item){
				 		if ($item->cid == "PVT"){
		 					continue;
		 				}
						echo ' <div class="table-row"><div class="table-col">'.$item->compitition_id.'</div>';  
						echo ' <div class="table-col">'.tost_hook_count($item->compitition_id, $item->tost_replacement_date ) .'</div>';
						echo ' <div class="table-col">'.$item->tost_replacement_date.'</div></div>';					 	
				 	}
				break;	
		}	
 	}
 	wp_die();
}
/*
	Creates the button to bring up the modal form to update dates. 
*/
function modal_button( $id, $name, $old_val , $val_name ){
    echo '<div id="A' .$name .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"  hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"' . $id . '", "name":"'. $name .'" ,"value": "' .$old_val . '", "val_name":"'. $val_name .'", "' .$gname.'":"'. $val .'"  }\' ';  
  	echo 'hx-swap="beforeend">
				' .$val_name. '</button></div>	';
}

function cb_modal_btn( $id, $name, $old_value ){
    echo '<div id="A' .$name .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"  hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"'. $id .'", "name":"'. $name .'", "old_value":"'. $old_value .'" }\' ';  
  	echo 'hx-swap="beforeend">' .$old_value. '</button></div>	';
}

function cb_modal_btn_t( $id, $name, $old_value ){
    echo '<td id="A' .$name .  '"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"  hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal_t", "id":"'. $id .'", "name":"'. $name .'", "old_value":"'. $old_value .'" }\'  ';               // "id": ' .$id. '            	    
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></td>	';
}
/*
	Creates the button to bring up the modal form to update annual due dates and hours. 
*/

function cb_annual( $id, $name, $date , $hours, $old_value,  $hours100  ){
    echo '<div class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"  hx-target="body" ';
 	echo 'hx-vals=\'{ "function":"cb_modal_date_hour", "id":"' . $id . '", "name":"'. $name .'" , "date": "' .$date . '", "hours":"'. $hours .'" }\' ';                       	                       	    
  	echo 'hx-swap="beforeend">' .$date. '</button></div><div id="B' .$name .  '"class="table-col">' .$hours. '</div>';
    echo '<div id="Alast_100_date" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"  hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"' . $id . '", "name":"last_100_date", "old_value": "' .$old_value . '", "hours100":"'. $hours100 .'" }\' ';                       	                         	    
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div><div id="Blast_100_date"class="table-col"  >' .$hours100. '</div>';
}
/*
	Creates the button to bring up the modal form to update 100 hour due dates and hours. 
	_n is for intitial load
	_s is for update. 
*/
function call_100_hour_n( $id, $name, $old_value, $hours100 ){
    echo '<div id="A' .$name .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"  hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"' . $id . '", "name":"'. $name .'", "old_value": "' .$old_value . '", "hours100":"'. $hours100 .'" }\' ';                       	                         	    
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div><div id="B' .$name .  '"class="table-col"  >' .$hours100. '</div>';
}
function call_100_hour_s( $id, $name, $old_value, $hours100 ){
    echo '<div id="A' .$name .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=htmx_status_get"  hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"' . $id . '", "name":"'. $name .'", "old_value": "' .$old_value . '", "hours100":"'. $hours100 .'" }\' ';                       	                         	    
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div><div id="B' .$name .  '"class="table-col"  hx-swap-oob="true" >' .$hours100. '</div>';
}
/*
	Creats the modal pop up form that allows Annual and 100 hour dates and hours to 
	be updated.
*/
function cb_modal_date_hour(){
// 	var_dump($get);
		echo ('<div id="modal"
    	 			_="on closeModal add .closing
    	    		wait for animationend
    	    		then remove me">
  				<div class="modal-underlay"
    	   			_="on click trigger closeModal">
  				</div>');
  		echo ('	<div class="modal-content">'); 
  		echo ('<h1>Update Date & Time</h1>');
  		echo '<form          
    			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
    			hx-vals=\'{"function":"cb_update", "id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'  ';   
// 		echo 'hx-swap="outerHTML"	hx-target="#A'. $_GET['name'] .'">' ;   
		echo 'hx-swap="innerHTML"	hx-target="#annualrow">' ;   
		
    	echo '<div class="table-container"</div><div class="table-row"><div class="table-col">';
    	echo 'Annual Date</div>'; 	
  		echo '<div class="table-col"><input type="date" id="new_value" name="new_value" value='. $_GET['date'] .' /> </div><div class="table-col"><div class="table-col"></div></div></div>' ; 
 		echo '<div class="table-row"><div class="table-col">Total Hours</div>';  
  		echo '<div class="table-col"><input type="number" id="newhour" name="newhour" value='. $_GET['hours'] .' > </div></div>' ; 
  		echo '<div class="table-row"><div class="table-col"><input type="submit" value="Submit" _="on click trigger closeModal"/></div><div class="table-col">';
  		echo '<p><b>Instructions: </b>Enter most recient annual date above. The "Total Hours" is showing hours at last annual +  recorded flight hours since last annual date. This will be saved as the new "Total Hours". Overwrite if necessary. 
  		The 100 hour date will be set to the annual date. The 100 hour counter wil be reset to "0". The next annual due date will be calculated. 
  		<u>Click Submit to accept.</u> </p></div>';
    	echo ( ' </form>');  	
    	echo('<div class="table-row"><div class="table-col"><button _="on click trigger closeModal">
    	  				Cancel
    				</button>
  				</div></div></div></div></div>
			</div>'); 
 wp_die();
}
/*
	Creates the modal form to allow updating a date or aircraft status. 
*/
function cb_modal(){
	global $wpdb;
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	   
	
	echo ('<div id="modal"
     			_="on closeModal add .closing
        		wait for animationend
        		then remove me">
  			<div class="modal-underlay"
       			_="on click trigger closeModal">
  			</div>');
  	echo ('	<div class="modal-content">');
  	if($_GET['name'] == 'status') {
  		echo('<h2>Select updated status</h2>');
        echo '<form          
       			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"      
       			hx-vals=\'{"function":"cb_update", "id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
          		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#A'. $_GET["name"] .'">  
        	    <select name="new_value" id="new_value"          		 
         		_="on change trigger closeModal"> ' ;   
    	$sql = "SELECT * FROM ". $table_status . " WHERE active = 1 ORDER BY title ASC ";
    	$astats = $wpdb->get_results( $sql, OBJECT);       	
    	foreach($astats as $key){ 	
    		if ($key->id == $_GET['value']) {
    			echo '<option value=' . $key->id . ' selected>'. $key->title . ' </option>';
    		} else {
    			echo '<option value=' . $key->id . '>'. $key->title . '</option>';
    		}
       	};
    	echo ( '</select> </form>');  		  		

		} else {
  		echo ('<h1>Update Date</h1>');
  		if($_GET['name'] == "last_100_date"){
  			echo ('<h3> Enter last 100 hour inspection date:</h3>');
  		}
  		if($_GET['name'] === "transponder_due"){
  			echo ('<h3> Enter next transponder inspection due date:</h3>');
  		}
  		if($_GET['name'] === "registration"){
  			echo ('<h3> Enter next registration due date:</h3>');
  		}
  		if($_GET['name'] === "tost_replacement_date"){
  			echo ('<h3> Enter last Tost hook replacement date:</h3>');
  		}
  		echo '<form          
       			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update" 
       			hx-vals=\'{"function":"cb_update", "id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
        		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#A'. $_GET["name"] .'">' ;        		
  		echo '<input type="date"id="new_value"  name="new_value" value='. $_GET['old_value'] .'
  				_="on change trigger closeModal"> ' ; 
      			echo ( ' </form>');  		
      }     
     	echo('<button _="on click trigger closeModal">
      				Cancel
    			</button>
  			</div>
		</div>');
 wp_die();

}
/* 
	modal box for the non aircraft items. 
*/
function cb_modal_t(){
	echo ('<div id="modal"
     			_="on closeModal add .closing
        		wait for animationend
        		then remove me">
  			<div class="modal-underlay"
       			_="on click trigger closeModal">
  			</div>');
  	echo ('	<div class="modal-content">');
  		echo ('<h1>Update Date</h1>');
  		echo ('<h2>'. $_GET['name'] .'</h2>');
  		echo ('<h2>Enter the new Expiration Date:</h2>');
  		echo '<form          
       			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update_t" 
       			hx-vals=\'{ "function":"cb_update_t", "id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
        		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#A'. $_GET["name"] .'">' ;        		
  		echo '<input type="date" id="new_value"  name="new_value" value='. $_GET['old_value'] .'
  				_="on change trigger closeModal"> ' ; 
      			echo ( ' </form>');  		
    
     	echo('<button _="on click trigger closeModal">
      				Cancel
    			</button>
  			</div>
		</div>');
 wp_die();
 }
/*
	Updates the detail pages after modal form is submitted. Also does the grunt work of
	actuall updating the database. 
*/
function cb_update() {	
	global $wpdb;
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	   	

	$update_array = array( "record_id"=> $_POST["id"], "name"=> $_POST["name"], "new_value"=>$_POST["new_value"] );	
	if(isset($_POST["newhour"])){
		$update_array["newhour"]= $_POST["newhour"];
	}

	$result = update_record( $update_array );		

	if ($result["success"] === false ){
		if ( $_POST["name"] == "last_100_date" ||   $_POST["name"] == "tost_replacement_date" ){
			call_100_hour_s(  $_POST["id"],  $_POST["name"], "UPDATE FAILED", $item->tost_releases);
		} elseif ( $_POST["name"] == "status"  ){ 	
			 modal_button( $_POST["id"],  $_POST["name"], $_POST["new_value"], "UPDATE FAILED");	
		 } elseif (  $_POST["name"] == "tost_replacement_date" ){
			call_100_hour_s(  $_POST["id"],  $_POST["name"],"UPDATE FAILED" , tost_hook_count ( $result["cid"],  $_POST["new_value"] ));		
		} elseif( $_POST["name"] == "annual") {
			 cb_annual( $_POST["id"],  $_POST["name"], "UPDATE FAILED" , "-------" );	
// 			 call_100_hour_s(  $_POST["id"], "last_100_date" ,  "UPDATE FAILED" , "-------"  );		
		} else {
			cb_modal_btn( $_POST["id"],  $_POST["name"], "UPDATE FAILED" );	
		}
 	} else {
 		if ( $_POST["name"] == "last_100_date"  ){
			call_100_hour_s( $_POST["id"],  $_POST["name"], $_POST["new_value"], hours_since_100 ( $result["cid"],  $_POST["new_value"] ));				
		 } elseif (  $_POST["name"] == "tost_replacement_date" ){
			call_100_hour_s( $_POST["id"],  $_POST["name"], $_POST["new_value"], tost_hook_count ( $result["cid"],  $_POST["new_value"] ));					
		} elseif ( $_POST["name"] == "status"  ){ 	
    		$sql = "SELECT title FROM ". $table_status . " WHERE id = '". $_POST["new_value"]."' ";
    		$astats = $wpdb->get_row( $sql, OBJECT);   	
			modal_button( $_POST["id"],  $_POST["name"], $_POST["new_value"], $astats->title);	
		} elseif( $_POST["name"] == "annual") {
			cb_annual(  $_POST["id"],  $_POST["name"],  $result["annual_due_date"] , $_POST["newhour"], $_POST["new_value"], hours_since_100 ( $result["cid"],  $_POST["new_value"] ) );	
//  			call_100_hour_s(  $_POST["id"], "last_100_date" ,  $_POST["new_value"], hours_since_100 ( $result["cid"],  $_POST["new_value"] ) );		
		} else { 	
 			cb_modal_btn( $_POST["id"],  $_POST["name"], $_POST["new_value"] );		
 		}
 	}
    wp_die();
}

function cb_update_t() {	 
	$update_array = array( "record_id"=>$_POST["id"],  "name"=> $_POST["name"] , "new_value"=>$_POST["new_value"] );
	$result = update_record( $update_array );	
	if ($results === false ){
		cb_modal_btn_t( $result, $_POST["name"], " UPDATE FAILED" );	
 	} else {
 		cb_modal_btn_t( $result, $_POST["name"], $_POST["new_value"] );	 	
 	}
     wp_die();
}

function update_record( $values ){	
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	   
	  $record_id = $values['record_id']; 
	  if( current_user_can( 'cb_edit_maintenance') ) {// 
		  if ($record_id != null){		
			$item = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$table_name} WHERE `aircraft_id` = %d AND valid_until IS NULL " ,   $record_id  ), ARRAY_A );									
			if( $wpdb->num_rows > 0 ) {// 		
			$result["cid"] =  $item['compitition_id'];	
				$old_record = $item['id'];  // save old record id
				if(  $item['aircraft_type'] > 2){
					$item['registration_due_date'] = $values['new_value']; 
				} else{ 
					switch ($values['name'] ){				
					case ('annual'):	
						$item['last_annual_date'] =  $values['new_value'];	
						$start_date = new \DateTime($values['new_value'] );
				 		$start_date->modify('+1 year');
				 		$start_date->modify('last day of this month');												
						$item['annual_due_date'] = $start_date->format('Y-m-d');
						$result['annual_due_date'] = $item['annual_due_date'];
						$item['last_100_date'] = $values['new_value'];
						$item['totalhours'] = $values['newhour'];
						break;					
					case ('registration'):			
						$item['registration_due_date'] =  $values['new_value'];
						break;					
					case( 'transponder_due'):					
						$item['transponder_due'] =  $values['new_value'];
						break;
					case('last_100_date' ):
						$item['last_100_date'] = $values['new_value'];
						break;
					case( 'status' ):
						$item['status'] = $values['new_value'];
						break;
					case( 'tost_replacement_date' ):
						$item['tost_replacement_date'] = $values['new_value'];
						break;	
					case('comment'):
						$item['comment'] = $values['new_value'];
 						break;	
 					default:
 						$item['registration_due_date'] = $values['new_value']; 				
					}										
				}	
				
// 				var_dump($item);
// 	$result["success"] = true ;	
// 	return $result; 		
		
		    $update_result = $wpdb->insert($table_name, array(
		    	'aircraft_id' => $item['aircraft_id'] , 
		    	'registration' =>  $item['registration'], 
		    	'aircraft_type' =>  $item['aircraft_type'], 
				'status' =>  $item['status'], 
				'captian_id' =>  $item['captian_id'], 
				'date_updated' => current_time('mysql', 1), 
				'make' =>  $item['make'], 
				'model' =>  $item['model'], 
				'annual_due_date'=> $item['annual_due_date'], 
				'last_100_date'=> $item['last_100_date'], 
				'last_100_hour'=> $item['last_100_hour'], 
				'totalhours'=> $item['totalhours'], 
				'registration_due_date'=> $item['registration_due_date'], 
				'transponder_due'=> $item['transponder_due'], 
				'compitition_id' =>  $item['compitition_id'], 
				'comment'=> $item['comment'], 
				'last_annual_date'=> $item['last_annual_date'],
				'tost_replacement_date'=> $item['tost_replacement_date'],				
				'valid_until' => null  ), 				
				array('%d', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s' ));													
			    // create new record with valid_until = null. 				
				if ( $update_result != false ) {
// 					$new_id = $wpdb->insert_id;  // get id of new record. 
//  		  		    // mark existing recored as nolonger valid by setting the valin_until to now.
  		       		if($wpdb->update($table_name, array('valid_until' => current_time( 'mysql' )), array( 'id' =>  $old_record) ) != false ){
  		       			$result["success"] = true ;
 		  				return $result; 		  			
 		  			} else {
  		       			$result["success"] = false ;
 		  				return $result; 		  			
 		  			} 											
 				} else {
  		       		$result["success"] = false ;
 		  			return $result; 		  			
 				}		
 			}
 		}
 	}										
}
function accumulated_hours( $id, $last_annual ){
	global $wpdb;

 	$sql2 = "SELECT SUM(time) FROM " . $flightSheet . " WHERE Glider='". $iid ."' AND Date >CAST('". $last_annual_date ."' AS Date)";
	$accumlated_hours  = $wpdb->get_var($sql2); 
	if(is_null($accumlated_hours)){
		$accumlated_hours = 0; 
	}	
	return $accumlated_hours;
}
function hours_since_100 ( $id, $date100 ){
	global $wpdb;

	$flightSheet = $wpdb->prefix . "cloud_base_pdp_flight_sheet";	
	$sql  = "SELECT SUM(time) FROM " . $flightSheet . " WHERE Glider='". $id ."' AND Date > CAST('". $date100 ."' AS Date)";
	$hours_since_100  = $wpdb->get_var($sql); 
	if(is_null($hours_since_100) ){
		$hours_since_100 = 0; 
	}	
	return (int)$hours_since_100 ;
}
function tost_hook_count ( $id, $tost_replacement_date ){
	global $wpdb;

	$flightSheet = $wpdb->prefix . "cloud_base_pdp_flight_sheet";		
	$sql= "SELECT count(*) FROM " . $flightSheet . " WHERE Glider='". $id ."' AND Date > CAST('". $tost_replacement_date ."' AS Date)";
	$tost_releases  = $wpdb->get_var($sql); 
	if(is_null( $tost_releases)){
		$tost_releases = 0 ; 
	}
	return $tost_releases ;
}

/* 
	function to ask to logon to view data. 
*/
function cb_not_authorized() {

    echo 'Please login to view this page.';  
    wp_die();
}

function cb_htmx_status_get(){
	switch($_GET['function']){
		case 'cb_modal':
			cb_modal();
			break;
		case 'cb_modal_t':
			cb_modal_t();
			break;		
		case 'cb_status_hr_report':
			cb_status_hr_report();
			break;	
		case 'cb_status_summary':
			cb_status_summary();
			break;	
		case 'cb_status_detail':
			cb_status_detail();
			break;									
		case 'cb_modal_date_hour':
			cb_modal_date_hour();
			break;	
	    default:
			break;	
	}
}
function cb_htmx_status_put(){
	switch($_POST['function']){
		case 'cb_update':
			cb_update();
			break;
		case 'cb_update_t':
			cb_update_t();
			break;		
								
	    default:
			break;	
	}
}
// ad action to enable wp_ajax endpoints. 
// add_action('wp_ajax_cb_update_number', 'cb_update_number');
add_action('wp_ajax_cb_update', 'cb_update');
add_action('wp_ajax_cb_update_t', 'cb_update_t');
// add_action('wp_ajax_cb_status_detail', 'cb_status_detail');
// add_action('wp_ajax_cb_status_summary', 'cb_status_summary');
// add_action('wp_ajax_cb_status_hr_report', 'cb_status_hr_report');
// add_action('wp_ajax_cb_modal', 'cb_modal');
// add_action('wp_ajax_cb_modal_t', 'cb_modal_t');
add_action('wp_ajax_cb_modal_date_hour', 'cb_modal_date_hour');

add_action('wp_ajax_htmx_status_get', 'cb_htmx_status_get');
// add_action('wp_ajax_htmx_status_put', 'cb_htmx_status_put');

add_action('wp_ajax_nopriv_cb_status_summary', 'cb_not_authorized');


?>
