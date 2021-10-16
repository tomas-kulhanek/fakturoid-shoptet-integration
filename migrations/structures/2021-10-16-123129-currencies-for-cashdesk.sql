DROP INDEX project_code ON sf_currency;
ALTER TABLE sf_currency ADD cashdesk TINYINT(1) DEFAULT '0' NOT NULL;
CREATE UNIQUE INDEX project_code_cashdesk ON sf_currency (project_id, code, cashdesk);
