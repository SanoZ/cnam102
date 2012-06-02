<?php

class CheckoutController extends Zend_Controller_Action {

    public function init(){
        
    }
    
    /**
     * The default action - show the home page
     */
    public function indexAction() {
        //overwritting cdn url
        $this->view->cdnURL = "";
        
        $bl = new Model_Blacklist();
        $bl->trackBlacklist();
        
        if($bl->isBlacklisted($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $this->_redirect("index/blacklist");
            return;
        }
        
        $verificationcode = new Zend_Session_Namespace('verificationcode');
       
        // this will increment for each page load.
        $verificationcode->code = date("YMdHis");//base64_encode(rand(1000, 100000));
       
        
        
        $model_pages = new Model_Pages();
        $optionClass = new Admin_Model_Option();
        $page_seo = $this->_request->getRequestUri();
        $page_seo = substr($page_seo, 1);
        if ($page_seo) {
            $page = $model_pages->GetPageUrl($page_seo, "");
            //Applying seo fields
            if ($page != null) {
                $this->view->seo_title = $page->page_title;
                $this->view->meta_desc = $page->meta_desc;
            } else {
                $this->view->seo_title = 'Secure Checkout';
                $this->view->meta_desc = '';
            }
        }
        //Adding the functionality of the shopping cart
        $this->view->headScript()->appendFile('/jscript/custom/validation_rules.js', 'text/javascript');
        $this->view->headScript()->appendFile('/jscript/custom/checkout_js.js', 'text/javascript');
        $this->view->headScript()->appendFile('/jscript/jQuery-Validation/jquery.validationEngine.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/validationEngine.jquery.css');
        $this->view->headScript()->appendFile('/jscript/custom/shippingCalculator.js', 'text/javascript');

        // Cleanup removes any products with 0 quantity
        $this->cleanUpOrder();

        // If the cart is empty redirect the customer to the empty cart page
        if ($this->cartEmpty()) {
            $this->_redirect('/shoppingcart/display-shoppingcart');
        }

        //loading the necessary objects
        $paymentMethodModel = new Model_PaymentMethod();
        $orderModel = new Model_Orders();
        $addressModel = new Model_Address();
        $globalModel = new Model_Globals();
        $orderClass = new Model_Orders();
        $model_product = new Model_Product();
        $currentIdOrder = $orderModel->getCurrentOrderCookie();
        //if the user is authenticated the form has to be loaded
        $currentOrder = $orderModel->loadOrder($currentIdOrder, array(), false);
        $this->view->order = $currentOrder->toArray();
        //loading the payment methods
        $paymentMethods = array();
        $this->view->user_type = 1; //default of the payment to display
        if (Zend_Auth::getInstance()->hasIdentity()) {

            $identity = Zend_Auth::getInstance()->getIdentity();
            $this->view->user_type = $identity->user_type;
            if ($currentOrder->status == Model_Orders::$ORDER_STATUS_SHOPPINGCART) {
                $lastorder = $orderModel->loadLastOrderUser($identity->user_id);
                if ($lastorder != null) {
                    $currentOrder->customer_email = $lastorder->customer_email;
                    $currentOrder->customer_name = $lastorder->customer_name;
                    $currentOrder->customer_lname = $lastorder->customer_lname;
                    $currentOrder->customer_phone = $lastorder->customer_phone;
                    $currentOrder->customer_company = $lastorder->customer_company;
                    $currentOrder->delivery_address_id = $lastorder->delivery_address_id;
                    $currentOrder->billing_address_id = $lastorder->billing_address_id;
                    $currentOrder->delivery_instructions = $lastorder->delivery_instructions;
                    $currentOrder->customer_shipping_name = $lastorder->customer_shipping_name;
                    $currentOrder->customer_shipping_lname = $lastorder->customer_shipping_lname;
                    $currentOrder->customer_shipping_phone = $lastorder->customer_shipping_phone;
                    $currentOrder->customer_shipping_company = $lastorder->customer_shipping_company;
                } else {
                    $model_customer = new Model_CustomerInfo();
                    $customer = $model_customer->loadCustomerInfo($identity->user_id);
                    if ($customer != null) {
                        $currentOrder->customer_email = $customer->cus_email;
                        $currentOrder->customer_name = $customer->cus_name;
                        $currentOrder->customer_lname = $customer->cus_lname;
                        $currentOrder->customer_phone = $customer->cus_phone;
                        $currentOrder->customer_company = $customer->cus_company;
                        $currentOrder->delivery_address_id = $customer->delivery_address_id;
                        $currentOrder->billing_address_id = $customer->billing_address_id;
                        $currentOrder->delivery_instructions = $customer->delivery_instructions;
                        $currentOrder->customer_shipping_name = $customer->cus_shipping_name;
                        $currentOrder->customer_shipping_lname = $customer->cus_shipping_lname;
                        $currentOrder->customer_shipping_phone = $customer->cus_shipping_phone;
                        $currentOrder->customer_shipping_company = $customer->cus_shipping_company;
                    }
                }
                //loads the information of the previous order
                $this->view->order = $currentOrder->toArray();
            } else {//tries to load the infromation of the customer
                $model_customer = new Model_CustomerInfo();
                $customer = $model_customer->loadCustomerInfo($identity->user_id);
                if ($customer != null) {
                    $currentOrder->customer_email = $customer->cus_email;
                    $currentOrder->customer_name = $customer->cus_name;
                    $currentOrder->customer_lname = $customer->cus_lname;
                    $currentOrder->customer_phone = $customer->cus_phone;
                    $currentOrder->customer_company = $customer->cus_company;
                    $currentOrder->delivery_address_id = $customer->delivery_address_id;
                    $currentOrder->billing_address_id = $customer->billing_address_id;
                    $currentOrder->delivery_instructions = $customer->delivery_instructions;
                    $currentOrder->customer_shipping_name = $customer->cus_shipping_name;
                    $currentOrder->customer_shipping_lname = $customer->cus_shipping_lname;
                    $currentOrder->customer_shipping_phone = $customer->cus_shipping_phone;
                    $currentOrder->customer_shipping_company = $customer->cus_shipping_company;
                }
            }
            $this->view->logged = true;
            $this->view->loggedName = $identity->user_name;
            if ($identity->super_admin == true) {
                $this->view->loggedName = "Admin in the backend";
                $paymentMethods = $paymentMethodModel->loadAdminPaymentMethod();
                if ($identity->user_type == 3) {
                    $paymentMethods = $paymentMethodModel->loadTestingPaymentMethod();
                }
            }
        } else {
            $this->view->logged = false;
        }
        //creating the section of the payment method
        $this->view->paymentmethodsMain = $this->getHtmlPaymentMethods($paymentMethodModel->loadActivePaymentMethod()->toArray());
        $this->view->paymentmethods = $this->getHtmlPaymentMethods($paymentMethods);

        $delivery = $addressModel->loadAddress($currentOrder->delivery_address_id);

        $billing = $delivery;
        if ($currentOrder->delivery_address_id <> $currentOrder->billing_address_id)
            $billing = $addressModel->loadAddress($currentOrder->billing_address_id);
        $this->view->delivery = $delivery->toArray();
        $this->view->billing = $billing->toArray();

        $this->view->states = $addressModel->getAllStates();


        // Load products for RHS cart summary
        if ($currentIdOrder && $orderClass->exists($currentIdOrder)) {
            $order = $orderClass->loadOrder($currentIdOrder, array(Model_Orders::$ORDER_STATUS_SHOPPINGCART, Model_Orders::$ORDER_STATUS_CHECKOUT));
            $productsModel = $orderClass->loadOrderProducts($currentIdOrder);
            $products = array();
            $count = 0;
            for ($i = 0; $i < $productsModel->count(); $i++) {
                $product = $productsModel->getRow($i)->toArray();
                $count += $product['quantity'];
                $product['image_url'] = '';
                $product['manufacturer_name'] = '';
                $product['service'] = $model_product->getService($product['product_id']);
                $selected_specs = $optionClass->getSelectedSpecs($product['element_id']);
                foreach ($selected_specs as $k => $spec) {
                    $opt = $optionClass->loadOption($spec['specs_option_id']);
                    $opt = $opt->toArray();
                    $selected_specs[$k]['option_title'] = $opt['option_title'];
                }
                $product['selected_specs'] = $selected_specs;
                $products[] = $product;
            }



            // Retrive images for each product
            $i = 0;
            foreach ($products as $prod_id) {
                $p = $model_product->getProductById($prod_id['product_id']);
                $p = $p->toArray();
                if ($p['deleted'] == 0) {
                    //getting the images of the products
                    $images = $model_product->getProductImagesById($prod_id['product_id']);
                    $products[$i]['images'] = $images->toArray();
                }
                $i++;
            }
            $this->view->total_items = $count;
            $this->view->products = $products;
            //$this->view->order = $order->toArray();
        } else {
            $this->view->products = null;
            $this->view->order = null;
        }
    }

    public function updateEmailAction() {
        //information is being sent via ajax, then the display of the view is disabled
        //cleanning the information entered
        $emailEntered = trim($_POST['email']);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();


        $orderModel = new Model_Orders();
        $addressModel = new Model_Address();
        $modeluser = new Model_User();
        //updating the order email
        $currentIdOrder = $orderModel->getCurrentOrderCookie();
        $currentOrder = $orderModel->loadOrder($currentIdOrder);
        $currentOrder->customer_email = $emailEntered;
        $currentOrder->order_checkout = Date('Y-m-d h:i:s', mktime());
        $orderModel->updateOrder($currentOrder->toArray());
        //finish updating email on the order

        $order_id = $orderModel->getLastOrderIdCookie();

        if ($order_id <> null && $orderModel->exists($order_id)) {//order exists
            $order = $orderModel->loadOrder($order_id);

            if (strcmp($order->customer_email, $emailEntered) == 0) {
                //load the information of the previous order
                $result = $order->toArray();
                $user = $modeluser->getUserByEmail($emailEntered);
                if ($user) {
                    $result['result'] = true;
                } else {
                    $result['result'] = false;
                }
                $result['infoToDisplay'] = true;
                //loading addressess information
                $delivery = $addressModel->loadAddress($order->delivery_address_id);
                $billing = $delivery;
                if ($order->delivery_address_id <> $order->billing_address_id)
                    $billing = $addressModel->loadAddress($order->billing_address_id);

                $result['delivery'] = $delivery->toArray();
                $result['billing'] = $billing->toArray();
            } else {
                //enable password validation                
                $user = $modeluser->getUserByEmail($emailEntered);
                if ($user) {
                    //If the user exists in the database the password field should be display
                    $result = array('result' => true);
                } else {
                    //if it dosent exist the passworde shall not be display and the info to prepopulate the fields are not sent
                    $result = array('result' => false);
                    $result['infoToDisplay'] = false;
                }
            }
        } else {
            //Do not populate the fields, it is the first time access, check if the user is already in the database
            //enable password validation
            $user = $modeluser->getUserByEmail($emailEntered);
            if ($user) {
                //If the user exists in the database the password field should be display
                $result = array('result' => true);
            } else {
                //if it dosent exist the passworde shall not be display and the info to prepopulate the fields are not sent
                $result = array('result' => false);
                $result['infoToDisplay'] = false;
            }
        }
        //checking if the admin is looged in, if it is, it will display the infromation of the user that matches the email
        //echo Zend_Auth::getInstance()->hasIdentity();
        //$identity = Zend_Auth::getInstance()->getIdentity();
        //var_dump($identity);
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            $result = array('result' => false);
            if ($identity->super_admin == true) {//admin
                //if it dosent exist the passworde shall not be display and the info to prepopulate the fields are not sent
                $result = array('result' => false);
                $result['infoToDisplay'] = false;
                echo json_encode($result);
                return;
            } else if ($identity->user_email == $emailEntered) {//logged user
                //donot display the password field and load the user info if his has one
                $result = array('result' => false);
                $result['infoToDisplay'] = false;
                //get the last order of this user
                $order = $orderModel->loadLastOrderUser($identity->user_id);
                if ($order != null) {
                    $result = array('result' => false);
                    $result['infoToDisplay'] = true;
                    $result = $order->toArray();
                    $delivery = $addressModel->loadAddress($order->delivery_address_id);
                    $billing = $delivery;
                    if ($order->delivery_address_id <> $order->billing_address_id)
                        $billing = $addressModel->loadAddress($order->billing_address_id);

                    $result['delivery'] = $delivery->toArray();
                    $result['billing'] = $billing->toArray();
                }
                echo json_encode($result);
                return;
            }
        }

        echo json_encode($result);
    }

    public function saveOrderAction() {
        
        $bl = new Model_Blacklist();
        
        
        $verificationcode = new Zend_Session_Namespace('verificationcode');
        if($this->_request->getParam('zorillavercode',"") != $verificationcode->code){
            $result = array();
            
            $result['response'] = "Error with the validation of the order please refresh the page" ;
            $result['success'] = false;
            $result['jsonValidateReturn'] = array();
            $result['jsonValidateReturn'][0][0] = "#transaction_response";
            $result['jsonValidateReturn'][0][1] = "Error with the validation of the order please refresh the page";
            $result['jsonValidateReturn'][0]['isError'] = true;
            
            
            echo json_encode($result);
            die;
        }
        //Checking if the user is logged in
        $user_id = 0;
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            if ($identity->super_admin == false) {//not admin
                $user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
            }
        }
        //saving the addresses
        $modelAddress = new Model_Address();
        $dataAddress = array();
        $dataAddress['street'] = $_POST['address_street_checkout'];
        $dataAddress['city'] = $_POST['address_city_checkout'];
        $dataAddress['state'] = $_POST['address_state_checkout'];
        //hardcoded to manage only australia posting
        $dataAddress['country'] = "Australia";
        $dataAddress['customer_id'] = $user_id;
        $dataAddress['postcode'] = $_POST['address_postcode_checkout'];
        $addressIdShipping = $modelAddress->addAddress($dataAddress);
        //setting the billing as the shipping, it will be overrided if needed by the following if
        $addressIdBilling = $addressIdShipping;
        //saves the billing information if it is needed for billing        
        if ($_POST['address_same_billing'] != '1') {
            $dataAddress = array();
            $dataAddress['street'] = $_POST['address_street_checkout_shipping'];
            $dataAddress['city'] = $_POST['address_city_checkout_shipping'];
            $dataAddress['state'] = $_POST['address_state_checkout_shipping'];
            //hardcoded to manage only australia posting
            $dataAddress['country'] = "Australia";
            $dataAddress['customer_id'] = $user_id;
            $dataAddress['postcode'] = $_POST['address_postcode_checkout_shipping'];
            $addressIdBilling = $modelAddress->addAddress($dataAddress);
        }
        $orderModel = new Model_Orders();
        $currentIdOrder = $orderModel->getCurrentOrderCookie();
        $currentOrder = $orderModel->loadOrder($currentIdOrder, array(), false);
        $currentOrder->customer_email = $_POST['email_checkout'];
        $currentOrder->customer_id = $user_id;
        $currentOrder->billing_address_id = $addressIdBilling;
        $currentOrder->delivery_address_id = $addressIdShipping;
        if ($_POST['address_instructions_checkout'] == 'Delivery instructions (Optional)') {
            $currentOrder->delivery_instructions = '';
        } else {
            $currentOrder->delivery_instructions = $_POST['address_instructions_checkout'];
        }
        $currentOrder->customer_name = $_POST['fname_checkout'];
        $currentOrder->customer_lname = $_POST['lname_checkout'];
        $currentOrder->customer_phone = $_POST['phone_checkout'];
        if (isset($_POST['company_checkout']))
            $currentOrder->customer_company = $_POST['company_checkout'];
        else
            $currentOrder->customer_company = '';
        $currentOrder->status = Model_Orders::$ORDER_STATUS_CHECKOUT;

        if (isset($_POST['fname_shipping_checkout'])) {

            $currentOrder->customer_shipping_name = $_POST['fname_shipping_checkout'];
            $currentOrder->customer_shipping_lname = $_POST['lname_shipping_checkout'];
            $currentOrder->customer_shipping_phone = $_POST['phone_shipping_checkout'];
            if (isset($_POST['company_shipping_checkout']))
                $currentOrder->customer_shipping_company = $_POST['company_shipping_checkout'];
            else
                $currentOrder->customer_shipping_company = '';
        }


        //Saving the infromation of the payment method selected for the order
        $currentOrder->payment_method_id = $_POST['payment_selected_id'];
        //if the payment was credit card
        if ($currentOrder->payment_method_id == 1) {//credit card payment id
            //Loading the module to encrypt the credit card info
            $secure = new Model_Encrypt();
            //loading the info of the credit card to the order
            $currentOrder->customer_cc_name = $_POST['payment_name_checkout' . $verificationcode->code];
            $currentOrder->customer_cc_no = $secure->Encrypt($_POST['payment_number_checkout'. $verificationcode->code]);
            $currentOrder->customer_cc_month = $_POST['payment_expire_month_checkout'. $verificationcode->code];
            $currentOrder->customer_cc_year = $_POST['payment_expire_year_checkout'. $verificationcode->code];
            $currentOrder->customer_cc_ccv = $_POST['payment_ccv_checkout'. $verificationcode->code];
        }
        //Saving the order in the database
        $orderModel->updateOrder($currentOrder->toArray());
        //redirects the user to the
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'ecommerce');
        if ($config->ecommerce->confirmation_page_enabled == 1) {
            $this->_redirect('/checkout/confirmation');
        } else {
            $result = $this->processOrder();
            $jsonEnabled = true; //harcoded need to be fixed in next versions
            if ($jsonEnabled) {
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->layout->disableLayout();
                //$result['jsonValidateReturn']['isError'] = ;
                echo json_encode($result);
            }
        }
    }

    public function thankYouAction() {
        //overriding cdn configuration
        $this->view->cdnURL = "";
        $model_pages = new Model_Pages();
        $page = $model_pages->GetPageUrl("checkout", "");
        //Applying seo fields
        if ($page != null) {
            $this->view->seo_title = $page->page_title;
            $this->view->meta_desc = $page->meta_desc;
        } else {
            $this->view->seo_title = 'Thank You';
            $this->view->meta_desc = '';
        }
        $this->view->headScript()->appendFile('/jscript/custom/validation_rules.js', 'text/javascript');
        $this->view->headScript()->appendFile('/jscript/jQuery-Validation/jquery.validationEngine2.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/validationEngine.jquery.css');

        $model_pages = new Model_Pages();
        $page_seo = $this->_request->getRequestUri();
        $page_seo = substr($page_seo, 1);
        $optionClass = new Admin_Model_Option();
        if ($page_seo) {
            $page = $model_pages->GetPageUrl($page_seo, "");
            //Applying seo fields
            if ($page != null) {
                $this->view->seo_title = $page->page_title;
                $this->view->meta_desc = $page->meta_desc;
            }
        }
        //Checks if the user is logged in to register the user if htey want to
        $orderModel = new Model_Orders();
        $addressModel = new Model_Address();
        $user_model = new Model_User();
        $lastIdOrder = $orderModel->getLastOrderIdCookie();
        $this->view->displayRemember = false;
        $this->view->orderId = $lastIdOrder;
        $this->view->order = $orderModel->loadOrder($lastIdOrder)->toArray();
        $this->view->delivery = $addressModel->loadAddress($this->view->order['delivery_address_id'])->toArray();
        $products = $orderModel->loadOrderProducts($lastIdOrder)->toArray();
        foreach ($products as $key => $product) {
            $selected_specs = $optionClass->getSelectedSpecs($product['element_id']);
            foreach ($selected_specs as $k => $spec) {
                $opt = $optionClass->loadOption($spec['specs_option_id']);
                $opt = $opt->toArray();
                $selected_specs[$k]['option_title'] = $opt['option_title'];
            }
            $products[$key]['selected_specs'] = $selected_specs;
        }
        $this->view->products = $products;
        if (!(Zend_Auth::getInstance()->hasIdentity())) {
            $this->view->displayRemember = true;
        } else {
            $this->view->displayRemember = false;
        }
        $order = $orderModel->loadOrder($lastIdOrder, array(), false);
        $user = $user_model->getUserByEmail($order->customer_email);

        if ($user == null) {//doesnt exit in the database
            $this->view->displayRemember = true;
        } else {
            $this->view->displayRemember = false;
        }

        //line to resend the order several time
        //$this->sendEmailOrder($orderModel->loadOrder($lastIdOrder));
    }

    public function confirmationAction() {
        //Loading the module to encrypt the credit card info
        $secure = new Model_Encrypt();
        $orderModel = new Model_Orders();
        $addressModel = new Model_Address();
        //loading the order
        $currentIdOrder = $orderModel->getCurrentOrderCookie();
        $orderModel->recalculate($currentIdOrder);
        $order = $orderModel->loadOrder($currentIdOrder, array(), false);
        $order->customer_cc_no = $secure->Decrypt($order->customer_cc_no);
        //getting last 4 characters
        $last = substr($order->customer_cc_no, -4, 4);
        //getting the size and setting the * elements
        $lenght = strlen($order->customer_cc_no);
        $order->customer_cc_no = str_repeat("*", $lenght - 4) . $last;

        $this->view->order = $order;

        //loading addressess information
        $delivery = $addressModel->loadAddress($order->delivery_address_id);
        $billing = $delivery;
        if ($order->delivery_address_id <> $order->billing_address_id)
            $billing = $addressModel->loadAddress($order->billing_address_id);
        $this->view->delivery = $delivery;
        $this->view->billing = $billing;

        //loading the products of the order
        $productsModel = $orderModel->loadOrderProducts($currentIdOrder);
        $products = array();
        for ($i = 0; $i < $productsModel->count(); $i++) {
            $products[] = $productsModel->getRow($i)->toArray();
        }
        $this->view->products = $products;
    }

    public function processOrderAction() {
        $this->processOrder();
        $this->render('thank-you');
    }

    private function processOrder() {
        $resultPayment = array();
        //20/12/2011 juan - UPdate the process of creating an order to send an email if the process fails
        try{
            $secure = new Model_Encrypt();
            $orderModel = new Model_Orders();
            $productModel = new Model_Product();

            $currentIdOrder = $orderModel->getCurrentOrderCookie();
            $order = $orderModel->loadOrder($currentIdOrder, array(), false);

            //Loads the selected payment method
            $modelPaymentMethod = new Model_PaymentMethod();
            $paymentOption = $modelPaymentMethod->loadPaymentMethod($order->payment_method_id);
            //loading the class of the payment
            $paymentClass = $paymentOption->payment_module;
            $payment = new $paymentClass();
            //Getting the information from the database to config the module
            $config = unserialize($paymentOption->payment_config);
            //Loading the configuration into the payment module
            $payment->loadConfig($config);
            //creating the creditcard option
            $ccinfo = array();
            $ccinfo['cc_name'] = $order->customer_cc_name;
            $ccinfo['cc_no'] = $secure->Decrypt($order->customer_cc_no);
            $ccinfo['cc_month'] = $order->customer_cc_month;
            $ccinfo['cc_year'] = $order->customer_cc_year;
            $ccinfo['cc_ccv'] = $order->customer_cc_ccv;
            //sending the information to the payment module
            $payment->submitPayment($ccinfo, $order->order_id, $order->total);
            //getting the response from the bank
            $responseBank = $payment->getResponse();
            //saving the response and taking action
            if ($responseBank == true) {//approved by the bank
                $tbl = new Model_DbTable_OrderSequence();
                $seq = $tbl->insert(array());
                $order->order_no = $seq;
                if ($order->payment_method_id == 1 || $order->payment_method_id == 7) {//credit card type and paypal
                    $order->status = Model_Orders::$ORDER_STATUS_APPROVED;
                } else {
                    $order->status = Model_Orders::$ORDER_STATUS_PENDING_CONFIRMATION_PAYMET;
                }
                $order->order_processed = Date('Y-m-d h:i:s', mktime());
                //juan 20/12/2011 added to save the order as soon as the transaction is approved form the bank.
                $order->save();
                // end change juan
                $this->view->response = "Approved";
                $resultPayment['response'] = "Approved";
                $resultPayment['success'] = true;
                $resultPayment['jsonValidateReturn'] = array();
                $resultPayment['jsonValidateReturn'][] = "#transaction_response";
                $resultPayment['jsonValidateReturn']['isError'] = false;
                $resultPayment['successUrl'] = "https://" . $_SERVER['SERVER_NAME'] . "/checkout/thank-you";

                $orderModel->recordTransaction($currentIdOrder, $paymentOption->payment_id, "{$order->total},{$order->customer_cc_name}", "Approved: " . $payment->responsexml, 'sent');

                //saves the current cookie as a confirmed order
                $orderModel->setLastOrderIdCookie($currentIdOrder);
                //erases the cookie of the previous shoppingcart
                $orderModel->setCurrentOrderIdCookie(null);

                // Now increase popularity by 10 and decrease stock by quantity (For each product)
                $products = $orderModel->loadOrderProducts($currentIdOrder)->toArray();
                foreach ($products as $product) {
                    $productModel->addPopularityProduct($product['product_id'], (10 * $product['quantity']));
                    $productModel->reduceStock($product['product_id'], $product['quantity'], $product['idvariant_product']);
                }
                $this->sendEmailOrder($order);
            } else {
                $order->status = Model_Orders::$ORDER_STATUS_PENDING;
                $error = $payment->getError();
                $this->view->response = "Error in the transaction: " . $error;
                $resultPayment['response'] = "Error in the transaction: " . $error;
                $resultPayment['success'] = false;
                $resultPayment['jsonValidateReturn'] = array();
                $resultPayment['jsonValidateReturn'][0][0] = "#transaction_response";
                $resultPayment['jsonValidateReturn'][0][1] = "There was an error with the credit card details: " . $error;
                $resultPayment['jsonValidateReturn'][0]['isError'] = true;
                $orderModel->recordTransaction($currentIdOrder, $paymentOption->payment_id, "{$order->total},{$order->customer_cc_name}: " . $payment->requestxml, $error . ": " . $payment->responsexml, 'sent');
            }
            
            //saving the user if it logged in
            if (Zend_Auth::getInstance()->hasIdentity()) {
                $identity = Zend_Auth::getInstance()->getIdentity();
                if ($identity->super_admin == false) {
                    $order->customer_id = $identity->user_id;
                    //creating record of the user
                    $model_customer = new Model_CustomerInfo();
                    $data = array();
                    $data['cus_name'] = $order->customer_name;
                    $data['cus_lname'] = $order->customer_lname;
                    $data['cus_phone'] = $order->customer_phone;
                    $data['cus_company'] = $order->customer_company;
                    $data['delivery_address_id'] = $order->delivery_address_id;
                    $data['billing_address_id'] = $order->billing_address_id;
                    $data['cus_shipping_name'] = $order->customer_shipping_name;
                    $data['cus_shipping_lname'] = $order->customer_shipping_lname;
                    $data['cus_shipping_phone'] = $order->customer_shipping_phone;
                    $data['cus_shipping_company'] = $order->customer_shipping_company;
                    $data['delivery_instructions'] = $order->delivery_instructions;
                    $data['cus_email'] = $order->customer_email;
                    $data['customer_email'] = $order->customer_email;
                    $data['user_id'] = $order->customer_id;

                    if ($order->billing_address_id == $order->delivery_address_id) {
                        $data['address_same_billing'] = true;
                    } else {
                        $data['address_same_billing'] = false;
                    }

                    $address_model = new Model_Address();
                    $delivery = $address_model->loadAddress($order->delivery_address_id);
                    $data['deliveryAddress_street'] = $delivery->street;
                    $data['deliveryAddress_city'] = $delivery->city;
                    $data['deliveryAddress_state'] = $delivery->state;
                    $data['deliveryAddress_postcode'] = $delivery->postcode;

                    $billing = $address_model->loadAddress($order->billing_address_id);
                    $data['billingAddress_street'] = $billing->street;
                    $data['billingAddress_city'] = $billing->city;
                    $data['billingAddress_state'] = $billing->state;
                    $data['billingAddress_postcode'] = $billing->postcode;


                    $model_customer->updateCustomerInfo($data);
                }
            }
            $order->ip_address = $_SERVER['REMOTE_ADDR'];
            //saving th eorder information
            $orderModel->updateOrder($order->toArray());

            
        } catch(Exception $ex){
            //20/12/2011 juan - UPdate the process of creating an order to send an email if the process fails
            $resultPayment['response'] = "There was an error with you order# " . $currentIdOrder . ". Please contact us for more information";
            $resultPayment['success'] = false;
            $resultPayment['jsonValidateReturn'] = array();
            $resultPayment['jsonValidateReturn'][0][0] = "#transaction_response";
            $resultPayment['jsonValidateReturn'][0][1] = "There was an error with you order# " . $currentIdOrder . ". Please contact us for more information";
            $resultPayment['jsonValidateReturn'][0]['isError'] = true;
            
            $mail = new Zend_Mail('');
            $mail->setBodyHtml("Exception: " . $ex . " \n Page: " . $_SERVER["REQUEST_URI"] ." Order id" . $currentIdOrder);
            $mail->setFrom("admin@ziller.com.au", 'ANTIMALL SERVER');
            $mail->addTo("error@ziller.com.au", 'Dev Team');
            $mail->setSubject('Important Checkout ERROR Antimall');
            $mailset = new Admin_Model_Mail();
            $mail->send($mailset->getTransport());  
            //Juan Change end
        }
        return $resultPayment;
    }

    private function getHtmlPaymentMethods($paymentMethods) {
        $return_html = array();
        $globalModel = new Model_Globals();

        foreach ($paymentMethods as $paymentMethod) {
            $paymenthtml = new Zend_View();
            $paymenthtml->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/payment/');
            $paymenthtml->years = $globalModel->getFollowingYears(10);
            $paymenthtml->months = $globalModel->getMonths();
            //var_dump($paymentMethods);
            $html = $paymenthtml->render($paymentMethod['payment_view']);
            $return_html[$paymentMethod['payment_id']]['html'] = $html;
            $return_html[$paymentMethod['payment_id']]['name'] = $paymentMethod['payment_name'];
            $return_html[$paymentMethod['payment_id']]['payment_id'] = $paymentMethod['payment_id'];
        }
        return $return_html;
    }

    public function sendEmailOrder($order) {
        //Loading the module to encrypt the credit card info
        $optionClass = new Admin_Model_Option();
        $model_settings = new Admin_Model_Settings();
        $settings = $model_settings->GetSettings();
        $secure = new Model_Encrypt();
        $orderModel = new Model_Orders();
        $addressModel = new Model_Address();
        $model = new Model_Product();
        // create view object
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/emails/');
        $html->setHelperPath(APPLICATION_PATH . '/modules/default/views/helpers/');

        // assign values
        $order->customer_cc_no = $secure->Decrypt($order->customer_cc_no);
        //getting last 4 characters
        $last = substr($order->customer_cc_no, -4, 4);
        //getting the size and setting the * elements
        $lenght = strlen($order->customer_cc_no);
        $order->customer_cc_no = @str_repeat("*", $lenght - 4) . $last;
        $html->order = $order;

        $delivery = $addressModel->loadAddress($order->delivery_address_id);
        $billing = $delivery;
        if ($order->delivery_address_id <> $order->billing_address_id)
            $billing = $addressModel->loadAddress($order->billing_address_id);
        $html->delivery = $delivery;
        $html->billing = $billing;

        //loading the products of the order
        $productsModel = $orderModel->loadOrderProducts($order->order_id);
        $products = array();
        for ($i = 0; $i < $productsModel->count(); $i++) {
            $p = $productsModel->getRow($i)->toArray();
            $manufacturers = $model->getManufacturer($p['product_id']);
            $p['manufacturer'] = implode(" | ", $manufacturers);

            $images = $model->getImagesOrdered($p['product_id']);

            $p['images'] = $images;
            $selected_specs = $optionClass->getSelectedSpecs($p['element_id']);
            foreach ($selected_specs as $k => $spec) {
                $opt = $optionClass->loadOption($spec['specs_option_id']);
                $opt = $opt->toArray();
                $selected_specs[$k]['option_title'] = $opt['option_title'];
            }
            $p['selected_specs'] = $selected_specs;

            $p['size'] = $model->getSizeDetails($p['idvariant_product']);

            $products[] = $p;
        }

        $html->products = $products;

        // create mail object
        $mail = new Zend_Mail('utf-8');

        // render view
        $bodyText = $html->render('OrderTemplateMail.phtml');

        $model = new Admin_Model_Settings();
        $mailSettings = $model->getEmailType('Order');

        $model_sr = new Admin_Model_Sr();
        $reply_to = $model_sr->GetSingleSr("reply_to")->value; 
        $reply_to_name = $model_sr->GetSingleSr("reply_to_name")->value; 
        
        // configure base stuff
        $mTo = array();
        if($mailSettings->email_to != ''){
            $mTo = explode(",", $mailSettings->email_to);
        }
        array_push($mTo, $order->customer_email);
        $mail->addTo($mTo);
        if($mailSettings->email_bcc != ''){
            $mail->addBcc(explode(",", $mailSettings->email_bcc));
        }
        if($reply_to != ''){
            $mail->setReplyTo($reply_to,$reply_to_name);
        }

        if($mailSettings->email_cc != ''){
            $mail->addCc(explode(",", $mailSettings->email_cc));
        }
        $mail->setSubject(' Order #' . $order->order_id . ' Notification');

        $mail->setFrom($mailSettings->email_from, $mailSettings->email_from_name);
        $mail->setBodyHtml($bodyText);
        
        $mailset = new Admin_Model_Mail();
        $mail->send($mailset->getTransport());
    }
    

    public function registerUserAction() {
        //loading the user model
        $userModel = new Model_User();
        //loading the order model
        $orderModel = new Model_Orders();
        //loading address model
        $addressModel = new Model_Address();
        //loading the order information
        $order = $orderModel->loadOrder($_POST['orderId_checkout']);


        //loading the data to save the user into an array
        $data = array();
        $data['email'] = $order->customer_email;
        $data['password'] = $_POST['password_account'];
        $data['type'] = '1';
        $data['super_admin'] = '0';
        $data['user_name'] = $order->customer_name . ' ' . $order->customer_lname;
        $data['display_name'] = $order->customer_name . ' ' . $order->customer_lname;
        $data['image'] = 'profile-default.gif';
        //saving the information of the client
        $userId = $userModel->CreateUser($data);

        $abpath = getcwd();
        //create user directories
        @mkdir($abpath . "/uploads/" . $userId, 0777);
        //mkdir($abpath . "/uploads/" . $newUser . "/lessons/", 0777);
        //mkdir($abpath . "/uploads/" . $newUser . "/lessons/images", 0777);
        @mkdir($abpath . "/uploads/" . $userId . "/images", 0777);

        @copy($abpath . "/images/profile-default.gif", $abpath . "/uploads/" . $userId . "/images/profile-default.gif");

        //setting the order to the client
        $order->customer_id = $userId;
        //updating the order
        $orderModel->updateOrder($order->toArray());
        //saving the credit card infromation of the customer
        //loading the ccdata from the order.
        $ccdata = array();
        $ccdata['name'] = $order->customer_cc_name;
        $ccdata['number'] = $order->customer_cc_no;
        $ccdata['month'] = $order->customer_cc_month;
        $ccdata['year'] = $order->customer_cc_year;
        //creating the entry of the credit card info
        $userModel->addCreditCardInfoCustomer($userId, $ccdata);
        //adding the addressess to the current user
        //loading the addressess
        $delivery = $addressModel->loadAddress($order->delivery_address_id);
        //setting the user id to the delivery address
        $delivery->customer_id = $userId;
        //SAving the delivery address
        $addressModel->updateAddress($delivery->toArray(), $delivery->address_id);
        //Loading the billing address
        //loading the addressess
        $billing = $addressModel->loadAddress($order->billing_address_id);
        //setting the user id to the delivery address
        $billing->customer_id = $userId;
        //Saving the delivery address
        $addressModel->updateAddress($billing->toArray(), $billing->address_id);

        // Creating view object for email
        $email = new Zend_View();
        $mail = new Zend_Mail('utf-8');
        $email->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/emails/');
        $email->setHelperPath(APPLICATION_PATH . '/modules/default/views/helpers/');

        $model_customer = new Model_CustomerInfo();
        $dataCustomer = array();
        $dataCustomer['cus_name'] = $order->customer_name;
        $dataCustomer['cus_lname'] = $order->customer_lname;
        $dataCustomer['cus_phone'] = $order->customer_phone;
        $dataCustomer['cus_company'] = $order->customer_company;
        $dataCustomer['delivery_address_id'] = $order->delivery_address_id;
        $dataCustomer['billing_address_id'] = $order->billing_address_id;
        if ($order->customer_shipping_name != null) {
            $dataCustomer['cus_shipping_name'] = $order->customer_shipping_name;
            $dataCustomer['cus_shipping_lname'] = $order->customer_shipping_lname;
            $dataCustomer['cus_shipping_phone'] = $order->customer_shipping_phone;
            $dataCustomer['cus_shipping_company'] = $order->customer_shipping_company;
        }
        $dataCustomer['delivery_instructions'] = $order->delivery_instructions;
        $dataCustomer['cus_email'] = $order->customer_email;
        $dataCustomer['user_id'] = $userId;
        $model_customer->addCustomerInfo($dataCustomer);


        // Add data to email view
        $email->userdata = $data;
        $email->password = $data['password'];

        // email information
        $bodyText = $email->render('AccountTemplateMail.phtml');

        $mail->setBodyHtml($bodyText);

        $store = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'store');

        $model = new Admin_Model_Settings();
        $mailSettings = $model->getEmailType('Account');
        
        $model = new Admin_Model_Sr();
        $reply_to = $model->GetSingleSr("reply_to")->value; 
        $reply_to_name = $model->GetSingleSr("reply_to_name")->value; 

        
        $mTo = array();
        if($mailSettings->email_to != ''){
            $mTo = explode(",", $mailSettings->email_to);
        }
        array_push($mTo, $email->userdata['email']);
//        array_push($mTo, "sandrine@ziller.com.au");
        $mail->addTo($mTo);
        
        if($reply_to != ''){
            $mail->setReplyTo($reply_to,$reply_to_name);
        }
        $mail->setSubject('Your ' . $store->store->namestore . ' Account.');

        if($mailSettings->email_bcc != ''){
            $mail->addBcc(explode(",", $mailSettings->email_bcc));
        }
        if($mailSettings->email_cc != ''){
            $mail->addCc(explode(",", $mailSettings->email_cc));
        }
        $mail->setFrom($mailSettings->email_from, $mailSettings->email_from_name);
        //$mail->setFrom($store->store->email, $store->store->emailfrom);
        $mailset = new Admin_Model_Mail();
        $mail->send($mailset->getTransport());   



        $this->_redirect('/checkout/thank-you-register');
    }

    public function thankYouRegisterAction() {
        $model_pages = new Model_Pages();
        $page = $model_pages->GetPageUrl("checkout", "");
        //Applying seo fields
        if ($page != null) {
            $this->view->seo_title = $page->page_title;
            $this->view->meta_desc = $page->meta_desc;
        } else {
            $this->view->seo_title = 'Thank You';
            $this->view->meta_desc = '';
        }
        $this->view->headScript()->appendFile('/jscript/custom/validation_rules.js', 'text/javascript');
        $this->view->headScript()->appendFile('/jscript/jQuery-Validation/jquery.validationEngine2.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/validationEngine.jquery.css');
    }

    public function displayShippingAction() {
        $this->_helper->layout->disableLayout();
        $orderModel = new Model_Orders();
        $addressModel = new Model_Address();
        $shippingMethodModel = new Model_ShippingMethod();
        $currentIdOrder = $orderModel->getCurrentOrderCookie();

        $postcode = $_GET['postcode'];
        $products = $orderModel->loadOrderProducts($currentIdOrder);
        $delivery = array();
        $shippings = $shippingMethodModel->getPricesShippingMethods($postcode, $products, $delivery);

        $this->view->shippings = $shippings;
    }

    public function cartEmpty() {
        $order = new Model_Orders();
        if ($order->getCurrentOrderCookie() == NULL) {
            return true;
        }
        $products = $order->loadOrderProducts($order->getCurrentOrderCookie())->toArray();
        if (empty($products))
            return true;
        return false;
    }

    public function cleanUpOrder() {
        $order = new Model_Orders();
        if ($order->getCurrentOrderCookie() == NULL) {
            return;
        }
        $products = $order->loadOrderProducts($order->getCurrentOrderCookie())->toArray();
        foreach ($products as $product) {
            // If no quantity remove from order
            if ($product['quantity'] == 0) {
                $this->removeItem($product['product_id'], $product['element_id']);
            }
        }
    }

    private function removeItem($product_id, $element_id) {
        $order = new Model_Orders();
        $cookie = $order->getCurrentOrderCookie();
        $order_id = 0;
        if ($cookie == null || !$order->exists($cookie)) {
            $order_id = $order->createEmptyOrder();
            $order->setCurrentOrderIdCookie($order_id);
        } else {
            $order_id = $cookie;
        }

        $order->deleteProductOrder($order_id, $product_id, $element_id);

        //Updates the order with the total value
        $order->recalculate($order_id);
    }

    public function resetPasswordAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $user_model = new Model_User();
        $global_model = new Model_Globals();

        $result = array();
        $email = $_POST['email'];
        $result['text'] = "<p>The email entered is invalid</p>";
        if ($email != '') {
            $user = $user_model->getUserByEmail($email);
            if ($user != null) {
                //generating the new password
                $pass = $global_model->genRandomString(10);
                $user->user_password = md5($pass);
                $user->save();
                $this->sendEmailForgotPasword($user, $pass);
                $result['text'] = "<p class='register-success'>Your password has been sent out to you.<br/>Please check your email.</p>";
            } else {
                $result['text'] = "<p>There in no user with this email account.</p>";
            }
        }
        echo json_encode($result);
    }

    private function sendEmailForgotPasword($user, $password) {
        //Loading the module to encrypt the credit card info
        // create view object
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/emails/');
        $html->setHelperPath(APPLICATION_PATH . '/modules/default/views/helpers/');

        $model = new Admin_Model_Sr();
        $reply_to = $model->GetSingleSr("reply_to")->value; 
        $reply_to_name = $model->GetSingleSr("reply_to_name")->value; 
                
        $html->user = $user;
        $html->password = $password;

        // create mail object
        $mail = new Zend_Mail('utf-8');

        // render view
        $bodyText = $html->render('PasswordForgotTemplateMail.phtml');

        $model = new Admin_Model_Settings();
        $mailSettings = $model->getEmailType('Password');
        $mTo = array();
        if($mailSettings->email_to != ''){
            $mTo = explode(",", $mailSettings->email_to);
        }
        if($reply_to != ''){
            $mail->setReplyTo($reply_to,$reply_to_name);
        }
        array_push($mTo, $user->user_email);
        $mail->addTo($mTo);
        if($mailSettings->email_bcc != ''){
            $mail->addBcc(explode(",", $mailSettings->email_bcc));
        }
        if($mailSettings->email_cc != ''){
            $mail->addCc(explode(",", $mailSettings->email_cc));
        }
        $mail->setSubject('Forgot Password Notification');
        $mail->setFrom($mailSettings->email_from, $mailSettings->email_from_name);
        $mail->setBodyHtml($bodyText);
        $mailset = new Admin_Model_Mail();
        $mail->send($mailset->getTransport());
    }

    public function paypalReturnAction() {
        $order = new Model_Orders();
        /* ==================================================================
          PayPal Express Checkout Call
          ===================================================================
         */
        // Check to see if the Request object contains a variable named 'token'	
        $token = "";
        if (isset($_REQUEST['token'])) {
            $token = $_REQUEST['token'];
        }

        // If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.	
        if ($token != "") {

            $paypal = new Model_PaypalFunctions();

            /*
              '------------------------------------
              ' Calls the GetExpressCheckoutDetails API call
              '
              ' The GetShippingDetails function is defined in PayPalFunctions.jsp
              ' included at the top of this file.
              '-------------------------------------------------
             */


            $resArray = $paypal->GetShippingDetails($token);
            $ack = strtoupper($resArray["ACK"]);
            if ($ack == "SUCCESS" || $ack == "SUCESSWITHWARNING") {
                /*
                  ' The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review
                  ' page
                 */

                $email = $resArray["EMAIL"]; // ' Email address of payer.
                $payerId = $resArray["PAYERID"]; // ' Unique PayPal customer account identification number.
                $payerStatus = $resArray["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
                $salutation = $resArray["SALUTATION"]; // ' Payer's salutation.
                $firstName = $resArray["FIRSTNAME"]; // ' Payer's first name.
                $middleName = $resArray["MIDDLENAME"]; // ' Payer's middle name.
                $lastName = $resArray["LASTNAME"]; // ' Payer's last name.
                $suffix = $resArray["SUFFIX"]; // ' Payer's suffix.
                $cntryCode = $resArray["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
                $business = $resArray["BUSINESS"]; // ' Payer's business name.
                $shipToName = $resArray["SHIPTONAME"]; // ' Person's name associated with this address.
                $shipToStreet = $resArray["SHIPTOSTREET"]; // ' First street address.
                $shipToStreet2 = $resArray["SHIPTOSTREET2"]; // ' Second street address.
                $shipToCity = $resArray["SHIPTOCITY"]; // ' Name of city.
                $shipToState = $resArray["SHIPTOSTATE"]; // ' State or province
                $shipToCntryCode = $resArray["SHIPTOCOUNTRYCODE"]; // ' Country code. 
                $shipToZip = $resArray["SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
                $addressStatus = $resArray["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal   
                $invoiceNumber = $resArray["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
                $phonNumber = $resArray["PHONENUM"]; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one. 

                if ($shipToCntryCode == 'AU') {//if paypal delivery address is australia
                    $cookie = $order->getCurrentOrderCookie();
                    if ($cookie != null && $order->exists($cookie)) {

                        //getting the order
                        $data_order = $order->loadOrder($cookie);
                        $data_order->customer_email = $email;
                        $data_order->customer_name = $firstName;
                        $data_order->customer_lname = $lastName;
                        $data_order->customer_phone = $phonNumber;
                        $data_order->customer_company = $business;
                        //creating the address
                        $modelAddress = new Model_Address();
                        $dataAddress = array();
                        $dataAddress['street'] = $shipToStreet . ' ' . $shipToStreet2;
                        $dataAddress['city'] = $shipToCity;
                        $dataAddress['state'] = $shipToState;
                        $dataAddress['country'] = $shipToCntryCode;
                        $dataAddress['customer_id'] = 0;
                        $dataAddress['postcode'] = $shipToZip;
                        $addressIdShipping = $modelAddress->addAddress($dataAddress);
                        //setting the billing as the shipping, it will be overrided if needed by the following if
                        $addressIdBilling = $addressIdShipping;
                        $data_order->billing_address_id = $addressIdBilling;
                        $data_order->delivery_address_id = $addressIdShipping;
                        $data_order->status = Model_Orders::$ORDER_STATUS_CHECKOUT;
                        $data_order->order_checkout = Date('Y-m-d h:i:s', mktime());
                        $data_order->order_processed = Date('Y-m-d h:i:s', mktime());
                        $data_order->payment_method_id = 7; //paypal
                        $data_order->save();


                        $returnPaypal = $this->processOrder();
                        if ($returnPaypal['success'] == true) {
                            $this->_redirect("/checkout/thank-you");
                        } else {
                            $this->render("paypal-error");
                        }
                    }
                } else {//if paypal delivery address is not australia
                    $this->_redirect('/shoppingcart/display-shoppingcart-only-australia');
                }
            } else {
                $this->paypalErrorNotification($resArray);
                
            }
        } else {
            $this->_redirect('/');
        }
    }

    private function paypalErrorNotification($resArray) {
        //Display a user friendly Error on the page using any of the following error information returned by PayPal
        $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
        $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
        $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
        $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

        $message = "GetExpressCheckoutDetails API call failed. " .
                "Detailed Error Message: " . $ErrorLongMsg .
                "Short Error Message: " . $ErrorShortMsg .
                "Error Code: " . $ErrorCode .
                "Error Severity Code: " . $ErrorSeverityCode;

        // create mail object
        $mail = new Zend_Mail('utf-8');

        // render view
        $bodyText = $message;


        // configure base stuff
        $mTo = "error@ziller.com.au";
        $mail->addTo($mTo);
        $mail->setSubject('Error reported from paypal in antimall');
        $mail->setFrom("admin@antimall.com.au", "Antimall Server");
        $mail->setBodyHtml($bodyText);
        $mailset = new Admin_Model_Mail();
        $mail->send($mailset->getTransport());

        $this->render("paypal-error");
    }

    /* public function paypalErrorAction(){
      $this->render("paypal-error");
      } */

    public function paypalCancelAction() {
        $this->_redirect('/shoppingcart/display-shoppingcart');
    }

    public function paypalCheckoutAction() {
        $this->indexAction();
        $this->view->paypal = true;
    }

}

?>
