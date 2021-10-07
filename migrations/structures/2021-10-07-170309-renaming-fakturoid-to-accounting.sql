ALTER TABLE sf_credit_note
    CHANGE fakturoid_number accounting_number             VARCHAR(255) DEFAULT NULL,
    CHANGE fakturoid_issued_at accounting_issued_at          DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_id accounting_id                 INT          DEFAULT NULL,
    CHANGE fakturoid_subject_id accounting_subject_id         INT          DEFAULT NULL,
    CHANGE fakturoid_sent_at accounting_sent_at            DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_paid_at accounting_paid_at            DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_reminder_sent_at accounting_reminder_sent_at   DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_accepted_at accounting_accepted_at        DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_cancelled_at accounting_cancelled_at       DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_webinvoice_seen_at accounting_webinvoice_seen_at DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_public_token accounting_public_token       VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_proforma_invoice
    CHANGE fakturoid_number accounting_number             VARCHAR(255) DEFAULT NULL,
    CHANGE fakturoid_issued_at accounting_issued_at          DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_id accounting_id                 INT          DEFAULT NULL,
    CHANGE fakturoid_subject_id accounting_subject_id         INT          DEFAULT NULL,
    CHANGE fakturoid_sent_at accounting_sent_at            DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_paid_at accounting_paid_at            DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_reminder_sent_at accounting_reminder_sent_at   DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_accepted_at accounting_accepted_at        DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_cancelled_at accounting_cancelled_at       DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_webinvoice_seen_at accounting_webinvoice_seen_at DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_public_token accounting_public_token       VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_invoice
    CHANGE fakturoid_number accounting_number             VARCHAR(255) DEFAULT NULL,
    CHANGE fakturoid_issued_at accounting_issued_at          DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_id accounting_id                 INT          DEFAULT NULL,
    CHANGE fakturoid_subject_id accounting_subject_id         INT          DEFAULT NULL,
    CHANGE fakturoid_sent_at accounting_sent_at            DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_paid_at accounting_paid_at            DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_reminder_sent_at accounting_reminder_sent_at   DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_accepted_at accounting_accepted_at        DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_cancelled_at accounting_cancelled_at       DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_webinvoice_seen_at accounting_webinvoice_seen_at DATE         DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_public_token accounting_public_token       VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_customer
    CHANGE  fakturoid_created_at accounting_created_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE  fakturoid_updated_at accounting_updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE fakturoid_id accounting_id INT DEFAULT NULL;
ALTER TABLE sf_project_setting
    CHANGE fakturoid_email accounting_email   VARCHAR(255) DEFAULT NULL,
    CHANGE fakturoid_account accounting_account VARCHAR(255) DEFAULT NULL,
    CHANGE fakturoid_api_key accounting_api_key LONGTEXT DEFAULT NULL;
