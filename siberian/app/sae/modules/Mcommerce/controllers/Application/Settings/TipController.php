<?php

class Mcommerce_Application_Settings_TipController extends Application_Controller_Default_Ajax {

    public function saveAction() {

        if ($datas = $this->getRequest()->getPost()) {

            try {

                $mcommerce = new Mcommerce_Model_Mcommerce();
                $mcommerce->find(["value_id" => $datas["option_value_id"]]);

                $add_tip = $datas["add_tip"] ? "true" : "false";

                $mcommerce->setAddTip($add_tip);

                $html = [
                    'success' => '1',
                    'success_message' => $this->_('Settings successfully saved'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                ];

            } catch (Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);

        }

    }

}