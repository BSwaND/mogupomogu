ALTER TABLE `#__djcf_payments` ADD `type_details` varchar(255) NOT NULL;
ALTER TABLE `#__djcf_categories` ADD `ads_limit` int(11) NOT NULL DEFAULT 0;

INSERT INTO `#__djcf_emails` (`id`, `label`, `title`, `content`) VALUES
(35, 'COM_DJCLASSIFIEDS_ET_PAYMENT_COMPLETED', 'Payment completed', '<p>Hello,</p>\r\n<p>Payment type:&nbsp;[[payment_item_name]]</p>\r\n<p>Item name:&nbsp;[[payment_type]]</p>\r\n<p>Price:&nbsp;[[payment_price]]</p>\r\n<p>Payment method:&nbsp;[[payment_method]]</p>');