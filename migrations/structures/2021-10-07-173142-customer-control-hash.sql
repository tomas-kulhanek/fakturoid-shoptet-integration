ALTER TABLE sf_customer
    ADD control_hash VARCHAR(255) NOT NULL,
    ADD guid         CHAR(36)     NOT NULL COMMENT '(DC2Type:uuid)',
    CHANGE shoptet_guid shoptet_guid VARCHAR(255) DEFAULT NULL;

CREATE UNIQUE INDEX UNIQ_994E2EEE2B6FCFB2 ON sf_customer (guid);

DROP TABLE sf_users_projects;
