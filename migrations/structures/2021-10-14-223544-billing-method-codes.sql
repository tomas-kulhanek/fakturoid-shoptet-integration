ALTER TABLE sf_credit_note ADD billing_method VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_proforma_invoice ADD billing_method VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_order ADD billing_method VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_invoice ADD billing_method VARCHAR(255) DEFAULT NULL;
