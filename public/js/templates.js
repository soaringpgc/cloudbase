// templates for public scripts. 
// loaded with wp_register_script  & wp_enque_script see class-cloud-base-public.php
//

// flight 
var flighttemplate = _.template(`
     <div class="edit"><%= id %></div>
     <label class="Cell0"><%=  flight_number %></label>
     <label class="Cell"><%=  aircraft_id %></label>
     <label class="Cell2"><%=  pilot_id %></label>     	 
     <label class="Cell0"> <button id="launch" class="buttonlaunch view"></button><%=  start_time %></label class="edit">
     	 <button class="landing" ">Landing</button><div class="el_time"><%=  end_time %></label> </div>
     	     	 <label class="Cell"><%=  end_time %></label>   
     	 <label class="Cell"><%=  flight_fee_id %></label>   
     	 <div class="edit" > <%= tow_pilot_id %></div>	 
     	 <div class="edit" > <%= tow_plane_id %></div>	 

`);


// Sign off Types 
  var signofftemplate = _.template(`
   <div class="Cell" > <%= id %></div>
   <div>
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


//   <div class="Cell" > <%= typeof(compitition_id) !== 'undefined' ? compitition_id : ' ' %></div>
