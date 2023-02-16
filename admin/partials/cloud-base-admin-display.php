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
		$plugin_path = plugin_dir_path(__FILE__) ;

		// Build the Form
	
 		$page_tabs_enhanced = array(
 		array( "tab"=>"aircraft"			 , "title"=>"Equipment", 		"page"=>"cloud_base", 'plug_path'=>$plugin_path ),
 		array( "tab"=>"manage_aircraft_types", "title"=>"Equipment Types",  "page"=>"cloud_base", 'plug_path'=>$plugin_path ), 
 		array( "tab"=>"manage_status_types"  , "title"=>"Status Types", 	"page"=>"cloud_base", 'plug_path'=>$plugin_path ),
 		array( "tab"=>"manage_flight_types" , "title"=>"Flight Types",  	"page"=>"cloud_base", 'plug_path'=>$plugin_path ),
 		array( "tab"=>"manage_tow_fees" 	 , "title"=> "Tow Fees",        "page"=>"cloud_base", 'plug_path'=>$plugin_path ),
 		array( "tab"=>"manage_sign_offs"     , "title"=>"Sign Off Types", 	"page"=>"cloud_base", 'plug_path'=>$plugin_path ),
 		array( "tab"=>"config_page" 		 , "title"=> "Basic Configuration", "page"=>"cloud_base", 'plug_path'=>$plugin_path ) 
		);	
// hook to allow other plugins to add additional admin tabs in the CloudBase admin area. 
//		
		$page_tabs_enhanced = apply_filters( 'cb_admin_add_config', $page_tabs_enhanced );		
		echo "<h1>" . esc_html( get_admin_page_title() ) . "</h1><h2 class-'nav-tab-wrapper'> ";
		
		foreach ($page_tabs_enhanced as $key){
			echo '<a href="?page='. $key['page']. '&tab=' .  $key['tab'] . '" class="nav-tab' . ($active_tab == $key['tab'] ? 
			`nav-tab-active` : ` `) . '">' .$key['title'] . '</a>' ;
		} 	
				
?>
</h2>
<br>     
<div class="wrap" text-align:left align:left >
<?php	
//	$plugin_path = plugin_dir_path(__FILE__) ;
	
	$key = array_search( $active_tab, array_column($page_tabs_enhanced, 'tab'));	
//	$include_tab = $active_tab . '_tab.php';
	$include_tab = $page_tabs_enhanced[$key]['tab'] . '_tab.php';	
	$file_to_check =  $page_tabs_enhanced[$key]['plug_path'] . $include_tab;

	if (file_exists( $file_to_check )){
		include_once $file_to_check;
	}  else {
	echo $file_to_check;
		include_once( 'config_page_tab.php');
	}
  
   wp_nonce_field('wp_rest');	
	
?> 
</div>
