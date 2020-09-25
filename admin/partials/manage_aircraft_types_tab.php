<br>
<script language="JavaScript">
	var cb_admin_tab = "aircraft_types";
</script>

<div style="display:inline-block"  align:left id="aircraft_types"  class="aircraft_type editform" >
    <h3>Aircraft Type</h3><DIV>
    <form id="addaircraft_type" action="#" >
    	<div>
    	<input type = "hidden"
            id = "type_id"
            size = "2"
            value = ""
            name = "type_id"/>
    	<label for="charge">Aircraft type: </label>
        <input type = "text"
            id = "title"
            size = "10"
            title = "Type of Aircraft ." 
            name = "title"/>
        <label for="charge">Sort code: </label>
        <input type = "number"
            step="1"
            min="1"
            id = "sort_code"
            name = "sort_code"
            style="width: 3em"
            title = "Charge for given altitude." />
        <label for="hookup">Base fee: </label>
        <input type = "number"
            step="0.01"
            id = "base_charge"
            name = "base_charge"
            style="width: 5em"
            value ="0"
            title = "base charge." />   
        <label for="first_hour">First Hour: </label>
        <input type = "number"
            step="0.01"
            id = "first_hour"
            name = "first_hour"
            style="width: 5em"
            value ="0"
            title = "first hour charge." /> 
        <label for="each_hour">Each Hour: </label>
        <input type = "number"
            step="0.01"
            id = "each_hour"
            name = "each_hour"
            style="width: 5em"
            value ="0"
            title = "additional hourly charge." /> 
        <label for="each_hour">Min Charge: </label>
        <input type = "number"
            step="0.01"
            id = "min_charge"
            name = "min_charge"
            style="width: 5em"
            value ="0"
            title = "miminum charge." /> 
        <input type = "hidden"
            id = "active"
            size = "2"
            value = ""
            name = "active"/>    
        <button id="add" class="view">Add</button>
        <button id="update" class="edit">Update</button>
     </div>
    </form></DIV>

<div  class="Table">
    <div class="Title">
        <p>Aircraft Types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>Type ID</p>
        </div>  
        <div class="Cell"  >
            <p>Title</p>
        </div>
         <div class="Cell"  >
            <p>Sort Code</p>
        </div>      
        <div class="Cell"  >
            <p>Base Charge</p>
        </div>      
        <div class="Cell"  >
            <p>First Hour</p>
        </div>      
        <div class="Cell"  >
            <p>Each Hour</p>
        </div>      
        <div class="Cell"  >
            <p>Minimum Charge</p>
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
    Fields are provided for charging by aircraft type. Base charge, first hour, each hour
    and minimum charge fields are provided. 
</p>    

