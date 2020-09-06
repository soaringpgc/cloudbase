// templates for admin scripts. 
// loaded with wp_register_script  & wp_enque_script see class-cloud-base-admin.php
//

// fees 
 var feeitemtemplate = _.template(`
 <div class="Row" id=<%= altitude %> >
     <div class="Cell">
         <%= altitude %>
     </div>
     <div class="Cell edit">
        <input value = <%=  charge %> size=6>
     </div>
     <div class="Cell ">
         <input value = <%=  hook_up %> size=6>
     </div>
     <div class="Cell ">
         <input value = <%=  base_charge %> size=6>
     </div>
     <div class="Cell">
        <button class="delete"></button> 
     </div>
 </div>`);
// Aircraft Types 
  var actypetemplate = _.template(`
   <div class="Cell" > <%= id %></div>
   <div class="view">
     <label class="Cell"><%=  title %></label>
     <div class="Cell"><button class="delete" ">Delete</button></div>
   </div>
   <input class="edit" value = <%=  title %> size=11 id="aircraft_type" >
`);

  var actypetemplate_o = _.template(`
     <div class="Cell" id="inputcell">
         <%= id %>
     </div>
     <div class="Cell edit" >
        <input value = <%=  title %> size=15 id="aircraft_type">
     </div>
`);
