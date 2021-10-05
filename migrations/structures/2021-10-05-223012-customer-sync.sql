ALTER TABLE sf_projects
    ADD last_customer_sync_at DATETIME NULL COMMENT '(DC2Type:datetime_immutable)';
UPDATE sf_projects
SET last_customer_sync_at = '2021-09-05T12:00:00';
