<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait,
        RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID';
    protected $table = 'user';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');
    //protected $created_at = 'CreatedAt';
    //protected $updated_at = 'UpdatedAt';
    public $timestamps = false;
    protected $fillable = array('username', 'email', 'password', 'firstname', 'lastname', 'status', 'dateofbirth', 'profilepicture', 'facebookpage', 'twitterpage', 'instagrampage', 'hometown', 'school', 'occupation', 'maritalstatus', 'noofkids', 'favoriteholidayspot', 'mobile', 'gender', 'createddate', 'facebook_id', 'google_id', 'device_id', 'gcm_id', 'timezone', 'device_type','dateformat');

    public function setpasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }

    public static $loginrule = array(
        'username' => 'required',
        'password' => 'required'
    );
    public static $rules = array(
        'email' => array('required_if_attribute:email,==,', 'unique:users', 'regex:/^./'),
        'username' => array('required_if_attribute:email,==,', 'unique:users', 'regex:/^./'),
        'password' => 'required'
    );
    public static $registerrules = array(
        'firstname' => 'required',
        'lastname' => 'required',
        'username' => 'required',
        'email' => 'required|email',
        'mobile' => 'required|digits:10|unique:user',
        'password' => 'required|min:5|confirmed'
    );
    public static $loginrules = array(
        'emai' => 'required',
        'password' => 'required',
        'usertype' => 'required'
    );
    public static $rulespwd = array('OldPassword' => 'required|pwdvalidation',
        'NewPassword' => 'required|confirmed|alphaNum|min:4|max:10',
        'NewPassword_confirmation' => 'required',
    );

}
?>