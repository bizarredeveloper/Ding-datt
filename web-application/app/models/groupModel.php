<?php

class groupModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'group';
    public $timestamps = false;
    protected $fillable = array('groupname', 'grouptype', 'createdby', 'createddate', 'updateddate', 'groupimage', 'user_id', 'status');
    public static $rules = array(
        'groupname' => 'required|unique:group',
        'createdby' => 'required',
        'grouptype' => 'required',
            );

}
?>