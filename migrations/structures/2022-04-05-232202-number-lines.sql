CREATE TABLE ac_number_lines
(
    id            INT UNSIGNED AUTO_INCREMENT NOT NULL,
    project_id    INT UNSIGNED NOT NULL,
    accounting_id INT DEFAULT NULL,
    format        VARCHAR(255) NOT NULL,
    `default`     TINYINT(1) NOT NULL,
    INDEX         IDX_7DCD1C6B166D1F9C (project_id),
    UNIQUE INDEX project_accounting_id (project_id, accounting_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB;
ALTER TABLE ac_number_lines
    ADD CONSTRAINT FK_7DCD1C6B166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_project_setting
    ADD accounting_number_line INT UNSIGNED DEFAULT NULL, ADD accounting_credit_note_number_line INT UNSIGNED DEFAULT NULL;
ALTER TABLE sf_project_setting
    ADD CONSTRAINT FK_82E7E25EF6FC593 FOREIGN KEY (accounting_number_line) REFERENCES ac_number_lines (id) ON DELETE CASCADE;
ALTER TABLE sf_project_setting
    ADD CONSTRAINT FK_82E7E25634515A2 FOREIGN KEY (accounting_credit_note_number_line) REFERENCES ac_number_lines (id) ON DELETE CASCADE;
CREATE UNIQUE INDEX UNIQ_82E7E25EF6FC593 ON sf_project_setting (accounting_number_line);
CREATE UNIQUE INDEX UNIQ_82E7E25634515A2 ON sf_project_setting (accounting_credit_note_number_line);
