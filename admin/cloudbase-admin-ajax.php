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
	     $sql = "SELECT s.id as record_id, s.compitition_id as cid, s.aircraft_id as id, u.title as status, u.color as color, s.date_updated as udate FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND s.aircraft_type < 3" ;				
		  $items = $wpdb->get_results( $sql, OBJECT);
		  echo '<div ><div class="hform"> Fleet Status:</div><br>';
		  $ldate = '0000-00-00';
		  echo('<table class="centered><tr class="table-heading">');
 		  if ($_GET['details'] == 1){ 
		 	foreach($items as $item){
		 		if ($item->cid == "PVT"){
		 			continue;
		 		}
// hx-vals={"equip":"' . $item->id. '"}
		 		echo ' <td><div hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_status_detail"
		 		hx-vals=\'{"equip":"' . $item->id . '", "record_id":"'. $item->record_id .' "}\' 
		 		hx-trigger="click" 				
		 		hx-target="#equipment-detail" 
		 		class="hform" style="color:'.$item->color.'">'.$item->cid.'</div></td>';
		 	}	
		 	echo '</tr></table>';
		 	echo ('<nav class="navbar"><ul class="nav-list">') ;
				foreach($navhours as $key => $value ){					
 					echo('<li class="nav-item" 
 						hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_status_hr_report"
 						hx-vals={"hr_report":"' . $key. '"}
 						hx-trigger="click" 
 						hx-target="#equipment-detail">' .$value .  '</li>');
				}
			echo ('</ul></nav>');	
		    $sql = "SELECT * FROM {$table_name} WHERE valid_until is NULL AND aircraft_type > 2 ORDER BY registration" ;				
		  	$items = $wpdb->get_results( $sql, OBJECT);


		 	echo '<table class"centered"><tr>';
		 	if( current_user_can( 'edit_users' ) ) {	 
		 	foreach($items as $item){
		 		echo '<td>'.  $item->registration .'</td>';
// 		 	 	modal_button_t( $item->id , "other", $item->registration_due_date  , $item->registration_due_date  );
		 	 	cb_modal_btn_t( $item->id, $item->registration,  $item->registration_due_date  );
		 	}	
		 	} else {
		 		foreach($items as $item){
		 			echo '<td">'.$item->registration .'</div><div class="table-col ">'.$item->registration_due_date .'</td>';
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
		displays status detail for each asset. Depending on viewer it may allow the 
		viewer to edit and update dates and hours. 
*/
function cb_status_detail(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";
	$flightSheet = $wpdb->prefix . "cloud_base_pdp_flight_sheet";	
	
		if (!empty($_GET['record_id'])){
 		$sql = "SELECT *, u.title as astatus FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE  s.id = " .$_GET['record_id'] ;				

//  	if (!empty($_GET['equip'])){
//  		$sql = "SELECT *, u.title as astatus FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND  s.aircraft_id=" .$_GET['equip'] ;				
 		$item = $wpdb->get_row( $sql, OBJECT);	

 		$sql2 = "SELECT SUM(time) FROM " . $flightSheet . " WHERE Glider='". $item->compitition_id ."' AND Date >". $item->last_annual_date ;
		$accumlated_hours  = $wpdb->get_row($sql2); 
// var_dump($accumlated_hours);

		if($accumlated_hours === NULL){
			$accumlated_hours = 0; 
		}		
		$sql3 = "SELECT SUM(time) FROM " . $flightSheet . " WHERE Glider='". $item->compitition_id ."' AND Date >". $item->last_100_date ;
		$hours_since_100  = $wpdb->get_row($sql3); 


		if($hours_since_100 === NULL){
			$hours_since_100 = 0; 
		}
		$sql4 = "SELECT count(*) FROM " . $flightSheet . " WHERE Glider='". $item->compitition_id ."' AND Date >". $item->tost_replacement_date ;
		$tost_releases  = $wpdb->get_row($sql4); 
		if($tost_releases === NULL){
			$tost_releases = 0; 
		}
 	
//  var_dump($item->last_100_hour );	
 		if( current_user_can( 'cb_edit_maintenance') ) {	 		
			echo('<div class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col ">Registation</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
				echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
				echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';	
				modal_button( $item->aircraft_id, "status", $item->status, $item->astatus, "select", $item->id);
				echo '</div>';	
			echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Total Hours<sup>*</sup></div><div class="table-col ">100Hr Date</div><div class="table-col ">Last 100hr</div></div>');
				echo '<div class="table-row">';
				
				call_annual( $item->aircraft_id ,"annual", $item->annual_due_date, (int)$item->totalhours + (int)$accumlated_hours, $item->last_100_date, (int)$hours_since_100  );
				call_100_hour(  $item->aircraft_id, "last_100_date", $item->last_100_date, (int)$hours_since_100 );
				echo '</div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
			echo '<div class="table-row">';
	 			modal_button( $item->aircraft_id, "registration", $item->registration_due_date, $item->registration_due_date);
//  				modal_button(  $item->aircraft_id, "transponder", $item->transponder_due, $item->transponder_due);
 				cb_modal_btn( $item->id, "transponder_due", $item->transponder_due );
				call_100_hour(  $item->aircraft_id, "tosthookdate", $item->tostdate, $item->tost_releases);
			echo '</div></div>';
			echo '<p><sup>*</sup>Toltal hours is hours at last annual + flight hours since annual date </p>';
			echo ('<br>');
		} else if( current_user_can( 'read' ) ) {	
			echo('<dis class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col ">Registation</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
				echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
				echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';
				echo ' <div class="table-col">'.$item->astatus.'</div></div>';				
				echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Last 100hr</div><div class="table-col ">100Hr Date</div><div class="table-col ">Total Hours</div></div>');
			echo ' <div class="table-col">'.$item->annual_due_date.'</div>';
				echo ' <div class="table-col">'.(int)$hours_since_100 .'</div>';
				echo ' <div class="table-col">'.$item->last_100_date.'</div>';
				echo ' <div class="table-col">'.(int)$item->totalhours + (int)$accumlated_hours.'</div></div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
				echo ' <div class="table-col">'.$item->registration_due_date.'</div>';
				echo ' <div class="table-col">'.$item->transponder_due.'</div>';
				echo ' <div class="table-col">'.'1921-11-02'.'</div>';
				echo ' <div class="table-col">'.'1313'.'</div></div>';
// 			echo ' <div class="table-row-shade"><div class="table-col">Comments:</div>';
// 			echo ' <div >'.$item->comment.'</div></div>';
			echo '</div></div></div>';
			echo '<p><sup>*</sup>Toltal hours is hours at last annual + flight hours since annual date. </p>';
			echo ('<br>');		
		}
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
				echo ('<div class="table-row-shade"><div class="table-col ">Aircraft</div><div class="table-col ">Last 100 Hr</div><div class="table-col ">Last 100 Hr date</div><div class="table-col ">Time remaining</div></div>');
				 	foreach($items as $item){
				 		if ($item->cid == "PVT"){
		 					continue;
		 				}
						echo ' <div class="table-row"><div class="table-col">'.$item->compitition_id.'</div>';  
						echo ' <div class="table-col">'.(int)$hours_since_100 .'</div>';
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
				break;		
		}	
 	}
 	wp_die();
}
/*
	Creates the button to bring up the modal form to update dates. 
*/
function modal_button( $eid, $datatype, $val , $val_name, $id=0 ){
    echo '<div id="' .$datatype .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
 	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'" ,"value": "' .$val . '", "val_name":"'. $val_name .'", "' .$gname.'":"'. $val .'"  }\' ';  
  	echo 'hx-swap="beforeend">
				' .$val_name. '</button></div>	';
}

function cb_modal_btn( $id, $name, $old_value ){
    echo '<div id="' .$id .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
 	echo 'hx-vals=\'{  "old_value":"'. $old_value .'" }\' ';  
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div>	';
}
function modal_button_t( $eid, $datatype, $val , $val_name,  $id=0  ){
    echo '<td id="' .$datatype .  '"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
 	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'" ,"value": "' .$val . '", "val_name":"'. $val_name .'"}\'  ';                       	    
  	echo 'hx-swap="beforeend">
				' .$val_name. '</button></td>	';
}

/*
		NTFS (Note to Future Self)
		"name" is the actual field in the database(cloud_base_aircraft). I use it for the ID of the 
		HTML element (I an an "A" before the name as HTML element names can not start with a letter
		and I of course committed that sin when I designed the database.) and as the varable name 
		for the new value. So $_POST["name"] will have the database field name. While $_POST[$_POST["name"]]
	     will have the new value. $_POST['id] is the database record ID we are working with. 
	     $_POST['old_value'] has the previous value. 
*/
function cb_modal_btn_t( $id, $name, $old_value ){
    echo '<td id="A' .$name .  '"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal_t"  hx-target="body" ';
 	echo 'hx-vals=\'{ "id":"'. $id .'", "name":"'. $name .'", "old_value":"'. $old_value .'" }\'  ';               // "id": ' .$id. '            	    
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></td>	';
}

/*
	Creates the button to bring up the modal form to update annual due dates and hours. 
*/
function call_annual( $eid, $datatype, $date , $hours, $date100, $hours100 ){
    echo '<div id="' .$datatype .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal_date_hour"  hx-target="body" ';
 	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'" , "date": "' .$date . '", "hours":"'. $hours .'", "date100": "' .$date100 . '", "hours100":"'. $hours100 .'" }\' ';                       	    
                     	    
  	echo 'hx-swap="beforeend">
				' .$date. '</button></div><div 	class="table-col">' .$hours. '</div>';
}
// function call_annual_t( $eid, $datatype, $date , $hours, $date100, $hours100 ){
//     echo '<td id="' .$datatype .  '" ><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal_date_hour"  hx-target="body" ';
//  	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'" , "date": "' .$date . '", "hours":"'. $hours .'", "date100": "' .$date100 . '", "hours100":"'. $hours100 .'" }\' ';                       	    
//                      	    
//   	echo 'hx-swap="beforeend">
// 				' .$date. '</button></td><td 	>' .$hours. '</td>';
// }
/*
	Creates the button to bring up the modal form to update 100 hour due dates and hours. 
*/
function call_100_hour( $eid, $datatype, $date100, $hours100 ){
    echo '<div id="' .$datatype .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal_date_hour"  hx-target="body" ';
 	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'", "date100": "' .$date100 . '", "hours100":"'. $hours100 .'" }\' ';                       	    
                     	    
  	echo 'hx-swap="beforeend">
				' .$date100. '</button></div><div 	class="table-col">' .$hours100. '</div>';
}
// function call_100_hour_t( $eid, $datatype, $date100, $hours100 ){
//     echo '<td id="' .$datatype .  '" ><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal_date_hour"  hx-target="body" ';
//  	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'", "date100": "' .$date100 . '", "hours100":"'. $hours100 .'" }\' ';                       	    
//                      	    
//   	echo 'hx-swap="beforeend">
// 				' .$date100. '</button></td><td ">' .$hours100. '</td>';
// }
/*
	Creats the modal pop up form that allows Annual and 100 hour dates and hours to 
	be updated.
*/
function cb_modal_date_hour(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
// 	var_dump($get);
  	if($_GET['datatype'] == 'annual') {
		echo ('<div id="modal"
    	 			_="on closeModal add .closing
    	    		wait for animationend
    	    		then remove me">
  				<div class="modal-underlay"
    	   			_="on click trigger closeModal">
  				</div>');
  		echo ('	<div class="modal-content">');
// var_dump($_GET);  
  		echo ('<h1>Update Date & Time</h1>');
  		echo '<form          
    				hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
    				hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" }\' 
    	 		hx-swap="outerHTML"
    	 		hx-target="#'. $_GET['datatype'] .'">' ;   
    	echo '<div class="table-container"</div><div class="table-row"><div class="table-col">';
    	echo 'Annual Date</div>'; 	
  		echo '<div class="table-col"><input type="date" id="data" name="data" value='. $_GET['date'] .' /> </div><div class="table-col"><div class="table-col"></div></div></div>' ; 
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
  		echo '<div class="table-col"><input type="date" id="newdate" name="newdate" value='. $_GET['date100'] .' /> </div><div class="table-col"><div class="table-col"></div></div></div>' ; 
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
    				hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" , "date100":"'. $_GET['date100']. ', "hours100":"'. $_GET['hours100']. ' }\' 
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
/*
	Creates the modal form to allow updating a date or aircraft status. 
*/
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
   	var_dump($_GET);
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
//   	} elseif ($_GET['datatype'] == 'registration' || $_GET['datatype'] == 'transponder') {	
		} else {
  		echo ('<h1>Update Date</h1>');
//   		echo '<form          
//        			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
//        			hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" ,"value": "' .$_GET['value']. '", "val_name":"'. $_GET['val_name']. '" }\' 
//         		hx-trigger="change"
//         		hx-swap="outerHTML"
//         		hx-target="#'. $_GET['datatype'] .'">' ;   
  		echo '<form          
       			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update" 
       			hx-vals=\'{"id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
        		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#A'. $_GET["name"] .'">' ;        		
  		echo '<input type="date" id="' .  $_GET["name"] . '" name="'.$_GET["name"].'" value='. $_GET['old_value'] .'
  				_="on change trigger closeModal"> ' ; 
      			echo ( ' </form>');  		
      }     
     	echo('<button _="on click trigger closeModal">
      				Cancel
    			</button>
  			</div>
		</div>');
// 		$expire= $this->cb_expire($_GET['value'], 'yearly_eom', NULL);
 wp_die();

}
function cb_modal_t(){
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
  		echo ('<h1>Update Date</h1>');
// var_dump($_GET);
  		echo ('<h2>'. $_GET['name'] .'</h2>');
  		echo ('<h2>Enter the new Expiration Date:</h2>');
  		echo '<form          
       			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update_t" 
       			hx-vals=\'{"id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
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
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
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

 		modal_button( $_POST['equip'], $_POST['datatype'], $_POST['nstatus'], $val_name );
//  		}
// 	} elseif($_POST['datatype'] == 'registration' || $_POST['datatype'] == 'transponder') {
	} elseif($_POST['datatype'] == 'registration' ) {
// 		var_dump($_POST);
 		modal_button( $_POST['equip'], $_POST['datatype'], $_POST['newdate'], $_POST['newdate'] );
	
	} elseif($_POST['datatype'] == 'annual ') {
/*
	read current aircraft record. 
	
*/	

// var_dump($_POST);
// die();
// 
// call_annual( $item->aircraft_id ,"annual", $item->annual_due_date, (int)$item->totalhours + (int)$accumlated_hours, $item->last_100_date, (int)$hours_since_100  );


	} else {
/*
		$_POST[$_POST["name"]] required a bit of explanation, or at least NTFS (Note to Future Self)
		"name" is the actual field in the database(cloud_base_aircraft). I use it for the ID of the 
		HTML element and as the varable name for the new value. So $_POST["name"] will have the
		database field name. While $_POST[$_POST["name"]] will have the new value. $_POST['id] is
		the database record ID we are working with. $_POST['old_value'] has the previous value. 
*/
		cb_modal_btn_t( $_POST["id"], $_POST["name"], $_POST[$_POST["name"]] );
	}
    wp_die();
}
function cb_update_t() {	
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";			
	$update_array = array( "record_id"=>$_POST["id"],  "name"=> $_POST["name"] , "new_value"=>$_POST["new_value"],  "old_value"=>$_POST["old_value"] );
	$result = update_record( $update_array );	
// 	var_dump($result);

/*
		$_POST[$_POST["name"]] required a bit of explanation, or at least NTFS (Note to Future Self)
		"name" is the actual field in the database(cloud_base_aircraft). I use it for the ID of the 
		HTML element and as the varable name for the new value. So $_POST["name"] will have the
		database field name. While $_POST[$_POST["name"]] will have the new value. $_POST['id] is
		the database record ID we are working with. $_POST['old_value'] has the previous value. 
*/
 		cb_modal_btn_t( $result->id, $result->registration, $result->registration_due_date );
	
    wp_die();
}

function update_record( $values ){
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
//   var_dump($values) ;
    
	  unset($values['action']);
	  $record_id = $values['record_id']; 
	  unset($values['record_id']);
	  	  
	  if( current_user_can( 'cb_edit_maintenance') ) {
		  if ($record_id != null){	
	
		$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %s " ,  $record_id );	
		
		$item = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %s " ,  $record_id ), ARRAY_A );		
//  			var_dump($item)	;							
			if( $wpdb->num_rows > 0 ) {		
							
				$old_record = $item['id'];  // save old record id
//  				unset($item->id);         // remove old record id, $item will be used to create a new record. 
//  			var_dump($item['registration_due_date'])	;			
				if(  $item['aircraft_type'] > 2){
					$item['registration_due_date'] = $values['new_value']; 
				}

					switch ($values['name'] ){				
// 					case ('100LL'):
// 						$item['annual_due_date'] = $this->cb_expire_date($value, "yearly_eom", NULL );		
// 						break;					
// 					case ('last_registration'):					
// 						$item['registration_due_date'] = $this->cb_expire_date($value, "year7", NULL );
// 						break;					
// 					case( 'transponder_due'):					
// 						$item['transponder_due'] = $this->cb_epire($value, "biennial-eom", NULL );
// 						break;
// 					case(  '$last_100_date' ):
// 						$item['last_100_date'] = $value;
// 						break;
// 					case( 'status' ):
// 						$item['status'] = $value;
// 						break;
// 					case('comment'):
// 						$item['comment'] = $value;
//  						break;	
//  					default:
//  						$item->$key = $value;						
					}	
//  					var_dump($item)	;										

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
				if ( $update_result == 1 ) {
//  		  		    // mark existing recored as nolonger valid by setting the valin_until to now.
  		       		$wpdb->update($table_name, array('valid_until' => current_time( 'mysql' )), array( 'id' =>  $old_record) );
//   		  var_dump($wpdb->last_query ) ;
// 	 				// read it back to get id and send
  		  			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `registration` = %s AND valid_until IS NULL " , $registration  );	
 		  			$item = $wpdb->get_row( $sql, OBJECT);
 		  			return($item);
// 											
 				}	else {
					return $update_result;
					return $item;
 				}		
 			}
 		}
 	}										
}


function update_aircraft( $record_id, $last_annual_date, $last_registration, $last_transponder, $last_100_date, $status,  $comment){
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
	  
	  if( current_user_can( 'cb_edit_maintenance') ) {
		  if ($record_id != null){	
	
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `aircraft_id` = %d AND valid_until IS NULL " ,  $record_id );	
			$item = $wpdb->get_row( $sql, OBJECT);			
			if( $wpdb->num_rows > 0 ) {			
				$old_record = $item['id'];  // save old record id
				unset($item['id']);         // remove old record id, $item will be used to create a new record. 
				if(isset($last_annual_date)){
					$item['last_annual_date'] = $last_annual_date;
					$item['annual_due_date'] = $this->cb_expire_date($last_annual_date, "yearly_eom", NULL );			
				}
				if( isset($last_registration) )
				{
					$item['registration_due_date'] = $this->cb_expire_date($last_registration, "year7", NULL );
				}
				if( isset($transponder_due) )
				{
					$item['transponder_due'] = $this->cb_epire($transponder_due, "biennial-eom", NULL );
				}
				if( isset($last_100_date) )
				{
					$item['last_100_date'] = $last_100_date;
				}
				if( isset($status) )
				{
					$item['status'] = $status;
				}
				if( isset($comment) ){
					$item['comment'] = $comment;
				}
			 	$update_result = $wpdb->insert($table_name, $item);
			    // create new record with valid_until = null. 
				if ( $update_result == 1 ) {
 		  		    // mark existing recored as nolonger valid by setting the valin_until to now.
  		       		$wpdb->update($table_name, array('valid_until' => current_time( 'mysql' )), array( 'id' =>  $old_record) );
// 	 				// read it back to get id and send
  		  			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `registration` = %s AND valid_until IS NULL " , $registration  );	
 		  			$item = $wpdb->get_row( $sql, OBJECT);
 		  			return($item);
											
 				}	else {
					return new \WP_Error( 'update_failed', esc_html__( $wpdb->last_query , 'my-text-domain' ), array( 'status' => 400 ) );
 				}		
 			}
 		}
 	}										
}

/* 
	function to ask to logon to view data. 
*/
function cb_not_authorized() {

    echo 'Please login to view this page.';  
    wp_die();
}
// ad action to enable wp_ajax endpoints. 
add_action('wp_ajax_cb_update_number', 'cb_update_number');
add_action('wp_ajax_cb_update', 'cb_update');
add_action('wp_ajax_cb_update_t', 'cb_update_t');
add_action('wp_ajax_cb_status_detail', 'cb_status_detail');
add_action('wp_ajax_cb_status_summary', 'cb_status_summary');
add_action('wp_ajax_cb_status_hr_report', 'cb_status_hr_report');
add_action('wp_ajax_cb_modal', 'cb_modal');
add_action('wp_ajax_cb_modal_t', 'cb_modal_t');
add_action('wp_ajax_cb_modal_date_hour', 'cb_modal_date_hour');

add_action('wp_ajax_nopriv_cb_status_summary', 'cb_not_authorized');
?>
