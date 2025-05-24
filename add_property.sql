CREATE TABLE addproperty(
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id VARCHAR(50) NOT NULL UNIQUE,
    owner_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    property_type VARCHAR(100) NOT NULL,
    location TEXT NOT NULL,
    land_area DECIMAL(10,2) NOT NULL,
    images VARCHAR(255) NOT NULL,
    price_range VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
