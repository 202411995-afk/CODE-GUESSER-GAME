/*
-- Query: describe game_scores
-- Date: 2025-12-13 23:43
*/
INSERT INTO `` (`Field`,`Type`,`Null`,`Key`,`Default`,`Extra`) VALUES ('id','int(11)','NO','PRI',NULL,'auto_increment');
INSERT INTO `` (`Field`,`Type`,`Null`,`Key`,`Default`,`Extra`) VALUES ('username','varchar(50)','NO','',NULL,'');
INSERT INTO `` (`Field`,`Type`,`Null`,`Key`,`Default`,`Extra`) VALUES ('game_mode','enum(\'single\',\'multi\')','NO','',NULL,'');
INSERT INTO `` (`Field`,`Type`,`Null`,`Key`,`Default`,`Extra`) VALUES ('correct_answers','int(11)','NO','',NULL,'');
INSERT INTO `` (`Field`,`Type`,`Null`,`Key`,`Default`,`Extra`) VALUES ('time_taken','int(11)','NO','',NULL,'');
INSERT INTO `` (`Field`,`Type`,`Null`,`Key`,`Default`,`Extra`) VALUES ('played_at','timestamp','NO','','current_timestamp()','');
