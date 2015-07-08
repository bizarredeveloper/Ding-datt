<?php

class contestinterestModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'contest_interest_categories';
    public $timestamps = false;
    protected $fillable = array('contest_id', 'category_id');
    public static $rules = array(
        'contest_id' => 'required',
        'category_id' => 'required',
    );
}