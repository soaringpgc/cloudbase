<br>
<script language="JavaScript">
	var cb_admin_tab = "aircraft_types";
</script>

<div style="display:inline-block"  align:left id="aircraft_types"  class="aircraft_type" >
    <h3>Aircraft Type</h3><DIV>
    <form id="aircraft_type" action="#" >
    	<div>
        <input type = "text"
            id = "type"
            size = "8"
            title = "Type of Aircraft ." 
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
        <p>Aircraft Types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>ID</p>
        </div>
        <div class="Cell"  style="width: 13.3em">
            <p>Title</p>
        </div>
    </div>
    <div id="aircraftType-list"></div>
</div>

</div>

    
    <h4>Instructions</h4>
    
    You can not delete a aircraft type if an aircraft is assigned to that type. 



