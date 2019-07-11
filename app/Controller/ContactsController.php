<?php
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

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ContactsController extends AppController {

    var $helpers = array('System.SetupCase');

    function beforeFilter() {
        parent::beforeFilter();
    }

    /**
     * Not database connection
     */
    function form() {

        Configure::write("SetupCase.slug", "Contact");

        if (!empty($this->request->data)) {

            $this->request->data['Contact']['ip'] = $this->getIp();

            $this->Contact->set($this->data);
            if ($this->Contact->validates()) {
                // Data Validated

                //pr ($this->request->data);exit;
                $this->set('hideForm', true);

                $to = Configure::read('email.to');
                $vars = $this->request->data['Contact'];

                if ($this->send($to, $vars, 'Website Submission', $template = 'default')) {
                    $this->Session->setFlash('Message Sent');

                } else {
                    $this->Session->setFlash('Saved... but could not send email');
                }

            } else {
                // Data Not Validated
                $this->Session->setFlash('Could not send... check required fields');
            }

        }
        $this->set('link_contact', true);



        $this->layout = 'layout_02';
        $this->render('layout02/form');

}


    /**
     * With a database connection
     */
    function index() {

        if (!empty($this->request->data)) {

            $this->request->data['Contact']['ip'] = $this->getIp();

            //if ($this->Contact->save($this->request->data)) {

            if ($this->Contact->validates()) {

            }

                $this->set('hideForm', true);

                $to = Configure::read('email.to');
                $vars = $this->request->data['Contact'];

                if ($this->send($to, $vars, 'Website Submission', $template = 'default')) {
                    $this->Session->setFlash('Message Sent');

                } else {
                    $this->Session->setFlash('Saved... but could not send email');
                }

//            } else {
//                $this->Session->setFlash('Could not send... check required fields');
//            }
        }
        $this->set('link_contact', true);



        $this->render('layout02/contact');
    }






    private function getIp () {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }





}