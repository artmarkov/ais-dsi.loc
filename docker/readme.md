## Запуск проекта в docker

Создаем env-файл для docker-compose
```
# cp .env.example .env
```

### Сборка образов

Исходное состояние - нет образов, нет volumes, нет контейнеров
```
# docker image list
REPOSITORY          TAG                 IMAGE ID            CREATED             SIZE

λ docker volume list
DRIVER              VOLUME NAME

# docker ps -a
CONTAINER ID        IMAGE               COMMAND             CREATED             STATUS              PORTS               NAMES

```

Сборка
```
# docker-compose build
Building postgres
Step 1/6 : FROM postgres:10.1
10.1: Pulling from library/postgres
723254a2c089: Pull complete
39ec0e6c372c: Pull complete
ba1542fb91f3: Pull complete
c7195e642388: Pull complete
95424deca6a2: Pull complete
2d7d4b3a4ce2: Pull complete
fbde41d4a8cc: Pull complete
880120b92add: Pull complete
9a217c784089: Pull complete
d581543fe8e7: Pull complete
e5eff8940bb0: Pull complete
462d60a56b09: Pull complete
135fa6b9c139: Pull complete
Digest: sha256:3f4441460029e12905a5d447a3549ae2ac13323d045391b0cb0cf8b48ea17463
Status: Downloaded newer image for postgres:10.1
 ---> ec61d13c8566
Step 2/6 : ENV TZ=Europe/Moscow
 ---> Running in 956d8d2bc350
Removing intermediate container 956d8d2bc350
 ---> e4ca915a1d3e
Step 3/6 : RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
 ---> Running in 9124875ce2b1
Removing intermediate container 9124875ce2b1
 ---> 51ff768affc8
Step 4/6 : RUN localedef -i ru_RU -c -f UTF-8 -A /usr/share/locale/locale.alias ru_RU.UTF-8
 ---> Running in 2ccf36b8512d
Removing intermediate container 2ccf36b8512d
 ---> 7d1f1238b535
Step 5/6 : ENV LANG ru_RU.utf8
 ---> Running in aec23d729e5f
Removing intermediate container aec23d729e5f
 ---> 7497fbab525a
Step 6/6 : COPY ./setup.sh /docker-entrypoint-initdb.d/20-setup.sh
 ---> 0b1f88f4c278

Successfully built 0b1f88f4c278
Successfully tagged fdocpg_postgres:latest
Building php
Step 1/12 : FROM yiisoftware/yii2-php:7.3-apache
7.3-apache: Pulling from yiisoftware/yii2-php
6ae821421a7d: Pull complete
08f3d19635b0: Pull complete
dc8a54b8000b: Pull complete
b2c1d103db99: Pull complete
edfa752aa38a: Pull complete
583d37cbf2f0: Pull complete
c7846a240c1d: Pull complete
cc0bc6e8c5cf: Pull complete
43635160a7c0: Pull complete
56065ffbc975: Pull complete
3ceca89a6c41: Pull complete
c20c1f22c2a2: Pull complete
1067d022a98a: Pull complete
98df9168cabe: Pull complete
c8c247314953: Pull complete
49f3757deaac: Pull complete
1d45acbc361d: Pull complete
5379974a61d2: Pull complete
5c284efc14f0: Pull complete
b8fe6c42bc77: Pull complete
74c54c38e834: Pull complete
960e0ebf0e85: Pull complete
e51fc86bbae8: Pull complete
22f412039dff: Pull complete
Digest: sha256:fcf41f4eab29d99771784b1018977483724081ead6c09d1de3e79f1cd7f60e22
Status: Downloaded newer image for yiisoftware/yii2-php:7.3-apache
 ---> e484ad63d6ad
Step 2/12 : RUN a2enmod rewrite
 ---> Running in 6e8ef990535b
Module rewrite already enabled
Removing intermediate container 6e8ef990535b
 ---> 62481d81c936
Step 3/12 : RUN a2enmod ssl
 ---> Running in ea9e99393131
Considering dependency setenvif for ssl:
Module setenvif already enabled
Considering dependency mime for ssl:
Module mime already enabled
Considering dependency socache_shmcb for ssl:
Enabling module socache_shmcb.
Enabling module ssl.
See /usr/share/doc/apache2/README.Debian.gz on how to configure SSL and create self-signed certificates.
To activate the new configuration, you need to run:
  service apache2 restart
Removing intermediate container ea9e99393131
 ---> eafe0dd12f1a
Step 4/12 : RUN a2enmod dav
 ---> Running in 12f4e1f728e9
Enabling module dav.
To activate the new configuration, you need to run:
  service apache2 restart
Removing intermediate container 12f4e1f728e9
 ---> 39260e4a226d
Step 5/12 : RUN a2enmod dav_fs
 ---> Running in 63731bb66c96
Considering dependency dav for dav_fs:
Module dav already enabled
Enabling module dav_fs.
To activate the new configuration, you need to run:
  service apache2 restart
Removing intermediate container 63731bb66c96
 ---> a9f07e1effab
Step 6/12 : RUN apt-get -qq update && apt-get -qqy install          locales         libxslt-dev         libssh2-1-dev     && apt-get autoremove -y     && apt-get clean all
 ---> Running in 9e987ba9b7d6
...
Removing intermediate container 4372b7d58c20
 ---> fe0d6e637c4b
Step 10/12 : COPY server.crt /etc/apache2/ssl/server.crt
 ---> e1857467101d
Step 11/12 : COPY server.key /etc/apache2/ssl/server.key
 ---> aaf28037dbe7
Step 12/12 : ENV LANG ru_RU.utf8
 ---> Running in 38e4864129cf
Removing intermediate container 38e4864129cf
 ---> b346f41bbe61

Successfully built b346f41bbe61
Successfully tagged fdocpg_php:latest
```
Смотрим образы
```
# docker image list
REPOSITORY             TAG                 IMAGE ID            CREATED             SIZE
fdocpg_php             latest              b346f41bbe61        22 seconds ago      812MB
fdocpg_postgres        latest              0b1f88f4c278        2 minutes ago       290MB
yiisoftware/yii2-php   7.3-apache          e484ad63d6ad        9 months ago        753MB
postgres               10.1                ec61d13c8566        23 months ago       287MB
```

### Запуск контейнеров

Запускаем контейнеры (первый запуск)
```
# docker-compose up -d
Creating network "fdocpg_default" with the default driver
Creating volume "fdocpg_db" with default driver
Creating fdocpg-postgres ... done
Creating fdocpg-php      ... done

# docker ps -a
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                                      NAMES
9ac7c0abfb48        fdocpg_php          "docker-php-entrypoi…"   8 seconds ago       Up 5 seconds        0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp   fdocpg-php
292aa23c5db9        fdocpg_postgres     "docker-entrypoint.s…"   10 seconds ago      Up 8 seconds        0.0.0.0:5432->5432/tcp                     fdocpg-postgres

# docker logs fdocpg-php
usermod: no changes

warning: xdebug (xdebug.so) is already loaded!

Enabled xdebug
AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.19.0.3. Set the 'ServerName' directive globally to suppress this message
AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.19.0.3. Set the 'ServerName' directive globally to suppress this message
[Sat Nov 16 18:07:37.882610 2019] [mpm_prefork:notice] [pid 1] AH00163: Apache/2.4.25 (Debian) OpenSSL/1.0.2q configured -- resuming normal operations
[Sat Nov 16 18:07:37.882627 2019] [core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'

# docker logs fdocpg-postgres
Файлы, относящиеся к этой СУБД, будут принадлежать пользователю "postgres".
От его имени также будет запускаться процесс сервера.

Кластер баз данных будет инициализирован с локалью "ru_RU.UTF-8".
Кодировка БД по умолчанию, выбранная в соответствии с настройками: "UTF8".
Выбрана конфигурация текстового поиска по умолчанию "russian".

Контроль целостности страниц данных отключён.

исправление прав для существующего каталога /var/lib/postgresql/data... ок
создание подкаталогов... ок
выбирается значение max_connections... 100
выбирается значение shared_buffers... 128MB
выбор реализации динамической разделяемой памяти ... posix
создание конфигурационных файлов... ок
выполняется подготовительный скрипт ... ок
выполняется заключительная инициализация ... ок
сохранение данных на диске... ок

Готово. Теперь вы можете запустить сервер баз данных:

    pg_ctl -D /var/lib/postgresql/data -l файл_журнала start


ПРЕДУПРЕЖДЕНИЕ: используется проверка подлинности "trust" для локальных подключений.
Другой метод можно выбрать, отредактировав pg_hba.conf или используя ключи -A,
--auth-local или --auth-host при следующем выполнении initdb.
ожидание запуска сервера....2019-11-16 21:07:37.232 MSK [37] СООБЩЕНИЕ:  для приёма подключений по адресу IPv4 "127.0.0.1" открыт порт 5432
2019-11-16 21:07:37.232 MSK [37] СООБЩЕНИЕ:  не удалось привязаться к адресу IPv6 "::1": Невозможно назначить запрошенный адрес
2019-11-16 21:07:37.232 MSK [37] ПОДСКАЗКА:  Возможно порт 5432 занят другим процессом postmaster? Если нет, повторите попытку через несколько секунд.
2019-11-16 21:07:37.364 MSK [37] СООБЩЕНИЕ:  для приёма подключений открыт сокет Unix "/var/run/postgresql/.s.PGSQL.5432"
2019-11-16 21:07:37.432 MSK [38] СООБЩЕНИЕ:  система БД была выключена: 2019-11-16 21:07:35 MSK
2019-11-16 21:07:37.453 MSK [37] СООБЩЕНИЕ:  система БД готова принимать подключения
 готово
сервер запущен
CREATE DATABASE

CREATE ROLE


/usr/local/bin/docker-entrypoint.sh: running /docker-entrypoint-initdb.d/20-setup.sh
Creating database: fdoc_test
CREATE DATABASE
Creating extensions in DBs
CREATE EXTENSION
CREATE EXTENSION

2019-11-16 21:07:39.174 MSK [37] СООБЩЕНИЕ:  получен запрос на быстрое выключение
ожидание завершения работы сервера....2019-11-16 21:07:39.196 MSK [37] СООБЩЕНИЕ:  прерывание всех активных транзакций
2019-11-16 21:07:39.198 MSK [37] СООБЩЕНИЕ:  рабочий процесс: logical replication launcher (PID 44) завершился с кодом выхода 1
2019-11-16 21:07:39.198 MSK [39] СООБЩЕНИЕ:  выключение
2019-11-16 21:07:39.672 MSK [37] СООБЩЕНИЕ:  система БД выключена
 готово
сервер остановлен

PostgreSQL init process complete; ready for start up.

2019-11-16 21:07:39.731 MSK [1] СООБЩЕНИЕ:  для приёма подключений по адресу IPv4 "0.0.0.0" открыт порт 5432
2019-11-16 21:07:39.731 MSK [1] СООБЩЕНИЕ:  для приёма подключений по адресу IPv6 "::" открыт порт 5432
2019-11-16 21:07:39.752 MSK [1] СООБЩЕНИЕ:  для приёма подключений открыт сокет Unix "/var/run/postgresql/.s.PGSQL.5432"
2019-11-16 21:07:39.782 MSK [1] СООБЩЕНИЕ:  передача вывода в протокол процессу сбора протоколов
2019-11-16 21:07:39.782 MSK [1] ПОДСКАЗКА:  В дальнейшем протоколы будут выводиться в каталог "/logs".

# docker volume list
DRIVER              VOLUME NAME
local               fdocpg_db
```

### Установка приложения

Создаем конфиг приложения (192.168.0.1 поменять на свой ip)
```
# cp ..\.env.docker ..\.env
```

Вход в php контейнер. Установка пакетов через composer (из php-контейнера). Накат миграций
```
# docker-compose exec php bash
        _ _  __                                             _
       (_|_)/ _|                                           | |
  _   _ _ _| |_ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 | | | | | |  _| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 | |_| | | | | | | | (_| | | | | | |  __/\ V  V / (_) | |  |   <
  \__, |_|_|_| |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
   __/ |
  |___/

PHP version: 7.3.2

## composer install
Loading composer repositories with package information
Installing dependencies (including require-dev) from lock file
    1/109:      https://codeload.github.com/phpspec/php-diff/legacy.zip/0464787bfa7cd13576c5a1e318709768798bec6a
    2/109:      https://codeload.github.com/Codeception/Verify/legacy.zip/5d649dda453cd814dadc4bb053060cd2c6bb4b4c
    3/109:      https://codeload.github.com/Codeception/Stub/legacy.zip/f50bc271f392a2836ff80690ce0c058efe1ae03e
    4/109:      https://codeload.github.com/Codeception/Specify/legacy.zip/21b586f503ca444aa519dd9cafb32f113a05f286
    5/109:      https://codeload.github.com/Codeception/phpunit-wrapper/legacy.zip/a5633c736e0e0022bc5065b27c63f2d1aa97b69f
    6/109:      https://codeload.github.com/phar-io/version/legacy.zip/45a2ec53a73c70ce41d55cedef9063630abaf1b6
    7/109:      https://codeload.github.com/yiisoft/yii2-gii/legacy.zip/9ec1374d0844f448d2af29c707f77c9f8d1375c8
    8/109:      https://codeload.github.com/phar-io/manifest/legacy.zip/7761fcacf03b4d4f16e7ccb606d4879ca431fcf4
    9/109:      https://codeload.github.com/myclabs/DeepCopy/legacy.zip/3e01bdad3e18354c3dce54466b7fbe33a9f9f7f8

...

codeception/base suggests installing aws/aws-sdk-php (For using AWS Auth in REST module and Queue module)
codeception/base suggests installing codeception/phpbuiltinserver (Start and stop PHP built-in web server for your tests)
codeception/base suggests installing flow/jsonpath (For using JSONPath in REST module)
codeception/base suggests installing league/factory-muffin (For DataFactory module)
codeception/base suggests installing league/factory-muffin-faker (For Faker support in DataFactory module)
codeception/base suggests installing phpseclib/phpseclib (for SFTP option in FTP Module)
codeception/base suggests installing stecman/symfony-console-completion (For BASH autocompletion)
codeception/base suggests installing symfony/phpunit-bridge (For phpunit-bridge support)
Generating autoload files
> yii\composer\Installer::postInstall

## php yii migrate
Yii Migration Tool (based on Yii v2.0.29)

Creating migration history table "migration"...Done.
Total 7 new migrations to be applied:
        m160313_153426_session_init
        m180220_132921_system_tables
        m180220_140614_system_dictionary
        m180302_094151_acl_tables
        m180303_143019_acl_dictionary
        m180304_115136_create_minprom_dictionary
        m180305_072353_create_minprom_data_objects

Apply the above migrations? (yes|no) [no]:y
Apply the above migrations? (yes|no) [no]:y
*** applying m160313_153426_session_init
    > create table {{%session}} ... done (time: 0.037s)
*** applied m160313_153426_session_init (time: 0.122s)

*** applying m180220_132921_system_tables
    > create table requests ... done (time: 0.062s)
    > add comment on table requests ... done (time: 0.001s)
    > create table options ... done (time: 0.015s)
    > add primary key options_pkey on options (type,name) ... done (time: 0.014s)
    > add comment on table options ... done (time: 0.001s)
...
end;
$body$
  language plpgsql ... done (time: 0.001s)
    > execute SQL: create trigger trg_tfprotocol_data_hist
  after insert or update or delete on tfprotocol_data for each row
  execute procedure hist_tfprotocol_data(); ... done (time: 0.001s)
    > add column protocol_date date to table tfprotocol_sort ... done (time: 0.001s)
    > add column protocol_number string(100) to table tfprotocol_sort ... done (time: 0.043s)
    > add column protocol_type string(200) to table tfprotocol_sort ... done (time: 0.001s)
*** applied m180305_072353_create_minprom_data_objects (time: 11.321s)


7 migrations were applied.

Migrated up successfully.
## exit
#
```

Система доступна по адресу `https://localhost` (или `http://localhost`) Вход: admin/admin  

К БД можно подключиться `localhost:5432` БД fdoc, fdoc/fdoc
или зайти в консоль
```
# docker-compose exec postgres psql -U fdoc
psql (10.1)
Введите "help", чтобы получить справку.

fdoc=# select * from migration;
                  version                   | apply_time
--------------------------------------------+------------
 m000000_000000_base                        | 1573928471
 m160313_153426_session_init                | 1573928481
 m180220_132921_system_tables               | 1573928483
 m180220_140614_system_dictionary           | 1573928484
 m180302_094151_acl_tables                  | 1573928484
 m180303_143019_acl_dictionary              | 1573928484
 m180304_115136_create_minprom_dictionary   | 1573928510
 m180305_072353_create_minprom_data_objects | 1573928522
(8 строк)

fdoc=#
```

Останов приложения
```
# docker-compose down
Stopping fdocpg-php      ... done
Stopping fdocpg-postgres ... done
Removing fdocpg-php      ... done
Removing fdocpg-postgres ... done
Removing network fdocpg_default

# docker ps -a
CONTAINER ID        IMAGE               COMMAND             CREATED             STATUS              PORTS               NAMES
```

файлы БД лежат в volume и будут использованы при повторных запусках:
```
# docker-compose up -d
Creating network "fdocpg_default" with the default driver
Creating fdocpg-postgres ... done
Creating fdocpg-php      ... done

# docker ps -a
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                                      NAMES
03e4fea7fbd3        fdocpg_php          "docker-php-entrypoi…"   5 seconds ago       Up 3 seconds        0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp   fdocpg-php
1d166ae84280        fdocpg_postgres     "docker-entrypoint.s…"   6 seconds ago       Up 5 seconds        0.0.0.0:5432->5432/tcp                     fdocpg-postgres

```

### Создание резервной копии БД

```
# docker-compose exec postgres bash docker/postgres-dump.sh
# ls  dump.gz
```

### Восстановление резервной копии БД

1. Копируем в каталог docker проекта с именем dump.gz
2. docker-compose down - выключаем
3. docker volume rm fdocpg_db - удаляем БД (удаляем volume)
4. docker-compose up - включаем (создается БД)
5. docker-compose exec postgres bash docker/postgres-restore.sh (т.е. надо не накатывать миграции)
5. донакатываем миграции при необходимости 
```
# docker-compose exec php bash 
## php yii migrate
```

Дампы с тестоввыми данными можно взять здесь -
http://dev.ditgt.ru:8048/dump_db/ (vpn: http://10.99.102.48/dump_db/), вход - foo/bar
 * fdoc.sql.gz - размер ~3Гб	 
 * fdoc_lite.sql.gz - размер ~500Мб (облегченный - замена всех приложенных файлов pdf на пустышку)