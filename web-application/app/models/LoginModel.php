<?php
//class ClassModel extends Eloquent
class LoginModel extends Eloquent
{
    
    protected $primaryKey = 'AutoID';
    protected $created_at = 'CreatedAt';
    protected $updated_at = 'UpdatedAt';
    protected $table = 'class';
    protected $guarded = array('ClassName');
    protected $fillable = array('ClassName', 'ClassSection', 'ClassCode');
    
    public $timestamps = true;
    public static $rules = array(
        'ClassName' =>  array('required', 'unique:class','regex:/^./'),
        'ClassSection' => 'required',
        'ClassCode' => array('required', 'unique:class')
                             );
    
}