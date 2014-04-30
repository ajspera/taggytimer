<?php

class TimersController extends BaseController {
	
	protected $tagAccepted = array(
		'tag_title' => 'string'
	);
	public function index()
	{
		$userId = Auth::user()->id;
		$timer = Timer::where('user_id', '=', $userId)
			->orderBy('start', 'desc')
			->first();
		
		if($timer){
			$timer = $timer->tags()->get();
			Tag::collectionDuration($timer);
			$this->data = $timer->toArray();
//			$this->data = $this->data['tags'];
		} else {
			$this->data = array();
		}
		
	}
	public function show($id)
	{
		$timer = Tag::find($id);
		$queries = DB::getQueryLog();
		$last_query = end($queries);
		//print_r($last_query);
		if(empty($timer->exists))
			return $this->setResponseCode(404);
		$timer->duration();
		$this->data = $timer->toArray();

		
	}
	public function store()
	{
		/*===========================

		Start a timer for a tag
		
		=============================*/
		$this->accepted = $this->tagAccepted;
		$this->accepted['tag_title'] .= '*';

		$input = $this->input;
		$rules = array(
		    'tag_title' => array('required', 'min:2'),
	    );
		
		$validator = Validator::make($input, $rules);
	
		if($validator->fails()){
			
			$this->invalidation($validator);
			
						
		} else {
			$tags = array($input['tag_title']);
			$timer = Timer::addTags($tags);
			$timer = Tag::find($timer->id);
			$tagAssoc = Auth::user()->tags()->where('id', '=', $timer->id)->get()->first();
			if(empty($tagAssoc))
				$timer->users()->attach(Auth::user()->id);
			$timer->duration();
			$this->data = $timer->toArray();
		}

	}
	public function destroy($id)
	{
		$tags = array($id);
		$timer = Timer::removeTags($tags);
		return 'success';
	}
}