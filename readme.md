Тестовое задание ispring https://github.com/ispringtech/coding-interview/blob/master/backend/README.md

Для запуска приложения локально необходимо скопировать проект с github:

>git clone https://github.com/arivlav/testforispring.git

Открыть папку с проектом:

>cd testforispring

С помощь composer скачать все необходимые библиотеки
>composer install

В файле .env прописать настройки для базы данных. Н-р:
...
DATABASE_URL="mysql://root:1234@127.0.0.1:3306/todolist?serverVersion=5.7&charset=UTF8"
...

Создать базу 
>php bin/console doctrine:database:create

Применить миграции для создания таблиц в БД
>php bin/console doctrine:migrations:migrate

перейдите в папку public и запустите встроенный в php локальный веб-сервер
>cd public
>php -S php -S localhost:8080

Доступны следующие маршруты:
GET     localhost:8080/V1/tasks   - получение списка всех активных задач
POST    localhost:8080/V1/tasks   - создание задачи
PUT     localhost:8080/V1/tasks/2 - изменение статуса задачи с id = 2 c "активной" на "выполненную"
DELETE  localhost:8080/V1/tasks/2 - удаление задачи с id = 2
GET     localhost:8080/V1/tasks/archive - получение списка всех выполненных задач