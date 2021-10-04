ALTER TABLE system_order_status CHANGE shoptet_id shoptet_id INT DEFAULT NULL, CHANGE rank rank INT DEFAULT NULL;
ALTER TABLE sf_project_setting ADD propagate_delivery_address TINYINT(1) DEFAULT NULL;
