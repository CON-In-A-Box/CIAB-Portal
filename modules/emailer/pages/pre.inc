<?php namespace mailer;

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once __DIR__."/../functions/emailer.inc";
$emailer = new \mailer\Email();

if (!email::allowed()) {
    goSite();
}

if (!empty($_REQUEST)) {
    $arguments = [
    'loadList' => FILTER_SANITIZE_STRING,
    'test' => FILTER_SANITIZE_STRING,
    'listCount' => FILTER_SANITIZE_NUMBER_INT,
    'accessControl' => FILTER_SANITIZE_STRING,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['loadList'])) {
        $l = $emailer->getTo($updateData['loadList']);
        print(json_encode($l));
        exit();
    }

    if (isset($updateData['listCount'])) {
        $c = $emailer->getToCount($updateData['listCount']);
        print($c);
        exit();
    }

    if (isset($updateData['test'])) {
        $c = $emailer->countList(base64_decode($updateData['test']));
        print($c);
        exit();
    }

    if (isset($updateData['accessControl'])) {
        $c = $emailer->getAccessControl($updateData['accessControl']);
        print(json_encode($c));
        exit();
    }
}

if (!empty($_POST)) {
    $arguments = [
    'updateList' => FILTER_SANITIZE_STRING,
    'send' => FILTER_SANITIZE_STRING,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    if (isset($updateData['updateList'])) {
        $data = json_decode(base64_decode($updateData['updateList']));
        $emailer->updateList($data);
        exit();
    }
    if (isset($updateData['send'])) {
        $source = str_replace(" ", "", $updateData['send']);
        $data = json_decode(urlsafe_b64decode($source));
        $emailer->send($data);
        print "done";
        exit();
    }
}
