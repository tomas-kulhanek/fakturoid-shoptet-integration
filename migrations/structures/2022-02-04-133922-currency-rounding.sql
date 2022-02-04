ALTER TABLE sf_currency ADD rounding VARCHAR(255) NULL;
UPDATE sf_currency SET rounding = 'up';
ALTER TABLE sf_currency CHANGE rounding rounding VARCHAR(255) NOT NULL;

