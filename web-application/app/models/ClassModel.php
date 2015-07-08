<?php
class ClassModel extends Eloquent
{
    
    protected $primaryKey = 'AutoID';
    protected $created_at = 'CreatedAt';
    protected $updated_at = 'UpdatedAt';
    protected $table = 'class';
    protected $guarded = array('GradeName');
    protected $fillable = array('GradeName','importfile');
	
	public function batch(){
        return $this->hasMany('BatchModel', 'Class');
    }
	
	public function studentadmissionresult(){
        return $this->hasMany('StudentAdmissionModel', 'StudentCourse');
    }
    
    public $timestamps = true;
    
	
    
    public static $rules = array(
        'GradeName' =>  array('required', 'unique:class','regex:/^./'),       
                             );
	 public static $updaterules = array(
        'GradeName' =>  array('required','regex:/^./'),        
                             );	
       public static $importrules = array(
        'importfile'=>  'required|mimes:xlsx',	
			
        );							 
    
}