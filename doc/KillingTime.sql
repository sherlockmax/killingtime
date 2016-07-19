DROP DATABASE KillingTime;

CREATE DATABASE IF NOT EXISTS KillingTime DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE KillingTime;

CREATE TABLE friend (
  invite varchar(13) NOT NULL COMMENT '邀請人',
  player varchar(13) NOT NULL COMMENT '受邀人',
  status varchar(1) NOT NULL COMMENT '狀態(F:好友/W:待確認)',
  updatetime datetime NOT NULL COMMENT '日期時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='好友列表';

INSERT INTO friend (invite, player, `status`, updatetime) VALUES('sherlockmax', 'Angelina', 'F', '2016-07-20 01:55:14');
INSERT INTO friend (invite, player, `status`, updatetime) VALUES('sherlockmax', 'WillSmith', 'W', '2016-07-20 01:48:38');

CREATE TABLE gamerecord (
  id varchar(13) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '序號',
  player_1 varchar(13) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '玩家1',
  player_2 varchar(13) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '玩家2',
  gamename varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '遊戲名稱',
  winner varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '勝利者(1:player_1/ 2:player_2)',
  gamedata text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '遊戲資料',
  updatetime datetime NOT NULL COMMENT '日期時間',
  memo text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='遊戲紀錄';

CREATE TABLE player (
  account varchar(15) NOT NULL COMMENT '帳號',
  password varchar(32) NOT NULL COMMENT '密碼(加密)',
  email varchar(50) NOT NULL COMMENT '信箱',
  nickname varchar(12) NOT NULL COMMENT '暱稱',
  registtime datetime NOT NULL,
  isOnline varchar(1) NOT NULL DEFAULT '否' COMMENT '是否在線上',
  updatetime datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='玩家';

INSERT INTO player (`account`, `password`, email, nickname, registtime, isOnline, updatetime) VALUES('Angelina', '121f86fb9013db37331a1ee94cd423b2', 'Angelina@cc.aa', '安節力那求力', '2016-07-20 01:51:36', '否', '2016-07-20 01:51:36');
INSERT INTO player (`account`, `password`, email, nickname, registtime, isOnline, updatetime) VALUES('sherlockmax', '121f86fb9013db37331a1ee94cd423b2', 'uutony29@gmail.com', '夏洛克', '2016-07-19 22:27:31', '是', '2016-07-19 22:27:31');
INSERT INTO player (`account`, `password`, email, nickname, registtime, isOnline, updatetime) VALUES('WillSmith', '121f86fb9013db37331a1ee94cd423b2', 'WillSmith@aa.cc', '威爾使蜜濕', '2016-07-20 01:22:48', '否', '2016-07-20 01:22:48');


ALTER TABLE friend
  ADD PRIMARY KEY (invite,player),
  ADD KEY invite (invite),
  ADD KEY player (player);

ALTER TABLE gamerecord
  ADD PRIMARY KEY (id),
  ADD KEY player_1 (player_1),
  ADD KEY player_2 (player_2);

ALTER TABLE player
  ADD PRIMARY KEY (account);
