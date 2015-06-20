<?php

class userinterestModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'user_interest';
    public $timestamps = false;
    protected $fillable = array('user_id', 'interest_id');
    public static $rules = array(
        'user_id' => 'required',
        'interest_id' => 'required',
    );

}
?>