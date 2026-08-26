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
	
//  		 	echo '<pre> ' , var_dump($_GET) , '</pre>';	
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
		 		echo ('<br><nav class="navbar">
				<ul class="nav-list"> 
					<li class="nav-item" >100 hr</li>
					<li class="nav-item" >total hr</li>
					<li class="nav-item" >registration</li>
					<li class="nav-item" >transponder</li>
				</ul>
			</nav>');
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
// 		  $date_time = strtotime($ldate) ;
// 		  echo '</div><div> Updated: ' . date('d M Y', $date_time). '</div>';	  
	}     
	wp_die();

}
function cb_status_detail(){

	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	


	    	$sql = "SELECT *, u.title as astatus FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND  s.aircraft_id=" .$_GET['equip'] ;				
 			$item = $wpdb->get_row( $sql, OBJECT);	


		echo ('<div class="table-row smallerFont"><div class="table-col ">Registation</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
			echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
			echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
			echo ' <div class="table-col">'.$item->model.'</div>';
			echo ' <div class="table-col">'.$item->astatus.'</div></div>';				
		echo ('<div class="table-row smallerFont"><div class="table-col ">Annual Due</div><div class="table-col ">Last 100hr</div><div class="table-col ">100Hr Date</div><div class="table-col ">Total Hours</div></div>');
				echo ' <div class="table-col">'.$item->annual_due_date.'</div>';
				echo ' <div class="table-col">'.$item->last_100_hour.'</div>';
				echo ' <div class="table-col">'.$item->last_100_date.'</div></div>';

		echo ('<div class="table-row smallerFont"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder</div>
 			<div class="table-col ">Comments</div><div class="table-col">'.$item->totalhours.'</div></div>');
				echo ' <div class="table-col">'.$item->registration_due_date.'</div>';
				echo ' <div class="table-col">'.$item->transponder_due.'</div>';
				echo ' <div class="table-col">'.$item->comment.'</div></div>';

		echo ('<br>');

		 	 wp_die();
}

function cb_not_authorized() {

    echo 'Please login to view this page.';
  
    wp_die();
}
add_action('wp_ajax_cb_status_detail', 'cb_status_detail');
add_action('wp_ajax_cb_status_summary', 'cb_status_summary');
add_action('wp_ajax_nopriv_cb_status_summary', 'cb_not_authorized');
?>
