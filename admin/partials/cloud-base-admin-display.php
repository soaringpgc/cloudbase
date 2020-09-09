<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php

		// Generate a custom nonce value.
//		$user_signoff_nonce = wp_create_nonce( 'cloud_base' ); 
		// preset and set the tab we are displaying
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'config_page';

		// Build the Form
		$page_tabs = array (  "aircraft"=>"Aircraft", "manage_aircraft_types"=>"Aircraft Types", 
		"manage_flight_types "=>"Flight Types",  "manage_tow_fees" =>  "Tow Fees", "config_page" =>  "Basic Configuration Page" );		
		
//		$page_tabs = apply_filters('pgc_signoffs/tab_header' , $page_tabs);		
				
		echo "<h1>" . esc_html( get_admin_page_title() ) . "</h1><h2 class-'nav-tab-wrapper'> ";
		foreach ($page_tabs as $key => $value){
			echo '<a href="?page=cloud_base&tab=' .  $key . '" class="nav-tab' . ($active_tab == $key ? `nav-tab-active` : ``) . '">' .$value . '</a>' ;
		} 
?>
</h2>
<br>     
<div class="wrap" text-align:left align:left >
<?php	
	$plugin_path = plugin_dir_path(__FILE__) ;
	
	$include_tab = $active_tab . '_tab.php';
	$file_to_check = $plugin_path . $include_tab;

	if (file_exists( $file_to_check )){
		include_once $include_tab;
	}  else {
		do_action( 'pgc_signoffs/tab_page' , $active_tab);
	}
  
   wp_nonce_field('cloud_base');	
	
?> 
</div>
