<?php

class languageModel extends Eloquent {

    protected $primaryKey = 'controlID';
    protected $table = 'language_details';
    protected $created_at = 'CreatedAt';
    protected $updated_at = 'UpdatedAt';
    protected $fillable = array('ctrlCaptionId', 'EngText', 'PorText');
    public static $rules = array(
        'ctrlCaptionId' => array('required'),
        'EngText' => array('required'),
        'PorText' => array('required'),
    );
}
?>