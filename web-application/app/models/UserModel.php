<?php
class UserModel extends Eloquent
{
    protected $primaryKey = 'AutoID';
    protected $table = 'users';
    protected $guarded = array('VehicleCode');
    protected $fillable = array('VehicleCode', 'NumberSeats', 'VehicleNumber', 'MaximumAllowed', 'City', 'State', 'Phone', 'Insurance', 'VehicleType', 'Address', 'Tax', 'InsurancePhoto', 'RCPhoto');
    
    public $timestamps = false;
    
    public function setInsurancePhotoAttribute($InsurancePhoto)
    {
        if($InsurancePhoto)
        {    
        $this->attributes['InsurancePhoto'] = Input::get('VehicleCode') . '-InsurancePhoto.' . Input::file('InsurancePhoto')->getClientOriginalExtension();
        Input::file('InsurancePhoto')->move('assets/uploads/vehicle/', Input::get('VehicleCode') . '-InsurancePhoto.' . Input::file('InsurancePhoto')->getClientOriginalExtension());
        }
    }
    
    public function setRCPhotoAttribute($RCPhoto)
    {
        if($RCPhoto)
        {            
        $this->attributes['RCPhoto'] = Input::get('VehicleCode') . '-RCPhoto.' . Input::file('RCPhoto')->getClientOriginalExtension();
        Input::file('RCPhoto')->move('assets/uploads/vehicle/', Input::get('VehicleCode') . '-RCPhoto.' . Input::file('RCPhoto')->getClientOriginalExtension());
        }
    }
    
    public static $rules = array(
        'VehicleNumber' =>  array('required', 'unique:vehicle','regex:/^[\w.]+$/'),
        'NumberSeats' => 'required|integer', 
        'VehicleType' => 'required', 
        'MaximumAllowed' => 'integer|max:100',
        'VehicleCode' => 'required|unique:vehicle',
        'InsurancePhoto' => 'image|mimes:jpg,png,gif|max:5000',
        'RCPhoto' => 'image|mimes:jpg,png,gif|max:5000',
        );
}