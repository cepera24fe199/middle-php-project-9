<?php

session_start();

$createApp = require __DIR__ . '/createApp.php';

return $createApp();
