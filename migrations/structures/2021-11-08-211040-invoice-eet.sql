CREATE TABLE sf_invoice_eet
(
    id               INT AUTO_INCREMENT NOT NULL,
    invoice_id       INT          NOT NULL,
    uuid             VARCHAR(255)     DEFAULT NULL,
    first_sent       TINYINT(1) NOT NULL,
    vat_id           VARCHAR(255) NOT NULL,
    revenue_date     DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    total_revenue    DOUBLE PRECISION DEFAULT NULL,
    vat_base1        DOUBLE PRECISION DEFAULT NULL,
    vat1             DOUBLE PRECISION DEFAULT NULL,
    vat_base2        DOUBLE PRECISION DEFAULT NULL,
    vat2             DOUBLE PRECISION DEFAULT NULL,
    vat_base3        DOUBLE PRECISION DEFAULT NULL,
    vat3             DOUBLE PRECISION DEFAULT NULL,
    non_taxable_base DOUBLE PRECISION DEFAULT NULL,
    exchange_rate    DOUBLE PRECISION DEFAULT NULL,
    pkp              VARCHAR(255)     DEFAULT NULL,
    bkp              VARCHAR(255)     DEFAULT NULL,
    fik              VARCHAR(255)     DEFAULT NULL,
    mode             INT          NOT NULL,
    eet_mod          VARCHAR(255) NOT NULL,
    sent             DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    cash_desk_id     VARCHAR(255) NOT NULL,
    document_type    VARCHAR(255) NOT NULL,
    active           TINYINT(1) NOT NULL,
    UNIQUE INDEX UNIQ_9A850B962989F1FD (invoice_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB;
ALTER TABLE sf_invoice_eet
    ADD CONSTRAINT FK_9A850B962989F1FD FOREIGN KEY (invoice_id) REFERENCES sf_invoice (id) ON DELETE CASCADE;
