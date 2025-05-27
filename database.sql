-- Crearea tabelului pentru încălțăminte
CREATE TABLE shoes (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    brand VARCHAR(50),
    occasion VARCHAR(50),
    season VARCHAR(20),
    style VARCHAR(30),
    rating DECIMAL(3,2),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crearea tabelului pentru statistici
CREATE TABLE statistics (
    id SERIAL PRIMARY KEY,
    user_id INTEGER,
    occasion VARCHAR(50),
    season VARCHAR(20),
    style VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserarea unor date de exemplu
INSERT INTO shoes (name, description, price, brand, occasion, season, style, rating, image_url) VALUES
('Oxford Classic', 'Pantofi oxford din piele naturală premium, perfecți pentru ocazii formale. Confecționați cu atenție la detalii, acești pantofi reprezintă alegerea ideală pentru ținute business și evenimente speciale.', 899.99, 'Allen Edmonds', 'Formal', 'All Season', 'Classic', 4.8, 'assets/shoes/oxford_classic.jpg'),
('Air Max 270', 'Sneakers moderni cu tehnologie Air pentru confort maxim. Design contemporan perfect pentru ținute casual și activități sportive.', 799.99, 'Nike', 'Casual', 'Summer', 'Sport', 4.7, 'assets/shoes/airmax_270.jpg'),
('Chelsea Boot Elite', 'Ghete chelsea din piele întoarsă, versatile și elegante. Perfecte pentru sezonul rece și ținute smart-casual.', 699.99, 'Loake', 'Smart Casual', 'Fall/Winter', 'Modern', 4.6, 'assets/shoes/chelsea_boot.jpg'),
('Loafer Comfort', 'Mocasini comozi din piele moale, ideali pentru zilele de vară. Design clasic ce se potrivește atât la ținute casual cât și smart-casual.', 599.99, 'Tod''s', 'Casual', 'Spring/Summer', 'Classic', 4.5, 'assets/shoes/loafer_comfort.jpg'),
('Derby Business', 'Pantofi derby eleganți pentru ținute business. Confecționați din piele de înaltă calitate cu accent pe confort și durabilitate.', 749.99, 'Church''s', 'Business', 'All Season', 'Classic', 4.7, 'assets/shoes/derby_business.jpg'); 