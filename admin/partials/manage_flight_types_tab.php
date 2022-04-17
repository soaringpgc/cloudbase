<br>
<script language="JavaScript">
	var cb_admin_tab = "flight_types";
</script>

<div style="display:inline-block"  align:left id="flight_types"  class="flight_type editform"  >
<?php 			
if( current_user_can( 'manage_options' ) ) {	
  echo(' 
    <h3>Flight Type</h3><DIV>
    <form id="addflight_type" action="#" >
    	<div>
    	<input type = "hidden"
            id = "id"
            size = "2"
            value = ""
            name = "id"/>
        <div class="hform">         
       <label for="title" >Title:</label>    
        <input type = "text"
            id = "title"
            size = "8"
            title = "Type of flight." 
            name = "title"/>
        </div>   
        <div class="hform">     
        <label for="description" >Description:</label>    
        <input type = "text"
            id = "description"
            size = "20"
            title = "Type of flight." 
            name = "Description"/>
        </div>           
        <input type = "hidden"
            id = "active"
            size = "2"
            value = ""
            name = "active"/> 
        <br style="clear:both;">
        <div>
          <button id="add" class="view">Add</button>
          <button id="update" class="cb_edit">Update</button>
        </div>
       </div>
    </form> </DIV>');
}    
?>    

<div  class="Table">
    <div class="Title">
        <p>Flight Types</p>
    </div>
    <div class="Heading">
        <div class="Cell"  >
            <p>ID</p>
        </div>
        <div class="Cell"">
            <p>Title</p>
        </div>
                <div class="Cell">
            <p>Description</p>
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



