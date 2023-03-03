<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://quovadimus.net
 * @since      1.0.0
 *
 * @author    Dave Johnson/Philadelphia Glider Council 
 */	
	
	$request = new WP_REST_Request('GET', '/cloud_base/v1/no_fly');
 	$request->set_query_params(['summary'=>'1']);
	$response = rest_do_request ($request );
	$server = rest_get_server();
	$data = $server->response_to_data( $response, false );   	 			

	echo "<h4>" . get_option ('glider_club_short_name'). " NO Fly List as of : ". date("Y/m/d"). "</h4>";
	echo '<div class="flex-container" text-align:"center"> <div class="Table" >         
		<div class="Heading">
      	<div  class="Cell3" >Member</div>
    	</div> ';
		foreach ($data as $d){  
				echo '<div class="Row">';			
				echo '<div class="Cell3"  >' . $d->name.   '</div></div>';
		}	
  	echo '</div>';
?>



