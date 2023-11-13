<?php

function getZodiac($day, $month) {

   $day   = intval($day);
   $month = intval($month);

   switch($month) {
      case 1:
       if($day < 21) return "kozerog.jpg";
       else return "vodolei.jpg";
      break;

     case 2:
       if($day < 21) return "vodolei.jpg";
       else return "ribi.jpg";
      break;

      case 3:
       if($day < 21) return "ribi.jpg";
       else return "oven.jpg";
      break;

      case 4:
       if($day < 21) return "oven.jpg";
       else return "telec.jpg";
      break;

      case 5:
       if($day < 21) return "telec.jpg";
       else return "blizneci.jpg";
      break;

      case 6:
       if($day < 22) return "blizneci.jpg";
       else return "rak.jpg";
      break;

      case 7:
       if($day < 23) return "rak.jpg";
       else return "lev.jpg";
      break;

      case 8:
       if($day < 24) return "lev.jpg";
       else return "deva.jpg";
      break;

      case 9:
       if($day < 24) return "deva.jpg";
       else return "vesi.jpg";
      break;

      case 10:
       if($day < 24) return "vesi.jpg";
       else return "scorpion.jpg";
      break;

     case 11:
       if($day < 23) return "scorpion.jpg";
       else return "strelec.jpg";
      break;

      case 12:
       if($day < 22) return "strelec.jpg";
       else return "kozerog.jpg";
      break;

      default:
       return "kozerog.jpg";
      break;
   }
}

?>