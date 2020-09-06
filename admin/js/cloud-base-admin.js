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
	app.AircraftType = Backbone.Model.extend({
		initialize: function(){
		},
	    defaults: {
			wait: true
		}		
	} );
	
	app.Towfee = Backbone.Model.extend({
		initialize: function(){
		},
	    defaults: {
			wait: true
		}		
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
    	
// model view	
	app.TowFeeView = Backbone.View.extend({
		tagName: 'div',
        className: 'TowFee',
        template: feeitemtemplate,
//		template: _.template($('#feeitemtemplate').html()),
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
			'click .edit' : 'edit',
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
    		var value = this.input.val().trim();
    		if(value) {
    		alert(value);
 //     			this.model.save({title: value});
    		}
    		this.$el.removeClass('editing');
  		},
	});
	
// model view	
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
			this.model.destroy();
			this.remove();
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
//      	console.log('formdata');
      	$('#addTowFee div').children('input').each(function(i, el ){
      		if($(el).val() != ''){
      			formData[el.id] = $(el).val();
      		}
      	});
//      	console.log(JSON.stringify(formData));
      	this.collection.create( formData );
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
      	 console.log(JSON.stringify(formData));
       	this.collection.create( formData );
      }
    });  
     
   $(function(){
   if (typeof cb_admin_tab !== 'undefined' ){
   		switch(cb_admin_tab){
   			case "tow_fee" : new app.TowFeesView();
   			break;
   			case "aircraft_types" : new app.AircraftTypesView();
   			break;
   		}
   		   	console.log(cb_admin_tab);
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
