<?php

class ProfileModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'user';
    public $timestamps = false;
    protected $fillable = array('username', 'email', 'password', 'firstname', 'lastname', 'status', 'dateofbirth', 'profilepicture', 'facebookpage', 'twitterpage', 'instagrampage', 'hometown', 'school', 'occupation', 'maritalstatus', 'noofkids', 'favoriteholidayspot', 'mobile', 'gender', 'createddate', 'facebook_id', 'google_id', 'timezone', 'device_id', 'gcm_id', 'device_type','dateformat');
    public static $webrule = array(
        'username' => 'required|unique:user',
        'email' => 'required|email|unique:user',
        'password' => 'required|min:5',
        'dateofbirth' => 'required',
        'terms' => 'required'
            );
    public static $loginrule = array(
        'username' => 'required',
        'password' => 'required'
    );
    public static $rules = array(
        'username' => 'required|unique:user',
        'email' => 'required|email|unique:user',
        'password' => 'required|min:5',
            );
    public static $socialrules = array(
        'firstname' => 'required',
        'lastname' => 'required',
        'email' => 'required|email',
            );

}

?>