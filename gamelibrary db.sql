CREATE DATABASE IF NOT EXISTS gamelibrary;
USE gamelibrary;

-- Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Games (global catalog)
CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    release_date DATE,
    cover_image VARCHAR(255) DEFAULT 'default.jpg',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Categories (e.g., RPG, FPS, Adventure)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE
);

-- Platforms (e.g., PC, PlayStation, Xbox)
CREATE TABLE platforms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE
);

-- Game <-> Category (M:N)
CREATE TABLE game_categories (
    game_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (game_id, category_id),
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Game <-> Platform (M:N)
CREATE TABLE game_platforms (
    game_id INT NOT NULL,
    platform_id INT NOT NULL,
    PRIMARY KEY (game_id, platform_id),
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (platform_id) REFERENCES platforms(id) ON DELETE CASCADE
);

-- User’s personal library entries
CREATE TABLE user_games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,platforms
    game_id INT NOT NULL,
    status ENUM('want_to_play','playing','finished','dropped') NOT NULL DEFAULT 'want_to_play',
    rating TINYINT CHECK (rating >= 1 AND rating <= 10),
    notes TEXT,
    date_added DATE DEFAULT (CURRENT_DATE),
    date_completed DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_game (user_id, game_id)
);

INSERT INTO categories (name, slug) VALUES ('RPG','rpg'), ('Action','action'), ('Adventure','adventure');
INSERT INTO platforms (name, slug) VALUES ('PC','pc'), ('PlayStation 5','ps5'), ('Xbox Series X','xbox');
-- Add a couple of games manually later via the website.