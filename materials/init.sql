CREATE DATABASE IF NOT EXISTS mydb;
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'password';
GRANT SELECT,UPDATE,INSERT,DELETE ON mydb.* TO 'user'@'%';
FLUSH PRIVILEGES;
USE mydb;
CREATE TABLE profile(
    id_profile INT AUTO_INCREMENT,
    email VARCHAR(50) NOT NULL,
    password_hash VARCHAR(50) NOT NULL,
    reg_date DATETIME NULL,
    avatar BLOB NULL,
    is_author BOOL DEFAULT 0,
    is_admin BOOL DEFAULT 0,
    is_banned BOOL DEFAULT 0,
    PRIMARY KEY (id_profile)
);

CREATE TABLE ingame_account(
    id_account INT AUTO_INCREMENT,
    id_profile INT NOT NULL,
    keys_count INT NOT NULL,
    hearts INT NOT NULL,
    diamonds INT NOT NULL,
    PRIMARY KEY (id_account),
    FOREIGN KEY (id_profile) REFERENCES profile (id_profile) ON DELETE CASCADE
);
 
CREATE TABLE lesson_collection(
    id_collection INT AUTO_INCREMENT,
    collection_description TEXT NULL,
    collection_name VARCHAR(50) NOT NULL,
    key_cost INT NOT NULL,
    PRIMARY KEY (id_collection)
);

CREATE TABLE lesson(
    id_lesson INT AUTO_INCREMENT,
    id_collection INT NULL,
    lesson_order INT NOT NUll,
    lesson_title VARCHAR(50) NOT NULL,
    lesson_description TEXT NULL,
    id_author INT NOT NULL,
    is_hidden BOOL DEFAULT 0,
    PRIMARY KEY (id_lesson),
    FOREIGN KEY (id_collection) REFERENCES lesson_collection(id_collection) ON DELETE SET NULL,
    FOREIGN KEY (id_author) REFERENCES profile (id_profile) ON DELETE CASCADE
);

CREATE TABLE task(
    id_task INT AUTO_INCREMENT,
    task_text TINYTEXT NULL,
    task_json JSON NOT NULL,
    id_lesson INT NOT NULL,
    PRIMARY KEY (id_task),
    FOREIGN KEY (id_lesson) REFERENCES lesson (id_lesson) ON DELETE CASCADE
);

CREATE TABLE task_result(
    id_task_result INT AUTO_INCREMENT,
    id_task INT NOT NULL,
    id_profile INT NOT NULL,
    score FLOAT NOT NULL,
    PRIMARY KEY (id_task_result),
    FOREIGN KEY (id_task) REFERENCES task (id_task) ON DELETE CASCADE,
    FOREIGN KEY (id_profile) REFERENCES profile (id_profile) ON DELETE CASCADE
);

CREATE TABLE lesson_result(
    id_lesson_result INT AUTO_INCREMENT,
    id_lesson INT NOT NULL,
    id_profile INT NOT NULL,
    score FLOAT NOT NULL,
    PRIMARY KEY (id_lesson_result),
    FOREIGN KEY (id_lesson) REFERENCES lesson (id_lesson) ON DELETE CASCADE,
    FOREIGN KEY (id_profile) REFERENCES profile (id_profile) ON DELETE CASCADE
);

CREATE TABLE collection_status(
    id_status INT AUTO_INCREMENT,
    id_profile INT NOT NULL,
    id_collection INT NOT NULL,
    is_open INT NOT NULL,
    is_available INT NOT NULL,
    PRIMARY KEY (id_status),
    FOREIGN KEY (id_profile) REFERENCES profile (id_profile) ON DELETE CASCADE,
    FOREIGN KEY (id_collection) REFERENCES lesson_collection (id_collection) ON DELETE CASCADE
);

ALTER TABLE profile ADD CONSTRAINT unique_email UNIQUE (email);

DELIMITER //
CREATE FUNCTION is_better_than_avg (lesson_result_id INT) RETURNS BOOL READS SQL DATA
BEGIN
    DECLARE lesson_id INT;
    SELECT id_lesson FROM lesson_result WHERE id_lesson_result = lesson_result_id INTO lesson_id;
    IF ((SELECT AVG(score) FROM lesson_result WHERE id_lesson = lesson_id) < 
        (SELECT score FROM lesson_result WHERE id_lesson_result = lesson_result_id)) THEN
        RETURN 1;
    ELSE RETURN 0;
    END IF;
END//
DELIMITER ;
DELIMITER //
CREATE PROCEDURE collect_lessons (IN profile_id INT, IN name_of_collection VARCHAR(50))
BEGIN
    DECLARE collection_id INT;
    IF NOT EXISTS(SELECT * FROM lesson_collection WHERE collection_name =  name_of_collection) THEN
    INSERT INTO lesson_collection (collection_name, key_cost) VALUES (name_of_collection, 0);
    END IF;
    SELECT id_collection FROM lesson_collection WHERE collection_name = name_of_collection LIMIT 1 INTO collection_id;    
    UPDATE lesson SET id_collection = collection_id WHERE id_author = profile_id AND id_collection IS NULL;
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE create_default_lesson (IN profile_id INT)
BEGIN
    DECLARE author_email VARCHAR(50);
    SELECT email FROM profile WHERE id_profile = profile_id LIMIT 1 INTO author_email; 
    INSERT INTO lesson (lesson_title, lesson_order, id_author) VALUES (CONCAT(author_email, '_default_lesson'), 1, profile_id);
END//
DELIMITER ;

CREATE TRIGGER author_maker AFTER INSERT ON lesson
FOR EACH ROW
    UPDATE profile SET is_author = 1 WHERE id_profile = NEW.id_author;

CREATE TRIGGER reg_date_setter BEFORE INSERT ON profile
FOR EACH ROW
    SET NEW.reg_date = now();



INSERT INTO profile (email, password_hash, is_admin, is_author) VALUES ('denispetrenko', '1234', 1, 1);