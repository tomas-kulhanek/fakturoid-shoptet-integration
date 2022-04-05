ALTER TABLE sf_project_setting
    ADD accounting_send_mail_invoice                     TINYINT(1) DEFAULT '0' NOT NULL,
    ADD accounting_send_mail_proforma_invoice            TINYINT(1) DEFAULT '0' NOT NULL,
    ADD accounting_send_repeatedly_mail_invoice          TINYINT(1) DEFAULT '0' NOT NULL,
    ADD accounting_send_repeatedly_mail_proforma_invoice TINYINT(1) DEFAULT '0' NOT NULL;
