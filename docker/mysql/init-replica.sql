-- Создаем пользователя для репликации на master
CREATE USER IF NOT EXISTS 'replicator'@'%' IDENTIFIED BY 'replicator_password';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';
FLUSH PRIVILEGES;

-- Настраиваем репликацию на slave
CHANGE MASTER TO
    MASTER_HOST='mysql',
    MASTER_PORT=3306,
    MASTER_USER='replicator',
    MASTER_PASSWORD='replicator_password',
    MASTER_AUTO_POSITION=1;

-- Запускаем репликацию
START SLAVE;

-- Проверяем статус репликации
SHOW SLAVE STATUS\G
