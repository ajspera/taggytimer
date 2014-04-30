<?php

class PageController extends BaseController {

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	 var $mainNav = array(
	 	'/' => 'Timer',
	 	'/jobs' => 'Jobs',
	 	'/logs' => 'My Log',
	 );
	 var $accountNav = array(
	 	'/login' => 'Login',
	 	'/signup' => 'Sign Up'
	 );
	 var $fullPage = true;
	 var $respData = array(
	 
	 );
	 var $vData = array(
	 
	 );
	 var $JS = array(
	 	 'jquery.js',
	 	 'underscore.js',
	 	 'backbone.js',
	 	 'app.js'
	 );
	 var $CSS = array(
	 	'style.css'
	 );
	 var $layout = 'common';
	 
	 public $api = false;
	 
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}
	public function __construct()
	{
		$backboneJS = array();
		$this->backboneViews = array();
		$dirs = array(
			'models',
			'collections',
			'views',
			'routes'
		);
		foreach($dirs as $dir){
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./js/'.$dir)) as $filename)
			{
				$file = str_replace('./js/','',$filename->__toString());
				if($filename->isFile() && !in_array($file, $this->JS))
					if(strpos($file, '_.html') == false)
						$backboneJS[] =  $file;
					else
						$this->backboneViews[] = $filename->__toString();
			}
		}
		$this->JS = array_merge($this->JS, $backboneJS);
		View::composer('common',function($view){
			$view->nest('accountNavV','inc.nav.account')->nest('mainNavV','inc.nav.main');
			
			//temp for reference
			$view->nest('temp','sub');
			$view->with('bbViews', $this->backboneViews);
			
		});
		
	}
	public function index()
	{
		$this->vData['JS'] = $this->JS;
		$this->vData['CSS'] = $this->CSS;
		$this->vData['mainNav'] = $this->mainNav;
		$this->vData['accountNav'] = $this->accountNav;
		View::share('vData', $this->vData);
		View::share('respData', $this->respData);
	}

}