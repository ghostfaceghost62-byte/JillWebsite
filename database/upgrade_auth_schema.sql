-- Non-destructive upgrade for an existing hotelreservation_db installation.
-- Run this once in phpMyAdmin when upgrading an older copy of the project.
ALTER TABLE users
    MODIFY status ENUM('PENDING_VERIFICATION','ACTIVE','SUSPENDED','DEACTIVATED') NOT NULL DEFAULT 'PENDING_VERIFICATION',
    ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN verified_at DATETIME NULL,
    ADD COLUMN last_login DATETIME NULL;

UPDATE users SET status = 'ACTIVE', email_verified = 1, verified_at = NOW() WHERE role = 'ADMIN';

CREATE TABLE email_verifications(
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    verification_code_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    verified_at DATETIME NULL,
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(user_id),
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE password_resets(
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(user_id),
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE login_attempts(
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(160) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(ip_address,attempted_at),
    INDEX(email,attempted_at)
);
