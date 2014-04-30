/*
|--------------------------------------------------------------------------
| App Wrapper View
|--------------------------------------------------------------------------
*/
App.Views.Wrapper = App.BaseView.extend({
	el: $('#timerApp'),
	
	mainView: null,
	
	initialize: function() {
		App.accountNav = new App.Views.AccountNav({ model: App.me });
		App.mainNav = new App.Views.MainNav({ model: App.me });
		this.model.on('sessionLoad', this.render, this);
		$(_.bind(function() {
		    //$(window).focus(this.refresh);
		}, this) );

		this.model.loadSession();
	},

	events: {
		'click .rboxHead a': function(){
			this.refresh();
		}
	},
	refresh: function(){
		App.me.fetch();
	},
	changePage: function(view){
		//console.log(view);
		//console.log(this.mainView);
/* 		console.log('change page'); */
		if(this.mainView){
/* 			this.mainView.remove(); */
			this.mainView.unbind();
			this.mainView.stopListening();
			this.mainView.undelegateEvents();
		}
		
		this.mainView = view;
		this.render();
	},
	render: function() {
		if(App.me.loaded){
			if(!App.me.loggedIn && !(window.location.pathname == '/signup' || window.location.pathname == '/login')){
				redir('/login');
			}
			
			App.accountNav.render();
			App.mainNav.render();
			if(this.mainView){
/* 				console.log('main render'); */
				this.mainView.render();
			}
		}
	}
});