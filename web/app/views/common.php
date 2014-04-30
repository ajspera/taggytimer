<!DOCTYPE HTML>
<html>
<head>
	<title>TaggyTimer</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<? foreach($vData['CSS'] as $CSS): ?>
	<link rel="stylesheet" type="text/css" href="/css/<?= $CSS ?>" />
	<? endforeach; ?>
	<link rel="icon" type="image/png" href="/images/favicon.png" />
	<link rel="apple-touch-icon" href="/images/mobileIcon.png" />
	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<!--[if lt IE 9]><body class="PIE"><![endif]-->
<!--[if gte IE 9]><!-->
<body onload="setTimeout(function() { window.scrollTo(0, 1) }, 100);"><!--<![endif]-->
	<div id="timerApp">
		<section class="rboxHead">
			<header>
				<a class="logo" href="/"><img src="/images/stopwatch.png" alt="Timer" /></a>
				<nav class="account">
					<ul class="right">
						<? // $accountNavV ?>
					</ul>

				</nav>
				<nav class="mainNav">
					<ul class="left">
						<? // $mainNavV; ?>
					</ul>
				</nav>
				<div class="clr"></div>
			</header>
		</section>
		
		<div class="container">
			
		</div>
	</div>
	
	<? foreach($vData['JS'] as $JS): ?>
	<script type="text/javascript" src="/js/<?= $JS ?>"></script>
	<? endforeach; ?>
	
	<? foreach($bbViews as $v): 
		$id = explode( '/',$v);
		$id = explode( '.', end($id) );
		$id = rtrim( $id[0], '_' );
	?>
		<script type="text/template" id="<?= $id ?>">
		<?= file_get_contents($v) ?>
		</script>
	<? endforeach; ?>
	<script>
	$(function(){
		App.me = new App.Models.User;
		App.wrapper = new App.Views.Wrapper({model: App.me});
		router = new App.Router;
		redir = function(href){
		    router.navigate(href, {trigger: true});
		}
		Backbone.history.start({ pushState: true, root: App.root });

		
		// All navigation that is relative should be passed through the navigate
		// method, to be processed by the router. If the link has a `data-bypass`
		// attribute, bypass the delegation completely.
		$(document).on("click", "a[href]:not([data-bypass])", function(evt) {
		  // Get the absolute anchor href.
		  var href = { prop: $(this).prop("href"), attr: $(this).attr("href") };
		  // Get the absolute root.
		  var root = location.protocol + "//" + location.host + App.root;
		
		  // Ensure the root is part of the anchor href, meaning it's relative.
		  if (href.prop.slice(0, root.length) === root) {
		    // Stop the default event to ensure the link will not cause a page
		    // refresh.
		    evt.preventDefault();
			
		    // `Backbone.history.navigate` is sufficient for all Routers and will
		    // trigger the correct events. The Router's internal `navigate` method
		    // calls this anyways.  The fragment is sliced from the root.
		
		    redir(href.attr);
		    
		  }
		});
	});

	</script>	
</body>
</html>