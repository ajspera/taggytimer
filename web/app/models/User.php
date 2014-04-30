<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';
	
	protected $guarded = array(
		'email',
		'email_token',
		'password',
		'verified',
		'id'
	);
	
	protected $fillable = array(
		'first_name',
		'last_name',
		'professional_title',
		'postal_code',
		'phone_office',
		'phone_cell',
		'alert_sms',
		'alert_email'
	);

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');
	
	public function tags()
	{
		return $this->belongsToMany('Tag', 'tags_users')->withTimestamps();
	}
	
	public function timers()
	{
		return $this->hasMany('Timer');
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
	
	public static function signupEmail($email)
	{
		$user = User::where('email','=',$email)->get();
		if(@$user[0]){
			if($user[0]->verified)
				return false;
			$user = $user[0];
		} else {
			$user = new User;
			$user->email = $email;
		}
		$secretToken = Hash::make(rand().sha1(rand().time()));
		$user->email_token = Hash::make($secretToken);
		$user->save();
		return $secretToken;
	}
	
}