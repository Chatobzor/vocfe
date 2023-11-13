<?

/******************************************************************************
*                                                                             *
* This library will try to get the most probable IP address of an user. It is *
*   based on a the free of use 'identifier' script written by Marc Meurrens   *
*                          (http://www.cgsa.net/php)                          *
*                                                                             *
******************************************************************************/
if (!defined("_CHECK_HOST_")):
define("_CHECK_HOST_", 1);
function checkipaddress($checkip) {
   if($checkip == "" or strlen(trim($chekip)) == 0) return 1;
   if (eregi("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $checkip)) {
       for ($i = 1; $i <= 3; $i++) {
           if (!(substr($checkip, 0, strpos($checkip, ".")) >= "0" && substr($checkip, 0, strpos($checkip, ".")) <= "255")) {
               echo "Блок ".$i.". IP-адреса записан в неверном формате ($chekip). Если это ошибочное срабатывание, сообщиете об этом по адресу support@creatiff.com.ua";
               return 0;
           }
           $checkip = substr($checkip, strpos($checkip, ".") + 1);
       }

       if (!($checkip >= "0" && $checkip <= "255")) {
           echo "IP-адрес записан в неверном формате ($chekip). Если это ошибочное срабатывание, сообщиете об этом по адресу support@creatiff.com.ua";
           return 0;
       }
   }
   else {
       echo "IP-адрес записан в неверном формате ($chekip). Если это ошибочное срабатывание, сообщиете об этом по адресу support@creatiff.com.ua";
       return 0;
   }
return 1;
}
endif;
// Get some headers that may contain the IP address
$SimpleIP = (isset($REMOTE_ADDR) ? $REMOTE_ADDR : getenv("REMOTE_ADDR"));
if($SimpleIP == "") {
        if (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) $SimpleIP = $HTTP_SERVER_VARS['REMOTE_ADDR'];
}

$TrueIP = (isset($HTTP_X_FORWARDED_FOR) ? $HTTP_X_FORWARDED_FOR : getenv("HTTP_X_FORWARDED_FOR"));
if ($TrueIP == "") $TrueIP = (isset($HTTP_X_FORWARDED) ? $HTTP_X_FORWARDED : getenv("HTTP_X_FORWARDED"));
if ($TrueIP == "") $TrueIP = (isset($HTTP_FORWARDED_FOR) ? $HTTP_FORWARDED_FOR : getenv("HTTP_FORWARDED_FOR"));
if ($TrueIP == "") $TrueIP = (isset($HTTP_FORWARDED) ? $HTTP_FORWARDED : getenv("HTTP_FORWARDED"));

if(!checkipaddress($TrueIP)) $TrueIP = "";

$GetProxy = ($TrueIP == "" ? "0":"1");

if ($GetProxy == "0")
{
        $TrueIP = (isset($HTTP_VIA) ? $HTTP_VIA : getenv("HTTP_VIA"));
        if ($TrueIP == "") $TrueIP = (isset($HTTP_X_COMING_FROM) ? $HTTP_X_COMING_FROM : getenv("HTTP_X_COMING_FROM"));
        if ($TrueIP == "") $TrueIP = (isset($HTTP_COMING_FROM) ? $HTTP_COMING_FROM : getenv("HTTP_COMING_FROM"));
        if ($TrueIP != "") $GetProxy = "2";
};

if(!checkipaddress($TrueIP)) $TrueIP = "";

if ($TrueIP == $SimpleIP) $GetProxy = "0";

// Return the true IP if found, else the proxy IP with a 'p' at the begining
switch ($GetProxy)
{
        case '0':
                // True IP without proxy
                $IP = $SimpleIP;
                $ExternalIP = $SimpleIP;
                break;
        case '1':
                $b = ereg ("^([0-9]{1,3}\.){3,3}[0-9]{1,3}", $TrueIP, $IP_array);
                if ($b && (count($IP_array)>0))
                {
                        // True IP behind a proxy
                        $ExternalIP = $SimpleIP;
                        $InternalIP = $IP_array[0];
///                        $IP = $IP_array[0];
                        $IP = $SimpleIP.":".$IP_array[0];
                }
                else
                {
                        // Proxy IP
                        $ExternalIP = $SimpleIP;
                        $IP = "p".$SimpleIP;
                };
                break;

                break;
        case '2':
                // Proxy IP
                $ExternalIP = $SimpleIP;
                $IP = "p".$SimpleIP;
                break;
};

?>