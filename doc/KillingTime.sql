SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS KillingTime DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE KillingTime;

CREATE TABLE IF NOT EXISTS friend (
  invite varchar(13) NOT NULL COMMENT '邀請人',
  player varchar(13) NOT NULL COMMENT '受邀人',
  `status` varchar(1) NOT NULL COMMENT '狀態(F:好友/W:待確認)',
  updatetime datetime NOT NULL COMMENT '日期時間',
  PRIMARY KEY (invite,player),
  KEY invite (invite),
  KEY player (player)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='好友列表';

CREATE TABLE IF NOT EXISTS gamerecord (
  id varchar(13) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '序號',
  player_1 varchar(13) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '玩家1',
  player_2 varchar(13) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '玩家2',
  gamename varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '遊戲名稱',
  winner varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '勝利者(1:player_1/ 2:player_2)',
  gamedata text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '遊戲資料',
  updatetime datetime NOT NULL COMMENT '日期時間',
  memo text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '描述',
  PRIMARY KEY (id),
  KEY player_1 (player_1),
  KEY player_2 (player_2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='遊戲紀錄';

CREATE TABLE IF NOT EXISTS player (
  account varchar(15) NOT NULL COMMENT '帳號',
  `password` varchar(32) NOT NULL COMMENT '密碼(加密)',
  email varchar(50) NOT NULL COMMENT '信箱',
  nickname varchar(12) NOT NULL COMMENT '暱稱',
  registtime datetime NOT NULL,
  isOnline varchar(1) NOT NULL DEFAULT '否' COMMENT '是否在線上',
  updatetime datetime NOT NULL,
  PRIMARY KEY (account)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='玩家';
