ALTER TABLE sf_credit_note CHANGE accounting_public_token accounting_public_html_url VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_proforma_invoice CHANGE accounting_public_token accounting_public_html_url VARCHAR(255) DEFAULT NULL;
ALTER TABLE sf_invoice CHANGE accounting_public_token accounting_public_html_url VARCHAR(255) DEFAULT NULL;
