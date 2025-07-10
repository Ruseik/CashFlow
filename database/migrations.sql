-- Create database
CREATE DATABASE IF NOT EXISTS budget_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE budget_db;

-- Users table
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    primary_currency_id INT UNSIGNED,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Entities table
CREATE TABLE entities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    user_id INT UNSIGNED NOT NULL,
    show_in_basic_mode BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Currencies table
CREATE TABLE currencies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(3) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    symbol VARCHAR(5),
    show_in_basic_mode BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Update users table with foreign key
ALTER TABLE users
ADD FOREIGN KEY (primary_currency_id) REFERENCES currencies(id);

-- Purposes table
CREATE TABLE purposes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    user_id INT UNSIGNED NOT NULL,
    show_in_basic_mode BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Modes table
CREATE TABLE modes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    user_id INT UNSIGNED NOT NULL,
    show_in_basic_mode BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Transactions table
CREATE TABLE transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    start_entity_id INT UNSIGNED NOT NULL,
    dest_entity_id INT UNSIGNED NOT NULL,
    start_amount DECIMAL(15,8) NOT NULL,
    start_currency_id INT UNSIGNED NOT NULL,
    dest_amount DECIMAL(15,8) NOT NULL,
    dest_currency_id INT UNSIGNED NOT NULL,
    fee_entity_id INT UNSIGNED,
    fee_amount DECIMAL(15,8) DEFAULT 0.00,
    fee_currency_id INT UNSIGNED,
    date DATE NOT NULL,
    purpose_id INT UNSIGNED NOT NULL,
    mode_id INT UNSIGNED NOT NULL,
    remarks TEXT,
    grid_profit DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (start_entity_id) REFERENCES entities(id),
    FOREIGN KEY (dest_entity_id) REFERENCES entities(id),
    FOREIGN KEY (start_currency_id) REFERENCES currencies(id),
    FOREIGN KEY (dest_currency_id) REFERENCES currencies(id),
    FOREIGN KEY (fee_entity_id) REFERENCES entities(id),
    FOREIGN KEY (fee_currency_id) REFERENCES currencies(id),
    FOREIGN KEY (purpose_id) REFERENCES purposes(id),
    FOREIGN KEY (mode_id) REFERENCES modes(id)
) ENGINE=InnoDB;

-- Increase precision for start_amount and dest_amount
ALTER TABLE transactions
MODIFY start_amount DECIMAL(15,8) NOT NULL,
MODIFY dest_amount DECIMAL(15,8) NOT NULL,
MODIFY fee_amount DECIMAL(15,8) DEFAULT 0.00;

-- Insert default currency
INSERT INTO currencies (code, name, symbol) VALUES ('LKR', 'Sri Lankan Rupee', 'LKR');

-- Insert default Void entity (system entity)
INSERT INTO users (username, email, password, is_admin, primary_currency_id)
VALUES ('system', 'system@localhost', '$2y$10$abcdefghijklmnopqrstuv', TRUE, 1);

INSERT INTO entities (name, description, user_id, show_in_basic_mode)
VALUES ('Void', 'System entity for external transactions', 1, TRUE);

-- Insert default Purpose: Education
INSERT INTO purposes (name, description, user_id, show_in_basic_mode)
VALUES ('Education', 'Education-related expenses or income', 1, TRUE);

-- Insert default Mode: Travel
INSERT INTO modes (name, description, user_id, show_in_basic_mode)
VALUES ('Travel', 'Travel-related transactions', 1, TRUE);

INSERT INTO entities (name, description, user_id, show_in_basic_mode)
VALUES ('Pocket', 'System entity for Pocket in real world', 1, TRUE);

-- Modify currencies table to allow longer currency codes
ALTER TABLE currencies MODIFY code VARCHAR(5) NOT NULL;