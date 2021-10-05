CREATE TABLE sf_credit_note_billing_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    document_id     INT                NOT NULL,
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
    INDEX IDX_4A0057F2C33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_credit_note
(
    id                           INT AUTO_INCREMENT NOT NULL,
    order_id                     INT              DEFAULT NULL,
    invoice_id                   INT              DEFAULT NULL,
    project_id                   INT                NOT NULL,
    tax_date                     DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    document_remark              VARCHAR(255)     DEFAULT NULL,
    stock_amount_change_type     VARCHAR(255)     DEFAULT NULL,
    invoice_code                 VARCHAR(255)       NOT NULL,
    code                         VARCHAR(255)       NOT NULL,
    shoptet_code                 VARCHAR(255)     DEFAULT NULL,
    paid                         TINYINT(1)         NOT NULL,
    order_code                   VARCHAR(255)     DEFAULT NULL,
    addresses_equal              TINYINT(1)         NOT NULL,
    is_valid                     TINYINT(1)         NOT NULL,
    var_symbol                   INT              DEFAULT NULL,
    const_symbol                 VARCHAR(255)     DEFAULT NULL,
    spec_symbol                  INT              DEFAULT NULL,
    company_id                   VARCHAR(255)     DEFAULT NULL,
    vat_id                       VARCHAR(255)     DEFAULT NULL,
    tax_id                       VARCHAR(255)     DEFAULT NULL,
    vat_payer                    TINYINT(1)       DEFAULT NULL,
    creation_time                DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    change_time                  DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    due_date                     DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    billing_method_id            INT              DEFAULT NULL,
    billing_method_name          VARCHAR(255)     DEFAULT NULL,
    vat                          DOUBLE PRECISION DEFAULT NULL,
    vat_rate                     DOUBLE PRECISION DEFAULT NULL,
    to_pay                       DOUBLE PRECISION DEFAULT NULL,
    currency_code                VARCHAR(255)     DEFAULT NULL,
    with_vat                     DOUBLE PRECISION DEFAULT NULL,
    without_vat                  DOUBLE PRECISION DEFAULT NULL,
    exchange_rate                DOUBLE PRECISION DEFAULT NULL,
    eshop_bank_account           VARCHAR(255)     DEFAULT NULL,
    eshop_iban                   VARCHAR(255)     DEFAULT NULL,
    eshop_bic                    VARCHAR(255)     DEFAULT NULL,
    eshop_tax_mode               VARCHAR(255)     DEFAULT NULL,
    eshop_document_remark        VARCHAR(255)     DEFAULT NULL,
    weight                       DOUBLE PRECISION DEFAULT NULL,
    complete_package_weight      DOUBLE PRECISION DEFAULT NULL,
    external_system_id           VARCHAR(255)     DEFAULT NULL,
    external_system_last_sync_at DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    fakturoid_number             VARCHAR(255)     DEFAULT NULL,
    fakturoid_issued_at          DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_id                 INT              DEFAULT NULL,
    fakturoid_subject_id         INT              DEFAULT NULL,
    fakturoid_sent_at            DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_paid_at            DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_reminder_sent_at   DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_accepted_at        DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_cancelled_at       DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_webinvoice_seen_at DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_public_token       VARCHAR(255)     DEFAULT NULL,
    INDEX IDX_4B4DB628D9F6D38 (order_id),
    INDEX IDX_4B4DB622989F1FD (invoice_id),
    INDEX IDX_4B4DB62166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_proforma_invoice
(
    id                           INT AUTO_INCREMENT NOT NULL,
    order_id                     INT              DEFAULT NULL,
    invoice_id                   INT              DEFAULT NULL,
    project_id                   INT                NOT NULL,
    code                         VARCHAR(255)       NOT NULL,
    shoptet_code                 VARCHAR(255)     DEFAULT NULL,
    paid                         TINYINT(1)         NOT NULL,
    order_code                   VARCHAR(255)     DEFAULT NULL,
    addresses_equal              TINYINT(1)         NOT NULL,
    is_valid                     TINYINT(1)         NOT NULL,
    var_symbol                   INT              DEFAULT NULL,
    const_symbol                 VARCHAR(255)     DEFAULT NULL,
    spec_symbol                  INT              DEFAULT NULL,
    company_id                   VARCHAR(255)     DEFAULT NULL,
    vat_id                       VARCHAR(255)     DEFAULT NULL,
    tax_id                       VARCHAR(255)     DEFAULT NULL,
    vat_payer                    TINYINT(1)       DEFAULT NULL,
    creation_time                DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    change_time                  DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    due_date                     DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    billing_method_id            INT              DEFAULT NULL,
    billing_method_name          VARCHAR(255)     DEFAULT NULL,
    vat                          DOUBLE PRECISION DEFAULT NULL,
    vat_rate                     DOUBLE PRECISION DEFAULT NULL,
    to_pay                       DOUBLE PRECISION DEFAULT NULL,
    currency_code                VARCHAR(255)     DEFAULT NULL,
    with_vat                     DOUBLE PRECISION DEFAULT NULL,
    without_vat                  DOUBLE PRECISION DEFAULT NULL,
    exchange_rate                DOUBLE PRECISION DEFAULT NULL,
    eshop_bank_account           VARCHAR(255)     DEFAULT NULL,
    eshop_iban                   VARCHAR(255)     DEFAULT NULL,
    eshop_bic                    VARCHAR(255)     DEFAULT NULL,
    eshop_tax_mode               VARCHAR(255)     DEFAULT NULL,
    eshop_document_remark        VARCHAR(255)     DEFAULT NULL,
    weight                       DOUBLE PRECISION DEFAULT NULL,
    complete_package_weight      DOUBLE PRECISION DEFAULT NULL,
    external_system_id           VARCHAR(255)     DEFAULT NULL,
    external_system_last_sync_at DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    fakturoid_number             VARCHAR(255)     DEFAULT NULL,
    fakturoid_issued_at          DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_id                 INT              DEFAULT NULL,
    fakturoid_subject_id         INT              DEFAULT NULL,
    fakturoid_sent_at            DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_paid_at            DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_reminder_sent_at   DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_accepted_at        DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_cancelled_at       DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_webinvoice_seen_at DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_public_token       VARCHAR(255)     DEFAULT NULL,
    INDEX IDX_73D7983D8D9F6D38 (order_id),
    INDEX IDX_73D7983D2989F1FD (invoice_id),
    INDEX IDX_73D7983D166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_order
(
    id                  INT AUTO_INCREMENT NOT NULL,
    project_id          INT                NOT NULL,
    status_id           INT                NOT NULL,
    code                VARCHAR(255)       NOT NULL,
    shoptet_code        VARCHAR(255)     DEFAULT NULL,
    external_code       VARCHAR(255)     DEFAULT NULL,
    creation_time       DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    change_time         DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    email               VARCHAR(255)     DEFAULT NULL,
    phone               VARCHAR(255)     DEFAULT NULL,
    birth_date          DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    client_code         VARCHAR(255)     DEFAULT NULL,
    company_id          VARCHAR(255)     DEFAULT NULL,
    vat_id              VARCHAR(255)     DEFAULT NULL,
    tax_id              VARCHAR(255)     DEFAULT NULL,
    vat_payer           TINYINT(1)       DEFAULT NULL,
    customer_guid       VARCHAR(255)     DEFAULT NULL,
    addresses_equal     TINYINT(1)         NOT NULL,
    cash_desk_order     TINYINT(1)         NOT NULL,
    stock_id            INT              DEFAULT NULL,
    paid                TINYINT(1)         NOT NULL,
    admin_url           VARCHAR(255)       NOT NULL,
    online_payment_link VARCHAR(255)     DEFAULT NULL,
    language            VARCHAR(255)       NOT NULL,
    referer             VARCHAR(255)     DEFAULT NULL,
    billing_method_id   INT              DEFAULT NULL,
    billing_method_name VARCHAR(255)     DEFAULT NULL,
    price_vat           DOUBLE PRECISION DEFAULT NULL,
    price_vat_rate      DOUBLE PRECISION DEFAULT NULL,
    price_to_pay        DOUBLE PRECISION DEFAULT NULL,
    price_currency_code VARCHAR(255)     DEFAULT NULL,
    price_with_vat      DOUBLE PRECISION DEFAULT NULL,
    price_without_vat   DOUBLE PRECISION DEFAULT NULL,
    price_exchange_rate DOUBLE PRECISION DEFAULT NULL,
    client_ipaddress    VARCHAR(255)     DEFAULT NULL,
    INDEX IDX_6148EE62166D1F9C (project_id),
    INDEX IDX_6148EE626BF700BD (status_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_invoice_delivery_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    document_id     INT                NOT NULL,
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
    INDEX IDX_30D833CCC33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_invoice
(
    id                           INT AUTO_INCREMENT NOT NULL,
    proforma_invoice_id          INT              DEFAULT NULL,
    order_id                     INT              DEFAULT NULL,
    project_id                   INT                NOT NULL,
    proforma_invoice_code        VARCHAR(255)     DEFAULT NULL,
    tax_date                     DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    document_remark              VARCHAR(255)     DEFAULT NULL,
    code                         VARCHAR(255)       NOT NULL,
    shoptet_code                 VARCHAR(255)     DEFAULT NULL,
    paid                         TINYINT(1)         NOT NULL,
    order_code                   VARCHAR(255)     DEFAULT NULL,
    addresses_equal              TINYINT(1)         NOT NULL,
    is_valid                     TINYINT(1)         NOT NULL,
    var_symbol                   INT              DEFAULT NULL,
    const_symbol                 VARCHAR(255)     DEFAULT NULL,
    spec_symbol                  INT              DEFAULT NULL,
    company_id                   VARCHAR(255)     DEFAULT NULL,
    vat_id                       VARCHAR(255)     DEFAULT NULL,
    tax_id                       VARCHAR(255)     DEFAULT NULL,
    vat_payer                    TINYINT(1)       DEFAULT NULL,
    creation_time                DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    change_time                  DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    due_date                     DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    billing_method_id            INT              DEFAULT NULL,
    billing_method_name          VARCHAR(255)     DEFAULT NULL,
    vat                          DOUBLE PRECISION DEFAULT NULL,
    vat_rate                     DOUBLE PRECISION DEFAULT NULL,
    to_pay                       DOUBLE PRECISION DEFAULT NULL,
    currency_code                VARCHAR(255)     DEFAULT NULL,
    with_vat                     DOUBLE PRECISION DEFAULT NULL,
    without_vat                  DOUBLE PRECISION DEFAULT NULL,
    exchange_rate                DOUBLE PRECISION DEFAULT NULL,
    eshop_bank_account           VARCHAR(255)     DEFAULT NULL,
    eshop_iban                   VARCHAR(255)     DEFAULT NULL,
    eshop_bic                    VARCHAR(255)     DEFAULT NULL,
    eshop_tax_mode               VARCHAR(255)     DEFAULT NULL,
    eshop_document_remark        VARCHAR(255)     DEFAULT NULL,
    weight                       DOUBLE PRECISION DEFAULT NULL,
    complete_package_weight      DOUBLE PRECISION DEFAULT NULL,
    external_system_id           VARCHAR(255)     DEFAULT NULL,
    external_system_last_sync_at DATETIME         DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    fakturoid_number             VARCHAR(255)     DEFAULT NULL,
    fakturoid_issued_at          DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_id                 INT              DEFAULT NULL,
    fakturoid_subject_id         INT              DEFAULT NULL,
    fakturoid_sent_at            DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_paid_at            DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_reminder_sent_at   DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_accepted_at        DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_cancelled_at       DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_webinvoice_seen_at DATE             DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    fakturoid_public_token       VARCHAR(255)     DEFAULT NULL,
    INDEX IDX_508287937717CC92 (proforma_invoice_id),
    INDEX IDX_508287938D9F6D38 (order_id),
    INDEX IDX_50828793166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_order_item
(
    id                     INT AUTO_INCREMENT NOT NULL,
    document_id            INT                NOT NULL,
    supplier_name          VARCHAR(255)     DEFAULT NULL,
    amount_completed       DOUBLE PRECISION DEFAULT NULL,
    buy_price_with_vat     DOUBLE PRECISION DEFAULT NULL,
    buy_price_without_vat  DOUBLE PRECISION DEFAULT NULL,
    buy_price_vat          DOUBLE PRECISION DEFAULT NULL,
    buy_price_vat_rate     DOUBLE PRECISION DEFAULT NULL,
    recycling_fee_category VARCHAR(255)     DEFAULT NULL,
    recycling_fee          VARCHAR(255)     DEFAULT NULL,
    status_id              INT              DEFAULT NULL,
    status_name            VARCHAR(255)     DEFAULT NULL,
    main_image_name        VARCHAR(255)     DEFAULT NULL,
    main_image_neo_name    VARCHAR(255)     DEFAULT NULL,
    main_image_cdn_name    VARCHAR(255)     DEFAULT NULL,
    main_image_priority    INT              DEFAULT NULL,
    main_image_description VARCHAR(255)     DEFAULT NULL,
    stock_location         VARCHAR(255)     DEFAULT NULL,
    item_id                INT                NOT NULL,
    warranty_description   VARCHAR(255)     DEFAULT NULL,
    product_guid           VARCHAR(255)     DEFAULT NULL,
    code                   VARCHAR(255)     DEFAULT NULL,
    item_type              VARCHAR(255)       NOT NULL,
    name                   VARCHAR(255)     DEFAULT NULL,
    variant_name           VARCHAR(255)     DEFAULT NULL,
    brand                  VARCHAR(255)     DEFAULT NULL,
    remark                 VARCHAR(255)     DEFAULT NULL,
    weight                 DOUBLE PRECISION DEFAULT NULL,
    additional_field       VARCHAR(255)     DEFAULT NULL,
    amount                 DOUBLE PRECISION DEFAULT NULL,
    amount_unit            VARCHAR(255)     DEFAULT NULL,
    price_ratio            DOUBLE PRECISION DEFAULT NULL,
    item_price_with_vat    DOUBLE PRECISION DEFAULT NULL,
    item_price_without_vat DOUBLE PRECISION DEFAULT NULL,
    item_price_vat         DOUBLE PRECISION DEFAULT NULL,
    item_price_vat_rate    INT              DEFAULT NULL,
    control_hash           VARCHAR(255)       NOT NULL,
    unit_price_with_vat    DOUBLE PRECISION DEFAULT NULL,
    unit_price_without_vat VARCHAR(255)     DEFAULT NULL,
    INDEX IDX_9503C1BDC33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_invoice_item
(
    id               INT AUTO_INCREMENT NOT NULL,
    document_id      INT                NOT NULL,
    product_guid     VARCHAR(255)     DEFAULT NULL,
    item_type        VARCHAR(255)       NOT NULL,
    code             VARCHAR(255)     DEFAULT NULL,
    name             VARCHAR(255)     DEFAULT NULL,
    variant_name     VARCHAR(255)     DEFAULT NULL,
    brand            VARCHAR(255)     DEFAULT NULL,
    amount           DOUBLE PRECISION DEFAULT NULL,
    amount_unit      VARCHAR(255)     DEFAULT NULL,
    weight           DOUBLE PRECISION DEFAULT NULL,
    remark           VARCHAR(255)     DEFAULT NULL,
    price_ratio      DOUBLE PRECISION DEFAULT NULL,
    additional_field VARCHAR(255)     DEFAULT NULL,
    with_vat         DOUBLE PRECISION DEFAULT NULL,
    without_vat      DOUBLE PRECISION DEFAULT NULL,
    unit_with_vat    DOUBLE PRECISION DEFAULT NULL,
    unit_without_vat DOUBLE PRECISION DEFAULT NULL,
    vat              DOUBLE PRECISION DEFAULT NULL,
    vat_rate         INT              DEFAULT NULL,
    control_hash     VARCHAR(255)       NOT NULL,
    INDEX IDX_FC1C14FDC33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_invoice_billing_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    document_id     INT                NOT NULL,
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
    INDEX IDX_36B887BDC33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_projects
(
    id            INT AUTO_INCREMENT NOT NULL,
    owner_id      INT                NOT NULL,
    access_token  LONGTEXT           NOT NULL,
    token_type    VARCHAR(255)       NOT NULL,
    scope         VARCHAR(255)       NOT NULL,
    state         INT DEFAULT 0      NOT NULL,
    eshop_id      INT                NOT NULL,
    eshop_url     VARCHAR(255)       NOT NULL,
    contact_email VARCHAR(255)       NOT NULL,
    guid          CHAR(36)           NOT NULL COMMENT '(DC2Type:uuid)',
    created_at    DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    UNIQUE INDEX UNIQ_44E413432B6FCFB2 (guid),
    INDEX IDX_44E413437E3C61F9 (owner_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_users_projects
(
    project_id INT NOT NULL,
    user_id    INT NOT NULL,
    INDEX IDX_B869463F166D1F9C (project_id),
    INDEX IDX_B869463FA76ED395 (user_id),
    PRIMARY KEY (project_id, user_id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_order_shipping_method
(
    id          INT AUTO_INCREMENT NOT NULL,
    document_id INT                NOT NULL,
    guid        VARCHAR(255) DEFAULT NULL,
    name        VARCHAR(255) DEFAULT NULL,
    item_id     INT                NOT NULL,
    INDEX IDX_F676F716C33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_registered_webhooks
(
    id         INT UNSIGNED NOT NULL,
    project_id INT          NOT NULL,
    event      VARCHAR(255) NOT NULL,
    url        VARCHAR(255) NOT NULL,
    created_at DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    INDEX IDX_69412DBA166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_credit_note_delivery_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    document_id     INT          DEFAULT NULL,
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
    INDEX IDX_D6C7D71DC33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_order_payment_method
(
    id          INT AUTO_INCREMENT NOT NULL,
    document_id INT                NOT NULL,
    guid        VARCHAR(255) DEFAULT NULL,
    name        VARCHAR(255) DEFAULT NULL,
    item_id     INT                NOT NULL,
    INDEX IDX_DDEC5FE1C33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_credit_note_item
(
    id               INT AUTO_INCREMENT NOT NULL,
    document_id      INT                NOT NULL,
    product_guid     VARCHAR(255)     DEFAULT NULL,
    item_type        VARCHAR(255)       NOT NULL,
    code             VARCHAR(255)     DEFAULT NULL,
    name             VARCHAR(255)     DEFAULT NULL,
    variant_name     VARCHAR(255)     DEFAULT NULL,
    brand            VARCHAR(255)     DEFAULT NULL,
    amount           DOUBLE PRECISION DEFAULT NULL,
    amount_unit      VARCHAR(255)     DEFAULT NULL,
    weight           DOUBLE PRECISION DEFAULT NULL,
    remark           VARCHAR(255)     DEFAULT NULL,
    price_ratio      DOUBLE PRECISION DEFAULT NULL,
    additional_field VARCHAR(255)     DEFAULT NULL,
    with_vat         DOUBLE PRECISION DEFAULT NULL,
    without_vat      DOUBLE PRECISION DEFAULT NULL,
    unit_with_vat    DOUBLE PRECISION DEFAULT NULL,
    unit_without_vat DOUBLE PRECISION DEFAULT NULL,
    vat              DOUBLE PRECISION DEFAULT NULL,
    vat_rate         INT              DEFAULT NULL,
    control_hash     VARCHAR(255)       NOT NULL,
    INDEX IDX_F6A2DC78C33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_proforma_invoice_billing_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    document_id     INT                NOT NULL,
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
    INDEX IDX_E00E3986C33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_proforma_invoice_item
(
    id               INT AUTO_INCREMENT NOT NULL,
    document_id      INT                NOT NULL,
    product_guid     VARCHAR(255)     DEFAULT NULL,
    item_type        VARCHAR(255)       NOT NULL,
    code             VARCHAR(255)     DEFAULT NULL,
    name             VARCHAR(255)     DEFAULT NULL,
    variant_name     VARCHAR(255)     DEFAULT NULL,
    brand            VARCHAR(255)     DEFAULT NULL,
    amount           DOUBLE PRECISION DEFAULT NULL,
    amount_unit      VARCHAR(255)     DEFAULT NULL,
    weight           DOUBLE PRECISION DEFAULT NULL,
    remark           VARCHAR(255)     DEFAULT NULL,
    price_ratio      DOUBLE PRECISION DEFAULT NULL,
    additional_field VARCHAR(255)     DEFAULT NULL,
    with_vat         DOUBLE PRECISION DEFAULT NULL,
    without_vat      DOUBLE PRECISION DEFAULT NULL,
    unit_with_vat    DOUBLE PRECISION DEFAULT NULL,
    unit_without_vat DOUBLE PRECISION DEFAULT NULL,
    vat              DOUBLE PRECISION DEFAULT NULL,
    vat_rate         INT              DEFAULT NULL,
    control_hash     VARCHAR(255)       NOT NULL,
    INDEX IDX_EFAFE9F9C33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_order_billing_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    document_id     INT                NOT NULL,
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
    INDEX IDX_E515EC6FC33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_order_delivery_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    document_id     INT                NOT NULL,
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
    INDEX IDX_58D62D5FC33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_order_shipping_detail
(
    id           INT AUTO_INCREMENT NOT NULL,
    document_id  INT                NOT NULL,
    branch_id    VARCHAR(255) DEFAULT NULL,
    name         VARCHAR(255) DEFAULT NULL,
    note         VARCHAR(255) DEFAULT NULL,
    place        VARCHAR(255) DEFAULT NULL,
    street       VARCHAR(255)       NOT NULL,
    city         VARCHAR(255) DEFAULT NULL,
    zip_code     VARCHAR(255) DEFAULT NULL,
    country_code VARCHAR(255) DEFAULT NULL,
    link         VARCHAR(255) DEFAULT NULL,
    latitude     VARCHAR(255) DEFAULT NULL,
    longtitude   VARCHAR(255) DEFAULT NULL,
    INDEX IDX_8629B2E5C33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_proforma_invoice_delivery_address
(
    id              INT AUTO_INCREMENT NOT NULL,
    document_id     INT                NOT NULL,
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
    INDEX IDX_81056C56C33F7837 (document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_received_webhooks
(
    id             INT AUTO_INCREMENT NOT NULL,
    project_id     INT                NOT NULL,
    eshop_id       INT                NOT NULL,
    event          VARCHAR(255)       NOT NULL,
    event_created  DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    event_instance VARCHAR(255)       NOT NULL,
    last_received  DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    receive_count  INT                NOT NULL,
    INDEX IDX_B9C9207A166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE core_user
(
    id         INT AUTO_INCREMENT NOT NULL,
    email      VARCHAR(255)       NOT NULL,
    created_at DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    guid       CHAR(36)           NOT NULL COMMENT '(DC2Type:uuid)',
    UNIQUE INDEX UNIQ_BF76157CE7927C74 (email),
    UNIQUE INDEX UNIQ_BF76157C2B6FCFB2 (guid),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE system_order_status
(
    id              INT AUTO_INCREMENT NOT NULL,
    project_id      INT                NOT NULL,
    shoptet_id      INT DEFAULT NULL,
    rank            INT DEFAULT NULL,
    name            VARCHAR(255)       NOT NULL,
    mark_as_paid    TINYINT(1)         NOT NULL,
    create_invoice  TINYINT(1)         NOT NULL,
    create_proforma TINYINT(1)         NOT NULL,
    is_default      TINYINT(1)         NOT NULL,
    `type`          VARCHAR(255)       NOT NULL,
    INDEX IDX_ABBC5DC4166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
CREATE TABLE sf_project_setting
(
    id                         INT AUTO_INCREMENT NOT NULL,
    project_id                 INT          DEFAULT NULL,
    fakturoid_email            VARCHAR(255) DEFAULT NULL,
    fakturoid_api_key          LONGTEXT     DEFAULT NULL,
    fakturoid_account          VARCHAR(255) DEFAULT NULL,
    propagate_delivery_address TINYINT(1)         NOT NULL,
    automatization             VARCHAR(255)       NOT NULL,
    updated_at                 DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    UNIQUE INDEX UNIQ_82E7E25166D1F9C (project_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8
  COLLATE `UTF8_unicode_ci`
  ENGINE = InnoDB;
ALTER TABLE sf_credit_note_billing_address
    ADD CONSTRAINT FK_4A0057F2C33F7837 FOREIGN KEY (document_id) REFERENCES sf_credit_note (id) ON DELETE CASCADE;
ALTER TABLE sf_credit_note
    ADD CONSTRAINT FK_4B4DB628D9F6D38 FOREIGN KEY (order_id) REFERENCES sf_order (id) ON DELETE SET NULL;
ALTER TABLE sf_credit_note
    ADD CONSTRAINT FK_4B4DB622989F1FD FOREIGN KEY (invoice_id) REFERENCES sf_invoice (id) ON DELETE SET NULL;
ALTER TABLE sf_credit_note
    ADD CONSTRAINT FK_4B4DB62166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_proforma_invoice
    ADD CONSTRAINT FK_73D7983D8D9F6D38 FOREIGN KEY (order_id) REFERENCES sf_order (id) ON DELETE SET NULL;
ALTER TABLE sf_proforma_invoice
    ADD CONSTRAINT FK_73D7983D2989F1FD FOREIGN KEY (invoice_id) REFERENCES sf_invoice (id) ON DELETE SET NULL;
ALTER TABLE sf_proforma_invoice
    ADD CONSTRAINT FK_73D7983D166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_order
    ADD CONSTRAINT FK_6148EE62166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_order
    ADD CONSTRAINT FK_6148EE626BF700BD FOREIGN KEY (status_id) REFERENCES system_order_status (id) ON DELETE RESTRICT;
ALTER TABLE sf_invoice_delivery_address
    ADD CONSTRAINT FK_30D833CCC33F7837 FOREIGN KEY (document_id) REFERENCES sf_invoice (id) ON DELETE CASCADE;
ALTER TABLE sf_invoice
    ADD CONSTRAINT FK_508287937717CC92 FOREIGN KEY (proforma_invoice_id) REFERENCES sf_proforma_invoice (id) ON DELETE SET NULL;
ALTER TABLE sf_invoice
    ADD CONSTRAINT FK_508287938D9F6D38 FOREIGN KEY (order_id) REFERENCES sf_order (id) ON DELETE SET NULL;
ALTER TABLE sf_invoice
    ADD CONSTRAINT FK_50828793166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_order_item
    ADD CONSTRAINT FK_9503C1BDC33F7837 FOREIGN KEY (document_id) REFERENCES sf_order (id) ON DELETE CASCADE;
ALTER TABLE sf_invoice_item
    ADD CONSTRAINT FK_FC1C14FDC33F7837 FOREIGN KEY (document_id) REFERENCES sf_invoice (id) ON DELETE CASCADE;
ALTER TABLE sf_invoice_billing_address
    ADD CONSTRAINT FK_36B887BDC33F7837 FOREIGN KEY (document_id) REFERENCES sf_invoice (id) ON DELETE CASCADE;
ALTER TABLE sf_projects
    ADD CONSTRAINT FK_44E413437E3C61F9 FOREIGN KEY (owner_id) REFERENCES core_user (id) ON DELETE CASCADE;
ALTER TABLE sf_users_projects
    ADD CONSTRAINT FK_B869463F166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_users_projects
    ADD CONSTRAINT FK_B869463FA76ED395 FOREIGN KEY (user_id) REFERENCES core_user (id) ON DELETE CASCADE;
ALTER TABLE sf_order_shipping_method
    ADD CONSTRAINT FK_F676F716C33F7837 FOREIGN KEY (document_id) REFERENCES sf_order (id) ON DELETE CASCADE;
ALTER TABLE sf_registered_webhooks
    ADD CONSTRAINT FK_69412DBA166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_credit_note_delivery_address
    ADD CONSTRAINT FK_D6C7D71DC33F7837 FOREIGN KEY (document_id) REFERENCES sf_credit_note (id);
ALTER TABLE sf_order_payment_method
    ADD CONSTRAINT FK_DDEC5FE1C33F7837 FOREIGN KEY (document_id) REFERENCES sf_order (id) ON DELETE CASCADE;
ALTER TABLE sf_credit_note_item
    ADD CONSTRAINT FK_F6A2DC78C33F7837 FOREIGN KEY (document_id) REFERENCES sf_credit_note (id) ON DELETE CASCADE;
ALTER TABLE sf_proforma_invoice_billing_address
    ADD CONSTRAINT FK_E00E3986C33F7837 FOREIGN KEY (document_id) REFERENCES sf_proforma_invoice (id) ON DELETE CASCADE;
ALTER TABLE sf_proforma_invoice_item
    ADD CONSTRAINT FK_EFAFE9F9C33F7837 FOREIGN KEY (document_id) REFERENCES sf_proforma_invoice (id) ON DELETE CASCADE;
ALTER TABLE sf_order_billing_address
    ADD CONSTRAINT FK_E515EC6FC33F7837 FOREIGN KEY (document_id) REFERENCES sf_order (id) ON DELETE CASCADE;
ALTER TABLE sf_order_delivery_address
    ADD CONSTRAINT FK_58D62D5FC33F7837 FOREIGN KEY (document_id) REFERENCES sf_order (id) ON DELETE CASCADE;
ALTER TABLE sf_order_shipping_detail
    ADD CONSTRAINT FK_8629B2E5C33F7837 FOREIGN KEY (document_id) REFERENCES sf_order (id) ON DELETE CASCADE;
ALTER TABLE sf_proforma_invoice_delivery_address
    ADD CONSTRAINT FK_81056C56C33F7837 FOREIGN KEY (document_id) REFERENCES sf_proforma_invoice (id) ON DELETE CASCADE;
ALTER TABLE sf_received_webhooks
    ADD CONSTRAINT FK_B9C9207A166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE system_order_status
    ADD CONSTRAINT FK_ABBC5DC4166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_project_setting
    ADD CONSTRAINT FK_82E7E25166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
