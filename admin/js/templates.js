// templates for admin scripts. 
// loaded with wp_register_script  & wp_enque_script see class-cloud-base-admin.php
//

// fees 
 var feeitemtemplate_o = _.template(`

     <div class="Cell">
         <%= altitude %>
     </div>
     <div class="Cell">
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

var feeitemtemplate = _.template(`
     <div class="Cell">
         <%= altitude %>
     </div>
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


var feeitemtemplate_bu = _.template(`
     <div class="Cell">
         <%= altitude %>
     </div>
     <div class="view">
     	 <label class="Cell"><%=  charge %></label>
     	 <label class="Cell"><%=  hook_up %></label>
     	 <label class="Cell"><%=  hourly_fee %></label>
     	  <div class="Cell"><button class="delete" ">Delete</button></div>
      </div>
         <input class="edit" value = <%=  charge %> size=11 id="tow_fee" >
         <input class="edit" value = <%=  hook_up %> size=11 id="hook_up">
         <input class="edit" value = <%=  hourly_fee %> size=11 id="hourly_charge">
`);
