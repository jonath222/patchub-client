# patchub/client

Package Laravel permettant de recevoir les patchnotes Patchub par webhook et de les afficher dans une application cliente.

Le package est compatible avec Laravel 11, 12 et 13. Il ne dépend pas de Bootstrap, Tailwind, Livewire ou d'une navbar particulière.

## Prerequis

- PHP 8.2 ou superieur
- Laravel 11, 12 ou 13
- Une application Laravel avec l'authentification utilisateur
- L'acces au depot GitHub `jonath222/patchub-client`

Le package n'est pas publie sur Packagist. Il faut donc declarer son depot VCS avant la premiere installation.

## Installation initiale

Depuis la racine de l'application cliente :

```bash
composer config repositories.patchub vcs https://github.com/jonath222/patchub-client.git
composer require patchub/client:^1.0.6
```

Avec Docker et l'alias `dce docker compose exec` :

```bash
dce composer config repositories.patchub vcs https://github.com/jonath222/patchub-client.git
dce sh -lc 'mkdir -p /tmp/composer && export COMPOSER_HOME=/tmp/composer && composer require patchub/client:^1.0.6 --no-interaction'
```

`COMPOSER_HOME` est necessaire uniquement si Composer s'exécute dans un conteneur avec `HOME=/`. Il permet a Composer d'utiliser un cache accessible en ecriture.

Si le package est deja declare dans `composer.json`, ne relancez pas `composer require`. Utilisez la procedure de mise a jour ci-dessous.

## Configuration

Ajoutez le secret partage dans le fichier `.env` de l'application cliente :

```dotenv
PATCHUB_WEBHOOK_SECRET=le-secret-en-clair-fourni-par-patchub
PATCHUB_WEBHOOK_PATH=patchub/webhook
```

Le secret doit etre strictement identique a celui configure pour l'application dans Patchub.

Le provider Laravel est auto-decouvert par Composer. Le package ajoute automatiquement :

- la route `POST /patchub/webhook` ;
- la route `POST /patchub/mark-as-read` ;
- les migrations des tables de patchnotes ;
- le composant Blade `x-patchub-bell`.

Dans le modele `User`, utilisez le trait :

```php
use Patchub\Client\Concerns\HasPatchNotes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPatchNotes;
}
```

Puis executez les migrations :

```bash
php artisan migrate
```

## CSS et cloche

Publiez le CSS du package :

```bash
php artisan vendor:publish --tag=patchub-client-css --force
```

Importez ensuite le fichier publie dans l'entree CSS de votre application, par exemple dans `resources/css/app.css` :

```css
@import './patchub.css';
```

Compilez les assets :

```bash
npm install
npm run build
```

Rendez le composant une seule fois dans le layout global, de preference en dehors des navbars et sidebars :

```blade
<x-patchub-bell />
```

Le composant est autonome :

- la pastille est fixe en bas a droite ;
- le panneau s'ouvre au-dessus de la pastille ;
- sa largeur est limitee au viewport mobile ;
- son affichage ne depend d'aucun framework CSS ;
- les offsets peuvent etre personnalises avec `--patchub-offset-right` et `--patchub-offset-bottom`.

La route `patchub.patch-notes` est optionnelle. Le composant n'affiche les liens de detail que si l'application cliente fournit elle-meme cette route.

## Mise a jour en developpement

Apres la publication d'une nouvelle version du package, depuis l'application cliente :

```bash
dce sh -lc 'mkdir -p /tmp/composer && export COMPOSER_HOME=/tmp/composer && composer update patchub/client -W --no-interaction'
dce php artisan vendor:publish --tag=patchub-client-css --force --ansi
npm run build
dce php artisan migrate
dce php artisan optimize:clear
```

Si le package n'est pas encore dans `composer.json`, utilisez `composer require` avec la procedure d'installation initiale.

Committez ensuite `composer.json` et `composer.lock`.

## Installation en production

La production doit utiliser les versions verrouillees dans `composer.lock`. Ne donnez pas de nom de package a `composer install` : cette commande installe toutes les dependances du lock file.

```bash
dce sh -lc 'mkdir -p /tmp/composer && export COMPOSER_HOME=/tmp/composer && composer install --no-dev --prefer-dist --optimize-autoloader --classmap-authoritative --no-interaction'
dce php artisan vendor:publish --tag=patchub-client-css --force --ansi
npm ci
npm run build
dce php artisan migrate --force
dce php artisan optimize
```

Si `patchub/client` n'est pas deja present dans `composer.json` et `composer.lock`, faites d'abord l'installation ou la mise a jour en developpement, puis committez ces deux fichiers avant le deploiement.

N'utilisez pas `composer update` en production : il peut resoudre de nouvelles versions et produire un environnement different de celui teste en developpement.

## Verification

Verifier que le package est installe :

```bash
dce composer show patchub/client
dce php artisan route:list --name=patchub
```

Verifier ensuite qu'une patchnote envoyee depuis Patchub apparait dans la table locale `patchub_patch_notes`.

## Patchub lui-meme comme client

Patchub peut techniquement installer ce package pour tester le role d'application cliente, mais ce n'est pas requis pour emettre des patchnotes. L'application Patchub possede deja son propre fonctionnement d'emission et son interface d'administration.

Pour recevoir ses propres patchnotes via le package, Patchub doit etre configure comme une application cliente avec une URL webhook publique. Sans cette configuration, l'installation du package seule ne fera pas apparaitre les patchnotes existantes.
