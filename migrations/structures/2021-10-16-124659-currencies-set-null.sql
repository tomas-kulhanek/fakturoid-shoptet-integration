ALTER TABLE sf_currency
    DROP FOREIGN KEY FK_712128D812CB990C;
ALTER TABLE sf_currency
    ADD CONSTRAINT FK_712128D812CB990C FOREIGN KEY (bank_account_id) REFERENCES ac_bank_account (id) ON DELETE SET NULL;