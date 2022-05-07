ALTER TABLE sf_invoice_item ADD recycling_fee_category VARCHAR(255) DEFAULT NULL, ADD recycling_fee VARCHAR(255) DEFAULT NULL, ADD recycling_fee_accounting_id INT DEFAULT NULL;
ALTER TABLE sf_credit_note_item ADD recycling_fee_category VARCHAR(255) DEFAULT NULL, ADD recycling_fee VARCHAR(255) DEFAULT NULL, ADD recycling_fee_accounting_id INT DEFAULT NULL;
ALTER TABLE sf_proforma_invoice_item ADD recycling_fee_category VARCHAR(255) DEFAULT NULL, ADD recycling_fee VARCHAR(255) DEFAULT NULL, ADD recycling_fee_accounting_id INT DEFAULT NULL;
