<?php
/**
 * The rest functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/rest
 */

/**
 * The REST functionality of the plugin.
 *
 * Defines the plugin name, version, and examples to create your REST access
 * methods. Don't forget to validate and sanatize incoming data!
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/rest
 * @author     Dave Johnson <johnson.s.david@pm.me>
 */

class Cloud_Base_Fees extends Cloud_Base_Rest {
	public function register_routes() {     
     $this->resource_path = '/fees' . '(?:/(?P<id>[\d]+))?';    
     register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_fees_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_fees_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_fees_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_fees_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),          	 		      		
    	 		      		
      	  )
      	)
      );      	                         
    }
 	
	public function cloud_base_fees_get_callback( \WP_REST_Request $request) {
//		$params = $request->get_params();
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_tow_fees";	
// fields to return. 		
		$valid_fields = array('id'=>'id' , 'altitude'=>'altitude', 'charge'=>'charge', 'hourly'=>'hourly', 'hook_up'=>'hook_up');
 		$select_string = $this->select_fields($request, $valid_fields);
// process filters.  	  
 	    $valid_filters = array('altitude'=>'altitude' , 'charge'=>'charge', 'hourly'=>'hourly', 'hook_up'=>'hook_up' );
	    $filter_string = $this->select_filters($request, $valid_filters);
	
		if ($request['id'] != null){	
		// return the current fee for item requested
			$sql = $wpdb->prepare("SELECT {$select_string} FROM {$table_name} s WHERE {$filter_string} AND `id` = %d" ,  $request['id'] );		
		} else {
		// return all current fees. 
  	        $sql = "SELECT {$select_string} FROM {$table_name} s WHERE {$filter_string} ORDER BY altitude ASC ";	
		}

		$items = $wpdb->get_results( $sql, OBJECT);

		if( $wpdb->num_rows > 0 ) {
			return new \WP_REST_Response ($items);
//			wp_send_json($items);
 		 } else {
		// should not get here normally but if it happens.
     	 	return rest_ensure_response( 'Fee not avaliable.' );
		}
//	    wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500);
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_fees_post_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_tow_fees";		

		if (!empty($request['altitude'])){
			$altitude = $request['altitude'];
		} else {
//		 	wp_send_json_error(array('message'=>'missing altitude.'), 400);
			return new \WP_Error( 'rest_api_sad', esc_html__( 'missing altitude.', 'my-text-domain' ), array( 'status' => 400 ) );
		}
		if (!empty($request['charge'])){
				$fee = $request['charge'];
		} else {
			$fee = '0';
		}		
		if (!empty($request['hook_up'])){
				$hook_up = $request['hook_up'];
		} else {
			$hook_up = '0';
		}		
		if (!empty($request['hourly_fee'])){
				$hourly = $request['hourly_fee'];
		} else {
			$hook_up = '0';
		}	
 	// check it does not exist. 
 		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `altitude` = %s AND valid_until = 0 " , $altitude  );	
		$fees = $wpdb->get_row( $sql, OBJECT);		 			
		if( $wpdb->num_rows > 0 ) {
			return rest_ensure_response( 'Already exists id= '. $fees->id );
 		 } else {
		 	$sql =  $wpdb->prepare("INSERT INTO {$table_name} (altitude, charge, hook_up, hourly, valid_until ) VALUES ( %s, %f, %f, %f, %s) " , $altitude, $fee, $hook_up, $hourly, "0");	
			$wpdb->query($sql);
			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `altitude` = %d AND valid_until = 0 " , $altitude  );	
			$items = $wpdb->get_row( $sql, OBJECT);	
			if( $wpdb->num_rows > 0 ) {
				return new \WP_REST_Response ($items);
//				wp_send_json($items);
 		 	} else {
// 		 		  wp_send_json_error(array('message'=>'Fee not added.'), 404);
     	 		return new \WP_Error( 'rest_api_sad', esc_html__( 'Fee not added.', 'my-text-domain' ), array( 'status' => 404 ) );
			}
	    }
//	    wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500);
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong .', 'my-text-domain' ), array( 'status' => 500 ) );	
	}
	public function cloud_base_fees_edit_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_tow_fees";		
//		$fee_id =  $request['id'];
		$altitude = $request['altitude'];
		$expire_date =	date('Y-m-d H:i:s');
		$change = 0;
		
		if ($altitude  != null){	
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `altitude` = %s AND valid_until = 0 " ,  $altitude);	

			$fees = $wpdb->get_row( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ) {
// was a new value supplied and is it different than what we already have?			
				if (!empty($request['charge']) && $request['charge'] != $fees->charge ){
					$fee = $request['charge'];
					$change = 1;
				} else {
					$fee = $fees->charge;
				}		
// was a new value supplied and is it different than what we already have?			
				if (!empty($request['hook_up'] && $request['hook_up'] != $fees->hook_up )){
					$hook_up = $request['hook_up'];
					$change = 1;
				} else {
					$hook_up = $fees->hook_up;
				}				
// was a new value supplied and is it different than what we already have?			
				if (!empty($request['hourly'] && $request['hourly'] != $fees->hourly )){
					$hourly = $request['hourly'];
					$change = 1;
				} else {
					$hourly = $fees->hourly;
				}				
			}			
			if ($change != 0 ){
			// mark existing recored as nolonger valid by setting the valin_until to now.
	       		$sql =  $wpdb->prepare("UPDATE {$table_name} SET `valid_until`= now() WHERE `id` = %d " , $fees->id );
			 	$wpdb->query($sql);
		    // create new record with valid_until = 0. 
				$sql =  $wpdb->prepare("INSERT INTO {$table_name} (altitude, charge, hook_up, hourly, valid_until ) VALUES ( %s, %f, %f, %f, %s) " , 
				$altitude, $fee, $hook_up, $hourly, "0");	
				$wpdb->query($sql);			

				$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `altitude` = %d AND valid_until = 0 " , $altitude  );	
				$items = $wpdb->get_row( $sql, OBJECT);	
				if( $wpdb->num_rows > 0 ) {
					return new \WP_REST_Response ($items);
//					wp_send_json($items);
				} else {
//					wp_send_json_error(array('message'=>'Update failed.', 'altitude'=>$altitude), 400);
		    		return new \WP_Error( 'update Failes', esc_html__( 'Update failes. ', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			 return rest_ensure_response( 'Record Updated= '. $ITEMS->id );

//				wp_send_json(array('message'=>'Record Updated'), 201 );
		    } else {
//				wp_send_json_error(array('message'=>'Updates identical to existing record.', 'altitude'=>$altitude), 400);
		    	return new \WP_Error( 'nothing changed', esc_html__( 'Updates identical to existing record. ', 'my-text-domain' ), array( 'status' => 400 ) );
		    }		    
		}
//		wp_send_json_error(array('message'=>'Record not found.', 'altitude'=>$altitude), 404);
		return new \WP_Error( 'not found', esc_html__( 'Record Not found.', 'my-text-domain' ), array( 'status' => 404 ) );		

		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_fees_delete_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_tow_fees";	
		$fee_id =  $request['id'];
		
		if ($fee_id  != null){	
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d AND valid_until = 0 " ,  $fee_id );	
			$expire_date =	date('Y-m-d H:i:s');
			$items = $wpdb->get_results( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ) {
		        $sql =  $wpdb->prepare("UPDATE {$table_name} SET `valid_until`= now() WHERE `id` = %d " , $fee_id );
				$wpdb->query($sql);
				return rest_ensure_response( 'Deleted = '. $items->id );
//				wp_send_json(array('message'=>'Deleted', 'id'=>$fee_id), 202 );
			}
		} else{
//			wp_send_json_error(array('message'=>'Record not found.', 'id'=>$fee_id), 404);
			return new \WP_Error( 'not found', esc_html__( 'Record Not found.', 'my-text-domain' ), array( 'status' => 404 ) );

		}
	}
}

	
	

