App.Models.Timer = App.BaseModel.extend({
	urlRoot: '/api/timers',
	defaults: {
		tag_title: null
	},
	validate: function(attrs) {
		var errors = [];
		if ( ! attrs.tag_title ) {
			errors.push('Write something');
		}
		if(errors.length > 0)
			return errors;
	}
});