<?php

class NotesController extends BaseController {
	
	public function show($id)
	{
		$note = Note::with(array('messages'))->find($id);
		
		if(empty($note->exists))
			return $this->setResponseCode(404);
		
		$job = new Job;
		$job->id = $note->job_id;
		if(!$job->authorized)
			return $this->setResponseCode(401);
		
		$this->data = $note->toArray();
	}
	
	public function store()
	{
		$this->accepted = array(
			'note_title' => 'string*',
			'job_id' => 'job id*',
			'red_flag' => 'boolean',
			'message_text' => 'string'
		);
		
		$rules = array(
			'note_title' => array('required', 'min:2'),
			'job_id' => array('required', 'job_authed'),
			'red_flag'=> array('numeric','min:0','max:1')
		);
		
		$input = $this->input;
		
		$validator = Validator::make($input, $rules);
		
		if($validator->fails()){
			
			$this->invalidation($validator);
			
		} else {
			
			$note = new Note;
			$note->note_title = $input['note_title'];
			if($input['red_flag'])
				$note->red_flag = $input['red_flag'];
			$note->creator_id = Auth::user()->id;
			$job = Job::find($input['job_id']);
			$note->job_id = $job->id;
			$note->save();
			if(!empty($input['message_text'])){
				$message = new NoteMessage;
				$message->note_id = $note->id;
				$message->creator_id = $note->creator_id;
				$message->message_text = $input['message_text'];
				$message->red_flag = $input['red_flag'];
				$message->save();
			}
			$this->data = $note->toArray();
		}

	}
	public function update($id)
	{
		$this->accepted = array(
			'red_flag' => 'boolean'
		);
		$input = $this->input;
		
		$rules = array(
			'red_flag'=> array('numeric','min:0','max:1')
		);
		
		$note = Note::find($id);
		
		if(empty($note->exists))
			return $this->setResponseCode(404);
		
		$job = Job::find($note->job_id);
		
		if(!$job->authorized)
			return $this->setResponseCode(401);
		
		$validator = Validator::make($input, $rules);
	
		if($validator->fails()){
			
			$this->invalidation($validator);
			
		} else {
			if(isset($input['red_flag'])){
				$note->red_flag = $input['red_flag'];
				$note->save();
			}
			
			$this->data = $note->toArray();
		}
	
	}

}