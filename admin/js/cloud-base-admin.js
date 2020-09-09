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
    	
// model view	
	app.TowFeeView = Backbone.View.extend({
		tagName: 'div',
        className: 'Row view',
        template: feeitemtemplate,
		render: function(){
			this.$el.html( this.template(this.model.toJSON() ) );
			this.$input = this.$('.edit');
			return this;
		},
		initialize: function(){
    		this.model.on('change', this.render, this);
  		},
		events:{
			'click .delete' : 'deleteTowFee',
			'dblclick label' : 'edit',
			'keypress .edit' : 'updateOnEnter',
			'blur .edit' : 'close'
		},
		deleteTowFee: function(){
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
   		close: function(){
   			var tow_fee = this.$('#tow_fee').val().trim();
  			var base_charge = this.$('#hook_up').val().trim();
   			var hourly_fee = this.$('#hourly').val().trim();  
			
    		if(tow_fee || base_charge || hourly_fee) {
     			this.model.save({"charge": tow_fee, "hook_up": base_charge, "hourly": hourly_fee });
    		}
           this.$el.removeClass('editing');
  		},
	});
// model view
	app.FlightTypeView = Backbone.View.extend({
		tagName: 'div',
        className: 'Row view',
        template: flighttypetemplate,
		render: function(){
			this.$el.html( this.template(this.model.toJSON() ) );
			this.$input = this.$('.edit');
			return this;
		},
		initialize: function(){
    		this.model.on('change', this.render, this);
  		},
		events:{
			'click .delete' : 'deleteFlightType',
			'dblclick label' : 'edit',
			'keypress .edit' : 'updateOnEnter',
			'blur .edit' : 'close'
		},
		deleteFlightType: function(){
			this.model.destroy(
			{
    			wait: true,			
    			error: function(model, response) {
    				var parsedmessage = JSON.parse(response.responseText);
    				alert(parsedmessage.data.message);
    				var parsedmessage = JSON.parse(response.responseText);
    				},	
    			success: (function(model, response){
            		this.remove();  
    			 	}).bind(this) //  NTFS: ".bind(this)" makes the right "this" available to the callback. 
    			}	
			)
 		},
		edit: function(){
			this.$el.addClass('editing');
      		this.$input.focus();
		},
		updateOnEnter: function(e){
    		if(e.which == 13){
      			this.close();
    		}
   		},
   		close: function(){
   			var title_value = this.$('#flight_type').val().trim();
   			if(title_value){
   				this.model.save({ "title": title_value }, {error: function(model, response) {alert(JSON.stringify(response))}});
   			}
  			this.$el.removeClass('editing');
  		},
	});	


	app.AircraftTypeView = Backbone.View.extend({
		tagName: 'div',
        className: 'Row view',
        template: actypetemplate,
		render: function(){
			this.$el.html( this.template(this.model.toJSON() ) );
			this.$input = this.$('.edit');
			return this;
		},
		initialize: function(){
    		this.model.on('change', this.render, this);
  		},
		events:{
			'click .delete' : 'deleteAircraftType',
			'dblclick label' : 'edit',
			'keypress .edit' : 'updateOnEnter',
			'blur .edit' : 'close'
		},
		deleteAircraftType: function(){
			this.model.destroy(
			{
    			wait: true,			
    			error: function(model, response) {
    				var parsedmessage = JSON.parse(response.responseText);
    				alert(parsedmessage.data.message);
    				var parsedmessage = JSON.parse(response.responseText);
    				},	
    			success: (function(model, response){
            		this.remove();  
    			 	}).bind(this) //  NTFS: ".bind(this)" makes the right "this" available to the callback. 
    			}	
			)
 		},
		edit: function(){
			this.$el.addClass('editing');
      		this.$input.focus();
		},
		updateOnEnter: function(e){
    		if(e.which == 13){
      			this.close();
    		}
   		},
   		close: function(){
   			var title_value = this.$('#aircraft_type').val().trim();
   			if(title_value){
   				this.model.save({ "title": title_value }, {error: function(model, response) {alert(JSON.stringify(model))}});
   			}
  			this.$el.removeClass('editing');
  		},
	});	
	    
// view for the collection  . 
 	 app.TowFeesView = Backbone.View.extend({
      el: '#tow_fees',     
      // It's the first function called when this view it's instantiated.
      initialize: function(){
//      	console.log('the view has been initialized. ');
        this.collection = new app.TowFeesList();
        this.collection.fetch({reset:true});
        this.render();
        this.listenTo(this.collection, 'add', this.renderTowFee);
        this.listenTo(this.collection, 'reset', this.render);
      },
      render: function(){
      	this.collection.each(function(item){
  			this.renderTowFee(item);    	
      	}, this );
      },	
      renderTowFee: function(item){
      	var towfeeview = new app.TowFeeView({
      	  model: item
      	})
      	this.$el.append( towfeeview.render().el);   
      },
      events:{
      	'click #add' : 'addTowFee'
      },
      addTowFee: function(e){
      	e.preventDefault();
      	var formData ={};
      	$('#addTowFee div').children('input').each(function(i, el ){
      		if($(el).val() != ''){
      			formData[el.id] = $(el).val();
      		}
      	});
      	this.collection.create( formData, {wait: true});
      },
    });

 	 app.AircraftTypesView = Backbone.View.extend({
      el: '#aircraft_types',     
      // It's the first function called when this view it's instantiated.
      initialize: function(){
//      	console.log('the view has been initialized. ');
        this.collection = new app.AircraftTypeList();
        this.collection.fetch({reset:true});
        this.render();
        this.listenTo(this.collection, 'add', this.renderAircraftTypes);
        this.listenTo(this.collection, 'reset', this.render);
      },
      render: function(){
      	this.collection.each(function(item){
  			this.renderAircraftTypes(item);    	
      	}, this );
      },	
      renderAircraftTypes: function(item){
      	var aircrafttypeview = new app.AircraftTypeView({
      	  model: item
      	})
      	this.$el.append( aircrafttypeview.render().el);   
      },
      events:{
      	'click #add' :  'addType'
      },    
      addType: function(e){
      	e.preventDefault();      	
      	var formData ={};
      	$('#aircraft_type div').children('input').each(function(i, el ){
      		if($(el).val() != ''){
      			formData[el.id] = $(el).val();		
      		}
      	});
//      	 console.log(JSON.stringify(formData));
       	this.collection.create( formData, {wait: true});
      }
    });  
    
 	 app.FlightTypesView = Backbone.View.extend({
      el: '#flight_types',     
      // It's the first function called when this view it's instantiated.
      initialize: function(){
//      	console.log('the view has been initialized. ');
        this.collection = new app.FlightTypeList();
        this.collection.fetch({reset:true});
        this.render();
        this.listenTo(this.collection, 'add', this.renderFlightTypes);
        this.listenTo(this.collection, 'reset', this.render);
      },
      render: function(){
      	this.collection.each(function(item){
  			this.renderFlightTypes(item);    	
      	}, this );
      },	
      renderFlightTypes: function(item){
      	var flighttypeview= new app.FlightTypeView({
      	  model: item
      	})
      	this.$el.append( flighttypeview.render().el);   
      },
      events:{
      	'click #add' :  'addType'
      },    
      addType: function(e){
      	e.preventDefault();      	
      	var formData ={};
      	$('#addflight_type div').children('input').each(function(i, el ){
      		if($(el).val() != ''){
      			formData[el.id] = $(el).val();		
      		}
      	});
//      	 console.log(JSON.stringify(formData));
       	this.collection.create( formData, {wait: true});
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

   		}
   	} else {
   	console.log("not defined");}
   });
      
//     new app.TowFeesView(tow_fees);  
   
// 	app.towfeeform = new Backform.Form({
// 		el: $("#tow_fees"),
// 		model: app.Towfee,
// 		events:{
// 			"submit": function(e) {
// 				e.preventDefault();
// 				this.model.save()
// 					.done(function(result){
// 						alert("update Sucessful.");
// 				})
// 					.fail(function(error){
// 						alert(error);
// 				});
// 				return false;
// 			}
// 		}
// 	
// 	});

  }) // $(function) close
})( jQuery );
