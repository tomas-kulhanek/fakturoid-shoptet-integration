ALTER TABLE sf_invoice CHANGE accounting_updated_at accounting_updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
ALTER TABLE sf_credit_note CHANGE accounting_updated_at accounting_updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
ALTER TABLE sf_proforma_invoice CHANGE accounting_updated_at accounting_updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
