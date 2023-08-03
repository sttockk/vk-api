<?php

use MyProject\Library\Vk;

require_once __DIR__ . '/MyProject/Library/Vk.php';

$access_token = '';
$group_id = '';

try {
    $vk = new Vk($access_token, $group_id);
    $vk->getMembersId();
} catch (Exception $e) {
    echo $e->getMessage();
}
