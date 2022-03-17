ALTER TABLE sf_project_setting
    ADD accounting_credit_note_number_line_id INT DEFAULT NULL;
UPDATE sf_customer_delivery_address
set country_code = 'CZ'
WHERE country_code = ''
   OR country_code IS NULL;
UPDATE sf_customer_billing_address
set country_code = 'CZ'
WHERE country_code = ''
   OR country_code IS NULL;
ALTER TABLE sf_customer_delivery_address
    CHANGE country_code country_code VARCHAR(255) DEFAULT 'CZ' NOT NULL;
ALTER TABLE sf_customer_billing_address
    CHANGE country_code country_code VARCHAR(255) DEFAULT 'CZ' NOT NULL;
ALTER TABLE sf_action_log
    ADD credit_note_id INT UNSIGNED DEFAULT NULL;
ALTER TABLE sf_action_log
    ADD CONSTRAINT FK_752C28311C696F7A FOREIGN KEY (credit_note_id) REFERENCES sf_credit_note (id) ON DELETE CASCADE;
CREATE INDEX IDX_752C28311C696F7A ON sf_action_log (credit_note_id);
