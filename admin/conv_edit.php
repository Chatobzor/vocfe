<?php

error_reporting(E_ALL & ~E_NOTICE);

require_once "check_session.php";
require_once "../inc_common.php";

header('Location: conv.php?session='.$session);