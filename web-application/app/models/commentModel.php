<?php
class commentModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'comments';
    public $timestamps = false;
    protected $fillable = array('contest_participant_id', 'userid', 'comment', 'createddate', 'replycomment', 'replydate');
    public static $rules = array(
        'contest_participant_id' => 'required',
        'userid' => 'required',
        'comment' => 'required',
            );

}
?>