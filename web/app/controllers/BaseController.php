<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	public $input = array();
	public $data = array();
	public $response_code = 200;
	public $status = 'success';
	public $error = array();
	public $api = true;
	public $accepted = array();
	
	public function __construct()
	{
		$json = Input::json()->all();
		$regInput = Input::all();
		
		if(!empty($regInput))
			$this->input = array_merge($this->input, $regInput);
		if(!empty($json))
			$this->input = array_merge($this->input, Input::json()->all());
		
		
		Validator::extend('job_authed', function($attribute, $value, $parameters)
		{
			$d = Job::find($value);
			if(empty($d->exists))
				return false;
			if(@$parameters[0] != 'admin')
				return true;
			if($d->admin)
				return true;
			return false;
		});
		
	}
	protected function invalidation($validator)
	{
		if(is_array($validator))
			$this->data = $validator;
		else
			$this->data = $validator->messages()->all();
		$this->setResponseCode(400);
		$this->status = 'fail';
	}
	protected function setResponseCode($code)
	{
		switch($code){
			case 400:
				$this->error = 'input validation error';
				$this->status = 'fail';
				break;
			case 404:
				$this->error = 'resource does not exist';
				$this->status = 'fail';
				break;
			case 401:
				$this->error = 'unauthorized request';
				$this->status = 'fail';
				break;
			default:
				
		}
		$this->response_code = $code;
	}
	protected function unauthorized()
	{
		$this->setResponseCode(401);
	}
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
	
	protected function formatApiResponse()
	{
		$resp = array(
			'response_code' => $this->response_code,
			'status' => $this->status,
			'message' => $this->data
		);
		if(!empty($this->error))
			$resp['error'] = $this->error;
		
		if(isset($_GET['docs']))
			$resp['accepted'] = $this->accepted;
		return Response::json($resp,$this->response_code);
	}
    public function callAction($method, $parameters)
    {
    	$response = parent::callAction($method, $parameters);
    	
    	if($this->api)
    		$response = $this->formatApiResponse();

        return $response;
    }
    protected function withClean($model,$accepted)
	{
		if(isset($this->input['with'])){
			$this->input['with'] = explode(',',$this->input['with']);
			$withAccepted = explode(',',$this->accepted['with']);
			$with = array();
			foreach($this->input['with'] as $w){
				if(in_array($w,$withAccepted))
					$with[] = $w;
			}
			$model = $model->with($with);
		}
		
		return $model;
	}

}