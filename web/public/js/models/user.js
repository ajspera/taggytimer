App.Models.User = App.BaseModel.extend({
	urlRoot: '/api/users',
	loggedIn: false,
	loaded: false,
	id: 'me',
	defaults: {
		email: null,
		password: null,
		first_name: null,
		last_name: null
	},
	
	tags: {},
	
	timers: {},
	
	constructor : function ( attributes, options ) {
		App.BaseModel.apply( this, arguments );
		this.tags = new App.Collections.Tags;
		this.tags.url += '?user=me';
		this.timers = new App.Collections.Timers;
	},
	loadSession: function() {
		this.loaded = false;
		this.id = 'me';
		this.fetch({
			success: _.bind(function(){
				this.loggedIn = true;
				this.loaded = true;
				this.trigger('sessionLoad');
			},this),
			error: _.bind(function() {
				this.loaded = true;
				this.trigger('sessionLoad');
				this.id = null;
				this.clear();
			},this)
		});
	},
	login: function(email, password, success, fail) {

	},
	logout: function() {
	
	},
	_validate: function(attrs) {
		return true;
	}
});