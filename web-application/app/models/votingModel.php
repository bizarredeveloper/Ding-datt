<?php

class votingModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'voting';
    public $timestamps = false;
    protected $fillable = array('ID', 'contest_participant_id', 'user_id', 'vote', 'votingdate');
    public static $rules = array(
        'contest_participant_id' => 'required',
        'user_id' => 'required',
        'vote' => 'required',
        'votingdate' => 'required',
            );

}
?>