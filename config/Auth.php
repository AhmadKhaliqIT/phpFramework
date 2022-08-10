<?php
/* بسم الله الرحمن الرحیم */
/**
 * phpFramework
 *
 * @author     Ahmad Khaliq
 * @author     Mojtaba Zadegi
 * @copyright  2022 Ahmad Khaliq
 * @license    https://github.com/AhmadKhaliqIT/phpFramework/blob/main/LICENSE
 * @link       https://github.com/AhmadKhaliqIT/phpFramework/
 */



return [
    'guards' => [
        [
            'name' => 'Admin',
            'table'=> 'admins',
            'login_route'=> 'auth_admins_login'
        ],
        [
            'name' => 'Account',
            'table'=> 'accounts',
            'login_route'=> 'auth_accounts_login'
        ],
    ]
];
