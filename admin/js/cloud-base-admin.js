(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 $(function(){
// hide the expire date unless fixed date is selected.
	 	$("#expire_date").hide(); 	
	 var app = app || {};

//   	$(".calendar").mouseenter(function(){	 
	 $(".calendar").on( "click", function(){
  		 $('.calendar').datepicker({dateFormat : 'yy-mm-dd', showAnim: "slideDown"}); 
  		 $('.calendar').datepicker("setDate", new Date());
  	 });


// show the expire date filed if fixed date is selected. 
		$("#eff_period").on( "mouseleave", function(){
  			if ( ($("#eff_period").val() === "fixed") || ($("#eff_period").val() === "dues") ) {
		  		$("#expire_date").show();
  			} else {
	    		$("#expire_date").hide(); 	
 			}
		 })	;   
// to make it work on iPad, (this needs additional work. )
		$("#eff_period").on('touchend', function(e){
  			if ( ($("#eff_period").val() === "fixed")) {
		  		$("#expire_date").show();
  			} else {
	    		$("#expire_date").hide(); 	
 			}
		 })	;   		 		 	 

//NTFS: need to use `` or get unexpected EOF error. 	
// needs to be here rather than 'includes' because templates are compiled into 
// functions and the way wordpress load-scripts works, if the template function is not
// found we get an error.  templates moved to /js/templates.js file (and enqued with
// wp_register_scripts, wp_enque_scripts )
	 
//models
// aircraft type model 
	app.Model = Backbone.Model.extend({
	// over ride the sync function to include the Wordpress nonce. 
	// going to need this for everything so do it once.  
	  	sync: function( method, model, options ){
    		return Backbone.sync(method, this, jQuery.extend( options, {
      			beforeSend: function (xhr) {
//      			alert(cloud_base_admin_vars.nonce);
        		xhr.setRequestHeader( 'X-WP-NONCE', cloud_base_admin_vars.nonce );
      			},
   			} ));	
   		},	
	});
	app.AircraftType = app.Model.extend({
		initialize: function(){
		},	
	    defaults: {		
		},
		wait: true	
	} );
	app.FlightType = app.Model.extend({
		initialize: function(){
		},	
	    defaults: {		
		},
		wait: true	
	} );
	app.StatusType = app.Model.extend({
		initialize: function(){
		},	
	    defaults: {		
		},
		wait: true	
	} );
	app.Towfee = app.Model.extend({
		initialize: function(){

		},
		wait: true		
	} );
	app.Aircraft = app.Model.extend({
		idAttribute : "aircraft_id",
		initialize: function(){
		},
		wait: true,			
	} );
	app.SignOffType = app.Model.extend({
		initialize: function(){
		},	
	    defaults: {	
	    	authority_label: ' ',
	    	period_label :' '	
		},
		wait: true	
	} );		
// collections	
    app.Collection = Backbone.Collection.extend({	
    	sync: function( method, model, options ){
    		return Backbone.sync(method, this, jQuery.extend( options, {
      			beforeSend: function (xhr) {
 //     			alert(cloud_base_admin_vars.nonce);
        		xhr.setRequestHeader( 'X-WP-NONCE', cloud_base_admin_vars.nonce );
      			},
   			} ));	
   		},	
   	 }) ; 

    app.TowFeesList = app.Collection.extend({
    	model: app.Towfee,
    	url: cloud_base_admin_vars.root + 'cloud_base/v1/fees',  
   	 }) ; 
    app.AircraftTypeList = app.Collection.extend({
    	model: app.AircraftType,
    	url: cloud_base_admin_vars.root + 'cloud_base/v1/aircraft_types',			
    }) ; 
    app.FlightTypeList = app.Collection.extend({
    	model: app.AircraftType,
    	url: cloud_base_admin_vars.root + 'cloud_base/v1/flight_types',			
    }) ; 
    app.StatusTypeList = app.Collection.extend({
    	model: app.AircraftType,
    	url: cloud_base_admin_vars.root + 'cloud_base/v1/aircraft_status',			
    }) ; 
     app.AircraftList = app.Collection.extend({
    	model: app.AircraftType,
    	url: cloud_base_admin_vars.root + 'cloud_base/v1/aircraft',	
    }) ; 
    app.SignOffList =   app.Collection.extend({
    	model: app.AircraftType,
    	url: cloud_base_admin_vars.root + 'cloud_base/v1/sign_off_types',			
    }) ;    	   	
// model view	
	app.ModelView = Backbone.View.extend({
		tagName: 'div',
        className: 'Row',
		render: function(){
			this.$el.html( this.template(this.model.toJSON() ) );
			this.$input = this.$('.edit');
			return this;
		},
		initialize: function(){
    		this.model.on('change', this.render, this);
  		},
		events:{
			'click .delete' : 'deleteItem',
			'dblclick label' : 'update'
		},
		deleteItem: function(){
			this.model.destroy();
			this.remove();
		},
   		update: function(){
			var localmodel = this.model;
 			$("div.editform").addClass('editing');
             // 			
             // NTFS this requires the form id's to be the same as the model id's.
             // we are looping over the form, picking up the id's and then getting the 
             // value of the same id in the model and then loading it back into the from
             //  someone (probably me) is going to hate me in the future.  -dsj
            $(this.localDivTag).children('input').each(function(i, el ){
//             console.log( el );
      		   if(el.type === "checkbox" ){
      		   		if (localmodel.get(el.id) === "1" ){
      		   			$('#'+el.id).prop("checked", true);
      		   		} else {
      		   		    $('#'+el.id).prop("checked", false);
      		   		}
      		   } else {
      		      $('#'+el.id).val(localmodel.get(el.id));
      		   }  
      		});     		
      		$(this.localDivTag).children('select').each(function(i, el ){
				$('#'+el.id).val(localmodel.get(el.id));
      		});
      		$(this.localDivTag).children('textarea').each(function(i, el ){
				$('#'+el.id).val(localmodel.get(el.id));
      		});
		},
		deleteItem: function(){
			this.model.destroy(
			{
    			wait: true,			
    			error: function(model, response) {
    				var parsedmessage = JSON.parse(response.responseText);
    				 alert(JSON.stringify(parsedmessage.message));
    				},	
    			success: (function(model, response){
            		this.remove();  
    			 	}).bind(this) //  NTFS: ".bind(this)" makes the right "this" available to the callback. 
    			}	
			)
 		},	   
	});
	app.AircraftView = app.ModelView.extend({
	    template: aircrafttemplate,     
	});
	app.TowFeeView = app.ModelView.extend({
	    template: feeitemtemplate,
	});
	app.FlightTypeView = app.ModelView.extend({
	    template: flighttypetemplate,
	});
	app.StatusTypeView = app.ModelView.extend({
       template: statustypetemplate,
	});	
	app.AircraftTypeView = app.ModelView.extend({
	    template: actypetemplate,
	});
	app.SignOffTypeView = app.ModelView.extend({
	    template: signofftemplate,
	});
	
	app.CollectionView =  Backbone.View.extend({         
      initialize: function(){
//      	console.log('the view has been initialized. ');
        this.collection.fetch({reset:true});
        this.render();
        this.listenTo(this.collection, 'add', this.renderItem);
        this.listenTo(this.collection, 'reset', this.render);
      },
      render: function(){
      	this.collection.each(function(item){
  			this.renderItem(item);    	
      	}, this );
      },
      events:{
      	'click #add' : 'addItem',
      	'click #update' : 'updateItem'
      },
      addItem: function(e){
      	e.preventDefault();
      	var formData ={};
      	// grab all of the input fields
 		$(this.localDivTag).children('input').each(function(i, el ){
		  if($(el).val() != ''){
		  	if($(el).hasClass('checked_class')){
		  		formData[el.id]=($(el).is(":checked")? true : false );
		  	} else {
        		formData[el.id] = $(el).val();
        	}
      	  } 
      	});
      	//grab all of the <select> fields 
      	$(this.localDivTag).children('select').each(function(i, el ){
      		if($(el).val() != ''){
      			formData[el.id] = $(el).val();
      		}
      	});
      	$(this.localDivTag).children('textarea').each(function(i, el ){
      		if($(el).val() != ''){
      			formData[el.id] = $(el).val();
      		}
      	});
//  alert(JSON.stringify(formData));
      	this.collection.create( formData, {wait: true, error: function(model, response, error){
//       	console.log(response);
//       				var mresult= JSON.parse(response.responseText);     	
//       				alert(mresult["message"]) 
      				} 
      			});
      	// clean out the form:
      		$(this.localDivTag).children('input').each(function(i, el ){
				$('#'+el.id).val('');
      		});       
      		$(this.localDivTag).children('select').each(function(i, el ){
				$('#'+el.id).val('');
      		});  
      		$(this.localDivTag).children('textarea').each(function(i, el ){
				$('#'+el.id).val('');
      		});       
      },
      updateItem: function(e){     	
		e.preventDefault();
 		var formData ={};
		// grab all of the input fields
 		$(this.localDivTag).children('input').each(function(i, el ){
 		 if($(el).val() != ''){
		  	if($(el).hasClass('checked_class')){
		  		formData[el.id]=($(el).is(":checked")? true : false );
		  	} else {
        		formData[el.id] = $(el).val();
        	}
      	  } 		
      	});
      	//grab all of the <select> fields 
      	$(this.localDivTag).children('select').each(function(i, el ){
      	  if($(el).val() != ''){
      		formData[el.id] = $(el).val();
      	  }
      	});
      	$(this.localDivTag).children('textarea').each(function(i, el ){
      	  if($(el).val() != ''){
      		formData[el.id] = $(el).val();
      	  }
      	});
 //    	alert(JSON.stringify(formData));
//      console.log(formData);
      	var updateModel = this.collection.get(formData.id);
        updateModel.save(formData, {
        	wait: true, 
        		error: function(model, response, error){
//         		console.log(response.responseText);
      			var mresult= JSON.parse(response.responseText);     	
      			alert(mresult["message"]) 
      			}         
        	});
	// clean out the form:
      		$(this.localDivTag).children('input').each(function(i, el ){
				$('#'+el.id).val('');
      		});       
      		$(this.localDivTag).children('select').each(function(i, el ){
				$('#'+el.id).val('');
      		}); 
      		$(this.localDivTag).children('textarea').each(function(i, el ){
				$('#'+el.id).val('');
      		});           
		$("div.editform").removeClass('editing');	
      	}
	});
	app.AircraftsView = app.CollectionView.extend({
	 	el: '#aircrafts', 
		localDivTag: '#addAircraft Div',
	 	preinitialize(){
	 	   this.collection = new app.AircraftList();
	 	},	
        renderItem: function(item){
            var expandedView = app.AircraftView.extend({ localDivTag:this.localDivTag });
            var itemView = new expandedView({
//      		var itemView = new app.AircraftView({
      	  		model: item
      		})
      		this.$el.append( itemView.render().el);   
        }
	 });
	 app.TowFeesView = app.CollectionView.extend({
	 	el: '#tow_fees', 
		localDivTag: '#addTowFee Div',
	 	preinitialize(){
	 	   this.collection = new app.TowFeesList();
	 	},	
        renderItem: function(item){
            var expandedView = app.TowFeeView.extend({ localDivTag:this.localDivTag });
            var itemView = new expandedView({
//      		var itemView = new app.TowFeeView({
      	  		model: item
      		})
      		this.$el.append( itemView.render().el);   
        }
	 });	    
	 app.AircraftTypesView = app.CollectionView.extend({
	 	el: '#aircraft_types', 
	 	localDivTag: '#addaircraft_type Div',
	 	preinitialize(collection){
	 	   this.collection = new app.AircraftTypeList();
	 	},	
        renderItem: function(item){
            var expandedView = app.AircraftTypeView.extend({ localDivTag:this.localDivTag });
            var itemView = new expandedView({
//      		var itemView = new app.AircraftTypeView({
      	  		model: item
      		})
      		this.$el.append( itemView.render().el);   
        }
 	 });
	 app.FlightTypesView = app.CollectionView.extend({
	 	el: '#flight_types', 
	 	localDivTag: '#addflight_type Div',
	 	preinitialize(collection){
	 	   this.collection = new app.FlightTypeList();
	 	},	
        renderItem: function(item){
            var expandedView = app.FlightTypeView.extend({ localDivTag:this.localDivTag });
            var itemView = new expandedView({
//      		var itemView = new app.FlightTypeView({
      	  		model: item
      		})
      		this.$el.append( itemView.render().el);   
        }
 	 }); 
	 app.StatusTypesView = app.CollectionView.extend({
	 	el: '#status_types', 
	 	localDivTag: '#addstatus_type Div',
	 	preinitialize(collection){
	 	   this.collection = new app.StatusTypeList();
	 	},	
        renderItem: function(item){
        	var expandedView = app.StatusTypeView.extend({ localDivTag:this.localDivTag });
//      		var itemView = new app.StatusTypeView({
			var itemView = new expandedView({
      	  		model: item
      		})
      		this.$el.append( itemView.render().el);   
        }
 	 });  
 	 app.SignOffTypesView = app.CollectionView.extend({
	 	el: '#sign_off_types', 
	 	localDivTag: '#addsign_off_type Div',
	 	preinitialize(collection){
	 	   this.collection = new app.SignOffList();
	 	},	
        renderItem: function(item){
            var expandedView = app.SignOffTypeView.extend({ localDivTag:this.localDivTag });
            var itemView = new expandedView({
      	  		model: item
      		})
      		this.$el.append( itemView.render().el);   
        }
 	 });  
 	 	      
   $(function(){
   if (typeof cb_admin_tab !== 'undefined' ){
   		switch(cb_admin_tab){
   			case "tow_fee" : new app.TowFeesView();
   			break;
   			case "aircraft_types" : new app.AircraftTypesView();
   			break;
   			case "flight_types" : new app.FlightTypesView();
   			break;
   			case "status_types" : new app.StatusTypesView();
   			break;
   			case "aircraft" : new app.AircraftsView();
   			break;
   			case "sign_off_types" : new app.SignOffTypesView();
   			break;
   		}
   	} else {
   	console.log("not defined");}
   });

  }) // $(function) close
})( jQuery );
