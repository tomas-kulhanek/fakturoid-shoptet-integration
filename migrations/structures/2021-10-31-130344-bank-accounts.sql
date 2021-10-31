ALTER TABLE ac_bank_account
    ADD `system` TINYINT(1) DEFAULT '0' NOT NULL, CHANGE accounting_id accounting_id INT DEFAULT NULL, CHANGE currency currency VARCHAR(255) DEFAULT NULL;
