<?php

class contestModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'contest';
    public $timestamps = false;
    protected $fillable = array('contest_name', 'description', 'themephoto', 'noofparticipant', 'conteststartdate', 'contestenddate', 'votingstartdate', 'votingenddate', 'contesttype', 'createdby', 'visibility', 'status', 'contestcode', 'sponsor', 'sponsorphoto', 'sponsorname', 'prize', 'createddate', 'createddate');
    public static $rules = array(
        'contest_name' => 'required|unique:contest',
        'themephoto' => 'required',
        'conteststartdate' => 'required',
        'contestenddate' => 'required',
        'votingstartdate' => 'required',
        'votingenddate' => 'required',
        'noofparticipant' => 'required',
        'contesttype' => 'required',
            );

}
?>