<br>
<script language="JavaScript">
	var cb_admin_tab = "sign_off_types";
</script>

<div style="display:inline-block"  align:left id="sign_off_types"  class="sign_off_type editform" >
<?php 			
if( current_user_can( 'manage_options' ) ) {	
  echo('<h3>Sign Offs</h3>
    <form id="addsign_off_type" action="#" >
      	<input type = "hidden"
            id = "id"
            size = "2"
            value = ""
            name = "id"/>
        <div class="hform">    
    	<label for="signoff_type" >Sign off:</label>
        <input type = "text"
            id = "signoff_type"
            size = "10"
            title = "signoff." 
            name = "signoff_type"/>
        </div>
        <div class="hform">
		<label for "authority">Authority</label>
			<select name ="authority" id="authority"  >');
			// authority array is stored in WP options, It is created/updated on activation 
			$value_label_authority = get_option('cloud_base_authoritys');

			foreach ($value_label_authority  as $key => $authority ){
				echo ('<option value="' . $key . '">' . $authority . '</option>');
			}	

		echo ('</select></div>
		<div style="float:left;margin-right:20px;">
		<label for="no_fly">No Fly</label>
		  <input type="checkbox" name="no_fly" id="no_fly" value=false class="checked_class"/>
		</div>
		
		<div class="hform">
		<label for="period">Effective Period</label>
		<select name ="period"  id="period" title="EOM - End Of Month, select Fixed Date for specific date"> ');
		$value_lable_period = array("Choose"=>"", "yearly"=>"Yearly", "biennial"=>"Biennial", "yearly-eom"=>"Yearly-EOM", "biennial-eom"=>"Biennial-EOM", "dues"=>"Dues", "no_expire"=>"No expire", 
		"monthly" => "Monthly", "quarterly" => "Quarterly", "fixed"=>"Fixed Date" );				
		foreach ($value_lable_period  as $key => $period ){
			echo ('<option value="' . $key . '">' . $period . '</option>');
		}	
		echo ('</select >
		</div>	
		<div id="expire_date" class="hform"">
	   	   <label for="expire">Expire Date</label>
    	   <input type = "date"
            id = "expire"
            name ="expire"
            size = "10"
            title = "Enter the date the sign off expires - fixed date only"/> 
         </div> 
         <div>
         	<label for="applytoall">Apply to all members</label>
			 <input type="checkbox" name="applytoall" id="applytoall" value="false" class="checked_class">
		 </div>
		 <br style="clear:both;">
		 <div>
         <button id="add" class="view">Add</button>
 		 <button id="update" class="cb_edit">Update</button>
		</div>
		</form>			
	');
}		
?>  

<div  class="Table">
    <div class="Title">
        <p>Current SignOff types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>Type ID</p>
        </div>  
        <div class="Cell2"  >
            <p>Sign off</p>
        </div>
         <div class="Cell2"  >
            <p>Authority</p>
        </div>      
        <div class="Cell"  >
            <p>Effective Period</p>
        </div>      
        <div class="Cell"  >
            <p>Fixed Date</p>
        </div>      
        <div class="Cell0"  >
            <p>No Fly</p>
        </div>      
        <div class="Cell0"  >
            <p>Apply to all</p>
        </div>      
    </div>
</div> 		
</div>    
    <h4>Instructions</h4>
    <p>
    	Fill in the Sign off in the Signoff field, example: "FAA Biannual Review." For Authority the role that can sign off; Treasurer, CFI-G, etc.
    Check "No  Fly" if lack of this sign off will put the person on the "No Fly" list. For Effective Period; When the signoff expires: Yearly(12 months from sign off)
    Yearly-EOM(Yearly but to the end of the Month), Biennial(24 months), Biennial-EOM,(24 months + end of Month), No Expire or "Fixed Date". If "Fixed Date" is selected
    a new field will appear. Enter the Month and Day of the year the sign off expires. Example: Dues expires March 31 (03/31) of each year. Check "Apply to Existing" if this
    new sign off should be applied to current members. 
    </p>
    <p>
    As signoffs are created they will be listed below. clicking on the "ID" of a row will populate the above form with the existing information. The submit button will 
    change to "Update Changes." This allow an existing signoff to be corrected or updated. You can not delete a signoff once it has been created. 
    </p>
