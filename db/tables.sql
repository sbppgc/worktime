DROP TABLE IF EXISTS `corp_rest_events`;

CREATE TABLE `corp_rest_events` (
    `id` int(11) NOT NULL auto_increment,
    `time_start` datetime NOT NULL,
    `time_end` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `i_time_start_time_end` (`time_start`, `time_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rest_days`;

CREATE TABLE `rest_days` (
    `id` int(11) NOT NULL auto_increment,
    `day` date NOT NULL,
    PRIMARY KEY (`id`),
    KEY `i_day` (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_day_shedules`;

CREATE TABLE `user_day_shedules` (
    `id` int(11) NOT NULL auto_increment,
    `id_user` int(11) NOT NULL,
    `start` time NOT NULL,
    `end` time NOT NULL,
    PRIMARY KEY (`id`),
    KEY `i_id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_vacations`;

CREATE TABLE `user_vacations` (
    `id` int(11) NOT NULL auto_increment,
    `id_user` int(11) NOT NULL,
    `first_day` date NOT NULL,
    `last_day` date NOT NULL,
    PRIMARY KEY (`id`),
    KEY `i_id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

