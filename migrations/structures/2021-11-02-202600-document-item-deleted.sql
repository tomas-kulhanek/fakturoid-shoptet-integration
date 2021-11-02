ALTER TABLE sf_invoice_item ADD deleted_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
ALTER TABLE sf_proforma_invoice_item ADD deleted_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
ALTER TABLE sf_credit_note_item ADD deleted_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
