<?php
class imageModel extends Eloquent
{
	protected $primaryKey = 'ID';
	protected $table = 'img';
	protected $created_at = 'CreatedAt';
    protected $updated_at = 'UpdatedAt';
    protected $fillable = array('imgname','name');
    
    public static $rules = array(
                    'imgname'       => 'required',
					'name' =>'required'					
                	) ;
		 	}
?>


