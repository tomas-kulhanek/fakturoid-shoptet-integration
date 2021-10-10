ALTER TABLE sf_credit_note
    ADD guid CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)';
CREATE UNIQUE INDEX UNIQ_4B4DB622B6FCFB2 ON sf_credit_note (guid);
ALTER TABLE sf_proforma_invoice
    ADD guid CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)';
CREATE UNIQUE INDEX UNIQ_73D7983D2B6FCFB2 ON sf_proforma_invoice (guid);
ALTER TABLE sf_invoice
    ADD guid CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)';
CREATE UNIQUE INDEX UNIQ_508287932B6FCFB2 ON sf_invoice (guid);
