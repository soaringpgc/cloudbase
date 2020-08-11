<br>
 <div style="display:inline-block"  align:left>
<h3 align:left>Setup Basic Club Information</h3>
	<form action="admin-post.php" method="post" id="config_page" align ="left">
		<input type="hidden" name=action value="config_page">
    		<?php  
    		if( current_user_can( 'manage_options' ) ) {	
    		
    		    echo '<label>Club long Name</label>
         		 <input type = "text"
                 id = "long_name"
                 name = "long_name"
                 size = "60"
                 value = "'. get_option ('glider_club_long_name'). '"> <br>'   ;

    		    echo '<label>Club short Name</label>
         		 <input type = "text"
                 name = "short_name"
                 size = "10"  
                 value = "' . get_option ('glider_club_short_name'). '">' ;    
                 echo '<br><label>Altitude Units: </label>';
                if (get_option("glider_club_tow_units") == "m"){
          			echo '<input type="radio" name="units" value="m" checked="checked">Meters
          			<input type="radio" name="units" value="ft" >Feet'  ;
          		} else {
          			echo '<input type="radio" name="units" value="m" >Meters
          			<input type="radio" name="units" value="ft" checked="checked">Feet'  ;
          		}
     		}
     		wp_nonce_field('config_page' );  
  	   		submit_button();	
  	   	echo '</form>' ;
  	   	?> 
</div>
