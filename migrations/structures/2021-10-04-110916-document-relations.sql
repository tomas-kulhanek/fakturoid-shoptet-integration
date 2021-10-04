ALTER TABLE sf_credit_note ADD invoice_id INT DEFAULT NULL;
ALTER TABLE sf_credit_note ADD CONSTRAINT FK_4B4DB622989F1FD FOREIGN KEY (invoice_id) REFERENCES sf_invoice (id) ON DELETE SET NULL;
CREATE INDEX IDX_4B4DB622989F1FD ON sf_credit_note (invoice_id);
ALTER TABLE sf_invoice ADD proforma_invoice_id INT DEFAULT NULL;
ALTER TABLE sf_invoice ADD CONSTRAINT FK_508287937717CC92 FOREIGN KEY (proforma_invoice_id) REFERENCES sf_proforma_invoice (id) ON DELETE SET NULL;
CREATE INDEX IDX_508287937717CC92 ON sf_invoice (proforma_invoice_id);
