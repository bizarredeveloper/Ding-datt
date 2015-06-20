<?php

class contestparticipantModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'contestparticipant';
    //protected $created_at = 'CreatedAt';
    //protected $updated_at = 'UpdatedAt';
    public $timestamps = false;
    protected $fillable = array('contest_id', 'user_id', 'uploadfile', 'uploaddate', 'uploadtopic', 'dropbox_path', 'topicphoto', 'topicvideo');
    public static $rules = array(
        'contest_id' => 'required',
        'user_id' => 'required',
        'uploadfile' => 'required',
            );
    public static $topicrules = array(
        'contest_id' => 'required',
        'user_id' => 'required',
        'uploadtopic' => 'required',
        'uploaddate' => 'required',
            );

}
?>