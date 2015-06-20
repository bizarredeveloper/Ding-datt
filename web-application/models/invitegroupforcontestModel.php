<?php

class invitegroupforcontestModel extends Eloquent {

    protected $primaryKey = 'id';
    protected $table = 'invitegroupforcontest';
    public $timestamps = false;
    protected $fillable = array('contest_id', 'group_id', 'invitedetail', 'inviteddate', 'user_id');
    public static $rules = array(
        'contest_id' => 'required',
        'group_id' => 'required',
        'invitedetail' => 'required',
        'user_id' => 'required',
            );

}
?>