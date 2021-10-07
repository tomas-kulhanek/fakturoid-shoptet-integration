UPDATE
    core_user
        INNER JOIN sf_users_projects ON core_user.id = sf_users_projects.user_id
SET core_user.project_id = sf_users_projects.project_id,
    core_user.`role`     = 'user';


UPDATE
    core_user
        INNER JOIN sf_projects
        ON core_user.id = sf_projects.owner_id
SET core_user.`role` = 'owner';
