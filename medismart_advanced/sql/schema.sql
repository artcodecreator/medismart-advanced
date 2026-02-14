-- Advanced MediSmart schema
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(50) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  email_verified TINYINT(1) DEFAULT 0,
  verify_token VARCHAR(64),
  twofa_enabled TINYINT(1) DEFAULT 0,
  twofa_last_otp_hash VARCHAR(255),
  twofa_otp_expires DATETIME,
  cnic CHAR(13),
  study_program VARCHAR(20),
  about_me TEXT,
  display_picture VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('SUPER','SUPPORT','ANALYST') DEFAULT 'SUPER',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  category VARCHAR(80) NOT NULL,
  brand VARCHAR(80) NOT NULL,
  symptoms VARCHAR(255) DEFAULT '',
  requires_prescription TINYINT(1) DEFAULT 0,
  price DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 100,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('PLACED','ON_HOLD','APPROVED','REJECTED','PAID','SHIPPED','DELIVERED') DEFAULT 'PLACED',
  payment_method ENUM('COD','CARD') DEFAULT 'COD',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS prescriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  status ENUM('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  subject VARCHAR(200) NOT NULL,
  status ENUM('OPEN','CLOSED') DEFAULT 'OPEN',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  sender ENUM('USER','ADMIN') NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS login_events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  ip VARCHAR(64),
  user_agent VARCHAR(255),
  success TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  success TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed admin (password: Admin@123)
INSERT IGNORE INTO admins (id, username, password_hash, role) VALUES
(1, 'admin', '$2y$10$0f2M8R6lT3x0W0hQ8r5.6u5wE2ZzHcQe5G2kq1bVxj0i2m4XJgG8m', 'SUPER');

-- Seed products
INSERT INTO products (name, category, brand, symptoms, requires_prescription, price, stock) VALUES
('Panadol', 'Pain Reliever', 'GSK', 'fever,headache', 0, 50.00, 500),
('Augmentin', 'Antibiotic', 'GSK', 'infection', 1, 220.00, 200),
('Calpol', 'Fever Relief', 'GSK', 'fever', 0, 45.00, 500),
('Disprin', 'Headache', 'Bayer', 'headache', 0, 30.00, 400),
('Zyrtec', 'Allergy Relief', 'UCB', 'allergy,sneezing,itching', 0, 60.00, 300),
('Azomax', 'Antibiotic', 'Pfizer', 'throat,infection', 1, 350.00, 150),
('ORS', 'Electrolyte', 'WHO', 'dehydration,diarrhea', 0, 40.00, 600);
-- Alter orders table to store shipping fields.
ALTER TABLE orders
  ADD shipping_name   VARCHAR(120)  NULL,
  ADD shipping_phone  VARCHAR(20)   NULL,
  ADD shipping_addr1  VARCHAR(200)  NULL,
  ADD shipping_addr2  VARCHAR(200)  NULL,
  ADD shipping_city   VARCHAR(80)   NULL,
  ADD shipping_postal VARCHAR(20)   NULL;
