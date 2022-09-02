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

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<?php		
	
	$request = new WP_REST_Request('GET', '/cloud_base/v1/sign_off');
	$request->set_query_params(['no_fly'=>'no_fly']);
	$response = rest_do_request ($request );
	$server = rest_get_server();
	$data = $server->response_to_data( $response, false );
//exit(var_dump($data));
// 	 	ob_start();
   	 			

			echo "<h3>" . get_option ('glider_club_short_name'). " NO Fly List as of : ". date("Y/m/d"). "</h3>";
			echo '<div text-align:"center"> <div class="Table" >         
				<div class="Heading">
        	  	<div  class="Cell" >Member</div>
       		   	<div  class="Cell" >Sign-off</div>
          		<div  class="Cell">Expire Date</div>
       			</div> ';

				foreach ($data as $no_fly){  

					$user_data = get_userdata($no_fly->member_id);
//					var_dump($user_data  );
						echo '<div class="Row">';			
						echo '<div class="Cell"  >' . $user_data->last_name. ", " . $user_data->first_name .  '</div>';
										
				}	
  			echo '</div>';
  			echo '<br>';
 //  			$output = ob_get_contents();
//   			ob_end_clean();
//  			return $output ;

 
?>



