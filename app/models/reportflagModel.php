<?php

class reportflagModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'reportflag';
    public $timestamps = false;
    protected $fillable = array('contest_participant_id', 'report_description', 'report_userid', 'postedby_userid', 'createddate', 'action_taken', 'contest_id');
    public static $rules = array(
        'contest_participant_id' => 'required',
        'report_userid' => 'required',
        'postedby_userid' => 'required',
        'createddate' => 'required',
        'contest_id' => 'required',
            );

}
?>