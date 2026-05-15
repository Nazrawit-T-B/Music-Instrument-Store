-- this is the schema used to intialize the database for the music store
-- this code can be just copy pasted to phpMyAdmin or used on a local MySQL Server

CREATE DATABASE IF NOT EXISTS store;
USE store;

CREATE TABLE IF NOT EXISTS categories(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR (100) NOT NULL
);

CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
);

CREATE TABLE IF NOT EXISTS products(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    brand VARCHAR(100),
    category_id INT,
    price DECIMAL(10,2) NOT NULL,
    descri   TEXT,
    im VARCHAR(255),
    stock INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS cart_items(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(100),
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS orders( 
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT            NOT NULL,
    total      DECIMAL(10,2)  NOT NULL,
    status     ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT           NOT NULL,
    product_id INT           NOT NULL,
    quantity   INT           NOT NULL,
    price      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO categories (id,name) VALUES (1, 'Acoustic Guitars'),
(2, 'Electric Guitars'),
(3, 'Bass Guitars'),
(4, 'Grand Pianos'),
(5, 'Upright Pianos'),
(6, 'Rental Speakers');

INSERT INTO products (name, category_id, price, image, stock) VALUES

-- Acoustic Guitars (category 1)
('Martin D-28',              1,  2899.00, 'martinD28official.png',       50),
('Taylor 814ce',             1,  3999.00, 'Taylor814ce.png',             50),
('Yamaha FG800',             1,   569.99, 'YamahaFG800.png',            50),
('Gibson Hummingbird',       1,  3999.00, 'GibsonHummingbirdStandard.png',   50),
('Fender CD-60S',            1,   209.99, 'FenderCD60S.png',            50),

-- Electric Guitars (category 2)
('Gibson Les Paul Custom',        2,  2799.00, 'GibsonLesPaulCustom.png',      50),
('Gibson EDS-1275 Double Neck',   2,  7999.00, 'GibsonEDS1275DoubleNeck.png',    50),
('Gretsch White Falcon',          2,  4190.00, 'GretschWhiteFalcon.png',       50),
('B.C. Rich Mockingbird',         2,   1499.99, 'B.C.RichMockingbird.png',    50),
('Ibanez RG750',                  2,  1204.00, 'Ibanez RG750 .png',            50),

-- Bass Guitars (category 3)
('Fender Precision Bass',         3,  2799.00, 'fenderprecision.png',          50),
('Fender Jazz Bass',              3,  7999.00, 'fenderjazz.png',              50),
('Music Man StingRay',            3,  4190.00, 'musicman.png',                 50),
('Höfner 500/1 Violin Bass',      3,  1499.99, 'hofner500.png',                50),
('Rickenbacker 4003',             3,  1204.00, 'rickenbacker.png',             50),

-- Grand Pianos (category 4)
('Steinway & Sons Model D Concert Grand',  4, 2799.00, 'steinway.png',       50),
('Yamaha CFX Concert Grand',               4,  7999.00, 'yamaha.png',      50),
('Bösendorfer Imperial 290',               4, 4190.00, 'bosendorfer.png',    50),
('Kawai Shigeru K-Grand SK-EX',            4,  1499.99, 'kawai.png',          50),
('Fazioli F308 Concert Grand',             4, 1204.00, 'fazioli.png',        50),

-- Upright Pianos (category 5)
('Yamaha U3 Upright Piano',               5,  2799.00, 'yamahaupright.png',        50),
('Kawai K-300 Upright Piano',             5,  7999.00, 'kawaiK300.png',       50),
('Steinway & Sons Model K Upright',       5, 4190.00, 'steinwayupright.png', 50),
('Bösendorfer 130 Upright Piano',         5, 1499.99, 'bosendorferup.png',   50),
('Petrof P125 Upright Piano',             5, 1204.00, 'petrof.png',          50),

-- Rental Speakers (category 6)
('L-Acoustics',      6,  2899.00, 'Lacoustics.png',      30),
('Meyer Sound',      6,  2899.00, 'MeyerSound.png',      30),
('Martin Audio',     6,  2899.00, 'MartinAudio.png',     30),
('JBL Professional', 6,  2899.00, 'JBLprofessional.png', 30),
('D&B Audiotechnik', 6,  4499.00, 'dbaudiotechnik.png',  30);
