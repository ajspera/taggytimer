/*
|--------------------------------------------------------------------------
| Signup View
|--------------------------------------------------------------------------
*/
App.Views.Signup = App.BaseView.extend({
	tagName: 'div',
	el: $('div.container'),
	view: 'signup',

	initialize: function() {
		this.render();
	},
	
	events: {
		'submit #signupForm': function(e){
			e.preventDefault();
			this.model.loaded = false;
			var set = {};
			set.email = $('#email').val();
			set.first_name = $('#first_name').val();
			set.last_name = $('#last_name').val();
			set.password = $('#password').val();
			set.repeat_password = $('#repeat_password').val();
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
					redir('/');
				}, this)
				
			}, { parse: true })
			return false;
		}
	},
	render: function() {
		if(App.me.loggedIn){
			redir('/');
			return;
		}
		this.$el.html( this.template( this.model.toJSON() ) );
	}
});