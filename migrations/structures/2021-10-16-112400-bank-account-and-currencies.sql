CREATE TABLE ac_bank_account
(
    id            INT AUTO_INCREMENT NOT NULL,
    project_id    INT                NOT NULL,
    accounting_id INT                NOT NULL,
    name          VARCHAR(255)       NOT NULL,
    currency      VARCHAR(255)       NOT NULL,
    number        VARCHAR(255) DEFAULT NULL,
    iban          VARCHAR(255) DEFAULT NULL,
    swift         VARCHAR(255) DEFAULT NULL,
    INDEX IDX_DA272D3F166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_currency
(
    id                   INT AUTO_INCREMENT NOT NULL,
    project_id           INT                NOT NULL,
    bank_account_id      INT DEFAULT NULL,
    code                 VARCHAR(255)       NOT NULL,
    title                VARCHAR(255)       NOT NULL,
    is_default           TINYINT(1)         NOT NULL,
    is_default_admin     TINYINT(1)         NOT NULL,
    is_visible           TINYINT(1)         NOT NULL,
    priority             INT                NOT NULL,
    price_decimal_places INT                NOT NULL,
    INDEX IDX_712128D8166D1F9C (project_id),
    INDEX IDX_712128D812CB990C (bank_account_id),
    UNIQUE INDEX project_code (project_id, code),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
ALTER TABLE ac_bank_account
    ADD CONSTRAINT FK_DA272D3F166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_currency
    ADD CONSTRAINT FK_712128D8166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_currency
    ADD CONSTRAINT FK_712128D812CB990C FOREIGN KEY (bank_account_id) REFERENCES ac_bank_account (id) ON DELETE CASCADE;
