<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cloud_Base
 * @subpackage Cloud_Base/includes
 * @author     Your Name <email@example.com>
 */
class Cloud_Base_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	
		$min_php = '5.6.0';
		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
		}
			create_cb_database();
			create_cb_roles();
			set_default_cb_configuration();
	}
}
function create_cb_database(){
		global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			$db_version = 0.4;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			if (get_option("cloud_base_db_version") != $db_version){

				$table_name = $wpdb->prefix . "cloud_base_aircraft";
				// create aircraft table
				$sql = "CREATE TABLE ". $table_name . " (
					id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					aircraft_id smallint(6),
					make text NOT NULL,
					model text NOT NULL,
					registration tinyint NOT NULL,
					compitition_id tinytext,
					aircraft_type int(4),
					status varchar(20),
					captian_id int(10),
					valid_until datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					PRIMARY KEY  (id)
				);" . $charset_collate  . ";";
				dbDelta($sql);

				$table_name = $wpdb->prefix . "cloud_base_periodic";
				$sql = "CREATE TABLE ". $table_name . " (
					id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					aircraft_id int(10) NOT NULL,
					title tinytext NOT NULL,
					interval_unit varchar(20),
					interval_number decimal(7,2),
					last_date datetime,
					last_not_date decimal(7,2),
					updater_id int(10),
					valid_until datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					PRIMARY KEY  (id)
				);" . $charset_collate  . ";";
				dbDelta($sql);
				
				$table_name = $wpdb->prefix . "cloud_base_equipment";
				// create aircraft table
				$sql = "CREATE TABLE ". $table_name . " (
        			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        			catagory int(10) UNSIGNED NOT NULL,
        			short_name varchar(10),
            		long_name varchar(30),
            		annual_due_date datetime DEFAULT '0000-00-00 00:00:00',
					registration_due_date datetime DEFAULT '0000-00-00 00:00:00',
					date_entered datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					make text NOT NULL,
					model text NOT NULL,
					status varchar(8),
					op_hours decimal(7,2),
					captian_id int(10),
					valid_until datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					PRIMARY KEY  (id)
				);" . $charset_collate  . ";";
				dbDelta($sql);		
				
				$table_name = $wpdb->prefix . "cloud_base_squawk";
				// create squawk table
				$sql = "CREATE TABLE ". $table_name . " (
        			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        			squawk_id int(10) NOT NULL,
        			equipment int(10) UNSIGNED NOT NULL,
					date_entered datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					title tinytext NOT NULL,
					text text NOT NULL,
					user_id int(10) UNSIGNED NOT NULL,
					status varchar(8),
					valid_until datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					PRIMARY KEY  (id)
				);" . $charset_collate  . ";";
				dbDelta($sql);					

				$table_name = $wpdb->prefix . "cloud_base_aircraft_type";
				// create aircraft type table
				$sql = "CREATE TABLE ". $table_name . " (
        			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        			title text NOT NULL,
        			active bit(1) DEFAULT 1,
					PRIMARY KEY  (id)
				);" . $charset_collate  . ";";
				dbDelta($sql);		
				
				$table_name = $wpdb->prefix . "cloud_base_aircraft_status";
				// create aircraft type table
				$sql = "CREATE TABLE ". $table_name . " (
        			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        			title text NOT NULL,
        			active bit(1) DEFAULT 1,
					PRIMARY KEY  (id)
				);" . $charset_collate  . ";";
				dbDelta($sql);									
				
				$table_name = $wpdb->prefix . "cloud_base_tow_fees";
				// create tow fee table
				$sql = "CREATE TABLE " . $table_name . " (
    		    	id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        			altitude varchar(10) NOT NULL,
        			hook_up decimal(5,2),
        			charge decimal(5,2),
        			hourly decimal(5,2),
					valid_until datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,        			
					PRIMARY KEY  (id)
				);" . $charset_collate  . ";";
				dbDelta($sql);

				$table_name = $wpdb->prefix . "cloud_base_flight_sheet";
				// create flight sheet table
				$sql = "CREATE TABLE ". $table_name ." (
					id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					flight_number INT(10) UNSIGNED NOT NULL,
					flight_type int(10) UNSIGNED,
					equipment_id int(10) UNSIGNED,
					pilot_id int(10) UNSIGNED NOT NULL,
					flight_fee_id int(10) UNSIGNED,
					total_charge decimal(5,2),
					start_time datetime DEFAULT '0000-00-00 00:00:00' ,
					end_time datetime DEFAULT '0000-00-00 00:00:00' ,
					valid_until datetime DEFAULT '0000-00-00 00:00:00',
					instructor_id int(10) UNSIGNED,
					tow_plane_id int(10) UNSIGNED,
					tow_pilot_id int(10) UNSIGNED,
					date_entered datetime DEFAULT CURRENT_TIMESTAMP ,
					notes varchar(250),
					ip char (20),
					PRIMARY KEY  (id)
				);" . $charset_collate  . ";";
				dbDelta($sql);
				
				$table_name = $wpdb->prefix . "cloud_base_flight_type";
				// create audit table for flight sheet
				$sql = "CREATE TABLE ". $table_name . " (
					id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					title char(10),
 			        discription varchar(40) NOT NULL,
 			        active bit(1) DEFAULT 1,
          			PRIMARY KEY (ID)
				);" . $charset_collate  . ";";
				dbDelta($sql);

				$table_name = $wpdb->prefix . "cloud_base_sqw_work";
				// create audit table for flight sheet
				$sql = "CREATE TABLE ". $table_name . " (
					id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					squawk_id int(10) UNSIGNED NOT NULL,
		  			laborer varchar(40) NOT NULL,
 			        discription varchar(40) NOT NULL,
   			       	date_updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    		      	user_id int(10) UNSIGNED NOT NULL,
          			PRIMARY KEY (ID)
				);" . $charset_collate  . ";";
				dbDelta($sql);

				//  Set the version of the Database
				update_option("cloud_base_db_version", $db_version);
			}
}
function create_cb_roles(){
// add CFIG role
	if(!role_exists('cfi_g')){
		add_role('cfi_g' , 'CFI-G', array('edit_cb_instruction'));
	} else {
		//add capability to existing cfi-g
		$role_object = get_role('cfi-g' );
		if ( !$role_object->has_cap('edit_cb_instruction')){
			$role_object->add_cap('edit_cb_instruction', true );
		}
	}
// add Chief CFIG 
	if(!role_exists('chief_flight')){
		add_role('chief_flight' , 'Chief CFI', array('chief_flight'));
	} else {
		//add capability to existing chief_flight
		$role_object = get_role('chief_flight' );
		if ( !$role_object->has_cap('chief_flight')){
			$role_object->add_cap('chief_flight', true );
		}	
	}	
		
// add Treasurer role
	if(!role_exists('treasurer')){
		add_role('treasurer' , 'Treasurer', array('edit_gc_dues', 'read', 'list_users', 'orinite_users', 'remove_users', 'edit_users', 'create_users', 'delete_users'));
	} else {
		//add capability to existing Treasurer
		$role_object = get_role('treasurer' );
		if ( !$role_object->has_cap('edit_cb_dues')){
			$role_object->add_cap('edit_cb_dues', true);
		}
	}
// add Tow Pilot role
	if(!role_exists('tow_pilot')){
		add_role('tow_pilot' , 'Tow Pilot', array('edit_cb_tow'));
	} else {
		//add capability to existing tow pilot
		$role_object = get_role('tow_pilot' );
		if ( !$role_object->has_cap('edit_cb_tow')){
			$role_object->add_cap('edit_cb_tow', true);
		}
	}
// add Chief Tow Pilot role
	if(!role_exists('chief_tow')){
		add_role('chief_tow' , 'Chief Tow Pilot', array('chief_tow'));
	} else {
		//add capability to existing tow pilot
		$role_object = get_role('chief_tow' );
		if ( !$role_object->has_cap('chief_tow')){
			$role_object->add_cap('chief_tow', true);
		}	
	}		
		
// Add Operations role
	if(!role_exists('operations')){
		add_role('operations' , 'Operations', array('edit_cb_operations'));
	} else {
		//add capability to existing operations
		$role_object = get_role('operations' );
		if ( !$role_object->has_cap('edit_cb_operations')){
			$role_object->add_cap('edit_cb_operations', true);
		}
	}
// Add Chief Operations role	
	if(!role_exists('chief_of_ops')){
		add_role('chief_of_ops' , 'Chief Of Ops', array('edit_cb_operations', 'edit_cb_tow', 'edit_cb_instruction', 'edit_cb_dues'));
	} else {
		//add capability to existing operations
		$role_object = get_role('chief_of_ops' );
		if ( !$role_object->has_cap('edit_cb_operations')){
			$role_object->add_cap('edit_cb_operations', true);
		}	
	}	
// Add flight editor role	
	if(!role_exists('flight_edit')){
		add_role('flight_edit' , 'Flight Editor', array('edit_flights'));
	} else {
		//add capability to existing operations
		$role_object = get_role('flight_edit' );
		if ( !$role_object->has_cap('edit_flights')){
			$role_object->add_cap('edit_flights', true);
		}	
	}		
		
	// update admin to have all roles - this may change later.
	$role_object = get_role('administrator' );
	$cb_roles = array('edit_cb_instruction', 'edit_cb_dues', 'edit_cb_tow', 'edit_cb_operations', 'chief_tow', 'chief_flight', 'flight_edit' );
	foreach ($cb_roles as $cb_role ){
			if ( !$role_object->has_cap($cb_role)){
			$role_object->add_cap($cb_role , true);
		}
	}
}
function set_default_cb_configuration(){
	if ( get_option('glider_club_long_name') == false ){
		update_option('glider_club_long_name', 'Generic Flying Club Name'  );
		update_option('glider_club_short_name', 'GFCN'  );
	}
}
function role_exists( $role ) {
		if( ! empty( $role ) ) {
		return $GLOBALS['wp_roles']->is_role( $role );
		}
		return false;
}
