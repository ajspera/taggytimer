<?php

class TagsController extends BaseController {
	
	protected $tagAccepted = array(
		'tag_title' => 'string'
	);
	public function index()
	{
		$this->accepted = array(
			'user' => 'me'
		);
		$input = $this->input;
		$this->data = Auth::user()->tags()->orderBy('created_at', 'desc')->get();
		Tag::collectionDuration($this->data);
		$this->data = $this->data->toArray();
		
	}
	public function show($id)
	{
		$input = $this->input;
		if(strpos($id, 'title:') === 0){
			$id = substr($id, 6);
			if(strlen($id) > 2)
				$tag = Tag::getTitle($id);
			else
				$tag = null;
		} else {
			$tag = $tag->find($id);
		}
		
		if(empty($tag->exists))
			return $this->setResponseCode(404);
		$tag->duration();
		$this->data = $tag->toArray();
		
	}
	public function store()
	{
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
			$tag = Tag::getTitle($input['tag_title']);
			$tagAssoc = Auth::user()->tags()->where('id', '=', $tag->id)->get()->first();
			if(empty($tagAssoc))
				$tag->users()->attach(Auth::user()->id);
			$tag->duration();
			$this->data = $tag->toArray();
		}

	}
	public function destroy($id)
	{
		$tag = Auth::user()->tags()->where('id', '=', $id)->get()->first();
		if(!empty($tag->id)){
			$tag->users()->detach(Auth::user()->id);
		}
		return 'success';
	}
}