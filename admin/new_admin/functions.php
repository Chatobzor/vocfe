<?php

if (!function_exists('parse_dat')) {
    function parse_dat($str) {
        $conf = array();
        $strs = explode(";\n", $str);
        foreach($strs as $str) {
            $str = explode('=', $str);
            $key = $str[0];
            unset($str[0]);
            $conf[$key] = implode('=', $str);
        }

        return $conf;
    }
}

if (!function_exists('parse_to_dat')) {
    function parse_to_dat($array) {
        $str = '';
        foreach($array as $k => $v) {
            $str .= $k . '=' . $v . ";\n";
        }

        return $str;
    }
}

if (!function_exists('save_file')) {
    function save_file($path, $content, $p = 'w') {
        $fp = fopen($path, $p);
        flock($fp, LOCK_EX);
        $fw = fwrite($fp, $content);
        flock($fp, LOCK_UN);
        $fc = fclose($fp);

        return $fw;
    }
}
if (!function_exists('writeLog')) {
    function writeLog($login = FALSE, $action = FALSE) {
        global $admin_path;
        
        if (!$login || !$action) {
            exit;
        }
        
        $logs = array();
        if (file_exists($admin_path . 'logs.dat')) {
            $log_string = '';
            $logs = file($admin_path . 'logs.dat');
            $logs = array_splice($logs, -($logs_limit));
            $logs[] = date('d.m.Y H:i:s') . "\t[" . $login . "]\t" . $action . "\n";
            foreach ($logs as $log) {
                $log_string .= $log;
            }
            save_file($admin_path . 'logs.dat', $log_string);
        } else {
            save_file($admin_path . 'logs.dat', date('d.m.Y H:i:s') . "\t[" . $login . "]\t" . $action . "\n");
        }
    }
}
if (!function_exists('getMsgStatistic')) {
	function getMsgStatistic() {
		global $data_path;

		$logs = scandir($data_path . 'logs');
		$parsed = array();

		arsort($logs);
		$i = 0;
		$last_days_count = 30;
		$total_count = $last_days_count * 2;

		foreach($logs as $log) {
			$info = pathinfo($log);
			$ex = $info['extension'];

			if ($ex == 'log') {

				if ($i >= $total_count) break;

				$filename = $info['filename'];

				$names = explode('-', $filename);

				if (count($names) == 3) {
					$parsed[$filename]['public'] = count(file($data_path . 'logs/' . $log));
					$i++;
				} else if (count($names) == 4) {
					$filename = str_replace('-private', '', $filename);
					$parsed[$filename]['private'] = count(file($data_path . 'logs/' . $log));
					$i++;
				}
			}
		}

		if (empty($parsed)) return [];

		uksort($parsed, function($a, $b) {
			return strtotime($a) - strtotime($b);
		});

		return $parsed;
	}
}