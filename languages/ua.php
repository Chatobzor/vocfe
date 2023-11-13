<?php
//######################## -= README =- ###########################
// Український мовний модуль для VooDoo Chat v1.0 та моду VOC++     #
// Переклад: DareDEVIL (daredevil@rozmova.if.ua)                    #
// Український мовний модуль для VooDoo Chat v0.20.31               #
// Переклад: Vitaliy Prudyus (pruvital@kahovka.net)                 #
// даний мовний модуль розроблявся та оптимізовувася під чат        #
// гурту "Тартак" (chat.tartak.com.ua), тому перед використанням    #
// раджу почитати весь переклад, щоб упевнитись, що в ньому нічого  #
// не суперечить політиці Вашого сайту.                             #
// Буду радий, якщо моя робота комусь стане у пригоді!              #
// Слава Україні!                                                   #
//##################################################################

// translit feature for Ukrainian language
/*
Рішення Української комісії з питань правничої термінології №9 (під головуванням С.Головатого)
Протокол №2 від 19.04.1996
"Правила відтворення українських власних назв засобами англійської мови"


А = A
Б = B
В = V
Г = H, gh - gh - при відтворенні сполуки зг
Ґ = G
Д = D
Е = E
Є = Ye, ie - Ye - на початку слова, інакше - ie
Ж = Zh
З = Z
И = Y
І = I
Ї = Yi, i - Yi - на початку слова
Й = Y, i - Y - на початку слова
К = K
Л = L
М = M
Н = N
О = O
П = P
Р = R
С = S
Т = T
У = U
Ф = F
Х = Kh
Ц = Ts
Ч = Ch
Ш = Sh
Щ = Sch
Ь = '
Ю = Yu, iu - Yu - на початку слова
Я = Ya, ia* - Ya - на початку слова
---------------------------------------------

Може також вживатись (переважно у відтворенні власних назв) спрощений варіант:
1) спрощення подвоєних при­голосних ж, х, ц, ч, ш:
Запоріжжя - Zaporizhia
2) не відтворюються апостроф і знак м'якшення (за винятком буквосполучень -ьо-, -ьї-, що завжди передаються як -‘o-, -‘i-):
Львів - L’viv - Lviv, Стеф'юк - Stef’iuk - Stefiuk
*/

$translit_table_general_ua                  =  array("a" => "а",
                                                                                 "b" => "б",
                                                 "d" => "д",
                                                         "e" => "е",
                                                   "f" => "ф",
                                                  "h" => "г",
                                                   "g" => "Ґ",
                                                   "i" => "і",
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
                                                  "y" => "и",
                                         "'" => "ь",
                                                 "z" => "з");
$translit_table_polysymbolic_ua =  array("sch"=> "щ",
                                                                                 "gh" => "зг",
                                         "ie" => "є",
                                         "kh" => "х",
                                         "ts" => "ц",
                                         "ch" => "ч",
                                         "sh" => "ш",
                                         "zh" => "ж",
                                         "iu" => "ю",
                                         "ia" => "я");
$translit_table_start_ua                 =  array("ye" => "є",
                                         "yi" => "ї",
                                         "yu" => "ю",
                                         "ya" => "я",
                                         "y"  => "й");
define("MAX_START_VAL_UA", 2);
define("MAX_POLY_VAL_UA", 3);
define("MIN_POLY_VAL_UA", 2);

if (!defined("_TRANSLITE_UA_")):
define("_TRANSLITE_UA_", 1);

function translit_ua($msg) {
        global $translit_table_general_ua, $translit_table_polysymbolic_ua, $translit_table_start_ua;

    $word_arr = array();
        $word_arr = explode(" ", $msg);


    for($i = 0; $i < count($word_arr); $i++) {
            //first-letter check
        $word_arr[$i] = trim($word_arr[$i]);
        if(strlen($word_arr[$i]) == 0) continue;

        $lwr_test          = $word_arr[$i];
        $word_arr[$i] = strtolower($word_arr[$i]);

        if(strcmp($lwr_test, $word_arr[$i]) != 0) $IsMixedCase = true;
        else $IsMixedCase = false;

        //maybe smile?
        $a = substr($word_arr[$i], 0, 1);
        if($a == "*") continue;

        if(strlen($word_arr[$i]) > 1) {

          $a = substr($word_arr[$i], 0, MAX_START_VAL_UA);

          if(strlen($word_arr[$i]) > MAX_START_VAL_UA)  $b = substr($word_arr[$i], MAX_START_VAL_UA);
          else $b = "";

          for($start_case = MAX_START_VAL_UA; $start_case > 0; $start_case = $start_case -1) {
             for($j = 0; $j < count($translit_table_start_ua); $j++) {
                             if(array_key_exists($a, $translit_table_start_ua)) {
                                if(strlen($translit_table_start_ua[$a]) == $start_case) {
                                        $a = str_replace($a, $translit_table_start_ua[$a], $a);
                                }
                       }
                   }
          }

          $word_arr[$i] = $a.$b;
        }

        for($start_case = MAX_POLY_VAL_UA; $start_case >= MIN_POLY_VAL_UA; $start_case = $start_case -1) {
                reset($translit_table_polysymbolic_ua);
            while ($t_val = current($translit_table_polysymbolic_ua)) {
                    $eng = key($translit_table_polysymbolic_ua);
                if(strlen($eng) ==  $start_case) $word_arr[$i] = str_replace($eng, $t_val, $word_arr[$i]);
                    next($translit_table_polysymbolic_ua);
            }
        }
        //finally, "main" cycle
        reset($translit_table_general_ua);
        while ($t_val = current($translit_table_general_ua)) {
                    $eng = key($translit_table_general_ua);
                $word_arr[$i] = str_replace($eng, $t_val, $word_arr[$i]);
                    next($translit_table_general_ua);
        }

        if($IsMixedCase) $word_arr[$i] = UCFirst($word_arr[$i]);
    }
    return implode(" ", $word_arr);
}
endif;


// for rozmova skin
// for daemon - must be ' = ' ONLY
$w_usr_adm_link = "АДМІНАМ";
$w_roz_clear_pub_adm = "Стерті всі фрази від <b>~</b> в загальному каналі. Автор: <b>*</b>";
//

$w_invisibility                 = "Невидимість";
$w_guestbook                    = "Гостьова";
$w_system_smile                 = "Системний";
$w_favor_smile                  = "Улюблений";
$w_favor_yes                    = "ТАК";
$w_favor_no                     = "НІ";
$w_favor_add                    = "додати";
$w_favor_rem                    = "видалити";
$w_info_tip                     = "Показати профіль користувача (необхідно обрати нік або в каналі, або в списку)";
$w_info_tip_err                 = "Спочатку необхідно обрати нік або в каналі, або в списку!";
$w_prv_tip                              = "Сказати в приват [Control + Enter]";
$w_pub_tip                              = "Сказати в загал [Enter]";
$w_enc_tip                              = "Перекодувати";
$w_ext_tip                              = "Вийти геть (цього робити не треба :-))!";
$w_usr_all                              = "ВСІ";
$w_usr_all_link                 = "ВСІМ";
$w_usr_adm                              = "АДМІНИ";
$w_usr_boys                             = "ХЛОПЦІ";
$w_usr_boys_link                = "ХЛОПЦЯМ";
$w_usr_girls                    = "ДІВЧАТА";
$w_usr_girls_link               = "ДІВЧАТАМ";
$w_usr_they                             = "ВОНИ";
$w_usr_they_link                = "ЇМ";
$w_usr_clan                                                = "МІЙ КЛАН";
$w_usr_clan_link                                = "КЛАНУ";
$w_usr_shaman                                           = "ШАМАНИ";
$w_usr_shaman_link                                = "ШАМАНАМ";
$w_roz_who                              = "для кого";
$w_roz_color                    = "колір";
$w_roz_msg                              = "повідомлення";
$w_roz_pvt_log                  = "Логи приватів";
$w_roz_marr                             = "ЗАГС :-)";
$w_roz_family                   = "Сімейний стан у чаті";
$w_roz_marr_man_yes             = "жонатий на";
$w_roz_marr_man_no              = "холостий";
$w_roz_marr_wom_yes             = "одружена на";
$w_roz_marr_wom_no              = "не заміжня";
$w_roz_marr_it_yes              = "одружено на";
$w_roz_marr_it_no               = "не одружено";
$w_roz_custom_login             = "Привітання при вході (<b>#</b> заміняє нік користувача)";
$w_roz_custom_logout    = "Фраза при виході (<b>#</b> заміняє нік користувача)";
$w_roz_silence              = "заборонити говорити на";
$w_roz_announce             = "Написати об`яву";
$w_roz_silence_msg          = "<b>Модератор</b> заборонив Вам казати що-небудь на <b>~ хвилин.</b> Подумайте над цим. Правила для того, що б їх виконкувати.";
$w_roz_silence_remind   = "Вам заборонено казати що-небудь ще <b>~ секунд!</b>";
$w_roz_similar                  = "Схожі ніки (мультивходи)";
$w_roz_similar_search   = "Шукати схожі ніки для";
$w_roz_similar_hash_ip  = "З однаковим ІД браузера та ІР (найбільш імовірна схожість)";
$w_roz_similar_hash             = "З однаковим ІД браузера (скоріше за все одна і та ж людина)";
$w_roz_similar_ip               = "З однаковим ІP (малоімовірно, але може бути що одна і та ж людина)";
$w_roz_similar_online   = "Зараз у чаті";
$w_roz_browser_id               = "ІД браузера";
$w_roz_registered_at    = "Профиль зареєстровано";
$w_roz_similar_ref              = "ПОШУК ПО ВСІЙ БАЗІ: для зареєстрованих користувачів перевіряти";
$w_roz_userdb                   = "База данных користувачів";
$w_roz_common                   = "Логи загального каналу";
$w_roz_from                             = "Початок периоду";
$w_roz_till                             = "Кінець периоду";
$w_roz_hours                    = "Години";
$w_roz_minutes                  = "Хвилини";
$w_roz_seconds                  = "Секунди";
$w_roz_chat_status              = "Спеціальний статус користувача";
$w_roz_moderator                = "Модератор чату";
$w_roz_administrator    = "Адміністратор чату";
$w_roz_room_for                 = "Для кімнати";
$w_roz_silenced_adm             = "<b>~</b> заборонено щось казати на протязі <b># хвилин.</b> Автор: <b>*</b>";
$w_roz_ban_adm                  = "<b>~</b> забанений на <b># хвилин.</b> Автор: <b>*</b>. Тип бану: <b>@</b>";
$w_roz_clear_pub                = "витерти з загалу";
$w_roz_add_announce             = "Спочатку необхідно написати текст оголошення!";
$w_roz_add_alert                = "Спочатку виберіть нік та напишіть причину!";
$w_roz_add_clear                = "Спочатку виберіть нік!";
$w_roz_add_silence              = "Спочатку виберіть нік та напишіть в поле повідомлення, на скільки хвилин необхідно відібрати мову в користувача!";
$w_roz_add_ban                  = "Спочатку виберіть нік та напишіть в поле повідомлення, на скільки хвилин необхідно забанити користувача!";
$w_roz_reiting                  = "Рейтинг (скільки усього часу в чаті)";
$w_roz_announce_stat    = "В загальний канал написано оголошення. Автор <b>*.</b>";
$w_roz_warning_stat     = "<b>~</b> отримав попередження від <b>*.</b>";
$w_roz_damn_cmd                 = "Проклясти";
$w_roz_undamn_cmd               = "Індульгенція";
$w_roz_damn_mess                = "<b>Шаман</b> прокляв людину з ніком <b>~!</b>";
$w_roz_undamn_mess              = "<b>Шаман</b> зняв одне прокляття з <b>~.</b>";
$w_roz_damn_mess_adm    = "<b>*</b> прокляв людину з ніком <b>~!</b>";
$w_roz_undamn_mess_adm  = "<b>*</b> зняв одне прокляття з <b>~.</b>";
$w_roz_damneds                  = "Проклять";
$w_roz_priest                   = "Шаман чату";
$w_roz_rew_mess                 = "<b>Шаман</b> подарував амулет людині з ніком <b>~!</b>";
$w_roz_rew_mess_adm             = "<b>*</b> подарував амулет людині з ніком <b>~!</b>";
$w_roz_reward_cmd               = "Дати амулет";
$w_roz_reward                   = "Амулетів";
$w_roz_personal_file    = "Особова справа користувача :-)";
$w_roz_more_personal    = "повний перелік";
$w_roz_add_personal             = "додати думку";
$w_roz_my_clan                  = "Мій Клан";
$w_roz_clans                    = "Клани";
$w_roz_clan_add_user    = "може приймати до клану";
$w_roz_clan_delete_user = "може вилучати з клану";
$w_roz_clan_edit                = "може редагувати властивості клана";
$w_roz_clan_edit_user   = "може редагувати клан-властивості користувача, котрий є у клані";
$w_roz_clan_add_clan    = "додати клан";
$w_roz_clan_delete_clan = "видалити клан";
$w_roz_clan_status              = "Статус у клані";
$w_roz_clan_notfound    = "Клан <b>~</b> не знайдений!";
$w_roz_clan                             = "Клан";
$w_roz_clan_status              = "Статус у клані";
$w_roz_clan_name                = "Ім'я клану";
$w_roz_clan_email               = "E-Mail Адміністратора клану";
$w_roz_clan_url                 = "Офіційний веб-сайт клану";
$w_roz_clan_avatar              = "Аватар клану (маленький (до 18х14)! для переліку користувачів)";
$w_roz_clan_logo                = "Логотип клану (для профилю користувача)";
$w_roz_clan_border              = "Потрібна однопіксельна чорна рамка навколо логотипа?";
$w_roz_add_clan                 = "Додати клан";
$w_roz_remove_clan              = "Видалити клан";
$w_roz_clan_err_name    = "Не задане ім'я клану або клан з таким ім'ям вже існує!";
$w_roz_clan_err_email   = "E-Mail неправильний!";
$w_roz_clan_err_http    = "URL неправильний!";
$w_roz_clan_err_avatar  = "Аватар не відповідає вимогам! (Розміри не білбше ніж 18 х 14 пкс, <b><u>тільки GIF!</u></b>)";
$w_roz_clan_err_logo    = "Лого завелике! (Розміри не більше за 200 х 200 пкс, <b><u>GIF або JPG (розширення gif, jpg и jpeg)!</u></b>)";
$w_roz_clan_del_quest   = "Ви впевнені, що хочете видалити клан <b>#</b>? (дія необоротня!)";
$w_roz_yes                              = "Так";
$w_roz_no                               = "Ні";
$w_roz_shaman_alert = "<b>~</b> отримує попередження від <b>Шамана</b>. Причина: <b>#</b>";
$w_roz_clan_notfound   = "Такого клану не знайдено!";
$w_roz_clan_edit_btn   = "Редагувати клан";
$w_roz_style_start     = "Відкриваючий тег власного стилю";
$w_roz_style_end       = "Закрываючий тег власного стилю";
$w_roz_style               = "стиль";
$w_roz_clan_edt_add_usr= "додати користувача";
$w_roz_clan_edt_del_usr= "видалити з клану";
$w_roz_clan_edt_edt_usr= "редагувати доступ";
$w_roz_clan_edt_edt_cln= "атрибути клану";
$w_roz_clan_user_exists= "Цей користувач вже належить до якогось клану!";
$w_roz_clan_common_entr= "<b>|| # з клану \"@\" входить в кімнату.</b>";
$w_roz_clan_common_exit= "<b>|| # з клану \"@\" виходить з кімнати.</b>";
$w_roz_clan_del_avatar = "видалити аватар";
$w_roz_clan_del_logo   = "видалити лого";
$w_roz_clan_cst_greet  = "привітання для людей з клану (# замість нику та статусу, @ замість імені клану)";
$w_roz_clan_cst_goodbye= "повідомлення при виході для людей з клану (# замість нику та статусу, @ замість імені клану)";
$w_roz_clan_exceeds_lim= "Кількість користувачів у клані перевищує <b>#!</b>";
$w_roz_show_admin           = "Показувати мене як адміністратора у списку користувачів";
$w_roz_just_married           = "<b>Відтепер ~ та # - єдина родина! Чоловік може вцілювати дружину! ГІРКООО!!!</b>";
$w_roz_no_married           = "<b>Розпалась родина ~ та # :-(</b>";
$w_roz_just_married_adm= "Відтепер <b>~</b> та <b>#</b> - єдина родина! Автор <b>*</b>";
$w_roz_no_married_adm  = "Відтепер <b>~</b> та <b>#</b> - розведені! Автор <b>*</b>";
$w_filter_tip                   = "Фільтр [вимкнуто] - увімкнувши його, у загальному каналі Ви будете бачити лише ті повідомлення, котрі адресовані саме Вам.";
$w_filter_tip_on           = "Фільтр [ВВІМКНУТО] - вимкнувши його, у загальному каналі Ви будете бачити усі повідомлення.";
$w_roz_marry_pan           = "Женити";
$w_roz_unmarry_pan           = "Розвести";
$w_roz_marry_who           = "Кого";
$w_roz_marry_with           = "з";
$w_roz_pause_tip           = "Відключити скроллінг (прокрутку) повідомлень.";
$w_roz_pause_tip_on           = "Включити скроллінг (прокрутку) повідомлень.";
$w_roz_agreed                   = "Я погоджуюсь з Правилами проекту";
$w_adm_level[ADM_VIEW_PRIVATE] = "продивлятись лог приватних розмов";
$w_roz_clear_channels  = "очистити";
$w_roz_clear_pub_all   = "Очистити загальний канал?";
$w_roz_clear_priv            = "Очистити приват?";
$w_roz_profile                   = "профіль";
$w_roz_quaked_msg           = "<b>*</b> змусив потрястись <b>~</b>";
$w_roz_show_for_moders = "Модератори можуть бачити мене у режимі 'невидимки'";
$w_roz_show_ip                   = "Показувати цей IP (навіть для модераторів)";
$w_roz_old_paste           = "Заборонити одночасний вибір декількох ніків (олд-скул режим :))";
$w_roz_chat_closed           = "Вибачте, чат тимчасово закрито!";
$w_roz_out_of_space           = "На розділі залишилось дуже мало вільного місця (< 10 МБайт). Будь-ласка, доведіть цей факт до адміністрації чату!";
$w_roz_translit                   = "трансліт";
$w_roz_need_cause           = "Вкажіть причину дії!";
$w_roz_my_contributions= "Ваші персональні відмітки";
$w_roz_my_contrib_notes= "Ваші персональні відмітки про користувача. Ніхто, крім Вас, не зможе їх побачити, ВКЛЮЧНО з господарем цього профіля!";
$w_roz_new_message            = "Вам надійшов новий приватний лист! Прочитати його можна у ";
$w_roz_offline_pm            = "Офлайн PM";
$w_roz_user_banned            = "Користувач забанений по <b>#</b> до <b>*</b>";
$w_roz_last_action_tim = "Коли користувач був у нас";
$w_roz_reduce_traffic  = "Зменшити трафік за рахунок видалення смайлів";
$w_roz_quit                           = "вихід";
$w_roz_filter                   = "фільтр";
$w_roz_pause                   = "пауза";
$w_roz_user                    = "Користувач";
//user statuses
$w_roz_user_status[]   = array("points" => 1000000, "status" => "старожил");
$w_roz_user_status[]   = array("points" => 500000, "status" => "почесний громадянин");
$w_roz_user_status[]   = array("points" => 200000, "status" => "особливо поважний громадянин");
$w_roz_user_status[]   = array("points" => 150000, "status" => "поважний громадянин");
$w_roz_user_status[]   = array("points" => 100000, "status" => "громадянин");
$w_roz_user_status[]   = array("points" => 50000, "status" => "житель");
$w_roz_user_status[]   = array("points" => 25000, "status" => "постійний відвідувач");
$w_roz_user_status[]   = array("points" => 20000, "status" => "друг на районі");
$w_roz_user_status[]   = array("points" => 15000, "status" => "квартирант");
$w_roz_user_status[]   = array("points" => 10000, "status" => "відвідувач");
$w_roz_user_status[]   = array("points" => 1000, "status" => "гість");

//new in ver > 0.20.31
$w_premoder_room = "Увага, це -  ПРЕ-МОДЕРУЄМА кімната. При відправці Ваші повідомлення будуть перевірятись адміністратором.";
$w_pr_noemail = "Неможливо відправити ключ на е-мейл, оскільки нік було зареєстровано без е-мейл адреси.";
$w_pr_title = "Нагадування пароля";
$w_pr_already_sent = "Нагадування вже було відіслано!";
$w_pr_mailtext = "Для того, що б змінити Ваш пароль у чаті ~, будь ласка, відкрийте адресу #";
$w_pr_no_code = "Вибачте, некоректний код нагадування паролю!";

$w_admin_browserhash_kill = "Відключити по ІД браузера";
$w_admin_subnet_kill = "Відключити підмережу адрес";

//new in ver > 0.18.16
$w_regmail_body = "Для активації вашого логіна ~ у чаті \"*\", відкрийте цей лінк #";
$w_regmail_no_code = "Вибачте, неправильний код активації";
$w_regmail_activated = "Ваш логін активовано. Тепер Ви можете почати спілкування у чаті";
$w_regmail_sent = "Інструкції відіслані на вказану email-адресу";
$w_regmail_enter_mail = "(Ви отримаєте листа з інструкціями щодо активації користувача на цей e-mail)";
$w_already_registered = "Нікнейм ~ вже зайнято.";
$w_registered_only = "Чат працює у 'клубному режимі'. Ви повинні зареєструватися, щоб увійти до чату!";

$w_impro_enter_code = "Будь ласка, уведіть код, який Ви бачите на малюнку";
$w_impro_incorrect_code = "Ви увели невірний код";

$w_sel_lang = "Мова інтерфейсу";

//new in ver > 0.16.08
$w_rules="Правила";
$w_statistic = "Статистика";
$w_total_users = "Усього користувачів";
$w_last_registered = "Останні ~ зареєстрованих";
$w_last_visit = "Останній візит";

$w_sure_user_delete = "Ви впевнені, що хочете видалити цього користувача?!!";
$w_user_deleted = "Користувача видалено";

$w_big_photo = "Ваше фото (.gif або .jpg, макс. ~ Bytes, * пікс. ширина и # пікс. висота)";
//it's redeclaration, so if you have lang-file from previous versions,
//don't forget to remove $w_big_photo from lines below

$w_style = "Стиль";
$w_bold = "Жирний";
$w_italic = "Похилий";
$w_underlined = "Підкреслений";
$w_too_big_photo = "Фото, яке Ви намагаєтесь завантажити, завелике. Максимальний розмір ~ байт, а Ваше фото * байт";
$w_too_big_photo_width = "Фото, яке Ви намагаєтесь завантажити, завелике. Ширина повинна бути не більше ~ пікс., а у Вашого фото ширина * пікс.";
$w_too_big_photo_height = "Фото, яке Ви намагаєтесь завантажити, завелике. Висота повинна бути не більше ~ пікс., а у Вашого фото висота * пікс.";
$w_too_big_avatar = "Маленькое фото (аватар), яке Ви намагаєтесь завантажити, завелике.  Максимальный розмір ~ байт, а у Вас * байт";

//new in ver>0.14.24
$w_adm_add_room = "Додати кімнату";
$w_adm_room_design = "Стандартний дизайн кімнати";
$w_edit = "Редагувати";
$w_room_name = "Назва кімнати";
$w_bot_name = "Ім*я бота для команди";
$w_list_of_rooms = "список кімнат";
$w_set_topic_text = "* відкриває новий топік: <hr><b><center>#</center></b><hr>";
$w_topic = "Топік";
$w_adm_no_permission = "у Вас немає прав для здійснення цієї операції";
$w_adm_unban = "Розблокувати";
$w_adm_banned_now = "Зараз заблоковані:";
$w_adm_nick_or_ip = "нік або IP-адреса";
$w_adm_ban_until = "до";
$w_adm_cannot_ban_mod = "Ви не можете заблокувати іншого модератора";
$w_adm_level[ADM_BAN] = "BAN юзерів";
$w_adm_level[ADM_IP_BAN] = "BAN юзерів по ІР";
$w_adm_level[ADM_VIEW_IP] = "Перегляд юзерських ІР";
$w_adm_level[ADM_UN_BAN] = "Чистка BAN";
$w_adm_level[ADM_BAN_MODERATORS] = "BAN інших модерів";
$w_adm_level[ADM_CHANGE_TOPIC] = "Зміна топіка";
$w_adm_level[ADM_CREATE_ROOMS] = "Робота з кімнатами";
$w_adm_level[ADM_EDIT_USERS] = "Редаг. профайли";
$w_adm_level[ADM_BAN_BY_BROWSERHASH] = "Відключити по ІД браузера";
$w_adm_level[ADM_BAN_BY_SUBNET] = "Відключити підмережу адрес";

//new in ver >0.09.20
$w_web_indicator = "Дозволити використання веб-індикатора";
$w_web_indicator_code = "Щоб використовувати такий ~ індикатор, просто сокпіюйте цей ХТМЛ-код та поставте його на свою веб-сторінку і відвідувачі вашого сайту завжди будуть бачити чи ви присутні зараз в нашому чаті:";
$w_too_many = "Вибачте, забагато користувачів у чаті.";
$w_too_many_from_ip = "Вибачте, забагато користувачів у чаті, що зайшли з вашої IP-адреси";
$w_try_again_later = "Спробуйте увійти через 2-3 хв.";
$w_in_room = "Зараз у кімнаті";
$w_who_in_rooms = "Кімнати";
$w_who_in_current_room = "Повернутись до списку чатерів вашої кімнати";

//for hi-tech skin:
$w_clear_input_field = "Очистити поле повідомлення";
$w_clear_whisper_field = "Очистити приват";
$w_stop_scrolling = "Призупинити авто-скролінг";
$w_cont_scrolling = "Авто-скроллинг";
$w_reload_main = "Оновити вікно чату";
$w_for_registered = "(тільки для зареєстрованих користувачів)";

//new in ver > 0.07.09a

//user statuses
$w_your_status = "Ваш статус";
$w_user_status[ONLINE] = "Онлайн";
$w_user_status[DISCONNECTED] = "Оффлайн";
$w_user_status[AWAY] = "На перекурі";
$w_user_status[NA] = "Відійшов";
$w_user_status[DND] = "Не турбувати!";

$w_st_set = "Встановити";

//rooms
$w_select_room = "Оберіть кімнату";
$w_goto_room = "Перейти!";
// ~ -- usernick, * -- roomname
$w_goes_to_room = "~ йде до кімнати &quot;<b>*</b>&quot;";
$w_came_from_room = "~ приходить з кімнати &quot;<b>*</b>&quot;";

//message for the flood-checking mechanism
$w_flood = "Флуд! (багаторазове повторення)";

$w_2ignor = "-ігнор";
$w_2visible = "+вид.";

//end of new

$w_only_one_tail = "Ви можете використовувати лише одне підключення до чату! Спробуйте оновити вікно.";

//server

$w_server_restarting = "Сервер повідомлень перезавантажується. Намагаюсь відновити підключення...";

//common
$w_banned = "Ви відключені від чату";
$w_timeout = "Час вийшов :-о";
$w_no_user = "Немає такого користувача!";
$w_title = "РОZМОВА -- Народний чат";

//copyright
$w_copyright = "<center><br><font class=\"copyright\">Використовується <a href=\"http://vocplus.creatiff.com.ua\" target='_blank'>VOC++ Business Edition</a></u> &copy; 2004-2005 <u><a href=\"http://www.creatiff.com.ua\" target='_blank'>CREATIFF Design Studio</a></u><br>".
"Український <u><a href=\"http://voc.sourceforge.net\" target='_blank'>VooDoo Chat</a></u> v0.20.31 &copy; 2004 <u><a href=\"http://vitaliy.iatp.org.ua\" target='_blank'>Vitaliy Prudyus</a></u>
<br>Powered by <u><a href=\"http://voc.sourceforge.net\" target='_blank'>VooDoo Chat</a></u> v0.20.31 &copy; 1999-2003 by <u><a href=\"http://voc.sourceforge.net\" target='_blank'>Vlad Vostrykh</a></u></font></center>";
//welcome
$w_welcome = $w_title;
$w_enter_login_nick = "Нік";
$w_login = "(Нік повинен бути довжиною від 2 до 20 символів,<br>може складатись з літер української та англійської абетки, <br>пробілу, а також знаку _ )";
$w_login_button = "&nbsp;Увійти&nbsp;";
$w_select_design = "Дизайн чату";
$w_select_type = "Тип чату";
$w_chat_type["tail"] = "Безперервний";
$w_chat_type["php_tail"] = "Безперервний на PHP";
$w_chat_type["reload"] = "Класичний, з перезавантаженням";
$w_chat_type["js_tail"] = "Эмуляція безперервного на Java Script";

$w_whisper_to = "Комусь шепоче";

//bottom:
$w_whisper = "Приват";
$w_no_whisper = "Вголос";
$w_color = "Колір";
$w_say = "ОК";
$w_logout = "Вихід";
$w_too_long = "Занадто довге повідомлення!";
$w_whisper_out = "Немає користувача, якому відправлено приватне повідомлення";

//tail:
$mod_text = "And remember,<br>respect is everything!<br><br>";
$w_right_not_refresh = "Увага! Ваш правий фрейм не оновлювався протягом 2 хвилин. Ще через хвилину ви будете віключені!!!";
$w_try_to_press = "Спробуйти натиснути тут";
$w_new_window = "Помилка. Ви не можете вікривати центральний фрейм двічі. Спробуйте оновити вікно.";
$w_disconnected = "Вибачте, Ви відключені від чату...";
$w_unknown_user = "Немає такого користувача в чаті!";

//who
$w_show_photos = "Відображати аватари";
$w_dont_show_photos = "Не показувати аватари";
$w_in_chat = "Народу в чаті";
$w_nobody_in = "В чаті нікого... Стань ПЕРШИМ! ;) ";
$w_people = "people";

if (!defined("_TRANSLITE_UA_")):
define("_TRANSLITE_UA_", 1);

function w_people_ua($num)
{
        if ($num == 1) return "";
        else return "";
}
endif;

$w_info = "Інформація про ";

//shower
$w_history = "Про що розмова:";

//top
$w_help = "Help";
$w_send_mes = "Написати нового листа";
$w_info_about = "Користувачі";
$w_relogon = "Перезайти в чат";
$w_pictures = "Смайли";
$w_gun = "Адмін";
$w_registration = "Реєстрація";
$w_about_me = "Моє інфо";
$w_color_settings = "Настройка кольору";
$w_feedback = "Зв'язок";



//alerter
$w_pub = "Пошта";
$w_messages = "повідомлень";
$w_new = "нов.";
$w_used = "&nbsp;місця зайн.";

//i am
$w_personal_data = "Ваш профайл";
$w_show_data = "Показувати дані ціє групи іншим відвідувачам";
$w_surname = "Прізвище";
$w_name = "Ім'я";
$w_birthday = "День народження";
$w_city = "Місто";
$w_gender = "Стать";
$w_male = "Чоловіча";
$w_female = "Жіноча";
$w_unknown = "Невідомо";
$w_addit_info = "Додаткова інформація";
$w_small_photo = "Ваш аватар (40x40 pixels)";
$w_check_for_delete = "Позначте, щоб видалити, або просто";
$w_other_photo = "оберіть інше фото, якщо Ви хочете змінити поточне";
$w_email = "E-mail";
$w_homepage = "Домашня сторінка";
$w_icq = "ICQ";
$w_if_wanna_change_password = "Якщо ви бажаєте змінити ваш пароль, нижче уведіть його двічі . Якщо ні - залиште поля порожніми.";
$w_new_password = "Новий пароль";
$w_confirm_password = "Підтвердження <i>нового</i> пароля";
$w_update = "Оновити";
$w_current_password = "Будь ласка, введіть Ваш поточний пароль та натисніть &quot;$w_update&quot;, щоб записати Ваші дані";

//updateData
$w_incorrect_password = "Неправильний пароль";
$w_pas_not_changed = "Пароль НЕ змінено";
$w_pas_changed = "Пароль змінено!";
$w_succ_updated = "Ваш профайл оновлено!";


//frameset
$w_enter_password = "будь ласка уведіть Ваш пароль";
$w_incorrect_nick = "Неправильній нік!";
$w_try_again = "Спробуйте ще раз";
$w_already_used = "Цей нік вже використовується в чаті!";

//Robot words... ~ will replaced with user nick
$w_rob_name = "Did Uhim";
$w_rob_login = "<b>|| <a style='text-decoration: underline' style='{cursor: pointer}' onClick=\"javascript:parent.Whisper('~');\">~</a> входить в кімнату.</b>";
$w_rob_hb = "~&gt; <b>HAPPY BIRTHDAY!</b>";
$w_rob_logout = "<b>|| ~ виходить з чату</b>.";
$w_rob_idle = "<b>~ виходить з кімнати.</b>";


//fullinfo
$w_no_such_reg_user = "Вибачте, такого зареєстрованого користувача в чаті немає";

//userinfo    ~ will replaced with user nick
$w_search_results = "Результати пошуку";
$w_select_nick = "Оберіть нік";
$w_enter_nick = "Уведіть нік для пошуку";
$w_search_comment = "(знак * позначає будь-яку кількість будь-яких символів)";
$w_search_button = "Пошук";
$w_search_no_found = "Користувача з ніком ~ не знайдено";
$w_search = "Пошук користувачів";

//snd
$w_message_text = "Текст повідомлення";
$w_send = "Відіслати";
$w_enter_nick_to_send = "Ник адресата";
$w_user_wrote = "цитата";
$w_not_shure_in_nick = "Якщо Ви не знаєте нік точно, скористуйтесь пошуком (знак * позначає будь-яку кількість будь-яких символів)";

//postMessage
$w_incorrec_nick_to_send = "Неправильний нік адресата";
$w_message_sended = "Ваше повідомлення для передано!";
$w_message_error = "Помилка під час передачі повідомлення. Чат-скринька користувача перповнена";
$w_back_to_send = "Повернутись до відправки повідомлень";

//meOp
// ~ - from User, * - time
$w_back_to_userboard = "Повернутись до списку повідомлень";
$w_status = "&nbsp;";
$w_from = "Від";
$w_subject = "Тема";
$w_no_subject = "Без теми";
$w_at_date = "Дата";
$w_from_line = "From ~, sended at *";
$w_date_format = "d/m/Y, H:i";
$w_answer = "Відповісти";
$w_delete = "Видалити";
$w_del_checked = "Видалити позначені";
$w_stat[0] = " ";
$w_stat[1] = "<b>Нове</b>";
$w_stat[2] = "<i>Прочитане</i>";
$w_stat[3] = " ";

//pictures
$w_symbols = "Слово";
$w_picture = "Малюнок";
$w_about_smiles = "<hr>
<center><b><font color=green>Інструкція з використання текстових смайлів:</font></b></center><br>
<b>Стандартні смайлики</b> складаються з 4-х (або менше) символів - эксперементуйте:
<ol>
 <li>Брови (можна пропускати) - &quot;<b>&gt;</b>&quot; або &quot;<b>&lt;</b>&quot;. Якщо пропустити, то смайл буде без бров
 <li>Очі (обов'язково) - &quot;<b>:</b>&quot; або &quot;<b>=</b>&quot; - звичайні очі, &quot;<b>;</b>&quot; - підморгнути
 <li>Ніс. На малюнках його все одно немає, тому можна пропускати... Але якщо кортить - можна надрукувати &quot;<b>-</b>&quot;, &quot;<b>^</b>&quot; або <b>'</b>
 <li>Рот (обов'язково):
    <ul>
        <li>&quot;<b>)</b>&quot;, &quot;<b>D</b>&quot; або &quot;<b>]</b>&quot; - посмішка
        <li>&quot;<b>(</b>&quot; - засмучений смайл
        <li>&quot;<b>|</b>&quot; - нейтральний... Хоч іноді більше схоже на піджаті губи дівчини, що образилась :)
        <li>&quot;<b>P</b>&quot; або &quot;<b>p</b>&quot; (і українські і англійські літери) - показати язика
        <li>&quot;<b>O</b>&quot; або &quot;<b>o</b>&quot; (і українські і англійські літери) - здивовано відритий рот
    </ul>
</ol>
";

//admin
$w_no_admin_rights = "Немає прав адміністратора";
$w_admin_action = "дія";
$w_admin_alert = "попередити";
$w_admin_kill = "БАН";
$w_admin_ip_kill = "БАН по ІР";
$w_admin_reason = "Привід";
$w_admin_ban = "Наказати";
$w_kill_list = "Kill list";
$w_admin_unban = "Розбанити";
$w_alert_text = "<span class=ha><b>~</b> отримує попередження від <b>Модератора</b>. Привід: <b>#</b></span>";
$w_kill_text = "<span class=ha><b>Модератор</b> відключає від чату <b>~</b> на $. Привід: <b>#</b></span>";
$w_kill_time = "відключити на";

$w_times[0]["name"] = "1 хвилину";
$w_times[0]["value"] = 60;
$w_times[1]["name"] = "3 хвилини";
$w_times[1]["value"] = 180;
$w_times[2]["name"] = "5 хвилин";
$w_times[2]["value"] = 300;
$w_times[3]["name"] = "10 хвилин";
$w_times[3]["value"] = 600;
$w_times[4]["name"] = "1 годину";
$w_times[4]["value"] = 3600;
$w_times[5]["name"] = "5 годин";
$w_times[5]["value"] = 18000;
$w_times[6]["name"] = "добу";
$w_times[6]["value"] = 86400;
$w_times[7]["name"] = "тиждень";
$w_times[7]["value"] = 604800;
$w_times[8]["name"] = "назавжди";
$w_times[8]["value"] = 315360002;




//leave
$w_leave = "Спасибі за участь!<br><br><b><a href=\"index.php\">Повернутись до чату</a></b>";

//registration
//in the 'top' section $w_registration = "Регистрация";
$w_password = "Пароль";
$w_reg_text = $w_login;
$w_password_mismatch = "паролі не співпадають";
$w_succesfull_reg = "Ваш нік <b>~</b> зареєстровано!<br><b><font color=red>Якщо Ви зараз у чаті, Вам необхідно перезайти у нього, аби змінили мали ефект</font></b>.";
$w_reg_error = "Помилка при реєстрації. Спробуйте ще раз!";

//feedback
$w_feed_headline = "<b>Зв'язатись з адмінами:</b>";
$w_feed_name = "Назвіться:";
$w_feed_message = "Ваше повідомлення";
$w_feed_sent_ok = "Ваше повідомлення відправлено куди треба! ;)";
$w_feed_error = "Помилка підчас відпралення. Спробуйте ще раз";


$registered_colors[0][0] = "Бордовий";
$registered_colors[0][1] = "#800000";

$registered_colors[1][0] = "Небесний";
$registered_colors[1][1] = "#0066FF";

$registered_colors[2][0] = "Загар";
$registered_colors[2][1] = "#d2b48c" ;

$registered_colors[3][0] = "Зелений";
$registered_colors[3][1] = "#008000" ;

$registered_colors[4][0] = "Кірпичний";
$registered_colors[4][1] = "#b22222" ;

$registered_colors[5][0] = "Кораловий";
$registered_colors[5][1] = "#f08080" ;

$registered_colors[6][0] = "Коричневий";
$registered_colors[6][1] = "#a52a2a" ;

$registered_colors[7][0] = "Темно-червоний";
$registered_colors[7][1] = "#c52222" ;

$registered_colors[8][0] = "Золотий";
$registered_colors[8][1] = "#DAA520";

$registered_colors[9][0] = "Лосось";
$registered_colors[9][1] = "#fa8072" ;

$registered_colors[10][0] = "Морської хвилі";
$registered_colors[10][1] = "#2e8b57" ;

$registered_colors[11][0] = "Оливковий";
$registered_colors[11][1] = "#808000" ;

$registered_colors[12][0] = "Оранжевий";
$registered_colors[12][1] = "#ff8c00" ;

$registered_colors[13][0] = "Орхідея";
$registered_colors[13][1] = "#da70d6" ;

$registered_colors[14][0] = "Перу";
$registered_colors[14][1] = "#cd853f" ;

$registered_colors[15][0] = "Пурпуровий";
$registered_colors[15][1] = "#800080" ;

$registered_colors[16][0] = "Рожевий";
$registered_colors[16][1] = "#ff1493" ;

$registered_colors[17][0] = "Сірий";
$registered_colors[17][1] = "#808080" ;

$registered_colors[18][0] = "Синій";
$registered_colors[18][1] = "#0000ff" ;

$registered_colors[19][0] = "Слива";
$registered_colors[19][1] = "#dda0dd" ;

$registered_colors[20][0] = "Фіолетовий";
$registered_colors[20][1] = "#8a2be2" ;

$registered_colors[21][0] = "Фуксин";
$registered_colors[21][1] = "#ff00ff" ;

$registered_colors[22][0] = "Бирюзовий";
$registered_colors[22][1] = "#008080" ;

$registered_colors[23][0] = "Основний";
$registered_colors[23][1] = "#000000" ;

$registered_colors[24][0] = "Чоколядовий";
$registered_colors[24][1] = "#d2691e" ;

$registered_colors[25][0] = "Темно-синій";
$registered_colors[25][1] = "#000080" ;

$default_color = 23;#black;
$highlighted_color = 7; #red;

$w_roz_only_for_club    = "Для того, щоб отримати доступ до цього розділу, Ви маєете спочатку приєднатись до Клубу, зареєструвавшись у розділі \"<b>".$w_about_me."</b>\"";
$w_roz_not_in_club      = "Цей нік не належить до Клубу.";
$w_roz_not_allowed      = "Ви не маєте права доступу до цієї кімнати!";
$w_enter_password_room  = "Ця кімната захищена паролем. Введіть його";
$w_full_access          = "Має доступ до усіх ресурсів";
$w_money                = "Кредів";
$w_classified_info      = "Службова інформація про користувача";
$w_info_browser         = "Браузер";
$w_info_os              = "Операційна система";
$w_info_user_agent      = "Строка USER_AGENT";
$w_grant_access         = "Рекомендувати користувача до повного доступу";
$w_no_money             = "Не вистачає кредів!";
$w_money_exchange       = "Обміняти поінти на кредити";
$w_exchange_tax         = "Курс обіну (кредитів до поінтів)";
$w_howmany_exchange     = "Скільки обміняти поінтів";
$w_exchange_do          = "Обміняти!";
$w_no_credits           = "Не вистачає поінтів!";
$w_security                    = "Налаштування системи безпеки в чаті";
$w_security_warn               = "Нічого не міняйти у цих налаштунках, крім випадків, коли Ви <b>ТОЧНО РОЗУМІЄТЕ, ЩО САМЕ ВИ РОБИТЕ</b>! Інакше чат може працювати неправильно або Ви не зможете зайти під цим логіном!";
$w_limit_by_hash               = "Перевіряти браузер під час спілкування в чаті (рекомендовано)";
$w_limit_by_ip                 = "Перевіряти IP (підмережу) під час знаходження в чаті (рекомендовано)";
$w_limit_by_cookie             = "Перевіряти cookie під час знаходження в чаті (для експертів)";
$w_limit_by_ip_only            = "(ТІЛЬКИ ЕКСПЕРТАМ) Дозволити заходити в чат <b>ВИКЛЮЧНО</b> з цих IP (без пробілів, використовуйте ; для відокремлення)";
$w_security_error              = "Спрацювала система захисту чату і Вам заборонено виконувати цю дію. Спробуйте у профілі позабирати зайві галочки у розділі про безпеку.";
$w_security_error_ip           = "IP-адреса, з якої Ви пробуєте зайти до чату, не зпівпадає з жодною з дозволених власником профіля.";
$w_roz_jail                    = "До саду! :)";
$w_jailed                      = "Вас вигнали до саду! Певно, погано себе поводили...";
$w_roz_jailed_adm              = "<b>~</b> виведений до саду на <b># хвилин.</b> Автор: <b>*</b>";
$w_jail_text                   = "<span class=ha><b>Модератор</b> вивів до саду <b>~</b> на $. Причина: <b>#</b></span>";
$w_roz_jailed_mess             = "Користувач виведений до саду до ";
$w_roz_silenced_mess           = "Користувачеві заборонено розмовляти до ";
$w_already_punished            = "Користувача вже покарано!";
$w_private_sound               = "Програвати звук при надходженні приватного повідомлення (тільки Internet Explorer)";
$w_font_face                   = "Яким шрифтом виводити повідомлення у чаті";
$w_font_size                   = "Який має бути розмір шрифта (% від базового)";
$w_membered_by                 = "Доступ даний по пораді";

//Added by MisterX
$shop                          = "Магазин";
$shop_quantity                 = "Кількість";
$shop_quantity_unlimited       = "Необмежена";
$shop_title                    = "Назва";
$shop_price                    = "Ціна";
$shop_vip                      = "Тільки для VIP";

$w_roz_personal_items          = "Мої речі";

$w_adm_user_add_clan           = "~ додав # до клану ";
$w_adm_user_del_clan           = "~ видалив # з клану ";
$w_adm_user_exchange           = "Поміняно ~ поінтів на # кредитів (було $ кредів, стало %)";
$w_adm_user_buy                = "Придбана річ \"~\" за # кредитів (було $ кредів, стало %)";
$w_adm_user_present            = "Річ \"~\" отримана у подарунок від #";
$w_adm_user_transfer           = "Річ \"~\" передана від #";
$w_adm_user_present_from       = "Річ \"~\" подарована #";
$w_adm_user_transfer_from      = "Річ \"~\" передана # за * кред(ів) (було $ кредів, стало %)";
$w_adm_user_item_used          = "Річ \"~\" використана";
$w_adm_user_item_used_on       = "Річ \"~\" використана на #";
$w_adm_user_item_removed       = "Річ \"~\" видалена успішно";

$w_shop_you_have               = "У Вас є";
$w_shop_other                  = "Інше";
$w_shop_all                    = "Усі";
$w_shop_buy                    = "Придбати";
$w_shop_present                = "Подарувати";
$w_shop_delete                 = "Видалити";
$w_shop_transfer               = "Передати";
$w_shop_back                   = "Повернути";
$w_shop_category_empty         = "У цій категорії товарів нема";
$w_shop_no_items               = "Цей товар вже розпроданий";
$w_shop_no_such_item           = "Такого товару не існує";
$w_shop_no_present             = "Подарунки не можна віддавати чи дарувати комусь іншому";
$w_shop_actions                = "Можливості";
$w_shop_invisibility           = "Кільце невидимості";
$w_shop_invisibility_use       = "Надіти";

$w_money_transfer              = "Переказати кредити іншому користувачеві або до Казни Клану...";
$w_money_transfer_note         = "Майте на увазі, що за переказ з Вашого рахунку автоматично буде знято # кред(ів).";
$w_money_transfer_accept       = "Підтвердіть переказ";
$w_money_transfer_password     = "Будь-ласка, вкажіть нижче власний пароль для підтверждения переказу";
$w_money_transfer_destination  = "Будь-ласка, вкажіть нік користувача, якому Ви хочете переказати гроші <i>АБО</i> поставте галочку МІЙ КЛАН.";
$w_money_transfer_amount       = "Сума (у цілих числах)";
$w_money_transfer_ok           = "Переказ здійснено успішно!";
$w_adm_money_transfer_from     = "# кредитів переказано до ~ (у Вас було $ кредів, стало %)";
$w_adm_money_transfer          = "# кредитів переказано від ~ (у Вас було $ кредів, стало %)";
$w_clan_treasury               = "Казна Клана";
$w_adm_clan_penalty            = "# кредів знято з вини ~ (було $ кредів, стало %)";
$w_adm_clan_rew                = "# кредів додано до Казни як нагорода для ~ (було $ кредів, стало %)";
$w_adm_chaos                   = "Хаос";
$w_adm_chaos_put               = "<b>Модератор</b> відправив до Хаосу <b>~</b> на # хвилин. Причина: ";
$w_adm_chaos_adm               = "<b>*</b> відправив до Хаосу <b>~</b> на # хвилин. Причина: ";
$w_user_chaos                  = "Ви знаходитесь у Хаосі до ~. Це означає, що Ви не можете публікувати репліки в загальний канал (ВИКЛЮЧНО ДО ПРИВАТУ), не можете коментувати у профілях та витрачати гроші.";
$w_roz_chaos_mess              = "Користувач знаходиться у Хаосі до ";

$w_webcam_show                 = "Дозволити іншим користувачам дивитись відео з Вашої вебкамери";
$w_webcam_note                 = "<b>ВАЖЛИВО:</b>Программа <b>WebcamXP</b> має бути встановлена, правильно налаштована та відкрита, щоб Ваші друзі могли бачити Вас. При потребі Ви можете скачати программу з сайта <a href=http://www.webcamxp.com>http://www.webcamxp.com</a>";
$w_webcam_ip_note              = "<b>ВАЖЛИВО:</b> У Вас має бути РЕАЛЬНА IP-адреса, це означає, що Ви маєте бути підключені до Internet напряму, <b>без</b> проксі-сервера чи фаєрвола і не використовуєте локальну мережу.";
$w_webcam_ip                   = "IP Вашого комп'ютера:";
$w_webcam_suggest              = "Скоріше за все, Ваш IP має бути ";
$w_webcam_port                 = "Номер порта для Вашої вебкамери (по замовчуванню 8080):";
$w_webcam_no                   = "Вибачте, але Ви не подключені напряму до Internet (Ви використовуєте проксі-сервер, фаєрвол чи локальну мережу). Перегляд вебкамери неможливий :-(";
$w_clear_nick_after            = "Очищати після відправки";
$w_add_features                = "Додаткові можливості";
$w_about_user                  = "Особиста інформація";
$w_security_opt                = "Безпека";
$w_items_opt                   = "Подарунки";
$w_money_opt                   = "Креди";
$w_webcam_opt                  = "Веб-камера";
$w_another_opt                 = "Інше";

$w_color_nick                  = "Кольоровий нік";
$w_color_nick_note             = "Кольоровий нік можуть поставити собі усі, крім шаманів. <br><b>ВАЖЛИВО:</b> Існуючий графічний або кольоровий нік буде замінено!<br><font color=red><b>Після того, як Ви оберете кольори літер, натисність ОК та ПЕРЕЗАЙДІТЬ ДО ЧАТУ!</b></font>";
$w_color_nick_current          = "Існуючий нік";
$w_color_nick_sample           = "Взірець";
$w_reffered_by                 = "Прийшов по посиланню від";
$w_adm_reffered_payment        = "Отримано # кредів як винагорода за залучення ~ (було $ кредів, стало %)";
$w_adm_reffered_subject        = "Ви отримали винагороду";
$w_adm_reffer_menu             = "Реферали";
$w_adm_reffer_note             = "У цій секції описується робота з Вашими рефералами -- особами, яких Ви запросили до чату. Коли кожен з них досягає рейтингу не менше <b>за ~ поінтів</b>, Ви отримаєте одноразову <b>заохочувальну виплату у розмірі # кредів</b> та <b>*% від суми</b> кожної обмінної операції ПІСЛЯ виплати. Для того, аби запросити людину до чату, просто <b>дайте їй посилання,</b> яке наведено нижче.";
$w_adm_reffer_link             = "Ваше посилання";
$w_roz_points                  = "Поінтів";
$w_days                        = "днів";
$w_clan_money_transfer         = "Переказати креди з Казни Клану на рахунок користувача";
$w_clan_money_transfer_cln     = "# кредів з Казни Клану переказав ~ на рахунок користувача ! (було $ кредів у Казні, стало %)";
$w_photo_reiting               = "Балів";
$w_photo_reiting_do            = "Оцінить фото";
$w_photo_reiting_do_not        = "Ви вже віддали свій голос за цю фотографію!";
$w_photo_reiting_vote          = "Проголосувало";
$w_photo_reiting_take_part     = "Приймати участь у рейтингу фотографій";
$w_mod_remove_photo            = "Видалити фотографію";
$w_mod_remove_photo_adm        = "Фотографію користувача # видалив Модератор $";
$w_mod_remove_photo_user       = "Фото було видалене Модератором як таке, що порушує Правила Чату. Будь-ласка, в майбутньому вставляйте лише ВЛАСНЕ фото,а не фото звірів, квітів та ін. Не слід зловживати чужими фотокартками -- аккаунт може бути заблоковано!";
$w_mod_remove_photo_subj       = "Фото з профіля було видалене";
$w_reg_seconds_left            = " секунд залишилось до початку реєстрації. Будь-ласка, почитайте Правила за цей час ;)";
$w_pass_secutity_time          = "Система безпеки нагадує, що прийшов час міняти пароль!";
$w_pass_secutity_note          = "Гарний пароль має:".
                                 "<ul><li>... бути не менше 8 символів у довжину чи більше, що спонукає використовувати великі слова або декілька слів;".
                                 "<li>... складатись з символів верхнього та нижнього регістрів, цифр та різних розкладок;".
                                 "<li>... не бути відомим словом, виразом або словосполученням;".
                                 "<li>... не бути цифрою, іменем, номером телефона, ICQ чи чимось, що можна асоціювати з Вами;".
                                 "<li>... бути створений випадковим чи напіввипадковим способом.</ul>";
$w_pass_secutity_check         = "Періодично нагадувати мені змінити пароль";
$w_pass_secutity_alert         = "Ваш пароль застарів! Змінить його, будь-ласка, у розділі МОЄ ІНФО!";
$w_chat_welcome_main           = "Ласкаво просимо до нашого чату! Не забудьте прочитати наші &quot;<a href=rules.php><font color=white>Правила</font></a>&quot;!";
$w_chat_welcome_text           = "Якщо Вам тут сподобається і Ви захочете зберегти за собою цей нік, то, будь-ласка, у чаті виберіть пункт \"Реєстрація\" -- червоний крайній правий пункт меню.";
$w_chat_welcome_note           = "треба 1 раз при створенні нового ніку";
$w_chat_go                     = "ДО ЧАТУ";
?>