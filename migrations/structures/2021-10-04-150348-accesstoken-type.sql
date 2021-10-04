ALTER TABLE sf_projects CHANGE access_token access_token LONGTEXT NOT NULL;
ALTER TABLE sf_project_setting CHANGE fakturoid_api_key fakturoid_api_key LONGTEXT DEFAULT NULL;
