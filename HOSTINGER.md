# Mise en ligne SRDVVIP sur Hostinger

Cette procédure utilise l’hébergement PHP classique de Hostinger et une base
MySQL/MariaDB gérée par phpMyAdmin. Le fichier `database/schema.sql` est le
schéma à importer sur Hostinger. Le fichier PostgreSQL présent dans le projet
sert uniquement à l’environnement Replit et ne doit pas être importé sur
Hostinger.

## 1. Préparer le compte Hostinger

Dans hPanel :

1. Ouvrir **Sites** puis **Gérer** le domaine.
2. Choisir **Bases de données → MySQL**.
3. Créer :
   - une base de données ;
   - un utilisateur MySQL ;
   - un mot de passe fort ;
   - associer l’utilisateur à la base avec tous les privilèges.
4. Noter exactement le nom de la base, le nom de l’utilisateur, le mot de
   passe et l’hôte MySQL. Sur la plupart des offres, l’hôte est `localhost`.

Ne pas essayer d’importer `database/schema-postgresql.sql`. Hostinger utilise
`database/schema.sql`.

## 2. Configurer PHP

Dans **Avancé → Configuration PHP** :

1. Sélectionner PHP 8.1 ou une version plus récente.
2. Vérifier que les extensions suivantes sont actives :
   - `PDO`
   - `pdo_mysql`
   - `mbstring`
   - `fileinfo`
   - `openssl`
   - `session`
3. Activer HTTPS avec le certificat SSL du domaine.

Le site utilise `password_hash`, `password_verify`, les sessions, les uploads
d’images et PDO MySQL. PHP 8.1+ est requis.

## 3. Importer la base

1. Ouvrir **phpMyAdmin** depuis hPanel.
2. Sélectionner la base Hostinger créée à l’étape 1 dans la colonne de gauche.
3. Ouvrir l’onglet **Importer**.
4. Choisir `database/schema.sql`.
5. Lancer l’import.
6. Vérifier que les tables suivantes existent :
   - `categories`
   - `menus`
   - `orders`
   - `order_items`
7. Vérifier que les catégories et les 12 menus initiaux sont présents.

Le schéma est réexécutable : les tables utilisent `IF NOT EXISTS` et les
données initiales utilisent `INSERT IGNORE`.

## 4. Envoyer les fichiers

Dans **Fichiers → Gestionnaire de fichiers**, ouvrir le dossier
`public_html` du domaine et envoyer les fichiers de production du projet :

- `index.html`
- `cart.php`
- `api/`
- `admin/`
- `config/`
- `css/`
- `js/`
- `SRD_VVIP/`
- `logo_SRD2.png`
- `favicon.png`
- `uploads/menu/`

Ne pas envoyer les fichiers de développement inutiles :

- `.replit`
- `replit.md`
- `.env.example`
- `database/schema-postgresql.sql`
- `attached_assets/`
- le dossier `.git/`

Conserver `database/schema.sql` uniquement si nécessaire pour référence, ou ne
pas l’envoyer après l’import. Le dossier `config/` est protégé par son
`.htaccess`.

## 5. Configurer les identifiants MySQL et admin

1. Copier `config/local.php.example` sous le nom `config/local.php`.
2. Modifier uniquement `config/local.php` avec les valeurs Hostinger :

```php
<?php
return [
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_NAME' => 'nom_de_la_base_hostinger',
    'DB_USER' => 'utilisateur_mysql_hostinger',
    'DB_PASSWORD' => 'mot_de_passe_mysql_hostinger',
    'SRDVVIP_ADMIN_USER' => 'SRDVVIP',
    'SRDVVIP_ADMIN_PASSWORD_HASH' => 'hash_du_mot_de_passe_admin',
    'APP_TIMEZONE' => 'Africa/Douala',
];
```

3. Générer un hash localement, sans mettre le mot de passe dans le code :

```bash
php -r "echo password_hash('VotreNouveauMotDePasse', PASSWORD_DEFAULT), PHP_EOL;"
```

4. Copier uniquement le résultat dans `SRDVVIP_ADMIN_PASSWORD_HASH`.
5. Ne jamais publier `config/local.php` dans un dépôt public.
6. Régler les permissions de `config/local.php` sur `600` si Hostinger le
   permet.

Le login se trouve à :

```text
https://votre-domaine.tld/admin/login.php
```

## 6. Permissions

Le serveur doit pouvoir lire tous les fichiers et écrire dans :

```text
uploads/menu/
```

Permissions usuelles :

- dossiers : `755`
- fichiers : `644`
- `config/local.php` : `600` si disponible
- `uploads/menu/` : `755`, ou `775` si les uploads sont refusés

## 7. Contrôle fonctionnel après migration

Effectuer ces tests dans cet ordre :

1. Ouvrir la page publique et vérifier les images.
2. Ouvrir `https://votre-domaine.tld/api/menu.php`.
   La réponse attendue commence par `{"ok":true,"menus":[...`.
3. Ajouter un plat au panier.
4. Ouvrir `cart.php`.
5. Remplir une commande de test avec le mode « À emporter ».
6. Vérifier la redirection WhatsApp.
7. Se connecter à l’administration.
8. Vérifier le tableau de bord et la commande de test.
9. Modifier le statut de la commande.
10. Ajouter, modifier puis désactiver un menu de test.
11. Supprimer la commande de test depuis phpMyAdmin uniquement si elle ne doit
    pas rester dans l’historique.

## 8. Vérifications de sécurité

Après les tests :

1. Vérifier que `https://votre-domaine.tld/config/local.php` ne renvoie pas le
   contenu du fichier.
2. Vérifier que `https://votre-domaine.tld/database/schema.sql` n’est pas
   accessible.
3. Vérifier que HTTPS reste actif.
4. Changer le mot de passe admin de test avant l’ouverture publique.
5. Ne jamais partager le contenu de `config/local.php`.
6. Conserver une sauvegarde phpMyAdmin de la base avant chaque modification
   importante.

## 9. En cas de problème

- **API menu en 503** : vérifier `DB_HOST`, `DB_NAME`, `DB_USER`,
  `DB_PASSWORD`, les privilèges MySQL et l’import du schéma.
- **Commande en 503** : vérifier d’abord `/api/menu.php`, puis les tables
  `orders` et `order_items`.
- **Upload refusé** : vérifier les permissions de `uploads/menu/` et les
  limites PHP `upload_max_filesize` et `post_max_size`.
- **Session admin qui expire** : vérifier HTTPS, les cookies du navigateur et
  les permissions du dossier de sessions PHP de l’hébergement.
- **Page blanche** : consulter les logs PHP Hostinger et vérifier que PHP 8.1+
  et `pdo_mysql` sont activés.