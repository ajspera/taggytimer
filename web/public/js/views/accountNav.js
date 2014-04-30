/*
|--------------------------------------------------------------------------
| Account Navigation View
|--------------------------------------------------------------------------
*/
App.Views.AccountNav = App.BaseView.extend({
	tagName: 'ul',
	el: $('nav.account ul'),
	currentPage: null,
	view: 'accountNav',

	initialize: function() {
		this.model.on('change', this.render, this);
		this.model.on('sessionLoad', this.render, this );
	},
	
	events: {
		'click .logout': function(e){
			e.preventDefault();
			$.get('/api/logout').success(function(){
				location.reload();
			});
		}
	},
	render: function() {
		json = this.model.toJSON();
		json.loggedIn = this.model.loggedIn;
		this.$el.html( this.template( json ) );
		$('nav.account .link' + App.ActiveNav).addClass('active');
	}
});