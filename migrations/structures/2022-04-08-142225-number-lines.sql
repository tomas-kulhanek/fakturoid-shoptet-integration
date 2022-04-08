ALTER TABLE sf_project_setting DROP FOREIGN KEY FK_82E7E25634515A2;
ALTER TABLE sf_project_setting DROP FOREIGN KEY FK_82E7E25EF6FC593;
ALTER TABLE sf_project_setting ADD CONSTRAINT FK_82E7E25634515A2 FOREIGN KEY (accounting_credit_note_number_line) REFERENCES ac_number_lines (id) ON DELETE SET NULL;
ALTER TABLE sf_project_setting ADD CONSTRAINT FK_82E7E25EF6FC593 FOREIGN KEY (accounting_number_line) REFERENCES ac_number_lines (id) ON DELETE SET NULL;
ALTER TABLE ac_number_lines CHANGE preview preview VARCHAR(255) DEFAULT NULL;