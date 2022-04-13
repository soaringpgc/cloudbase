<br>
<script language="JavaScript">
	var cb_admin_tab = "tow_fee";
</script>

<div style="display:inline-block"  align:left id="tow_fees"  class="editform" >
<?php 			
if( current_user_can( 'manage_options' ) ) {	
  echo ('   
    <h3>Tow Charges</h3>
    <form id="addTowFee" action="#" >
    	<div>
     	<input type = "hidden"
            id = "id"
            size = "2"
            value = ""
            name = "id"/>
        <div class="hform">            
          <label for="altitude">Altitude');
    		  if (get_option("glider_club_tow_units") == "m"){
    		  	echo '(m)' ;
    		  } else {
    		  	echo '(ft)' ;
    		  }          
          echo (' </label>
          <input type = "text"
            id = "altitude"
            name = "altitude"
            size = "8"
            title = "Rlease altitude Above Ground Level(AGL)." 
            name = "altitude"/>
        </div>
        <div class="hform">
          <label for="charge">Charge: </label>
          <input type = "number"
            step="0.01"
            id = "charge"
            name = "charge"
            style="width: 7em"
            title = "Charge for given altitude." />
        </div>
        <div class="hform">
          <label for="hookup">Base fee: </label>
          <input type = "number"
            step="0.01"
            id = "hook_up"
            name = "hook_up"
            style="width: 7em"
            value ="0"
            title = "base charge." />   
        </div>
        <div class="hform">
          <label for="hourly_fee">Hourly: </label>
          <input type = "number"
            step="0.01"
            id = "hourly"
            name = "hourly"
            style="width: 7em"
            value ="0"
            title = "additional hourly charge." />  
          </div>          
		 <br style="clear:both;">
         <div>
          <button id="add" class="view">Add</button>
          <button id="update" class="edit">Update</button>
         </div>
    </form>');
}    
?>    
</div>

<div  class="Table">
    <div class="Title">
        <p>Tow Fees </p>
    </div>
    <div class="Heading">
        <div class="Cell">
            <p>Alitude</p>
        </div>
        <div class="Cell">
            <p>Fee</p>
        </div>
         <div class="Cell">
            <p>Base</p>
        </div>
        <div class="Cell">
            <p>Hourly</p>
        </div>
        <div class="Cell">
            <p>Delete</p>
        </div>
    </div>
    <section >
    </section>
</div>

</div>

    
    <h4>Instructions</h4>
<p>      
    Tried to make this as flexible as possible. For normal tow fees; enter Altitude and
    Charge, leave Base Fee and Hourly blank. Altitude can be text such as "SRB" or "Self"
    Some clubs have a hook up fee that would be entered in the Base fee field. 
</p><p>      
    For retrive enter "Retrive" under altitude, the basic charge under Base fee and 
    charge for each additional hour under Hourly. 
</p><p>      
    To edit an existing item double click anywhere in that line. The data will be copied 
    to the form at the top of the page and the button will change to "Update" click on
    Update to save the new values.  
</p><p>      
    



