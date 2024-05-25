CREATE TABLE `command_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `guild_id` int NOT NULL,
  `command` int NOT NULL,
  `executed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `command_history`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `command_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
