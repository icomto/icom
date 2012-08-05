CREATE TABLE  ap_users (
    namespace BIGINT NOT NULL,
    item_id BIGINT NOT NULL DEFAULT 0,
    user_id INT UNSIGNED NOT NULL,
    permission TINYINT(1) NOT NULL,
    PRIMARY KEY (namespace, item_id, user_id)
);
CREATE TABLE  ap_groups (
    namespace BIGINT NOT NULL,
    item_id BIGINT NOT NULL DEFAULT 0,
    group_id INT UNSIGNED NOT NULL,
    permission TINYINT(1) NOT NULL,
    PRIMARY KEY (namespace, item_id, group_id)
);

ALTER TABLE user_bookmarks CHANGE thing thing enum('thread','wiki','news','i_set','i_image') NOT NULL;

CREATE TABLE `i_image_comments` (
    `comment_id` bigint(20) NOT NULL AUTO_INCREMENT,
    `image_id` bigint(20) NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_id` int(10) unsigned NOT NULL,
    `message` varchar(4000) NOT NULL,
    PRIMARY KEY (`comment_id`),
    KEY `image_id` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
TABLE `i_image_sources` (
    `image_id` bigint(20) NOT NULL,
    `source_id` bigint(20) NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`image_id`,`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_image_tags` (
    `image_id` bigint(20) NOT NULL,
    `tag_id` bigint(20) NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`image_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_images` (
    `image_id` bigint(20) NOT NULL,
    `ext` varchar(5) NOT NULL,
    `width` int(10) unsigned NOT NULL,
    `height` int(10) unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    `has_thumb` tinyint(1) NOT NULL DEFAULT '0',
    `status` enum('ok','blacklisted') NOT NULL DEFAULT 'ok',
    `hits` bigint(20) NOT NULL DEFAULT '0',
    `size` int(10) unsigned NOT NULL,
    `has_large` tinyint(1) NOT NULL DEFAULT '0',
    `has_default` tinyint(1) NOT NULL DEFAULT '0',
    `has_medium` tinyint(1) NOT NULL DEFAULT '0',
    `has_mini` tinyint(1) NOT NULL DEFAULT '0',
    `has_icon` tinyint(1) NOT NULL DEFAULT '0',
    `signature` bigint(20) NOT NULL DEFAULT '0',
    PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_logs` (
    `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `t` varchar(15) NOT NULL,
    `content_id` bigint(20) NOT NULL,
    `attr_id` bigint(20) NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `action` enum('add','remove') NOT NULL,
    `args` text NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_set_comments` (
    `comment_id` bigint(20) NOT NULL AUTO_INCREMENT,
    `set_id` bigint(20) NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_id` int(10) unsigned NOT NULL,
    `message` varchar(4000) NOT NULL,
    PRIMARY KEY (`comment_id`),
    KEY `set_id` (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_set_images` (
    `set_id` bigint(20) NOT NULL,
    `image_id` bigint(20) NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `content` varchar(2000) NOT NULL,
    PRIMARY KEY (`set_id`,`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_set_tags` (
    `set_id` bigint(20) NOT NULL,
    `tag_id` bigint(20) NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`set_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_sets` (
    `set_id` bigint(20) NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `mtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    `atime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    `name` varchar(100) NOT NULL,
    `content` varchar(2000) NOT NULL,
    PRIMARY KEY (`set_id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_sources` (
    `source_id` bigint(20) NOT NULL,
    `url` varchar(1000) NOT NULL,
    `name` varchar(500) NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `i_tags` (
    `tag_id` bigint(20) NOT NULL,
    `name` varchar(100) NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
