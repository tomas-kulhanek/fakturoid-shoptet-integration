ALTER TABLE sf_invoice ADD issue_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
ALTER TABLE sf_credit_note ADD issue_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
ALTER TABLE sf_proforma_invoice ADD issue_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)';
