ALTER TABLE sf_projects
    DROP FOREIGN KEY FK_44E413437E3C61F9;
DROP INDEX IDX_44E413437E3C61F9 ON sf_projects;
ALTER TABLE sf_projects
    DROP owner_id;
ALTER TABLE core_user
    CHANGE project_id project_id INT NOT NULL;
