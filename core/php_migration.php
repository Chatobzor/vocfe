<?php

// Migration to PHP 5.6
foreach ($GLOBALS as $k => $v) {
    if ($k[0] == "_") {
        if ($k == "_FILES") {
            ${"HTTP_POST$k"} = $GLOBALS[$k];
        } else {
            ${"HTTP$k"."_VARS"} = $GLOBALS[$k];
        }
    }
}

// Migration PHP 7.4
if (!function_exists('eregi_replace')) {
    function eregi_replace($pattern, $replacement, $string)
    {
        $pattern = iconv("WINDOWS-1251", "UTF-8", $pattern);
        $string = iconv("WINDOWS-1251", "UTF-8", $string);
        $result = preg_replace("/".$pattern."/iu", $replacement, $string);

        return iconv("UTF-8", "WINDOWS-1251", $result);
    }
}
if (!function_exists('ereg')) {
    function ereg($pattern, $string)
    {
        $pattern = iconv("WINDOWS-1251", "UTF-8", $pattern);
        $string = iconv("WINDOWS-1251", "UTF-8", $string);
        return preg_match("/".$pattern."/u", $string);
    }
}
if (!function_exists('eregi')) {
    function eregi($pattern, $string)
    {
        $pattern = iconv("WINDOWS-1251", "UTF-8", $pattern);
        $string = iconv("WINDOWS-1251", "UTF-8", $string);
        return preg_match("/".$pattern."/iu", $string);
    }
}
if (!function_exists('split')) {
    function split($pattern, $subject, $limit = -1, $flags = 0)
    {
        $pattern = iconv("WINDOWS-1251", "UTF-8", $pattern);
        $subject = iconv("WINDOWS-1251", "UTF-8", $subject);
        $result = preg_split("/".$pattern."/u", $subject, $limit);

        return array_map(function ($item) {
            return iconv("UTF-8", "WINDOWS-1251", $item);
        }, $result);
    }
}

global $SQLLink;

function get_connection($passed_connection)
{
    if (isset($passed_connection)) {
        return $passed_connection;
    }
    global $SQLLink;

    if (!$SQLLink) {
        global $mysql_db, $mysql_server, $mysql_user, $mysql_password;
        return $SQLLink = mysqli_connect($mysql_server, $mysql_user, $mysql_password, $mysql_db);
    }

    return $SQLLink;
}

if (!function_exists('mysql_connect')) {
    function mysql_connect($host, $username, $password)
    {
        global $SQLLink;
        return $SQLLink = mysqli_connect($host, $username, $password);
    }
}

if (!function_exists('mysql_error')) {
    function mysql_error($SQLConn = null)
    {
        return mysqli_error(get_connection($SQLConn));
    }
}

if (!function_exists('mysql_fetch_array')) {
    function mysql_fetch_array($result, $result_type = MYSQLI_BOTH)
    {
        if (version_compare(phpversion(), '7', '>') and is_string($result_type)) {
            $defined_consants = get_defined_constants();
            $const = str_replace('MYSQL_', 'MYSQLI_', $result_type);
            $result_type = $defined_consants[$const];
        }

        return mysqli_fetch_array($result, $result_type);
    }
}

if (!function_exists('mysql_fetch_assoc')) {
    function mysql_fetch_assoc($result)
    {
        return mysqli_fetch_assoc($result);
    }
}

if (!function_exists('mysql_fetch_row')) {
    function mysql_fetch_row($result)
    {
        return mysqli_fetch_row($result);
    }
}
if (!function_exists('mysql_insert_id')) {
    function mysql_insert_id($SQLConn = null)
    {
        return mysqli_insert_id(get_connection($SQLConn));
    }
}
if (!function_exists('mysql_num_fields')) {
    function mysql_num_fields($result)
    {
        return mysqli_num_fields($result);
    }
}
if (!function_exists('mysql_num_rows')) {
    function mysql_num_rows($result)
    {
        return mysqli_num_rows($result);
    }
}
if (!function_exists('mysql_query')) {
    function mysql_query($query, $SQLConn = null)
    {
        return mysqli_query(get_connection($SQLConn), $query);
    }
}
if (!function_exists('mysql_select_db')) {
    function mysql_select_db($db_name, $SQLConn = null)
    {
        return mysqli_select_db(get_connection($SQLConn), $db_name);
    }
}

if (!function_exists('mysql_field_name')) {
    function mysql_field_name($result, $offset)
    {
        if (is_array($result)) {
            return $result[$offset];
        }
        $result = mysqli_fetch_field_direct($result, $offset);

        return $result->name;
    }
}


if (!function_exists('mysql_list_fields')) {
    function mysql_list_fields($db_name, $table_name, $SQLConn = null)
    {
        $SQLConn = get_connection($SQLConn);
        $CurDB = mysql_fetch_array(mysql_query('SELECT Database()', $SQLConn));
        $CurDB = $CurDB[0];
        mysql_select_db($db_name, $SQLConn);
        $result = mysql_query("SHOW COLUMNS FROM $table_name", $SQLConn);
        mysql_select_db($CurDB, $SQLConn);
        if (!$result) {
            print 'Could not run query: '.mysql_error($SQLConn);
            return [];
        }
        $fields = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $fields[] = $row['Field'];
        }
        return $fields;
    }
}
if (!function_exists('mysql_list_tables')) {
    function mysql_list_tables($db_name, $SQLConn = null)
    {
        $SQLConn = get_connection($SQLConn);
        $CurDB = mysql_fetch_array(mysql_query('SELECT Database()', $SQLConn));
        $CurDB = $CurDB[0];
        mysql_select_db($db_name, $SQLConn);
        $result = mysql_query("SHOW TABLES", $SQLConn);
        mysql_select_db($CurDB, $SQLConn);
        if (!$result) {
            print 'Could not run query: '.mysql_error($SQLConn);
            return [];
        }

        return $result;
    }
}

if (!function_exists('mysql_escape_string')) {
    function mysql_escape_string($unescaped_string, $SQLConn = null)
    {
        return mysqli_escape_string(get_connection($SQLConn), $unescaped_string);
    }
}

if (!function_exists('mysql_result')) {
    function mysql_result($res, $row, $field = 0)
    {
        $res->data_seek($row);
        $datarow = $res->fetch_array();
        return $datarow[$field];
    }
}

if (!function_exists('mysql_free_result')) {
    function mysql_free_result($result)
    {
        return mysqli_free_result($result);
    }
}

if (!function_exists('mysql_real_escape_string')) {
    function mysql_real_escape_string($unescaped_string, $SQLConn = null)
    {
        return mysqli_real_escape_string(get_connection($SQLConn), $unescaped_string);
    }
}

if (!function_exists('mysql_close')) {
    function mysql_close($SQLConn = null)
    {
        return mysqli_close($SQLConn);
    }
}
