ALTER TABLE sf_order DROP status_name, CHANGE status_id status_id INT NOT NULL;
ALTER TABLE system_order_status ADD `type` VARCHAR(255) NOT NULL;
