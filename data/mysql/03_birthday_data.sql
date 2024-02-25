CREATE TABLE `birthday_data` (
                                 `user_id` bigint NOT NULL,
                                 `birthday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `birthday_data`
    ADD PRIMARY KEY (`user_id`);
COMMIT;
