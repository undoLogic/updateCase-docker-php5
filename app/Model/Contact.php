<?php class Contact extends AppModel {

    var $name = "Contact";

    var $useTable = false;

    public $validate = array(
//        'login' => array(
//            'alphaNumeric' => array(
//                'rule' => 'alphaNumeric',
//                'required' => true,
//                'message' => 'Letters and numbers only'
//            ),
//            'between' => array(
//                'rule' => array('lengthBetween', 5, 15),
//                'message' => 'Between 5 to 15 characters'
//            )
//        ),
//        'password' => array(
//            'rule' => array('minLength', '8'),
//            'message' => 'Minimum 8 characters long'
//        ),
        'email' => 'email',
        'name' => array(
            'rule' => 'alphaNumeric',
            'message' => 'Enter your name',
            'allowEmpty' => false
        )
    );

}