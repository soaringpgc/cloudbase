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
   <div class="Cell" > <%= aircraft_id %></div>
   <div class="view">
       <label class="Cell" > <%= registration %></label>
       <label class="Cell" > <%=  compitition_id  %></label>
       <label class="Cell" > <%=  type  %></label>
       <label class="Cell" > <%=  make %></label>
       <label class="Cell" > <%=  model %></label>
       <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
   <input class="edit" value = <%= typeof(registration) !== 'undefined' ?  registration : ' ' %> size=11 id="registration" >
   <input class="edit" value = <%= typeof(compitition_id)  !== '' ? compitition_id : '_'  %>  size=11 id="compitition_id" >
   <input class="edit" value = <%= type %> size=11 id="type" >
   <input class="edit" value = <%= make %> size=11 id="make" >
   <input class="edit" value = <%= typeof(model) === '' ? 'a' : '_' %> size=11 id="make" >
`);


//   <div class="Cell" > <%= typeof(compitition_id) !== 'undefined' ? compitition_id : ' ' %></div>
