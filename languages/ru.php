<?php
$translit_table_general_ru =  array("a" => "а",
                                     "b" => "б",
                                     "d" => "д",
                                     "e" => "е",
                                     "f" => "ф",
                                     "h" => "х",
                                     "g" => "г",
                                     "i" => "и",
                                     "j" => "й",
                                     "k" => "к",
                                     "l" => "л",
                                     "m" => "м",
                                     "n" => "н",
                                     "o" => "o",
                                     "p" => "п",
                                     "r" => "р",
                                     "s" => "с",
                                     "t" => "т",
                                     "u" => "у",
                                     "v" => "в",
                                     "w" => "в",
                                     "y" => "ы",
                                     "'" => "ь",
                                     "z" => "з"
                                     );

$translit_table_polysymbolic_ru =  array("shch"=>"щ",
                                          "sch"=> "щ",
                                          "tsh"=> "щ",
                                          "ww"=> "щ",
                                          "xh"=> "щ",
                                          "ye" => "е",
                                          "kh" => "х",
                                          "ts" => "ц",
                                          "tz" => "ц",
                                          "ch" => "ч",
                                          "sh" => "ш",
                                          "zh" => "ж",
                                          "yu" => "ю",
                                          "ju" => "ю",
                                          "ja" => "я",
                                          "ya" => "я",
                                          "ia" => "я"
                                         );
define("MAX_POLY_VAL_RU", 4);
define("MIN_POLY_VAL_RU", 2);

if (!defined("_TRANSLITE_RU_")):
define("_TRANSLITE_RU_", 1);

function translit_ru($msg) {
  global $translit_table_general_ru, $translit_table_polysymbolic_ru, $translit_table_start_ru;

  $word_arr = array();
  $word_arr = explode(" ", $msg);


  for($i = 0; $i < count($word_arr); $i++) {
  //first-letter check
  $word_arr[$i] = trim($word_arr[$i]);
  if(strlen($word_arr[$i]) == 0) continue;

  $lwr_test  = $word_arr[$i];
  $word_arr[$i] = strtolower($word_arr[$i]);

  if(strcmp($lwr_test, $word_arr[$i]) != 0) $IsMixedCase = true;
  else $IsMixedCase = false;

  //maybe smile?
  $a = substr($word_arr[$i], 0, 1);
  if($a == "*") continue;

  for($start_case = MAX_POLY_VAL_RU; $start_case >= MIN_POLY_VAL_RU; $start_case = $start_case -1) {
  reset($translit_table_polysymbolic_ru);
  while ($t_val = current($translit_table_polysymbolic_ru)) {
 $eng = key($translit_table_polysymbolic_ru);
  if(strlen($eng) ==  $start_case) $word_arr[$i] = str_replace($eng, $t_val, $word_arr[$i]);
 next($translit_table_polysymbolic_ru);
  }
  }
  //finally, "main" cycle
  reset($translit_table_general_ru);
  while ($t_val = current($translit_table_general_ru)) {
 $eng = key($translit_table_general_ru);
  $word_arr[$i] = str_replace($eng, $t_val, $word_arr[$i]);
 next($translit_table_general_ru);
  }

  if($IsMixedCase) $word_arr[$i] = UCFirst($word_arr[$i]);
  }
  return implode(" ", $word_arr);
}
endif;

// for rozmova skin
// for daemon - must be ' = ' ONLY
$w_usr_adm_link = "АДМИНАМ";
$w_roz_clear_pub_adm = "Стерты все фразы от <b>~</b> в общем канале. Автор: <b>*</b>";
//

$w_invisibility = "Невидимость";
$w_guestbook = "Гостевая";
$w_system_smile = "Системный";
$w_favor_smile  = "Любимый";
$w_favor_yes = "ДА";
$w_favor_no  = "НЕТ";
$w_favor_add = "добавить";
$w_favor_rem = "удалить";
$w_info_tip  = "Показать профиль пользователя (нужно выбрать ник или в канале, или в списке)";
$w_info_tip_err = "Сначала нужно выбрать ник или в канале, или в списке!";
$w_prv_tip = "Сказать в приват [Control + Enter]";
$w_pub_tip = "Сказать в общак [Enter]";
$w_enc_tip = "Перекодировать";
$w_ext_tip = "Выйти (а вот этого делать не нада :-))!";
$w_usr_all = "ВСЕ";
$w_usr_all_link = "ВСЕМ";
$w_usr_adm = "АДМИНЫ";
$w_usr_boys  = "ПАРНИ";
$w_usr_boys_link  = "ПАРНЯМ";
$w_usr_girls = "ДЕВУШКИ";
$w_usr_girls_link = "ДЕВУШКАМ";
$w_usr_they  = "ОНИ";
$w_usr_they_link  = "ИМ";
$w_usr_clan  = "МОЙ КЛАН";
$w_usr_clan_link = "КЛАНУ";
$w_usr_shaman = "ШАМАНЫ";
$w_usr_shaman_link = "ШАМАНАМ";
$w_roz_who = "для кого";
$w_roz_color = "цвет";
$w_roz_msg = "сообщение";
$w_roz_pvt_log  = "Логи приватов";
$w_roz_marr  = "ЗАГС :-)";
$w_roz_family = "Семейное положение в чате";
$w_roz_marr_man_yes = "женат на";
$w_roz_marr_man_no  = "холост";
$w_roz_marr_wom_yes = "замужем за";
$w_roz_marr_wom_no  = "не замужем";
$w_roz_marr_it_yes  = "женато на";
$w_roz_marr_it_no = "не женато";
$w_roz_custom_login = "Приветствие при входе (<b>#</b> заменяет ник пользователя)";
$w_roz_custom_logout  = "Фраза при выходе (<b>#</b> заменяет ник пользователя)";
$w_roz_silence  = "Заткнуть";
$w_roz_announce = "Объявление";
$w_roz_silence_msg  = "<b>Модератор</b> запретил Вам говорить что-либо на <b>~ минут.</b> Задумайтесь над этим - Правила чата написаны не просто так.";
$w_roz_silence_remind = "Вам запрещено говорить что-либо еще <b>~ секунд!</b>";
$w_roz_similar  = "Похожие ники (мультивходы)";
$w_roz_similar_search = "искать схожие ники для";
$w_roz_similar_hash_ip  = "С одинаковыми ИД браузера и ІР (наиболее вероятное сходство)";
$w_roz_similar_hash = "С одинаковым ИД браузера (скорее всего, один и тот же человек)";
$w_roz_similar_ip = "С одинаковым ІР (маловероятно, но возможно что это один и тот же человек)";
$w_roz_similar_online = "Сейчас в чате";
$w_roz_browser_id = "ИД браузера";
$w_roz_registered_at  = "Профиль зарегестрирован";
$w_roz_similar_ref  = "ПОИСК ПО ВСЕЙ БАЗЕ: для зарегестрированных пользователей учитывать";
$w_roz_userdb = "База данных пользователей";
$w_roz_common = "Логи общего канала";
$w_roz_from  = "Начало периода";
$w_roz_till  = "Конец периода";
$w_roz_hours = "Часы";
$w_roz_minutes  = "Минуты";
$w_roz_seconds  = "Секунды";
$w_roz_chat_status  = "Специальный статус пользователя";
$w_roz_moderator  = "Модератор чата";
$w_roz_administrator  = "Администратор чата";
$w_roz_room_for = "Для комнаты";
$w_roz_silenced_adm = "<b>~</b> запрещено говорить на протяжении <b># минут.</b> Автор: <b>*</b>";
$w_roz_ban_adm  = "<b>~</b> забанен на <b># минут.</b> Автор: <b>*</b>. Тип бана: <b>@</b>";
$w_roz_clear_pub  = "стереть";
$w_roz_add_announce = "Сначала нужно набрать объявление!";
$w_roz_add_alert  = "Сначала нужно выбрать ник и написать причину!";
$w_roz_add_clear  = "Сначала нужно выбрать ник!";
$w_roz_add_silence  = "Сначала нужно выбрать ник и написать в поле сообщения, на сколько минут ставить молчанку!";
$w_roz_add_ban  = "Сначала нужно выбрать ник и написать в поле сообщения, на сколько минут банить!";
$w_roz_reiting  = "Рейтинг (сколько всего времени в чате)";
$w_roz_announce_stat  = "В общий канал написано объявление. Автор <b>*.</b>";
$w_roz_warning_stat = "<b>~</b> получил предупреждение от <b>*.</b>";
$w_roz_damn_cmd = "Проклясть";
$w_roz_undamn_cmd = "Индульгенция";
$w_roz_damn_mess  = "<b>Шаман</b> проклял человека под ником <b>~!</b>";
$w_roz_undamn_mess  = "<b>Шаман</b> снял одно проклятие с человека под ником <b>~.</b>";
$w_roz_damn_mess_adm  = "<b>*</b> проклял человека под ником <b>~!</b>";
$w_roz_undamn_mess_adm  = "<b>*</b> снял одно проклятие с человека под ником <b>~.</b>";
$w_roz_damneds  = "Проклятий";
$w_roz_priest = "Шаман чата";
$w_roz_rew_mess = "<b>Шаман</b> подарил амулет <b>~!</b>";
$w_roz_rew_mess_adm = "<b>*</b> подарил амулет <b>~!</b>";
$w_roz_reward_cmd = "Дать амулет";
$w_roz_reward = "Амулетов";
$w_roz_personal_file  = "Личное дело пользователя :-)";
$w_roz_more_personal  = "полный список";
$w_roz_add_personal = "добавить мнение";
$w_roz_my_clan  = "Мой Клан";
$w_roz_clans = "Кланы";
$w_roz_clan_add_user  = "может принимать в клан";
$w_roz_clan_delete_user = "может выгонять из клана";
$w_roz_clan_edit  = "может редактировать свойства клана";
$w_roz_clan_edit_user = "может редактировать специфические клан-свойства пользователя, который состоит в клане";
$w_roz_clan_add_clan  = "добавить клан";
$w_roz_clan_delete_clan = "удалить клан";
$w_roz_clan_status  = "Статус в клане";
$w_roz_clan_notfound  = "Клан <b>~</b> не найден!";
$w_roz_clan  = "Клан";
$w_roz_clan_status  = "Статус в клане";
$w_roz_clan_name  = "Имя клана";
$w_roz_clan_email = "E-Mail Администратора клана";
$w_roz_clan_url = "Официальный веб-сайт клана";
$w_roz_clan_avatar  = "Аватар клана (маленький (до 18х14)! для списка пользователей)";
$w_roz_clan_logo  = "Логотип клана (для профиля пользователя)";
$w_roz_clan_border  = "Нужна однопиксельная черная рамка вокруг логотипа?";
$w_roz_add_clan = "Добавить клан";
$w_roz_remove_clan  = "удалить клан";
$w_roz_clan_err_name  = "Не задано имя клана или клан с подобным именем уже существует!";
$w_roz_clan_err_email = "E-Mail неправильный!";
$w_roz_clan_err_http  = "URL неправильный!";
$w_roz_clan_err_avatar  = "Аватар недопустим! (Размеры не более чем 18 х 14 пкс, <b><u>только GIF!</u></b>)";
$w_roz_clan_err_logo  = "Лого большое! (Размеры не более чем 200 х 200 пкс, <b><u>GIF или JPG (расширения gif, jpg и jpeg)!</u></b>)";
$w_roz_clan_del_quest = "Вы уверены, что хотите удалить клан <b>#</b>? (действие нельзя отменить!)";
$w_roz_yes = "Да";
$w_roz_no  = "Нет";
$w_roz_shaman_alert = "<b>~</b> получает предупреждение от <b>Шамана</b>. Причина: <b>#</b>";
$w_roz_clan_notfound = "Такой клан не найден";
$w_roz_clan_edit_btn = "Редактировать клан";
$w_roz_style_start = "Открывающий тег собственного стиля";
$w_roz_style_end = "Закрывающий тег собственного стиля";
$w_roz_style = "стиль";
$w_roz_clan_edt_add_usr= "добавить пользователя";
$w_roz_clan_edt_del_usr= "удалить из клана";
$w_roz_clan_edt_edt_usr= "редактировать доступ";
$w_roz_clan_edt_edt_cln= "атрибуты клана";
$w_roz_clan_user_exists= "Этот пользователь уже входит в какой-то клан!";
$w_roz_clan_common_entr= "<b>|| # из клана \"@\" входит в комнату.</b>";
$w_roz_clan_common_exit= "<b>|| # из клана \"@\" выходит из комнаты.</b>";
$w_roz_clan_del_avatar = "удалить аватар";
$w_roz_clan_del_logo = "удалить лого";
$w_roz_clan_cst_greet  = "приветствие для людей из клана (# вместо ника и статуса, @ вместо имени клана)";
$w_roz_clan_cst_goodbye= "прощальная фраза для людей из клана (# вместо ника и статуса, @ вместо имени клана)";
$w_roz_clan_exceeds_lim= "Количество пользователей в клане превышает #";
$w_roz_show_admin = "Показывать меня как администратора в списке пользователей";
$w_roz_just_married = "<b>С этого момента ~ и # - одна семья! Муж может поцеловать жену! ГООРЬКО!!!</b>";
$w_roz_no_married = "<b>~ и # разошлись :-(</b>";
$w_roz_just_married_adm= "<b>~</b> и <b>#</b> - одна семья! Автор <b>*</b>";
$w_roz_no_married_adm  = "<b>~</b> и <b>#</b> - разошлись! Автор <b>*</b>";
$w_filter_tip = "Фильтр [выключен] - включив его, в общем канале Вы будете получать сообщения, адресованные ЛИЧНО вам.";
$w_filter_tip_on = "Фильтр [ВКЛЮЧЕН] - выключив его, в общем канале Вы будете все сообщения.";
$w_roz_marry_pan = "Женить";
$w_roz_unmarry_pan = "Развести";
$w_roz_marry_who = "Кого";
$w_roz_marry_with = "с";
$w_roz_pause_tip = "Отключить скроллинг (прокрутку) сообщений";
$w_roz_pause_tip_on = "Включить скроллинг (прокрутку) сообщений";
$w_roz_agreed = "Я соглашаюсь с Правилами проекта";
$w_adm_level[ADM_VIEW_PRIVATE] = "Просматривать лог привата";
$w_roz_clear_channels  = "очистить";
$w_roz_clear_pub_all = "Очистить общий канал?";
$w_roz_clear_priv  = "Очистить приват?";
$w_roz_profile = "профиль";
$w_roz_show_for_moders = "Модераторы могут видеть меня в режиме 'невидимости'";
$w_roz_show_ip = "Показывать этот IP (даже для модераторов)";
$w_roz_old_paste = "Запретить одновременный выбор нескольких ников (олд-скул режим вставки :))";
$w_roz_quaked_msg = "<b>*</b> потряс <b>~</b>";
$w_roz_chat_closed = "Извините, чат временно закрыт!";
$w_roz_out_of_space = "На разделе осталось очень мало свободного места (< 10 МБайт). Пожалуйста, поставьте об этом в известность администрацию чата!";
$w_roz_translit = "транслит";
$w_roz_need_cause = "Введите причину действия!";
$w_roz_my_contributions= "Ваши личные заметки";
$w_roz_my_contrib_notes= "Ваши личные заметки о пользователе. Никто, кроме Вас, не сможет их увидеть, ВКЛЮЧАЯ владельца этого профиля!";
$w_roz_new_message  = "Вам пришло новое приватное письмо! Прочесть его можно в ";
$w_roz_offline_pm  = "Оффлайн PM";
$w_roz_user_banned  = "Пользователь забанен по <b>#</b> до <b>*</b>";
$w_roz_last_action_tim = "Когда пользователь был у нас";
$w_roz_reduce_traffic  = "Уменьшить траффик, вырезая смайлы";
$w_roz_quit  = "выход";
$w_roz_filter = "фильтр";
$w_roz_pause = "пауза";
$w_roz_user = "Пользователь";

//user statuses
$w_roz_user_status[] = array("points" => 1000000, "status" => "старожил");
$w_roz_user_status[] = array("points" => 500000, "status" => "почетный гражданин");
$w_roz_user_status[] = array("points" => 200000, "status" => "особенно уважаемый гражданин");
$w_roz_user_status[] = array("points" => 150000, "status" => "уважаемый гражданин");
$w_roz_user_status[] = array("points" => 100000, "status" => "гражданин");
$w_roz_user_status[] = array("points" => 50000, "status" => "житель");
$w_roz_user_status[] = array("points" => 25000, "status" => "постоянный посетитель");
$w_roz_user_status[] = array("points" => 20000, "status" => "постоялец");
$w_roz_user_status[] = array("points" => 15000, "status" => "квартирант");
$w_roz_user_status[] = array("points" => 10000, "status" => "посетитель");
$w_roz_user_status[] = array("points" => 1000, "status" => "гость");

//new in ver > 0.20.31
$w_premoder_room = "Внимание, это ПРЕД-МОДЕРИРУЕМАЯ комната. При отправке Ваши сообщения будут проверяться администратором.";

$w_pr_noemail = "Невозможно отправить ключ на е-майл, т.к. данный ник был зарегистрирован без е-майл адреса.";
$w_pr_title = "Напоминание пароля";
$w_pr_already_sent = "Напоминание уже было отослано!";
$w_pr_mailtext = "Чтобы изменить Ваш пароль в чате ~, пожалуйста, откройте адрес #";
$w_pr_no_code = "Извините, некорректный код напоминания пароля!";

$w_admin_browserhash_kill = "Хеш-бан";
$w_admin_subnet_kill = "Подсеть";
$w_adm_level[ADM_BAN_BY_BROWSERHASH] = "Хеш-бан";
$w_adm_level[ADM_BAN_BY_SUBNET] = "Подсеть";



$w_sel_lang = "Язык интерфейса";

if (!defined("_TRANSLITE_RU_")):
define("_TRANSLITE_RU_", 1);

function w_people_ru($num) {
  switch (substr($num,-1,1)) {
  case 2:
  case 3:
  case 4: $zap = "человека"; break;
  default: $zap = "человек"; break;
  }
  if ((substr($num,-2,1) == 1) && $num>10) {$zap = "человек";}
  return $zap;
}
endif;

$w_mail_used = "Email адрес, введенный Вами, уже используется в нашей базе пользователей";
$w_max_per_mail = "На один E-Mail адрес вы можете зарегистрировать ~ ник(а)";

//new in ver > 0.18.16
$w_regmail_body = "Для активации вашего лоигна ~ в чате \"*\", откройте адрес #";
$w_regmail_no_code = "Извините, некорректный код активации";
$w_regmail_activated = "Ваш логин активирован, Вы можете войти в чат";
$w_regmail_sent = "Инструкции отосланы на указанный email-адрес";
$w_regmail_enter_mail = "(Вы получите письмо с инструкцией по активации пользователя на этот адрес)";
$w_already_registered = "Ник ~ уже зарегистрирован.";
$w_registered_only = "Чат работает в 'клубном режиме'. Вы должны зарегистрироваться чтобы войти в чат!";

$w_impro_enter_code = "Пожалуйста, введите код который Вы видите на картинке";
$w_impro_incorrect_code = "Вы ввели неверный код";

//new in ver > 0.16.08
$w_rules="Правила";
$w_statistic = "Статистика";
$w_total_users = "Всего пользователей";
$w_last_registered = "Последние ~ зарегистрированных";
$w_last_visit = "Последний визит";

$w_sure_user_delete = "Вы уверены что хотите удалить этого пользователя??";
$w_user_deleted = "Пользователь удален";

$w_big_photo = "Большое фото (.gif или .jpeg, макс. ~ Bytes, * пикс. шириной и # пикселей высотой)";
//it's redeclaration, so if you have lang-file from previous versions,
//don't forget to remove $w_big_photo from lines below

$w_style = "Стиль";
$w_bold = "Жирн.";
$w_italic = "Накл.";
$w_underlined = "Подчеркн.";
$w_too_big_photo = "Фото, которое Вы пытаетесь загрузить, слишком велико. Максимальный размер ~ байт, а Ваше фото * байт";
$w_too_big_photo_width = "Фото, которое Вы пытаетесь загрузить, слишком велико. Ширина должна быть меньше ~ пикс., а у Вашего фото ширина * пикс.";
$w_too_big_photo_height = "Фото, которое Вы пытаетесь загрузить, слишком велико. Высота должна быть меньше ~ пикс., а у Вашего фото высота * пикс.";
$w_too_big_avatar = "Маленькое фото (аватар), которое Вы пытаетесь загрузить, слишком велико.  Максимальный размер ~ байт, а ваше фото * байт";

//new in ver>0.14.24
$w_adm_add_room = "Добавить комнату";
$w_adm_room_design = "Предустановленный дизайн комнаты";
$w_edit = "Редактировать";
$w_room_name = "Название комнаты";
$w_bot_name = "Имя бота для комнаты";
$w_list_of_rooms = "список комнат";
$w_set_topic_text = "* устанавливает новый топик: <hr><b><center>#</center></b><hr>";
$w_topic = "Топик";
$w_adm_no_permission = "у Вас нет прав выполнить эту операцию";
$w_adm_unban = "разBANить";
$w_adm_banned_now = "сейчас забанены:";
$w_adm_nick_or_ip = "ник или ип-адрес";
$w_adm_ban_until = "до";
$w_adm_cannot_ban_mod = "вы не можете забанить другого модератора";
$w_adm_level[ADM_BAN] = "бан";
$w_adm_level[ADM_IP_BAN] = "бан по ip";
$w_adm_level[ADM_VIEW_IP] = "просмотр пользовательских ip-адресов";
$w_adm_level[ADM_UN_BAN] = "чистка бан-листа";
$w_adm_level[ADM_BAN_MODERATORS] = "бан других модераторов";
$w_adm_level[ADM_CHANGE_TOPIC] = "смена топика";
$w_adm_level[ADM_CREATE_ROOMS] = "операции с комнатами";
$w_adm_level[ADM_EDIT_USERS] = "редактирование пользовательских данных";

//new in ver >0.09.20
$w_web_indicator = "Разрешить использование веб-индикатора.";
$w_web_indicator_code = "Чтобы использовать такой ~ индикатор, просто скопируйте следующий хтмл-код и вставьте его в Вашу домашнюю страничку:";
$w_too_many = "Извините, слишком много пользователей в чате.";
$w_too_many_from_ip = "Извините, слишком много пользователей в чате зашедших с вашего адреса";
$w_try_again_later = "Попробуйте позднее";
$w_in_room = "Сейчас в комнате";
$w_who_in_rooms = "Комнаты";
$w_who_in_current_room = "Вернуться к списку посетителей текущей комнаты";

//for hi-tech skin:
$w_clear_input_field = "Очистить поле ввода";
$w_clear_whisper_field = "Очистить приват";
$w_stop_scrolling = "Приостановить авто-скроллинг";
$w_cont_scrolling = "Запустить авто-скроллинг";
$w_reload_main = "Обновить окно сообщений";
$w_for_registered = "(только для зарегистрированных пользователей)";

//new in ver > 0.07.09a

//user statuses
$w_your_status = "Ваш статус";
$w_user_status[ONLINE] = "Онлайн";
$w_user_status[DISCONNECTED] = "Отключен";
$w_user_status[AWAY] = "Ушел";
$w_user_status[NA] = "N/A";
$w_user_status[DND] = "Не беспокоить!";
$w_user_status['PRIVATE'] = "В привате";

$w_st_set = "Установить";

//rooms
$w_select_room = "Выберите комнату";
$w_goto_room = "перейти!";
// ~ -- usernick, * -- roomname
$w_goes_to_room = "~ уходит в комнату &quot;<b>*</b>&quot;";
$w_came_from_room = "~ приходит из комнаты &quot;<b>*</b>&quot;";

//message for the flood-checking mechanism
$w_flood = "Флуд! (мгногократное повторение)";

$w_2ignor = "-игн";
$w_2visible = "+вид";

//end of new

$w_only_one_tail = "Вы можете использовать только одно подключение к чату! Попробуйте обновить окно.";

//server

$w_server_restarting = "Сервер сообщений перезагружается. Пытаюсь подключиться заново...";

//common
$w_banned = "Вы отключены от чата";
$w_timeout = "Время вышло :-о";
$w_no_user = "Нет такого пользователя в чате!";
$w_title = "(Чат Chat.bz)";
$w_copyright = "<center><br>VOC++ BSE <a href=\"http://mvoc.ru\">Mvoc.ru</a> | Хостинг чатов <a href=\"http://chat.bz\">Chat.bz</a></center>";
//welcome
$w_welcome = "Voodoo chat";
$w_enter_login_nick = "Введите Ваш ник";
$w_login = "(Ник должен быть длиной от 3 до 15 символов<br> и может содержать буквы английского алфавита и знак _)";
$w_login_button = "&nbsp;Login&nbsp;";
$w_select_design = "Дизайн чата";
$w_select_type = "Тип чата";
$w_chat_type["tail"] = "Непрерывный";
$w_chat_type["php_tail"] = "Непрерывный на пхп";
$w_chat_type["reload"] = "Классический, с перезагрузкой";
$w_chat_type["js_tail"] = "Эмуляция непрерывного на JS";

$w_whisper_to = "Кому-то шепчет";

//bottom:
$w_whisper = "Приват";
$w_no_whisper = "Вслух";
$w_color = "Цвет";
$w_say = "Сказать";
$w_logout = "Выйти";
$w_too_long = "Слишком длинное сообщение!";
$w_whisper_out = "Нет пользователя, которому отправлен приват";

//tail:
$mod_text = "And remember,<br>respect is everything!<br><br>";
$w_right_not_refresh = "Внимание! Ваш правый фрейм не обновлялся 2 минуты. Еще через минуту Вы будете отключены!!";
$w_try_to_press = "Попробуйте нажать здесь.";
$w_new_window = "Ошибка. Вы не можете открывать центральный фрейм дважды. Попробуйте обновить окно.";
$w_disconnected = "Извините, вы отключены от чата";
$w_unknown_user = "Нет такого пользователя в чате!";

//who
$w_show_photos = "Показывать фото";
$w_dont_show_photos = "Выключить фото";
$w_in_chat = "Сейчас в чате";
$w_nobody_in = "Никого нет в чате";
$w_people = "people";

$w_info = "Информация о ";

//shower
$w_history = "История";

//top
$w_help = "Помощь";
$w_send_mes = "Отправить сообщение на личку";
$w_info_about = "Мы!";
$w_relogon = "Перезайти";
$w_pictures = "Смайлики";
$w_gun = "Админка!";
$w_registration = "Регистрация";
$w_about_me = "Профиль";
$w_color_settings = "Настройки цвета";
$w_feedback = "Комментарии";

//alerter
$w_pub = "Личка";
$w_messages = "сообщений";
$w_new = "нов.";
$w_used = "места исп.";

//i am
$w_personal_data = "Ваша персональная информация";
$w_show_data = "Показывать данные из этой группы для посетителей";
$w_surname = "Фамилия";
$w_name = "Имя";
$w_birthday = "День рождения";
$w_city = "Город";
$w_gender = "Пол";
$w_male = "Муж";
$w_female = "Жен";
$w_unknown = "Неизвестно";
$w_addit_info = "Дополнительная информация";
$w_small_photo = "Маленькое фото (40x40 pixels)";
$w_check_for_delete = "Кликните чтобы удалить, или просто";
$w_other_photo = "Выберите фото если Вы хотите заменить текущее";
$w_email = "e-mail";
$w_homepage = "URL домашней страницы";
$w_icq = "ICQ UIN";
$w_if_wanna_change_password = "Если Вы хотите изменить Ваш пароль, введите его два раза ниже. Если нет -- оставьте поля ввода пустыми.";
$w_new_password = "Новый пароль";
$w_confirm_password = "Подтверждение <i>нового</i> пароля";
$w_update = "Обновить";
$w_current_password = "Пожалуйста, введите Ваш текущий пароль и нажмите &quot;$w_update&quot; чтобы записать Ваши данные";

//updateData
$w_incorrect_password = "Некорректный пароль";
$w_pas_not_changed = "Пароль НЕ изменен";
$w_pas_changed = "Пароль изменен!";
$w_succ_updated = "Ваши данные успешно обновлены!";


//frameset
$w_enter_password = "пожалуйста введите Ваш пароль";
$w_incorrect_nick = "Некорректный ник!";
$w_try_again = "Попробуйте еще";
$w_already_used = "Этот ник уже используется в чате!";

//Robot words... ~ will replaced with user nick
$w_rob_name = "Robik";
$w_rob_login = "<b>|| <a style='text-decoration: underline' style='{cursor: pointer}' onClick=\"javascript:parent.Whisper('~');\">~</a> входит в комнату.</b>";
$w_rob_hb = "~&gt; <b>С Днем Рождения!</b>";
$w_rob_logout = "~ прощается с нами и уходит.";
$w_rob_idle = "~ уходит не попрощавшись (таймаут).";


//fullinfo
$w_no_such_reg_user = "Извините, не найден такой зарегистрированный пользователь";

//userinfo  ~ will replaced with user nick
$w_search_results = "Результаты поиска";
$w_select_nick = "Выберите ник";
$w_enter_nick = "Введите ник для поиска";
$w_search_comment = "(знак * обозначает любое количество любых символов)";
$w_search_button = "Искать!";
$w_search_no_found = "~ не найден";
$w_search = "Поиск пользователей";

//snd
$w_message_text = "Текст сообщения";
$w_send = "Отправить";
$w_enter_nick_to_send = "Ник адресата";
$w_user_wrote = "~ написал(а):";
$w_not_shure_in_nick = "Если Вы не знаете ник точно, попробуйте поиск (знак * обозначает любое количество любых символов)";

//postMessage
$w_incorrec_nick_to_send = "Неправильный ник адресата";
$w_message_sended = "Ваше сообщение успешно отослано";
$w_message_error = "Ошибка при отправке сообщения. Ящик адресата переполнен";
$w_back_to_send = "Вернуться к отправке сообщений";

//meOp
// ~ - from User, * - time
$w_back_to_userboard = "Вернуться к списку сообщений";
$w_status = "&nbsp;";
$w_from = "От";
$w_subject = "Тема";
$w_no_subject = "Без темы";
$w_at_date = "Дата";
$w_from_line = "From ~, sended at *";
$w_date_format = "d/m/Y, H:i";
$w_answer = "Ответить";
$w_delete = "удалить";
$w_del_checked = "Удалить отмеченные";
$w_stat[0] = " ";
$w_stat[1] = "<b>N</b>";
$w_stat[2] = "<b>R</b>";
$w_stat[3] = " ";

//pictures
$w_symbols = "Слово";
$w_picture = "Картинка";
$w_about_smiles = "
                <b>Стандартные смайлики</b> состоят из 4-х (или менее) символов - экспериментируйте:
                <ol>
                 <li>Брови (можно пропускать). - &quot;<b>&gt;</b>&quot; или &quot;<b>&lt;</b>&quot;. Если пропущено - смайлик без бровей
                 <li>Глаза (обязательно) - &quot;<b>:</b>&quot; или &quot;<b>=</b>&quot; - обычные глаза, &quot;<b>;</b>&quot; - подмигнуть
                 <li>Нос. На рисунках его все-равно нет, поэтому можно пропускать... Но если хочется - можно писать &quot;<b>-</b>&quot;, &quot;<b>^</b>&quot; или &quot;<b>'</b>&quot;
                 <li>Рот (обязательно):
                  <ul>
                  <li>&quot;<b>)</b>&quot;, &quot;<b>D</b>&quot; или &quot;<b>]</b>&quot; - улыбка
                  <li>&quot;<b>(</b>&quot; - расстроеный смайлик
                  <li>&quot;<b>|</b>&quot; - нейтральный... Хотя иногда больше похоже на поджатые губы обиженной девушки :)
                  <li>&quot;<b>P</b>&quot; или &quot;<b>p</b>&quot; (и русские и английские буквы) - показать язык
                  <li>&quot;<b>O</b>&quot; или &quot;<b>o</b>&quot; (и русские и английские буквы) - удивлённо открытый рот
                  </ul>
                </ol>
                Вы можете кликнуть на картинку ниже, чтобы вставить ее код в ваше сообщение
                ";

//admin
$w_no_admin_rights = "Нет прав администратора";
$w_admin_action = "действие";
$w_admin_alert = "предупредить";
$w_admin_kill = "отключить";
$w_admin_ip_kill = "отключить по ip-адресу";
$w_admin_reason = "причина";
$w_admin_ban = "заBANить";
$w_kill_list = "Kill list";
$w_admin_unban = "UnBan";
$w_alert_text = "<b>~</b> получает предупреждение от <b>Модератора</b>. Причина: <b>#</b>";
$w_kill_text = "<b>Модератор</b> отключает <b>~</b> на $. Причина: <b>#</b>";
$w_kill_time = "отключить на";

$w_times[0]["name"] = "1 минуту";
$w_times[0]["value"] = 60;
$w_times[1]["name"] = "3 минуты";
$w_times[1]["value"] = 180;
$w_times[2]["name"] = "5 минут";
$w_times[2]["value"] = 300;
$w_times[3]["name"] = "10 минут";
$w_times[3]["value"] = 600;
$w_times[4]["name"] = "1 час";
$w_times[4]["value"] = 3600;
$w_times[5]["name"] = "5 часов";
$w_times[5]["value"] = 18000;
$w_times[6]["name"] = "сутки";
$w_times[6]["value"] = 86400;
$w_times[7]["name"] = "неделю";
$w_times[7]["value"] = 604800;
$w_times[8]["name"] = "навсегда";
$w_times[8]["value"] = 315360002;

//leave
$w_leave = "<br>Спасибо за участие!<br><br><a href=\"index.php\">Вернуться в чат</a>";

//registration
//in the 'top' section $w_registration = "Регистрация";
$w_password = "Пароль";
$w_reg_text = $w_login;
$w_password_mismatch = "<br>пароли не совпадают";
$w_succesfull_reg = "<br>Ваш ник ~ успешно зарегистрирован.<br><b><font color=red>Если Вы находитесь в данный момент в чате, Вам нужно перезайти в него, что бы изменения вступили в силу.</font></b>";
$w_reg_error = "<br>Ошибка во время регистрации. Попробуйте еще раз позже.";

//feedback
$w_feed_headline = "Вы можете отправить мне свои пожелания:";
$w_feed_name = "Ф.И.О.";
$w_feed_message = "Ваше сообщение";
$w_feed_sent_ok = "Ваше сообщение успешно отправлено! Спасибо!";
$w_feed_error = "Произошла ошибка при отправке сообщения. Попробуйте, пожалуйста, еще раз позже.";

$registered_colors[0][0] = "Бордовый";
$registered_colors[0][1] = "#800000";

$registered_colors[1][0] = "Небесный";
$registered_colors[1][1] = "#0066FF" ;

$registered_colors[2][0] = "Загар";
$registered_colors[2][1] = "#d2b48c" ;

$registered_colors[3][0] = "Зеленый";
$registered_colors[3][1] = "#008000" ;

$registered_colors[4][0] = "Кирпичный";
$registered_colors[4][1] = "#b22222" ;

$registered_colors[5][0] = "Кораловый";
$registered_colors[5][1] = "#f08080" ;

$registered_colors[6][0] = "Коричневый";
$registered_colors[6][1] = "#a52a2a" ;

$registered_colors[7][0] = "Красный";
$registered_colors[7][1] = "#ff0000" ;

$registered_colors[8][0] = "Золотой";
$registered_colors[8][1] = "#DAA520";

$registered_colors[9][0] = "Лосось";
$registered_colors[9][1] = "#fa8072" ;

$registered_colors[10][0] = "Морской волны";
$registered_colors[10][1] = "#2e8b57" ;

$registered_colors[11][0] = "Оливковый";
$registered_colors[11][1] = "#808000" ;

$registered_colors[12][0] = "Оранжевый";
$registered_colors[12][1] = "#ff8c00" ;

$registered_colors[13][0] = "Орхидея";
$registered_colors[13][1] = "#da70d6" ;

$registered_colors[14][0] = "Перу";
$registered_colors[14][1] = "#cd853f" ;

$registered_colors[15][0] = "Пурпуровый";
$registered_colors[15][1] = "#800080" ;

$registered_colors[16][0] = "Розовый";
$registered_colors[16][1] = "#ff1493" ;

$registered_colors[17][0] = "Серый";
$registered_colors[17][1] = "#808080" ;

$registered_colors[18][0] = "Синий";
$registered_colors[18][1] = "#0000ff" ;

$registered_colors[19][0] = "Слива";
$registered_colors[19][1] = "#dda0dd" ;

$registered_colors[20][0] = "Фиолетовый";
$registered_colors[20][1] = "#8a2be2" ;

$registered_colors[21][0] = "Фуксин";
$registered_colors[21][1] = "#ff00ff" ;

$registered_colors[22][0] = "Бирюзовый";
$registered_colors[22][1] = "#008080" ;

$registered_colors[23][0] = "Основной";
$registered_colors[23][1] = "#000000" ;

$registered_colors[24][0] = "Шоколадный";
$registered_colors[24][1] = "#d2691e" ;

$registered_colors[25][0] = "Темно-синий";
$registered_colors[25][1] = "#000080" ;

$default_color = 23; #000000;
$highlighted_color = 7; #ff0000;

$w_roz_only_for_club = "Для того, что бы получить доступ к этому разделу, Вы сначала должны присоедениться к Клубу, введя свои данные в разделе \"<b>".$w_about_me."</b>\"";
$w_roz_not_in_club = "Этот ник не принадлежит к Клубу.";
$w_roz_not_allowed = "У Вас нет прав доступа в эту комнату!";
$w_enter_password_room = "Эта комната защищена паролем. Введите его";
$w_full_access = "Есть доступ ко всем ресурсам";
$w_money  = "Кредов";
$w_membered_by = "Доступ дан по совету";
$w_classified_info = "Служебная информация о пользователе";
$w_info_browser  = "Браузер";
$w_info_os  = "Операционная система";
$w_info_user_agent = "Строка USER_AGENT";
$w_grant_access  = "Рекомендовать пользователя для полного доступа";
$w_no_money = "Не хватает кредов!";
$w_money_exchange  = "Обменять поинты на кредиты";
$w_exchange_tax  = "Курс обмена (кредитов к поинтам)";
$w_howmany_exchange  = "Сколько обменять поинтов";
$w_exchange_do = "Обменять!";
$w_no_credits  = "Не хватает поинтов!";
$w_security = "Настройки безопастности";
$w_security_warn = "Ничего не меняйте в настройках безопастности, разве что Вы <b>ТОЧНО ПОНИМАЕТЕ, ЧТО ИМЕННО ВЫ ДЕЛАЕТЕ</b>! В противном случае чат может работать неправильно или Вы не сможете вообще в него войти под этим логином!";
$w_limit_by_hash = "Проверять браузер во время нахождения в чате (рекомендуется)";
$w_limit_by_ip = "Проверять IP (подсеть) во время нахождения в чате (рекомендуется)";
$w_limit_by_cookie = "Проверять cookie во время нахождения в чате (для экспертов)";
$w_limit_by_ip_only  = "(ТОЛЬКО ЭКСПЕРТЫ) Разрешить заходить в чат <b>ИСКЛЮЧИТЕЛЬНО</b> с этих IP (без пробелов, используйте ; для разделения)";
$w_security_error  = "Сработала система безопастности чата. Попробуйте убрать галочки касательно безопастности (по одной!) в Вашем профиле.";
$w_security_error_ip = "Вы не можете зайти в чат с этого IP-адреса, так как он не является разрешенным владельцем профиля.";
$w_roz_jail = "В сад! :)";
$w_jailed = "Вас вывели в сад! Наверное, плохо себя вели...";
$w_roz_jailed_adm  = "<b>~</b> выведен в сад на <b># минут.</b> Автор: <b>*</b>";
$w_jail_text = "<span class=ha><b>Модератор</b> вывел в сад <b>~</b> на $. Причина: <b>#</b></span>";
$w_roz_jailed_mess = "Пользователь выведен в сад до ";
$w_roz_silenced_mess = "Пользователю запрещено говорить до ";
$w_already_punished  = "Пользователь уже наказан!";
$w_private_sound = "Проигрывать звук при получении приватного сообщения (только Internet Explorer)";
$w_font_face = "Каким шрифтом выводить сообщения чата";
$w_font_size = "Размер шрифта (% от базового)";
//Added by MisterX
$shop = "Магазин";
$shop_quantity = "Количество";
$shop_quantity_unlimited = "Неограниченно";
$shop_title = "Название";
$shop_price = "Цена";
$shop_vip = "Только для VIP";

$w_adm_user_add_clan = "~ добавил # в клан ";
$w_adm_user_del_clan = "~ удалил # из клана ";
$w_adm_user_exchange = "Обменяно ~ поинтов на # кредитов (было $ кредитов, стало %)";
$w_adm_user_buy  = "Куплена вещь \"~\" за # кредитов (было $ кредитов, стало %)";
$w_adm_user_present  = "Подарена вещь \"~\" от #";
$w_adm_user_transfer = "Вещь \"~\" передана от #";
$w_adm_user_present_from = "Вещь \"~\" подарена #";
$w_adm_user_transfer_from  = "Вещь \"~\" передана # за * кредит(ов) (было $ кредитов, стало %)";
$w_adm_user_item_used  = "Вещь \"~\" использована";
$w_adm_user_item_used_on = "Вещь \"~\" использована на #";
$w_adm_user_item_removed = "Вещь \"~\" удалена";
$w_adm_user_item_returned  = "Вещь \"~\" возвращена в магазин (было $ кредитов, стало %)";

$w_shop_you_have = "У Вас есть";
$w_shop_other  = "Другие";
$w_shop_all = "Все";
$w_shop_buy = "Купить";
$w_shop_present  = "Подарить";
$w_shop_delete = "Удалить";
$w_shop_transfer = "Передать";
$w_shop_back = "Вернуть";
$w_shop_category_empty = "В этой категории товаров нет";
$w_shop_no_items = "Этот товар уже распродан";
$w_shop_no_such_item = "Такого товара не существует";
$w_shop_no_present = "Подарки нельзя отдавать или передаривать";
$w_shop_actions  = "Возможности";
$w_shop_invisibility = "Кольцо невидимости";
$w_shop_invisibility_use = "Надеть";

$w_money_transfer  = "Перевести деньги другому пользователю или в Казну Клана...";
$w_money_transfer_note = "Учтите, что за перевод с Вашего счета автоматически будет снято # кредит(ов).";
$w_money_transfer_accept = "Подтвердить перевод";
$w_money_transfer_password = "Пожалуйста, укажите ниже свой пароль для подтверждения перевода";
$w_money_transfer_destination  = "Пожалуйста, укажите ник пользователя, которому Вы хотите перевести деньги <i>ИЛИ</i> поставьте галочку МОЙ КЛАН.";
$w_money_transfer_amount = "Сумма (в целых числах)";
$w_money_transfer_ok = "Перевод совершен успешно!";
$w_adm_money_transfer_from = "# кредитов переведено к ~ (у Вас было $ кредитов, стало %)";
$w_adm_money_transfer  = "# кредитов переведено от ~ (было $ кредитов, стало %)";
$w_clan_treasury = "Казна Клана";
$w_adm_clan_penalty  = "# кредитов сняты по вине ~ (было $ кредитов, сейчас %)";
$w_adm_clan_rew  = "# кредитов добавлено к Казне как награда для ~ (было $ кредитов, сейчас %)";
$w_adm_chaos = "Хаос";
$w_adm_chaos_put = "<b>Модератор</b> отправил в Хаос <b>~</b> на # минут. Причина: ";
$w_adm_chaos_adm = "<b>*</b> отправил в Хаос <b>~</b> на # минут. Причина: ";
$w_user_chaos  = "Вы находитесь в Хаосе до ~. Это означает, что Вы не можете публиковать реплики в общем канале (ТОЛЬКО В ПРИВАТ), не сможете комментировать в профілях и тратить деньги.";
$w_roz_chaos_mess  = "Пользователь находится в Хаосе до ";

$w_webcam_show = "Разрешить другим пользователям просмотр Вашей вебкамеры";
$w_webcam_note = "<b>ВАЖНО:</b>Программа <b>WebcamXP</b> должна быть установлена, сконфигурирована и запущена, что бы Ваши друзья могли видеть Вас.  Вы можете скачать программу с сайта <a href=http://www.webcamxp.com>http://www.webcamxp.com</a>";
$w_webcam_ip_note  = "<b>ВАЖНО:</b> У Вас должен быть РЕАЛЬНЫЙ IP-адрес, это означает, что Вы должны быть подключены к Internet напрямую, <b>без</b> прокси-сервера или фаерволла и не использовать локальную сеть.";
$w_webcam_ip = "IP Вашего компьютера:";
$w_webcam_suggest  = "Скорее всего, Ваш IP должен быть ";
$w_webcam_port = "Номер порта для Вашей вебкамеры (по умолчанию 8080):";
$w_webcam_no = "Извините, но Вы не подключены напрямую к Internet (Вы используете прокси-сервер, фаервол или локальную сеть). Просмотр вебкамеры невозможен :-(";
$w_clear_nick_after  = "Очищать после отправки";
$w_add_features  = "Дополнительные возможности";
$w_about_user  = "Личная информация";
$w_security_opt  = "Безопасность";
$w_items_opt = "Подарки";
$w_money_opt = "Кредиты";
$w_webcam_opt  = "Веб-камера";
$w_another_opt = "Другое";

$w_color_nick  = "Цветной ник";
$w_color_nick_note = "Цветной ник могут поставить себе все, кроме шаманов. <b>ВАЖНО:</b> Существующий графический или цветной ник будет удален!<font color=red><b>После того, как Вы выберите цвет букв, нажмите ОК и ПЕРЕЗАЙДИТЕ В ЧАТ!</b></font>";
$w_color_nick_current  = "Существующий ник";
$w_color_nick_sample = "Образец";
$w_reffered_by = "Пришел по ссылке от";
$w_adm_reffered_payment  = "Получено # кредов как вознаграждение за привод ~ (было $ кредов, стало %)";
$w_adm_reffered_subject  = "Вы получили вознаграждение";
$w_adm_reffer_menu = "Рефералы";
$w_adm_reffer_note = "В этой секции описывается работа с Вашими рефералами -- людьми, которых Вы пригласили в чат. Когда каждый из них достигнет рейтинга не менее ~ поинтов, Вы получите однократную поощрительную выплату в размере # кредов и *% процентов от суммы каждой обменной операции ПОСЛЕ выплаты. Что бы пригласить человека в чат, просто дайте ему ссылку, которая показана ниже.";
$w_adm_reffer_link = "Ваша ссылка";
$w_roz_points  = "Поинтов";
$w_days = "дней";
$w_clan_money_transfer = "Певести креды с Казны Клана на счет пользователя";
$w_clan_money_transfer_cln = "# кредов из Казны Клана перевел ~ на счет пользователя ! (было $ кредов в Казне, стало %)";
$w_photo_reiting = "Баллов";
$w_photo_reiting_do  = "Оцените фото";
$w_photo_reiting_do_not  = "Вы уже голосовали за эту фотографию!";
$w_photo_reiting_vote  = "Проголосовало";
$w_photo_reiting_take_part = "Принимать участие в рейтинге фотографий";
$w_mod_remove_photo  = "Удалить фотографию";
$w_mod_remove_photo_adm  = "Фотографию пользователя # удалил $";
$w_mod_remove_photo_user = "Ваша фотография была удалена Модератором как такая, что противоречит Правилам чата. Пожалуйста, в дальнейшем вставляйте ТОЛЬКО ВАШУ фотографию. Не следует злопоутреблять, выставляя чужие фотографии -- аккаунт может быть заблокирован!";
$w_mod_remove_photo_subj = "Ваша фотография была удалена";
$w_reg_seconds_left  = " секунд осталось до начала регистрации. Пожалуйста, прочтите Правила за это время ;)";
$w_pass_secutity_time  = "Система безопастности напоминает, что нужно поменять пароль!";
$w_pass_secutity_note  = "Хороший пароль должен:".
                          "<ul><li>... быть не менее 8 символов в длинну или больше, что подразумевает использование нескольких слов или дополнительных символов;".
                          "<li>... состоять из символов верхнего и нижнего регистров, цифр и разных раскладок;".
                          "<li>... не быть известным словом, словосочетанием или выражением;".
                          "<li>... не быть цифрой, именем, номером телефона или чего либо, что можно ассоциировать с Вами;".
                          "<li>... создан случайным или псеводослучайным образом.</ul>";
$w_pass_secutity_check = "Периодически напоминать мне о необходимости сменить пароль";
$w_pass_secutity_alert = "Ваш пароль устарел! Смените его, пожалуйста, в Вашем профиле!";
$w_chat_welcome_main = "Добро пожаловать в наш чат!";
$w_chat_welcome_text = "Если Вам тут понравится и Вы захотите сохранить за собой этот ник, то, пожалуйста, в чате заполните раздел \"Регистрация\" -- красный крайний правый пункт меню.";
$w_chat_welcome_note = "требуется 1 раз при создании нового ника";
$w_chat_go  = "В ЧАТ";

/* Words for QUIZ       */
$w_quiz_ok = '<b> Молодец, <font color=#FF0000> <{USER_NAME}></font>! Правильный ответ<font color=#FF0000> (<{ANSWER}>) </font color>был дан за <{TIME}> секунд и принёс тебе <{POINTS_TO_ADD}> поинтов! (Всего пойнтов: <{POINTS_TOTAL}>, верных ответов на вопросы: <{QUIZ_TOTAL}>)</b> ';
$w_quiz_ok_no_reg = '<b> Молодец,<font color=#FF0000> <{USER_NAME}></font>!!! Ты дал правильный ответ<font color=#FF0000> (<{ANSWER}>)</font> за <{TIME}> секунд, но поинтов за него ты не получишь, потому что нужно вовремя регистрироваться :)</b> ';
$w_quiz_question_text = '<b><font color=#FF0000> Внимание, вопрос!</font><font color=blue> <{QUESTION}></font></b> ';
$w_quiz_words_count = '<b>  Слов:<font color=#FF0000> <{COUNT}></font></b> ';
$w_quiz_letters_count = '<b> <font color=green> Букв:</font> <{COUNT}></font></b> ';
$w_quiz_tip_text = '<b> Подсказка<font color=#FF0000> <{TIP_NUM}>: <{TIP_TEXT}></font></b> ';
$w_quiz_smoke_text = '<b> Никто не ответил на вопрос :( Пойду на перекур схожу на пару минут, и вернусь с новой пачкой вопросов!!!</b> ';
$w_quiz_smoke_back = '<b> Ну вот, я перекурил, подумал и готов вновь задавать вопросы :)</b> ';
$w_quiz_unanswered = '<b> Никто не ответил на вопрос. Следующий через <{SEC}> секунд.Правильный ответ был: </b><font color=#FF0000><{ANSWER}>.</b> ';
$w_quiz_command_me = '!я';
$w_quiz_me_answer = '<b> На счету <{USER_NAME}> уже <{POINTS}> пойнтов и <{ANSWERS}> верных ответов, самый быстрый ответ дан за <{SEC}> секунд.</b> ';
$w_quiz_command_top = '!топ';
$w_quiz_top_answer = '<b> Десятка самых умных чатлан: <br><div style="padding-left:55px;"><{TOP}></div> </b>';
$w_quiz_correct_answer_was = '<b> Правильный ответ был: <font color=#FF0000><{ANSWER}>.</font></b> ';
/* End Words for QUIZ   */
?>