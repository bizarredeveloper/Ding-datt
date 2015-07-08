<?php

class invitefollowerforcontestModel extends Eloquent {

    protected $primaryKey = 'id';
    protected $table = 'invitefollowerforcontest';
    public $timestamps = false;
    protected $fillable = array('contest_id', 'follower_id', 'invitedate');
    ///follower_id ---> primary key of the Follow table id

    public static $rules = array(
        'contest_id' => 'required',
        'follower_id' => 'required',
            );

}
?>