ALTER TABLE `#__djcf_items` 
	ADD `new_draft` int(11) NOT NULL DEFAULT 0;

ALTER TABLE `#__djcf_fields` 
	ADD `chx_filter_logic` int(11) NOT NULL DEFAULT 0;