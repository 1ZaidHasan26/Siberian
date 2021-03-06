<?php

use Siberian\Exception;

/**
 * Class Application_Controller_View_Abstract
 */
abstract class Application_Controller_View_Abstract extends Backoffice_Controller_Default
{
    /**
     * @var array
     */
    public $cache_triggers = [
        'save' => [
            'tags' => ['app_#APP_ID#'],
        ],
        'switchtoionic' => [
            'tags' => ['app_#APP_ID#'],
        ],
        'saveadvertising' => [
            'tags' => ['app_#APP_ID#'],
        ],
    ];

    /**
     *
     */
    public function loadAction()
    {
        $payload = [
            'title' => sprintf('%s > %s',
                __('Manage'),
                __('Application')
            ),
            'icon' => 'fa-mobile',
            'ionic_message' => __('If your app is already published on the stores, be sure you have sent an update with the Ionic version, and that this update has already been accepted, otherwise your app may be broken.')
        ];

        $this->_sendJson($payload);
    }

    public function findAction()
    {
        $request = $this->getRequest();
        if (Siberian\Version::is('SAE')) {
            $appId = 1;
            $application = Application_Model_Application::getInstance();
        } else {
            $appId = $request->getParam('app_id', null);
            $application = (new Application_Model_Application())->find($appId);
        }


        $admin = new Admin_Model_Admin();

        if (Siberian_Version::is('SAE')) {
            $admins = $admin->findAll()->toArray();
            $admin_owner = $admin;
            $admin_owner->setData(current($admins));
        } else {
            $admins = $admin->getAllApplicationAdmins($appId);
            $admin_owner = $application->getOwner();
        }

        $admin_list = [];
        foreach ($admins as $admin) {
            $_dataAdmin = $admin;
            $_dataAdmin['key'] = sha1($admin['firstname'] . $admin['admin_id']);

            $admin_list[] = $_dataAdmin;
        }

        $admin = [
            'name' => $admin_owner->getFirstname() . ' ' . $admin_owner->getLastname(),
            'email' => $admin_owner->getEmail(),
            'company' => $admin_owner->getCompany(),
            'phone' => $admin_owner->getPhone()
        ];


        $store_categories = Application_Model_Device_Ionic_Ios::getStoreCategeories();
        $devices = [];
        foreach ($application->getDevices() as $device) {
            $device->setName($device->getName());
            $device->setBrandName($device->getBrandName());
            $device->setStoreName($device->getStoreName());
            $device->setHasMissingInformation(
                !$device->getUseOurDeveloperAccount() &&
                (!$device->getDeveloperAccountUsername() || !$device->getDeveloperAccountPassword())
            );
            $data = $device->getData();

            $data['owner_admob_weight'] = (integer)$data['owner_admob_weight'];

            if ((int) $device->getTypeId() === 2) {
                try {
                    $data['versionCode'] = Application_Model_Device_Abstract::validatedVersion($device);
                } catch (\Exception $e) {
                    // Here we fix the version
                    $device->setVersion('1.0')->save();
                    $data['versionCode'] = Application_Model_Device_Abstract::validatedVersion($device);
                }
                $data['versionCode'] = Application_Model_Device_Abstract::formatVersionCode($data['versionCode']);
            }

            $devices[] = $data;
        }

        $data = [
            'admin' => $admin,
            'admin_list' => $admin_list,
            'app_store_icon' => $application->getAppStoreIcon(),
            'google_play_icon' => $application->getGooglePlayIcon(),
            'devices' => $devices,
            'url' => $application->getUrl(),
            'has_ios_certificate' => Push_Model_Certificate::getiOSCertificat($appId),
            'pem_infos' => Push_Model_Certificate::getInfos($appId),
        ];

        foreach ($store_categories as $name => $store_category) {
            if ($store_category->getId() == $application->getMainCategoryId()) {
                $data['main_category_name'] = $name;
            } else if ($store_category->getId() == $application->getSecondaryCategoryId()) {
                $data['secondary_category_name'] = $name;
            }
        }

        $data['bundle_id'] = $application->getBundleId();
        $data['package_name'] = $application->getPackageName();
        $data['is_active'] = $application->isActive();
        $data['is_locked'] = $application->isLocked();
        $data['pre_init'] = (boolean) $application->getPreInit();
        $data['disable_updates'] = (boolean) $application->getDisableUpdates();
        $data['can_be_published'] = $application->canBePublished();
        $data['owner_use_ads'] = (boolean) $application->getOwnerUseAds();

        if ($application->getFreeUntil()) {
            $data['free_until'] = datetime_to_format($application->getFreeUntil(), Zend_Date::DATE_SHORT);
        }
        $data['android_sdk'] = Application_Model_Tools::isAndroidSDKInstalled();
        $data['apk'] = Application_Model_ApkQueue::getPackages($appId);
        $data['apk_service'] = Application_Model_SourceQueue::getApkServiceStatus($appId);
        $data['zip'] = Application_Model_SourceQueue::getPackages($appId);
        $data['queued'] = Application_Model_Queue::getPosition($appId);
        $data['confirm_message_domain'] = __('If your app is already published, changing the URL key or domain will break it. You will have to republish it. Change it anyway?');

        $application->addData($data);

        $data = [
            'application' => $application->getData(),
            'statuses' => Application_Model_Device::getStatuses(),
            'design_codes' => Application_Model_Application::getDesignCodes()
        ];

        $data['application']['disable_battery_optimization'] = (boolean) $data['application']['disable_battery_optimization'];

        // Set ios Autopublish informations
        $appIosAutopublish = (new Application_Model_IosAutopublish())->find($appId, 'app_id');

        $languages = 'en';
        if ($lang = Siberian_Json::decode($appIosAutopublish->getLanguages())) {
            foreach ($lang as $code => $value) {
                if ($value) {
                    $languages = $code;
                    break;
                }
            }
        }

        // Sanitize vars
        if (isset($data['infos']['want_to_autopublish']) &&
            $data['infos']['want_to_autopublish'] === null) {
            $data['infos']['want_to_autopublish'] = false;
        }
        if (isset($data['infos']['itunes_login']) &&
            $data['infos']['itunes_login'] === null) {
            $data['infos']['itunes_login'] = '';
        }
        if (isset($data['infos']['itunes_password']) &&
            $data['infos']['itunes_password'] === null) {
            $data['infos']['itunes_password'] = '';
        }

        $accountType = 'non2fa';
        $itunesLogin = $appIosAutopublish->getItunesLogin();

        $isFilled = mb_strlen($appIosAutopublish->getCypheredCredentials()) > 0;

        $data['ios_publish_informations'] = [
            'id' => $appIosAutopublish->getId(),
            'want_to_autopublish' => $appIosAutopublish->getWantToAutopublish(),
            'account_type' => $accountType,
            'itunes_login' => $itunesLogin,
            'itunes_original_login' => $appIosAutopublish->getItunesOriginalLogin(),
            'itunes_password' => $isFilled ? Application_Model_IosAutopublish::$fakePassword : '',
            'has_ads' => (bool)$appIosAutopublish->getHasAds(),
            'has_bg_locate' => (bool)$appIosAutopublish->getHasBgLocate(),
            'has_audio' => (bool)$appIosAutopublish->getHasAudio(),
            'languages' => $languages,
            'last_start' => $appIosAutopublish->getLastStart(),
            'last_success' => $appIosAutopublish->getLastSuccess(),
            'last_finish' => $appIosAutopublish->getLastFinish(),
            'last_build_status' => $appIosAutopublish->getLastBuildStatus(),
            'last_builded_version' => $appIosAutopublish->getLastBuildedVersion(),
            'last_builded_ipa_link' => $appIosAutopublish->getLastBuildedIpaLink(),
            'error_message' => $appIosAutopublish->getErrorMessage(),
            'teams' => $appIosAutopublish->getTeamsArray(),
            'itcProviders' => $appIosAutopublish->getItcProvidersArray(),
            'selected_team' => $appIosAutopublish->getTeamId(),
            'selected_team_name' => $appIosAutopublish->getTeamName(),
            'selected_provider' => $appIosAutopublish->getItcProvider(),
            'password_filled' => $isFilled,
            'stats' => $appIosAutopublish->getStats(),
        ];

        $this->_sendJson($data);

    }

    public function saveAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getBodyParams();

            if (empty($data['app_id'])) {
                throw new Exception(__('An error occurred while saving. Please try again later.'));
            }

            // Be sure all apps are Ionc!
            $data['design_code'] = 'ionic';

            $application = (new Application_Model_Application())->find($data['app_id']);
            if (!$application ||
                !$application->getId()) {
                throw new Exception(__('An error occurred while saving. Please try again later.'));
            }

            if (!empty($data['key'])) {

                $moduleNames = array_map('strtolower', Zend_Controller_Front::getInstance()->getDispatcher()->getSortedModuleDirectories());
                if (in_array($data['key'], $moduleNames, true)) {
                    throw new Exception(__('Your domain key "%s" is not valid.', $data['key']));
                }

                $dummy = (new Application_Model_Application())->find($data['key'], 'key');
                if ($dummy &&
                    $dummy->getId() &&
                    $dummy->getId() !== $application->getId()) {
                    throw new Exception(__('The key is already used by another application.'));
                }
            } else {
                throw new Exception(__('The key cannot be empty.'));
            }

            if (!empty($data['domain'])) {

                $data['domain'] = str_replace(['http://', 'https://'], '', $data['domain']);

                $tmp_url = str_replace(['http://', 'https://'], '', $this->getRequest()->getBaseUrl());
                $tmp_url = current(explode('/', $tmp_url));

                $tmp_domain = explode('/', $data['domain']);
                $domain = current($tmp_domain);
                if (preg_match('/^(www.)?(' . $domain . ')/', $tmp_url)) {
                    throw new Exception(__("You can't use this domain."));
                }

                $domain_folder = next($tmp_domain);
                $moduleNames = array_map('strtolower', Zend_Controller_Front::getInstance()->getDispatcher()->getSortedModuleDirectories());
                if (in_array($domain_folder, $moduleNames, false)) {
                    throw new Exception(__('Your domain key \'%s\' is not valid.', $domain_folder));
                }

                if (!Zend_Uri::check('http://' . $data['domain'])) {
                    throw new Exception(__('Please enter a valid URL'));
                }

                $dummy = (new Application_Model_Application())->find($data['domain'], 'domain');
                if ($dummy &&
                    $dummy->getId() &&
                    ($dummy->getId() !== $application->getId())) {
                    throw new Exception('The domain is already used by another application.');
                }

            }

            if (!empty($data['package_name'])) {
                $application->setPackageName($data['package_name']);
            }

            if (!empty($data['bundle_id'])) {
                $application->setBundleId($data['bundle_id']);
            }

            if (empty($data['free_until'])) {
                $data['free_until'] = null;
            } else {
                $data['free_until'] = new Zend_Date($data['free_until'], 'MM/dd/yyyy');
                $data['free_until'] = $data['free_until']->toString('yyyy-MM-dd HH:mm:ss');
            }

            if (array_key_exists('disable_battery_optimization', $data)) {
                $val = filter_var($data['disable_battery_optimization'], FILTER_VALIDATE_BOOLEAN);
                $application->setDisableBatteryOptimization($val ? 1 : 0);

                unset($data['disable_battery_optimization']);
            }

            if (array_key_exists('disable_updates', $data)) {
                $val = filter_var($data['disable_updates'], FILTER_VALIDATE_BOOLEAN);
                $application->setDisableUpdates($val ? 1 : 0);

                unset($data['disable_updates']);
            }

            if (array_key_exists('pre_init', $data)) {
                $val = filter_var($data['pre_init'], FILTER_VALIDATE_BOOLEAN);
                $application->setData('pre_init', $val ? 1 : 0);

                unset($data['pre_init']);
            }

            $application->addData($data)->save();

            $payload = [
                'success' => true,
                'message' => __('Info successfully saved'),
                'bundle_id' => $application->getBundleId(),
                'package_name' => $application->getPackageName(),
                'url' => $application->getUrl(),
            ];

        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    public function switchionicAction()
    {
        if ($data = Zend_Json::decode($this->getRequest()->getRawBody())) {

            try {

                if (empty($data["app_id"])) {
                    throw new Exception(__("An error occurred while saving. Please try again later."));
                }

                if (isset($data["design_code"]) && $data["design_code"] != Application_Model_Application::DESIGN_CODE_IONIC) {
                    throw new Exception(__("You can't go back with Angular."));
                }

                $application = new Application_Model_Application();
                $application->find($data["app_id"]);

                if (!$application->getId()) {
                    throw new Siberian_Exception(__("Application id %s not found.", $data["app_id"]));
                }

                $application->setDesignCode(Application_Model_Application::DESIGN_CODE_IONIC);

                if ($design_id = $application->getDesignId()) {

                    $design = new Template_Model_Design();
                    $design->find($design_id);

                    if ($design->getId()) {
                        $application->setDesign($design);
                        Template_Model_Design::generateCss($application, false, false, true);
                    }

                }

                $application->save();

                $data = [
                    "success" => 1,
                    "message" => __("Your application is now switched to Ionic"),
                    "design_code" => "ionic",
                ];

            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }

            $this->_sendHtml($data);
        }
    }


    public function savedeviceAction()
    {
        try {
            $request = $this->getRequest();
            $values = $request->getBodyParams();

            if (empty($values['app_id']) || !is_array($values['devices']) || empty($values['devices'])) {
                throw new Exception('#783-01: ' . __('An error occurred while saving. Please try again later.'));
            }

            $application = (new Application_Model_Application())->find($values['app_id']);
            if (!$application && 
                !$application->getId()) {
                throw new Exception('#783-02: ' . __('An error occurred while saving. Please try again later.'));
            }

            $rVersionCode = null;
            foreach ($values['devices'] as $deviceData) {
                if (!empty($deviceData['store_url'])) {
                    if (stripos($deviceData['store_url'], 'https://') === false) {
                        $deviceData['store_url'] = 'https://' . $deviceData['store_url'];
                    }
                    if (!Zend_Uri::check($deviceData['store_url'])) {
                        throw new Exception(__('Please enter a correct URL for the %s store', $deviceData['name']));
                    }
                } else {
                    $deviceData['store_url'] = null;
                }

                $device = $application->getDevice($deviceData['type_id']);

                $currentVersion = $device->getVersion();
                if ($currentVersion !== $deviceData['version']) {
                    // Reset build number on version change!
                    $deviceData['build_number'] = 0;
                }

                if ((int) $deviceData['type_id'] === 2) {
                    $currentVersion = Application_Model_Device_Abstract::validatedVersion($device);
                    $newVersion = Application_Model_Device_Abstract::validatedVersion($device, $deviceData['version'], 1);

                    // Ask user to confirm intent!
                    if (($newVersion < $currentVersion) &&
                        !array_key_exists('confirm', $values)) {
                        throw new Exception(p__('application', 'The new version must be greater than the current one, please confirm if you really want to downgrade it.'), 100);
                    }

                    $rVersionCode = $newVersion;
                }

                $device
                    ->addData($deviceData)
                    ->save();
            }



            $payload = [
                'success' => true,
                'message' => __('Info successfully saved'),
                'versionCode' => Application_Model_Device_Abstract::formatVersionCode($rVersionCode),
            ];

        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];

            if ($e->getCode() === 100) {
                $payload['confirm'] = true;
                $payload['confirmText'] = p__('application', 'You are about to downgrade the version, after that you may not be able to update your Android application, please confirm if you are sure!');
            }
        }

        $this->_sendJson($payload);
    }

    public function saveadvertisingAction()
    {

        if ($data = Zend_Json::decode($this->getRequest()->getRawBody())) {

            try {

                if (empty($data["app_id"]) OR !is_array($data["devices"]) OR empty($data["devices"])) {
                    throw new Exception(__("An error occurred while saving. Please try again later."));
                }

                $application = new Application_Model_Application();
                $application->find($data["app_id"]);

                if (!$application->getId()) {
                    throw new Exception(__("An error occurred while saving. Please try again later."));
                }

                $data_app_to_save = [
                    "owner_use_ads" => $data["owner_use_ads"]
                ];

                $application->addData($data_app_to_save)->save();

                foreach ($data["devices"] as $deviceData) {
                    $device = $application->getDevice($deviceData["type_id"]);
                    $data_device_to_save = [
                        "owner_admob_id" => $deviceData["owner_admob_id"],
                        "owner_admob_interstitial_id" => $deviceData["owner_admob_interstitial_id"],
                        "owner_admob_type" => $deviceData["owner_admob_type"],
                        "owner_admob_weight" => $deviceData["owner_admob_weight"]
                    ];
                    $device->addData($data_device_to_save)->save();
                }

                $data = [
                    "success" => 1,
                    "message" => __("Info successfully saved")
                ];

            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }

            $this->_sendHtml($data);
        }
    }

    public function savebannerAction()
    {

        if ($data = Zend_Json::decode($this->getRequest()->getRawBody())) {

            try {

                if (empty($data["app_id"]) OR !is_array($data["devices"]) OR empty($data["devices"])) {
                    throw new Exception(__("An error occurred while saving. Please try again later."));
                }

                $application = new Application_Model_Application();
                $application->find($data["app_id"]);

                if (!$application->getId()) {
                    throw new Exception(__("An error occurred while saving. Please try again later."));
                }

                $data_app_to_save = [
                    "banner_title" => $data["banner_title"],
                    "banner_author" => $data["banner_author"],
                    "banner_button_label" => $data["banner_button_label"]
                ];

                $application->addData($data_app_to_save)->save();

                foreach ($data["devices"] as $deviceData) {
                    $device = $application->getDevice($deviceData["type_id"]);
                    $data_device_to_save = [
                        "banner_store_label" => $deviceData["banner_store_label"],
                        "banner_store_price" => $deviceData["banner_store_price"]
                    ];
                    $device->addData($data_device_to_save)->save();
                }

                $data = [
                    "success" => 1,
                    "message" => __("Info successfully saved")
                ];

            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }

            $this->_sendHtml($data);
        }
    }

    /**
     * @throws Siberian_Exception
     * @throws Zend_Exception
     * @throws \Siberian\Exception
     */
    public function downloadsourceAction()
    {
        $request = $this->getRequest();
        try {
            if (__getConfig('is_demo')) {
                throw new \Siberian\Exception(
                    __("This is a demo version, you can't download any source codes / APKs"));
            }

            $params = $request->getParams();
            if (empty($params)) {
                throw new \Siberian\Exception(__('Missing parameters for generation.'));
            }

            $application = new Application_Model_Application();

            if (empty($params['app_id']) OR empty($params['device_id'])) {
                throw new \Siberian\Exception('#908-00: ' . __('This application does not exist'));
            }

            $application->find($params['app_id']);
            if (!$application->getId()) {
                throw new \Siberian\Exception('#908-01: ' . __('This application does not exist'));
            }

            $mainDomain = __get('main_domain');
            if (empty($mainDomain)) {
                throw new \Siberian\Exception('#908-02: ' .
                    __('Main domain is required, you can set it in <b>Settings > General</b>'));
            }

            $application->setDesignCode('ionic');

            $application_id = $params['app_id'];
            $type = ($request->getParam('type') == 'apk') ? 'apk' : 'zip';
            $device = ($request->getParam('device_id') == 1) ? 'ios' : 'android';
            $noads = ($request->getParam('no_ads') == 1) ? 'noads' : '';
            $isApkService = $request->getParam('apk', false) === 'apk';
            $design_code = $request->getParam('design_code');
            $adminIdCredentials = $request->getParam('admin_id', 0);

            // Firebase Validation!
            if ($device === 'android') {
                $credentials = (new Push_Model_Firebase())
                    ->find($adminIdCredentials, 'admin_id');

                $credentials->checkFirebase();
            }

            if ($type == 'apk') {
                $queue = new Application_Model_ApkQueue();

                $queue->setAppId($application_id);
                $queue->setName($application->getName());
            } else {
                $queue = new Application_Model_SourceQueue();

                $queue->setAppId($application_id);
                $queue->setName($application->getName());
                $queue->setType($device . $noads);
                $queue->setDesignCode($design_code);
            }

            // New case for source to apk generator!
            if ($isApkService) {
                $queue->setIsApkService(1);
                $queue->setApkStatus('pending');
            }

            $queue->setHost($mainDomain);
            $queue->setUserId($this->getSession()->getBackofficeUserId());
            $queue->setUserType('backoffice');
            $queue->save();

            /** Fallback for SAE, or disabled cron */
            $reload = false;
            if (!Cron_Model_Cron::is_active()) {
                $cron = new Cron_Model_Cron();
                $value = ($type == 'apk') ? 'apkgenerator' : 'sources';
                $task = $cron->find($value, 'command');
                Siberian_Cache::__clearLocks();
                $siberian_cron = new Siberian_Cron();
                $siberian_cron->execute($task);
                $reload = true;
            }

            $more['apk'] = Application_Model_ApkQueue::getPackages($application->getId());
            $more['zip'] = Application_Model_SourceQueue::getPackages($application_id);
            $more['queued'] = Application_Model_Queue::getPosition($application_id);
            $more['apk_service'] = Application_Model_SourceQueue::getApkServiceStatus($application_id);

            $payload = [
                'success' => true,
                'message' => __('Application successfully queued for generation.'),
                'more' => $more,
                'reload' => $reload,
                'isApkService' => $isApkService,
            ];


        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }

    public function cancelqueueAction()
    {
        try {
            if ($data = $this->getRequest()->getParams()) {

                $application_id = $data['app_id'];
                $type = ($this->getRequest()->getParam("type") == "apk") ? "apk" : "zip";
                $device = ($this->getRequest()->getParam("device_id") == 1) ? "ios" : "android";
                $noads = ($this->getRequest()->getParam("no_ads") == 1) ? "noads" : "";

                Application_Model_Queue::cancel($application_id, $type, $device . $noads);

                $more["zip"] = Application_Model_SourceQueue::getPackages($application_id);
                $more["queued"] = Application_Model_Queue::getPosition($application_id);

                $data = [
                    "success" => 1,
                    "message" => __("Generation cancelled."),
                    "more" => $more,
                ];

            } else {
                $data = [
                    "error" => 1,
                    "message" => __("Missing parameters for cancellation."),
                ];
            }
        } catch (Exception $e) {
            $data = [
                "error" => 1,
                "message" => $e->getMessage(),
            ];
        }


        $this->_sendHtml($data);
    }

    public function uploadcertificateAction()
    {

        if ($app_id = $this->getRequest()->getParam('app_id')) {

            try {

                if (empty($_FILES) || empty($_FILES['file']['name'])) {
                    throw new Exception('No file has been sent');
                }

                if (\Siberian\Version::is('SAE')) {
                    $application = Application_Model_Application::getInstance();
                    $app_id = $application->getId();
                } else {
                    $application = (new Application_Model_Application())->find($app_id);
                    if (!$application ||
                        !$application->getId()) {
                        throw new Exception(__('An error occurred while saving. Please try again later.'));
                    }
                }


                $base_path = Core_Model_Directory::getBasePathTo("var/apps/iphone/");
                if (!is_dir($base_path)) mkdir($base_path, 0775, true);
                $path = Core_Model_Directory::getPathTo("var/apps/iphone/");
                $adapter = new Zend_File_Transfer_Adapter_Http();
                $adapter->setDestination(Core_Model_Directory::getTmpDirectory(true));

                if ($adapter->receive()) {

                    $file = $adapter->getFileInfo();

                    $certificat = new Push_Model_Certificate();
                    $certificat->find(['type' => 'ios', 'app_id' => $app_id]);

                    if (!$certificat->getId()) {
                        $certificat->setType("ios")
                            ->setAppId($app_id);
                    }

                    $new_name = uniqid("cert_") . ".pem";
                    if (!rename($file["file"]["tmp_name"], $base_path . $new_name)) {
                        throw new Exception(__("An error occurred while saving. Please try again later."));
                    }

                    $certificat->setPath($path . $new_name)
                        ->save();

                    $data = [
                        "success" => 1,
                        "pem_infos" => Push_Model_Certificate::getInfos($app_id),
                        "message" => __("The file has been successfully uploaded")
                    ];

                } else {
                    $messages = $adapter->getMessages();
                    if (!empty($messages)) {
                        $message = implode("\n", $messages);
                    } else {
                        $message = __("An error occurred during the process. Please try again later.");
                    }

                    throw new Exception($message);
                }
            } catch (Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }

            $this->_sendHtml($data);

        }

    }

}
