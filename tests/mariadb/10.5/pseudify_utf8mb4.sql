-- https://mariadb.com/kb/en/create-user/
CREATE USER IF NOT EXISTS 'pseudify'@'%' IDENTIFIED BY 'pseudify(!)w4ldh4ck3r';

-- https://mariadb.com/kb/en/create-database/
-- https://mariadb.com/kb/en/supported-character-sets-and-collations/
CREATE DATABASE IF NOT EXISTS pseudify_utf8mb4 CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_general_ci';

-- https://mariadb.com/kb/en/grant/
GRANT SELECT, UPDATE ON pseudify_utf8mb4.* TO 'pseudify'@'%';

FLUSH PRIVILEGES;

-- https://mariadb.com/kb/en/data-types/

DROP TABLE IF EXISTS `wh_user`;
DROP TABLE IF EXISTS `wh_user_session`;
DROP TABLE IF EXISTS `wh_meta_data`;
DROP TABLE IF EXISTS `wh_log`;

CREATE TABLE `wh_user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

  `username` VARCHAR(255),
  `password` VARCHAR(255),
  `first_name` VARCHAR(255),
  `last_name` VARCHAR(255),
  `email` VARCHAR(255),
  `city` VARCHAR(255),

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wh_user_session` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

  `session_data` BLOB,
  `session_data_json` TEXT,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wh_meta_data` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

  `meta_data` BLOB,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wh_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

  `log_type` VARCHAR(255),
  `log_data` BLOB,
  `log_message` TEXT,
  `ip` VARCHAR(255),

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wh_user` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `city`) VALUES(1, 'karl13', '$argon2i$v=19$m=8,t=1,p=1$amo3Z28zNTlwZG84TG1YZg$1Ka95oewxn3xs/jLrTN0R9lhIxtNnQynBFRdE/70cAQ', 'Jordyn', 'Shields', 'madaline30@example.net', 'Lake Tanner');
INSERT INTO `wh_user` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `city`) VALUES(2, 'reilly.chase', '$argon2i$v=19$m=8,t=1,p=1$amo3Z28zNTlwZG84TG1YZg$1Ka95oewxn3xs/jLrTN0R9lhIxtNnQynBFRdE/70cAQ', 'Keenan', 'King', 'johns.percy@example.com', 'Edwardotown');
INSERT INTO `wh_user` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `city`) VALUES(3, 'hpagac', '$argon2i$v=19$m=8,t=1,p=1$QXNXbTRMZWxmenBRUzdwZQ$i6hntUDLa3ZFqmCG4FM0iPrpMp6d4D8XfrNBtyDmV9U', 'Donato', 'Keeling', 'mcclure.ofelia@example.com', 'North Elenamouth');
INSERT INTO `wh_user` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `city`) VALUES(4, 'georgiana59', '$argon2i$v=19$m=8,t=1,p=1$SUJJeWZGSGEwS2h2TEw5Ug$kCQm4/5DqnjXc/3SiXwimtTBvbDO9H0Ru1f5hkQvE/Q', 'Maybell', 'Anderson', 'cassin.bernadette@example.net', 'South Wilfordland');
INSERT INTO `wh_user` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `city`) VALUES(5, 'howell.damien', '$argon2i$v=19$m=8,t=1,p=1$ZldmOWd2TDJRb3FTNVpGNA$ORIwp6yekRx02mqM4WCTVhllgXpUpuFJZ1MmbYwAMXs', 'Mckayla', 'Stoltenberg', 'conn.abigale@example.net', 'Dorothyfort');

INSERT INTO `wh_user_session` (`id`, `session_data`, `session_data_json`) VALUES(1, 'a:1:{s:7:\"last_ip\";s:38:\"4fb:1447:defb:9d47:a2e0:a36a:10d3:fd98\";}', '{\"data\":{\"last_ip\":\"4fb:1447:defb:9d47:a2e0:a36a:10d3:fd98\"}}');
INSERT INTO `wh_user_session` (`id`, `session_data`, `session_data_json`) VALUES(2, 'a:1:{s:7:\"last_ip\";s:13:\"107.66.23.195\";}', '{\"data\":{\"last_ip\":\"107.66.23.195\"}}');
INSERT INTO `wh_user_session` (`id`, `session_data`, `session_data_json`) VALUES(3, 'a:1:{s:7:\"last_ip\";s:13:\"244.166.32.78\";}', '{\"data\":{\"last_ip\":\"244.166.32.78\"}}');
INSERT INTO `wh_user_session` (`id`, `session_data`, `session_data_json`) VALUES(4, 'a:1:{s:7:\"last_ip\";s:37:\"1321:57fc:460b:d4d0:d83f:c200:4b:f1c8\";}', '{\"data\":{\"last_ip\":\"1321:57fc:460b:d4d0:d83f:c200:4b:f1c8\"}}');
INSERT INTO `wh_user_session` (`id`, `session_data`, `session_data_json`) VALUES(5, 'a:1:{s:7:\"last_ip\";s:14:\"197.110.248.18\";}', '{\"data\":{\"last_ip\":\"197.110.248.18\"}}');

INSERT INTO `wh_meta_data` (`id`, `meta_data`) VALUES(1, '1f8b080000000000000365525d4f023110fc2fcd3d9a4a7bc7570951236234011340455fc8c215aea1d7d66b112e86ff6e7b7211e3db4e77ba33d32db0987d599630b4e525413d60dd80294322453dc19a3dcb3a0ced2c2f14e41c7948628632bde752e21472c115fae118b076af8b34c0ae1f1041b1d18a8ae8b34fba51deef5cb83eb9307d12bdcb347f7a4de96cf03859c6c3d9f8c5dc8f6fa2a7c9c3deb44abe9d1c1a34ff1825afb7b3974ccacddc3c9bddf0f19d8cf2e5dbfe6634b7958f33cd4506365bb8d2f093999376fa8f682408e5f8c1d551d4b0d96bb4ae949d5eaabbeab0c1d05a14d62deac46d8646ab2d9412aa6c0c4938eb12c2d0d469e9b85af262138e9a0cf11c840c35f56fbbd24a61588a0d487ecd0f901bc9b1e29587d016aeac270d74a15d56ae75e1bbc77a33346c86fed94c1c2e7864b9b542ab450aaeb297f821c048e0b64f4e85a9c3d224c1a4d5c231c5ed8e9f7f26110709f2fb1912741220711777086e605f78fef11bb387fddf33020000');
INSERT INTO `wh_meta_data` (`id`, `meta_data`) VALUES(2, '1f8b080000000000000365525b4fc23014fe2fcd1ecda4ddc6a5842851319a800920282fe4cc55d6d0b5752dc262f8efb693458c0f4d7a6edfa5a74023fa65684cd1965518f581f67c4c28e219ea739af40ded52b433ac945030e4421c5194ab3d1322cca0e04ca29f1e0dc6ec5599f9b0e7000228374a121e7c0e702f2806dd0b3bc0177a808395c88aa76546e6b78fd3341acd270b7d3f19064fd387bd6e576c3b3db448f1318e9737f3452ec4e6453febdde87185c745faba1f8e5f4cade38c739d83c9d7b6d2ec24e6c49dfd6bd402b8b4ec601b2b7294f45bed2b696697f2ae4eb6287ae7a5b1ebc67187a2f1db162a01b5378a049c5531a6686695b04ca6acdcf85442112b800b7f27ee6ddf949421a47c03825db303145ab050b25a832f735b3548b7aa5436afde55e9aac76633c46f86fcd94ce4075c6498315cc97506b696173b10a0d8f7764e4ab96ecc92380e71bb1d4624ec741dfe1945e429f0ef6788eb19e704773b218e9290443d7fdcc8f11b4093841d36020000');
INSERT INTO `wh_meta_data` (`id`, `meta_data`) VALUES(3, '1f8b08000000000000036592dd6ea33010855f65657159116ca0818922f52fca6ea5d52a4bab46bd89066c821b302c769246ddbcfbda6ca246eae5f11cfb3be31984103e34444036e240c90421759a01919c4c2444130d0990ad16bdc246102b2905b2166dbf96a8304ec97f47875aefdb9e3b99daeb1ef6eb5631e9eda634f59a697265a6f4aa9b522f7b7e7c142faff36c3edb67ac624fb37dfcbcf636f78b261ac50f7fd4dbb21885995cee65639eee76f9c3aff47bf07b4bcbb8da2c76b3d1624871c15c55a8ab953974e214e6c4e65f8c5d8d5219f16e864a601b9bbffe28fee232e2e79352f6daaccecd8e81fcc4432eea7a680c488d1755fbf4ade2a2d7ad723206221a948395596f61b152f9b9fb3b2e8c1137e21d9bae16be124302fbed853487016d4959bb35d5b7175997366b8dca663a9e67c3dc6cd897d950abb4b09856ad381a744f5ddb1c08d479c7a7c0b27385d04a1a320af1b82c20ba0e72e0110f80276109050b02887228699158ee053a7468fab926d1790b58e0533f4e7c1ad92d381eff01fdaee8ae4c020000');
INSERT INTO `wh_meta_data` (`id`, `meta_data`) VALUES(4, '1f8b08000000000000036552616bc23010fd2fa11fa5336dd51a299bcc3926e8409d3abfc8d5461b4c93ac89d332fcef4b3bcb043f047277efde7b970b109ffc68121074a005463d20dd32f6086209ea31d2ea69121274d4341790516443ec1394ca13e5dc4d206354a03f8c02ad4f324fcab06b091cc8f75278ccf98e70d7c9a2b06122dc501176d63cc9de9789371f8ca6b13f9c4f16ea75d277dea76f27d52ee8617a6e7ad9d738583ecf1729e7fb95fa50c7e1688dc759fc79ea8f57baf271a3b94941a71b53287a3573d54eee808a0313869e4d3d8a18b67acdf6a3d0b307f152259b04ed58aecda69eb843d0787b808243351b411c6eaa18133433921b2a629aefcb548b209a01e3e5ddb36fbb9542b810b33d70fa44cf90294e5d412b0f659999a2661ac85c9ab4d8c9dc562ff566bc7233dedd66b08d34d59a49b149c054f6029b03824b6ce7ea94a98add52e16ec7c5b8e97a41e8e2d00adc68f8a506feff0d41dd14fa166f0f6edbde96edb8fc020ca4e19b36020000');
INSERT INTO `wh_meta_data` (`id`, `meta_data`) VALUES(5, '1f8b080000000000000365915f4fc23014c5bf8aa97b240bedc6c64a480c22c428448828c10772dd0a6b5cd7ba967f1abebbedc448c25b4feeb9f7776e2fd0807e6b1a52f4c10e187580264e138a78863a9c061d4ddb146d34ab4a100c591951942b58438a7e6b0ab4dec92a7332b18d1e546b5912ee6dbb38f144b7dd305ddc505dec4de6e3f9fbf374b478dd0b56f6a6b3af6cb798783cca4b33eb3f42b0187c8adb61381835f953a5462acac27e7bbeaac63d73e88b9764e618f88cb9cc41e74b735075b298a213fbc2a70ae0a5617b730afd966d87d9f5fabe3636295af14a9be5d98a7d598291f54e14157056b49407c60a5eae9d6a51c404f0c2bd89ed13695a6c2ae6cb95b5c00ddb835005f353299cc3fe73cacda1a65af3585626bfba2b989d2d3726479de3df2d88bb05b9b805b64a33adb92c971918a88762bb37c5ce1b9fb2725523028a4818fa388afc80f871dbce3f43040e81ffcf1fa2138004894f62bf15fbd8c6381e7f00628e8a6d25020000');

INSERT INTO `wh_log` (`id`, `log_type`, `log_data`, `log_message`, `ip`) VALUES(1, 'foo', '65794a3163325679546d46745a534936496e4a76626d46735a4738784e534973496d567459576c73496a6f6962574e6a624856795a5335765a6d5673615746415a586868625842735a53356a623230694c434a7359584e30546d46745a534936496b746c5a577870626d63694c434a7063434936496a457a4d6a45364e54646d597a6f304e6a42694f6d51305a4441365a44677a5a6a706a4d6a41774f6a52694f6d5978597a676966513d3d', '{\"message\":\"foo text \\\"ronaldo15\\\", another \\\"mcclure.ofelia@example.com\\\"\"}', '1321:57fc:460b:d4d0:d83f:c200:4b:f1c8');
INSERT INTO `wh_log` (`id`, `log_type`, `log_data`, `log_message`, `ip`) VALUES(2, 'foo', '65794a3163325679546d46745a534936496e4e3059584a724c6d70315a4751694c434a6c6257467062434936496e4e796233646c514756345957317762475575626d5630496977696247467a64453568625755694f694a4362336c6c63694973496d6c77496a6f694d5455314c6a49784e5334324e7934784f54456966513d3d', '{\"message\":\"foo text \\\"stark.judd\\\", another \\\"srowe@example.net\\\"\"}', '155.215.67.191');
INSERT INTO `wh_log` (`id`, `log_type`, `log_data`, `log_message`, `ip`) VALUES(3, 'bar', '613a323a7b693a303b733a33383a223466623a313434373a646566623a396434373a613265303a613336613a313064333a66643938223b733a343a2275736572223b4f3a383a22737464436c617373223a353a7b733a383a22757365724e616d65223b733a31323a226672656964612e6d616e7465223b733a383a226c6173744e616d65223b733a353a2254726f6d70223b733a353a22656d61696c223b733a32333a226c61666179657474653634406578616d706c652e6e6574223b733a323a226964223b693a31303b733a343a2275736572223b523a333b7d7d', '{\"message\":\"bar text \\\"Tromp\\\", another \\\"freida.mante\\\"\"}', '4fb:1447:defb:9d47:a2e0:a36a:10d3:fd98');
INSERT INTO `wh_log` (`id`, `log_type`, `log_data`, `log_message`, `ip`) VALUES(4, 'bar', '613a323a7b693a303b733a31343a223234332e3230322e3234312e3637223b733a343a2275736572223b4f3a383a22737464436c617373223a353a7b733a383a22757365724e616d65223b733a31313a2267656f726769616e613539223b733a383a226c6173744e616d65223b733a353a22426c6f636b223b733a353a22656d61696c223b733a31393a226e6f6c616e3131406578616d706c652e6e6574223b733a323a226964223b693a323b733a343a2275736572223b523a333b7d7d', '{\"message\":\"bar text \\\"Block\\\", another \\\"georgiana59\\\"\"}', '243.202.241.67');
INSERT INTO `wh_log` (`id`, `log_type`, `log_data`, `log_message`, `ip`) VALUES(5, 'bar', '613a323a7b693a303b733a31353a223133322e3138382e3234312e313535223b733a343a2275736572223b4f3a383a22737464436c617373223a353a7b733a383a22757365724e616d65223b733a373a22637972696c3036223b733a383a226c6173744e616d65223b733a383a22486f6d656e69636b223b733a353a22656d61696c223b733a32313a22636c696e746f6e3434406578616d706c652e6e6574223b733a323a226964223b693a39313b733a343a2275736572223b523a333b7d7d', '{\"message\":\"bar text \\\"Homenick\\\", another \\\"cyril06\\\"\"}', '132.188.241.155');
INSERT INTO `wh_log` (`id`, `log_type`, `log_data`, `log_message`, `ip`) VALUES(6, '', '', '', '');
