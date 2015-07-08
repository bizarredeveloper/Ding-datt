<?php

class invitememberforgroupModel extends Eloquent {

    protected $primaryKey = 'id';
    protected $table = 'invitememberforgroup';
    public $timestamps = false;
    protected $fillable = array('group_id', 'invitetype', 'user_id', 'inviteddate');
    public static $rules = array(
        'group_id' => 'required',
        'invitetype' => 'required',
        'user_id' => 'required',
            );

}
?>