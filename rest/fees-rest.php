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
        //    	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
    	 		      		
      	  )
      	)
      );      	                
             
    }
 	
	public function cloud_base_fees_get_callback( \WP_REST_Request $request) {
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_tow_fees";	
		$fee_id =  $request['id'];
		$audit = $request['audit'];
		echo $audit;

		if ($fee_id  != null){	
			$sql = $wpdb->prepare("SELECT altitude, charge, hook_up FROM {$table_name} WHERE `id` = %d AND valid_until = 0 " ,  $fee_id );		
		} else {
			if ($audit == '1'){
				$sql = "SELECT * FROM ". $table_name . " ORDER BY altitude DESC ";					
			} else {
            	$sql = "SELECT altitude, charge, hook_up FROM ". $table_name . " WHERE valid_until = 0 ORDER BY altitude ASC ";	
            }
		}
		$fees = $wpdb->get_results( $sql, OBJECT);

		if( $wpdb->num_rows > 0 ) {
			wp_send_json($fees);
 		 } else {
		// If the code somehow executes to here something bad happened return a 500.
      	return rest_ensure_response( 'Fee not avaliable.' );
		}
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );

	}	
	public function cloud_base_fees_post_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_tow_fees";		

		if (!empty($request['altitude'])){
			$altitude = $request['altitude'];
		} else {
			return new \WP_Error( 'rest_api_sad', esc_html__( 'missing altitude.', 'my-text-domain' ), array( 'status' => 400 ) );
		}

		if (!empty($request['fee'])){
				$fee = $request['fee'];
		} else {
			return new \WP_Error( 'rest_api_sad', esc_html__( 'missing fee.', 'my-text-domain' ), array( 'status' => 400 ) );
		}		

		if (!empty($request['hook_up'])){
				$hook_up = $request['hook_up'];
		} else {
			$hook_up = '0';
		}		
	
 	// check it does not exist. 
 		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `altitude` = %d AND valid_until = 0 " , $altitude  );	
		$fees = $wpdb->get_row( $sql, OBJECT);		 			
		if( $wpdb->num_rows > 0 ) {
			return rest_ensure_response( 'Already exists id= '. $fees->id );
 		 } else {
		 	$sql =  $wpdb->prepare("INSERT INTO {$table_name} (altitude, charge, hook_up, valid_until ) VALUES ( %d, %f, %f, %s) " , $altitude, $fee, $hook_up, "0");	
			$wpdb->query($sql);
						
			wp_send_json(array('message'=>'Record Added'), 201 );

  //       	return new \WP_Error( 'rest_api_happy', esc_html__( 'Record added.', 'my-text-domain' ), array( 'status' => 201 ) );

	    }
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong .', 'my-text-domain' ), array( 'status' => 500 ) );
	
		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_fees_edit_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_tow_fees";		
//		$fee_id =  $request['id'];
		$altitude = $request['altitude'];
		$expire_date =	date('Y-m-d H:i:s');
		$change = 0;

		
		if ($altitude  != null){	
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `altitude` = %d AND valid_until = 0 " ,  $altitude);	

			$fees = $wpdb->get_row( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ) {
// was a new value supplied and is it different than what we already have?			
				if (!empty($request['fee']) && $request['fee'] != $fees->charge ){
					$fee = $request['fee'];
					$change = 1;
				} else {
					$fee = $fees->charge;
				}		

				if (!empty($request['hook_up'])){
					$hook_up = $request['hook_up'];
					$change = 1;
				} else {
					$hook_up = $fees->hook_up;
				}				
			}
			if ($change != 0 ){
	       		$sql =  $wpdb->prepare("UPDATE {$table_name} SET `valid_until`= now() WHERE `id` = %d " , $fees->id );
	
		        echo $sql;
			// 	$wpdb->query($sql);
		
				$sql =  $wpdb->prepare("INSERT INTO {$table_name} (altitude, charge, hook_up, valid_until ) VALUES ( %d, %f, %f, %s) " , 
				$altitude, $fee, $hook_up, "0");	
		//		$wpdb->query($sql);
		     	echo $sql;			
		 		wp_send_json(array('message'=>'Record Updated'), 201 );

		    } else {
		    	return new \WP_Error( 'nothing changed', esc_html__( 'Updates identical to existing record. ', 'my-text-domain' ), array( 'status' => 400 ) );
		    }		    
		}
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
			$fees = $wpdb->get_results( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ) {
		        $sql =  $wpdb->prepare("UPDATE {$table_name} SET `valid_until`= now() WHERE `id` = %d " , $fee_id );
	
			$wpdb->query($sql);
				wp_send_json(array('message'=>'Deleted', 'id'=>$fee_id), 202 );
			}
			return new \WP_Error( 'not found', esc_html__( 'Record Not found.', 'my-text-domain' ), array( 'status' => 404 ) );
		}

	}

	
	
}

	
	

