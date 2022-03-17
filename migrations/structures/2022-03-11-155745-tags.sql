ALTER TABLE sf_project_setting
    ADD accounting_invoice_tags          VARCHAR(255) DEFAULT NULL,
    ADD accounting_proforma_invoice_tags VARCHAR(255) DEFAULT NULL,
    ADD accounting_credit_note_tags      VARCHAR(255) DEFAULT NULL,
    ADD accounting_customer_tags         VARCHAR(255) DEFAULT NULL;
