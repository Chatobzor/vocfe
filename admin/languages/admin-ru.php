<?php

$adm_lang = "Русский";
$adm_lang_common = "admin-ru.php";
$adm_configuration = "Конфигурирование";
$adm_administration = "Администрирование";
$charset = "utf-8";
$adm_login_promt = "Пожалуйста, введите имя пользователя (логин) и пароль администратора:";
$adm_login = "Логин";
$adm_password = "Пароль";
$adm_login_do = "Войти";
$adm_first_run = "Вы должны прочесть, понять и принять условия лицензии перед использованием";
$adm_accept = "ПРИНИМАЮ";
$adm_dont_accept = "Не принимаю";
$adm_change_default = "Пожалуйста, измените имя пользователя и пароль, которые даны по умолчанию (они редактируются в файле <b>chat/admin/admin_users.php</b>)!";
$adm_cannot_open = "Не могу открыть";
$adm_cannot_write = "Не могу записать в ";
$adm_note = "ВНИМАНИЕ";
$adm_permissions = "Пожалуйста, проверьте права доступа для";
$adm_webserver = "Права должны быть выставлены таким образом, что бы сюда мог записывать пользователь, от которого запущен веб-сервер (скорее всего, 0777).";
$adm_step = "Шаг";
$adm_try_to_locate = "Пытаюсь найти";
$adm_found = "найдено";
$adm_not_found = "Ничего не найдено";
$adm_writeable = "Доступен для записи";
$adm_incorrect_tip = "Если путь не отвечает действительному, не забудьте отредактировать его";
$adm_directory = "директорию";
$adm_slash_tip = "Путь должен заканчиваться слешем!";
$adm_test = "Тест";
$adm_looks_ok = "Все в порядке";
$adm_next = "Далее";
$adm_saving_data = "Сохраняем";
$adm_reloading_conf = "Перезагружаем конфигурационный файл (voc.conf)";
$adm_chat_url = "URL чата";
$adm_chat_url_tip = "URL, который нужно набрать в браузере, что бы попасть на стартовую страничку Вашего чата. <b>Путь должен заканчиваться слешем!</b> Скорее всего, путь похож на";
$adm_daemon_url = "URL демона";
$adm_daemon_url_tip = "URL, на который пользовательский браузер будет посылать запросы на соединение с демоном. <b>Путь НЕ должен заканчиваться слешем!</b> Скорее всего, путь похож на";
$adm_daemon_url_tip2 = "Демон эмулирует HTTP-сервер, потому не нужно ничего добавлять после имени домена. Одной из распространенных ошибок является указание вместо адреса сервера чего-то вроде <b>http://server.com/data/daemon/daemon.pl</b>!";
$adm_daemon_port = "Порт демона";
$adm_daemon_port_tip = "Демон будет ожидать запросов на соединения от браузеров пользователей, прослушивая этот порт. Если демон будет запущен не от имени администратора сервера (root), то Вы не можете выбрать порт, номер которого меньше 1024 или больше 65535!";
$adm_daemon_listen = "Принимать соединения от";
$adm_daemon_listen_tip = "Этот IP (который отвечает одному сетевому интерфейсу) демон будет прослушивать на предмет запросов на соединения. Рекомендуется выставить значение этого параметра как <b>0.0.0.0</b> -- \"прослушивать все интерфейсы\". Так же Вы можете попробовать использовать имя домена вместо IP.";
$adm_back = "Назад";
$adm_save = "Сохранить";
$adm_user_search = "Поиск пользователей";
$adm_inactive = "неактивных последние";
$adm_months = "месяцев";
$adm_search_results = "Результаты поиска";
$adm_user_view = "Нажмите на ник, что бы отредактировать профиль пользователя";
$adm_check_delete = "Отметьте ники, которые следует удалить";
$adm_delete = "Удалить";
$adm_check_uncheck = "Отметить / Снять выделение со всех";
$adm_admin_tools = "Административные утилиты";
$adm_admin_tools = "Административные утилиты";
$adm_moder_list = "Список модераторов";
$adm_rooms_admin = "Администрирование комнат";
$adm_robik_class = "Обучение бота";
$adm_canon_nicks = "Сгенерировать канонические ники";
$adm_new_moder = "Для того, что бы добавить нового модератора, сначала воспользуйтесь поиском по нику и в режиме редактирования профиля выставьте нужные права";
$adm_welcome = "Добро пожаловать в Административную панель Вашего чата!";
$adm_welcome1 = "Для начала работы выберете подходящий пункт из меню слева.";
$adm_welcome2 = "Если у Вас есть вопросы по VOC++, посетите наши форумы технической поддержки.";
$adm_welcome3 = "Общий форум:";
$adm_welcome4 = "Для зарегестрированных пользователей:";
$adm_welcome5 = "Поддержка по E-Mail (только для зарегестрированных!):";
$adm_user = "У пользователя под ником";
$adm_has_rights = "есть привилегии на";
$w_roz_custom_login = "Приветствие при входе (<b>#</b> заменяет ник пользователя)";
$w_roz_custom_logout = "Фраза при выходе (<b>#</b> заменяет ник пользователя)";
$w_roz_chat_status = "Специальный статус пользователя";
$w_roz_style_start = "Открывающий тег собственного стиля";
$w_roz_style_end = "Закрывающий тег собственного стиля";
$adm_update = "Обновить";
$adm_vip_message = "Является ПОЧЕТНЫМ ГОСТЕМ (Внимание! У этой персоны не может быть прав модератора!)";
$adm_shaman_message = "Является ШАМАНОМ (Внимание! У ШАМАНА не может быть прав модератора!)";
$adm_rooms_updated = "Данные комнат успешно обновлены!";
$adm_rooms_name = "Имя комнаты";
$adm_topic = "Тема при входе в комнату (топик)";
$adm_predefined = "Дизайн по умолчанию";
$adm_bot_name = "Имя бота";
$adm_premoderate = "Пре-модерируемая комната";
$adm_new = "Новая";
$adm_predefined_note = "<b>Дизайн по умолчанию</b> -- если Вы выбираете один из дизайнов, как дизайн \"по умолчанию\", то ВСЕ пользователи в данной комнате будут видеть именно его. <b>В противном случае, если Вы хотите дать пользователям возможность самим выбирать дизайн комнаты, не выставляйте никакого значения <nobr>('---')!</nobr></b>";
$adm_premoderated_note = "<b>Пре-модерируемая комната</b> -- это специальный тип комнаты, предназначеный для проведения он-лайн общения с важными гостями (VIP-персонами). Все сообщения от обычных пользователей (не VIP и не модераторов) сначала проверяются модераторами, а потом уже становятся видимыми всем. Грубые сообщения модераторы могут удалить.</b>";
$adm_rob_note = "Внимание: символ тильда (<b>~</b>) будет заменен на ник отправителя";
$adm_rob_questions = "Вопрос боту";
$adm_rob_answers = "Ответ бота";
$adm_rob_probability = "Вероятность";
$adm_reset = "Сброс";
$adm_new_word = "Новый";
$adm_generating_can = "Генирируем канонические ники";
$adm_other_tools = "Утилиты";
$adm_sm_convert = "Редактор смайлов";
$adm_statistics = "Статистика чата";
$adm_engines = "Движки";
$adm_engines_sysV = "Проверяем, поддерживает ли сервер технологию <b>SysV Shared memory</b>";
$adm_shared_exists = "PHP скомпилировано с поддержкой SysV IPC: функция <b>shmop_open</b> доступна! Вы можете работать с движком Shared Memory.";
$adm_shared_not_exst = "PHP <b>НЕ скомпилировано</b> с поддержкой SysV IPC: функция <b>shmop_open</b> недоступна! Вы не сможете использовать один из движков чата, основанный на технологии SharedMemory.";
$adm_select_main_eng = "Выберите <b>основной движок хранения данных</b> (отвечает за сообщения, список посетителей, список игнорируемых посетителей)";
$adm_select_add_eng = "Выберите <b>дополнительный движок хранения данных</b> (отвечает за анкеты пользователей, комментарии в анкетах, &quot;личные&quot; письма)";
$lng_engines[0] = "Файлы";
$lng_engines[1] = "СУБД MySQL";
$lng_engines[2] = "Технология SysV Shared Memory";
$adm_mysql_settings = "Параметры соединения с сервером СУБД MySQL";
$adm_mysql_server = "Сервер MySQL (доменное имя или IP)";
$adm_mysql_username = "Имя пользователя MySQL";
$adm_mysql_password = "Пароль для имени пользователя MySQL";
$adm_mysql_db_name = "Имя базы данных MySQL";
$adm_mysql_error = "!!!! ОШИБКА: невозможно соединиться с MySQL сервером, используя указанные параметры !!!!";
$adm_mysql_error_db = "ОШИБКА: отсутствует база данных";
$adm_chat_types = "Типы чатов";
$adm_chat_types_tip = "Различные типы чатов, которые будут доступны пользователям. Мы рекомендуем использовать <b>'tail'</b> или <b>'php_tail'</b>";
$adm_shamans_list = "Список шаманов";
$adm_clans_list = "Кланы";
$adm_refresh = "обновить";
$adm_check_stat_for = "посмотреть статистику за";
$adm_today = "сегодня";
$adm_yesterday = "вчера";
$adm_users = "Пользователи за";
$adm_messages_per_m = "Сообщений в минуту за";
$adm_blue_expl = "синим выделены 'общие' сообщения";
$adm_green_expl = "зеленым выделены 'приватные' сообщения";
$adm_red_expl = "красным указано общее количество ('общих' и 'приватных') сообщений";
$adm_sm_note = "ВНИМАНИЕ: Пожалуйста, удалите все неграфические файлы из директории 'converts'";
$adm_status_new = "новый";
$adm_sm_instructions = "<b>Использование</b>:<br>".
    "- Во-первых,  удалите все неграфические файлы из директории 'converts';<br>".
    "- Этот скрипт автоматически покажет все файлы, опознанные как смайлы в директории 'converts'. Удалите из директории неопознанные файлы (например, файл 'to_remove');<br>".
    "- Вы можете изменить строку подстановки для каждого смайла в отдельности;<br>".
    "- Если необходимость в каких-то смайлах отпала, их можно удалить (строка подстановки должна быть пустой);<br>".
    "- Что бы добавить новые смайлы в набор, достаточно поместить их в директорию 'converts' и снова запустить этот скрипт!<br>".
    "- Скрипт НЕ ПОКАЗЫВАЕТ лишние файлы из директории 'converts', потому Вам придется удалить 'мусор' самостоятельно;<br>".
    "- Если смайл физически находится в директории, но не проиндексирован, его статус показывается как <b>$adm_status_new</b>!";
$adm_status_old = "Старый";
$adm_congratulations = "Поздравляем";
$adm_smileys_writed = "смайлов было записано в файл converts.dat";
$adm_smileys_in_dir = "файлов в директории 'converts'";
$adm_status = "Статус";
$adm_smile = "Смайл";
$adm_smile_promt = "Строка подстановки";
$adm_smile_common = "Общий";
$adm_smile_yes = "ДА";
$adm_smile_no = "НЕТ";
$adm_smile_make = "сделать";
$adm_smile_undo = "отменить";
$adm_smile_see_again = "Перечитать файлы еще раз после сохранения/загрузки";
$adm_smile_save_upl = "Сохранить/Обновить";
$adm_nofiles = "Директория 'converts' пуста!";
$adm_options_and_lim = "Настройки и ограничения чата";
$adm_email = "E-mail администратора чата";
$adm_email_note = "На этот адрес электронной почты будут приходить комментарии пользователей";
$adm_time_offset = "Разница во времени";
$adm_time_hours = "часов";
$adm_time_note = "Разница во времени между Вашим компьютером и сервером. (Время на компьютере - Время на сервере)";
$adm_time_guess = "Скорее всего, нужно поставить";
$adm_mailbox_size = "Максимальный размер почтового ящика (в байтах)";
$adm_mailbox_note = "В почтовом ящике сохраняются комментарии посетителей к анкетам. Если общий размер комметариев для одной анкеты превысит эту величину, больше комментрировать будет нельзя";
$adm_disconnect = "Время автоотключения (в секундах)";
$adm_disconnect_note = "В случае, если пользователь не обновляет ни одно из окон, система будет считать что он присутвует только на протяжении этого времени";
$adm_history_size = "Размер истории сообщений";
$adm_history_note = "Максимальное количество сообщений, которое выводится на стартовой страничке и при заходе в чат. Если Вы не хотите, что бы читали историю Вашего чата, поставьте 0";
$adm_photo_limits = "Ограничения для фотографий";
$adm_maximum_size = "Максимальный размер";
$adm_size_bytes = "байт";
$adm_maximum_width = "Максимальная ширина";
$adm_maximum_height = "Максимальная высота";
$adm_size_pixels = "пикселов";
$adm_size_note = "Ограничения для фотографий в анкетах пользователей. Поставьте 0 для снятия ограничений";
$adm_maximum_users = "Максимальное количество пользователей в чате";
$adm_maximum_usr_not = "Максимальное количество пользователей, которые могут одновременно находиться в чате. Этот параметр помогает защитится от ник-флудинга";
$adm_max_conn_ip = "Максимальное количество соединений с одного IP-адреса";
$adm_max_conn_ip_not = "Ограничивает количество одновременных соединений с одного компьютера/прокси";
$adm_max_capital = "Максимальное количество ЗАГЛАВНЫХ БУКВ в сообщении";
$adm_max_cap_note = "Если пользователь использовал заглавных букв больше, чем указано тут, то все сообщение будет автоматически переведено в нижний регистр.".
    "Вы <b>должны правильно выставить локаль на сервере</b>, что бы функции strotoupper и strtolower работали корректно. Одной из распространенных ошибок является подмена понятия 'локаль' (locale) понятием 'кодовая страница' (codepage) - это разные вещи!".
    "Выставьте 0 для отмены проверки";
$adm_max_smileys = "Максимальное количество смайлов";
$adm_max_smileys_not = "Максимальное количество смайлов, которые пользователь может одновременно использовать в своем сообщении";
$adm_flood_protect = "Защита от флуда";
$adm_on = "Вкл.";
$adm_off = "Выкл.";
$adm_time_messages = "Минимальный период в секундах, который должен пройти между идущими подряд сообщениями пользователя";
$adm_check_last = "Проверять на аналогичное сообщение в последних";
$adm_max_messages = "сообщениях (максимум 40)";
$adm_user_access_lim = "Настройки доступа для пользователей";
$adm_nick_note = "Параметры выбора ника";
$adm_nick_min_len = "минимальная длинна";
$adm_nick_max_len = "максимальная длинна";
$adm_nick_avail_char = "Доступные символы в нике (для функции grep)";
$adm_nick_av_chr_not = "Будьте осторожны с этим полем. Его значением должно быть особым образом составленое <b>регулярное выражение</b>. По умолчанию следует взять значение <b>_a-zA-Z0-9</b>";
$adm_similar_select = "Выберите функцию проверки на схожие ники";
$adm_similar_note = "К примеру, 'схожими' никами будут считаться abc и ABC, хотя они в разных регистрах . Гляньте в chat/inc_to_canon_nick.php для детального ознакомления с каждой из функций. ".
    "Если Вы изменили значение этого параметра с уже набранной базой пользователей, <b>обязательно сгенерируйте канонические ники заново!</b>";
$adm_club_mode = "Обязательная регистрация (клубный режим)";
$adm_club_note = "Пользователь обязан будет зарегестрироваться, что бы войти в чат. <b>ДОЛЖНО БЫТЬ ВКЛЮЧЕНО.</b>";
$adm_image_protected = "Регистрация з защитой по изображению";
$adm_image_pr_note = "Для того, что бы зарегестрироваться, пользователь должен будет ввести четырехзначный цифровой код, который будет виден на сгенерированном изображении. Помогает защитить базу данных от ник-флудинга специальными хакерскими скриптами";
$adm_email_conf = "Регистрация с подтверждением по e-mail";
$adm_email_conf_note = "Регистрация должна быть активизирована при помощи специального кода, который высылается на e-mail пользователя";
$adm_max_nicks_email = "Максимальное количество ников, которые можно зарегестрировать с одного e-mail адреса";
$adm_max_nicks_note = "Работает только если включена 'Регистрация с подтверждением по e-mail'";
$adm_add_features = "Дополнительно";
$adm_store_statistic = "Сохранять статистику";
$adm_store_note = "Вам нужно будет создать cronjob (для UNIX-систем) для регулярного периодического запуска <b>update_statistic.php</b> (например, раз в 15 минут) перед тем, как включать эту опцию. Загляните в README";
$adm_en_web_indicat = "Включить веб-индикатор";
$adm_en_web_ind_note = "Пользователи смогут размещать иконку на своих сайтах, которая будет показывать, в чате ее владелец или нет";
$adm_en_logging = "Лог чата";
$adm_log_messsages = "Запоминать сообщения (вести журнал чата)";
$adm_log_bans = "Запоминать бан/разбан";
$adm_log_note = "Файлы логов физически находятся в директории <b>[data]logs/</b>. Именуются файлы по стандарту YYYY-mm-dd.log, обновляются в реальном времени. ".
    " <b>Вы должны удалять ненужные логи самостоятельно!</b>";
$adm_mod_voc_socket = "Сокет для <b>mod_voc</b> ";
$adm_modvoc_note = "Сокет UNIX, который предназначен для коммуникации между демоном и веб-сервером Apache при использовании mod_voc. Рекомендуется, что бы путь указывал в Ваш домашний каталог. По умолчанию он равен <b>/tmp/vochat/</b>";
$adm_locale = "Системная локаль";
$adm_locale_note = "Корректное имя языковой системной локали";
$adm_listen_type = "Режим работы демона";
$adm_listen_standart = "Стандартный режим, демон прослушивает указанный сокет [IP]:[порт]";
$adm_listen_mod = "Режим <b>mod_voc</b>. Работает исключительно с веб-сервером Apache, который передает соединения демону (для пользователя порты демона и веб-сервера одинаковы). Вам необходимо установить <b>mod_voc</b> для корректной работы этого режима. В случае использования демона на С++, его необходмо откомпилировать с поддержкой <b>mod_voc</b>";
$adm_listen_note = "Если Вы не знаете или четко не понимаете, что это такое, оставьте выбор по умолчанию";
$adm_look_and_feel = "Внешний вид";
$adm_system_language = "Язык системы";
$adm_sys_lang_note = "Язык сообщений системы. Просмотрите директорию <b>chat/languages</b>";
$adm_avail_lang = "Языки, доступные для выбора пользователями";
$adm_avail_lang_note = "Языки интерфейса чата.  Просмотрите директорию <b>chat/languages</b>";
$adm_charset = "Кодировка";
$adm_charset_note = "Кодировка для html-страниц (тег <meta ... >). По умолчанию iso-8859-1, настоятельно рекомендуем <b>utf-8</b>";
$adm_chat_designes = "Дизайны чата";
$adm_designes_note = "Имя дизайна чата, который будет видеть пользователь. Проверьте директорию <b>chat/designes</b> на предмет доступных дизайнов";
$adm_default_design = "Дизайн по умолчанию";
$adm_def_des_note = "Дизайн по умолчанию выбирается для пользователя, который первый раз вошел в чат и не может определиться с предпочтениями. Значание этого поля должно быть именем одного из установленых дизайнов, в противном случае первый из них будет выбран в качестве 'назначенного'";
$adm_show_in_separ = "Показывать приватные сообщения в отельном фрейме";
$adm_show_sp_note = "Приватные сообщения \"от\" и \"к\" пользователю будут показываться в отдельном фрейме, снизу фрейма общих сообщений.".
    "Работает только и исключительно с дизайном <b>rozmova</b> и типами чата <b>'непрерывный'</b> (tail, с демоном) и <b>'непрерывный на php'</b> (php_tail)";
$adm_keep_whispering = "Продолжать шепот";
$adm_keep_note = "Не удалять ник адресата после отправки сообщения из поля для ввода ника, так же разрешать выбор нескольких ников в общем канале";
$adm_user_color = "Использовать цвет сообщений для отрисовки ника";
$adm_user_color_note = "Ник автора сообщения будет выводиться тем же цветом, что и само сообщение (опция <b>ДА</b>) или же цветом по умолчанию (опция <b>НЕТ</b>)";
$adm_message_formats = "Шаблоны сообщений. Вы можете использовать следующие коды";
$adm_code = "Код";
$adm_action = "Описание";
$adm_act_msg_note = "тело сообщения";
$adm_act_nick_note = "Ник автора сообщения";
$adm_act_nick_wo_tag = "Ник автора без всякого html, используется для JavaScript (например, в кликабельных никах)";
$adm_act_to_note = "Ник адресата (для приватных сообщений)";
$adm_private_note = "заменяется на 'кому-то шепчет') (параметр <b>\$"."w_whisper_to</b> в языковом файле)";
$adm_hours_time_note = "Часы отправки сообщения";
$adm_mins_time_note = "Минуты отправки сообщения";
$adm_mins_time_note = "Секунды отправки сообщения";
$adm_avatar_note = "тег img с аватаром (маленьким фото) автора";
$adm_normal_message = "'Обычные' сообщения для их автора";
$adm_private_author = "'Приватные' сообщения для их автора";
$adm_normal_all = "'Обычные' сообщения для всех, <b>кроме</b> автора";
$adm_private_to = "'Приватные' сообщения для адресата";
$adm_whisper_to_som = "Шаблон 'приватного' сообщения для всех, <b>кроме автора и адресата</b> ('кому-то шепчет')";
$adm_whisper_note = "Отформатируйте сообщение для пользователей, которые должны знать сам факт отправки такого сообщения, но не должны видеть само сообщение (не являются авторами или адресатами приватных сообщений). [PRIVATE] будет заменено на значение параметра \$"."w_whisper_to из языкового файла. ".
    "<b>Что бы избавиться от сообщений 'кому-то шепчет', просто оставьте это поле пустым</b>";
$adm_format_reset = "Сбросить настройки на стандартные";
$adm_reset_simple = "Простые сообщения";
$adm_clickable_nicks = "Кликабельные ники";
$adm_highligt_nick = "Подсветка ника в сообщении";
$adm_tag_before = "Открывающий тег";
$adm_tag_after = "Закрывающий тег";
$adm_highlight_note = "Если Ваш ник в чате -- \"User\" и кто-то напишет Вам:\"Привет, User!\", то Вы увидите \"Привет, <b>User</b>!\"".
    "Все остальные пользователи увидяит лишь \"Привет, User!\"";
$adm_high_inside = "Подсветка сообщения, если ник находится в его теле";
$adm_high_inside_not = "В дополнение к предыдущей 'подсветке ника' Вы можете обрамить всё сообщение тегами";
$adm_enable_modify = "Форматирование сообщений пользователем";
$adm_enable_bold = "можно выделять сообщения <b>жирным начертанием</b>";
$adm_enable_italic = "можно выделять сообщения <i>курсивом</i>";
$adm_enable_underlin = "можно <u>подчеркивать</u> сообщения";
$adm_yes = "ДА";
$adm_no = "НЕТ";
$adm_check_access = "Проверка доступа к необходимым файлам и  базе данных";
$adm_checking_data = "Проверяем файлы в <b>data</b>-директории";
$adm_cannot_detect = "Не могу определить корректное имя для ";
$adm_checking_subdir = "Проверяем подкаталоги";
$adm_safe_mode_note = "<b>ВНИМАНИЕ!</b> Вы используете PHP в безопасном режиме (safe mode), так что Вам необходимо самостоятельно создать <br> $real_name/0<br>$real_name/1<br>...<br> (например при помощи ftp-клиента), один подкаталог на каждых 2000 зарегестрированных пользователя. Эти подкаталоги должны принадлежать пользователю UNIX, от имени которого работает php и должны быть доступны для записи (chmod 0777)";
$adm_cannot_write_di = "Не могу записать в директорию";
$adm_table = "Таблица";
$adm_table_cr_or_ex = "создана или уже существует";
$adm_table_not_creat = "не создана";
$adm_table_updated = "обновлена для новой версии";
$adm_update_of_table = "обновление таблицы";
$adm_failed = "завершено неудачно";
$adm_check_daemon = "Проверка демона";
$adm_conf_success = "<b>Конфигурирование чата завершено.</b> Самое время запустить демона и войти в чат!";
$adm_gen_canon_ok = "Канонические ники сгенерированы успешно!";
$adm_shaman_search = "Создается список шаманов";
$adm_new_shaman = "Для того, что бы добавить нового шамана, сначала воспользуйтесь поиском по нику и в режиме редактирования профиля выставьте нужные права";
$adm_use_guardian = "Защитить базу данных при помощи VOC++ Guardian";
$adm_guardian_note = "VOC++ Guardian является защитным механизмом, который призван проверять целостность и восстанавливать файл users.dat (основной ключевой файл для базы данных пользователей на файловом движке) в автоматическом режиме. ".
    "<b>Обязательно откалибруйте Guardian при включении этой опции (пункт меню \"Утилиты -> VOC++ Guardian\")!</b>";
$adm_guardian_calib = "Калибруем VOC++ Guardian";
$adm_guardian_c_ok = "VOC+ Guardian откалиброван успешно";
$adm_reconstruct_idx = "Восстановить поврежденные индексы";
$adm_reconstruct_p = "Восстанавливаем поврежденные индексы";
$adm_rec_idx_ok = "Восстановление завершено";
$adm_open_chat = "Открыть чат для посетителей";
$adm_open_chat_tip = "Если снять галочку, то только администраторы с классом доступа \"бан модераторов\" смогут попасть в чат. Удобно использовать при внесении изменений/восстановлении чата";
$adm_allow_del_logs = "Guardian может удалять старые лог-файлы";
$adm_allow_del_lg_n = "Guardian может удалять старые лог-файлы, если свободного места на разделе осталось очень мало (< 10 Мбайт)";
$adm_allow_multipl = "Разрешить посылать \"общее\" сообщение нескольким адресатам";
$adm_allow_mul_note = "Пользователь сможет выбирать нескольких адресатов для своих \"общих\" сообщений. Каждый из пользователей в своем профиле сможет настроить это свойство индивидуально";
$adm_enable_gzip = "Разрешить PHP выводить данные, сжатые GZIP-ом";
$adm_enable_gzip_not = "РНР сможет выводить данные основных 'тяжелых' фреймов, предварительно упаковав их GZIP-ом. Интерпретатор PHP должен поддерживать функцию <nobr><i>ob_start(\"ob_gzhandler\")</i>.</nobr> Эта опция позволяет сберечь траффик и ускорить вывод страницы чата";
$adm_mysql_import = "Импорт из MySQL";
$adm_mysql_import_no = "Эта утилита импортирует существующие профили пользователей из MySQL-версии базы Voodoo Chat к файловой, которую использует VOC++. <b>ВНИМАНИЕ! Будут стерты ВСЕ существующие профили в базе на файлах!</b>";
$adm_mysql_prefix = "Префикс MySQL таблиц";
$adm_plugin_not_found = "Подключаемый модуль # не найден!";
$adm_plugin_language = "Язык";
$adm_plugin_author = "Автор";
$adm_plugin_description = "Описание";
$adm_plugin_eng_supp = "Поддерживаемый движок плагинов";
$adm_plugin_enabled = "ВКЛЮЧЕН";
$adm_plugin_disabled = "ВЫКЛЮЧЕН";
$adm_gen_similar_table = "Перестроить таблицу поиска ников";
$adm_gen_similar_table_pr = "Индексируются ники для быстрого поиска мультивходов";
$adm_gen_similar_table_ok = "Индексация прошла успешно";
$adm_plugin_more = "Дополнительно";
$adm_rooms_club = "Ограничить доступ";
$adm_rooms_pass = "Пароль";
$adm_rooms_jail = "Сад :)";
//Added by MisterX
$adm_shop_manager = "Магазин";
$adm_shop_manager_itmes = "Товар";
$adm_shop_manager_cats = "Категории";
$adm_shop_manager_log = "Логи магазина";
$adm_shop['Title'] = "Название";
$adm_shop['Price'] = "Цена";
$adm_shop['Operations'] = "Операции";
$adm_shop['Op_Add'] = "Добавить";
$adm_shop['Op_Del'] = "Удалить";
$adm_shop['Op_Edit'] = "Редактировать";
$adm_shop['Picture'] = "Картинка";
$adm_shop['Item'] = "товар";
$adm_shop['Quantity'] = "Количество";
$adm_shop['Saled'] = "Продано";
$adm_shop['Unlimited'] = "Неограничено";
$adm_shop['VIP'] = "Только для VIP";
$adm_shop['Category'] = "Категория";
$adm_shop['Action'] = "Действие";
//added by DD
$adm_register_all = "Перерегистрировать пользователей";
$adm_register_prog = "Заново регистрируем пользователей";
$adm_rooms_points = "Начислять поинты";
$adm_image_url = "Дополнительный URL для изображений";
$adm_image_tip = "Этот URL <b>должен заканчиваться слешем</b>! Используйте этот параметр, если у Вас в системе установлен thttpd, nginx или другой \"легкий\"  веб-сервер для отдачи статического контента.<b>Не забудьте перегенерировать смайлы после изменения параметра!</b>";
$adm_md5_salt = "Соль MD5";
$adm_md5_salt_value = "Введите сюда случайное значение. Это значение должно быть выставлено ТОЛЬКО РАЗ. Изменение его на рабочем чате повлечет сбой ВСЕХ паролей";

$adm_choose_language = "Язык интерфейса";
$adm_photo_reiting = "Разрешить рейтинг фотографий";
//
$adm_zerolize = "Обнулить";
$adm_zerolize_reiting = "Рейтинг (активный)";
$adm_zerolize_reiting_all = "Рейтинг (накопленный)";
$adm_zerolize_photo = "Рейтинг фотографий";
$adm_zerolize_credits = "Кредиты";
$adm_zerolizing = "Обнуляем";
?>