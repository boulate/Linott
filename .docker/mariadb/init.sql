-- Linott Database Initialization
-- This file is executed when the MariaDB container is first created

-- Ensure the database exists (already created by MYSQL_DATABASE env var)
-- Additional initialization can be added here if needed

-- Set character set
ALTER DATABASE linott CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant all privileges to the linott user
GRANT ALL PRIVILEGES ON linott.* TO 'linott'@'%';
FLUSH PRIVILEGES;
