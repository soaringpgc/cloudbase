<br>
<style type="text/css">
    .Table
    {
        display: table;
    }
    .Title
    {
        display: table-caption;
        text-align: center;
        font-weight: bold;
        font-size: larger;
    }
    .Heading
    {
        display: table-row;
        font-weight: bold;
        text-align: center;
    }
    .Row
    {
        display: table-row;
    }
    .Cell
    {
        display: table-cell;
         text-align: center;
        border: solid;
        border-width: thin;
        padding-left: 5px;
        padding-right: 5px;
        width: 50px;
    }
</style>
<!-- 
moved to javascript file to keep wp_enquescripts from throwing an error. 
<script type="text/template" id="feeitemtemplate">
    <div class="Row">
        <div class="Cell">
            <%= altitude %>
        </div>
        <div class="Cell">
            <%=  charge %>
        </div>
        <div class="Cell">
            <%=  hook_up %>
        </div>
        <div class="Cell">
            <button class="delete"></button> 
        </div>
    </div>
</script>
 -->

<!-- 


<script type="text/template" id="feeitemtemplate">
    <div class="Row">
        <div class="Cell">
           <%= id %>
        </div>
        <div class="Cell">
            <%= altitude %>
        </div>
        <div class="Cell">
            <%=  charge %>
        </div>
        <div class="Cell">
            <%=  hookup %>
        </div>
        <div class="Cell">
            <button class="delete"></button> 
        </div>
    </div>
</script>
 -->


<script type="text/template" id="feeitemtemplate_0">
	<div class="fee-view">
		<label><%= altitude %></label> <label><%=  charge %></label> <label><%=  hookup %></label> 
		    <input type="hidden" id="fee_id" name="fee_id" value=<%= id %>> <button class="delete"></button> 
	</div>
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
            size = "12"
            title = "Rlease altitude Above Ground Level(AGL)." 
            name = "altitude"/>
        <label for="charge">Charge: </label>
        <input type = "number"
            step="0.01"
            id = "charge"
            size = "6"
            title = "Charge for given altitude." 
            name = "charge"/>
        <label for="hookup">Base fee: </label>
        <input type = "number"
            step="0.01"
            id = "hook_up"
            size = "6"
            value ="0"
            title = "Charge for hookup." 
            name = "charge"/>      
        <button id="add">Add</button>
<!-- 		 <?php wp_nonce_field('tow_charge' ) ?> 
       <?php    submit_button();	 ?>
-->
       </div>
    </form>

<div  class="TowFee" class="Table">
    <div class="Title">
        <p>Tow Fees </p>
    </div>
    <div class="Heading">
<!-- 
        <div class="Cell">
            <p>ID</p>
        </div>
 -->
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
            <p>Delete</p>
        </div>
    </div>
</div>

</div>

    
    <h4>Instructions</h4>



