(function() {
	window.App = {
		Models: {},
		Collections: {},
		Views: {},
		Router: {},
		ActiveNav: '',
		root: '/'
	};

	window.vent = _.extend({}, Backbone.Events);


	window.App.BaseView = Backbone.View.extend({
		
		template: function(data)
		{
			return template( this.view, data );
		},
		errors : function(errs){
			var list = '';
			$(errs).each( _.bind(function(k,txt){
				list += '<li>' + txt + '</li>';
			}, this) );
			$(this.el).find('.errors').html(list);
		}
	});
	window.App.BaseCollection = Backbone.Collection.extend({
		parse: function(resp) {
			var data = resp.message;
			
			var time = Math.round(+new Date()/1000);
			_.each(data, function(obj,k){
				data[k]['retrievedAt'] = time;
			});

			return data;
		}
	});
	window.App.BaseModel = Backbone.Model.extend({
		parse: function(resp) {
			var time = Math.round(+new Date()/1000);
			if(typeof(resp.message) == 'object'){
				var data = resp.message;
				data.retrievedAt = time;
			} else {
				var data = resp;
			}
			return data;
		}
	});
	
	jQuery.fn.extend({
		pulse: function() {
			return this.each(function() {
				$(this).fadeIn(500, function(){
					$(this).delay(200).fadeOut(500);
				});
			});
		}
	});
	$(document).ready(function(){
		$(window).bind('keyup',function(e){
			if(e.keyCode == 65 && typeof(e.target.form) == 'undefined'){
				$('input[type="text"]').focus();
			}
		})
	});
	
})();

var template = function(view, data)
{
	return _.template( $('#' + view).html(), data );
}