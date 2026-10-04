<?php
/**
 * The rest functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and examples to create your REST access
 * methods. Don't forget to validate and sanatize incoming data!
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/public
 * @author     Your Name <email@example.com>
 
          	'permission_callback' => array($this, 'cloud_base_members_access_check' ),), 
 */
class Cloud_Base_htmx_Status extends Cloud_Base_Rest {

	public function register_routes() {
	   
     $this->resource_path = '/equipment_status' . '(?:/(?P<id>[\d]+))?';
    
     register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_status_htmx_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_status_htmx_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),       	      	
      	)
      );	              
    }

// call back for status:	
	public function cloud_base_status_htmx_get_callback( \WP_REST_Request $request) {
// 
// echo '<pre> ' , var_dump($_GET) , '</pre>'; 
		
		switch($request['function']){
		case 'status':
			$this->cb_modal();
			break;
		case 'cb_modal':
			$this->cb_modal();
			break;
		case 'cb_modal_t':
			$this->cb_modal_t();
			break;		
		case 'cb_status_hr_report':
			$this->cb_status_hr_report();
			break;	
		case 'cb_status_summary':
			$this->cb_status_summary($request);
			break;	
		case 'cb_status_detail':
			$this->cb_status_detail($request);
			break;									
		case 'cb_modal_date_hour':
			$this->cb_modal_date_hour();
			break;	
	    default:
			break;	
	}
	
	
//  		$this->cb_status_summary($request);

// echo '<pre> ' , var_dump($request['function']) , '</pre>'; 
		exit();
// 	
// 
	}	
	public function cloud_base_status_htmx_post_callback( \WP_REST_Request $request) {
 
 
 		switch($_POST['function']){
			case 'cb_update':
				$this->cb_update();
				break;
			case 'cb_update_t':
				$this->cb_update_t();
				break;										
		    default:
				break;	
		}
 
	}
	
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
public function cb_status_summary($request){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
	$nonce = wp_create_nonce( 'wp_rest' );
		
	$navhours = array("100h" => "100 Hour", "thour" => "Total Hr", "hreg" => "Registration", "tcheck"=>"Transponder", 'thookcount' => "Tost Hook CT" );
	if( current_user_can( 'read' ) ) {	      
	     $sql = "SELECT s.compitition_id as cid, s.aircraft_id as id, u.title as status, u.color as color, s.date_updated as udate FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND s.aircraft_type < 3 ORDER BY s.aircraft_type,  s.compitition_id";				
		  $items = $wpdb->get_results( $sql, OBJECT);
		  echo '<div ><div class="hform"> Fleet Status:</div><br>';
		  $ldate = '0000-00-00';
		  echo('<table class="centered><tr class="table-heading">');
 		  if ($request['details'] == 1){ 
		 	foreach($items as $item){
		 		if ($item->cid == "PVT"){
		 			continue;
		 		}  
		 		echo ' <td><div hx-get="' .  esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status"
		 		hx-vals=\'{"function":"cb_status_detail", "record_id":"'. $item->id .' "}\'
				hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'
		 		hx-trigger="click" 				
		 		hx-target="#equipment-detail" 
		 		class="hform" style="color:'.$item->color.'">'.$item->cid.'</div></td>';
		 	}	
		 	echo '</tr></table>';
		 	echo ('<nav class="navbar"><ul class="nav-list">') ;
				foreach($navhours as $key => $value ){					
 					echo('<li class="nav-item" 
 						hx-get="' .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status"');
 					echo 'hx-vals=\'{ "function":"cb_status_hr_report", "hr_report":"' . $key. '"}\'   
 						  hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\''; 
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
		 		 	$this->cb_modal_btn_t( $item->aircraft_id, $item->registration,  $item->registration_due_date  );
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
}

/*
		displays status detail for each aircraft. Depending on viewer it may allow the 
		viewer to edit and update dates and hours. 
*/
public function cb_status_detail($request){ //

	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";
	$table_squawk = $wpdb->prefix . 'cloud_base_squawk';

	if (!empty($_GET['record_id'])){
 	$sql = "SELECT *, s.id as id,  s.compitition_id as cid,u.title as astatus FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until IS NULL AND  s.aircraft_id = " .$_GET['record_id'] ;				
 	$item = $wpdb->get_row( $sql, OBJECT);	
	if(is_null($item->totalhours)){
		$item->totalhours = 0; 
	}		 	 		
 		if( current_user_can( 'cb_edit_maintenance') ) {	 		
			echo('<div class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col ">Registation</div><div class="table-col ">Comp ID</div><div class="table-col ">Model</div><div class="table-col ">Status</div></div>');
				echo ' <div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
				echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';	
				$this->modal_button( $item->aircraft_id, "status", $item->status, $item->astatus );
				echo '</div>';	
			echo ('<div class="table-row-shade"><div class="table-col ">Annual Due</div><div class="table-col ">Total Hours<sup>*</sup></div><div class="table-col ">Last 100 Hr </div><div class="table-col ">Time since 100</div></div>');
				echo '<div class="table-row" id="annualrow" name="annualrow" >';	
				$this->cb_annual( $item->aircraft_id ,"annual", $item->annual_due_date, (int)$item->totalhours + 
						$this->accumulated_hours( $item->compitition_id,  $item->last_annual_date ) , $item->last_100_date, 
						$this->hours_since_100( $item->compitition_id, $item->last_100_date ), $item->last_annual_date );
				echo '</div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder Due</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
			echo '<div class="table-row">';
 				$this->cb_modal_btn( $item->aircraft_id, "registration", $item->registration_due_date );
 				$this->cb_modal_btn( $item->aircraft_id, "transponder_due", $item->transponder_due );
				$this->call_100_hour_n(  $item->aircraft_id, "tost_replacement_date", $item->tost_replacement_date, $this->tost_hook_count ($item->compitition_id, $item->tost_replacement_date));
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
				echo ' <div class="table-col">'.(int)$item->totalhours + $this->accumulated_hours( $item->compitition_id,  $item->last_annual_date ) .'</div></div>';
				echo ' <div class="table-col">'.$item->last_100_date.'</div>';
				echo ' <div class="table-col">'.$item->hours_since_100( $item->compitition_id, $item->last_100_date ).'</div>';
			echo ('<div class="table-row-shade"><div class="table-col ">Registration Due</div><div class="table-col ">Transponder Due</div><div class="table-col ">Tost Hook date</div><div class="table-col ">Tost Hook Count</div></div>');
				echo ' <div class="table-col">'.$item->registration_due_date.'</div>';
				echo ' <div class="table-col">'.$item->transponder_due.'</div>';
				echo ' <div class="table-col">'.$item->tost_replacement_date.'</div>';
				echo ' <div class="table-col">'. $this->tost_hook_count ($item->compitition_id, $item->tost_replacement_date).'</div></div>';
			echo '</div></div></div>';
		}
		  	$sql = "Select s.squawk_id, a.registration, a.compitition_id, s.date_entered, s.status, s.text, s.comment, a.captian_id, s.user_id  FROM {$table_name} a INNER JOIN {$table_squawk} s 
  		on a.aircraft_id=s.equipment  WHERE a.valid_until is NULL AND s.status != 'COMPLETED' AND a.compitition_id = '" .$item->compitition_id. "' ORDER BY s.date_entered DESC "; 

		$squawks = $wpdb->get_results($sql); 
		if ( count($squawks) == 0 ){
			echo '<p> No outstanding Squawks. </p> ';
		} else {
			echo('<dis class="table-container">');
			echo ('<div class="table-row-shade"><div class="table-col" style="width:80%"">Squawk</div><div class="table-col ">Status</div></div>');
			foreach($squawks as $squawk ){
				echo  '<div class="table-row"><div class="table-col">'.$squawk->text.'</div><div class="table-col">'.$squawk->status.'</div> </div> ';  				
			}		
			echo '</div></div>';
		}
	}
 }
/*
	produces the vertical report of due dates by menu item. 
*/
public function cb_status_hr_report(){
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
 	if (!empty($_GET['hr_report'])){
	    $sql = "SELECT *, s.compitition_id as cid, s.aircraft_id as id, u.title as status, u.color as color, s.date_updated as udate FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL AND s.aircraft_type < 3" ;				
 		$items =$wpdb->get_results( $sql, OBJECT);	
		switch ($_GET['hr_report'] ){
			case("100h" ):
				echo('<div class="table-row"><div class="table-col ">100 Hour</div></div>');
				echo ('<div class="table-row-shade"><div class="table-col ">Aircraft</div><div class="table-col ">Hours since 100 Hr</div><div class="table-col ">Last 100 Hr date</div></div>');
				 	foreach($items as $item){
				 		if ($item->cid == "PVT"){
		 					continue;
		 				}
						echo ' <div class="table-row"><div class="table-col">'.$item->compitition_id.'</div>';  
						echo ' <div class="table-col">'. $this->hours_since_100($item->compitition_id, $item->last_100_date ) .'</div>';
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
						echo ' <div class="table-col">'.$this->tost_hook_count($item->compitition_id, $item->tost_replacement_date ) .'</div>';
						echo ' <div class="table-col">'.$item->tost_replacement_date.'</div></div>';					 	
				 	}
				break;	
		}	
 	}
}
/*
	Creates the button to bring up the modal form to update dates. 
*/
public function modal_button( $id, $name, $old_val , $val_name ){
	$cname = str_replace(' ', '', $name);
	$nonce = wp_create_nonce( 'wp_rest' );
    echo '<div id="A' .$cname .  '" class="table-col"><button hx-get="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status" hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"' . $id . '", "name":"'. $name .'" ,"value": "' .$old_val . '", "val_name":"'. $val_name .'", "' .$gname.'":"'. $val .'"  }\' ';  
	echo ('hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'');
  	echo 'hx-swap="beforeend">
				' .$val_name. '</button></div>	';
}

public function cb_modal_btn( $id, $name, $old_value ){
	$cname = str_replace(' ', '', $name);
	$nonce = wp_create_nonce( 'wp_rest' );
    echo '<div id="A' .$cname .  '" class="table-col"><button hx-get="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status" hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"'. $id .'", "name":"'. $name .'", "old_value":"'. $old_value .'" }\' ';  
	echo ('hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'');
  	echo 'hx-swap="beforeend">' .$old_value. '</button></div>	';
}

public function cb_modal_btn_t( $id, $name, $old_value ){
	$cname = str_replace(' ', '', $name);
	$nonce = wp_create_nonce( 'wp_rest' );
    echo '<td id="A' .$cname .  '"><button hx-get="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status" hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal_t", "id":"'. $id .'", "name":"'. $name .'", "old_value":"'. $old_value .'" }\'  ';               // "id": ' .$id. '            	    
	echo ('hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'');
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></td>	';
}
/*
	Creates the button to bring up the modal form to update annual due dates and hours. 
*/

public function cb_annual( $id, $name, $date , $hours, $old_value,  $hours100  ){
	$cname = str_replace(' ', '', $name);
	$nonce = wp_create_nonce( 'wp_rest' );
    echo '<div class="table-col"><button hx-get="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status" hx-target="body" ';
 	echo 'hx-vals=\'{ "function":"cb_modal_date_hour", "id":"' . $id . '", "name":"'. $name .'" , "date": "' .$date . '", "hours":"'. $hours .'" }\' ';                       	                       	    
	echo ('hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\''); 
  	echo 'hx-swap="beforeend">' .$date. '</button></div><div id="B' .$name .  '"class="table-col">' .$hours. '</div>';
    echo '<div id="Alast_100_date" class="table-col"><button hx-get="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status" hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"' . $id . '", "name":"last_100_date", "old_value": "' .$old_value . '", "hours100":"'. $hours100 .'" }\' ';                       	                         	    
	echo ('hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\''); 
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div><div id="Blast_100_date"class="table-col"  >' .$hours100. '</div>';
}
/*
	Creates the button to bring up the modal form to update 100 hour due dates and hours. 
	_n is for intitial load
	_s is for update. 
*/
public function call_100_hour_n( $id, $name, $old_value, $hours100 ){
	$cname = str_replace(' ', '', $name);
	$nonce = wp_create_nonce( 'wp_rest' );
    echo '<div id="A' .$cname .  '" class="table-col"><button hx-get="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status" hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"' . $id . '", "name":"'. $name .'", "old_value": "' .$old_value . '", "hours100":"'. $hours100 .'" }\' ';                       	                         	    
	echo ('hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'');
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div><div id="B' .$name .  '"class="table-col"  >' .$hours100. '</div>';
}
public function call_100_hour_s( $id, $name, $old_value, $hours100 ){
	$cname = str_replace(' ', '', $name);
	$nonce = wp_create_nonce( 'wp_rest' );
    echo '<div id="A' .$cname .  '" class="table-col"><button hx-get="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status" hx-target="body" ';
 	echo 'hx-vals=\'{"function":"cb_modal", "id":"' . $id . '", "name":"'. $name .'", "old_value": "' .$old_value . '", "hours100":"'. $hours100 .'" }\' ';                       	                         	    
	echo ('hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'');
  	echo 'hx-swap="beforeend">
				' .$old_value. '</button></div><div id="B' .$name .  '"class="table-col"  hx-swap-oob="true" >' .$hours100. '</div>';
}
/*
	Creats the modal pop up form that allows Annual and 100 hour dates and hours to 
	be updated.
*/
public function cb_modal_date_hour(){
	$nonce = wp_create_nonce( 'wp_rest' );
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
    		hx-post="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status"       		
    		hx-vals=\'{"function":"cb_update", "id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'  ';   
	echo ('hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'');
	echo 'hx-swap="innerHTML"	hx-target="#annualrow">' ;   	
    echo '<div class="table-container"</div><div class="table-row"><div class="table-col"> Last Annual Date</div>'; 	
  	echo '<div class="table-col"><input type="date" id="new_value" name="new_value" value='. $_GET['date'] .' /> </div><div class="table-col"><div class="table-col"></div></div></div>' ; 
 	echo '<div class="table-row"><div class="table-col">Total Hours</div>';  
  	echo '<div class="table-col"><input type="number" id="newhour" name="newhour" value='. $_GET['hours'] .' > </div></div>' ; 
  	echo '<div class="table-row"><div class="table-col"><input type="submit" value="Submit" _="on click trigger closeModal"/></div><div class="table-col">';
  	echo '<p><b>Instructions: </b>Enter most recient annual date above. The "Total Hours" is showing hours at last annual +  recorded flight hours since last annual date. This will be saved as the new "Total Hours". Change if necessary. 
  	The 100 hour date will be set to the annual date. The 100 hour counter wil be reset to "0". The next annual due date will be calculated. 
  	<u>Click Submit to accept.</u> </p></div>';
    echo ( ' </form>');  	
    echo('<div class="table-row"><div class="table-col"><button _="on click trigger closeModal">
      				Cancel
    			</button>
  			</div></div></div></div></div>
			</div>'); 
}
/*
	Creates the modal form to allow updating a date or aircraft status. 
*/
public function cb_modal(){
	global $wpdb;
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	  
	$cname = str_replace(' ', '', $_GET["name"] ); 
	$nonce = wp_create_nonce( 'wp_rest' );
	
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
       			hx-post="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status"     
       			hx-vals=\'{"function":"cb_update", "id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
 				hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'
          		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#A'. $cname .'">  
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
       			hx-post="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status"
       			hx-vals=\'{"function":"cb_update", "id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
				hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'
        		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#A'. $cname .'">' ;        		
  		echo '<input type="date"id="new_value"  name="new_value" value='. $_GET['old_value'] .'
  				_="on change trigger closeModal"> ' ; 
      			echo ( ' </form>');  		
      }     
     	echo('<button _="on click trigger closeModal">
      				Cancel
    			</button>
  			</div>
		</div>');
}
/* 
	modal box for the non aircraft items. 
*/
public function cb_modal_t(){
	$cname = str_replace(' ', '', $_GET["name"] ); 
	$nonce = wp_create_nonce( 'wp_rest' );
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
       			hx-post="'  .   esc_url_raw( rest_url() ). 'cloud_base/v1/equipment_status"
       			hx-vals=\'{ "function":"cb_update_t", "id":"' . $_GET['id'] . '", "name":"'. $_GET['name'] .'" ,"old_value": "' .$_GET['old_value']. '" }\'        		
				hx-headers=\'{"X-WP-Nonce":"' . $nonce . ' "}\'
        		hx-trigger="change"
        		hx-swap="outerHTML"
        		hx-target="#A'. $cname.'">' ;        		
  		echo '<input type="date" id="new_value"  name="new_value" value='. $_GET['old_value'] .'
  				_="on change trigger closeModal"> ' ; 
      			echo ( ' </form>');  		
    
     	echo('<button _="on click trigger closeModal">
      				Cancel
    			</button>
  			</div>
		</div>');
 }
/*
	Updates the detail pages after modal form is submitted. Also does the grunt work of
	actuall updating the database. 
*/
public function cb_update() {	
	
	global $wpdb;
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	  
	$new_date = $date = preg_replace("([^0-9/-])", "", $_POST['new_value']); 	

	$update_array = array( "record_id"=> $_POST["id"], "name"=> $_POST["name"], "new_value"=>$_POST["new_value"] );	
	if(isset($_POST["newhour"])){
		$update_array["newhour"]= filter_var($_POST["newhour"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
	$result = $this->update_record( $update_array );		
	if ($result["success"] === false ){
		if ( $_POST["name"] == "last_100_date" ||   $_POST["name"] == "tost_replacement_date" ){
			$this->call_100_hour_s(  $_POST["id"],  $_POST["name"], "UPDATE FAILED", $item->tost_releases);
		} elseif ( $_POST["name"] == "status"  ){ 	
			 $this->modal_button( $_POST["id"],  $_POST["name"], $_POST["new_value"], "UPDATE FAILED");	
		 } elseif (  $_POST["name"] == "tost_replacement_date" ){
			$this->call_100_hour_s(  $_POST["id"],  $_POST["name"],"UPDATE FAILED" , tost_hook_count ( $result["cid"],  $new_date));		
		} elseif( $_POST["name"] == "annual") {
			 $this->cb_annual( $_POST["id"],  $_POST["name"], "UPDATE FAILED" , "-------" );	
// 			 call_100_hour_s(  $_POST["id"], "last_100_date" ,  "UPDATE FAILED" , "-------"  );		
		} else {
			$this->cb_modal_btn( $_POST["id"],  $_POST["name"], "UPDATE FAILED" );	
		}
 	} else {
 		if ( $_POST["name"] == "last_100_date"  ){
			$this->call_100_hour_s( $_POST["id"],  $_POST["name"], $new_date , $this->hours_since_100 ( $result["cid"],  $new_date));				
		 } elseif (  $_POST["name"] == "tost_replacement_date" ){
			$this->call_100_hour_s( $_POST["id"],  $_POST["name"], $new_date , $this->tost_hook_count ( $result["cid"],  $new_date ));					
		} elseif ( $_POST["name"] == "status"  ){ 	
    		$sql = "SELECT title FROM ". $table_status . " WHERE id = '". $new_date ."' ";
    		$astats = $wpdb->get_row( $sql, OBJECT);   	
			$this->modal_button( $_POST["id"],  $_POST["name"],$new_date , $astats->title);	
		} elseif( $_POST["name"] == "annual") {
			$this->cb_annual(  $_POST["id"],  $_POST["name"],  $result["annual_due_date"] , $_POST["newhour"], $new_date , $this->hours_since_100 ( $result["cid"],  $new_date ) );	
//  			$this->call_100_hour_s(  $_POST["id"], "last_100_date" ,  $_POST["new_value"], hours_since_100 ( $result["cid"],  $_POST["new_value"] ) );		
		} else { 	
 			$this->cb_modal_btn( $_POST["id"],  $_POST["name"], $new_date  );		
 		}
 	}
}

public function cb_update_t() {	 
	$new_date = $date = preg_replace("([^0-9/-])", "", $_POST['new_value']); 	

	$update_array = array( "record_id"=>$_POST["id"],  "name"=> $_POST["name"] , "new_value"=>$new_date );
	$result = $this->update_record( $update_array );	
	if ($results === false ){
		$this->cb_modal_btn_t( $result, $_POST["name"], " UPDATE FAILED" );	
 	} else {
 		$this->cb_modal_btn_t( $result, $_POST["name"], $new_date  );	 	
 	}
}

public function update_record( $values ){	
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
public function accumulated_hours( $id, $last_annual ){
	global $wpdb;

 	$sql2 = "SELECT SUM(time) FROM " . $flightSheet . " WHERE Glider='". $iid ."' AND Date >CAST('". $last_annual_date ."' AS Date)";
	$accumlated_hours  = $wpdb->get_var($sql2); 
	if(is_null($accumlated_hours)){
		$accumlated_hours = 0; 
	}	
	return $accumlated_hours;
}
public function hours_since_100 ( $id, $date100 ){
	global $wpdb;

	$flightSheet = $wpdb->prefix . "cloud_base_pdp_flight_sheet";	
	$sql  = "SELECT SUM(time) FROM " . $flightSheet . " WHERE Glider='". $id ."' AND Date > CAST('". $date100 ."' AS Date)";
	$hours_since_100  = $wpdb->get_var($sql); 
	if(is_null($hours_since_100) ){
		$hours_since_100 = 0; 
	}	
	return (int)$hours_since_100 ;
}
public function tost_hook_count ( $id, $tost_replacement_date ){
	global $wpdb;

	$flightSheet = $wpdb->prefix . "cloud_base_pdp_flight_sheet";		
	$sql= "SELECT count(*) FROM " . $flightSheet . " WHERE Glider='". $id ."' AND Date > CAST('". $tost_replacement_date ."' AS Date)";
	$tost_releases  = $wpdb->get_var($sql); 
	if(is_null( $tost_releases)){
		$tost_releases = 0 ; 
	}
	return $tost_releases ;
}


}
	

