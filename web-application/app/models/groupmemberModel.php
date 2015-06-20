<?php

class groupmemberModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'group_members';
    public $timestamps = false;
    protected $fillable = array('group_id', 'user_id', 'createddate', 'updateddate');
    public static $rules = array(
        'group_id' => 'required',
        'user_id' => 'required'
            );

}
?>