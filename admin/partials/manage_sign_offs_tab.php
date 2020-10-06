<br>
<script language="JavaScript">
	var cb_admin_tab = "sign_offs";
</script>

<div style="display:inline-block"  align:left id="sign_off_types"  class="sign_off_type editform" >
<?php 			
if( current_user_can( 'manage_options' ) ) {	
  echo('     <h3>Sign Offs</h3><DIV>
    <form id="addsign_off_type" action="#" >
    	<div>
      	<input type = "hidden"
            id = "id"
            size = "2"
            value = ""
            name = "id"/>
    	<label for="charge">Sign off: </label>
        <input type = "text"
            id = "signoff_type"
            size = "20"
            title = "signoff." 
            name = "signoff_type"/>
			<label for "authority">Authority</label>
			<select name ="authority" id="authority"  >');

// 				if (get_option ('glider_club_short_name') == 'PGC'){
// 					$value_label_authority = array("read"=>"Self", "edit_gc_dues"=>"Treasurer", "edit_gc_operations"=>"Operations", 
// 						"edit_gc_instruction"=>"CFI-G", "chief_flight"=>"Chief CFI-G", "chief_tow"=>"Chief Tow Pilot", "edit_gc_tow"=>"Tow Pilot", "manage_options"=>"god");		
// 				} else {
// 					$value_label_authority = array("read"=>"Self", "cb_edit_dues"=>"Treasurer", "cb_edit_operations"=>"Operations", 
// 						"cb_edit_instruction"=>"CFI-G", "cb_edit_cfig"=>"Chief CFI-G", "cb_chief_tow"=>"Chief Tow Pilot");				
// 				}

				// authority array is stored in WP options, It is created/updated on activation 
			$value_label_authority = get_option('cloud_base_authoritys');

		foreach ($value_label_authority  as $key => $authority ){
			echo ('<option value="' . $key . '">' . $authority . '</option>');
		}	
							
		$value_lable_period = array("Choose"=>"", "yearly"=>"Yearly", "biennial"=>"Biennial", "yearly-eom"=>"Yearly-EOM", "biennial-eom"=>"Biennial-EOM", "no_expire"=>"No expire", 
		"monthly" => "Monthly", "quarterly" => "Quarterly", "fixed"=>"Fixed Date" );		
		echo ('</select>
		  <label>No Fly</label>
		  <input type="checkbox" name="no_fly" id="no_fly" value=false class="checked_class"/>
		  </select> 
		<label>Apply to existing</label>
		<input type="checkbox" name="applytoall" id="applytoall" value="false" class="checked_class">
		<br><label>Effective Period</label>
		<select name ="period"  id="period" title="EOM - End Of Month, select Fixed Date for specific date"> ');
		foreach ($value_lable_period  as $key => $period ){
			echo ('<option value="' . $key . '">' . $period . '</option>');
		}	
		echo ('</select >
	  <label>Expire Date</label>
    	 <input type = "text"
          id = "expire"
          class = "calendar"
          name ="expire"
          size = "10"
          title = "Enter the date the sign off expires - fixed date only"
         />        
         <button id="add" class="view">Add</button>
 		<button id="update" class="edit">Update</button>
			</div>
		</form></DIV>				

		');
}				//not implemented yet....
?>   		
<!-- 
         	</div>
 -->

<div  class="Table">
    <div class="Title">
        <p>Sign Off Types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>ID</p>
        </div>  
        <div class="Cell2"  >
            <p>Title</p>
        </div>
         <div class="Cell2"  >
            <p>Authority</p>
        </div>      
        <div class="Cell"  >
            <p>Period</p>
        </div>      
        <div class="Cell"  >
            <p>Date</p>
        </div>      
        <div class="Cell0"  >
            <p>No Fly</p>
        </div>      
        <div class="Cell0"  >
            <p>All</p>
        </div>      
    </div>
</div>

</div>

    
    <h4>Instructions</h4>
<p>    Sort code determines if an aircraft will be listed in the glider(sort code 1) list
    or tow plane (sort code 2) lists. Future type of aircraft will use new (3+) sort codes. 
</p><p>    
    You can not delete a aircraft type if an aircraft is assigned to that type. 
</p><p>  
    To edit an existing item double click anywhere in that line. The data will be copied 
    to the form at the top of the page and the button will change to "Update" click on
    Update to save the new values.  
</p><p>      
    Fields are provided for charging by aircraft type. Base charge, first hour, each houe
    and minimum charge fields are provided. How they are used is up to you. 
</p>    

