CREATE TABLE `command_data`
(
    `id`          int          NOT NULL,
    `name`        varchar(200) NOT NULL,
    `description` varchar(255) NOT NULL,
    `disabled`    tinyint(1)   NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `command_data`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;
