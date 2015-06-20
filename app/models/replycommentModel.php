<?php

class replycommentModel extends Eloquent {

    protected $primaryKey = 'id';
    protected $table = 'replycomment';
    public $timestamps = false;
    protected $fillable = array('comment_id', 'replycomment', 'createddate', 'user_id');
    public static $rules = array(
        'comment_id' => 'required',
        'replycomment' => 'required',
        'user_id' => 'required'
            );

}
?>