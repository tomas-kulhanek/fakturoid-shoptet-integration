ALTER TABLE sf_credit_note ADD main_with_vat DOUBLE PRECISION DEFAULT NULL, ADD main_without_vat DOUBLE PRECISION DEFAULT NULL;
ALTER TABLE sf_proforma_invoice ADD main_with_vat DOUBLE PRECISION DEFAULT NULL, ADD main_without_vat DOUBLE PRECISION DEFAULT NULL;
ALTER TABLE sf_invoice ADD main_with_vat DOUBLE PRECISION DEFAULT NULL, ADD main_without_vat DOUBLE PRECISION DEFAULT NULL;
