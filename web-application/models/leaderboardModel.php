<?php

class leaderboardModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'leaderboard';
    //public $timestamps = false;
    protected $fillable = array('contest_id', 'user_id', 'position', 'votes');
    public static $rules = array(
        'contest_id' => 'required',
        'user_id' => 'required',
        'position' => 'required',
        'votes' => 'required',
    );

}
?>