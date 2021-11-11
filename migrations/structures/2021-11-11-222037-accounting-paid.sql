ALTER TABLE sf_invoice ADD accounting_paid TINYINT(1) DEFAULT '0' NOT NULL;
ALTER TABLE sf_credit_note ADD accounting_paid TINYINT(1) DEFAULT '0' NOT NULL;
ALTER TABLE sf_proforma_invoice ADD accounting_paid TINYINT(1) DEFAULT '0' NOT NULL;
