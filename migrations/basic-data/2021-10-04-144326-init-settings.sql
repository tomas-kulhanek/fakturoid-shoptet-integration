INSERT INTO sf_project_setting (project_id,
                                automatization) (SELECT id, 0 from sf_projects)
