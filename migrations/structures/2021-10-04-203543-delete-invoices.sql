ALTER TABLE sf_credit_note CHANGE var_symbol var_symbol INT DEFAULT NULL;
ALTER TABLE sf_proforma_invoice CHANGE var_symbol var_symbol INT DEFAULT NULL;
ALTER TABLE sf_invoice_item ADD unit_with_vat VARCHAR(255) DEFAULT NULL, ADD unit_without_vat VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_credit_note_item ADD unit_with_vat VARCHAR(255) DEFAULT NULL, ADD unit_without_vat VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_proforma_invoice_item ADD unit_with_vat VARCHAR(255) DEFAULT NULL, ADD unit_without_vat VARCHAR(255) DEFAULT NULL;
