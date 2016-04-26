-- insert task to set users status to invisible
INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`) VALUES 
('Ynchat Check Update Status', 'ynchat', 'Ynchat_Plugin_Task_Core', 1800, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);

ALTER TABLE  `engine4_ynchat_status` ADD  `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;