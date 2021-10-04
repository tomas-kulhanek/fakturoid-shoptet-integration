CREATE TABLE sf_project_setting
(
    id                INT AUTO_INCREMENT NOT NULL,
    project_id        INT          DEFAULT NULL,
    fakturoid_email   VARCHAR(255) DEFAULT NULL,
    fakturoid_api_key VARCHAR(255) DEFAULT NULL,
    fakturoid_account VARCHAR(255) DEFAULT NULL,
    automatization    VARCHAR(255)       NOT NULL,
    updated_at        DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    UNIQUE INDEX UNIQ_82E7E25166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
ALTER TABLE sf_project_setting
    ADD CONSTRAINT FK_82E7E25166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_projects
    ADD state INT DEFAULT 0 NOT NULL;
