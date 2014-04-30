<?php

class UsersController extends BaseController {
	
	
	protected $userAccepted = array(
	    'first_name' => 'string',
	    'last_name' => 'string',
	    'phone_office' => 'string',
	    'phone_cell' => 'string',
	    'professional_title' => 'string',
	    'postal_code' => 'string'
	);
	protected $userRules = array(
		'email' => array('email','unique:users'),
	    'first_name' => array('min:2'),
	    'last_name' => array('min:2'),
	    'phone_office' => array('regex:/^\(?([2-9][0-9][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/'),
	    'phone_cell' => array('regex:/^\(?([2-9][0-9][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/')
    );
	
	public function index()
	{
		$this->data = User::all()->toArray();
	}
	
	public function show($id)
	{
		if($id == 'me') {
			$this->data = Auth::user()->toArray();
		} else {
			$user = User::find($id);
			if(!empty($user->exists))
				$this->data = $user->toArray();
			else
				$this->setResponseCode(404);
		}
	}
	
	public function login()
	{
		return $this->show('me');
	}
	
	public function logout()
	{
		Auth::logout();
		$this->data = array();
	}
	
	public function apiSignup()
	{
		$this->accepted = array(
			'email' => 'string*'
		);
		
		$rules = array('email' => array('email','required','unique:users'),
		    'first_name' => array('min:2'),
		    'last_name' => array('min:2'),
		    'phone_office' => array('regex:/^\(?([2-9][0-9][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/'),
		    'phone_cell' => array('regex:/^\(?([2-9][0-9][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/'));
		$rules['password'] = array('required','min:8');
		$rules['repeat_password'] = array('required', 'same:password');
		$input = $this->input;
		$validator = Validator::make($input, $rules);
	
		if($validator->fails()){
			
			$this->invalidation($validator);
			$this->v = $validator;
						
		} else {
			$user = new User;
			$user->password = Hash::make($input['password']);
			$user->email = $input['email'];
			$user->first_name = $input['first_name'];
			$user->last_name = $input['last_name'];
			$user->save();
			
			$creds = array(
				'email' => $input['email'],
				'password' => $input['password']
			);
			Auth::attempt($creds, true, true);
			
			$this->data = Auth::user()->toArray();
		}
		
	}
	public function getSignup()
	{
		$input = array_merge(Input::old(),$this->input);
		$this->api = false;
		$data = array();
		$body = 'default';
		$data['values'] = $input;
		
		if(isset($input['email']) && isset($input['token'])){
			$user = User::where('email','=',$input['email'])->first();
			if($user->exists && !empty($user->email_token)){
				if(Hash::check($input['token'], $user->email_token)){
					$body = 'profile';
				} else {
					$data['badToken'] = true;
					$data['values']['email_start'] = $input['email'];
				}
			}
		}
		
		if(isset($_GET['success']))
			$body = 'success';
		if(isset($_GET['check_mail']))
			$body = 'check_mail';

		$data['body'] = $body;
		return View::make('signup')->with($data);
	}
	public function postSignup()
	{
		$this->api = false;
		$input = $this->input;
		print 'asdasdasdasda';
		
		if(isset($_POST['email_start'])){
			$this->input['email'] = $_POST['email_start'];
			$this->apiSignup();
			if($this->status == 'fail')
				return Redirect::to('/signup')->withErrors($this->v)->withInput();
			else
				return Redirect::to('/signup?check_mail=1')->withInput();
		}
		if(!isset($input['email']) || !isset($input['token']))
			return Redirect::to('/signup');
		
		$user = User::where('email','=',$input['email'])->first();
		
		if(!$user->exists || empty($user->email_token) || !Hash::check($input['token'], $user->email_token))
			return Redirect::to('/signup');
	
		if($validator->fails()){
			
			return Redirect::to('/signup')->withErrors($validator)->withInput();
						
		} else {
			
			if(!isset($input['email']) || !isset($input['token']))
				return Redirect::to('/signup');
			
			$user->password = Hash::make($input['password']);
			$user->verified = 1;
			$user->email_token = null;
			$user->fill($input);
			$user->save();
			//redirect to thank you page
			return Redirect::to('/signup?success=1')->withInput();
		}

	}
	public function update($id)
	{
		$this->accepted = $this->userAccepted;
		$this->accepted['first_name'] = 'string';
		$this->accepted['last_name'] = 'string';
		$input = $this->input;
		
		if($id == 'me') {
			$id = Auth::user()->id;
		} else {
			if($id != Auth::user()->id)
				return $this->setResponseCode(401);
		}
		$rules = $this->userRules;
		unset($rules['email']);
		$validator = Validator::make($input, $rules);
	
		if($validator->fails()){
			
			$this->invalidation($validator);
			
		} else {
			$user = User::find($id);
			$user->fill($input);
			$user->save();
			$this->data = $user->toArray();
		}
	
	}

}