CREATE TABLE admin_activity (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    UserName VARCHAR(120) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    Password VARCHAR(255) NOT NULL,
    LastLogin TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO admin_activity (UserName, Email, Password) 
VALUES ('yash', 'adminyash@gmail.com', MD5('yash123'));
