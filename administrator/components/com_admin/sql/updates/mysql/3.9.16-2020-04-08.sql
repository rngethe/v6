UPDATE `jos_emundus_setup_tags` SET `request` = 'php|require_once (JPATH_BASE.DS.\'components\'.DS.\'com_emundus\'.DS.\'models\'.DS.\'application.php\');\r\n$m_application = new EmundusModelApplication();\r\n$fnum = \'[FNUM]\';\r\nif (!empty($fnum)) {\r\n return $m_application->getAttachmentsProgress($fnum).\'%\';\r\n}' WHERE `jos_emundus_setup_tags`.`tag` LIKE 'ATTACHMENT_PROGRESS';
UPDATE `jos_emundus_setup_tags` SET `request` = 'php|require_once (JPATH_BASE.DS.\'components\'.DS.\'com_emundus\'.DS.\'models\'.DS.\'application.php\');\r\n$m_application = new EmundusModelApplication();\r\n$fnum = \'[FNUM]\';\r\nif (!empty($fnum)) {\r\n $app_progress = $m_application->getFormsProgress($fnum);\r\nreturn $app_progress.\'%\';\r\n}' WHERE `jos_emundus_setup_tags`.`tag` LIKE 'APPLICATION_PROGRESS';

