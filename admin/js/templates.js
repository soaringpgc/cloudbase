// templates for admin scripts. 
// loaded with wp_register_script  & wp_enque_script see class-cloud-base-admin.php
//

// fees 
var feeitemtemplate = _.template(`
     <div class="Cell"><%= altitude %></div>
     <div class="view">
     	 <label class="Cell"><%=  charge %></label>
     	 <label class="Cell"><%=  hook_up %></label>
     	 <label class="Cell"><%=  hourly %></label>
     	 <div class="Cell"><button class="delete" ">Delete</button></div>
      </div>
     <div class="edit">
         <input  value = <%=  charge  %> size=11 id="tow_fee" >
         <input  value = <%=  hook_up %> size=11 id="hook_up">
         <input  value = <%=  hourly  %> size=12 id="hourly">
    </div>
`);

// Aircraft Types 
  var actypetemplate = _.template(`
   <div class="Cell" > <%= id %></div>
   <div class="view">
     <label class="Cell"><%=  title %></label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
   <input class="edit" value = <%=  title %> size=11 id="aircraft_type" >
`);

// Flight Types 
  var flighttypetemplate = _.template(`
   <div class="Cell" > <%= id %></div>
   <div class="view">
     <label class="Cell"><%= title %></label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
   <input class="edit" value = <%= title %> size=11 id="flight_type" >
`);
// Flight Types 
  var statustypetemplate = _.template(`
   <div class="Cell" > <%= id %></div>
   <div class="view">
     <label class="Cell"><%= title %></label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
   <input class="edit" value = <%= title %> size=11 id="status_type" >
`);
// Flight Types 
  var aircrafttemplate = _.template(`
   <div class="Cell" > <%= Registration %></div>
   <div class="Cell" > <%= Competition %></div>
   <div class="view">
        <label class="Cell"><%=  annual_due_date %></label>
     	<label class="Cell"><%=  registration_due_date  %></label>
     	<label class="Cell"><%=  status %></label>
    	<label class="Cell"><%= captian_id %></label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
   <input class="edit" value = <%= annual_due_date %> size=11 id="status_type" >
`);


