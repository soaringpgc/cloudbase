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
 */
class Cloud_Base_Sign_off_types extends Cloud_Base_Rest {

	public function register_routes() {	              
     $this->resource_path = '/sign_off_types' . '(?:/(?P<id>[\d]+))?';    
     register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'cloud_base_signoffs_get_callback' ), 
        	'permission_callback' => array($this, 'cloud_base_members_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_signoffs_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	                        
    }
// call back for signoffs:	
	public function cloud_base_signoffs_get_callback( \WP_REST_Request $request) {		
		global $wpdb;
	    $table_name = $wpdb->prefix . "cloud_base_signoffs_types";    
	    $cloud_base_authoritys = get_option('cloud_base_authoritys');
	    // authority array is stored in WP options, It is created/updated on activation           
//	    $value_label_authority = $this->cloud_base_authoritys;
							
// NTFS: this array has the new "cb_" capabilities and the "PGC" capabilities from  
// and earily version of sign offs. eventualy all shoudl be over written with new. 
// forget all that on activation PGC stuff is copyied and updated. (I hope)
//
// authority array is stored in WP options, It is created/updated on activation 

		if(isset($request['id'])){
		   $sql= $wpdb->prepare( "SELECT * FROM {$table_name} WHERE `active` = 1 AND `id` = %d ",  $request['id'] ); 
		} elseif (isset($request['all'])) {
			$sql = "SELECT * FROM {$table_name} WHERE `active` = 1 AND `applytoall` = 1 ";
		} else {
			$sql = "SELECT * FROM {$table_name} WHERE `active` = 1 ";
		}
 		$items = $wpdb->get_results( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {
			foreach($items as $k=> $v){
//	NTFS: The CAPABILITY is stored in the database, however it does not look pretty
// use the above array to reverse lookup the primary AUTHORITY that can signoff an item. 			
			$items[$k]->authority_label =  $cloud_base_authoritys[$v->authority];
// likewise for the period. sending both dthe period and period lable. 
// kinda a pain as these two arrays need to be in two different locations. 			
			$items[$k]->period_label =  $this->value_lable_period[$v->period];
			}			
			return new \WP_REST_Response ($items);
 		 } else {
			return new \WP_Error( 'no_types', esc_html__( 'no Types avaliable.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_signoffs_post_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_signoffs_types";
//		$value_label_authority = get_option('cloud_base_authoritys');
		if (!empty($request['signoff_type'])){
			$title = $request['signoff_type'];
		} else {
    		return new \WP_Error( 'Sign Off Required', esc_html__( 'Sign Off Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
		} 			 
    	if (!empty($request['authority'])){
    	  	$authority  =  $request['authority'];
    	} else{
    		return new \WP_Error( 'authority required', esc_html__( 'authority Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
        }
    	if (!empty($request['period'])) {
    	  	$period  = $request['period'];
    	  	if($period == 'fixed'){
    	  		if (!empty($request['fixed_date'])){
    	  			$fixed_date  =  $request['fixed_date'];
    			} else{
     				return new \WP_Error( 'date required', esc_html__( 'Date required for fixed period.', 'my-text-domain' ), array( 'status' => 404 ) );  
    			}	    	  	
    	  	}
    	} else{
     		return new \WP_Error( 'period required', esc_html__( 'Period Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
    	}	   	
 	   // check it does not exist. 
 		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `signoff_type` = %s " , $title  );	
		$items = $wpdb->get_row( $sql, OBJECT);		 			
		if( $wpdb->num_rows > 0 ) {	
			return rest_ensure_response( 'Sign off already exists id= '. $items->id );
 		 } else {
		 	$sql =  $wpdb->prepare("INSERT INTO {$table_name} (signoff_type,  authority, period, fixed_date, user_id) 		 	
		 	VALUES ( %s, %s, %s, %s, %d) " , $title, $authority, $period, $fixed_date, get_current_user_id());			 	 		 
			$wpdb->query($sql);	
			// handle checkboxes sepertally
			if (!empty($request['no_fly'] && $request['no_fly']))	{
	        	$sql =  $wpdb->prepare("UPDATE {$table_name} SET `no_fly`=  true  WHERE `id` = %d ",  $id);			        					
			} else {
				$sql =  $wpdb->prepare("UPDATE {$table_name} SET `no_fly` = NULL WHERE `id` = %d ", $id);		
			}
			$wpdb->query($sql);	
			if (!empty($request['applytoall'] && $request['applytoall']))	{
	        	$sql =  $wpdb->prepare("UPDATE {$table_name} SET `applytoall`=  true  WHERE `id` = %d ",  $id);			        					
			} else {
				$sql =  $wpdb->prepare("UPDATE {$table_name} SET `applytoall` = NULL WHERE `id` = %d ", $id);		
			}
			$wpdb->query($sql);							
 		// read it back to get id and send
 			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `signoff_type` = %s " , $title  );	 
			$items = $wpdb->get_row( $sql, OBJECT);
//	NTFS: see above. 
			$items->authority_label =  $this->cloud_base_authoritys[$items->authority];
			$items->period_label =  $this->value_lable_period[$items->period];				
			return new \WP_REST_Response ($items);						
	    }    
	    wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500 );	
	}
	public function cloud_base_signoffs_edit_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_signoffs_types";
//		$value_label_authority = get_option('cloud_base_authoritys');
//		$value_lable_period = array("yearly"=>"Yearly", "biennial"=>"Biennial", "yearly-eom"=>"Yearly-EOM", "biennial-eom"=>"Biennial-EOM", "no_expire"=>"No expire", 
//				"monthly" => "Monthly", "quarterly" => "Quarterly", "fixed"=>"Fixed Date" );		
		$id =  $request['id'];
 		if ($id != null ) {	
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d " ,  $id) ;	
			$items = $wpdb->get_row( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ) { 			
// was a new value supplied 		
				if (!empty($request['signoff_type'])){
					$title = $request['signoff_type'];
				} else {
					$title = $items->signoff_type;
				}		
// was a new value supplied 		
				if (!empty($request['authority'] ) ){
					$authority = $request['authority'];					
				} else {
					$authority = $items->authority;
				}	 			
// was a new value supplied 
   				if (!empty($request['period'])) {
    	  			$period  = $request['period'];
    	  			if($period == 'fixed'){
    	  				if (empty($request['fixed_date'])){
     						return new \WP_Error( 'date required', esc_html__( 'Date required for fixed period.', 'my-text-domain' ), array( 'status' => 404 ) );  
    					}	    	  	
    	  			}					
				} else {
					$period = $items->period;
				}	 			 					 				
 			} else {
			 	return new \WP_Error( 'not_found', esc_html__( 'Record not found.', 'my-text-domain' ), array( 'status' => 400 ) );					
			}
// 	        $sql =  $wpdb->prepare("UPDATE {$table_name} SET `signoff_type`=%s, `authority`=%s , `period`=%s, `fixed_date`=%d, `user_id`=%d, `no_fly`=%d, `applytoall`=%d 
// 	        	WHERE `id` = %d ", $title, $authority, $period, $fixed_date, get_current_user_id(), $no_fly, $applytoall, $id);	
	        	
	        $sql =  $wpdb->prepare("UPDATE {$table_name} SET `signoff_type`=%s, `authority`=%s, `period`=%s, `user_id`=%d 
	        	WHERE `id` = %d ", $title, $authority, $period, get_current_user_id(), $id);			        	
			$wpdb->query($sql);	
// want to keep values as NULL unless actually set. funky way that wpdb->prepare handles nulls
			if ($period == 'fixed')	{
	        	$sql =  $wpdb->prepare("UPDATE {$table_name} SET `fixed_date`=%s, WHERE `id` = %d ", $request['fixed_date'], $id);			        	
			} else {
				$sql =  $wpdb->prepare("UPDATE {$table_name} SET `fixed_date` = NULL WHERE `id` = %d ", $id);		
			}
			$wpdb->query($sql);	

			if (!empty($request['no_fly'] && $request['no_fly']))	{
	        	$sql =  $wpdb->prepare("UPDATE {$table_name} SET `no_fly`=  true  WHERE `id` = %d ",  $id);			        					
			} else {
				$sql =  $wpdb->prepare("UPDATE {$table_name} SET `no_fly` = NULL WHERE `id` = %d ", $id);		
			}
			$wpdb->query($sql);	
			if (!empty($request['applytoall'] && $request['applytoall']))	{
	        	$sql =  $wpdb->prepare("UPDATE {$table_name} SET `applytoall`=  true  WHERE `id` = %d ",  $id);			        					
			} else {
				$sql =  $wpdb->prepare("UPDATE {$table_name} SET `applytoall` = NULL WHERE `id` = %d ", $id);		
			}
			$wpdb->query($sql);															
        // read back 
			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d" , $id  );	
			$items = $wpdb->get_row( $sql, OBJECT);	
			if( $wpdb->num_rows > 0 ) {
									
//	NTFS: see above. 
				$items->authority_label =  $this->cloud_base_authoritys[$items->authority];
				$items->period_label =  $this->value_lable_period[$items->period];				
				return new \WP_REST_Response ($items);
			} else {
		    	return new \WP_Error( 'update Failes', esc_html__( 'Update failed. ', 'my-text-domain' ), array( 'status' => 400 ) );
			}			
		}

	}
	public function cloud_base_signoffs_delete_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_signoffs_types";
		$item_id =  $request['id'];			
		$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d " ,  $item_id );	
		$items = $wpdb->get_row( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {
		    $sql =  $wpdb->prepare("UPDATE  {$table_name} SET active = 0 WHERE `id` = %d " , $items->id );
			$wpdb->query($sql);
			return new \WP_REST_Response ($items);
		} else{
		 	return new \WP_Error( 'not_found', esc_html__( 'Not found. ', 'my-text-domain' ), array( 'status' => 400 ) );
		}	
	} 			
	
}

