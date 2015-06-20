<?php

class AllocationController extends BaseController
{
    
    public function AllocationLayout()
    {
        return View::make('allocation/allocation');
    }
    
    public function AllocationProcess()
    {
        
        $VehicleData = Input::all();
        $validation  = Validator::make($VehicleData, VehicleModel::$rules);
        
        if ($validation->passes()) 
        {
            VehicleModel::create($VehicleData);
            return Redirect::to('allocation')->withErrors('Vehicle Details Saved Succesfully');
        } else 
        {
            return Redirect::to('allocation')->withInput()->withErrors($validation->messages());
        }
    }
}