<?php

class privateusercontestModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'private_contest_users';
    public $timestamps = false;
    protected $fillable = array('user_id', 'contest_id', 'requesteddate', 'status');
    public static $rules = array(
        'user_id' => 'required',
        'contest_id' => 'required',
    );

}
?>