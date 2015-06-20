<?php

class webpanelController extends BaseController {

    public function showpanel() {
        $inputs = Input::all();
        return View::make('webpanel/webpanel')->with('inputs', $inputs);
    }

}

?>