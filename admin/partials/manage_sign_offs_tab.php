<br>
<script language="JavaScript">
	var cb_admin_tab = "sign_offs";
</script>

<div style="display:inline-block"  align:left id="aircraft_types"  class="aircraft_type" >
    <h3>Sign Offs</h3><DIV>
    <form id="addaircraft_type" action="#" >
    	<div>
    	<label for="charge">Sign off: </label>
        <input type = "text"
            id = "signoff"
            size = "10"
            title = "signoff." 
            name = "signoff"/>
			<label for "authority">Authority</label>
			<select name ="authority" id="authority"  >
			<?php 
			  	$value_label_authority = array("read"=>"Self", "cb_edit_dues"=>"Treasurer", "cb_edit_operations"=>"Operations", "cb_edit_instruction"=>"CFI-G", 
			  	"cb_edit_cfig"=>"Chief Flight Instructor", "cb_edit_towpilot"=>"Chief Tow Pilot");
				foreach ($value_label_authority  as $key => $authority ){
					echo ('<option value="' . $key . '">' . $authority . '</option>');
				}	
									
				$value_lable_period = array("yearly"=>"Yearly", "biennial"=>"Biennial", "yearly-eom"=>"Yearly-EOM", "biennial-eom"=>"Biennial-EOM", "no_expire"=>"No expire", 
				"monthly" => "Monthly", "quarterly" => "Quarterly", "fixed"=>"Fixed Date" );		
				echo '</select> <label>No Fly</label><input type="checkbox" name="nofly" id="nofly" value="nofly" />
    					<label>Effective Period</label><select name ="period"  id="eff_period" title="EOM - End Of Month, select Fixed Date for specific date"> ';
    			foreach ($value_lable_period  as $key => $period ){
					echo ('<option value="' . $key . '">' . $period . '</option>');
				}	
				//not implemented yet....
				echo '</select> <label>Apply to existing</label><input type="checkbox" name="applyall" id="applyall" value="applyall" >';
			?>
    		
    		</select >
    	  <div  id="expire_date"  style="display:none" >
    		  <label>Expire Date</label>
     	    	 <input type = "text"
                 id = "expire"
                 class = "calendar"
                 name ="expire"
                 size = "10"
                 title = "Enter the date the sign off expires"
                />
         	</div>

        <button id="add">Add</button>
       </div>
    </form></DIV>

<div  class="Table">
    <div class="Title">
        <p>Sign Off Types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>ID</p>
        </div>  
        <div class="Cell"  >
            <p>Title</p>
        </div>
         <div class="Cell"  >
            <p>Authority</p>
        </div>      
        <div class="Cell"  >
            <p>No Fly</p>
        </div>      
        <div class="Cell"  >
            <p>Effective</p>
        </div>      
        <div class="Cell"  >
            <p>Date</p>
        </div>      
        <div class="Cell"  >
            <p>All</p>
        </div>      
       
    </div>
    <div id="aircraftType-list"></div>
</div>

</div>

    
    <h4>Instructions</h4>
<p>    Sort code determines if an aircraft will be listed in the glider(sort code 1) list
    or tow plane (sort code 2) lists. Future type of aircraft will use new (3+) sort codes. 
</p><p>    
    You can not delete a aircraft type if an aircraft is assigned to that type. 
</p><p>  
    To edit an existing item double click anywhere in that line input fields will 
    replace the display fields and each can be updated. Press "Enter" to have the new
    values accepted. 
</p><p>      
    Fields are provided for charging by aircraft type. Base charge, first hour, each houe
    and minimum charge fields are provided. How they are used is up to you. 
</p>    
