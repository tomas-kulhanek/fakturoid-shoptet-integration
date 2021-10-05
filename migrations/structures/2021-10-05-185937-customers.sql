CREATE TABLE sf_customer_delivery_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    customer_id     INT                NOT NULL,
    company         VARCHAR(255) DEFAULT NULL,
    full_name       VARCHAR(255) DEFAULT NULL,
    street          VARCHAR(255) DEFAULT NULL,
    house_number    VARCHAR(255) DEFAULT NULL,
    city            VARCHAR(255) DEFAULT NULL,
    district        VARCHAR(255) DEFAULT NULL,
    additional      VARCHAR(255) DEFAULT NULL,
    zip             VARCHAR(255) DEFAULT NULL,
    country_code    VARCHAR(255) DEFAULT NULL,
    region_name     VARCHAR(255) DEFAULT NULL,
    region_shortcut VARCHAR(255) DEFAULT NULL,
    INDEX IDX_7D7E038B9395C3F3 (customer_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_customer
(
    id              INT AUTO_INCREMENT NOT NULL,
    project_id      INT                NOT NULL,
    guid            VARCHAR(255)       NOT NULL,
    creation_time   DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    change_time     DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    company_id      VARCHAR(255)     DEFAULT NULL,
    vat_id          VARCHAR(255)     DEFAULT NULL,
    client_code     VARCHAR(255)     DEFAULT NULL,
    remark          VARCHAR(255)     DEFAULT NULL,
    price_ratio     DOUBLE PRECISION DEFAULT NULL,
    birth_date      DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    disabled_orders TINYINT(1)       DEFAULT NULL,
    admin_url       VARCHAR(255)       NOT NULL,
    INDEX IDX_994E2EEE166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_customer_billing_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    customer_id     INT          DEFAULT NULL,
    company         VARCHAR(255) DEFAULT NULL,
    full_name       VARCHAR(255) DEFAULT NULL,
    street          VARCHAR(255) DEFAULT NULL,
    house_number    VARCHAR(255) DEFAULT NULL,
    city            VARCHAR(255) DEFAULT NULL,
    district        VARCHAR(255) DEFAULT NULL,
    additional      VARCHAR(255) DEFAULT NULL,
    zip             VARCHAR(255) DEFAULT NULL,
    country_code    VARCHAR(255) DEFAULT NULL,
    region_name     VARCHAR(255) DEFAULT NULL,
    region_shortcut VARCHAR(255) DEFAULT NULL,
    UNIQUE INDEX UNIQ_22E998DD9395C3F3 (customer_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
ALTER TABLE sf_customer_delivery_address
    ADD CONSTRAINT FK_7D7E038B9395C3F3 FOREIGN KEY (customer_id) REFERENCES sf_customer (id) ON DELETE CASCADE;
ALTER TABLE sf_customer
    ADD CONSTRAINT FK_994E2EEE166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_customer_billing_address
    ADD CONSTRAINT FK_22E998DD9395C3F3 FOREIGN KEY (customer_id) REFERENCES sf_customer (id);
