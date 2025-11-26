-- SQL schema for user_photos table in controle_estoque schema
CREATE TABLE IF NOT EXISTS controle_estoque.user_photos (
    user_id INT NOT NULL PRIMARY KEY,
    photo LONGBLOB NOT NULL,
    mime_type VARCHAR(50) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_photos_user FOREIGN KEY (user_id) REFERENCES controle_estoque.users(id) ON DELETE CASCADE
);
