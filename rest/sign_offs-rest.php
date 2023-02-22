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
         	'permission_callback' => array($this, 'cloud_base_dummy_access_check' ),), 
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
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_signoffs_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),        	 		      		
      	  )
      	)
      );	                             
    }
// call back for signoffs:	
	public function cloud_base_signoffs_get_callback( \WP_REST_Request $request) {
		global $wpdb;
	    $table_name = $wpdb->prefix . "cloud_base_member_signoffs";    
	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";    
	    $cloud_base_authoritys = get_option('cloud_base_authoritys');
	    $filter_string ="";
	    $valid_filters = array( 'authority'=>'authority_id', 'signoff'=>'signoff_id', 'no_fly'=>'no_fly' );
     	$valid_keys = array_keys($valid_filters );
     	
     	if (isset($request['fly_list'])){
     		$sql = "SELECT DISTINCT m.member_id  FROM ". $table_name . " m INNER JOIN " . $table_types  . " t ON m.signoff_id = t.id WHERE t.no_fly = 1 AND m.date_expire <= NOW()" ;     		
     	}  elseif (isset($request['no_fly'])){
    		$sql = "SELECT DISTINCT m.member_id  FROM ". $table_name . " m INNER JOIN " . $table_types  . " t ON m.signoff_id = t.id WHERE t.no_fly = 1 AND m.date_expire >= NOW()" ;      	     		
      	}  elseif (isset($request['missing'])){
      		// select all active members 
      		// loop over all members searching signoff table for requested missing signoff. 
      		// when not found, add member to list. 
      		// return list.  		 	
      	} elseif (isset($request['expired'])){
    		$sql = $wpdb->prepare( "SELECT member_id  FROM {$table_name} WHERE signoff_id= %d AND date_expire <= NOW()",  $request['expired'] );      	     		      	
       	} elseif (isset($request['signoff'])){
     		$sql = $wpdb->prepare( "SELECT member_id  FROM {$table_name } WHERE signoff_id= %d AND date_expire >= NOW()",  $request['signoff'] );      	     		      	      	
       	} elseif (isset($request['member_id'])){
       		$sql = $wpdb->prepare( "SELECT *  FROM {$table_name } WHERE member_id= %d ",  $request['member_id'] );     
       	} elseif ( isset($request['id']) ) {
       		$sql = $wpdb->prepare( "SELECT *  FROM {$table_name } WHERE member_id= %d ",  $request['id'] );     
		} else {
			$sql = $wpdb->prepare( "SELECT *  FROM {$table_name } WHERE member_id= %d ",   get_current_user_id() );     
 	    } 
 	    	    	   	         	    
        $items = $wpdb->get_results( $sql, OBJECT);           
        foreach( $items as $i=>$v){
        	$user = get_user_by('ID', $v->member_id);
        	if ( $user != false ){
        		$user_roles = ( array ) $user->roles;
        		if(in_array('inactive', $user_roles )){
        			unset($items[$i]);
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
	public function cloud_base_signoffs_post_callback( \WP_REST_Request $request) {
		global $wpdb;
	    $table_name = $wpdb->prefix . "cloud_base_member_signoffs";    
	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";    
	    if( !isset($request['member_id'] )){
	    	return new \WP_Error( 'missing_member_id', esc_html__( 'Missing member id.', 'my-text-domain' ), array( 'status' => 401 ) );
	    }
	    if( !isset($request['signoff_id'] )){
	    	return new \WP_Error( 'missing_signoff_id', esc_html__( 'Missing signoff id.', 'my-text-domain' ), array( 'status' => 401 ) );
	    }
	    if( !isset($request['effective_date'] )){
	    	return new \WP_Error( 'missing_date', esc_html__( 'Missing effective date.', 'my-text-domain' ), array( 'status' => 401 ) );
	    }
	    		
		$member_id = $request['member_id'];	
		$authority_id = get_current_user_id();	
		$signoff_id = $request['signoff_id'];				
		$date_effective = new \DateTime($request['effective_date']);	
		$date_expire = new \DateTime($request['effective_date']);	
		$date_expire = cb_expire($date_expire, $signoff_id);

 		$sql = $wpdb->prepare("SELECT authority FROM  {$table_types} WHERE id = ", $signoff_id );	 	
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
				if ($wpdb->update( $table_name, array ('member_id' => $old_data->member_id, 'Signoff_id' => $old_data->Signoff_id, 'date_entered' => date('Y-m-d'), 
				'date_effective' => $date_effective->format('Y-m-d'), 'date_expire' => $date_expire->format('Y-m-d'), 'authority_id' => get_current_user_id()),
				array('id' => $old_data->id)) ){				
				return new \WP_REST_Response ('SUCCESS: entry created', 200);	
				} else {
				// If the code somehow executes to here something bad happened return a 500.
					return new \WP_Error( 'unable_to_create', esc_html__( ' Unable ot create new record.', 'my-text-domain' ), array( 'status' => 500 ) );
				}
			}
		}

	}
	public function cloud_base_signoffs_edit_callback( \WP_REST_Request $request) {
		global $wpdb;
	    $table_name = $wpdb->prefix . "cloud_base_member_signoffs";    
	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";    
	    if( !isset($request['id'] )){
	    	return new \WP_Error( 'missing_record_id', esc_html__( 'Missing record Id.', 'my-text-domain' ), array( 'status' => 401 ) );
	    }
	    if( !isset($request['effective_date'] )){
	    	return new \WP_Error( 'missing_date', esc_html__( 'Missing effective date.', 'my-text-domain' ), array( 'status' => 401 ) );
	    }
		$record_id = $request['id'];			
 		$date_effective = new \DateTime($request['effective_date']);	
 		$date_expire = new \DateTime($request['effective_date']);	

 		$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d", $record_id);	 	
 		$old_data = $wpdb->get_row($sql); 	
 		$sql = $wpdb->prepare("SELECT authority FROM  {$table_types} WHERE id = ", $old_data->signoff_id);	 	
 		$authority =  $wpdb->get_var($sql);
 		if (current_user_can($authority )){   // && (get_current_user_id() != $old_data->member_id );
 			$date_expire = cb_expire($date_expire, $old_data->Signoff_id); 	
 			if ($wpdb->update( $table_name, array ('member_id' => $old_data->member_id, 'Signoff_id' => $old_data->Signoff_id, 'date_entered' => date('Y-m-d'), 
 				'date_effective' => $date_effective->format('Y-m-d'), 'date_expire' => $date_expire->format('Y-m-d'), 'authority_id' => get_current_user_id()),
 				array('id' => $record_id)) ){				
 				return new \WP_REST_Response ($record_id, 200);
	
 			} else {
 				return new \WP_Error( 'update_failed', esc_html__( 'Unable to update record.', 'my-text-domain' ), array( 'status' => 400 ) );	  
 			}
 		} else {
 			return new \WP_Error( 'not_authorized', esc_html__( 'Not Authorized', 'my-text-domain' ), array( 'status' => 401 ) );
 		}
	}
	public function cloud_base_signoffs_delete_callback( \WP_REST_Request $request) {
 		global $wpdb;
 	    $table_name = $wpdb->prefix . "cloud_base_member_signoffs";    
 
  		$authority =  $wpdb->get_var($sql);
  		if (current_user_can($authority )){   // && (get_current_user_id() != $old_data->member_id );
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
	public function cb_expire_date($start_date, $period, $fixed_date ){
	// function to calculate the expire date. 
			switch($period ){
			case "monthly":
				$start_date->modify('+1 month');
			break;
			case "quarterly":
				$start_date->modify('+3 month');
			break;
			case "yearly":
				$start_date->modify('+1 year');
			break;
			case "biennial":
				$start_date->modify('+2 year');
			break;
			case "fixed":
				$start_date = new \DateTime($fixed_date );
			break;
			case "no_expire":
				 $start_date = new \DateTime('2099-12-31');
			break;
			case "yearly-eom":
				 $start_date->modify('+1 year');
				 $start_date->modify('last day of this month');
			break;
			case "biennial-eom":
				$start_date->modify('+2 year');
				$start_date->modify('last day of this month');
			break;
			case "dues":
				$end_date = new \DateTime($fixed_date );
				$year = date("Y") + 1 ;		
				// create a new date using the month and day passed in Start Date and either 
				// a year one or two years from now depending on what month it is now. 
				$start_date = new \DateTime($end_date->format('m')."/". $end_date->format('d')."/".$year);
				break;
			default:
		}	
		return($start_date);	
	}
	public function cb_expire($start_date, $signoff_id){
// do the database look up here. the call expire_date	
		global $wpdb;
	 	$table_signoffs = $wpdb->prefix . "cloud_base_signoffs_types";
	 	$sql = $wpdb->prepare("SELECT * FROM {$table_signoffs} WHERE `id` = %d", $signoff_id);	 	
  		$signoff_duration = $wpdb->get_row($sql);
		$date_expire = $this->cb_expire_date($start_date, $signoff_duration->period, $signoff_duration->fixed_date);
		return($date_expire);	
	}
			
}

