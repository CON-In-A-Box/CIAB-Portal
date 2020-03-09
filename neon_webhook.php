<?php

require(__DIR__.'/functions/functions.inc');
require_once(__DIR__.'/functions/users.inc');

$json = file_get_contents('php://input');
$event = json_decode($json);

$aid = $event->data->individualAccount->accountId;
lookup_user_by_id($aid);
