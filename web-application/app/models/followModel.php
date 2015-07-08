<?php

class followModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'followers';
    public $timestamps = false;
    protected $fillable = array('userid', 'followerid', 'createddate');
    public static $rules = array(
        'userid' => 'required',
        'followerid' => 'required',
        'createddate' => 'required',
            );

}
?>