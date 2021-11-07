ALTER TABLE sf_invoice ADD accounting_updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
ALTER TABLE sf_credit_note ADD accounting_updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
ALTER TABLE sf_proforma_invoice ADD accounting_updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
