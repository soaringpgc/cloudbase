
  <h3><?php _e("Extra profile information", "blank"); ?></h3>
  <table class="form-table">
      <tr>
      <th><label for="address1"><?php _e("St. Address"); ?></label></th>
      <th><label for="address2"><?php _e("Address"); ?></label></th>
      <th><label for="city"><?php _e("City"); ?></label></th>
      </tr>
     <tr> 
      <tr> 
      	<td>
        	<input type="text" name="address1" id="address1" class="regular-text" 
            	value="<?php echo esc_attr( get_the_author_meta( 'address1', $user->ID ) ); ?>" /><br />
    	</td>
      	<td>
        	<input type="text" name="address2" id="address2" class="regular-text" 
          	  value="<?php echo esc_attr( get_the_author_meta( 'address2', $user->ID ) ); ?>" /><br />       
    	</td>
    	<td>
        	<input type="text" name="city" id="city" class="regular-text" 
          	  value="<?php echo esc_attr( get_the_author_meta( 'city', $user->ID ) ); ?>" /><br />    
    	</td>
    </tr>
      <tr>
      <th><label for="state"><?php _e("State"); ?></label></th>
      <th><label for="zip"><?php _e("Zip"); ?></label></th>
      
      </tr>
     <tr> 
      <tr> 
      	<td>
        	<input type="text" name="state" id="state" class="regular-text" 
            	value="<?php echo esc_attr( get_the_author_meta( 'state', $user->ID ) ); ?>" /><br />
    	</td>
      	<td>
        	<input type="text" name="zip" id="zip" class="regular-text" 
          	  value="<?php echo esc_attr( get_the_author_meta( 'zip', $user->ID ) ); ?>" /><br />       
    	</td>
    </tr>             
    <tr>
      <th><label for="cel"><?php _e("Cell(text) Phone"); ?></label></th>
      <th><label for="tel"><?php _e("Home Phone"); ?></label></th>
      <th><label for="wrk"><?php _e("Work Phone"); ?></label></th>
      </tr>
     <tr> 
      	<td>
        	<input type="text" name="cel" id="cel" class="regular-text" 
            	value="<?php echo esc_attr( get_the_author_meta( 'cel', $user->ID ) ); ?>" /><br />
    	</td>
      	<td>
        	<input type="text" name="tel" id="tel" class="regular-text" 
          	  value="<?php echo esc_attr( get_the_author_meta( 'tel', $user->ID ) ); ?>" /><br />       
    	</td>
    	<td>
        	<input type="text" name="wrk" id="wrk" class="regular-text" 
          	  value="<?php echo esc_attr( get_the_author_meta( 'wrk', $user->ID ) ); ?>" /><br />    
    	</td>
    </tr>
  </table>
