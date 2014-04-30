/*
|--------------------------------------------------------------------------
| Tags View
|--------------------------------------------------------------------------
*/
App.Views.Tags = App.BaseView.extend({
	tagName: 'div',
	el: $('div.container'),
	view: 'tags',
	model: new App.Models.Tag,
	collection: new App.Collections.Tags,

	initialize: function() {
		this.collection = App.me.tags;
		this.collection.on('all', this.render, this);
	},
	events: {
		'submit #tagsForm': function(e){
			e.preventDefault();
			var set = {
				tag_title : $('#tag_title').val()
			};
			this.model.clear();
			pass = this.model.save(set, {
				success: _.bind(function(model){
					this.collection.add(model);
					this.model = new App.Models.Tag;
					$('#tag_title').val('');
				}, this ),
				error: _.bind(function(model, xhr){
					var errors = JSON.parse(xhr.responseText).message;
					this.errors(errors);
				}, this)
			});
			if(!pass){
				this.errors(this.model.validationError);
			}
			return false;
		},
		'click .removeTag' : function(e){
			e.preventDefault();
			id = $(e.currentTarget).attr('rel');
			this.collection.get(id).destroy();
			return false;
		}
	},
	render: function(m) {
		var data = {
			tags: this.collection.toJSON()
		}
		this.$el.html( this.template( data ) );
		//$('#tag_title').filter(':visible').focus();
	}
});