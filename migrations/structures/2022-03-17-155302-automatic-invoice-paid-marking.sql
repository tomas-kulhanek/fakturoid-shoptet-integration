ALTER TABLE sf_project_setting ADD accounting_mark_invoice_as_paid TINYINT(1) DEFAULT '1' NOT NULL;
ALTER TABLE sf_projects CHANGE last_credit_note_sync_at last_credit_note_sync_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)';
