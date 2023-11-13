# VOCFE

**VOCFE: VOC++ Final Edition.**


# Changelog:

- Весь проект теперь использует UTF-8 кодировку
- Вместо Daemon теперь используется Pusher websocket (pusher.com), удалены старые настройки daemon и скрипт автозапуска
- Поддержка PHP 8.1
- Весь код постепенно будет отформатирован до PSR-12 стиля, очистка мусора в коде
- Произведена докеризация (проект содержит образ Docker)
- Викторина QUIZ подключена к базе чата напрямую, удалены настройки базы с конфигурации викторины
- Удален стандартный встроенный бот 
- Удалена стандартная капча, подготовка к подключению Google reCaptcha

# Установленны моды
- Quiz v3.0
- [New Admin v2.0](https://mvoc.ru/post/130)
- [Вложения v2.0](https://mvoc.ru/post/attachments)

# Commands

Запуск локально с помощью docker:

`docker-compose down && docker-compose up --build -d`

---

# URLS

Localhost
- [PhpMyAdmin](http://localhost:8081/index.php)