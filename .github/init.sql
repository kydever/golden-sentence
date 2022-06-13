CREATE TABLE `users` (
`id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
`openid` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '员工企业微信唯一标识',
`name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '员工姓名',
`is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
`created_at` datetime NOT NULL DEFAULT '2022-01-01 00:00:00',
`updated_at` datetime NOT NULL DEFAULT '2022-01-01 00:00:00',
PRIMARY KEY (`id`),
UNIQUE KEY `UNIQUE_OPENID` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='员工表';

CREATE TABLE `sentences` (
`id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
`content` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
`created_at` datetime NOT NULL DEFAULT '2022-01-01 00:00:00',
`updated_at` datetime NOT NULL DEFAULT '2022-01-01 00:00:00',
PRIMARY KEY (`id`),
KEY `INDEX_USER_ID` (`user_id`),
KEY `INDEX_CREATED_AT` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='金句表';
