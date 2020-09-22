<br>
<script language="JavaScript">
	var cb_admin_tab = "status_types";
</script>

<div style="display:inline-block"  align:left id="status_types"  class="status_type" >
    <h3>Status Types</h3><DIV>
    <form id="addstatus_type" action="#" >
    	<div>
        <input type = "text"
            id = "type"
            size = "8"
            title = "Status ." 
            name = "type"/> 
        <button id="add">Add</button>
       </div>
    </form></DIV>

<div  class="Table">
    <div class="Title">
        <p>Status Types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>ID</p>
        </div>
        <div class="Cell"  style="width: 13.3em">
            <p>Title</p>
        </div>
    </div>
    <div id="StatusType-list"></div>
</div>

</div>

    
    <h4>Instructions</h4>
<p>      
    This like 'flight types' is a bit silly. However the altenative is to hard code them
    into the code. And as soon as you do that someone will want to change it. 
</p><p>      
    You can not delete a status type if an aircraft is assigned to that type. 
</p><p>  
    To edit an existing item double click anywhere in that line input fields will 
    replace the display fields and each can be updated. Press "Enter" to have the new
    values accepted. 
</p>    

