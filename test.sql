CREATE TABLE admin(
        id INT  PRIMARY KEY,  
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);
CREATE TABLE allowed_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(50) NOT NULL UNIQUE
);
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(50) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE attendance ADD dars VARCHAR(255);

CREATE TABLE admin_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL
);
INSERT INTO allowed_students (student_code)
VALUES
('40110001'),
('40110002'),
('40110003'); 
