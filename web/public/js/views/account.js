/*
|--------------------------------------------------------------------------
| Account View
|--------------------------------------------------------------------------
*/
App.Views.Account = App.BaseView.extend({
	tagName: 'div',
	el: $('div.container'),
	view: 'account',

	initialize: function() {
	},
	events: {
		'submit #accountForm': function(e){
			e.preventDefault();
			var set = {};
			set.first_name = $('#first_name').val();
			set.last_name = $('#last_name').val();
			//console.log(this.model.url());
			//console.log(this.model.save(set));
			this.model.save(set,{
				error: _.bind(function(model, xhr, options) {
					if(xhr.status == 400){
						var errors = JSON.parse(xhr.responseText);
						errors = errors.message;
						var errorsHTML = '';
						_.each(errors, function(e){
							errorsHTML += '<li>' + e + '</li>';
						});
						this.$el.find('.errors').html(errorsHTML);
						console.log(errors);
					}
				}, this),
				
				success: _.bind(function(model, xhr, options) {
					App.me.loggedIn = true;
					App.me.loaded = true;
					this.render();
					$('.saved').pulse();
				}, this)
				
			}, { parse: true })
			return false;
		}
	},
	render: function() {
		this.$el.html( this.template( this.model.toJSON() ) );
	}
});