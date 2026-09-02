<?php
/* This file takes the AJAX request and processes it to build the results elements. 
	 */

/* 
 */

function cb_status_summary(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
	
	$navhours = array("100h" => "100 Hour", "thour" => "Total Hr", "hreg" => "Registration", "tcheck"=>"Transponder", 'thookcount' => "Tost Hook CT" );
	if( current_user_can( 'read' ) ) {	      
	     $sql = "SELECT s.compitition_id as cid, s.aircraft_id as id, u.title as status, u.color as color, s.date_updated as udate FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND s.aircraft_type < 3" ;				
		  $items = $wpdb->get_results( $sql, OBJECT);
		  echo '<div><div class="hform"> Fleet Status:</div><br>';
		  $ldate = '0000-00-00';
 		  if ($_GET['details'] == 1){ 
		 	foreach($items as $item){
		 		if ($item->cid == "PVT"){
		 			continue;
		 		}
		 		if ($item->udate > $ldate){
		 			$ldate = $item->udate ;
		 		}
		 		echo ' <div 
		 		hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_status_detail"
		 		hx-vals={"equip":"' . $item->id. '"}
		 		hx-trigger="click" 				
		 		hx-target="#equipment-detail" 
		 		class="hform" style="color:'.$item->color.'">'.$item->cid.'</div>';
		 	}	
		 	echo ('<br><nav class="navbar"><ul class="nav-list">') ;
				foreach($navhours as $key => $value ){					
 					echo('<li class="nav-item" 
 						hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_status_hr_report"
 						hx-vals={"hr_report":"' . $key. '"}
 						hx-trigger="click" 
 						hx-target="#equipment-detail">' .$value .  '</li>');
				}
			echo ('</ul></nav>');
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
function cb_status_detail(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
 	if (!empty($_GET['equip'])){
 		$sql = "SELECT *, u.title as astatus FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND  s.aircraft_id=" .$_GET['equip'] ;				
 		$item = $wpdb->get_row( $sql, OBJECT);	
 	
//  		if(!property_exists($item, 'last_100_hour'))  {

// the following is necessary because the varables are not in the database and/or are not set. 
 		if(!isset($item->last_100_hour))  {
			$item->last_100_hour = "0";	
   		}
   		 if(!isset($item->tostcount))  {
			$item->tostcount = "0";	
   		}
   		 if(!isset($item->totalhours))  {
			$item->totalhours = "0";	
   		}
	
 		if( current_user_can( 'cb_edit_maintenance') ) {	
			echo('<dis class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col ">Registation</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
				echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
				echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';	
				modal_button( $item->aircraft_id, "status", $item->status, $item->astatus, "select");
				echo '</div>';	
			echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Total Hours</div><div class="table-col ">100Hr Date</div><div class="table-col ">Last 100hr</div></div>');
				echo '<div class="table-row">';
				
				call_annual( $item->aircraft_id ,"annual", $item->annual_due_date, $item->totalhours, $item->last_100_date, $item->last_100_hour );
				call_100_hour(  $item->aircraft_id, "last_100_date", $item->last_100_date, $item->last_100_hour);
//  				modal_button(  $item->aircraft_id, "last_100_date", $item->last_100_date, $item->last_100_date, "date");
//  				modal_button(  $item->aircraft_id, "last_100_hour", $item->last_100_hour, $item->last_100_hour, "number"); 				
				echo '</div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
	echo '<div class="table-row">';
	 			modal_button( $item->aircraft_id, "registration", $item->registration_due_date, $item->registration_due_date);
 				modal_button(  $item->aircraft_id, "transponder", $item->transponder_due, $item->transponder_due);
				call_100_hour(  $item->aircraft_id, "tosthookdate", $item->tostdate, $item->tostcount);


 // 				modal_button(  $item->aircraft_id, "tosthookdate", $item->tostdate, $item->tostcount, "date");
//  				modal_button(  $item->aircraft_id, "tosthookcount", "0", "0", "number");
			echo '</div>';
			echo ('</div><br>');
		} else if( current_user_can( 'read' ) ) {	
			echo('<dis class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col ">Registation</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
				echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
				echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';
				echo ' <div class="table-col">'.$item->astatus.'</div></div>';				
				echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Last 100hr</div><div class="table-col ">100Hr Date</div><div class="table-col ">Total Hours</div></div>');
			echo ' <div class="table-col">'.$item->annual_due_date.'</div>';
				echo ' <div class="table-col">'.$item->last_100_hour.'</div>';
				echo ' <div class="table-col">'.$item->last_100_date.'</div>';
				echo ' <div class="table-col">'.$item->totalhours.'</div></div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
				echo ' <div class="table-col">'.$item->registration_due_date.'</div>';
				echo ' <div class="table-col">'.$item->transponder_due.'</div>';
				echo ' <div class="table-col">'.'1921-11-02'.'</div>';
				echo ' <div class="table-col">'.'1313'.'</div></div>';
			echo ' <div class="table-row-shade"><div class="table-col">Comments:</div>';
			echo ' <div >'.$item->comment.'</div></div>';
			echo ('</div><br>');		
		}
	}
	wp_die();
 }
function cb_status_hr_report(){
// 	$navhours = array("100h" => "100 Hour", "thour" => "Total Hr", "hreg" => "Registration", "tcheck"=>"Transponder", 'thookcount' => "Tost Hook CT" );

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
				echo ('<div class="table-row-shade"><div class="table-col ">Aircraft</div><div class="table-col ">Last 100 Hr</div><div class="table-col ">Last 100 Hr date</div><div class="table-col ">Time remaining</div></div>');
				 	foreach($items as $item){
				 		if ($item->cid == "PVT"){
		 					continue;
		 				}
						echo ' <div class="table-row"><div class="table-col">'.$item->compitition_id.'</div>';  
						echo ' <div class="table-col">'.$item->last_100_hour.'</div>';
						echo ' <div class="table-col">'.$item->last_100_date.'</div>';		
						echo ' <div class="table-col">'.'time remaining'.'</div></div>';				 	
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
						echo ' <div class="table-col">'.$item->totalhours.'</div></div>';
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
				break;		
		}	
 	}
 	wp_die();
}

function modal_button( $eid, $datatype, $val , $val_name ){
    echo '<div id="' .$datatype .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
 	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'" ,"value": "' .$val . '", "val_name":"'. $val_name .'"}\'  ';                       	    
  	echo 'hx-swap="beforeend">
				' .$val_name. '</button></div>	';
}
function call_annual( $eid, $datatype, $date , $hours, $date100, $hours100 ){
    echo '<div id="' .$datatype .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal_date_hour"  hx-target="body" ';
 	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'" , "date": "' .$date . '", "hours":"'. $hours .'", "date100": "' .$date100 . '", "hours100":"'. $hours100 .'" }\' ';                       	    
                     	    
  	echo 'hx-swap="beforeend">
				' .$date. '</button></div><div 	class="table-col">' .$hours. '</div>';
}
function call_100_hour( $eid, $datatype, $date100, $hours100 ){
    echo '<div id="' .$datatype .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal_date_hour"  hx-target="body" ';
 	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'", "date100": "' .$date100 . '", "hours100":"'. $hours100 .'" }\' ';                       	    
                     	    
  	echo 'hx-swap="beforeend">
				' .$date100. '</button></div><div 	class="table-col">' .$hours100. '</div>';
}

function cb_modal_date_hour(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
  	if($_GET['datatype'] == 'annual') {
		echo ('<div id="modal"
    	 			_="on closeModal add .closing
    	    		wait for animationend
    	    		then remove me">
  				<div class="modal-underlay"
    	   			_="on click trigger closeModal">
  				</div>');
  		echo ('	<div class="modal-content">');
// v	ar_dump($_GET);  
  		echo ('<h1>Update Date & Time</h1>');
  		echo '<form          
    				hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
    				hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" ,"date": "' .$_GET['date']. '", "hours":"'. $_GET['hours']. '", "date100":"'. $_GET['date100']. ', "hours100":"'. $_GET['hours100']. ' }\' 
    	 		hx-swap="outerHTML"
    	 		hx-target="#'. $_GET['datatype'] .'">' ;   
    	echo '<div class="table-container"</div><div class="table-row"><div class="table-col">';
    	echo 'Annual Date</div>'; 	
  		echo '<div class="table-col"><input type="date" id="newdata" name="newdata" value='. $_GET['date'] .' /> </div><div class="table-col"><div class="table-col"></div></div></div>' ; 
 		echo '<div class="table-row"><div class="table-col">Total Hours</div>';  
  		echo '<div class="table-col"><input type="number" id="newhour" name="newhour" value='. $_GET['hours'] .' > </div></div>' ; 
  		echo '<div class="table-row"><div class="table-col"><input type="submit" value="Submit" _="on click trigger closeModal"/></div><div class="table-col">';
  		echo '<p><b>Instructions: </b>Enter new annual date above. The "Total Hours" is showing hours at last annual +  recorded flight hours since last annual date. This will be saved as the new "Total Hours". Overwrite if necessary. 
  		The 100 hour date will be set to the annual date. The 100 hour counter wil be reset to "0". <u>Click Submit to accept.</u> </p></div>';
    	echo ( ' </form>');  	
    	echo ('<div class="table-row-shade"><div class="table-col">100 hour</div><div class="table-col">'. $_GET['date100'] .'</div>') 	;
    	echo ('<div class="table-col">100 hour counter</div><div class="table-col">'. $_GET['hours100'] .'</div></div>') 	;
    	echo('<div class="table-row"><div class="table-col"><button _="on click trigger closeModal">
    	  				Cancel
    				</button>
  				</div></div></div></div></div>
			</div>');
		} elseif($_GET['datatype'] == 'last_100_date') {
			echo ('<div id="modal"
    		 			_="on closeModal add .closing
    		    		wait for animationend
    		    		then remove me">
  					<div class="modal-underlay"
    		   			_="on click trigger closeModal">
  					</div>');
  			echo ('	<div class="modal-content">');
//		var_dump($_GET);  
  			echo ('<h1>Update 100 Hour</h1>');
   		echo '<form          
    				hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
    				hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" ,"date": "' .$_GET['date']. '", "hours":"'. $_GET['hours']. '", "date100":"'. $_GET['date100']. ', "hours100":"'. $_GET['hours100']. ' }\' 
    	 		hx-swap="outerHTML"
    	 		hx-target="#'. $_GET['datatype'] .'">' ;   
    	echo '<div class="table-container"</div><div class="table-row"><div class="table-col">';
    	echo '100 Hour Date</div>'; 	
  		echo '<div class="table-col"><input type="date" id="newdata" name="newdata" value='. $_GET['date100'] .' /> </div><div class="table-col"><div class="table-col"></div></div></div>' ; 
 		echo '<div class="table-row"><div class="table-col">100 Hours</div>';  
  		echo '<div class="table-col">'. $_GET['hours100'] .' </div></div>' ; 
  		echo '<div class="table-row"><div class="table-col"><input type="submit" value="Submit" _="on click trigger closeModal"/></div><div class="table-col">';
  		echo '<p><b>Instructions: </b>Enter new 100 hour inspection date above.  
  					The 100 hour counter wil be reset to "0". <u>Click Submit to accept.</u> </p></div>';
    	echo ( ' </form>');  	
    	echo('<div class="table-row"><div class="table-col"><button _="on click trigger closeModal">
    	  				Cancel
    				</button>
  				</div></div></div></div></div>
			</div>'); 			
		} elseif($_GET['datatype'] == 'tosthookdate') {
			echo ('<div id="modal"
    		 			_="on closeModal add .closing
    		    		wait for animationend
    		    		then remove me">
  					<div class="modal-underlay"
    		   			_="on click trigger closeModal">
  					</div>');
  			echo ('	<div class="modal-content">');
//		var_dump($_GET);  
  			echo ('<h1>Update Tost Hook</h1>');
   		echo '<form          
    				hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
    				hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" ,"date": "' .$_GET['date']. '", "hours":"'. $_GET['hours']. '", "date100":"'. $_GET['date100']. ', "hours100":"'. $_GET['hours100']. ' }\' 
    	 		hx-swap="outerHTML"
    	 		hx-target="#'. $_GET['datatype'] .'">' ;   
    	echo '<div class="table-container"</div><div class="table-row"><div class="table-col">';
    	echo 'TOST Date</div>'; 	
  		echo '<div class="table-col"><input type="date" id="newdata" name="newdata" value='. $_GET['date100'] .' /> </div><div class="table-col"><div class="table-col"></div></div></div>' ; 
 		echo '<div class="table-row"><div class="table-col">Tost Count</div>';  
  		echo '<div class="table-col">'. $_GET['hours100'] .' </div></div>' ; 
  		echo '<div class="table-row"><div class="table-col"><input type="submit" value="Submit" _="on click trigger closeModal"/></div><div class="table-col">';
  		echo '<p><b>Instructions: </b>Enter Tost Hook replacement date above.  
  					Teh Tost hook counter wil be reset to "0". <u>Click Submit to accept.</u> </p></div>';
    	echo ( ' </form>');  	
    	echo('<div class="table-row"><div class="table-col"><button _="on click trigger closeModal">
    	  				Cancel
    				</button>
  				</div></div></div></div></div>
			</div>'); 			
		}
 wp_die();
// hx-trigger="keyup[keyCode=13]"      hx-trigger="clilck from:#enter delay:50ms"
}


function cb_modal(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	

	echo ('<div id="modal"
     			_="on closeModal add .closing
        		wait for animationend
        		then remove me">
  			<div class="modal-underlay"
       			_="on click trigger closeModal">
  			</div>');
  	echo ('	<div class="modal-content">');
  	if($_GET['datatype'] == 'status') {
  		echo('<h2>Select updated status</h2>');
        echo '<form          
       			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
       			hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" ,"value": "' .$_GET['value']. '", "val_name":"'. $_GET['val_name'] . '"}\' 
        		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#status">     
        	    <select name="nstatus" id="nstatus"          		 
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
  	} elseif ($_GET['datatype'] == 'registration' || $_GET['datatype'] == 'transponder') {	
  		echo ('<h1>Update Date</h1>');
  		echo '<form          
       			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
       			hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" ,"value": "' .$_GET['value']. '", "val_name":"'. $_GET['val_name']. '" }\' 
        		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#'. $_GET['datatype'] .'">' ;   
  		echo '<input type="date" id="newdata" name="newdata" value='. $_GET['value'] .'
  				_="on change trigger closeModal"> ' ; 
      			echo ( ' </form>');  		
      }     
     	echo('<button _="on click trigger closeModal">
      				Cancel
    			</button>
  			</div>
		</div>');
 wp_die();
// hx-trigger="keyup[keyCode=13]"      hx-trigger="clilck from:#enter delay:50ms"
}
function cb_update() {	
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
// var_dump($_POST)	;	
	if($_POST['datatype'] == 'status'){
		$sql = "SELECT title FROM ". $table_status . " WHERE active = 1 and id = " .$_POST['nstatus'] ." ";
// 		var_dump($_POST['equip']);
		$val_name = $wpdb->get_var( $sql);  
// update the database 
		$data = ([ 'status' => $_POST['nstatus']]);
		$where = [ 'id' =>  $_POST['equip']];
//  		if ($wpdb->update($table_name, $data, $where ) != FALSE );  {
//  need to update last reciord wutg exoired date  and create new record in database . 	
// 	reload the button with updated status. 

 			modal_button( $_POST['equip'], $_POST['datatype'], $_POST['nstatus'], $val_name, "select"  );
//  		}
	} elseif($_POST['datatype'] == 'registration' || $_POST['datatype'] == 'transponder') {
	
// 		var_dump($_POST);
 		modal_button( $_POST['equip'], $_POST['datatype'], $_POST['newdata'], $_POST['newdata'], "date"  );
	
	} elseif($_POST['datatype'] == 'annual ') {

// 	 	modal_button( $_POST['equip'], $_POST['datatype'], $_POST['newdata'], $_POST['newdata'], "number"  );

	}
    wp_die();
}

/* 
	function to ask to logon to view data. 
*/
function cb_not_authorized() {

    echo 'Please login to view this page.';  
    wp_die();
}

add_action('wp_ajax_cb_update_number', 'cb_update_number');
add_action('wp_ajax_cb_update', 'cb_update');
add_action('wp_ajax_cb_status_detail', 'cb_status_detail');
add_action('wp_ajax_cb_status_summary', 'cb_status_summary');
add_action('wp_ajax_cb_status_hr_report', 'cb_status_hr_report');
add_action('wp_ajax_cb_modal', 'cb_modal');
add_action('wp_ajax_cb_modal_date_hour', 'cb_modal_date_hour');

add_action('wp_ajax_nopriv_cb_status_summary', 'cb_not_authorized');
?>
