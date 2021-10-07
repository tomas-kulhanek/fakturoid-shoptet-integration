ALTER TABLE sf_order_item
    CHANGE unit_price_without_vat unit_price_without_vat DOUBLE PRECISION DEFAULT NULL;
ALTER TABLE sf_project_setting
    CHANGE automatization automatization INT NOT NULL;
