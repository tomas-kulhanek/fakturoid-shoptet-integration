ALTER TABLE sf_credit_note ADD deleted_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
ALTER TABLE sf_proforma_invoice ADD deleted_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
ALTER TABLE sf_order ADD deleted_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
ALTER TABLE sf_invoice ADD deleted_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
