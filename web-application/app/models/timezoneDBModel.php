<?php

class timezoneDBModel extends Eloquent {

    protected $primaryKey = 'id';
    protected $table = 'timezone';
    public $timestamps = false;
    protected $fillable = array('timezonename', 'timezonevalue');
    public static $rules = array(
        'timezonename' => 'required',
        'timezonevalue' => 'required',
            );

}
?>