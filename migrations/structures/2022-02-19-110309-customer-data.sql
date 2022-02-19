ALTER TABLE sf_customer_delivery_address CHANGE country_code country_code VARCHAR(255) DEFAULT 'CZ' NOT NULL;
ALTER TABLE sf_customer_billing_address CHANGE country_code country_code VARCHAR(255) DEFAULT 'CZ' NOT NULL;
