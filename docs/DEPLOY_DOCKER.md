# Déploiement sur VPS avec Docker

Ce projet est dockerisé en 2 conteneurs :

- **app** — PHP 8.3-FPM + Nginx + assets Vite compilés (image construite depuis le `Dockerfile` à la racine)
- **mysql** — MySQL 8.0 avec volume persistant

Le module IA FastAPI n'est pas inclus (dossier `fastapi/` non déployé) : l'application fonctionne sans, `IAService::healthCheck()` détecte son absence proprement.

Le conteneur `app` n'expose **qu'un seul port** (configurable, par défaut `8080`) et la base MySQL n'est **jamais exposée** sur l'hôte — adapté à un VPS qui héberge déjà d'autres sites sur les ports 80/443.

---

## 1. Prérequis sur le VPS

```bash
# Installer Docker + le plugin Compose (Ubuntu/Debian)
curl -fsSL https://get.docker.com | sh
sudo apt-get install -y docker-compose-plugin

# Vérifier qu'aucun autre service n'utilise déjà le port choisi (ex: 8080)
ss -tlnp | grep 8080
```

## 2. Récupérer le code

```bash
git clone <url-de-ton-repo> gestipro
cd gestipro
```

## 3. Configurer l'environnement

```bash
cp .env.docker.example .env.docker
```

Éditer `.env.docker` et changer **au minimum** :

| Variable | Valeur |
|---|---|
| `APP_URL` | `http://IP_DU_VPS:8080` (mets à jour plus tard avec ton domaine) |
| `APP_PORT` | un port libre sur le VPS si `8080` est déjà pris |
| `DB_PASSWORD` **et** `MYSQL_PASSWORD` | le même mot de passe fort dans les deux (Laravel lit `DB_*`, l'image MySQL lit `MYSQL_*`) |
| `MYSQL_ROOT_PASSWORD` | un mot de passe fort, différent du précédent |

`.env.docker` est ignoré par git (comme `.env`) — il ne sera jamais commité.

Générer la clé d'application et la coller dans `APP_KEY=` :

```bash
docker compose run --rm app php artisan key:generate --show
```

## 4. Construire et démarrer

```bash
docker compose build
docker compose up -d
```

## 5. Migrer la base (et charger les données de démo si besoin)

```bash
docker compose exec app php artisan migrate --force

# Optionnel : jeu de données de démo (admin@gestipro.sn / password)
docker compose exec app php artisan db:seed --class=DemoSeeder --force
```

## 6. Vérifier

```bash
curl -I http://localhost:${APP_PORT:-8080}
docker compose logs -f app
```

Ouvrir le port dans le firewall si nécessaire :

```bash
sudo ufw allow 8080/tcp
```

L'application est accessible sur `http://IP_DU_VPS:8080`.

---

## Plus tard : ajouter un nom de domaine + HTTPS

Comme le VPS héberge déjà d'autres sites, ne touche pas au reverse proxy existant pour ces sites. Ajoute simplement un nouveau bloc serveur dans le nginx (ou autre proxy) déjà présent sur l'hôte, qui pointe vers `127.0.0.1:8080` :

```nginx
server {
    listen 80;
    server_name ton-domaine.com;
    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Puis active le HTTPS avec certbot comme pour tes autres sites (`certbot --nginx -d ton-domaine.com`), et mets à jour `APP_URL=https://ton-domaine.com` dans `.env.docker` avant de relancer :

```bash
docker compose up -d --force-recreate app
```

---

## Mises à jour ultérieures

```bash
git pull
docker compose build app
docker compose up -d
docker compose exec app php artisan migrate --force
```

`config:cache` / `route:cache` / `view:cache` sont régénérés automatiquement à chaque démarrage du conteneur (voir `docker/entrypoint.sh`).

## Sauvegarde de la base

```bash
docker compose exec mysql sh -c 'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" gestipro' > backup_$(date +%F).sql
```
