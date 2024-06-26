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
	
		$min_php = '7.4.0';
		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
		}
		create_cb_database();
		create_cb_roles();
		set_default_cb_configuration();
		update_authoritys();	
	}
}
function create_cb_database(){
   	global $wpdb;
   	$charset_collate = $wpdb->get_charset_collate();
   	$db_version = 0.90;
   	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   
   	if (get_option("cloud_base_db_version") != $db_version){ 
      $table_name = $wpdb->prefix . "cloud_base_aircraft";
      // create aircraft table
      $sql = "CREATE TABLE ". $table_name . " (
      	id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      	aircraft_id smallint(6),
      	make tinytext,
      	model tinytext,
      	registration tinytext,
      	compitition_id tinytext,
      	aircraft_type int(4),
      	status tinyint(4),
      	captian_id int(10),
      	annual_due_date date DEFAULT NULL,
      	registration_due_date date DEFAULT NULL,
      	transponder_due date DEFAULT NULL,
      	comment varchar(255),
      	date_updated datetime DEFAULT NULL,
      	valid_until datetime DEFAULT NULL,
      	PRIMARY KEY  (id)
      );" . $charset_collate  . ";";
      dbDelta($sql);	
			
    $table_name = $wpdb->prefix . "cloud_base_squawk";
    // create squawk table
    $sql = "CREATE TABLE ". $table_name . " (
    	id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    	squawk_id int(10) NOT NULL,
    	equipment int(10) UNSIGNED NOT NULL,
    	date_entered datetime DEFAULT NULL,
    	date_updated datetime DEFAULT NULL,
    	text text  NULL,
    	user_id int(10) UNSIGNED NOT NULL,
    	user_last_update_id int(10) UNSIGNED,
    	status varchar(10),
    	PRIMARY KEY  (id)
    	);" . $charset_collate  . ";";
    dbDelta($sql);					

	$table_name = $wpdb->prefix . "cloud_base_aircraft_type";
	// create aircraft type table
	$sql = "CREATE TABLE ". $table_name . " (
  		id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  		type_id smallint(6),
  		sort_code smallint(6),
  		title text NOT NULL,
  		base_charge decimal(5,2),
  		first_hour decimal(5,2),
  		each_hour decimal(5,2),
  		min_charge decimal(5,2),
  		active bit(1) DEFAULT 1,
  		valid_until datetime DEFAULT NULL,
			PRIMARY KEY  (id)
		);" . $charset_collate  . ";";
	 dbDelta($sql);
	 // prepopulate with tow plane type
	$sql =  $wpdb->prepare("INSERT INTO {$table_name} (type_id, title, sort_code, base_charge, first_hour, each_hour, min_charge, valid_until ) 
	VALUES (%d, %s, %d, %f, %f, %f, %f, null)  ON DUPLICATE KEY UPDATE id=id", '1', 'Tow', 1, '0','0', '0', null);	
	$wpdb->query($sql);		
	// prepopulate with Glider Type	
	$sql =  $wpdb->prepare("INSERT INTO {$table_name} (type_id, title, sort_code, base_charge, first_hour, each_hour, min_charge, valid_until ) 
	VALUES (%d, %s, %d, %f, %f, %f, %f, null)  ON DUPLICATE KEY UPDATE id=id" , '2', 'Glider', 0, '0','0', '0', null);	
	$wpdb->query($sql);			
			
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
	  valid_until datetime DEFAULT NULL,        			
	  PRIMARY KEY  (id)
	  );" . $charset_collate  . ";";
	dbDelta($sql);

	$table_name = $wpdb->prefix . "cloud_base_flight_sheet";
	// create flight sheet table
	$sql = "CREATE TABLE ". $table_name ." (
 	  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 	  flightyear smallint(6),
 	  date date DEFAULT NULL,
	  flight_number INT(10) UNSIGNED NOT NULL,
	  flight_type int(10) UNSIGNED,
	  aircraft_id int(10) UNSIGNED,
	  pilot_id int(10) UNSIGNED NOT NULL,
	  flight_fee_id int(10) UNSIGNED,
	  total_charge decimal(5,2),
	  charge_detail VARCHAR(500),
	  start_time datetime DEFAULT NULL ,
	  end_time datetime DEFAULT NULL ,
	  valid_until datetime DEFAULT NULL,
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
	  description varchar(40),
	  active bit(1) DEFAULT 1,
      PRIMARY KEY (ID)
	  );" . $charset_collate  . ";";
	dbDelta($sql);
	
	$table_name = $wpdb->prefix . "cloud_base_aircraft_event_types";
	// create audit table for flight sheet
	$sql = "CREATE TABLE ". $table_name . " (
	  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	  title char(10),
	  description varchar(40),
	  frequency decimal(5,2),
	  frequency_units varchar(20),
	  aircraft_type int(10) UNSIGNED,
	  active bit(1) DEFAULT 1,
      PRIMARY KEY (ID)
	  );" . $charset_collate  . ";";
	dbDelta($sql);	

	$table_name = $wpdb->prefix . "cloud_base_aircraft_events";
	// create audit table for flight sheet
	$sql = "CREATE TABLE ". $table_name . " (
	  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	  event_id int(10) UNSIGNED NOT NULL,
	  event_date datetime DEFAULT NULL,
	  hobbs decimal(5,2),
	  tach decimal(5,2),
	  pilot_id int(10) UNSIGNED NOT NULL,
	  instructor_id int(10) UNSIGNED,
	  notes varchar(250),	  	  
	  description varchar(40),
	  aircraft_type int(10) UNSIGNED,
	  date_updated datetime DEFAULT NULL,
	  valid_until datetime DEFAULT NULL,
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
	  date_updated datetime DEFAULT NULL,
	  user_id int(10) UNSIGNED NOT NULL,
      PRIMARY KEY (id)
	  );" . $charset_collate  . ";";
	dbDelta($sql);
// Sign offs	
   	$table_name = $wpdb->prefix . "cloud_base_signoffs_types";	
  	// create signoff types table
	$sql = "CREATE TABLE ". $table_name . " (
	  	id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   	    signoff_type varchar(40) NOT NULL,
		period tinytext NOT NULL,
		fixed_date tinytext,
		user_id int(10) UNSIGNED NOT NULL,
		authority varchar(30),
		no_fly BOOLEAN,
		applytoall BOOLEAN,
		active boolean DEFAULT 1,
		PRIMARY KEY  (id)
		);" . $charset_collate  . ";";	
	dbDelta($sql); 

	$table_name = $wpdb->prefix . "cloud_base_member_signoffs";
	// create member specific signoffs 
	$sql = "CREATE TABLE ". $table_name . " (
	  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
   	  member_id int(10) NOT NULL,
   	  signoff_id int(10) UNSIGNED NOT NULL,
      authority_id int(10) NOT NULL,
	  date_entered datetime DEFAULT NULL,
	  date_effective datetime DEFAULT NULL,
	  date_expire datetime DEFAULT NULL,
	  PRIMARY KEY  (id)
	  );" . $charset_collate  . ";";
	dbDelta($sql); 

	//  Set the version of the Database
	update_option("cloud_base_db_version", $db_version);
	}
}
function create_cb_roles(){
// add CFIG role
	if(!role_exists('cfi_g')){
		add_role('cfi_g' , 'CFI-G', array('cb_edit_instruction'));
	} else {
		//add capability to existing cfi-g
		$role_object = get_role('cfi_g' );
		if ( !$role_object->has_cap('cb_edit_instruction')){
			$role_object->add_cap('cb_edit_instruction', true );
		}
	}
// add Chief CFIG 
	if(!role_exists('chief_flight')){
		add_role('chief_flight' , 'Chief CFI', array('cb_edit_cfig', 'cb_edit_instruction', 'chief_flight'));
	} else {
		//add capability to existing chief_flight
		$role_object = get_role('chief_flight' );
		if ( !$role_object->has_cap('cb_edit_cfig')){
			$role_object->add_cap('cb_edit_cfig', true );
		}	
		if ( !$role_object->has_cap('cb_edit_instruction')){
			$role_object->add_cap('cb_edit_instruction', true );
		}	
		if ( !$role_object->has_cap('chief_flight')){
			$role_object->add_cap('chief_flight', true );
		}	
	}	
// add CFIG scheduler
	if(!role_exists('cfig_scheduler')){
		add_role('cfig_scheduler' , 'CFI-G scheduler', array('cfig_scheduler'));
	} else {
		//add capability to existing cfi-g
		$role_object = get_role('cfig_scheduler' );
		if ( !$role_object->has_cap('cfig_scheduler')){
			$role_object->add_cap('cfig_scheduler', true );
		}
	}
// add CFI role (power)
	if(!role_exists('cfi')){
		add_role('cfi' , 'CFI', array('cb_power_instruction'));
	} else {
		//add capability to existing cfi
		$role_object = get_role('cfi' );
		if ( !$role_object->has_cap('cb_power_instruction')){
			$role_object->add_cap('cb_power_instruction', true );
		}
	}	
// add Tow Pilot scheduler
	if(!role_exists('tow_scheduler')){
		add_role('tow_scheduler' , 'Tow scheduler', array('tow_scheduler'));
	} else {
		//add capability to existing cfi-g
		$role_object = get_role('tow_scheduler' );
		if ( !$role_object->has_cap('tow_scheduler')){
			$role_object->add_cap('tow_scheduler', true );
		}
	}	
// add instruction scheduling assistant manager
	if(!role_exists('schedule_assist')){
		add_role('schedule_assist' , 'Instruction Scheduling', array('schedule_assist'));
	} else {
		//add capability to existing cfi-g
		$role_object = get_role('schedule_assist' );
		if ( !$role_object->has_cap('schedule_assist')){
			$role_object->add_cap('schedule_assist', true );
		}
	}			
// add Treasurer role
	if(!role_exists('treasurer')){
		add_role('treasurer' , 'Treasurer', array('cb_edit_dues', 'cb_edit_flight', 'read', 'list_users', 'list_roles', 'edit_users', 'create_users'));
	} else {
		//add capability to existing Treasurer
		$role_object = get_role('treasurer' );
		if ( !$role_object->has_cap('cb_edit_dues')){
			$role_object->add_cap('cb_edit_dues', true);
		}
		if ( !$role_object->has_cap('cb_edit_flight')){
			$role_object->add_cap('cb_edit_flights', true);
		}
		if ( !$role_object->has_cap('list_users')){
			$role_object->add_cap('list_users', true);
		}
		if ( !$role_object->has_cap('list_roles')){
			$role_object->add_cap('list_roles', true);
		}
		if ( !$role_object->has_cap('edit_users')){
			$role_object->add_cap('edit_users', true);
		}				
		if ( !$role_object->has_cap('create_users')){
			$role_object->add_cap('create_users', true);
		}				
		if ( !$role_object->has_cap('read')){
			$role_object->add_cap('read', true);
		}								
	}
// add Tow Pilot role
	if(!role_exists('tow_pilot')){
		add_role('tow_pilot' , 'Tow Pilot');
	}
// add inactive member
	if(!role_exists('inactive')){
		add_role('inactive' , 'Inactive', array('cb_edit_operations'=>false, 'cb_edit_towpilot'=>false, 
		'cb_edit_instruction'=>false, 'edit_users'=>false, 'cb_edit_dues'=>false, 'cb_edit_flight'=>false,
		 'read'=>true, 'list_users'=>false, 'list_roles'=>false, 'edit_users'=>false, 'create_users'=>false));
	}	
// add board role
	if(!role_exists('board_member')){
		add_role('board_member' , 'Board Member');
	}	
// add Chief Tow Pilot role
	if(!role_exists('chief_tow')){
		add_role('chief_tow' , 'Chief Tow Pilot', array( 'cb_edit_towpilot'), 'chief_tow');
	} else {
		//add capability to existing tow pilot
		$role_object = get_role('chief_tow' );
		if ( !$role_object->has_cap('cb_edit_towpilot')){
			$role_object->add_cap('cb_edit_towpilot', true);
		}
		if ( !$role_object->has_cap('chief_tow')){
			$role_object->add_cap('chief_tow', true);
		}		
	}				
// Add Operations role
	if(!role_exists('operations')){
		add_role('operations' , 'Operations', array('cb_edit_operations', 'edit_users'));
	} else {
		//add capability to existing operations
		$role_object = get_role('operations' );
		if ( !$role_object->has_cap('cb_edit_operations')){
			$role_object->add_cap('cb_edit_operations', true);
		}
		if ( !$role_object->has_cap('edit_users')){
			$role_object->add_cap('edit_users', true);
		}
	}
// Add Field Manager role
	if(!role_exists('field_manager')){
		add_role('field_manager' , 'Field Manager', array('read'));
	} 
// Add Assistant Field Manager role
	if(!role_exists('assistant_field_manager')){
		add_role('assistant_field_manager' , 'Assistant Field Manager', array('read'));
	} 	
// Add Chief Operations role	
	if(!role_exists('chief_of_ops')){
		add_role('chief_of_ops' , 'Chief Of Ops', array('cb_edit_operations', 'cb_edit_towpilot', 'cb_edit_instruction', 'edit_users'));
	} else {
		//add capability to existing operations
		$role_object = get_role('chief_of_ops' );
		if ( !$role_object->has_cap('cb_edit_operations')){
			$role_object->add_cap('cb_edit_operations', true);
		}
		if ( !$role_object->has_cap('cb_edit_towpilot')){
			$role_object->add_cap('cb_edit_towpilot', true);
		}
		if ( !$role_object->has_cap('cb_edit_instruction')){
			$role_object->add_cap('cb_edit_instruction', true);
		}
		if ( !$role_object->has_cap('edit_users')){
			$role_object->add_cap('edit_users', true);
		}							
	}	
// Add flight editor role	
	if(!role_exists('flight_edit')){
		add_role('flight_edit' , 'Flight Editor', array('cb_edit_flight'));
	} else {
		//add capability to existing operations
		$role_object = get_role('flight_edit' );
		if ( !$role_object->has_cap('cb_edit_flight')){
			$role_object->add_cap('cb_edit_flight', true);
		}	
	}		
// Add secheduling assistance editor role	
	if(!role_exists('schedule_assist')){
		add_role('schedule_assist' , 'Instruction Schedule Assistant', array('schedule_assist'));
	} else {
		//add capability to existing operations
		$role_object = get_role('schedule_assist' );
		if ( !$role_object->has_cap('schedule_assist')){
			$role_object->add_cap('schedule_assist', true);
		}	
	}		
// Add operations desk - role limited to only entering flights. 
	if(!role_exists('ops_desk')){
		add_role('ops_desk' , 'Operations Desk', array('ops_desk'));
	} else {
		//add capability to existing operations
		$role_object = get_role('ops_desk' );
		if ( !$role_object->has_cap('ops_desk')){
			$role_object->add_cap('ops_desk', true);
			$role_object->add_cap('cb_ops_desk', true);
			$role_object->add_cap('read', true);
		}	
	}		

// Add maintenance editor role	
	if(!role_exists('maintenance_editor')){
		add_role('maintenance_editor' , 'Maintenance Editor', array('cb_edit_maintenance'));
	} else {
		//add capability to existing operations
		$role_object = get_role('maintenance_editor' );
		if ( !$role_object->has_cap('cb_edit_maintenance')){
			$role_object->add_cap('cb_edit_maintenance', true);
		}	
	}		
			
	// update admin to have all roles - this may change later.
	$role_object = get_role('administrator' );
	$cb_roles = array('cb_edit_instruction', 'cb_edit_dues', 'cb_edit_flight', 'cb_edit_operations', 'cb_edit_towpilot', 'flight_edit', 'cb_edit_maintenance' );
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
function update_authoritys(){	
	update_option('cloud_base_authoritys', array("read"=>"Self", "edit_gc_dues"=>"Treasurer", 
	"edit_gc_operations"=>"Operations", "edit_gc_instruction"=>"CFI-G", "field_manager"=>"Field Manager", "assistant_field_manager"=>"Assistant FM", 
	"chief_flight"=>"Chief Flight Instructor", "chief_tow"=>"Chief Tow Pilot", "cfig_scheduler"=>"CFI-G scheduler",  "tow_scheduler"=>"Tow scheduler",
	"edit_gc_tow"=>"Tow Pilot", "manage_options"=>"god"));										    
}

function role_exists( $role ) {
		if( ! empty( $role ) ) {
		return $GLOBALS['wp_roles']->is_role( $role );
		}
		return false;
}

