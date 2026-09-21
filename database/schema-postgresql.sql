-- SRDVVIP schema for Replit's built-in PostgreSQL database.
-- This file intentionally contains no credentials and no database-selection command.

CREATE TABLE IF NOT EXISTS categories (
  id BIGSERIAL PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  slug VARCHAR(140) NOT NULL UNIQUE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menus (
  id BIGSERIAL PRIMARY KEY,
  category_id BIGINT NOT NULL REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  name VARCHAR(160) NOT NULL,
  description TEXT NOT NULL,
  price INTEGER NOT NULL CHECK (price >= 0),
  image_path VARCHAR(255) NOT NULL,
  is_active SMALLINT NOT NULL DEFAULT 1 CHECK (is_active IN (0, 1)),
  display_order INTEGER NOT NULL DEFAULT 0,
  created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (category_id, name)
);

CREATE INDEX IF NOT EXISTS idx_menus_active_order ON menus (is_active, display_order, id);

CREATE TABLE IF NOT EXISTS orders (
  id BIGSERIAL PRIMARY KEY,
  order_number VARCHAR(32) NOT NULL UNIQUE,
  customer_name VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  whatsapp VARCHAR(40) NOT NULL,
  recovery_mode VARCHAR(16) NOT NULL CHECK (recovery_mode IN ('delivery', 'takeaway', 'onsite')),
  address VARCHAR(255),
  district VARCHAR(120),
  people_count VARCHAR(20),
  requested_time TIME,
  instructions TEXT,
  total_amount INTEGER NOT NULL CHECK (total_amount >= 0),
  status VARCHAR(16) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'delivered', 'cancelled')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_orders_status_date ON orders (status, created_at);
CREATE INDEX IF NOT EXISTS idx_orders_search ON orders (order_number, customer_name, phone);

CREATE TABLE IF NOT EXISTS order_items (
  id BIGSERIAL PRIMARY KEY,
  order_id BIGINT NOT NULL REFERENCES orders(id) ON UPDATE CASCADE ON DELETE CASCADE,
  menu_id BIGINT NOT NULL REFERENCES menus(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  menu_name VARCHAR(160) NOT NULL,
  unit_price INTEGER NOT NULL CHECK (unit_price >= 0),
  quantity SMALLINT NOT NULL CHECK (quantity > 0),
  subtotal INTEGER NOT NULL CHECK (subtotal >= 0),
  image_path VARCHAR(255) NOT NULL
);

INSERT INTO categories (name, slug) VALUES
  ('Plats', 'plat'),
  ('Boissons', 'boisson')
ON CONFLICT (slug) DO NOTHING;

INSERT INTO menus (category_id, name, description, price, image_path, is_active, display_order)
SELECT c.id, seed.name, seed.description, seed.price, seed.image_path, 1, seed.display_order
FROM categories c
JOIN (
  VALUES
    ('plat', 'Dakéré', 'Notre plat signature : du mil savamment préparé avec une sauce riche et épicée.', 1000, 'SRD_VVIP/lait_dakere.jpg', 10),
    ('plat', 'Riz Sénégalais', 'Riz parfumé cuisiné à la sénégalaise, savoureux et généreux.', 1000, 'SRD_VVIP/riz_sénégalais.jpg', 20),
    ('plat', 'Riz Blanc Parfumé', 'Riz blanc de qualité, parfumé et accompagné de sauces maison.', 1000, 'SRD_VVIP/riz_parfume.jpg', 30),
    ('plat', 'Spaghetti Macaronis', 'Pâtes savoureuses préparées avec une sauce maison généreuse.', 1000, 'SRD_VVIP/spaghettis_macaronis.jpg', 40),
    ('plat', 'Pommes Plantain Viande', 'Plantains dorés accompagnés d’une viande tendre et bien assaisonnée.', 1000, 'SRD_VVIP/pommes_plantain_viande.jpg', 50),
    ('plat', 'Poulet', 'Poulet grillé ou braisé, mariné aux épices africaines et cuit à la perfection.', 3000, 'SRD_VVIP/poulet.jpg', 60),
    ('plat', 'Frites', 'Frites croustillantes et dorées, parfaites en accompagnement.', 500, 'SRD_VVIP/frites.jpg', 70),
    ('plat', 'Eru', 'Plat traditionnel camerounais aux légumes et viandes fumées, riche en saveurs.', 1500, 'SRD_VVIP/eru.jpg', 80),
    ('plat', 'Couscous', 'Couscous moelleux accompagné d’une sauce généreuse et de légumes.', 2000, 'SRD_VVIP/couscous.jpg', 90),
    ('plat', 'Shawarma', 'Shawarma généreux garni de viande tendre, légumes frais et sauces maison.', 1000, 'SRD_VVIP/shawarma.jpg', 100),
    ('boisson', 'Boissons', 'Large choix de boissons fraîches, jus naturels et sodas.', 1000, 'SRD_VVIP/boissons.jpg', 110),
    ('boisson', 'Eau Minérale', 'Eau minérale pure et fraîche pour accompagner votre repas.', 500, 'SRD_VVIP/eau_pure.jpg', 120)
) AS seed(slug, name, description, price, image_path, display_order) ON seed.slug = c.slug
ON CONFLICT (category_id, name) DO NOTHING;