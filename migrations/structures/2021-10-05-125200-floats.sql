ALTER TABLE sf_proforma_invoice ADD invoice_id INT DEFAULT NULL;
ALTER TABLE sf_proforma_invoice ADD CONSTRAINT FK_73D7983D2989F1FD FOREIGN KEY (invoice_id) REFERENCES sf_invoice (id) ON DELETE SET NULL;
CREATE INDEX IDX_73D7983D2989F1FD ON sf_proforma_invoice (invoice_id);
