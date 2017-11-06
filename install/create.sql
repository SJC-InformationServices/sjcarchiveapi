CREATE TABLE `entdef` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL,
  `createdon` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedon` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entitytypename` (`name`),
  UNIQUE KEY `entitytypelable` (`label`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `attdef` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Replace specialchar/space etc from name\n',
  `type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `length` int(11) DEFAULT '200',
  `iskey` tinyint(4) DEFAULT '0',
  `searchable` tinyint(4) DEFAULT '0',
  `options` json NOT NULL,
  `defaultvalue` text COLLATE utf8mb4_unicode_ci,
  `createdon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `entdef_entdef` (
  `parent_entdef` int(100) NOT NULL,
  `child_entdef` int(100) NOT NULL,
  PRIMARY KEY (`parent_entdef`,`child_entdef`),
  CONSTRAINT `ent_child` FOREIGN KEY (`child_entdef`) REFERENCES `entdef` (`id`),
  CONSTRAINT `ent_parent` FOREIGN KEY (`parent_entdef`) REFERENCES `entdef` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ent` (
  `id` binary(16) NOT NULL,
  `id_text` varchar(36) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (insert(insert(insert(insert(hex(`id_bin`),9,0,'-'),14,0,'-'),19,0,'-'),24,0,'-')) VIRTUAL,
  `entdef_id` int(100) NOT NULL,
  `createdon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedon` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entkey` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `ent_entdef` FOREIGN KEY (`entdef_id`) REFERENCES `entdef` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29824 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


alter table `ent_att` add  constraint `entdef_attdef` Foreign Key (`entdef_id`) references `entdef` (`id`);
alter table `ent_att` add  constraint `attdef_entdef` Foreign Key (`attdef_id`) references `attdef` (`id`);




