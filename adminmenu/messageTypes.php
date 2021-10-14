<?php

$options = [];

$option = new stdClass;
$option->cName = __('SMS');
$option->cWert = \Plugin\sms77_jtl5\lib\MessageType::SMS;
$option->nSort = 1;
$options[] = $option;

$option = new stdClass;
$option->cName = __('Voice');
$option->cWert = \Plugin\sms77_jtl5\lib\MessageType::VOICE;
$option->nSort = 2;
$options[] = $option;

$option = new stdClass;
$option->cName = __('Deaktiviert');
$option->cWert = \Plugin\sms77_jtl5\lib\MessageType::NONE;
$option->nSort = 3;
$options[] = $option;

return $options;
