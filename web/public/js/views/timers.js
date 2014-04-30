/*
|--------------------------------------------------------------------------
| Timers View
|--------------------------------------------------------------------------
*/
App.Views.Timers = App.BaseView.extend({
	timerName: 'div',
	el: $('div.container'),
	view: 'timers',
	model: new App.Models.Timer,
	collection: new App.Collections.Timers,
	clockInterval: null,
	
	initialize: function() {
		this.clockInterval = window.setInterval(_.bind(this.renderClocks, this), 1000);
		//this.collection.on(['add','sort','sync','remove'], this.render, this);
		this.listenTo(this.collection,'add',this.addClock);
		this.listenTo(this.collection,'sort',this.render);
		this.listenTo(this.collection,'remove',this.removeClock);
		console.log(this.model);
		
		$(window).resize(_.bind(this.dynamicRow, this));
	},
	unbind: function(name, callback, context) {
		if(this.clockInterval){
			clearInterval(this.clockInterval);
			this.clockInterval = null;
		}
		return App.BaseView.prototype.unbind.call(this, name, callback, context);
	},
	events: {
		'submit #timersForm': function(e){
			e.preventDefault();
			var set = {
				tag_title : $('#tag_title').val()
			};
			this.model = new App.Models.Timer;
			console.log(this.model.id);
			pass = this.model.save(set, {
				success: _.bind(function(model){
				    console.log(model.id);
					this.collection.add(model, {sort:false});
					this.model = new App.Models.Timer;
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
		'click .stop' : function(e){
			e.preventDefault();
			id = $(e.currentTarget).attr('rel');
			console.log(this.collection)
			console.log(this.collection.get(id))
			console.log(id)
			this.collection.get(id).destroy();
			return false;
		}
	},
	dynamicRow: function(dropSize) {
		_.each(this.collection.toJSON(), function(obj, k){
			if(!$('.timer'+obj.id+' .clock').hasClass('dropClock') || !dropSize){
				var height = $('.timer'+obj.id).innerHeight();
			} else {
				var clone = $('.timer'+obj.id).clone();
				clone.find('.clock').removeClass('dropClock');
				clone.appendTo('.timerList');
				var height = clone.innerHeight();
				clone.remove();
			}
			if(height > 90){
				$('.timer'+obj.id+' .clock').addClass('dropClock');
			} else {
				$('.timer'+obj.id+' .clock').removeClass('dropClock');
			}
		});
	},
	renderClocks: function() {
		var time = Math.round(+new Date()/1000);
		_.each(this.collection.toJSON(), function(obj, k){
			
			offset = time - obj.retrievedAt;
			var duration = obj.duration;
			if(obj.running)
				duration += offset;
			if(obj.running || $('.timer'+obj.id+' .clock').html() == ''){
				var sec = duration % 60;
				var min = parseInt(duration / 60) % 60;
				var hour = parseInt(duration / (60 * 60)) % 24;
				var day = parseInt(duration / (60 * 60 * 24));
				var neatTime = '';
				var sep = ' ';
				neatTime += day > 0 ? day + '<span class="scale">d</span>' + sep : '';
				neatTime += (hour < 10 ? '0' : '') + hour + '<span class="scale">h</span>' + sep;
				neatTime += (min < 10 ? '0' : '') + min + '<span class="scale">m</span>' + sep;
				neatTime += (sec < 10 ? '0' : '') + sec + '<span class="scale">s</span>';
				$('.timer'+obj.id+' .clock').html(neatTime);
			}
		});
		this.dynamicRow();
	},
	addClock: function(m) {
		var html = template('timer', m.toJSON());
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
	render: function(m) {
		var data = {
			timers: this.collection.toJSON()
		}
		this.$el.html( this.template( data ) );
		this.renderClocks();
//		$('#tag_title').filter(':visible').focus();
	}
});