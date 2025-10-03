BEGIN;

-- 0) Activer pgcrypto pour bcrypt() si besoin
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- 1) USERS (email = unique) + hash bcrypt
WITH u(email, roles, pwd, username, photo) AS (
    VALUES
        ('vanessa13@example.com', '["ROLE_USER"]'::json, crypt('vanessa13', gen_salt('bf')),  'Vanessa13', 'user1.png'),
        ('carla17@example.com',   '["ROLE_USER"]'::json, crypt('carla178',  gen_salt('bf')),  'Carla17',   'user2.png'),
        ('andre4125@example.com', '["ROLE_USER"]'::json, crypt('andre4125', gen_salt('bf')),  'Andre4125', 'user3.png'),
        ('admin@ecoride.com',     '["ROLE_ADMIN"]'::json,  crypt('admin321',   gen_salt('bf')), 'Admin',     NULL),
        ('employee@ecoride.com',  '["ROLE_EMPLOYEE"]'::json, crypt('employee321', gen_salt('bf')), 'Employé 1', NULL)
)
INSERT INTO "user"(email, roles, password, username, photo)
SELECT email, roles, pwd, username, photo FROM u
ON CONFLICT (email) DO UPDATE SET
  roles    = EXCLUDED.roles,
  username = EXCLUDED.username,
  photo    = EXCLUDED.photo,
  -- ne réécrit pas le mot de passe si déjà présent
  password = CASE
               WHEN "user".password IS NULL OR "user".password = ''
               THEN EXCLUDED.password
               ELSE "user".password
             END;

-- 1b) (Optionnel) role_type si la colonne existe
DO $$
BEGIN
  IF EXISTS (
    SELECT 1
    FROM information_schema.columns
    WHERE table_name='user' AND column_name='role_type'
  ) THEN
    UPDATE "user"
    SET role_type = '["chauffeur_passager"]'::json
    WHERE email IN ('vanessa13@example.com','carla17@example.com','andre4125@example.com');
  END IF;
END $$;

-- 2) CARS (idempotent via WHERE NOT EXISTS)
INSERT INTO car (users_id, registration, date_first_registration, model, color, mark, energy)
SELECT (SELECT id FROM "user" WHERE email='vanessa13@example.com'),
       '1234ABCD','2000-01-01','Clio','Bleu','Renault', FALSE
WHERE NOT EXISTS (SELECT 1 FROM car WHERE registration='1234ABCD');

INSERT INTO car (users_id, registration, date_first_registration, model, color, mark, energy)
SELECT (SELECT id FROM "user" WHERE email='carla17@example.com'),
       'F58746CD','2012-07-15','PROACE','noir','Toyota', TRUE
WHERE NOT EXISTS (SELECT 1 FROM car WHERE registration='F58746CD');

INSERT INTO car (users_id, registration, date_first_registration, model, color, mark, energy)
SELECT (SELECT id FROM "user" WHERE email='andre4125@example.com'),
       'D956GR56','2022-01-01','E-208','gris','peugeot', TRUE
WHERE NOT EXISTS (SELECT 1 FROM car WHERE registration='D956GR56');

-- 3) CARPOOLINGS (clé naturelle = driver + dep/arr + date)
INSERT INTO carpooling
(cars_id, users_id, departure_address, arrival_address, departure_date, arrival_date, departure_time, arrival_time, price, number_seats, status, preference)
SELECT
  (SELECT id FROM car    WHERE registration='1234ABCD'),
  (SELECT id FROM "user" WHERE email='vanessa13@example.com'),
  'Paris','Lyon','2025-12-01','2025-12-01','08:00:00','13:00:00',50,3,'ouvert','non fumeur'
WHERE NOT EXISTS (
  SELECT 1 FROM carpooling
  WHERE users_id=(SELECT id FROM "user" WHERE email='vanessa13@example.com')
    AND departure_address='Paris' AND arrival_address='Lyon' AND departure_date='2025-12-01'
);

INSERT INTO carpooling
(cars_id, users_id, departure_address, arrival_address, departure_date, arrival_date, departure_time, arrival_time, price, number_seats, status, preference)
SELECT
  (SELECT id FROM car    WHERE registration='F58746CD'),
  (SELECT id FROM "user" WHERE email='carla17@example.com'),
  'Paris','Rennes','2025-12-01','2025-12-01','08:30:00','12:00:00',30,2,'fermé','animaux acceptés'
WHERE NOT EXISTS (
  SELECT 1 FROM carpooling
  WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
    AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01'
);

INSERT INTO carpooling
(cars_id, users_id, departure_address, arrival_address, departure_date, arrival_date, departure_time, arrival_time, price, number_seats, status, preference)
SELECT
  (SELECT id FROM car    WHERE registration='D956GR56'),
  (SELECT id FROM "user" WHERE email='andre4125@example.com'),
  'Paris','Marseille','2026-01-01','2026-01-01','08:30:00','15:30:00',80,2,'ouvert','non fumeurs, animaux acceptés'
WHERE NOT EXISTS (
  SELECT 1 FROM carpooling
  WHERE users_id=(SELECT id FROM "user" WHERE email='andre4125@example.com')
    AND departure_address='Paris' AND arrival_address='Marseille' AND departure_date='2026-01-01'
);

-- 4) CREDITS (idempotent par (user, date))
INSERT INTO credit (users_id, balance, transaction_date)
SELECT (SELECT id FROM "user" WHERE email='vanessa13@example.com'), 20, '2025-11-01'
WHERE NOT EXISTS (
  SELECT 1 FROM credit
  WHERE users_id=(SELECT id FROM "user" WHERE email='vanessa13@example.com')
    AND transaction_date='2025-11-01'
);

INSERT INTO credit (users_id, balance, transaction_date)
SELECT (SELECT id FROM "user" WHERE email='carla17@example.com'), 16, '2025-11-01'
WHERE NOT EXISTS (
  SELECT 1 FROM credit
  WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
    AND transaction_date='2025-11-01'
);

INSERT INTO credit (users_id, balance, transaction_date)
SELECT (SELECT id FROM "user" WHERE email='andre4125@example.com'), 10, '2025-11-01'
WHERE NOT EXISTS (
  SELECT 1 FROM credit
  WHERE users_id=(SELECT id FROM "user" WHERE email='andre4125@example.com')
    AND transaction_date='2025-11-01'
);

-- 5) PASSAGERS (gère carpooling_passengers)
DO $$
BEGIN
  IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name='carpooling_passengers') THEN
    -- cp1: Vanessa (driver) -> passenger Carla
    INSERT INTO carpooling_passengers(carpooling_id, user_id)
    SELECT
      (SELECT id FROM carpooling
         WHERE users_id=(SELECT id FROM "user" WHERE email='vanessa13@example.com')
           AND departure_address='Paris' AND arrival_address='Lyon' AND departure_date='2025-12-01'),
      (SELECT id FROM "user" WHERE email='carla17@example.com')
    WHERE NOT EXISTS (
      SELECT 1 FROM carpooling_passengers
      WHERE carpooling_id = (SELECT id FROM carpooling
                               WHERE users_id=(SELECT id FROM "user" WHERE email='vanessa13@example.com')
                                 AND departure_address='Paris' AND arrival_address='Lyon' AND departure_date='2025-12-01')
        AND user_id       = (SELECT id FROM "user" WHERE email='carla17@example.com')
    );

    -- cp2: Carla (driver) -> passenger André
    INSERT INTO carpooling_passengers(carpooling_id, user_id)
    SELECT
      (SELECT id FROM carpooling
         WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
           AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01'),
      (SELECT id FROM "user" WHERE email='andre4125@example.com')
    WHERE NOT EXISTS (
      SELECT 1 FROM carpooling_passengers
      WHERE carpooling_id = (SELECT id FROM carpooling
                               WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
                                 AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01')
        AND user_id       = (SELECT id FROM "user" WHERE email='andre4125@example.com')
    );

    -- cp2: Carla (driver) -> passenger Vanessa
    INSERT INTO carpooling_passengers(carpooling_id, user_id)
    SELECT
      (SELECT id FROM carpooling
         WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
           AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01'),
      (SELECT id FROM "user" WHERE email='vanessa13@example.com')
    WHERE NOT EXISTS (
      SELECT 1 FROM carpooling_passengers
      WHERE carpooling_id = (SELECT id FROM carpooling
                               WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
                                 AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01')
        AND user_id       = (SELECT id FROM "user" WHERE email='vanessa13@example.com')
    );

    -- cp3: André (driver) -> passenger Vanessa  ⬅️ (ajouté)
    INSERT INTO carpooling_passengers(carpooling_id, user_id)
    SELECT
      (SELECT id FROM carpooling
         WHERE users_id=(SELECT id FROM "user" WHERE email='andre4125@example.com')
           AND departure_address='Paris' AND arrival_address='Marseille' AND departure_date='2026-01-01'),
      (SELECT id FROM "user" WHERE email='vanessa13@example.com')
    WHERE NOT EXISTS (
      SELECT 1 FROM carpooling_passengers
      WHERE carpooling_id = (SELECT id FROM carpooling
                               WHERE users_id=(SELECT id FROM "user" WHERE email='andre4125@example.com')
                                 AND departure_address='Paris' AND arrival_address='Marseille' AND departure_date='2026-01-01')
        AND user_id       = (SELECT id FROM "user" WHERE email='vanessa13@example.com')
    );

  ELSIF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name='carpooling_user') THEN
    -- cp1
    INSERT INTO carpooling_user(carpooling_id, user_id)
    SELECT
      (SELECT id FROM carpooling
         WHERE users_id=(SELECT id FROM "user" WHERE email='vanessa13@example.com')
           AND departure_address='Paris' AND arrival_address='Lyon' AND departure_date='2025-12-01'),
      (SELECT id FROM "user" WHERE email='carla17@example.com')
    WHERE NOT EXISTS (
      SELECT 1 FROM carpooling_user
      WHERE carpooling_id = (SELECT id FROM carpooling
                               WHERE users_id=(SELECT id FROM "user" WHERE email='vanessa13@example.com')
                                 AND departure_address='Paris' AND arrival_address='Lyon' AND departure_date='2025-12-01')
        AND user_id       = (SELECT id FROM "user" WHERE email='carla17@example.com')
    );

    -- cp2 (André)
    INSERT INTO carpooling_user(carpooling_id, user_id)
    SELECT
      (SELECT id FROM carpooling
         WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
           AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01'),
      (SELECT id FROM "user" WHERE email='andre4125@example.com')
    WHERE NOT EXISTS (
      SELECT 1 FROM carpooling_user
      WHERE carpooling_id = (SELECT id FROM carpooling
                               WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
                                 AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01')
        AND user_id       = (SELECT id FROM "user" WHERE email='andre4125@example.com')
    );

    -- cp2 (Vanessa)
    INSERT INTO carpooling_user(carpooling_id, user_id)
    SELECT
      (SELECT id FROM carpooling
         WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
           AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01'),
      (SELECT id FROM "user" WHERE email='vanessa13@example.com')
    WHERE NOT EXISTS (
      SELECT 1 FROM carpooling_user
      WHERE carpooling_id = (SELECT id FROM carpooling
                               WHERE users_id=(SELECT id FROM "user" WHERE email='carla17@example.com')
                                 AND departure_address='Paris' AND arrival_address='Rennes' AND departure_date='2025-12-01')
        AND user_id       = (SELECT id FROM "user" WHERE email='vanessa13@example.com')
    );

    -- cp3 (ajouté)
    INSERT INTO carpooling_user(carpooling_id, user_id)
    SELECT
      (SELECT id FROM carpooling
         WHERE users_id=(SELECT id FROM "user" WHERE email='andre4125@example.com')
           AND departure_address='Paris' AND arrival_address='Marseille' AND departure_date='2026-01-01'),
      (SELECT id FROM "user" WHERE email='vanessa13@example.com')
    WHERE NOT EXISTS (
      SELECT 1 FROM carpooling_user
      WHERE carpooling_id = (SELECT id FROM carpooling
                               WHERE users_id=(SELECT id FROM "user" WHERE email='andre4125@example.com')
                                 AND departure_address='Paris' AND arrival_address='Marseille' AND departure_date='2026-01-01')
        AND user_id       = (SELECT id FROM "user" WHERE email='vanessa13@example.com')
    );
  END IF;
END $$;

COMMIT;