// templates for admin scripts. 
// loaded with wp_register_script  & wp_enque_script see class-cloud-base-admin.php
//

// fees 
var feeitemtemplate = _.template(`
     <div class="Cell"><%= altitude %></div>
     <div >
     	 <label class="Cell"><%=  charge %></label>
     	 <label class="Cell"><%=  hook_up %></label>
     	 <label class="Cell"><%=  hourly %></label>
     	 <div class="Cell"><button class="delete" ">Delete</button></div>
      </div>
`);

// Aircraft Types 
  var actypetemplate = _.template(`
   <div class="Cell" > <%= type_id %></div>
   <div >
     <label class="Cell"><%=  title       %> </label>
     <label class="Cell"><%=  sort_code   %> </label>
     <label class="Cell"><%=  base_charge %> </label>
     <label class="Cell"><%=  first_hour  %> </label>
     <label class="Cell"><%=  each_hour   %> </label>
     <label class="Cell"><%=  min_charge  %> </label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div >
`);

// Flight Types 
  var flighttypetemplate = _.template(`
   <div class="Cell"  id = "id"> <%= id %></div>
   <div>
     <label class="Cell"><%= title %></label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
`);
// Status Types 
  var statustypetemplate = _.template(`
   <div class="Cell"  id = "id" > <%= id %></div>
   <div >
     <label class="Cell" ><%= title %></label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
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
     <input value = <%= typeof(compitition_id)  !== 'undefined' ? compitition_id : '" "'  %>  size=7 id="compitition_id" >
     <input value = <%= make   !== '' ? make   : '" "'  %>  size=10 id="make" >
     <input value = <%= model  !== '' ? model  : '" "'  %>  size=10 id="model" >
   </div>
`);

// Aircraft Types 
  var signofftemplate = _.template(`
   <div class="Cell" > <%= id %></div>
   <div class="view">
     <label class="Cell2"><%=  signoff_type  %> </label>
     <label class="Cell2"><%=  authority  %> </label>
     <label class="Cell"><%=  period   %> </label>
     <label class="Cell"><%=  fixed_date %> </label>
     <label class="Cell0"><%=  no_fly   %> </label>
     <label class="Cell0"><%=  applytoall  %> </label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div >
    <div class="edit">
      <input value = <%= signoff_type %> size=11 id="signoff_type" >
      <input value = <%= typeof(period)  !== 'object' ? period   : '" "'  %>  size=7 id="period" >
      <input value = <%= typeof(fixed_date)!== 'object' ? fixed_date : '" "'  %>  size=10 id="fixed_date" >
      <input value = <%= typeof(authority) !== 'object' ? authority  : '" "'  %>  size=10 id="authority" >
      <input value = <%= typeof(no_fly)  !== 'object' ? no_fly   : '" "'  %>  size=10 id="no_fly" >
      <input value = <%= typeof(applytoall) !== 'object' ? applytoall  : '" "'  %>  size=10 id="applytoall" >  
     </div>
`);


//   <div class="Cell" > <%= typeof(compitition_id) !== 'undefined' ? compitition_id : ' ' %></div>
