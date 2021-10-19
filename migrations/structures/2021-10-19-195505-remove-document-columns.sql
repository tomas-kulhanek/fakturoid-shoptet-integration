ALTER TABLE sf_credit_note DROP const_symbol, DROP spec_symbol, DROP external_system_id, DROP external_system_last_sync_at;
ALTER TABLE sf_proforma_invoice DROP const_symbol, DROP spec_symbol, DROP external_system_id, DROP external_system_last_sync_at;
ALTER TABLE sf_invoice DROP const_symbol, DROP spec_symbol, DROP external_system_id, DROP external_system_last_sync_at;
ALTER TABLE sf_customer ADD accounting_for_update TINYINT(1) NOT NULL;
