<?php
/*********
 *
 * How to use SetupCase
 *
 * in the app_controller in var $helpers = array("System.SetupCase");
 * in the app_controller in beforeFilter() { Configure::write("SetupCase.language", "eng"); }
 * place in each action of controller
Configure::write("SetupCase.slug", "Home");
Configure::write("SetupCase.siteName", "Template"); //this is the json file without .json
 * In layout remove the title tag and add
<?= $this->Element('System.SetupCase/meta'); ?>
 * then in the view add the following to initialize the slug
<?php $this->SetupCase->loadPageBySlug(Configure::read("SetupCase.slug")); ?>
 * Then you can swap your content with these elements to call the text
 *
 * GET A SINGLE BLOCK OF TEXT (USING GROUPNAME WILL LIMIT TO THE GROUP YOU ADDED)
<?= $this->SetupCase->getContentBy("LOCATION", "ELEMENT"); ?>
<?= $this->SetupCase->getContentBy("LOCATION", "ELEMENT", 'GROUPNAME'); ?>

 * GET AN URL I.E. FOR IMAGE SCR OR LINKS
<?= $this->SetupCase->getUrlBy("LOCATION", "ELEMENT"); ?>
<?= $this->SetupCase->getUrlBy("LOCATION", "ELEMENT", "GROUPNAME"); ?>
 *
 * LOOP THROUGH REPEATING (only change LOCATION & ELEMENT)
<?php $groups = $this->SetupCase->getGroupNamesByLocation("LOCATION"); ?>
<?php foreach ($groups as $group): ?>
<?= $this->SetupCase->getContentBy("LOCATION", "ELEMENT", $group); ?>
<?= $this->SetupCase->getUrlBy("LOCATION", "ELEMENT", $group); ?>
<?php endforeach; ?>
 *
 *
 *
 *
 * //IF YOU WANT TO ENABLE TESTMODE
 * //TEST mode, add to your page without elements created in SetupCase, but will use live elements if they are available
 * So this way you can add all elements and setTestConent... then client can add elements and they will be replaced
 * ADD TO app/Config/bootstrap.php
 * Configure::write("SetupCase.enableTestMode", TRUE);
 *
 * <?php $this->SetupCase->setTestContent("This text will appear on the next element once"); ?>
 * OR
 * <?php $this->SetupCase->setTestContent("http://website.com/images/imageGoesHere.jpg"); ?>
 *
 * Example: <?php $this->SetupCase->setTestContent($this->webroot."img/slide2.jpg"); ?>
 *
 * If you have multiple variants (not really used anymore as now only one variant is sent)
 * <?php $this->SetupCase->chooseVariantName("nameOfVariant"); ?>
 * Then you need to load the current SLUG you want to display
 * <?php $this->SetupCase->loadPageBySlug("SLUG_IN_SETUPCASE.COM"); ?>
 * <?= $this->SetupCase->getContentBy("LOCATION", "ELEMENT", "GROUPNAME{optional}"); ?>
 * <?= $this->SetupCase->getUrlBy("LOCATION", "ELEMENT", "GROUPNAME{optional}"); ?>
 * <?= $this->SetupCase->getTextOnlyBy("LOCATION", "ELEMENT", "GROUPNAME{optional}"); ?>
 *
 * Blog styles, allows you to loop through content on setupCase and display unlimited number of elements.
 *
 * <?php $this->SetupCase->setTestGroupNames(array(1,2,3));?>

 * <?php $groups = $this->SetupCase->getGroupNamesByLocation("LOCATION"); ?>
 * <?php foreach ($groups as $group): ?>
 * <?= $this->SetupCase->getContentBy("LOCATION", "ELEMENT", $group); ?>
 * <?= $this->SetupCase->getUrlBy("LOCATION", "ELEMENT", $group); ?>
 * <?php endforeach; ?>
 *
 */





/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('CakeEmail', 'Network/Email');
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

    function beforeFilter() {
        parent::beforeFilter();
    }

    /**
     * Redirect as per the language settings on your computer
     */
    function index() {

        $lang = $this->browserCheckLang();
        //pr ($lang);
        //exit;
        if ($lang=='en-us') {
            $this->redirect(array('language' => 'eng', 'action' => 'home'));
        } else {
            $this->redirect(array('language' => 'fre', 'action' => 'home'));
        }
    }

    function home() {
        $this->set('largeBanner', true);
        $this->set('langInMenu', true);

        Configure::write("UpdateCase.slug", 'Home');

    }


    function contactUs() {

        Configure::write("UpdateCase.slug", "ContactUs");

        $this->set('link_contactUs', true);

    }



    function styles() {
    }



























}