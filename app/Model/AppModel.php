<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
    class AppModel extends Model {
        var $actsAs = array('Containable');
        function __construct($id = false, $table = null, $ds = null) {

            //add to the bootstrap
            //Configure::write('localServer', array('localhost'));
            //Configure::write('liveServer', array('www.domain.com','domain.com'));
            if (!Configure::read('liveServer')) die('add live and local server to bootstrap: app_model.php');

//            if (in_array($_SERVER['HTTP_HOST'], Configure::read('liveServer'))) {
//                //we are live, so we only want to use the live server
//                $this->useDbConfig = 'live';
//            }
//            /* if you want to define a local db
//            elseif (in_array($_SERVER['HTTP_HOST'], Configure::read('localServer') )) {
//                //this is the server, so we only want to us the local server
//                $this->useDbConfig = 'local';
//            } */
//            else {
//
//                //allows you to decide which db to use
//                $this->useDbConfig = 'undotest';
//                //$this->useDbConfig = 'local';
//            }

            Configure::write('DbConfig', $this->useDbConfig);
            parent::__construct($id, $table, $ds);
        }
    }
