<?php
require_once 'autoloader.php';

// Init Mailfire SDK object
$clientId = 123;
$clientHash = 'a1s2d3f4g5h6j7k8l';
$mf = new Mailfire($clientId, $clientHash);


// Send email
$typeId = 1; // letter id
$categoryId = $mf->push->getCategorySystem(); // system or trigger
$projectId = 1;
$email = 'test@example.com';

$user = [ // User info, if you know
    'name' => 'John',
    'age' => '22',
    'gender' => 'm',
    'language' => 'en',
    'country' => 'US',
    'platform_id' => $mf->user->getPlatformDesktop(),
    'vip' => 0,
    'photo' => 'http://example.com/somephotourl.jpg',
];

$data = [ // Data for letter
    'some' => 'hi',
    'letter' => 'John',
    'variables' => '!',
];

$meta = []; // Your additional data

$response = $mf->push->send($typeId, $categoryId, $projectId, $email, $user, $data, $meta);
var_dump($response);

// Check email
$result = $mf->email->check('test@example.com');
var_dump($result);

// Unsubscribe
$projectId = 1;
$user = $mf->user->getByEmail($email, $projectId);
$unsub = $mf->unsub->addBySettings($user);
var_dump($unsub);
$unsub = $mf->unsub->subscribe($user);
var_dump($unsub);


// create goals
$data = [
    [
        'email' => 'testmail@mail.com',
        'type' => 'some_type',
        'project_id' => 1,
        'mail_id' => 1233534556,
    ],
    [
        'email' => 'testmail1@mail.com',
        'type' => 'some_type1',
        'project_id' => 2,
        'mail_id' => 233534556,
    ]
];

$res = $mf->goal->createGoal($data);

// get unsubscribe reason
$result = $mf->unsub->getUnsubscribeReason(91,'test@exmaple.com');
/* $result
 array(1) {
  'result' => string(5) "many_persistent_errors"
}
*/
