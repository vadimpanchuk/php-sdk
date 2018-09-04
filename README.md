# Mailfire PHP SDK

[![Latest Stable Version](https://poser.pugx.org/mailfire/php-sdk/v/stable)](https://packagist.org/packages/mailfire/php-sdk)
[![Total Downloads](https://poser.pugx.org/mailfire/php-sdk/downloads)](https://packagist.org/packages/mailfire/php-sdk)

PHP SDK for https://mailfire.io

## Installing
via Composer https://getcomposer.org/download
```sh
composer require mailfire/php-sdk
```
or download lib & include files in dir:
```php
require_once 'autoloader.php';
```

# Quick start: sending email
```php
$clientId = 123;
$clientHash = 'a1s2d3f4g5h6j7k8l';

// Init main object with access credentials
$mf = new Mailfire($clientId, $clientHash);

// You can enable Exceptions for sdk fails
// $mf->errorHandler->setErrorMode(MailfireErrorHandler::MODE_EXCEPTION);

// Required params for letter
$typeId = 1; // letter id (aka type_id)
$categoryId = $mf->push->getCategorySystem(); // system or trigger
$projectId = 1; // in your admin panel
$email = 'test@example.com'; // for matching user

// Variables for letter
$data = [ // Data for letter
    'some' => 'hi',
    'letter' => 'John',
    'variables' => '!',
];

// User info, that will be saved [not required]
$user = [
    'name' => 'John',
    'age' => '22',
    'gender' => 'm',
    'language' => 'en',
    'country' => 'US',
    'platform_id' => $mf->user->getPlatformDesktop(),
    'vip' => 0,
    'photo' => 'http://example.com/somephotourl.jpg',
    'channel_id' => 42,
    'subchannel_id' => 298,
    'client_user_id' => '123xyz'
];
// Your data, that will be sent with our webhooks
$meta = [
    'tracking_id' => 72348234,
];

// Sending
$response = $mf->push->send($typeId, $categoryId, $projectId, $email, $user, $data, $meta);
// it will make POST to /push/system or /push/trigger with json http://pastebin.com/raw/Dy3VeZpB

var_dump($response);
// 
```
# Other API methods
## Check email
```php
// Make POST /email/check with json {"email":"Test@Example.com"}
$result = $mf->email->check('Test@Example.com');
/* Returned array(
  'orig' => 'Test@Example.com',
  'valid' => false, // result
  'reason' => 'mx_record', // reason of result
  'email' => 'test@example.com', // fixed email
  'vendor' => 'Unknown', // vendor name like Gmail
  'domain' => 'example.com',
  'trusted' => false,
) */
```

## Validate email
```php

$projectId = 1;
$email = 'test@example.com';
$typeId = 1;

$result = $mf->email->validate($projectId, $email, $typeId);
/* Returned array(
  'code' => ...,
  'text' => ...,
) */

CODES:

EMAIL_VALIDATION_STATUS_PASSED = 1;
EMAIL_VALIDATION_STATUS_INVALID = 2;
EMAIL_VALIDATION_STATUS_SERVER_ERROR = 3;

```

## User info
```php
$projectId = 1;
// Make GET to /user/project/PROJECT_ID/email/Test@Example.com
$user = $mf->user->getByEmail('Test@Example.com', $projectId);
/* Returned array(
    "id":8424,
    "project_id":1,
    "email":"test@example.com",
    "name":"John",
    "gender":"m",
    "country":"UKR",
    "language":"en",
    "last_online":"2016-06-17 12:59:19",
    "last_reaction":"2016-06-17 12:59:19",
    "last_mailed":"2016-06-22 12:06:45",
    "last_request":"2016-06-03 04:01:19",
    "activation":"2016-01-30 11:28:24",
    ...
) */
```

## Unsubscribe
```php
$projectId = 1;
$user = $mf->user->getByEmail('test@example.com', $projectId);
// Make POST to /unsub/USER_ID/source/9
$unsub = $mf->unsub->addBySettings($user);
var_dump($unsub);
```

## Subscribe back
```php
$projectId = 1;
$user = $mf->user->getByEmail('test@example.com', $projectId);
// Make DELETE to /unsub/USER_ID
$unsub = $mf->unsub->subscribe($user);
var_dump($unsub);
```

## Unsubscribe from types
```php
$projectId = 1;
$user = $mf->user->getByEmail('test@example.com', $projectId);
$list = $mf->unsubTypes->getList($user);
var_dump($list);
//array(2) {
//  [0] =>
//  array(3) {
//    'type_id' =>
//   int(3)
//    'unsubscribed' =>
//    bool(false)
//    'name' =>
//    string(11) "Popular now"
//  }
//  [1] =>
//  array(3) {
//    'type_id' =>
//    int(4)
//    'unsubscribed' =>
//    bool(false)
//    'name' =>
//    string(8) "Breaking"
//  }
//}

$result = $mf->unsubTypes->setDisabledTypes(12, [4]); //subscribes
//for all active type_id`s except 4

$mf->unsubTypes->addTypes(12, [4]); //unsubscribe user from type 4
$mf->unsubTypes->removeTypes(12, [4]); //subscribe user for type 4
$mf->unsubTypes->removeAll(12); //subscribe user for all types

```

## Unsubscribe by admin

```php
$projectId = 123;
$result = $mf->unsub->unsubByAdmin('test@example.com',$projectId);

/*
success result
array(1) {
  'unsub' => bool(true)
}
error result (already unsubscribed)
array(1) {
  'unsub' => bool(false)
}
*/
```

## Check is unsubscribed
By user:
```php
$projectId = 1;
$user = $mf->user->getByEmail('test@example.com', $projectId);
$unsub = $mf->unsub->isUnsubByUser($user); // Returns false(if not unsubscribed) or unsub data
```
By email and project:
```php
$projectId = 1;
$unsub = $mf->unsub->isUnsubByEmailAndProjectId('test@example.com', $projectId); // Returns false(if not unsubscribed) or unsub data
```

## Get unsubscribe reason

```php
$projectId = 123;
$result = $mf->unsub->getUnsubscribeReason('test@example.com',$projectId);

//user does not unsubscribed
array(1) {
  'result' => bool(false)
}

//reason for the unsubscription is unknown
array(1) {
  'result' => string(7) "Unknown"
}

//success result
array(1) {
  'result' => string(5) "admin"
}


```

## Get unsubscribed list

```php
<?php

$result = $mf->unsub->getByDate('2018-06-10');

//Response example
return [
    0 => [
        'email' => "jo23lu56@gmail.com",
        'project_id' => 9,
        'client_user_id' => NULL,
        'source_id' => 9,
        'created_at' => "2018-02-27 20:31:45"
    ],
    // ...
];

```

## Send push notification
```php
//select user
$user = $mf->user->getByEmail('someone@example.com', 2);
//webpush data
$title = 'Webpush title';
$text = 'My awesome text';
$url = 'https://myproject.com/show/42';
$iconUrl = 'https://static.myproject.com/6hgf5ewwfoj6';
$typeId = 6;
//send
$mf->webpush->sendByUser($user, $title, $text, $url, $iconUrl, $typeId);
```

## Send push notification to all project users
```php
//webpush data
$projectId = 1;
$title = 'Webpush title';
$text = 'My awesome text';
$url = 'https://myproject.com/show/42';
$iconUrl = 'https://static.myproject.com/6hgf5ewwfoj6';
$typeId = 6;
//send
$mf->webpush->sendByProject($projectId, $title, $text, $url, $iconUrl, $typeId);
```

## Unsubscribe/Subscribe user for push notifications
```php
//select user
$user = $mf->user->getByEmail('someone@example.com', 2);
//unsubscribe
$mf->webpush->unsubscribeByUser($user);
//subscribe back
$mf->webpush->subscribeByUser($user);
//unsubscribe by push user id
$pushUserId = 123;
$mf->webpush->unsubscribeByPushUser($pushUserId);
```

## Create and update user data
```php
$fields = [
    'name' => 'John Dou',
    'gender' => 'm', //m or f
    'age' => 21, //int
    'photo' => 'http://moheban-ahlebeit.com/images/Face-Wallpaper/Face-Wallpaper-26.jpg',//image url
    'ak' => 'FFZxYfCfGgNDvmZRqnELYqU7',//Auth key
    'vip' => 1, //int
    'language' => 'es', //ISO 639-1
    'country' => 'esp', //ISO 3166-1 alpha-3 or ISO 3166-1 alpha-2
    'platform_id' => $mf->user->getPlatformDesktop(),
    'list_id' => 1,
    'status' => 0, //int
    'partner_id' => 1, //int

    // Your own custom fields may be here
    // allowed only int values
    'field1' => 542, //int
    'sessions_count' => 22, //int
    'session_last' => 1498137772, //unix timestamp
];
```
By email and project ID

```php
$result = $mf->user->setUserFieldsByEmailAndProjectId('ercling@yandex.ru', 2, $fields);
// $result is a boolean status
```

By user

```php
$user = $mf->user->getById(892396028);
$result = $mf->user->setUserFieldsByUser($user, $fields);
// $result is a boolean status
```

## Create and update payment data
```php
$startDate = 1509617696;
$expireDate = 1609617696; //optional
$paymentCount = 14; //optional
```
By email and project ID

```php
$result = $mf->user->addPaymentByEmailAndProjectId('ercling@yandex.ru', 2, $startDate, $expireDate, $paymentCount);
// $result is a boolean status
```

By user

```php
$user = $mf->user->getById(892396028);
$result = $mf->user->addPaymentByUser($user, $startDate, $expireDate, $paymentCount);
// $result is a boolean status
```

Attempt to send incorrect data
```php
$mf = new \Mailfire(3,'GH3ir1ZDMjRkNzg4MzgzE3MjU');
$fields = [
    'language' => 'ua',
    'gender' => 'male',
    'vip' => 'yes',
];
$result = $mf->user->setUserFieldsByEmailAndProjectId('ercling@yandex.ru', 2, $fields);
if (!$result){
    var_dump($mf->request->getLastResponse()->getData());
}

//array(3) {
//  'errorCode' =>
//  int(409)
//  'message' =>
//  string(16) "Validation error"
//  'errors' =>
//  array(3) {
//    'language' =>
//    string(45) "Field language is not valid language code: ua"
//    'gender' =>
//    string(41) "Field gender must be a part of list: m, f"
//    'vip' =>
//    string(44) "Field vip does not match the required format"
//  }
//}
```

## Update user online

By user

```php
$result = $mf->user->setOnlineByUser($user, new \DateTime()); //Accepted
```

By email and project ID
```php
$result = $mf->user->setOnlineByEmailAndProjectId('ercling@gmail.com', 1, new \DateTime());
```

## Get user custom fields

By email and project ID

```php
$result = $mf->user->getUserFieldsByEmailAndProjectId('ercling@yandex.ru', 1);
/*
Returns [
    'user' => [
        'id' => 892396028,
        'project_id' => 1,
         ...
    ],
    'custom_fields' => [
        'sessions_count' => 22,
         ...
    ],
]
*/
```

By user

```php
$user = $mf->user->getById(892396028);
$result = $mf->user->getUserFieldsByUser($user);
/*
Returns [
    'user' => [
        'id' => 892396028,
        'project_id' => 1,
         ...
    ],
    'custom_fields' => [
        'sessions_count' => 22,
         ...
    ],
]
*/
```

## Send goals using sdk

```php
$data = [
    [
        'email' => 'someone@example.com',
        'type' => 'some_type',
        'project_id' => 123,
        'mail_id' => '123123123',
    ],
    [
        'email' => 'someone1@example.com',
        'type' => 'some_type',
        'project_id' => 345,
        'mail_id' => '345345345',
    ]];

$res = $mf->goal->createGoal($data);





```

Success response

```php
/*
array(1) {
  'goals_added' => int(2)
}
*/
```


Error response

```php
/*
array(3) {
  'goals_added' =>
  int(0)
  [0] =>
  array(4) {
    'error_messages' =>
    array(1) {
      [0] =>
      string(25) "Parameter type is invalid"
    }
    'errorCode' =>
    int(409)
    'message' =>
    string(16) "Validation error"
    'goal_data' =>
    string(39) "somemail@example.com;<h1>;123;123123123"
  }
  [1] =>
  array(4) {
    'error_messages' =>
    array(1) {
      [0] =>
      string(26) "Parameter email is invalid"
    }
    'errorCode' =>
    int(409)
    'message' =>
    string(16) "Validation error"
    'goal_data' =>
    string(46) "somem@ail1@example.com;some_type;345;345345345"
  }
}
*/
```




## Send goals without sdk

```php
POST https://api.mailfire.io/v1/goals
params: {
            'type' : 'contact',
            'email' : 'andrey.reinwald@corp.flirchi.com', 
            'project_id' : 30,
            'mail_id' : 2739212714|null
        }
```

Request format

Name | Type | Description
-------|------|-------
`type`|`string`| **Required.** Goal type
`email`|`string`| **Required.** User email 
`project_id`|`int`| **Required.** Id of your project. You can find it at https://admin.mailfire.io/account/projects 
`mail_id`|`int`| Mail id after which the user made a goal

# Application pushes

$token - application token from Firebase server

$uid - unique identifier of users device

Send data notification 

```php
$message = ['title' => 'Hello'];

$message = jsone_encode($message);

$result = $mf->appPush->send($project, $uid, $message);
```

Create user

```php
$result = $mf->appPush->createPushUser($project, $token, $uid, $userId = null);
// $result is id of created user
```

Refresh token

```php
$result = $mf->appPush->refreshToken($project, $token, $uid);
// $result is a boolean status
```

Track show

```php
$created - show time in timestamp format
$result = $mf->appPush->trackShow($project, $uid, $pushId, $created);
// $result is a boolean status
```

Track click

```php
$created - click time in timestamp format
$result = $mf->appPush->trackClick($project, $uid, $pushId, $created);
// $result is a boolean status
```

Update online

```php
$result = $mf->appPush->updateOnline($project, $uid);
// $result is a boolean status
```

# Content

$entityId - ID of entity which user watched

$uid - unique identifier of users device

Track show

```php
$result = $mf->content->trackShow($project, $uid, $entityId = null);
// $result is a boolean status
```

# Force confirm update
```php
$result = $mf->user->forceConfirmByEmailAndProject($email, $projectId);
// $result == 'Accepted'; if successful
```

## Get response (if $result === false)
```php
$response = $mf->request->getLastResponse()->getData();

//array(3) {
//  'errorCode' =>
//  int(409)
//  'message' =>
//  string(16) "Validation error"
//  'errors' =>
//  array(1) {
//    'field_name' =>
//    string(29) "Can't find user field: field2"
//  }
//}
```

## Error handling
By default any error messages (except InvalidArgumentException in Mailfire constructor) collects in error_log.
If you want the component throws exceptions just change handler mode:
```php
$mf = new Mailfire($clientId, $clientHash);
$mf->errorHandler->setErrorMode(MailfireErrorHandler::MODE_EXCEPTION);
```

## Set curl options for single request
```php
$mf = new Mailfire($clientId, $clientHash);
$mf->request->setOption(CURLOPT_TIMEOUT, 2);
```

## Set curl options for multiple requests (permanent)
```php
$mf->request->setOption(CURLOPT_TIMEOUT_MS, 2000, true);
```

## Reset permanent curl options
```php
$mf->request->resetPermanentOptions();
```

# HOW TO RUN THE TESTS
Make sure you have PHPUnit installed.

Run PHPUnit in the mailfire repo base directory.
```bash
./test.sh
```
