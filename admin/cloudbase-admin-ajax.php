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
	     $sql = "SELECT s.id as record_id, s.compitition_id as cid, s.aircraft_id as id, u.title as status, u.color as color, s.date_updated as udate FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND s.aircraft_type < 3 ORDER BY s.aircraft_type,  s.compitition_id";				
		  $items = $wpdb->get_results( $sql, OBJECT);
		  echo '<div ><div class="hform"> Fleet Status:</div><br>';
		  $ldate = '0000-00-00';
		  echo('<table class="centered><tr class="table-heading">');
 		  if ($_GET['details'] == 1){ 
		 	foreach($items as $item){
		 		if ($item->cid == "PVT"){
		 			continue;
		 		}
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
		displays status detail for each aircraft. Depending on viewer it may allow the 
		viewer to edit and update dates and hours. 
*/
function cb_status_detail(){ //
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";
	$flightSheet = $wpdb->prefix . "cloud_base_pdp_flight_sheet";	
	
	if (!empty($_GET['record_id'])){
 	$sql = "SELECT *, s.id as id, u.title as astatus FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE  s.id = " .$_GET['record_id'] ;				
 	$item = $wpdb->get_row( $sql, OBJECT);	

 	$sql2 = "SELECT SUM(time) FROM " . $flightSheet . " WHERE Glider='". $item->compitition_id ."' AND Date >CAST('". $item->last_annual_date ."' AS Date)";
	$accumlated_hours  = $wpdb->get_var($sql2); 
	if(is_null($accumlated_hours)){
		$accumlated_hours = 0; 
	}	
	if(is_null($item->totalhours)){
		$item->totalhours = 0; 
	}		
	$sql3 = "SELECT SUM(time) FROM " . $flightSheet . " WHERE Glider='". $item->compitition_id ."' AND Date > CAST('". $item->last_100_date ."' AS Date)";
	$hours_since_100  = $wpdb->get_var($sql3); 
	if(is_null($hours_since_100) ){
		$hours_since_100 = 0; 
	}			 
	$sql4 = "SELECT count(*) FROM " . $flightSheet . " WHERE Glider='". $item->compitition_id ."' AND Date > CAST('". $item->tost_replacement_date ."' AS Date)";
	$tost_releases  = $wpdb->get_var($sql4); 
	if(is_null( $tost_releases)){
		$tost_releases = 0; 
	}
 		
 		if( current_user_can( 'cb_edit_maintenance') ) {	 		
			echo('<div class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col ">Registation Due</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
				echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
				echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';	
				modal_button( $item->id, "status", $item->status, $item->astatus );
				echo '</div>';	
			echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Total Hours<sup>*</sup></div><div class="table-col ">Last 100 Hr </div><div class="table-col ">Time since 100</div></div>');
				echo '<div class="table-row">';
				
				call_annual( $item->aircraft_id ,"annual", $item->annual_due_date, (int)$item->totalhours + (int)$accumlated_hours, $item->last_100_date, (int)$hours_since_100  );
  				call_100_hour_n(  $item->id, "last_100_date", $item->last_100_date, (int)$hours_since_100 );

				echo '</div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder Due</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
			echo '<div class="table-row">';
 				cb_modal_btn( $item->id, "registration", $item->registration_due_date );
 				cb_modal_btn( $item->id, "transponder_due", $item->transponder_due );
				call_100_hour_n(  $item->id, "tost_replacement_date", $item->tost_replacement_date, $item->tost_releases);
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
				echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Time since 100</div><div class="table-col ">100Hr Date</div><div class="table-col ">Total Hours</div></div>');
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
function modal_button( $id, $name, $old_val , $val_name ){
    echo '<div id="A' .$name .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
//  	echo 'hx-vals=\'{"id":"' . $id . '", "name":"'. $name .'" ,"value": "' .$old_val . '", "val_name":"'. $val_name .'", "' .$gname.'":"'. $val .'"  }\' ';  
 	echo 'hx-vals=\'{"id":"' . $id . '", "name":"'. $name .'" ,"value": "' .$old_val . '", "val_name":"'. $val_name .'", "' .$gname.'":"'. $val .'"  }\' ';  

  	echo 'hx-swap="beforeend">
				' .$val_name. '</button></div>	';
}

function cb_modal_btn( $id, $name, $old_value ){
    echo '<div id="A' .$name .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
 	echo 'hx-vals=\'{ "id":"'. $id .'", "name":"'. $name .'", "old_value":"'. $old_value .'" }\' ';  
  	echo 'hx-swap="beforeend">' .$old_value. '</button></div>	';
}
function modal_button_t( $eid, $datatype, $val , $val_name,  $id=0  ){
    echo '<td id="' .$datatype .  '"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
 	echo 'hx-vals=\'{"equip":"' . $eid . '", "datatype":"'. $datatype .'" ,"value": "' .$val . '", "val_name":"'. $val_name .'"}\'  ';                       	    
  	echo 'hx-swap="beforeend">
				' .$val_name. '</button></td>	';
}


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

/*
	Creates the button to bring up the modal form to update 100 hour due dates and hours. 
	_n is for intitial load
	_s is for update. 
*/

function call_100_hour_n( $id, $name, $old_value, $hours100 ){
    echo '<div id="A' .$name .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
 	echo 'hx-vals=\'{"id":"' . $id . '", "name":"'. $name .'", "old_value": "' .$old_value . '", "hours100":"'. $hours100 .'" }\' ';                       	                         	    
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div><div id="B' .$name .  '"class="table-col"  >' .$hours100. '</div>';
}
function call_100_hour_s( $id, $name, $old_value, $hours100 ){
    echo '<div id="A' .$name .  '" class="table-col"><button hx-get="' .  admin_url('admin-ajax.php')  . '?action=cb_modal"  hx-target="body" ';
 	echo 'hx-vals=\'{"id":"' . $id . '", "name":"'. $name .'", "old_value": "' .$old_value . '", "hours100":"'. $hours100 .'" }\' ';                       	                         	    
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div><div id="B' .$name .  '"class="table-col"  hx-swap-oob="true" >' .$hours100. '</div>';
}

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
  		echo ('<h1>Update Date & Time</h1>');
//   		  			hx-vals=\'{"equip":"' . $_GET['equip'] . '", "datatype":"'. $_GET['datatype'] .'" }\' 
  		echo '<form          
    			hx-post="' .  admin_url('admin-ajax.php')  . '?action=cb_update"        		
    			hx-vals=\'{"id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'     
    	 		hx-swap="outerHTML"
    	 		hx-target="#'. $_GET['name'] .'">' ;   
    	echo '<div class="table-container"</div><div class="table-row"><div class="table-col">';
    	echo 'Annual Date</div>'; 	
  		echo '<div class="table-col"><input type="date" id="data" name="data" value='. $_GET['old_value'] .' /> </div><div class="table-col"><div class="table-col"></div></div></div>' ; 
 		echo '<div class="table-row"><div class="table-col">Total Hours</div>';  
  		echo '<div class="table-col"><input type="number" id="newhour" name="newhour" value='. $_GET['hours'] .' > </div></div>' ; 
  		echo '<div class="table-row"><div class="table-col"><input type="submit" value="Submit" _="on click trigger closeModal"/></div><div class="table-col">';
  		echo '<p><b>Instructions: </b>Enter most recient annual date above. The "Total Hours" is showing hours at last annual +  recorded flight hours since last annual date. This will be saved as the new "Total Hours". Overwrite if necessary. 
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
       			hx-vals=\'{"id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
          		hx-trigger="change"
        		hx-swap="innerHTML"
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
       			hx-vals=\'{"id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
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
       			hx-vals=\'{"id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
        		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="this">' ;        		
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
      		hx-target="#A'. $_GET["name"] .'">' ;    
	Updates the detail pages after modal form is submitted. Also does the grunt work of
	actuall updating the database. 
*/
function cb_update() {	
	global $wpdb;
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	   	

	$update_array = array( "record_id"=> $_POST["id"], "name"=> $_POST["name"], "new_value"=>$_POST["new_value"],  "old_value"=>$_POST["old_value"] );
// 	var_dump($update_array);	
// 	$result = update_record( $update_array );	
	$result = true ;	
// 	var_dump($result);
	if ($result === false ){
		if ( $_POST["name"] == "last_100_date" ||   $_POST["name"] == "tost_replacement_date" ){
			call_100_hour_s(  $_POST["id"],  $_POST["name"], "UPDATE FAILED", $item->tost_releases);
// 		} elseif ( $_POST["name"] == "status"  ){ 	
// 			 modal_button( $_POST["id"],  $_POST["name"], $_POST["new_value"], "UPDATE FAILED");	
		} else {
			cb_modal_btn( $_POST["id"],  $_POST["name"], "UPDATE FAILED" );	
		}
 	} else {
 		if ( $_POST["name"] == "last_100_date" ||   $_POST["name"] == "tost_replacement_date" ){
			call_100_hour_s(  $_POST["id"],  $_POST["name"], $_POST["new_value"], "0");		
// 		} elseif ( $_POST["name"] == "status"  ){ 	
//     		$sql = "SELECT title FROM ". $table_status . " WHERE id = '". $_POST["new_value"]."' ";
//     		$astats = $wpdb->get_row( $sql, OBJECT);   	
// 			modal_button( $_POST["id"],  $_POST["name"], $_POST["new_value"], $astats->title);	
		} else { 	
 			cb_modal_btn( $_POST["id"],  $_POST["name"], $_POST["new_value"] );		
 		}
 	}
    wp_die();
}

function cb_update_t() {			
	$update_array = array( "record_id"=>$_POST["id"],  "name"=> $_POST["name"] , "new_value"=>$_POST["new_value"],  "old_value"=>$_POST["old_value"] );
	$result = update_record( $update_array );	
	if ($results === false ){
		cb_modal_btn_t( $_POST["id"], $_POST["name"], " UPDATE AILED" );	
 	} else {
 		cb_modal_btn_t( $_POST["id"], $_POST["name"], $_POST["new_value"] );	 	
 	}
    wp_die();
}

function update_record( $values ){	

	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	   
	  $record_id = $values['record_id']; 

	  if ($values['name'] != "status" && $values['name'] != "comment" ){
 	  	$new_date = new DateTime($values['new_value']);
	  }
	  if( current_user_can( 'cb_edit_maintenance') ) {// 
		  if ($record_id != null){	
	
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %s " ,  $record_id );	
			$item = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %s " ,  $record_id ), ARRAY_A );									
			if( $wpdb->num_rows > 0 ) {// 		
							
				$old_record = $item['id'];  // save old record id
//  				unset($item->id);         // remove old record id, $item will be used to create a new record. 
//  			var_dump($item['registration_due_date'])	;			
				if(  $item['aircraft_type'] > 2){
					$item['registration_due_date'] = $values['new_value']; 
				} else{ 
					switch ($values['name'] ){				
					case ('annual'):					
						$item['annual_due_date'] =  $values['new_value'];
						$item['annual'] = $values['new_value'];
						$item['last_100_date'] = $values['new_value'];
						$item['totalhours'] = $values['totalhours'];
						break;					
					case ('registration'):			
						$item['registration_due_date'] =  $values['new_value'];
						$item['registration'] = $values['new_value'];		
						break;					
					case( 'transponder_due'):					
						$item['transponder_due'] =  $values['new_value'];
						break;
					case('last_100_date' ):
						$item['last_100_date'] = $values['new_value'];
						$item['last_100_hour'] = null ;
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
//  		  		    // mark existing recored as nolonger valid by setting the valin_until to now.
  		       		if($wpdb->update($table_name, array('valid_until' => current_time( 'mysql' )), array( 'id' =>  $old_record) ) != false ){
//    		  var_dump($wpdb->last_query ) ;
//    		  var_dump($wpdb->last_error ) ;
// 	 				// read it back to get id and send
  		  			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `registration` = %s AND valid_until IS NULL" , $registration  );	
 		  			$item = $wpdb->get_row( $sql, OBJECT); 	  			
 		  			return($item); 		  			
 		  			} else {
 		  				return false;
 		  			} 											
 				}	else {
					return false;
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
