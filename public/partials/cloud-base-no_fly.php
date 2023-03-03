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
// 	$request->set_query_params(['no_fly'=>'no_fly']);
	$response = rest_do_request ($request );
	$server = rest_get_server();
	$data = $server->response_to_data( $response, false );   	 			

	echo "<h3>" . get_option ('glider_club_short_name'). " NO Fly List as of : ". date("Y/m/d"). "</h3>";
	echo '<div text-align:"center"> <div class="Table" >         
		<div class="Heading">
      	<div  class="Cell2" >Member</div>
       	<div  class="Cell2" >Sign-off</div>
    	<div  class="Cell1">Expire Date</div>
    	</div> ';
		foreach ($data as $d){  
				echo '<div class="Row">';			
				echo '<div class="Cell2"  >' . $d->name.   '</div>
					<div class="Cell2"  >' . $d->signoff_type . '</div>
					<div class="Cell1"  >' . substr($d->date_expire, 0,10). '</div></div>';								
		}	
  	echo '</div>';
?>



