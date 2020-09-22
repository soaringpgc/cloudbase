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
	 var app = app || {};

//   	$(".calendar").mouseenter(function(){	 
	 $(".calendar").click(function(){
  		 $('.calendar').datepicker({dateFormat : 'yy-mm-dd', showAnim: "slideDown"}); 
  		 $('.calendar').datepicker("setDate", new Date());
  	 });

// show the expire date filed if fixed date is selected. 
		$("#eff_period").mouseleave(function(){
  			if ( ($("#eff_period").val() === "fixed")) {
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
        		xhr.setRequestHeader( 'X-WP-NONCE', POST_SUBMITTER.nonce );
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
	    defaults: {
	    	charge: "0",
	    	hourly_fee: "0",
	    	hook_up: "0",
		},
		wait: true,			
	} );
	app.Aircraft = app.Model.extend({
		idAttribute : "aircraft_id",
		initialize: function(){
		},
	    defaults: {
	      registration: "N777RM",
          type: "Glider",
          make: "Grob",
          model: "G103",
		},
		wait: true,			
	} );
	app.SignOffType = app.Model.extend({
		initialize: function(){
		},	
	    defaults: {		
		},
		wait: true	
	} );		
// collections	
    app.TowFeesList = Backbone.Collection.extend({
    	model: app.Towfee,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/fees',  
   	 }) ; 
    app.AircraftTypeList = Backbone.Collection.extend({
    	model: app.AircraftType,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/aircraft_types',			
    }) ; 
    app.FlightTypeList = Backbone.Collection.extend({
    	model: app.AircraftType,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/flight_types',			
    }) ; 
    app.StatusTypeList = Backbone.Collection.extend({
    	model: app.AircraftType,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/status',			
    }) ; 
     app.AircraftList = Backbone.Collection.extend({
    	model: app.AircraftType,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/aircraft',			
    }) ; 
    app.SignOffList = Backbone.Collection.extend({
    	model: app.AircraftType,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/sign_off_types',			
    }) ;    	   	
// model view	
	app.ModelView = Backbone.View.extend({
		tagName: 'div',
        className: 'Row view',
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
			'dblclick label' : 'edit',
			'keypress .edit' : 'updateOnEnter',
			'blur .edit' : 'close'
		},
		deleteItem: function(){
			this.model.destroy();
			this.remove();
		},
		edit: function(e){
			this.$el.addClass('editing');
      		this.$input.focus();
		},
		updateOnEnter: function(e){
    		if(e.which == 13){
      			this.close();
    		}
   		},
	});
	app.AircraftView = app.ModelView.extend({
	        template: aircrafttemplate,
	   		close: function(){
   				var registration = this.$('#registration').val().trim();
  				var compitition_id = this.$('#compitition_id').val().trim();
   				var make = this.$('#make').val().trim();  
   				var model = this.$('#model').val().trim();  
    			if(registration || compitition_id || make  || model) {
     				this.model.save({"registration": registration, "compitition_id": compitition_id, "make": make,  "model": model });
    			}
           		this.$el.removeClass('editing');
  			}
	});
	app.TowFeeView = app.ModelView.extend({
	        template: feeitemtemplate,
	   		close: function(){
   				var tow_fee = this.$('#tow_fee').val().trim();
  				var base_charge = this.$('#hook_up').val().trim();
   				var hourly_fee = this.$('#hourly').val().trim();  
    			if(tow_fee || base_charge || hourly_fee) {
     				this.model.save({"charge": tow_fee, "hook_up": base_charge, "hourly": hourly_fee });
    			}
           		this.$el.removeClass('editing');
  			}
	});
	app.FlightTypeView = app.ModelView.extend({
	        template: flighttypetemplate,
   			close: function(){
   			var title_value = this.$('#flight_type').val().trim();
   			if(title_value){
   				this.model.save({ "title": title_value }, {error: function(model, response) {alert(JSON.stringify(response))}});
   			}
  			this.$el.removeClass('editing');
  		},
	});
	app.StatusTypeView = app.ModelView.extend({
       template: statustypetemplate,
	   close: function(){
		 var title_value = this.$('#status_type').val().trim();
		 if(title_value){
			this.model.save({ "title": title_value }, {error: function(model, response) {alert(JSON.stringify(response))}});
		 }
		 this.$el.removeClass('editing');
	   },
	});	
	app.AircraftTypeView = app.ModelView.extend({
	    template: actypetemplate,
   		close: function(){
   			var title_value = this.$('#aircraft_type').val().trim();
   			var sort_value = this.$('#sort_code').val().trim();
   	  		var base_value = this.$('#base_charge').val().trim();	
   	  		var first_value = this.$('#first_hour').val().trim();	
 	  		var hour_value = this.$('#each_hour').val().trim();	
 	     	var min_value = this.$('#min_charge').val().trim();		
   			if(title_value){
   				this.model.save({ "title": title_value, "sort_code": sort_value, "base_charge ": base_value,
   				"first_hour" : first_value, "each_hour" : hour_value, "min_charge" : min_value },
   				 {error: function(model, response) {alert(JSON.stringify(model))}});
   			}
  			this.$el.removeClass('editing');
  		},
  		deleteItem: function(){
			this.model.destroy(
			{
    			wait: true,			
    			error: function(model, response) {
    				var parsedmessage = JSON.parse(response.responseText);
  //  				 alert(JSON.stringify(parsedmessage.message));
    				},	
    			success: (function(model, response){
            		this.remove();  
    			 	}).bind(this) //  NTFS: ".bind(this)" makes the right "this" available to the callback. 
    			}	
			)
 		},
	});
	app.SignOffTypeView = app.ModelView.extend({
	    template: signofftemplate,
   		close: function(){
   			var signoff_type = this.$('#signoff_type').val().trim();
   			var period = this.$('#period').val().trim();
   	  		var authority = this.$('#authority').val().trim();	
   	  		var fixed_date = this.$('#fixed_date').val().trim();	
 	  		var no_fly = this.$('#no_fly').val().trim();	
 	     	var applytoall = this.$('#applytoall').val().trim();		
   			if(signoff_type){
   				this.model.save({ "signoff_type": signoff_type, "period": period, "authority ": authority,
   				"fixed_date" : fixed_date, "no_fly" : no_fly, "applytoall" : applytoall },
   				 {error: function(model, response) {alert(JSON.stringify(model))}});
   			}
  			this.$el.removeClass('editing');
  		},
  		deleteItem: function(){
			this.model.destroy(
			{
    			wait: true,			
    			error: function(model, response) {
    				var parsedmessage = JSON.parse(response.responseText);
  //  				 alert(JSON.stringify(parsedmessage.message));
    				},	
    			success: (function(model, response){
            		this.remove();  
    			 	}).bind(this) //  NTFS: ".bind(this)" makes the right "this" available to the callback. 
    			}	
			)
 		},
	});
	
	app.CollectionView =  Backbone.View.extend({    
      // It's the first function called when this view it's instantiated.
      
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
      	'click #add' : 'addItem'
      },
      addItem: function(e){
      	e.preventDefault();
      	var formData ={};
      	// grab all of the input fields
      	$(this.localDivTag).children('input').each(function(i, el ){
      		if($(el).val() != ''){
      			formData[el.id] = $(el).val();
      		}
      	});
      	//grab all of the <select> fields 
      	$(this.localDivTag).children('select').each(function(i, el ){
      		if($(el).val() != ''){
      			formData[el.id] = $(el).val();
      		}
      	});
//  alert(JSON.stringify(formData));
      	this.collection.create( formData, {wait: true});
      },	
	
	});
	app.AircraftsView = app.CollectionView.extend({
	 	el: '#aircrafts', 
		localDivTag: '#addAircraft Div',
	 	preinitialize(){
	 	   this.collection = new app.AircraftList();
	 	},	
        renderItem: function(item){
      		var itemView = new app.AircraftView({
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
      		var itemView = new app.TowFeeView({
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
      		var itemView = new app.AircraftTypeView({
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
      		var itemView = new app.FlightTypeView({
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
      		var itemView = new app.StatusTypeView({
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
      		var itemView = new app.SignOffTypeView({
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
   			case "sign_offs" : new app.SignOffTypesView();
   			break;
   		}
   	} else {
   	console.log("not defined");}
   });

  }) // $(function) close
})( jQuery );
