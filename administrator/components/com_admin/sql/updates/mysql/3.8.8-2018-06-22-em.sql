/* add a column to jos_emundus_uploads and jos_emundus_setup_attachments for photos and passports validation */

ALTER TABLE `jos_emundus_uploads` ADD `is_validated` DOUBLE DEFAULT NULL ;

ALTER TABLE `jos_emundus_setup_attachments` ADD `ocr_keywords` TEXT  DEFAULT NULL ;

UPDATE `jos_emundus_setup_attachments` SET `ocr_keywords` = 'passport;passeport;pasaporte;passaporte' WHERE lbl LIKE '%_passport%' ;

UPDATE `jos_emundus_setup_attachments` SET `ocr_keywords` = 'work experience;professional experience;personal information;education;experience professionnelle;diplomes obtenus' WHERE lbl LIKE '_cv%' ;

UPDATE `jos_emundus_setup_attachments` SET `ocr_keywords` = "dear;letter of motivation;motivation letter;cover letter;best regards;sincerely;thanks;thank you;statement of purpose;lettre de motivation;madame, monsieur;suite favorable;mes considérations;prie d'agréer;dans l'attente;veuillez agréer" WHERE lbl LIKE '_motivation%' ;