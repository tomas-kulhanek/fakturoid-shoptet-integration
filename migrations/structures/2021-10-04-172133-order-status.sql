UPDATE sf_project_setting SET propagate_delivery_address = false;
ALTER TABLE sf_project_setting CHANGE propagate_delivery_address propagate_delivery_address TINYINT(1) NOT NULL;
