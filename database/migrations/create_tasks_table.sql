-- Migration for tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assigned_at DATETIME,
    active_status BOOLEAN NOT NULL DEFAULT TRUE,
    successful_status BOOLEAN,
    successful_at DATETIME,
    delayed_status BOOLEAN,
    delayed_at DATETIME,
    failed_status BOOLEAN,
    failed_at DATETIME,
    created_user INT UNSIGNED NOT NULL,
    assigned_user INT UNSIGNED,
    transaction_id INT UNSIGNED,
    urgent_status BOOLEAN,
    important_status BOOLEAN,
    FOREIGN KEY (created_user) REFERENCES users(id),
    FOREIGN KEY (assigned_user) REFERENCES users(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);
