<br>
 <div style="display:inline-block"  align:left>
<h3 align:left>Setup Basic Club Information</h3>
	<form action="admin-post.php" method="post" id="config_page" align ="left">
		<input type="hidden" name=action value="config_page">
    		<?php  
    		
    		$months = array( "0"=>"January" ,"1"=>"February", "2"=>"March", "3"=>"April", "4"=>"May", "5"=>"June", "6"=>"July", "7"=>"August", "8"=>"September", "9"=>"October", "10"=>"November", "11"=>"December"); 
			$days =array( "1"=>"1st", "2"=>"2ed", "3"=>"3ed", "4"=>"4th", "5"=>"5th", "6"=>"6th", "7"=>"7th", "8"=>"8th", "9"=>"9th", "10"=>"10th", "11"=>"11th", "12"=>"12th", "13"=>"13th", "14"=>"14th", "15"=>"15th",
			 "16"=>"16th", "17"=>"17th", "18"=>"18th", "19"=>"19th", "20"=>"20th", "21"=>"21st", "22"=>"22ed", "23"=>"23ed", "24"=>"24th", "25"=>"25th", "26"=>"26th", "27"=>"27th", "28"=>"28th", "29"=>"29th", "30"=>"30th", "31"=>"31st",        	 );
 			$fyStartMonth = get_option ('cloud_base_fy_month');
 			$fyStartDay = get_option ('cloud_base_fy_day');
 			$sessionStartMonth = get_option ('cloud_base_session_month');
 			$sessionStartDay = get_option ('cloud_base_session_day');
			
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
// Fiscial Year date
          		echo '<br><label for fyStartMonth>Fiscal Year Start:</label>
          			<select name="fyStartMonth" id="fyStartMonth"  >' ;
                foreach($months as $key=> $value){
                	if ( $key == $fyStartMonth ){
                		echo '<option value="' . $key . '" selected="selected">' . $value . '</option>';
                	} else {
                		echo '<option value="' . $key . '">' . $value . '</option>';
                	}
                };
          		echo '</select><label for fyStartDay>Day:</label><select name="fyStartDay" id-"fyStartDay">';
                foreach($days as $key=> $value){
                	if ( $key == $fyStartDay ){
                		echo '<option value="' . $key . '" selected="selected">' . $value . '</option>';
                	} else {
                		echo '<option value="' . $key . '">' . $value . '</option>';
                	}
                };
// Session start date
         		echo '</select><br><label for seasionStartMonth>Seasion Yr Start:</label>
          			<select name="sessionStartMonth" id="sessionStartMonth" >' ;
                foreach($months as $key=> $value){
                	if ( $key == $sessionStartMonth ){
                		echo '<option value="' . $key . '" selected="selected">' . $value . '</option>';
                	} else {
                		echo '<option value="' . $key . '">' . $value . '</option>';
                	}
                };
          		echo	'
          			</select>
          			<label for sessionStartDay>Day:</label>
          			<select name="sessionStartDay" id-"sessionStartDay">';
               foreach($days as $key=> $value){
                	if ( $key == $sessionStartDay ){
                		echo '<option value="' . $key . '" selected="selected">' . $value . '</option>';
                	} else {
                		echo '<option value="' . $key . '">' . $value . '</option>';
                	}
                };				
          	    echo'</select>';	
     		}
     		wp_nonce_field('config_page' );  
  	   		submit_button();	
  	   	echo '</form>' ;
  	   	?> 
</div>
