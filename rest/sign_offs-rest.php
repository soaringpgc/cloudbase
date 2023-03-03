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
class Cloud_Base_Sign_offs extends Cloud_Base_Rest {

	public function register_routes() {	              
     $this->resource_path = '/sign_off' . '(?:/(?P<id>[\d]+))?';    register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_dummy_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_signoffs_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),        	 		      		
      	  )
      	));
      	$this->resource_path = '/no_fly' . '(?:/(?P<id>[\d]+))?';    register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_get_no_fly' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_dummy_access_check' ),), 
      	)
      );	                             
    }
// call back for signoffs:	
	public function cloud_base_signoffs_get_no_fly( \WP_REST_Request $request) {
		global $wpdb;
	    $table_signoffs = $wpdb->prefix . "cloud_base_member_signoffs";    
	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";    
	    $cloud_base_authoritys = get_option('cloud_base_authoritys');
	    $filter_string ="";
	    $valid_filters = array( 'authority'=>'authority_id', 'signoff'=>'signoff_id', 'no_fly'=>'no_fly' );
     	$valid_keys = array_keys($valid_filters );
     	
     	if (isset($request['summary'])){
     		// actually want the fly list. 
     		$sql = "SELECT DISTINCT s.member_id FROM " . $table_signoffs . " s inner join " . $table_types . " t 
   	 			on s.Signoff_id = t.id inner join wp_users a on a.id = s.member_id WHERE t.no_fly = 1 AND s.date_expire <= NOW()" ;           			
     	} else {
     		$sql = "SELECT s.member_id, t.signoff_type, s.date_expire FROM " . $table_signoffs . " s inner join " . $table_types . " t 
   	 			on s.Signoff_id = t.id inner join wp_users a on a.id = s.member_id WHERE t.no_fly = 1 AND s.date_expire <= NOW()" ;      
      	} 
        $items = $wpdb->get_results( $sql, OBJECT);  
         	// filter out the inactive membets.      
        foreach( $items as $i=>$v){
        	$user = get_user_by('ID', $v->member_id);        	
        	if ( $user != false ){
        		$user_roles = ( array ) $user->roles;
        		if(in_array('inactive', $user_roles )){
        			unset($items[$i]);
        		}  else {
        		    $first_name = get_user_meta($v->member_id, 'first_name', true);
        			$last_name = get_user_meta($v->member_id, 'last_name', true);
					$v->name = $first_name . ", " . $last_name;
        		}    
        	} else {
        		unset($items[$i]);        	
        	}
        }        
        if ($wpdb->last_error){
				return new \WP_Error( $wpdb->last_error, esc_html__( ' Unable to retrive data.', 'my-text-domain' ), array( 'status' => 500 ) );        
        }
 	    return new \WP_REST_Response ($items);	
	}
	public function cloud_base_signoffs_get_callback( \WP_REST_Request $request) {
		global $wpdb;
	    $table_name = $wpdb->prefix . "cloud_base_member_signoffs";    
	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";    
	    $cloud_base_authoritys = get_option('cloud_base_authoritys');
	    $filter_string ="";
	    $valid_filters = array( 'authority'=>'authority_id', 'signoff'=>'signoff_id', 'no_fly'=>'no_fly' );
     	$valid_keys = array_keys($valid_filters );
     	
//      	if (isset($request['fly_list'])){
//      		$sql = "SELECT DISTINCT m.member_id  FROM ". $table_name . " m INNER JOIN " . $table_types  . " t ON m.signoff_id = t.id WHERE t.no_fly = 1 AND m.date_expire <= NOW()" ;     		
//      	}  elseif (isset($request['no_fly'])){
//     		$sql = "SELECT DISTINCT m.member_id  FROM ". $table_name . " m INNER JOIN " . $table_types  . " t ON m.signoff_id = t.id WHERE t.no_fly = 1 AND m.date_expire >= NOW()" ;      	     		
//       	}  elseif (isset($request['missing'])){
		 	
  	     		      	      	      	
      	if (isset($request['expired'])){
    		$sql = $wpdb->prepare( "SELECT member_id  FROM {$table_name} WHERE signoff_id= %d AND date_expire <= NOW()",  $request['expired'] );      	     		      	
       	} elseif (isset($request['signoff'])){
     		$sql = $wpdb->prepare( "SELECT member_id  FROM {$table_name } WHERE signoff_id= %d AND date_expire >= NOW()",  $request['signoff'] );      	     		      	      	
       	} elseif (isset($request['member_id'])){
       		if(isset($request['update'])){
     			$sql = $wpdb->prepare( "SELECT m.member_id, m.id, t.signoff_type, t.authority, m.date_expire FROM {$table_name }  m INNER JOIN {$table_types } t ON m.signoff_id = t.id 
     				WHERE  m.member_id = %d",  $request['member_id'] ); 
     		} else { 
       			$sql = $wpdb->prepare( "SELECT *  FROM {$table_name } WHERE member_id= %d ",  $request['member_id'] );    
       		} 
       	} elseif ( isset($request['id']) ) {
       		$sql = $wpdb->prepare( "SELECT *  FROM {$table_name } WHERE member_id= %d ",  $request['id'] );     
		} else {
			$sql = $wpdb->prepare( "SELECT *  FROM {$table_name } WHERE member_id= %d ",   get_current_user_id() );     
 	    } 
 //return new \WP_REST_Response (get_user_by('ID', $request['current_user']));  	    	    	   	         	    
        $items = $wpdb->get_results( $sql, OBJECT);  
 		if(isset($request['update'])){
 			foreach( $items as $i=>$v){			
				if(!current_user_can($v->authority))	{
					unset($items[$i]);   
				}		
 			}
 		}

        $no_role = wp_get_users_with_no_role(); 
        $args = array('role'    => 'inactive', 'fields' => 'ID');
		$inactive = get_users( $args );
		$no_list = array_merge($inactive, $no_role );	   	
		   
        foreach( $items as $i=>$v){ // filter out users who are inactive         
        	if (in_array( $v->member_id, $no_list)) {
        		unset($items[$i]);          	
        	}; 
        }        
        if ($wpdb->last_error){
				return new \WP_Error( $wpdb->last_error, esc_html__( ' Unable to retrive data.', 'my-text-domain' ), array( 'status' => 500 ) );        
        }
 	    return new \WP_REST_Response ($items);
	   	
	}	
	public function cloud_base_signoffs_post_callback( \WP_REST_Request $request) {
		global $wpdb;
	    $table_name = $wpdb->prefix . "cloud_base_member_signoffs";    
	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";    
	    if( !isset($request['member_id'] )){
	    	return new \WP_Error( 'missing_member_id', esc_html__( 'Missing member id.', 'my-text-domain' ), array( 'status' => 400 ) );
	    }
	    if( !isset($request['signoff_id'] )){
	    	return new \WP_Error( 'missing_signoff_id', esc_html__( 'Missing signoff id.', 'my-text-domain' ), array( 'status' => 400 ) );
	    }
	    if( !isset($request['effective_date'] )){
	    	return new \WP_Error( 'missing_date', esc_html__( 'Missing effective date.', 'my-text-domain' ), array( 'status' => 400 ) );
	    }
	    		
		$member_id = $request['member_id'];	
		$authority_id = get_current_user_id();	
		$signoff_id = $request['signoff_id'];				
		$date_effective = new \DateTime($request['effective_date']);	
		$date_expire = new \DateTime($request['effective_date']);	
		$date_expire = $this->cb_expire($date_expire, $signoff_id);

 		$sql = $wpdb->prepare("SELECT authority FROM  {$table_types} WHERE id = %d", $signoff_id );	 	
 		$authority =  $wpdb->get_var($sql);
//		if($authority == "read" || (current_user_can($authority) &&  (get_current_user_id() != $old_data->member_id ) ) ) {
 		if (current_user_can($authority )){   // && (get_current_user_id() != $old_data->member_id );			
			// check to see if this signoff already exists. 
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `member_id` = %d AND `Signoff_id` = %d", $member_id, $signoff_id );
			$old_data = $wpdb->get_row($sql); 
			//old record does not exist. 	
			if ($wpdb->num_rows == null) {
				if ($wpdb->insert( $table_name, array ('member_id' => $member_id, 'signoff_id' => $signoff_id, 'date_entered' =>  date('Y-m-d'), 
				'date_effective' => $date_effective->format('Y-m-d'), 'date_expire' => $date_expire->format('Y-m-d'), 'authority_id' => get_current_user_id()) )){			
				return new \WP_REST_Response ('SUCCESS: entry created', 201);	
				} else {
				// If the code somehow executes to here something bad happened return a 500.
					return new \WP_Error( 'unable_to_create', esc_html__( ' Unable ot create new record.', 'my-text-domain' ), array( 'status' => 500 ) );
				}
			} else {
			// we found a record lets update it. The uses SHOULD have updated the old record. But they missed it
			// rather than make them redo it just update it. 
				if ($wpdb->update( $table_name, array ('member_id' => $old_data->member_id, 'signoff_id' => $old_data->signoff_id, 'date_entered' => date('Y-m-d'), 
				'date_effective' => $date_effective->format('Y-m-d'), 'date_expire' => $date_expire->format('Y-m-d'), 'authority_id' => get_current_user_id()),
				array('id' => $old_data->id)) ){				
				return new \WP_REST_Response ('SUCCESS: entry created', 200);	
				} else {
				// If the code somehow executes to here something bad happened return a 500.
					return new \WP_Error($wpdb->last_query, esc_html__( ' Unable ot create new record.', 'my-text-domain' ), array( 'status' => 500 ) );
				}
			}
		}

	}
	public function cloud_base_signoffs_edit_callback( \WP_REST_Request $request) {
		global $wpdb;
	    $table_name = $wpdb->prefix . "cloud_base_member_signoffs";    
	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";    
	    if( !isset($request['record_id'] )){
	    	return new \WP_Error( 'missing_record_id', esc_html__( 'Missing record Id.', 'my-text-domain' ), array( 'status' => 400 ) );
	    }
	    if( !isset($request['effective_date'] )){
	    	return new \WP_Error( 'missing_date', esc_html__( 'Missing effective date.', 'my-text-domain' ), array( 'status' => 400 ) );
	    }
		$record_id = $request['record_id'];			
 		$date_effective = new \DateTime($request['effective_date']);	
 		$date_expire = new \DateTime($request['effective_date']);	

 		$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d", $record_id );	 	
 		$old_data = $wpdb->get_row($sql); 	
// 	return new \WP_REST_Response ($old_data ); 
 		$sql = $wpdb->prepare("SELECT authority FROM  {$table_types} WHERE id = %d", $old_data->authority_id);	 	
// 	return new \WP_REST_Response (get_current_user_id() );	
 		$authority =  $wpdb->get_var($sql);
 		if (current_user_can($authority )){   // && (get_current_user_id() != $old_data->member_id );
 			$date_expire = $this->cb_expire($date_expire, $old_data->signoff_id); 	
 			if ($wpdb->update( $table_name, array ( 'date_entered' => date('Y-m-d'), 'date_effective' => $date_effective->format('Y-m-d'), 'date_expire' => $date_expire->format('Y-m-d'), 'authority_id' => get_current_user_id()),
 				array('id' => $record_id)) ){				
 				return new \WP_REST_Response ($record_id, 200);
	
 			} else {
// 				return new \WP_Error( 'update_failed', esc_html__($wpdb->last_query, 'my-text-domain' ), array( 'status' => 400 ) );	  
 				return new \WP_Error( 'update_failed', esc_html__( 'Unable to update record.', 'my-text-domain' ), array( 'status' => 400 ) );	  
 			}
 		} else {
 			return new \WP_Error( 'not_authorized', esc_html__( 'Not Authorized', 'my-text-domain' ), array( 'status' => 401 ) );
 		}
	}
	public function cloud_base_signoffs_delete_callback( \WP_REST_Request $request) {	
 		global $wpdb;
 	    $table_name = $wpdb->prefix . "cloud_base_member_signoffs";  
 	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";     
 	    if( isset($request['record_id'] )){ 
 	    	$record_id = $request['record_id'];		
 	    	$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d", $record_id );	 	
 			$old_data = $wpdb->get_row($sql); 	
 			$sql = $wpdb->prepare("SELECT authority FROM  {$table_types} WHERE id = %d", $old_data->authority_id);	 	
  			$authority =  $wpdb->get_var($sql);
  			if (current_user_can($authority )){   
				$record_id = $request['record_id'];	 		
	 			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d", $record_id);		
  				$record_tobe_deleted = $wpdb->get_row($sql); 
  				if ($record_tobe_deleted != null ){
  					$wpdb->delete( $table_name, array('id' => $record_id));	
  					return new \WP_REST_Response (null, 204);
  				} else {
 				 	return new \WP_Error( 'not_found', esc_html__( 'Not Found', 'my-text-domain' ), array( 'status' => 404 ) );
        	    }
			} else {
  				return new \WP_Error( 'not_authorized', esc_html__( 'Not Authorized', 'my-text-domain' ), array( 'status' => 401 ) );
  			}
  		}
	}	
			
}

