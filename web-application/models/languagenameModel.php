<?php

class languagenameModel extends Eloquent {

    protected $primaryKey = 'ID';
    protected $table = 'language_name';
    protected $created_at = 'CreatedAt';
    protected $updated_at = 'UpdatedAt';
    protected $fillable = array('ID', 'language_key', 'language_name');
    public static $rules = array(
        'ID' => array('required'),
        'language_key' => array('required'),
        'language_name' => array('required'),
    );

}
?>