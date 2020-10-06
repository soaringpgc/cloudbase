<br>
<script language="JavaScript">
	var cb_admin_tab = "status_types";
</script>
<div style="display:inline-block"  align:left id="status_types"  class="status_type editform" >
<?php 			
if( current_user_can( 'manage_options' ) ) {	
  echo ('   
   <h3>Status Types</h3><DIV>   
   <form id="addstatus_type" action="#" >
    	<div>
    	<input type = "hidden"
            id = "id"
            size = "2"
            value = ""
            name = "id"/>
        <input type = "text"
            id = "title"
            size = "8"
            title = "Status ." 
            name = "title"/> 
        <input type = "hidden"
            id = "active"
            size = "2"
            value = ""
            name = "active"/> 
        <button id="add" class="view">Add</button>
        <button id="update" class="edit">Update</button>
       </div>
    </form></DIV>');
}    
?>    

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
</div>

</div>

    
    <h4>Instructions</h4>
<p>      
    This like 'flight types' is a bit silly. However the altenative is to hard code them
    into the code. And as soon as you do that someone will want to change it. 
</p><p>      
    You can not delete a status type if an aircraft is assigned to that type. 
</p><p>  
    To edit an existing item double click anywhere in that line. The data will be copied 
    to the form at the top of the page and the button will change to "Update" click on
    Update to save the new values.  
</p>    

 
