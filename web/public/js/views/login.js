/*
|--------------------------------------------------------------------------
| Login View
|--------------------------------------------------------------------------
*/
App.Views.Login = App.BaseView.extend({
	tagName: 'div',
	el: $('div.container'),
	view: 'login',

	initialize: function() {
		this.render();
	},
	
	events: {
		'submit #loginForm': function(e){
			console.log(this);
			e.preventDefault();
			var set = {};
			$.post('/api/login',{email: $('#email').val(), password: $('#password').val()}).success( _.bind(function(data){
				App.me.loadSession();
				redir('/');
			}, this) ).fail(_.bind(function(data){
				this.$el.find('.errors').html('<li>Username or password incorrect</li>');
			}, this) );
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