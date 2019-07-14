CREATE TABLE `user` (
`user_id`  int(10) NOT NULL AUTO_INCREMENT ,
`username`  varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' ,
`pass`  char(32) NOT NULL DEFAULT '' ,
`email`  varchar(255) NOT NULL DEFAULT '' ,
PRIMARY KEY (`user_id`)
);
INSERT INTO user (username,pass,email) values('james',md5(11111),'james@qq.com'),('paul', md5(22), 'paul@weixin.com');


CREATE TABLE IF NOT EXISTS vote(
  id INT(10) AUTO_INCREMENT PRIMARY KEY,
  uid INT(10) NOT NULL DEFAULT 0 COMMENT '投票用户',
  touid INT(10) NOT NULL DEFAULT 0 COMMENT '得票用户',
  add_time INT(10) NOT NULL DEFAULT 0 COMMENT '投票时间'
) Engine=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


