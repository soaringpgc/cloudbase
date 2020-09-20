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
   <div class="Cell" > <%= type_id %></div>
   <div class="view">
     <label class="Cell"><%=  title       %> </label>
     <label class="Cell"><%=  sort_code   %> </label>
     <label class="Cell"><%=  base_charge %> </label>
     <label class="Cell"><%=  first_hour  %> </label>
     <label class="Cell"><%=  each_hour   %> </label>
     <label class="Cell"><%=  min_charge  %> </label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div >
    <div class="edit">
      <input value = <%=  title       %> size=11 id="aircraft_type" >
      <input value = <%= typeof(sort_code)  !== 'object' ? sort_code   : '" "'  %>  size=7 id="sort_code" >
      <input value = <%= typeof(base_charge)!== 'object' ? base_charge : '" "'  %>  size=10 id="base_charge" >
      <input value = <%= typeof(first_hour) !== 'object' ? first_hour  : '" "'  %>  size=10 id="first_hour" >
      <input value = <%= typeof(each_hour)  !== 'object' ? each_hour   : '" "'  %>  size=10 id="each_hour" >
      <input value = <%= typeof(min_charge) !== 'object' ? min_charge  : '" "'  %>  size=10 id="min_charge" >  
     </div>
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
   <div class="Cell" > <%= type %></div>
   <div class="view">
       <label class="Cell" > <%= registration     %></label>
       <label class="Cell" > <%= compitition_id  %></label>
       <label class="Cell" > <%= make            %></label>
       <label class="Cell" > <%= model           %></label>
       <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
   <div class="edit">
     <input value = <%= typeof(registration)    !== 'object' ? registration   : '" "'  %>  size=11 id="registration" >
     <input value = <%= typeof(compitition_id)  !== 'object' ? compitition_id : '" "'  %>  size=7 id="compitition_id" >
     <input value = <%= make   !== '' ? make   : '" "'  %>  size=10 id="make" >
     <input value = <%= model  !== '' ? model  : '" "'  %>  size=10 id="model" >
   </div>
`);


//   <div class="Cell" > <%= typeof(compitition_id) !== 'undefined' ? compitition_id : ' ' %></div>
