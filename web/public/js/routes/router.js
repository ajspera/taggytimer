App.Router = Backbone.Router.extend({
	routes: {
		'': 'timer',
		'login': 'login',
		'signup': 'signup',
		'tags': 'tags',
		'favs': 'favs',
		'logs': 'logs',
		'account': 'account',
		'tag/:tagTitle': 'tag'
	},
	timer: function() {
		App.ActiveNav = 'home';
		App.wrapper.changePage( new App.Views.Timers({collection: App.me.timers}) );
		App.me.timers.fetch();
	},
	favs: function() {
		App.ActiveNav = 'favs';
		App.wrapper.changePage( new App.Views.Favs({collection: App.me.tags}) );
		App.me.tags.fetch();
	},
	tags: function() {
		App.ActiveNav = 'tags';
		App.wrapper.changePage( new App.Views.Tags({collection: App.me.tags}) );
		App.me.tags.fetch();
	},
	tag: function(tagTitle) {
		console.log(tagTitle)
		console.log('sad');
		App.ActiveNav = 'tags';
		var model = new App.Models.Tag( {tag_title: tagTitle} );
		App.wrapper.changePage(  new App.Views.Tag({model: model}) );
		App.wrapper.mainView.model.fetch();
	},
	logs: function() {
		App.ActiveNav = 'logs';
	},
	login: function() {
		App.ActiveNav = 'login';
		App.wrapper.changePage( new App.Views.Login({model: App.me}) );
	},
	signup: function() {
		App.ActiveNav = 'signup';
		App.wrapper.changePage( new App.Views.Signup({model: App.me}) );
	},
	account: function() {
		App.ActiveNav = 'account';
		App.wrapper.changePage( new App.Views.Account({model: App.me}) );
	}
});