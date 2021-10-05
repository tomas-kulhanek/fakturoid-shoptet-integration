ALTER TABLE sf_projects
    ADD last_order_sync_at    DATETIME NULL COMMENT '(DC2Type:datetime_immutable)',
    ADD last_invoice_sync_at  DATETIME NULL COMMENT '(DC2Type:datetime_immutable)',
    ADD last_proforma_sync_at DATETIME NULL COMMENT '(DC2Type:datetime_immutable)';

UPDATE sf_projects
SET last_order_sync_at    = '2021-09-05T12:00:00',
    last_invoice_sync_at  = '2021-09-05T12:00:00',
    last_proforma_sync_at = '2021-09-05T12:00:00';
