ALTER TABLE sf_credit_note ADD accounting_number_line_id INT DEFAULT NULL;
ALTER TABLE sf_proforma_invoice ADD accounting_number_line_id INT DEFAULT NULL;
ALTER TABLE sf_invoice ADD accounting_number_line_id INT DEFAULT NULL;
ALTER TABLE sf_invoice_eet ADD accounting_id INT DEFAULT NULL;
