<?php

function do_hash($str, $salt = 777) {
	return md5(md5($salt . $str . $salt . 'mvoc.ru'));
}

function check_permissions($general_included_files = array(), $admin_path = FALSE, $file_path = FALSE) {

	if (!$general_included_files || !$admin_path || !$file_path)
	{
		echo 'error parameters';
		exit;
	}

	$file_path = str_replace('\\', '/', $file_path);
	$this_file_no_path = str_replace($file_path . 'admin/', '', $_SERVER["SCRIPT_FILENAME"]);
	$this_file = str_replace('.php', '', $this_file_no_path);

	if (!in_array($this_file, $general_included_files) && (!$_SESSION['permission'] && !in_array($this_file, $_SESSION['data_permissions']))) {
		$menus = unserialize(file_get_contents($admin_path . 'menu.dat'));
		$no_block = FALSE;
		foreach ($menus as $menu) {
			foreach($menu as $key => $value) {
				$key = explode('.php', $key);
				$key = $key[0];
				if (isset($_SESSION['data_permissions'][$key])) {
					$menu_key = $_SESSION['data_permissions'][$key];
					if ($value['files']) {
						foreach ($value['files'] as $file) {
							$f = explode('.php', $file);
							$f = $f[0];
							if ($f == $this_file) {
								$no_block = TRUE;
								break;
							}
						}
					}
				}
			}
		}

		if (!$no_block) {
			echo '<p class="error">File <b>' . $this_file . '</b> blocked permissions!</p>';
			writeLog($_SESSION['login'], 'error permission read file: ' . $this_file_no_path);
			exit;
		}
	}

	if (!in_array($this_file, $general_included_files)) {
		writeLog($_SESSION['login'], 'read file: ' . $this_file_no_path);
	}
}