<?php
/*
 * First install the plugin
 * Right click on 'Plugin' -> subverions -> edit properties -> add property -> https://wush.net/svn/undologic/common/system/trunk System
 */

App::uses('Controller', 'Controller');

App::uses('CakeEmail', 'Network/Email');


class AppController extends Controller {

    var $components = array(
        //'System.Offline',
        'Session', 'Cookie',
        'Language',
        'Secure'
    );

    //var $helpers = array("System.SetupCase");

    function beforeFilter() {

        $this->setupLanguage();

        $this->Secure->requirePasswordForTestingSites($_SERVER, $this->Session);

        //require_once(APP.'webroot'.DS.'updateCase.php');$this->updateCase = new UpdateCase;$this->set('updateCase', $this->updateCase);
        //Configure::write("UpdateCase.variant_id", 183);

    }

    function browserCheckLang() {

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $usersLang = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
            $test = substr($usersLang, 0, 2);

            if ($test == 'en') {
                $lang = 'en-us';
            } else {
                $lang = 'fr-ca';
            }
        } else {
            $lang = 'fr-ca';
        }

        return $lang;

    }



    function send($to, $vars, $subject, $template = 'default') {

        $Email = new CakeEmail();
        $Email->viewVars(array(
            'subject' => $subject,
            'vars' => $vars,
            'domain' => Router::url("/", TRUE)
        ));
        $Email->config('smtp');


        if (isset($vars['email'])) {
            $Email->from($vars['email']);
        }
        //pr ($Email);exit;

        $Email->to($to);

        $Email->bcc(array('test@undologic.com'));

//        $from = Configure::read('Email.from');
//        if (empty($from)) die ("add to bootstrap: Configure::write('Email.from', 'info@undologic.com');");
//
        $Email->subject($subject);
        $Email->emailFormat('html');

        $Email->template($template);

        //pr ($Email);exit;

        $sent = $Email->send();

        if ($sent) {
            return TRUE;
        } else {
            die ('could not email ');
            return FALSE;
        }
    }


    function setFrench() {
        $this->Language->setCurrLang('fre', $this->Session, $this->Cookie);
    }

    function setEnglish() {
        $this->Language->setCurrLang('eng', $this->Session, $this->Cookie);
        $this->Cookie->write('currLang', 'eng', NULL, '+350 day');
    }

    function currentLang() {
        $currLang = $this->Language->currLang();
        return $currLang;
    }

    function isFrench() {
        $currLang = $this->Language->currLang();
        if ($currLang == 'fre') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function setupLanguage() {



        //language stuff
        //does get info exist, this will be priority
        if (isset($_GET[ 'Lang' ])) {
            $this->Language->setGet($_GET[ 'Lang' ]);
        }
        //if there params of what language we should be using
        if (isset($this->params[ 'language' ])) {
            $this->Language->setParams($this->params[ 'language' ]);
        }
        //or we are going to check out session of cookie for a already selected language
        $this->Language->setSession($this->Session);
        $this->Language->setCookie($this->Cookie);
        //and fall back to the default if not set yet
        $this->Language->setDefaultLanguage(Configure::read('Config.language'));
        $currLang = $this->Language->currLang();

        $this->Language->setCurrLang($currLang);

        $currLang = 'eng';

        switch ($currLang) {
            case 'fre':

                $this->set('langFR', TRUE);
                $this->set('lang', 'fr');
                $this->set('currLang', $currLang);
                $this->Cookie->write('currLang', 'fre', NULL, '+350 day');
                Configure::write('Config.language', 'fre');
                Configure::write("UpdateCase.language", "fre"); //define the language in app_controller / globally
                break;
            default:
                $this->set('lang', 'en');
                $this->set('langEN', TRUE);
                $this->set('currLang', $currLang);
                $this->Cookie->write('currLang', 'eng', NULL, '+350 day');
                Configure::write('Config.language', 'eng');
                Configure::write("UpdateCase.language", "eng"); //define the language in app_controller / globally
        }
    }





}
