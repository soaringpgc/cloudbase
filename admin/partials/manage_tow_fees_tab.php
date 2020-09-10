<br>
<script language="JavaScript">
	var cb_admin_tab = "tow_fee";
</script>

<div style="display:inline-block"  align:left id="tow_fees"  class="TowFee" >
    <h3>Tow Charges</h3>
    <form id="addTowFee" action="#" >
    	<div>
  	  <input type="hidden" >
        <label for="altitude">Altitude
        <?php 
    		  if (get_option("glider_club_tow_units") == "m"){
    		  	echo '(m)' ;
    		  } else {
    		  	echo '(ft)' ;
    		  }
        ?>           
        </label>
        <input type = "text"
            id = "altitude"
            size = "8"
            title = "Rlease altitude Above Ground Level(AGL)." 
            name = "altitude"/>
        <label for="charge">Charge: </label>
        <input type = "number"
            step="0.01"
            id = "charge"
            style="width: 7em"
            title = "Charge for given altitude." />
        <label for="hookup">Base fee: </label>
        <input type = "number"
            step="0.01"
            id = "hook_up"
            style="width: 7em"
            value ="0"
            title = "base charge." />   
        <label for="hourly_fee">Hourly: </label>
        <input type = "number"
            step="0.01"
            id = "hourly_fee"
            style="width: 7em"
            value ="0"
            title = "additional hourly charge." />          
        <button id="add">Add</button>
<!-- 
		 <?php wp_nonce_field('tow_charge' ) ?> 
       <?php    submit_button();	 ?>
 -->
       </div>
    </form>

<div  class="TowFee Table">
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
    <div id="towfee-list"></div>
    </section>
</div>

</div>

    
    <h4>Instructions</h4>
    
    Tried to make this as flexible as possible. For normal tow fees; enter Altitude and
    Charge, leave Base Fee and Hourly blank. Altitude can be text such as "SRB" or "Self"
    Some clubs have a hook up fee that would be entered in the Base fee field. 
    
    For retrive enter "Retrive" under altitude, the basic charge under Base fee and 
    charge for each additional hour under Hourly. 
    
    To edit an existing item double click anywhere in that line input fields will 
    replace the display fields and each can be updated. Press "Enter" to have the new
    values accepted. 
    
    



