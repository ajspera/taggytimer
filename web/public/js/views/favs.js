/*
|--------------------------------------------------------------------------
| Favs View
|--------------------------------------------------------------------------
*/
App.Views.Favs = App.Views.Timers.extend({
	tagName: 'div',
	el: $('div.container'),
	view: 'favs',
	model: new App.Models.Tag,
	collection: new App.Collections.Tags,

	initialize: function() {
		this.clockInterval = window.setInterval(_.bind(this.renderClocks, this), 1000);
		
		this.listenTo(this.collection,'add',this.addClock);
		this.listenTo(this.collection,'sort',this.render);
		this.listenTo(this.collection,'remove',this.removeClock);
		
		$(window).resize(_.bind(this.dynamicRow, this));
	},
	events: {
		'submit #tagsForm': function(e){
			e.preventDefault();
			var set = {
				tag_title : $('#tag_title').val()
			};
			this.model = new App.Models.Tag;
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
		},
		'click .start': function(e){
			e.preventDefault();
			id = $(e.currentTarget).attr('rel');
			var tag = this.collection.get(id);
			var timer = new App.Models.Timer();
			var set = {
				tag_title : tag.attributes.tag_title
			};
			//tag.attributes.running = true;
			pass = timer.save(set, {
				success: _.bind(function(model){
					tag.attributes = model.attributes;
				}, this ),
				error: _.bind(function(model, xhr){
				}, tag)
			});
			$(e.currentTarget).hide();
			$(e.currentTarget).parent().find('.stop').show();
			return false;
		},
		'click .stop' : function(e){
			e.preventDefault();
			id = $(e.currentTarget).attr('rel');
			var tag = this.collection.get(id);
			var timer = new App.Models.Timer();
			timer.id = tag.id;
			timer.destroy();
			$(e.currentTarget).hide();
			$(e.currentTarget).parent().find('.start').show();
			tag.attributes.running = false;
			return false;
		}
	},
	addClock: function(m) {
		var html = template('fav', m.toJSON());
		html = $($.parseHTML(html));
		html.hide();
		this.$el.find('.timerList').prepend(html);
		this.renderClocks();
		this.$el.find('.timerList .timer'+m.id).slideDown();
	},
	removeClock: function(m) {
		$('.timer'+m.id).slideUp(500, function(){
			this.remove();
		});
	},
});