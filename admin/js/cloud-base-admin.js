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
	 var feeitemtemplate = _.template(`
	 <div class="Row">
        <div class="Cell">
            <%= altitude %>
        </div>
        <div class="Cell">
            <%=  charge %>
        </div>
        <div class="Cell">
            <%=  hook_up %>
        </div>
        <div class="Cell">
            <button class="delete"></button> 
        </div>
    </div>`);
	 
//model 
	app.Towfee = Backbone.Model.extend({
		initialize: function(){
//			console.log('the model has been initialized. ');
		},
	    defaults: {
// 			id: '42',
// 			altitude: '9000',
// 			charge: '300',
// 			hookup: '-3'
			wait: true
		}		
	} );
// model view	
	app.TowFeeView = Backbone.View.extend({
		tagName: 'div',
        classname: 'TowFee',
        template: feeitemtemplate,
//		template: _.template($('#feeitemtemplate').html()),
		render: function(){
			this.$el.html( this.template(this.model.toJSON() ) );
			this.$input = this.$('.edit');
			return this;
		},
		events:{
			'click .delete' : 'deleteTowFee'
		},
		deleteTowFee: function(){
			this.model.destroy();
			this.remove();
		},
		edit: function(){
			this.$el.addClass('editing');
			this.$input.focure();
		}
	})
// collection	
        app.TowFeesList = Backbone.Collection.extend({
    	model: app.Towfee,
    	url: POST_SUBMITTER.root + 'cloud_base/v1/fees',  	
   // 	sync : function(method, model, options){
   // 		return Backbone.sync(method, this, $.extend(options, 
   // 		{beforeSend : function (xhr) {
   // 			xhr.setRequestHeader ('X-WP-NONCE', POST_SUBMITTER.nonce);
 //   			    console.log(POST_SUBMITTER.nonce);
 //   		}
  //  	  }))	
 //    	}	
    }) ; 


//    app.towfee = new app.Towfee;
//    console.log (JSON.stringify( app.towfee));

//	app.TowFees = new TowFeesList;
    
// view for the collection  . 
 	 app.TowFeesView = Backbone.View.extend({
      el: '#tow_fees',     
      // It's the first function called when this view it's instantiated.
      initialize: function( tow_fees ){
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
// 		this.$el.html(this.template(this.model.toJSON() ));
//        return this;
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
      	console.log(JSON.stringify(formData));
      	this.collection.create( formData );
      },
    
    });
  
// dummy data    
   $(function(){
/* 
  	var tow_fees = [
   		{ id: '2',  altitude: 'SRB', charge: '15', hookup: '0' },
   		{ id: '3', altitude: '1000', charge: '25', hookup: '0' },
   		{ id: '4', altitude: '2000', charge: '30', hookup: '0' }
   		];
 */
   		new app.TowFeesView();
   });
      
//     new app.TowFeesView(tow_fees);  
   
	app.towfeeform = new Backform.Form({
		el: $("#tow_fees"),
		model: app.Towfee,
		events:{
			"submit": function(e) {
				e.preventDefault();
				this.model.save()
					.done(function(result){
						alert("update Sucessful.");
				})
					.fail(function(error){
						alert(error);
				});
				return false;
			}
		}
	
	});

  }) // $(function) close
})( jQuery );
