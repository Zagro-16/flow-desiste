USE formaflow;

-- Password demo aggiornata: Password123!
UPDATE users
SET password_hash = '$2y$12$eMBbhnLmYxQBLOGRoiDWbeFM/MaUhr30mlinRgELpDm4svo3gjVmG'
WHERE email IN ('admin@formaflow.local', 'docente@formaflow.local', 'corsista@formaflow.local');
