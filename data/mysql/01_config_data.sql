CREATE TABLE `config_data`
(
    `guild_id`    bigint       NOT NULL,
    `server_name` varchar(200) NOT NULL,
    `theme_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'fcba03'
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `config_data`
    ADD PRIMARY KEY (`guild_id`);
