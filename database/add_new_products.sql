-- Run this to add the 10 new bread products to your database
-- Usage: mysql -u root -p harvest_bread_db < database/add_new_products.sql

USE harvest_bread_db;

INSERT INTO products (name, description, price, image, stock, is_active) VALUES
    ('Andaluz Bread',          'A rustic Spanish-style loaf with a golden crust, perfect for slicing.',                         6.50,  'Andaluz_Bread.webp',          25, 1),
    ('Baker\'s Rolls',         'Classic oval rolls with a crisp crust and soft interior. Great for sandwiches.',                3.80,  'Bakers_Rolls.webp',           40, 1),
    ('German Country Loaf',    'Hearty sourdough country loaf with a cracked flour-dusted crust.',                              7.20,  'German_Country_Loaf.webp',    20, 1),
    ('Low-Carb Power Rolls',   'Dense, seed-covered rolls packed with nutrients — ideal for health-conscious eaters.',          5.90,  'Low_Carb_Power_Rolls.webp',   30, 1),
    ('Olive Ciabattini',       'Light ciabatta rolls studded with whole olives. Pairs beautifully with cheese or charcuterie.', 5.50,  'Olive_Ciabattini.webp',       35, 1),
    ('Potato Carrot Rolls',    'Soft seed-topped rolls made with potato and carrot for a naturally moist crumb.',               4.90,  'Potato_Carrot_Rolls.webp',    30, 1),
    ('Premium Seed Sourdough', 'Oval sourdough loaf generously coated in sunflower, flax, and pumpkin seeds.',                  8.50,  'Premium_Seed_Sourdough.webp', 15, 1),
    ('Pretzel Sandwich Rolls', 'Golden pretzel-glazed rolls with a distinctive cracked top. Perfect for hearty sandwiches.',    4.50,  'Pretzel_Sandwich_Rolls.webp', 40, 1),
    ('Pumpkin Seed Loaf',      'Oval loaf with a generous topping of roasted pumpkin seeds and a hearty whole-grain crumb.',    7.80,  'Pumpkin_Seed.webp',           20, 1),
    ('Rye Crust Sourdough',    'Traditional round rye sourdough with a thick floury crust and deep, earthy flavor.',            7.00,  'Rye_Crust_Sourdough.webp',    20, 1)
ON DUPLICATE KEY UPDATE name = name;

SELECT id, name, image FROM products ORDER BY id DESC LIMIT 12;
