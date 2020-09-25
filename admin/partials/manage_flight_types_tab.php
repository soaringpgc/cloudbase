<br>
<script language="JavaScript">
	var cb_admin_tab = "flight_types";
</script>

<div style="display:inline-block"  align:left id="flight_types"  class="flight_type editform"  >
    <h3>Flight Type</h3><DIV>
    <form id="addflight_type" action="#" >
    	<div>
    	<input type = "hidden"
            id = "id"
            size = "2"
            value = ""
            name = "id"/>
        <input type = "text"
            id = "title"
            size = "8"
            title = "Type of flight." 
            name = "title"/>
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
</div>

</div>
   
    <h4>Instructions</h4>
<p>      
    Must have at least one flight type.
</p><p>      
    To edit an existing item double click anywhere in that line. The data will be copied 
    to the form at the top of the page and the button will change to "Update" click on
    Update to save the new values.  
</p>     



