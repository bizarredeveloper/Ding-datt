<?php

class InterestCategoryModel extends Eloquent {

    protected $primaryKey = 'Interest_id';
    protected $table = 'interest_category';
    public $timestamps = false;
    protected $fillable = array('Interest_name', 'status');
    public static $webrule = array(
        'Interest_name' => 'required|unique:interest_category',
            );

}
?>