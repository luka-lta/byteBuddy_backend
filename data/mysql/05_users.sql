CREATE TABLE users
(
    user_id         INT AUTO_INCREMENT PRIMARY KEY,
    username        VARCHAR(255) UNIQUE NOT NULL,
    email           VARCHAR(255) UNIQUE NOT NULL,
    hashed_password VARCHAR(255)        NOT NULL,
    role            ENUM('ADMIN', 'MODERATOR', 'USER') DEFAULT 'USER',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `users`
    MODIFY `user_id` int NOT NULL AUTO_INCREMENT;