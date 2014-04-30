App.Models.Tag = App.BaseModel.extend({
	urlRoot: '/api/tags',
	query: {},
/*
	url: function(){
		var id = this.id ? this.id : 'title:' + this.attributes.tag_title;
		var q = this.query.length > 0 ? '?' + $.param(this.query) : '';
		return this.urlRoot + '/' + id + q;
	},
*/
	id: null,
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