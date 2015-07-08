<?php
/* This controller works crud process for category/interest   */
class categoryController extends BaseController {

    public function showcategory() {
        return View::make('admin/category');
    }

    public function activecategory() {
        $categoryid = Input::get('categoryid');
        $interestcategorydetails = InterestCategoryModel::where('Interest_id', $categoryid)->where('status', 1)->get()->count();
        if ($interestcategorydetails == 1) {
            /// Inactive /////
            $updatedetails['status'] = 0;
            $affectedRows = InterestCategoryModel::where('Interest_id', $categoryid)->update($updatedetails);
            if ($affectedRows)
                return 0;
        }else {
            ////Active///////
            $updatedetails['status'] = 1;
            $affectedRows = InterestCategoryModel::where('Interest_id', $categoryid)->update($updatedetails);
            if ($affectedRows)
                return 1;
        }
    }

    public function addcategory() {
        $categoryname = Input::get('categoryname');
        $inputdetails['Interest_name'] = $categoryname;
        $inputdetails['status'] = 1;
        $inputdetails['createddate'] = date('Y-m-d h:i:s');
        $validation = Validator::make($inputdetails, InterestCategoryModel::$webrule);
        if ($validation->passes()) {
            $savecategory = InterestCategoryModel::create($inputdetails);
            if ($savecategory) {
                $er_data = "Category saved successfully";
                return Redirect::to('category')->with('tab', 'categorylist')->with('er_data', $er_data);
            }
        } else {

            if ($validation->messages()->first('Interest_name') == 'The interest name field is required.') {
                $er_data = 'The Category name field is required.';
            } else if ($validation->messages()->first('Interest_name') == 'The interest name has already been taken.') {
                $er_data = 'The Category name has already been taken.';
            }


            return Redirect::to('category')->with('tab', 'createcategory')->with('er_data_create', $er_data);
        }
    }

    public function gotoeditcategory() {
        $categoryid = Input::get('interestid');
        return Redirect::to('category')->with('tab', 'createcategory')->with('categoryid', $categoryid);
    }

    public function editcategory($data = null) {
        $category_id = $data;
        $inputdetails['Interest_name'] = Input::get('categoryname');


        $updaterules = array(
            'Interest_name' => array('required', 'regex:/^./', 'unique:interest_category,Interest_name,' . $data . ',Interest_id'),
                );


        $validation = Validator::make($inputdetails, $updaterules);
        if ($validation->passes()) {

            $savecategory = InterestCategoryModel::where('Interest_id', $data)->update($inputdetails);
            $er_data = 'Category updated successfully';
            return Redirect::to('category')->with('er_data', $er_data);
        } else {

            if ($validation->messages()->first('Interest_name') == 'The interest name field is required.') {
                $er_data = 'The Category name field is required.';
            } else if ($validation->messages()->first('Interest_name') == 'The interest name has already been taken.') {
                $er_data = 'The Category name has already been taken.';
            }
            return Redirect::to('category')->with('tab', 'createcategory')->with('er_data_create', $er_data);
        }
    }

    public function deletecategory() {
        $categoryid = Input::get('categoryid');

        $usercategory = userinterestModel::where('interest_id', $categoryid)->get()->count();
        $contestcategory = contestinterestModel::where('category_id', $categoryid)->get()->count();
        if ($usercategory > 0 || $contestcategory > 0) {
            $er_data = 'This category is used. So not able to delete  ';
            return Redirect::to('category')->with('er_data', $er_data);
        } else {
            $deletecategory = InterestCategoryModel::where('Interest_id', $categoryid)->delete();
            if ($deletecategory) {
                $er_data = 'Category deleted successfully';
                return Redirect::to('category')->with('er_data', $er_data);
                ;
            }
        }
    }

}
?>