-- Select the target database in phpMyAdmin before importing this file.
-- Hostinger does not allow applications to create or switch databases here.

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  slug VARCHAR(140) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS menus (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT NOT NULL,
  price INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  display_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_menus_category FOREIGN KEY (category_id) REFERENCES categories(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uq_menus_category_name (category_id, name),
  INDEX idx_menus_active_order (is_active, display_order)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(32) NOT NULL UNIQUE,
  customer_name VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  whatsapp VARCHAR(40) NOT NULL,
  recovery_mode ENUM('delivery', 'takeaway', 'onsite') NOT NULL,
  address VARCHAR(255) NULL,
  district VARCHAR(120) NULL,
  people_count VARCHAR(20) NULL,
  requested_time TIME NULL,
  instructions TEXT NULL,
  total_amount INT UNSIGNED NOT NULL,
  status ENUM('pending', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_orders_status_date (status, created_at),
  INDEX idx_orders_search (order_number, customer_name, phone)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  menu_id INT UNSIGNED NOT NULL,
  menu_name VARCHAR(160) NOT NULL,
  unit_price INT UNSIGNED NOT NULL,
  quantity SMALLINT UNSIGNED NOT NULL,
  subtotal INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_order_items_menu FOREIGN KEY (menu_id) REFERENCES menus(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (name, slug) VALUES
  ('Plats', 'plat'),
  ('Boissons', 'boisson');

INSERT IGNORE INTO menus
  (category_id, name, description, price, image_path, is_active, display_order)
SELECT c.id, seed.name, seed.description, seed.price, seed.image_path, 1, seed.display_order
FROM categories c
JOIN (
  SELECT 'plat' slug, 'Dakéré' name, 'Notre plat signature : du mil savamment préparé avec une sauce riche et épicée.' description, 1000 price, 'SRD_VVIP/lait_dakere.jpg' image_path, 10 display_order
  UNION ALL SELECT 'plat', 'Riz Sénégalais', 'Riz parfumé cuisiné à la sénégalaise, savoureux et généreux.', 1000, 'SRD_VVIP/riz_sénégalais.jpg', 20
  UNION ALL SELECT 'plat', 'Riz Blanc Parfumé', 'Riz blanc de qualité, parfumé et accompagné de sauces maison.', 1000, 'SRD_VVIP/riz_parfume.jpg', 30
  UNION ALL SELECT 'plat', 'Spaghetti Macaronis', 'Pâtes savoureuses préparées avec une sauce maison généreuse.', 1000, 'SRD_VVIP/spaghettis_macaronis.jpg', 40
  UNION ALL SELECT 'plat', 'Pommes Plantain Viande', 'Plantains dorés accompagnés d’une viande tendre et bien assaisonnée.', 1000, 'SRD_VVIP/pommes_plantain_viande.jpg', 50
  UNION ALL SELECT 'plat', 'Poulet', 'Poulet grillé ou braisé, mariné aux épices africaines et cuit à la perfection.', 3000, 'SRD_VVIP/poulet.jpg', 60
  UNION ALL SELECT 'plat', 'Frites', 'Frites croustillantes et dorées, parfaites en accompagnement.', 500, 'SRD_VVIP/frites.jpg', 70
  UNION ALL SELECT 'plat', 'Eru', 'Plat traditionnel camerounais aux légumes et viandes fumées, riche en saveurs.', 1500, 'SRD_VVIP/eru.jpg', 80
  UNION ALL SELECT 'plat', 'Couscous', 'Couscous moelleux accompagné d’une sauce généreuse et de légumes.', 2000, 'SRD_VVIP/couscous.jpg', 90
  UNION ALL SELECT 'plat', 'Shawarma', 'Shawarma généreux garni de viande tendre, légumes frais et sauces maison.', 1000, 'SRD_VVIP/shawarma.jpg', 100
  UNION ALL SELECT 'boisson', 'Boissons', 'Large choix de boissons fraîches, jus naturels et sodas.', 1000, 'SRD_VVIP/boissons.jpg', 110
  UNION ALL SELECT 'boisson', 'Eau Minérale', 'Eau minérale pure et fraîche pour accompagner votre repas.', 500, 'SRD_VVIP/eau_pure.jpg', 120
) seed ON seed.slug = c.slug;