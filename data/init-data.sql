INSERT INTO users (id, email, name, roles, password_hash) VALUES
(
    X'018f4c3ed6b37f56bab2e93b7f1b7d08',
    'admin@example.com',
    'Admin User',
    '["ROLE_ADMIN"]',
    '$2y$13$p6Jtb3CqCKRP4FYeBs1fxO9Jxo1r1V5CYgq.RMGo.6hq1GJ5phoRC'
),
(
    X'01972b303b9272eb8e2aec57ee3901ad',
    'author.jakub@example.com',
    'Jakub User',
    '["ROLE_AUTHOR"]',
    '$2y$13$p6Jtb3CqCKRP4FYeBs1fxO9Jxo1r1V5CYgq.RMGo.6hq1GJ5phoRC'
),
(
    X'018f4c3ed6b37f56bab2e93b7f1b7d09',
    'user@example.com',
    'Regular User',
    '["ROLE_READER"]',
    '$2y$13$p6Jtb3CqCKRP4FYeBs1fxO9Jxo1r1V5CYgq.RMGo.6hq1GJ5phoRC'
);

-- # articles

INSERT INTO articles (id, title, content, author_id, created_at, updated_at) VALUES
(
    X'01972b316fc57f5cb98aa165d871d351',
    'První článek',
    'Toto je obsah prvního článku.',
    X'018f4c3ed6b37f56bab2e93b7f1b7d08', -- author: admin@example.com
    '2025-06-01 10:00:00.000000',
    NULL
),
(
    X'01972b31a1eb7004beb86d7c986e10f6',
    'Druhý článek',
    'Obsah druhého článku, napsal Jakub.',
    X'01972b303b9272eb8e2aec57ee3901ad', -- author: author.jakub@example.com
    '2025-06-01 12:00:00.000000',
    '2025-06-01 13:00:00.000000'
),
(
    X'01972b31cb8d751087c0d0ca148bcc6f',
    'Článek o Symfony',
    'Symfony je skvělý PHP framework pro vývoj aplikací.',
    X'01972b31a1eb7004beb86d7c986e10f6', -- author: author.jakub@example.com
    '2025-06-01 14:00:00.000000',
    NULL
);
