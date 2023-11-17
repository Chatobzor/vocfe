<?php
require_once("../../inc_common.php");
#for determining design:
include($engine_path."users_get_list.php");

if (!defined("_COMMON_")) {echo "stop";exit;}


header('Location: '.$chat_url.'pictures.php?session='.$session.'&cid=FAV&display=frame');