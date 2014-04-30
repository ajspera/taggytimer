/*
|--------------------------------------------------------------------------
| Main Navigation View
|--------------------------------------------------------------------------
*/
App.Views.MainNav = App.BaseView.extend({
	tagName: 'ul',
	el: $('nav.mainNav ul'),
	currentPage: null,
	view: 'mainNav',

	initialize: function() {
		this.model.on('change', this.render, this);
		this.render();
	},
	
	events: {
	},
	render: function() {
		json = this.model.toJSON();
		json.loggedIn = this.model.loggedIn;
		this.$el.html( this.template( json ) );
		$('.mainNav .link' + App.ActiveNav).addClass('active');
	}
});