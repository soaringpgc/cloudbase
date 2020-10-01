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
class Cloud_Base_pilots extends Cloud_Base_Rest {

	public function register_routes() {
	   
     $this->resource_path = '/pilots' . '(?:/(?P<id>[\d]+))?';
    
      register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_pilots_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),
         	'args' => array('id'=> array('type'=>'integer', 'required'=> false, 'sanitize_callback'=> 'absint'))), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_pilots_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_pilots_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_pilots_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	
	}

// call back for pilots signoffs:	
	public function cloud_base_pilots_get_callback( \WP_REST_Request $request) {
		global $wpdb;
		$valid_roles = array ('cfi_g', 'tow_pilot', 'subscriber', 'CFI_G', 'TOW_PILOT', 'SUBSCRIBER');			
		
		$role = 'subscriber';		
		if (!empty($request['role'])){
	      if (in_array($request['role'], $valid_roles, false)){
			$role = $request['role'];	
		  }
		}

		$pilots = array();	 
		$i =0;
		$users = get_users( [ 'role__in' => [ $role ] ] );	 
     	$table_signoffs = $wpdb->prefix . "pgc_member_signoffs";
     	$table_types = $wpdb->prefix . "pgc_signoffs_types";
		
  	 	$sql =  "SELECT a.id  FROM {$table_signoffs } s inner join {$table_types } t 
  	 			on s.Signoff_id = t.id inner join wp_users a on a.id = s.member_id  WHERE t.no_fly  = 1 AND s.date_expire < current_date() ORDER BY a.user_nicename ";
		 			  	 			
		$raw_no_fly_list= $wpdb->get_results($sql, OBJECT);
		if( $wpdb->num_rows > 0 ){
           $no_fly_list = array();
           foreach ($raw_no_fly_list as $fly){
              $no_fly_list[] = $fly->id;
           	}
           foreach ($users as $user){
           	 $pilots[$i]['ID'] = $user->ID;
           	 $pilots[$i]['first_name'] = $user->first_name;
           	 $pilots[$i]['last_name'] = $user->last_name;
           	 $pilots[$i]['no-fly'] = in_array($user->ID, $no_fly_list, false);
           	 $i++;
           }
           return new \WP_REST_Response ($items);   
        } else {
			return new \WP_Error( 'no_pilots', esc_html__( 'no pilots found.', 'my-text-domain' ), array( 'status' => 204 ) );        
        }         
	}
	
	public function cloud_base_pilots_data_put_callback( \WP_REST_Request $request) {
		global $wpdb;

	}
	
	public function cloud_base_pilots_data_edit_callback( \WP_REST_Request $request) {
		global $wpdb;
		$user_metas = array ('last_name', 'first_name', 'address1', 'address2', 'city', 'state', 
		    'soaringsociety','zip', 'cel', 'tel', 'wrk', 'pvtgliderinsco' , ' pvtinspolicynum'  ,
			'contact1name', 'contact1relationship',  'contact1cel',  'contact1tel',  'contact1address',
  			'contact1city', 'contact1state',  'contact1zip', 'contact2name',  'contact2relationship',
			'contact2cel', 'contact2tel', 'contact2address', 'contact2city',   'contact2state',
  			'contact2zip', 'certificate' , 'cirtissuedate', 'certType', 'endorsements',
            'totalhours', 'gliderflights', 'faibadge',	'pvtgldmake', 'pvtgldmodel','pvtnnumber',
			'pvtcompnum' ,'elt' , 'transponder', 'pvtinsurexpdate', 'pvtinspolicynum' )	;
			
		$table_name = $wpdb->prefix . "pgc_member_signoffs";
		$table_types = $wpdb->prefix . "pgc_signoffs_types";

    //can only update logged in user(self)
    // update pilot data. 
	    $id = get_current_user_id();		
		foreach($user_metas as $user_meta){
		  	if ( isset($request[$user_meta])){
		  		$meta = sanitize_text_field( $request[$user_meta]);
				update_user_meta( $id,$user_meta, $meta);
				if ( get_user_meta($id,  $key, true ) != $meta ){
					return new \WP_Error( 'rest_api_sad', esc_html__( 'ERROR: Unable to update record', 'my-text-domain' ), array( 'status' => 406 ) );		
//					wp_send_json_error( 'ERROR: Unable to update record.', 406 );
				}
			}
		}
			
// update self signoffs. 
		if (!empty($request['signoff_id']) && !empty($request['effective_date'])){					
  		  $sql = $wpdb->prepare("SELECT * FROM {$table_types} WHERE id = %d", $request['signoff_id']); 
  		  $sign_off_type = $wpdb->get_row($sql, OBJECT);
  		  if ($wpdb->num_rows > 0 ){
  	   	    if(user_can($id,  $sign_off_type->authority )){
			  $date_expire = cb_expire_date($date_effective, $sign_off_type->period, $sign_off_type->fixed_date );
			  $sql = "SELECT * from {$table_signoffs} WHERE member_id = ". $member_id ." and signoff_id =  " .$request['signoff_id'];
			  $wpdb->get_row($sql, OBJECT);
			  if ($wpdb->num_rows = 1 ) {
				if ($wpdb->update( $table_name, array ('member_id' => $id, 'Signoff_id' => $signoff_id, 'date_entered' => date('Y-m-d'), 
					'date_effective' => $date_effective->format('Y-m-d'), 'date_expire' => $date_expire->format('Y-m-d'), 'authority_id' => $id),
					array('id' => $record_id)) ){			  
			// read it back to get id and send
             		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %s " , $title  );	
            		$items = $wpdb->get_row( $sql, OBJECT);				
            		return new \WP_REST_Response ($items);	
  
//				wp_send_json(array('message'=>'Record Updared'), 201 );  
 		      } else {
 		      
  		 	    return new \WP_Error( 'rest_api_sad', esc_html__( 'does not exists.', 'my-text-domain' ), array( 'status' => 400 ) );
  		      }	
  		     } else {
  		 	    return new \WP_Error( 'rest_api_sad', esc_html__( 'Not authorized.', 'my-text-domain' ), array( 'status' => 400 ) );
  		     }
  		    } else {
			  return new \WP_Error( 'rest_api_sad', esc_html__( 'invalid Signoff.', 'my-text-domain' ), array( 'status' => 400 ) );
		    }
  		  } 
		}
		return  wp_send_json('SUCCESS: no data', 406);		
	}
	
	// call back for signoffs:	
	public function cloud_base_pilots_signoffs_get_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_signoffs = $wpdb->prefix . "pgc_member_signoffs";
     	$table_types = $wpdb->prefix . "pgc_signoffs_types"; 	

		if (!empty($request['id'])){		
			$id = $request['id'];
			
    		$sql = "SELECT s.id, t.signoff_type, s.date_effective, s.date_expire, s.authority_id, a.display_name as authority FROM " . 
    		$table_signoffs . " s inner join " . $table_types . " t 
        	 on s.Signoff_id = t.id inner join wp_users a on a.id = s.authority_id  WHERE `member_id` =  " . $id ;
 
 			$sql = $wpdb->prepare("SELECT s.id as sign_off_id, t.signoff_type, s.date_effective, s.date_expire, s.authority_id, a.display_name as authority 
 				FROM {$table_signoffs} s inner join {$table_types} t on s.Signoff_id = t.id inner join wp_users a on a.id = s.authority_id  
 				WHERE `member_id` = %d ",  $id) ;  			
			
			$signoffs = $wpdb->get_results( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ){
				wp_send_json( $signoffs); 
			} else {
			return new \WP_Error( 'not_found', esc_html__( 'Pilot not found or has no sign offs', 'my-text-domain' ), array( 'status' => 400 ) );
			}
		return;
		} else {
		// if no id is supplies return avaliable pilots.      	
			$pilots = array();	 
			$i =0;
			$sql = "SELECT DISTINCT( member_id ) FROM {$table_signoffs}";
			$members_with_signoffs_raw = $wpdb->get_results($sql, OBJECT);
			$members_with_signoffs = array();
			foreach ($members_with_signoffs_raw as $fly){
		   		$members_with_signoffs[] = $fly->member_id;
			}			
			$users = get_users( [ 'role__in' => [ 'subscriber' ] ] );	 
			foreach ($users as $user){
				if( in_array($user->ID, $members_with_signoffs, false) ){
					$pilots[$i]['ID'] = $user->ID;
					$pilots[$i]['first_name'] = $user->first_name;
					$pilots[$i]['last_name'] = $user->last_name;
				}
				$i++;
			}
			return rest_ensure_response( $pilots ); 	
		}	
	    wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500);
	
//		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong .', 'my-text-domain' ), array( 'status' => 500 ) );
		/* 
			Process your GET request here.		
		*/
	}	
	public function cloud_base_pilots_signoffs_post_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_signoffs = $wpdb->prefix . "pgc_member_signoffs";
     	$table_types = $wpdb->prefix . "pgc_signoffs_types"; 	


		if (!empty($request['member_id']) && does_user_exist( $request['member_id'] )){	
			$member_id = $wpdb->prepare($request['member_id']);	
		} else {
			wp_send_json_error(array('message'=>'Missing member ID.'), 400);
			return new \WP_Error( 'rest_api_sad', esc_html__( 'missing member ID.', 'my-text-domain' ), array( 'status' => 400 ) );		
		}
		if (!empty($request['authority_id'])  && does_user_exist( $request['authority_id'] )){	
			$authority_id = $wpdb->prepare($request['authority_id']);	
		} else {
			return new \WP_Error( 'rest_api_sad', esc_html__( 'missing authority ID.', 'my-text-domain' ), array( 'status' => 400 ) );		
//		  	wp_send_json_error(array('message'=>'Missing authority ID.'), 400);
		}		
		if (!empty($request['effective_date'])){	
			$date_effective = new \DateTime($request['effective_date']);		
		} else {
//		  	wp_send_json_error(array('message'=>'Effective date missing.'), 400);
			return new \WP_Error( 'rest_api_sad', esc_html__( 'Effective date missing.', 'my-text-domain' ), array( 'status' => 400 ) );		
		}			  

//		$signoff_id = $request['signoff_id'];	
		if (!empty($request['signoff_id'])){		
			
  		  $sql = $wpdb->prepare("SELECT * FROM {$table_types} WHERE id = %d", $request['signoff_id']); 
  		  $sign_off_type = $wpdb->get_row($sql, OBJECT);
  		  if ($wpdb->num_rows > 0 ){
  	   	    if(user_can($authority_id,  $sign_off_type->authority )){
			  $date_expire = cb_expire_date($date_effective, $sign_off_type->period, $sign_off_type->fixed_date );
			  $sql = "SELECT * from {$table_signoffs} WHERE member_id = ". $member_id ." and signoff_id =  " .$request['signoff_id'];
			  $wpdb->get_row($sql, OBJECT);
			  if ($wpdb->num_rows = 0 ) {
			    $sql = $wpdb->prepare("INSERT INTO {$table_signoffs}( member_id, signoff_id, date_entered, date_effective, date_expire, authority_id )
			       VALUES (%d, %d, now(), %d, %d,  %d)", $member_id, $request['signoff_id'], $date_effective, $date_expire, $authority_id  );
				$wpdb->query($sql);						
				wp_send_json(array('message'=>'Record Added'), 201 );  
 		      } else {
//  		     	wp_send_json_error(array('message'=>'Already exists..'), 400);
  		 	    return new \WP_Error( 'rest_api_sad', esc_html__( 'already exists.', 'my-text-domain' ), array( 'status' => 400 ) );
  		      }	
  		     } else {
//  		     	wp_send_json_error(array('message'=>'Not authorized.'), 400);
  		 	    return new \WP_Error( 'rest_api_sad', esc_html__( 'Not authorized.', 'my-text-domain' ), array( 'status' => 400 ) );
  		     }
  		    } else {
//  		    	wp_send_json_error(array('message'=>'Invalid Signoff.'), 400);
			  return new \WP_Error( 'rest_api_sad', esc_html__( 'invalid Signoff.', 'my-text-domain' ), array( 'status' => 400 ) );
		    }
  		  } else {
			return new \WP_Error( 'rest_api_sad', esc_html__( 'missing Signoff.', 'my-text-domain' ), array( 'status' => 400 ) );
//			wp_send_json_error(array('message'=>'Missing Signoff.'), 400);
		  }
	}
	public function cloud_base_pilots_signoffs_edit_callback( \WP_REST_Request $request) {
	
		
		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_pilots_signoffs_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}	
	
}
	

