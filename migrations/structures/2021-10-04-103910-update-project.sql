UPDATE sf_projects SET revoked = false;
ALTER TABLE sf_projects CHANGE revoked revoked TINYINT(1) NOT NULL;
