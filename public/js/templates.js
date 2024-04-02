// templates for public scripts. 
// loaded with wp_register_script  & wp_enque_script see class-cloud-base-public.php
//

// flight 
var flighttemplate = _.template(`
     <div class="edit"><%= id %></div>
     <label class="Cell0"><%=  flight_number %></label>
     <label class="Cell"><%=  glider %></label>
     <label class="Cell2"><%=  p_last_name %>, <%=p_first_name %></label>     	 
     <div class="Cell0" id="button"> 
     	 <button id="launch"  class="viewstart buttonlaunch "></button>
  		 <button id="landing" class="viewstop  buttonlanding"></button>
     </div  >
     <label class="Cell"> 
     	  <div ><%=  start_display_time %></div>

     </label >
     	 					
     	 <label class="Cell"><%=  towplane %></label>   
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

var aircraftemplate = _.template(`
     <div class="edit"><%= id %></div>
     <label class="Cell0"><%=  aircraft_id %></label>
     <label class="Cell"><%=  registration %></label>
     <label class="Cell2"><%=  compitition_id %> </label>     
     <label class="Cell2"><%=  display_status %> </label> 
     <label class="Cell2"><%=  annual_due_date %> </label>       
     <label class="Cell2"><%=  registration_due_date %> </label> 
     <label class="Cell2"><%=  transponder_due %> </label> 
     <label class="Cell2"><%=  comments %> </label>                 	 
     <div class="Cell0" id="button"> 
     	 <button id="launch"  class="viewstart buttonlaunch "></button>
     </div  >
`);

var squawkTemplate = _.template(`
     <div class="edit"><%= id %></div>
     <label class="Cell0"><%=  aircraft_id %></label>
     <label class="Cell"><%=  registration %></label>
     <label class="Cell2"><%=  compitition_id %> </label>     
     <label class="Cell2"><%=  display_status %> </label> 
     <label class="Cell2"><%=  annual_due_date %> </label>       
     <label class="Cell2"><%=  registration_due_date %> </label> 
     <label class="Cell2"><%=  transponder_due %> </label> 
     <label class="Cell2"><%=  comments %> </label>                 	 
     <div class="Cell0" id="button"> 
     	 <button id="launch"  class="viewstart buttonlaunch "></button>
     </div  >
`);