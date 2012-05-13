<?php

require_once('ApplicationController.php');

class AccountController extends ApplicationController
{

    public function init() {
        //menu location
        $this->view->store_section = true;
    }

    public function indexAction() {
        $this->view->seo_title = "My Account";
        $this->view->meta_desc = "Account area";

        $loggedin = $this->_loggedUser;
        if ($loggedin) {

            $data = $this->_loggedUser->getDescription();
            $this->view->name = $data['Name']['value'];
            $this->view->email = $this->_loggedUser->user_email;
            $this->view->user_id = $this->_loggedUser->user_id;

            //break date of birth into chunks and assign to variables
            if($data['DOB']['value']) {
                $dobbits = explode("/", $data['DOB']['value']);
                $this->view->dob_d = $dobbits[0];
                $this->view->dob_m = $dobbits[1];
                $this->view->dob_y = $dobbits[2];
            } else {
                $this->view->dob_d = '';
                $this->view->dob_m = '';
                $this->view->dob_y = '';
            }

            $this->view->user_data = $data;
            
            $this->view->update = $this->_request->getParam('update', 0);
            
            // Get list of stores
            $this->view->stores = $this->_loggedUser->getOwnedStores($this->_loggedUser->user_id);
            
            //Get list of countries
            $this->view->countries = $this->_loggedUser->getAllUserCountries();
            
            $this->view->is_user_FB = $this->_loggedUser->isFBUser();
        } else {
            $this->_redirect("/login");
        }        
    }

    public function registerAction() {

        $request = $this->getRequest();
        $form = new Form_Registration();

        $users_session = new Zend_Session_Namespace('users_session');

        if ($this->getRequest()->isPost()) {

            $email = $this->_request->getParam('email');
            $passwordclean = $this->_request->getParam('password');
            $fname = $this->_request->getParam('fname');
            $lname = $this->_request->getParam('lname');
            $dob_d = $this->_request->getParam('dob_d');
            $dob_m = $this->_request->getParam('dob_m');
            $dob_y = $this->_request->getParam('dob_y');
            $gender = $this->_request->getParam('gender');
            $country = $this->_request->getParam('country');
            



            $dob = $dob_d . "/" . $dob_m . "/" . $dob_y;
            $fname = trim($fname);
            $lname = trim($lname);
            $country = trim($country);

            //$name = explode(' ', $fname);
            //$name = $name[0];
            //$name .= "_" . $lname[0];
            
            //make sure email is valid

            $validator = new Zend_Validate_EmailAddress();

            if (!$validator->isValid($email)) {
                // email is invalid, redirect and don't continue
                return $this->_redirect('/login/invalidemail');
                die;
            }

            $user_model = new Model_User();
            $user = $user_model->getUserByEmail($email);
            if ($user != null) {
                // email is invalid, redirect and don't continue
                $this->_redirect('/login/emailinuse');
                die;
            }


            $model = new Model_User();



            //AKSIMET SPAM CHECKING!! If it fails, we mark as spam
            $spam = 0;

            try {
                //have to use a matching url and key

                $akismet = new Model_Akismet("http://" . $_SERVER["HTTP_HOST"], "8561120db9e5");

                if ($akismet->isKeyValid()) {
                    $akismet->setCommentAuthor($fname);
                    $akismet->setCommentAuthorEmail($email);
                    $akismet->setCommentAuthorURL("");
                    $akismet->setCommentContent("registration");

                    //Does AKSIMET think it's spam or not?
                    if ($akismet->isCommentSpam()) {
                        $spam = 1;
                    }
                }
            } catch (Exception $e) {
                
            }

            //END AKSIMET SPAM CHECKING!!
            //Create user
            $newUser = $model->createUser($email, $passwordclean);
            //creating the object to save the fields
            $user_fields_model = new Model_UserField();
            $fields = array();
            $fields[1] = $fname;
            $fields[2] = $lname;
            $fields[3] = $dob;
            $fields[15] = $gender;
            $fields[16] = $country;
            
            
            //saving the new fields in teh database
            $user_fields_model->saveUserFieldValues($newUser, $fields);
            
                
            //if it's spam.. don't log them in, just redirect them
            if ($spam == 1) {

                return $this->_helper->redirector('awaiting');
            }



            //we just registered (setting session for wall)
            $users_session->justRegistered = true;

            //Mail rego link to user

            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

            $email_send = new Model_Email();
            $email_send->sendEmailLoginRegisterUser($newUser, $passwordclean, $email);

            //login the user 
            $auth = new Model_Auth();
            //should always be valid becuase it was just created
            $auth->validateUser($email, $passwordclean);

            //TODO redirect to page user was on
            $this->_helper->redirector->gotoUrl('/account/index/new/1');
        }
        $this->_helper->redirector->gotoUrl('/login');
    }

    public function removeProfileImageAction() {
        $model_user = new Model_User();

        if ($model_user->getCurrentUser()) {
            $model_user->removeProfileImage($model_user->user_id);
        }
        die;
    }

    public function saveAction() {
        $model_user = new Model_User();
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();

            $model_user->getCurrentUser();
            //reseting password if both passwords are the same
            $pass1 = $request->getParam('user_pass', "");
            $pass2 = $request->getParam('user_pass2', "");
            if(sizeof($pass1) > 0 && $pass1 != ''){
                if($pass1 != $pass2){
                    $this->_redirect("/account/index/update/2");
                    return;
                }
            }
            
            if ($pass1 == $pass2 && sizeof($pass1) > 0 && $pass1 != '') {
                //reseting the password

                $model_user->user_password = md5($pass2);
            }
            //getting the fileds of the user
            $fname = $request->getParam("name", "");
            $lname = $request->getParam("lname", "");
            $bio = $request->getParam("bio", "");



            $gender = $this->_request->getParam('gender');
            $country = $this->_request->getParam('country');
            
            $dob_d = $this->_request->getParam('dob_d');
            $dob_m = $this->_request->getParam('dob_m');
            $dob_y = $this->_request->getParam('dob_y');
            $dob = $dob_d . "/" . $dob_m . "/" . $dob_y;

            //creating the array with the infromation of the fields of the user
            $fields = array("1" => $fname, "2" => $lname, "16" => $country, "15" => $gender, "3" => $dob,"5" => $bio,);

            $user_data = new Model_UserField();
            //saving new user field values
            $user_data->saveUserFieldValues($model_user->user_id, $fields);


            //Saving the image of the user


            $image = $_FILES['profile_image'];
            if($image != null){
                if ($image['error'] == UPLOAD_ERR_OK) {
                    $tmp_name = $image["tmp_name"];
                    $name = $image["name"];
                    
                    $_targetDir = getcwd() . "/images/profile/" . $model_user->user_id;
                    if (!is_dir($_targetDir)) {
                        mkdir($_targetDir);
                    }
                    $_targetDir .= "/images/";
                    if (!is_dir($_targetDir)) {
                        mkdir($_targetDir);
                    }
                    
                    move_uploaded_file($tmp_name, $_targetDir . $name);
                    
                    //saving the default image of the user
                    $image_model =  new Model_ElementImage();
                    $image_model->deleteImage($model_user->user_id, Model_ElementImage::$IMAGE_TYPE_PROFILE);
                    $image_model->saveImage($_targetDir . $name, Model_ElementImage::$IMAGE_TYPE_PROFILE , $model_user->user_id);
                }
            }
            $this->_redirect("/account/index/update/1");
        }
        $this->_redirect("/account");
    }
    
    public function forgotPasswordAction(){

        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $user_model = new Model_User();
        $global_model = new Model_Globals();

        $result = array();
        $email = $this->_request->getParam('email_forgot',"");
        
        $result['text'] = "<p>The email entered is invalid</p>";
        if($email != ''){
            $user = $user_model->getUserByEmail($email);
            if($user != null){
                //generating the new password
                $user_model->loadUser($user['user_id']);
                $pass = $global_model->genRandomString(10);
                $user_model->user_password =  md5($pass);

                //$this->sendEmailForgotPasword($email, $pass);
                $model_email = new Model_Email();
                $model_email->sendEmailForgotPasword($email,$pass);

                $result['text'] = "<p class='register-success'>Your password has been sent out.<br/>Please check your email.</p>";
            } else {
                $result['text'] = "<p>There in no user with this email account.</p>";
            }
        } 
        $this->_helper->json->sendJson($result);
    }
    
    public function manageAction() {
        $user = new Model_User();
        $loggedin = $user->getCurrentUser();
        if ($loggedin) {

            $data = $user->getDescription();
            $this->view->name = $data['Name']['value'];
            $this->view->email = $user->user_email;
            $this->view->user_id = $user->user_id;
            $this->view->user_data = $data;
            
            if($user->user_role == 5)
                $this->view->is_admin = true;
            else
                $this->view->is_admin = false;
            
            $store_seo_url = $this->_request->getParam('store', 0);
            $model_store = new Model_Store();
            
            $store = $model_store->getStoreFromUrl($store_seo_url);
            $this->view->store = $store;
            
            $this->view->products = $model_store->getStoreProducts($store['store_id'], 0, false);
            
            $model_ap = new Model_Affiliate();
            $ap_info = $model_ap->getAffiliateInfo($store['store_id']);
            
            $this->view->editStore = $model_store->checkStoreOwnerEdit($store['store_id'], $user->user_id);
           
            if($ap_info) {   
                $secure = new Model_Encrypt();
                $ap_info['credit_card_number'] = $secure->Decrypt($ap_info['credit_card_number']);
    //            $last = substr($cc_info['credit_card_number'], -4, 4);
    //            $lenght = strlen($cc_info['credit_card_number']);
    //            $cc_info['credit_card_number'] = str_repeat("*", $lenght - 4) . $last;
                $this->view->ap_info = $ap_info;
                $this->view->ap_active = $ap_info['active'];

                $this->view->verified = $model_ap->checkAffiliateTrack($store['store_id']);

                $this->view->code = 'OT-'.base64_encode($store['store_id'].'-'.$ap_info['affiliate_program_id']);

                $this->view->sales_report = $model_ap->getCompleteSalesReport($ap_info['affiliate_program_id']);
            } else {
                $this->view->ap_active = false;
                $this->view->code = false;
            }
            
            $model_global = new Model_Globals();
            $this->view->years = $model_global->getFollowingYears(10);
            $this->view->months = $model_global->getMonths();
            
            $this->view->seo_title = "Manage ".$store['store_name'];
            $this->view->meta_desc = "Manage ".$store['store_name'];
            
            $this->view->store_name = $store['store_name'];
            $this->view->store_seo_url = $store['store_seo_url'];
            
            $this->view->update = $this->_request->getParam('update', 0);
        } else {
            $this->_redirect("/login");
        }        
    }

    public function saveStoreAction() {
        $user = new Model_User();
        $logged = $user->getCurrentUser();
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();

            $store_id = $request->getParam("store_id", "");
            
            $model_store = new Model_Store();
            if($model_store->checkStoreOwnerEdit($store_id, $user->user_id)) {
                $data = array("store_id" => $store_id,
                    "store_name" => $request->getParam("store_name", ""),
                    "store_description" => $this->stringnl2br ( $request->getParam("store_description", "") ),
                    "store_domain" => $request->getParam("store_domain", ""));
                
                $model_store->saveStoreDetails($data);
                
                $store_seo_url = $request->getParam("store_seo_url", "");
                
                $image = $_FILES['store_image'];
                if($image != null){
                    if ($image['error'] == UPLOAD_ERR_OK) {
                        $image_model =  new Model_ElementImage();
                        $image_model->deleteImage($store_id, Model_ElementImage::$IMAGE_TYPE_STORE);
                        $tmp_name = $image["tmp_name"];
                        $name = $image["name"];
                        move_uploaded_file($tmp_name, getcwd() . "/images/store/" . $store_id . "/images/" . $name);
                        //saving the default image of the user
                        $url = $image_model->saveImage(getcwd() . "/images/store/" . $store_id . "/images/" . $name, Model_ElementImage::$IMAGE_TYPE_STORE , $store_id);
                    }
                }
                $this->_redirect("/account/manage/store/$store_seo_url/update/1");
            }
        }
        $this->_redirect("/account");
    }
    
    public function saveProductOrderAction() {
        $user = new Model_User();
        $logged = $user->getCurrentUser();
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();

            $store_id = $request->getParam("store_id", "");
            
            $model_store = new Model_Store();
            if($model_store->checkStoreOwner($store_id, $user->user_id)) {
                $products = $model_store->getStoreProducts($store_id, 0, false);
                
                foreach ($products as $value) {
                    $sort = $request->getParam("product-sort-{$value['product_id']}", false);
                    if($sort) {
                        $data = array("product_id" => $value['product_id'],
                            "product_sort_order" => $sort);
                        
                        $model_store->saveProductOrder($data);
                    }
                }
                
                $store_seo_url = $request->getParam("store_seo_url", "");
                
                $this->_redirect("/account/manage/store/$store_seo_url/update/1");
            }
        }
        $this->_redirect("/account");
    }
    
    public function saveStoreAffiliateAction() {
        $user = new Model_User();
        $logged = $user->getCurrentUser();
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();

            $store_id = $request->getParam("store_id", "");
            
            $model_store = new Model_Store();
            if($model_store->checkStoreOwner($store_id, $user->user_id)) {
                $data = array("credit_card_name" => $request->getParam("cc_name", ""),
                    "credit_card_number" => $request->getParam("cc_number", ""),
                    "credit_card_month" => $request->getParam("cc_expire_month", ""),
                    "credit_card_year" => $request->getParam("cc_expire_year", ""));
                
                $secure = new Model_Encrypt();
                $data['credit_card_number'] = $secure->Encrypt($data['credit_card_number']);

                $model_ap = new Model_Affiliate();
                $cc_info = $model_ap->getAffiliateInfo($store_id);
                $data['credit_card_id'] = $cc_info['credit_card_id'];
                $model_ap->updateCreditCard($data);
                
                $store_seo_url = $request->getParam("store_seo_url", "");
                
                $this->_redirect("/account/manage/store/$store_seo_url/update/1");
            }
        }
        $this->_redirect("/account");
    }

    public function salesReportAction() {
        $user = new Model_User();
        $loggedin = $user->getCurrentUser();
        if ($loggedin) {
            $this->view->user_id = $user->user_id;

            $store_seo_url = $this->_request->getParam('store', 0);
            $month = $this->_request->getParam('month', 0);
            $year = $this->_request->getParam('year', 0);
            
            $model_store = new Model_Store();
            $store = $model_store->getStoreFromUrl($store_seo_url);
            $this->view->store = $store;
            $this->view->month = $month;
            $this->view->year = $year;
            
            $model_ap = new Model_Affiliate();
            $ap_info = $model_ap->getAffiliateInfo($store['store_id']);
            
            $this->view->sales_report = $model_ap->getMonthlySalesReport($ap_info['affiliate_program_id'], $month, $year);
                        
            $this->view->seo_title = "View ".$store['store_name']." Sales Report";
            $this->view->meta_desc = "View ".$store['store_name']." Sales Report";
            
            $this->view->store_name = $store['store_name'];
            $this->view->store_seo_url = $store['store_seo_url'];
        } else {
            $this->_redirect("/login");
        }        
    }
    private function stringnl2br($input) {
        $out = str_replace( "\r\n", "<br/>", $input );
        return $out;
        
    }
    
    public function productsFeedUploadAction(){
        if ($this->_request->isPost()) {
            $extension = $_FILES['feed']['type'];
            $name = $_FILES['feed']['name'];
            $temp_name = $_FILES['feed']['tmp_name'];
            
            if($extension == "text/xml"){
                move_uploaded_file($temp_name, getcwd() . "/uploads/feeds/".$name);
                // processing the xml file
                $products = new Zend_Config_Xml (getcwd() . "/uploads/feeds/".$name);
               // var_dump($products->channel->item);
                foreach($products->channel->item as $product){
                    
                    $product_title = $product->title;
                    $product_title = $product->title;
                    $product_link = $product->link;
                    $product_description = $product->description;
                    $product_image = $product->image_link;
                  //  var_dump($product);
                    die;
                    
                    $data = array();
                    $data['product_name'] = $product_title;
                    $data['product_description'] = $product_description;
                    $data['product_url'] = $product_link;

                    // Create store in the database
                    $model_product = new Model_Product();
                    $product_id = $model_product->createProduct($data);


                    if($product_id) {        
                        // Category
                        /*$cat_data = array(
                            'product_category_id' => $this->_request->getParam("product-category",""),
                            'product_id' => $product_id
                        );
                        $model_product->createProductCategoryRelation($cat_data);*/

                        // Image
                        $img = $product_image;

                        //saving the default image of the user
                        $image_model =  new Model_ElementImage();
                        $image_model->saveImage($img, Model_ElementImage::$IMAGE_TYPE_PRODUCT, $product_id);

                        echo 'Product added successfully.';
                    } else {
                        echo 'Product already exists.';
                    }
                    die;
                    
                }
                
            } else {
                echo "the extension of the file is not correct, please upload an xml file ";
            }
            
        }
        echo "end";
        die;
    }

    
    public function deletePersonAction(){
        $result = array();
        $result['text'] = false;
        
        if($this->_request->getParam('ajax') != true){
            $result['text'] = false;
        }else{
            if( !$this->_loggedUser->isAdmin() ){
                $result['text'] = false;
            }else{
                 $user_id_to_delete = $this->_request->getParam('user_id');
                $user_model = new Model_User($user_id_to_delete);
                $user_model->deleted =  1;
                $result['text'] = "This person has been deleted.";
            }
        }
        
        echo $result['text'];
        die;
    }
    
}


