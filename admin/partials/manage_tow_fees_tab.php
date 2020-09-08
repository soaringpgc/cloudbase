<br>
<script language="JavaScript">
	var cb_admin_tab = "tow_fee";
</script>

<div style="display:inline-block"  align:left id="tow_fees"  class="TowFee" >
    <h3>Tow Charges</h3>
    <form id="addTowFee" action="#" >
    	<div>
  	  <input type="hidden" >
<!-- 
        <input type = "hidden"
            id = "record_id"
            size = "5"
            value = ""
            name = "record_id"/>
 -->
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
        <label for="hourly">Hourly: </label>
        <input type = "number"
            step="0.01"
            id = "hourly"
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



