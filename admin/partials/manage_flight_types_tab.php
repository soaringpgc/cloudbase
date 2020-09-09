<br>
<script language="JavaScript">
	var cb_admin_tab = "flight_types";
</script>

<div style="display:inline-block"  align:left id="flight_types"  class="flight_type" >
    <h3>Flight Type</h3><DIV>
    <form id="addflight_type" action="#" >
    	<div>
        <input type = "text"
            id = "type"
            size = "8"
            title = "Type of flight." 
            name = "type"/>
        <button id="add">Add</button>
<!-- 
		 <?php wp_nonce_field('tow_charge' ) ?> 
       <?php    submit_button();	 ?>
 -->
       </div>
    </form></DIV>

<div  class="Table">
    <div class="Title">
        <p>Flight Types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>ID</p>
        </div>
        <div class="Cell"  style="width: 13.3em">
            <p>Title</p>
        </div>
    </div>
    <div id="flightType-list"></div>
</div>

</div>
   
    <h4>Instructions</h4>
    
    Must have at least one flight type. 



