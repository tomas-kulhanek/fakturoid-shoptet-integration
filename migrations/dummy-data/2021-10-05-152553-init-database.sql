INSERT INTO `core_user` (`id`, `email`, `created_at`, `guid`)
VALUES (1, 'jsem@tomaskulhanek.cz', '2021-10-04 09:24:43', '8de36e61-28f8-4e10-bfe9-a0438b821d1b'),
       (2, 'kulhanek@shoptet.cz', '2021-10-04 09:21:16', 'e8e5289f-0d87-444b-a5ce-ba09234c4810'),
       (3, 'veravrsecka@seznam.cz', '2021-10-04 09:21:16', 'f3ca0509-9aec-44bf-8d8a-132830ed59a9');

INSERT INTO `sf_projects` (`id`, `owner_id`, `access_token`, `token_type`, `scope`, `eshop_id`, `eshop_url`,
                           `contact_email`, `guid`, `created_at`)
VALUES (1, 1,
        'emnkdkkguxcupkp56phxr3wl5haps1svfi0o8t5zo0b2hl4ad73njhq6pwu9kntrpds4mh37d6qgo0gfbfetw9l4fa1xaljvrmbuto5unx2206lia6w1bjsp4hzmxx0mkybro78v06gcoyf3gffhomy1zoou9ehgs0g4vkyl3hlgqfi81940ix76cigpjfew4n2fyi8tyhvlda9mhbm9hqttcim5ba1t5cnswe0f6vb8kmqiw9unazpqrej94ul',
        'bearer', 'api', 470424, 'https://shoptet.helppc.cz/', 'jsem@tomaskulhanek.cz',
        'b7edca14-0374-484d-9fba-64a4c5d54445', '2021-10-04 09:24:43');

INSERT INTO `sf_users_projects` (`project_id`, `user_id`)
VALUES (1, 1),
       (1, 2),
       (1, 3);
