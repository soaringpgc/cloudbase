(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
	var app = app || {};
 
	app.Model = Backbone.Model.extend({
	// over ride the sync function to include the Wordpress nonce. 
	// going to need this for everything so do it once.  
	  	sync: function( method, model, options ){
    		return Backbone.sync(method, this, jQuery.extend( options, {
      			beforeSend: function (xhr) {
//      			alert(POST_SUBMITTER.nonce);
        		xhr.setRequestHeader( 'X-WP-NONCE', POST_SUBMITTER.nonce );
      			},
   			} ));	
   		},	
	});
	app.Pilots = app.Model.extend({
//		url: POST_SUBMITTER.root + 'cloud_base/v1/pilots',
		preinitialize(){	     
	 	},		
		wait: true
	});
	app.Aircraft = app.Model.extend({
//		url: POST_SUBMITTER.root + 'cloud_base/v1/aircraft',
		preinitialize(){	     
	 	},		
		wait: true
	});

	app.Flight = app.Model.extend({
		initialize: function(){
		},	
		wait: true
	});

// collections	
    app.Collection = Backbone.Collection.extend({	
    	sync: function( method, model, options ){
    		return Backbone.sync(method, this, jQuery.extend( options, {
      			beforeSend: function (xhr) {
 //     			alert(POST_SUBMITTER.nonce);
        		xhr.setRequestHeader( 'X-WP-NONCE', POST_SUBMITTER.nonce );
      			},
   			} ));	
   		},	
   	 }) ; 

    app.FlightList= app.Collection.extend({
    	model: app.Flight,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/flights',  
   	 }) ; 
   	 app.AircraftList= app.Collection.extend({
    	model: app.Aircraft,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/aircraft',  
   	 }) ; 	 	
    app.PilotList= app.Collection.extend({
    	model: app.Pilots,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/pilots?role=subscriber'
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
			'dblclick label' : 'update',
			'click .launch' : 'launch_time',
			'click .landing' : 'landing_time',
		},
   		update: function(){
			var localmodel = this.model;
 			$("div.editform").addClass('editing'); 			
             // NTFS this requires the form id's to be the same as the model id's.
             // we are looping over the form, picking up the id's and then getting the 
             // value of the same id in the model and then loading it back into the form
             //  someone (probably me) is going to hate me in the future.  -dsj
            $(this.localDivTag).children('input').each(function(i, el ){
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
		},  
		launch_time: function(){
			var today = new  Date();
			var time = today.getHours()+'-'+(today.getMonth()+1); 
		
		},
		landing_time: function(){
		
		}
	});
	app.FlightView = app.ModelView.extend({
	        template: flighttemplate,     
	});	
// 		
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
      	this.collection.create( formData, {wait: true});
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
//      	alert(JSON.stringify(formData));
      	var updateModel = this.collection.get(formData.id);
        updateModel.save(formData, {wait: true});
// clean out the form:
      		$(this.localDivTag).children('input').each(function(i, el ){
				$('#'+el.id).val('');
      		});       
      		$(this.localDivTag).children('select').each(function(i, el ){
				$('#'+el.id).val('');
      		});       
		$("div.editform").removeClass('editing');	
      	}
	});
	app.FlightsView = app.CollectionView.extend({
	 	el: '#flights', 
		localDivTag: '#addFlight Div',
	 	preinitialize(){

	 	},	
	    initialize: function(){
	      var self = this;
//      	console.log('the view has been initialized. ');
	 	  this.aircraft = new app.AircraftList();
	 	  this.pilots = new app.PilotList();
	 	  this.collection = new app.FlightList();
//NTFS : we fetch the aircraft, if that is successful, we fetch pilots, and if that is successful 
// fetch the flights. It took forever to figure this out. 
		  this.aircraft.fetch({reset:true, success:function(){
    		  self.pilots.fetch({reset:true, success:function( ){
    		  	  self.collection.fetch({reset:true });    
    		  }});		  
		  }})	
          this.listenTo(this.pilots, 'reset', this.render);                
//          this.render();
          this.listenTo(this.collection, 'add', this.renderItem);
          this.listenTo(this.collection, 'reset', this.render);
        },
        render: function(){
      	  this.collection.each(function(item){
//			var pmodel = this.aircraft.findWhere({aircraft_id: item.get('aircraft_id')}).get("compitition_id");			
//             alert     (JSON.stringify (pmodel));
// NTFS Here we are basically doing a SQL join we are copying elements from the aircraft and pilots models into the
// flight model so it can be displayed for basic human consumption. These values are for display only so 
// we add the silent so they are not sent back to the server, (where they will be ignored anyway. )
   		  item.set({"p_first_name" : this.pilots.findWhere({pilot_id: parseInt(item.get('pilot_id'), 10) }).get("first_name")}, {silent: true }); 
   		  item.set({"p_last_name" :this.pilots.findWhere({pilot_id: parseInt(item.get('pilot_id'), 10) }).get("last_name")}, {silent: true } ); 
   		  item.set({"glider" :this.aircraft.findWhere({aircraft_id: item.get('aircraft_id')}).get("compitition_id")}, {silent: true }); 
   		  item.set({"towplane" : this.aircraft.findWhere({aircraft_id: item.get('tow_plane_id')}).get("registration")}, {silent: true }); 
   		
  		  this.renderItem(item);    	
      	}, this );
      },
        renderItem: function(item){      
            var expandedView = app.FlightView.extend({ localDivTag:this.localDivTag });
            var itemView = new expandedView({
      	  		model: item
      		})
      		this.$el.append( itemView.render().el);   
        }
	}); 
	app.EditView = app.CollectionView.extend({
	 	el: '#eflights', 
		localDivTag: '#editflights div',
 	 	preinitialize(){
 	 	   this.collection = new app.FlightList();
 	 	},	
         renderItem: function(item){
//             var expandedView = app.FlightView.extend({ localDivTag:this.localDivTag });
//             var itemView = new expandedView({
//       	  		model: item
//       		})
//       		this.$el.append( itemView.render().el);   
         }
	}); 

 
   $(function(){
//     var today = new Date();
// 	var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
// 	var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
// 	var dateTime = date+' '+time; 	
//    	$( "<h4>"+date+"</h4>" ).insertAfter( $(".datetime") );

   
   if (typeof cb_admin_tab !== 'undefined' ){
   		switch(cb_admin_tab){
   			case "flights" : 
   				new app.FlightsView();
   				new app.EditView();
   			break;
   		}
   	} else {
 
   	console.log("not defined");}
   });



})( jQuery );
