CREATE DATABASE TimeManager;

USE TimeManager;

CREATE TABLE IF NOT EXISTS users (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(128) NOT NULL,
phone INT,
email VARCHAR(128) NOT NULL
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS task_status (
id INT NOT NULL PRIMARY KEY,
status VARCHAR(30) NOT NULL
) ENGINE=INNODB;

INSERT INTO task_status (id, status) VALUES (0, 'new');
INSERT INTO task_status (id, status) VALUES (1, 'completed');
INSERT INTO task_status (id, status) VALUES (2, 'due');
INSERT INTO task_status (id, status) VALUES (3, 'deleted');

CREATE TABLE IF NOT EXISTS tasks (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(512) NOT NULL,
description VARCHAR(1264) NULL,
status_id INT NOT NULL DEFAULT 0,
completed_on TIMESTAMP
) ENGINE=INNODB;

ALTER TABLE tasks  
ADD CONSTRAINT FK_status_id
FOREIGN KEY (status_id) REFERENCES task_status(id)
ON UPDATE CASCADE  
ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS user_tasks (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
task_id INT NOT NULL
) ENGINE=INNODB;

ALTER TABLE user_tasks  
ADD CONSTRAINT FK_task_id
FOREIGN KEY (task_id) REFERENCES tasks(id)
ON UPDATE CASCADE  
ON DELETE CASCADE;

ALTER TABLE user_tasks  
ADD CONSTRAINT FK_user_id
FOREIGN KEY (user_id) REFERENCES users(id)
ON UPDATE CASCADE  
ON DELETE CASCADE;


