CREATE TABLE `birthday_data`
(
    `guild_id` bigint NOT NULL,
    `user_id`  bigint NOT NULL,
    `birthday` date   NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `birthday_data`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `guild_id` (`guild_id`);

ALTER TABLE `birthday_data`
    ADD CONSTRAINT `Guild` FOREIGN KEY (`guild_id`) REFERENCES `config_data` (`guild_id`) ON DELETE CASCADE ON UPDATE CASCADE;
