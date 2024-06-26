// templates for admin scripts. 
// loaded with wp_register_script  & wp_enque_script see class-cloud-base-admin.php
//

// fees 
var feeitemtemplate = _.template(`
     <div class="Cell"><%= altitude %></div>
     <div >
          <div class="hiding" > <%= id %></div>
     	 <label class="Cell"><%=  charge %></label>
     	 <label class="Cell"><%=  hook_up %></label>
     	 <label class="Cell"><%=  hourly %></label>
     	 <div class="Cell"><button class="delete" ">Delete</button></div>
      </div>
`);

// Aircraft Types 
  var actypetemplate = _.template(`  
   <div >
     <div class="hiding" > <%= id %></div>
   	 <label class="Cell"><%=  type_id     %></label>
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
   <div>
     <div class="Cell"  id = "id"> <%= id %></div>
     <label class="Cell"><%= title %></label>
     <label class="Cell"><%= description %></label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
`);
// Status Types 
  var statustypetemplate = _.template(`  
     <div >
      <div class="Cell"  id = "id" > <%= id %></div>
     <label class="Cell" ><%= title %></label>
     <label class="Cell" ><input type="color" value= <%= color %> > </label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
`);
// Aircraft 
  var aircrafttemplate = _.template(`  
  <div >
  		<div class="hiding" > <%= id %></div>
  		<div class="Cell" > <%= aircraft_id %></div>
  		<div class="hiding" > <%= aircraft_type %></div>
  		<div class="Cell" > <%= type %></div>  
       <label class="Cell" > <%= registration    %></label>
       <label class="Cell" > <%= compitition_id  %></label>
       <label class="Cell" > <%= make            %></label>
       <label class="Cell" > <%= model           %></label>
        <div class="hiding" > <%= captian_id %></div>  
        <label class="Cell" > <%= captian_name      %></label>
       <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
`);

// Sign off Types 
  var signofftemplate = _.template(`
  
   <div>
    <div class="Cell" > <%= id %></div>
     <label class="Cell2"><%=  signoff_type  %> </label>
     <div class="hiding" > <%= authority %></div>
     <label class="Cell2"><%=  authority_label  %> </label>
     <label class="Cell"><%=  period_label  %> </label>
     <div class="hiding" > <%= period %></div>
     <label class="Cell"><%=  fixed_date %> </label>
     <label class="Cell0"><%=  no_fly   %> </label>
     <label class="Cell0"><%=  applytoall  %> </label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div >
`);

// Aircraft 
  var aircrafttemplate_alt = _.template(`  
  <div >
  		<div class="hiding" > <%= id %></div>
  		<div class="Cell" > <%= aircraft_id %></div>
  		<div class="Cell" > <%= type %></div>  
       <label class="Cell" > <%= registration    %></label>
       <label class="Cell" > <%= compitition_id  %></label>
       <label class="Cell" > <%= make            %></label>
       <label class="Cell" > <%= model           %></label>
       <label class="hiding" > <%= status 		 %></label>
       <label class="Cell" > <%= status_t 		 %></label> 
       <label class="Cell3" > <%= comment 		 %></label>
       <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
`);
//   <div class="Cell" > <%= typeof(compitition_id) !== 'undefined' ? compitition_id : ' ' %></div>
