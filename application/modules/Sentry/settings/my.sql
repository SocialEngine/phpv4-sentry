
INSERT IGNORE INTO `engine4_core_settings` (`name` , `value`) VALUES
('sentry.enabled', '0'),
('sentry.dsn', '0');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_Sentry', 'Sentry', 'Sentry Integration', '', '{"route":"admin_default","module":"Sentry","controller":"settings"}', 'core_admin_main_settings', '', 999);
