CREATE TABLE `channel_data`
(
    `guild_id`            bigint NOT NULL,
    `welcome_channel_id`  bigint DEFAULT NULL,
    `leave_channel_id`    bigint DEFAULT NULL,
    `birthday_channel_id` bigint DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `channel_data`
    ADD KEY `guild_id` (`guild_id`);

ALTER TABLE `channel_data`
    ADD CONSTRAINT `GuildId` FOREIGN KEY (`guild_id`) REFERENCES `guild_data` (`guild_id`) ON DELETE CASCADE ON UPDATE CASCADE;
