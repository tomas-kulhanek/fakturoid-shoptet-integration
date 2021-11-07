CREATE TABLE sf_action_log
(
    id                  INT AUTO_INCREMENT NOT NULL,
    project_id          INT          NOT NULL,
    user_id             INT DEFAULT NULL,
    invoice_id          INT DEFAULT NULL,
    proforma_invoice_id INT DEFAULT NULL,
    order_id            INT DEFAULT NULL,
    customer_id         INT DEFAULT NULL,
    type                VARCHAR(255) NOT NULL,
    created_at          DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    document_type       VARCHAR(255) NOT NULL,
    INDEX               IDX_752C2831166D1F9C (project_id),
    INDEX               IDX_752C2831A76ED395 (user_id),
    INDEX               IDX_752C28312989F1FD (invoice_id),
    INDEX               IDX_752C28317717CC92 (proforma_invoice_id),
    INDEX               IDX_752C28318D9F6D38 (order_id),
    INDEX               IDX_752C28319395C3F3 (customer_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB;
ALTER TABLE sf_action_log
    ADD CONSTRAINT FK_752C2831166D1F9C FOREIGN KEY (project_id) REFERENCES sf_projects (id) ON DELETE CASCADE;
ALTER TABLE sf_action_log
    ADD CONSTRAINT FK_752C2831A76ED395 FOREIGN KEY (user_id) REFERENCES core_user (id) ON DELETE SET NULL;
ALTER TABLE sf_action_log
    ADD CONSTRAINT FK_752C28312989F1FD FOREIGN KEY (invoice_id) REFERENCES sf_invoice (id) ON DELETE CASCADE;
ALTER TABLE sf_action_log
    ADD CONSTRAINT FK_752C28317717CC92 FOREIGN KEY (proforma_invoice_id) REFERENCES sf_proforma_invoice (id) ON DELETE CASCADE;
ALTER TABLE sf_action_log
    ADD CONSTRAINT FK_752C28318D9F6D38 FOREIGN KEY (order_id) REFERENCES sf_order (id) ON DELETE CASCADE;
ALTER TABLE sf_action_log
    ADD CONSTRAINT FK_752C28319395C3F3 FOREIGN KEY (customer_id) REFERENCES sf_customer (id) ON DELETE CASCADE;
drop table core_action_log;
