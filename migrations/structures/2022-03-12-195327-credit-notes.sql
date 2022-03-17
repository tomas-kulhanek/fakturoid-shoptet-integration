ALTER TABLE sf_projects ADD last_credit_note_sync_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)' DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE sf_project_setting CHANGE accounting_customer_tags accounting_customer_tags VARCHAR(255) NOT NULL;
