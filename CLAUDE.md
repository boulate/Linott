# CLAUDE.md - Linott v2

Ce fichier contient toutes les informations nécessaires pour développer Linott v2.

---

## Objectifs du projet

| Objectif | Description |
|----------|-------------|
| **Fonctionnel** | Application de comptabilité analytique des heures de travail |
| **Métier** | Logique analytique à 4 niveaux (Section → Axe1 → Axe2 → Axe3) |
| **Légal** | Compatibilité avec le droit du travail français (congés, RTT, heures sup) |
| **Technique** | Stack moderne, maintenable, pérenne (versions LTS) |
| **Équipe** | Alignement avec la stack de l'entreprise (Symfony) |
| **Déploiement** | Containerisé (Docker), déployable facilement |

---

## Stack technique

### Choix et justifications

| Composant | Technologie | Version | Justification |
|-----------|-------------|---------|---------------|
| **Framework** | Symfony | 6.4 LTS | Support jusqu'à nov. 2027, alignement entreprise |
| **Langage** | PHP | 8.2+ | Version stable, support long terme |
| **ORM** | Doctrine ORM | 3.x | Standard Symfony, migrations intégrées |
| **Templates** | Twig | 3.x | Standard Symfony |
| **Interactivité** | HTMX | 2.x | Stable, peu de JS, logique côté serveur |
| **UI dynamique** | Alpine.js | 3.x | Léger, interactions locales |
| **CSS** | Tailwind CSS | 3.x | Version stable (pas v4), productif |
| **BDD** | MariaDB | 10.11 LTS | Compatible MySQL, connu, support jusqu'à 2028 |
| **Serveur web** | Caddy | 2.x | HTTPS auto, config simple |
| **Conteneurs** | Docker | - | Environnement reproductible |

### Backend détaillé

```
Framework:       Symfony 6.4 LTS
Langage:         PHP 8.2+
ORM:             Doctrine ORM 3.x
Migrations:      Doctrine Migrations
Auth:            Symfony Security (sessions)
Validation:      Symfony Validator
Templates:       Twig 3.x
Tests:           PHPUnit + Symfony WebTestCase
Qualité:         PHP-CS-Fixer, PHPStan niveau 6
```

### Frontend détaillé

```
Interactivité:   HTMX 2.0.4 (CDN ou local)
UI dynamique:    Alpine.js 3.14.x (CDN ou local)
CSS:             Tailwind CSS 3.4.x
Graphiques:      Chart.js 4.x
Calendrier:      FullCalendar 6.x (si nécessaire)
Icônes:          Lucide Icons
Build:           Symfony AssetMapper (pas de Node/npm obligatoire)
```

### Infrastructure

```
Conteneurs:      Docker + Docker Compose
Serveur web:     Caddy 2.x (ou Nginx)
PHP:             PHP-FPM 8.2
Base de données: MariaDB 10.11 LTS
Cache:           Redis (optionnel, phase ultérieure)
```

---

## Architecture du projet

```
linott/
├── .docker/
│   ├── php/
│   │   ├── Dockerfile
│   │   └── php.ini
│   ├── caddy/
│   │   └── Caddyfile
│   └── mariadb/
│       └── init.sql
├── assets/
│   ├── styles/
│   │   └── app.css
│   └── app.js
├── bin/
│   └── console
├── config/
│   ├── packages/
│   │   ├── doctrine.yaml
│   │   ├── security.yaml
│   │   ├── twig.yaml
│   │   └── ...
│   ├── routes.yaml
│   └── services.yaml
├── migrations/
├── public/
│   └── index.php
├── src/
│   ├── Controller/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ComptaController.php
│   │   ├── CalendrierController.php
│   │   ├── StatsController.php
│   │   └── Admin/
│   │       ├── UserController.php
│   │       ├── SectionController.php
│   │       └── AxeController.php
│   ├── Entity/
│   │   ├── User.php
│   │   ├── Section.php
│   │   ├── Axe1.php
│   │   ├── Axe2.php
│   │   ├── Axe3.php
│   │   ├── Periode.php
│   │   ├── Conge.php
│   │   ├── TypeConge.php
│   │   └── Configuration.php
│   ├── Repository/
│   │   ├── UserRepository.php
│   │   ├── SectionRepository.php
│   │   ├── PeriodeRepository.php
│   │   └── ...
│   ├── Service/
│   │   ├── ComptaService.php
│   │   ├── CongeService.php
│   │   ├── StatsService.php
│   │   └── ExportService.php
│   ├── Form/
│   │   ├── PeriodeType.php
│   │   ├── CongeType.php
│   │   └── ...
│   ├── Security/
│   │   └── UserAuthenticator.php
│   └── Kernel.php
├── templates/
│   ├── base.html.twig
│   ├── auth/
│   │   └── login.html.twig
│   ├── dashboard/
│   │   └── index.html.twig
│   ├── compta/
│   │   ├── index.html.twig
│   │   ├── _fiche.html.twig
│   │   ├── _periode_row.html.twig
│   │   └── _periode_form.html.twig
│   ├── calendrier/
│   │   ├── index.html.twig
│   │   └── _conge_form.html.twig
│   ├── stats/
│   │   └── index.html.twig
│   ├── admin/
│   │   ├── users/
│   │   ├── sections/
│   │   └── axes/
│   └── components/
│       ├── _flash_messages.html.twig
│       ├── _pagination.html.twig
│       ├── _confirm_modal.html.twig
│       └── _loading.html.twig
├── tests/
│   ├── Controller/
│   ├── Service/
│   └── bootstrap.php
├── var/
│   ├── cache/
│   └── log/
├── vendor/
├── .env
├── .env.local
├── .env.test
├── .gitignore
├── composer.json
├── composer.lock
├── docker-compose.yml
├── phpunit.xml.dist
├── tailwind.config.js
├── postcss.config.js
└── package.json
```

---

## Modèle de données

### Schéma des entités principales

```
┌─────────────┐
│    User     │
├─────────────┤
│ id          │
│ email       │
│ password    │
│ nom         │
│ prenom      │
│ roles[]     │
│ actif       │
│ created_at  │
│ updated_at  │
└─────────────┘
       │
       │ 1:N
       ▼
┌─────────────┐      ┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│   Section   │ 1:N  │    Axe1     │ 1:N  │    Axe2     │ 1:N  │    Axe3     │
├─────────────┤─────▶├─────────────┤─────▶├─────────────┤─────▶├─────────────┤
│ id          │      │ id          │      │ id          │      │ id          │
│ code        │      │ code        │      │ code        │      │ code        │
│ libelle     │      │ libelle     │      │ libelle     │      │ libelle     │
│ actif       │      │ section_id  │      │ axe1_id     │      │ axe2_id     │
│ ordre       │      │ actif       │      │ actif       │      │ actif       │
└─────────────┘      │ ordre       │      │ ordre       │      │ ordre       │
                     └─────────────┘      └─────────────┘      └─────────────┘
                            │                   │                    │
                            └───────────────────┴────────────────────┘
                                              │
                                              ▼
                                       ┌─────────────┐
                                       │   Periode   │
                                       ├─────────────┤
                                       │ id          │
                                       │ user_id     │
                                       │ date        │
                                       │ heure_debut │
                                       │ heure_fin   │
                                       │ section_id  │
                                       │ axe1_id     │
                                       │ axe2_id     │
                                       │ axe3_id     │
                                       │ commentaire │
                                       │ validee     │
                                       │ created_at  │
                                       │ updated_at  │
                                       └─────────────┘

┌─────────────┐      ┌─────────────┐
│  TypeConge  │ 1:N  │    Conge    │
├─────────────┤─────▶├─────────────┤
│ id          │      │ id          │
│ code        │      │ user_id     │
│ libelle     │      │ type_id     │
│ decompte    │      │ date_debut  │
│ couleur     │      │ date_fin    │
│ actif       │      │ nb_jours    │
└─────────────┘      │ commentaire │
                     │ statut      │
                     │ created_at  │
                     └─────────────┘

┌─────────────────┐
│  Configuration  │
├─────────────────┤
│ id              │
│ cle             │
│ valeur          │
│ description     │
└─────────────────┘
```

### Règles métier clés

**Périodes :**
- Une période appartient à un utilisateur et une date
- Section obligatoire, Axe1/Axe2/Axe3 optionnels (dépend de la config)
- Axe1 doit appartenir à la Section sélectionnée
- Axe2 doit appartenir à l'Axe1 sélectionné
- Axe3 doit appartenir à l'Axe2 sélectionné
- Pas de chevauchement de périodes pour un même utilisateur/date
- Total journalier calculé automatiquement

**Congés :**
- Types : CP (congés payés), RTT, récupération, maladie, sans solde, etc.
- Compteurs annuels par type de congé
- Validation par un admin (optionnel)

---

## Pattern HTMX avec Symfony

### Principe

Le serveur retourne des **fragments HTML**, pas du JSON. HTMX remplace des parties de la page sans rechargement complet.

### Exemple : Navigation entre jours

**Controller :**
```php
#[Route('/compta/{date?}', name: 'compta_index')]
public function index(?string $date, Request $request): Response
{
    $currentDate = $date
        ? new \DateTimeImmutable($date)
        : new \DateTimeImmutable();

    $periodes = $this->periodeRepository->findByUserAndDate(
        $this->getUser(),
        $currentDate
    );

    $params = [
        'date' => $currentDate,
        'previousDate' => $currentDate->modify('-1 day')->format('Y-m-d'),
        'nextDate' => $currentDate->modify('+1 day')->format('Y-m-d'),
        'periodes' => $periodes,
        'totalHeures' => $this->comptaService->calculateTotal($periodes),
    ];

    // Si requête HTMX, retourner uniquement le fragment
    if ($request->headers->has('HX-Request')) {
        return $this->render('compta/_fiche.html.twig', $params);
    }

    return $this->render('compta/index.html.twig', $params);
}
```

**Template principal (`compta/index.html.twig`) :**
```twig
{% extends 'base.html.twig' %}

{% block body %}
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-6">
        <button hx-get="{{ path('compta_index', {date: previousDate}) }}"
                hx-target="#fiche-container"
                hx-push-url="true"
                class="btn btn-secondary">
            &larr; Jour précédent
        </button>

        <h1 id="current-date" class="text-2xl font-bold">
            {{ date|format_datetime('full', 'none', locale='fr') }}
        </h1>

        <button hx-get="{{ path('compta_index', {date: nextDate}) }}"
                hx-target="#fiche-container"
                hx-push-url="true"
                class="btn btn-secondary">
            Jour suivant &rarr;
        </button>
    </div>

    <div id="fiche-container">
        {% include 'compta/_fiche.html.twig' %}
    </div>
</div>
{% endblock %}
```

**Fragment HTMX (`compta/_fiche.html.twig`) :**
```twig
{# Mise à jour du titre hors du conteneur principal #}
<h1 id="current-date"
    hx-swap-oob="true"
    class="text-2xl font-bold">
    {{ date|format_datetime('full', 'none', locale='fr') }}
</h1>

<div class="bg-white rounded-lg shadow">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Début</th>
                <th class="px-4 py-2 text-left">Fin</th>
                <th class="px-4 py-2 text-left">Section</th>
                <th class="px-4 py-2 text-left">Axe 1</th>
                <th class="px-4 py-2 text-left">Axe 2</th>
                <th class="px-4 py-2 text-left">Axe 3</th>
                <th class="px-4 py-2 text-right">Durée</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody id="periodes-list">
            {% for periode in periodes %}
                {% include 'compta/_periode_row.html.twig' %}
            {% else %}
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        Aucune période saisie pour cette journée
                    </td>
                </tr>
            {% endfor %}
        </tbody>
        <tfoot class="bg-gray-50 font-bold">
            <tr>
                <td colspan="6" class="px-4 py-2 text-right">Total :</td>
                <td class="px-4 py-2 text-right">{{ totalHeures }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

{# Bouton d'ajout #}
<div class="mt-4">
    <button hx-get="{{ path('compta_periode_new', {date: date|date('Y-m-d')}) }}"
            hx-target="#periode-modal"
            hx-swap="innerHTML"
            class="btn btn-primary">
        + Ajouter une période
    </button>
</div>

<div id="periode-modal"></div>
```

### Sélecteurs en cascade (Alpine.js + HTMX)

```twig
<div x-data="{
    sectionId: '{{ periode.section.id ?? '' }}',
    axe1Id: '{{ periode.axe1.id ?? '' }}',
    axe2Id: '{{ periode.axe2.id ?? '' }}'
}">
    {# Section #}
    <select name="section"
            x-model="sectionId"
            hx-get="{{ path('api_axes1') }}"
            hx-target="#axe1-container"
            hx-include="[name='section']"
            class="form-select">
        <option value="">-- Choisir une section --</option>
        {% for section in sections %}
            <option value="{{ section.id }}">{{ section.libelle }}</option>
        {% endfor %}
    </select>

    {# Axe 1 - chargé dynamiquement #}
    <div id="axe1-container">
        <select name="axe1" x-model="axe1Id" class="form-select" disabled>
            <option value="">-- Choisir d'abord une section --</option>
        </select>
    </div>

    {# Axe 2 - chargé dynamiquement #}
    <div id="axe2-container">
        <select name="axe2" x-model="axe2Id" class="form-select" disabled>
            <option value="">-- Choisir d'abord un axe 1 --</option>
        </select>
    </div>

    {# Axe 3 - chargé dynamiquement #}
    <div id="axe3-container">
        <select name="axe3" class="form-select" disabled>
            <option value="">-- Choisir d'abord un axe 2 --</option>
        </select>
    </div>
</div>
```

---

## Plan de migration détaillé

### Vue d'ensemble

```
PHASE 1 ─────────────────────────────────────────────────────────────
│ Infrastructure Docker + Symfony + Auth                             │
│ Durée estimée : 2-3 sessions                                       │
└────────────────────────────────────────────────────────────────────┘
                                │
                                ▼
PHASE 2 ─────────────────────────────────────────────────────────────
│ Modèle de données (Entities Doctrine)                              │
│ Durée estimée : 2 sessions                                         │
└────────────────────────────────────────────────────────────────────┘
                                │
                                ▼
PHASE 3 ─────────────────────────────────────────────────────────────
│ Module Compta (saisie des heures) - CŒUR MÉTIER                    │
│ Durée estimée : 4-5 sessions                                       │
└────────────────────────────────────────────────────────────────────┘
                                │
                                ▼
PHASE 4 ─────────────────────────────────────────────────────────────
│ Module Calendrier (congés, RTT)                                    │
│ Durée estimée : 2-3 sessions                                       │
└────────────────────────────────────────────────────────────────────┘
                                │
                                ▼
PHASE 5 ─────────────────────────────────────────────────────────────
│ Module Stats + Exports                                             │
│ Durée estimée : 2 sessions                                         │
└────────────────────────────────────────────────────────────────────┘
                                │
                                ▼
PHASE 6 ─────────────────────────────────────────────────────────────
│ Module Admin                                                       │
│ Durée estimée : 2 sessions                                         │
└────────────────────────────────────────────────────────────────────┘
```

---

### Phase 1 : Infrastructure + Auth

**Objectif** : Environnement de dev fonctionnel avec authentification

#### Tâches

| # | Tâche | Fichiers concernés | Test de validation |
|---|-------|-------------------|-------------------|
| 1.1 | Créer docker-compose.yml | `docker-compose.yml` | `docker compose up -d` sans erreur |
| 1.2 | Dockerfile PHP 8.2 + extensions | `.docker/php/Dockerfile` | `docker compose exec php php -v` affiche 8.2 |
| 1.3 | Configuration Caddy | `.docker/caddy/Caddyfile` | `curl http://localhost` répond |
| 1.4 | Configuration MariaDB | `.docker/mariadb/init.sql` | `docker compose exec db mysql -u linott -p` fonctionne |
| 1.5 | Installer Symfony 6.4 | `composer.json` | `bin/console --version` affiche 6.4 |
| 1.6 | Configurer Doctrine | `config/packages/doctrine.yaml`, `.env` | `bin/console doctrine:database:create` OK |
| 1.7 | Entity User | `src/Entity/User.php` | `bin/console make:migration` génère une migration |
| 1.8 | Migration User | `migrations/` | `bin/console doctrine:migrations:migrate` OK |
| 1.9 | Fixtures User admin | `src/DataFixtures/UserFixtures.php` | `bin/console doctrine:fixtures:load` crée un user |
| 1.10 | Configurer Security | `config/packages/security.yaml` | Routes protégées redirigent vers /login |
| 1.11 | Login form | `src/Controller/AuthController.php`, `templates/auth/login.html.twig` | Formulaire s'affiche |
| 1.12 | Authentification | `src/Security/UserAuthenticator.php` | Login avec fixtures fonctionne |
| 1.13 | Logout | `config/packages/security.yaml` | Déconnexion fonctionne |
| 1.14 | Layout Tailwind | `templates/base.html.twig`, `assets/styles/app.css` | Page stylée correctement |
| 1.15 | Intégrer HTMX | `templates/base.html.twig` | `htmx` disponible dans console JS |
| 1.16 | Intégrer Alpine.js | `templates/base.html.twig` | `Alpine` disponible dans console JS |
| 1.17 | Dashboard post-login | `src/Controller/DashboardController.php` | Après login, redirige vers dashboard |
| 1.18 | Tests PHPUnit config | `phpunit.xml.dist`, `tests/bootstrap.php` | `bin/phpunit` s'exécute |
| 1.19 | Test login fonctionnel | `tests/Controller/AuthControllerTest.php` | Test passe |

#### Critère de fin de phase

Un utilisateur peut :
1. Accéder à http://localhost
2. Être redirigé vers /login s'il n'est pas connecté
3. Se connecter avec admin@linott.local / password
4. Voir un dashboard vide mais stylé
5. Se déconnecter

#### Configuration Docker à créer

**docker-compose.yml :**
```yaml
version: '3.8'

services:
  php:
    build:
      context: .
      dockerfile: .docker/php/Dockerfile
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      DATABASE_URL: mysql://linott:linott@db:3306/linott?serverVersion=10.11.0-MariaDB

  caddy:
    image: caddy:2-alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - .:/var/www/html
      - ./.docker/caddy/Caddyfile:/etc/caddy/Caddyfile
    depends_on:
      - php

  db:
    image: mariadb:10.11
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: linott
      MYSQL_USER: linott
      MYSQL_PASSWORD: linott
    volumes:
      - db_data:/var/lib/mysql
      - ./.docker/mariadb/init.sql:/docker-entrypoint-initdb.d/init.sql
    ports:
      - "3306:3306"

volumes:
  db_data:
```

---

### Phase 2 : Modèle de données

**Objectif** : Toutes les entities Doctrine créées avec leurs relations

#### Tâches

| # | Tâche | Fichiers concernés | Test de validation |
|---|-------|-------------------|-------------------|
| 2.1 | Entity Section | `src/Entity/Section.php` | Migration générée |
| 2.2 | Entity Axe1 + relation Section | `src/Entity/Axe1.php` | FK valide |
| 2.3 | Entity Axe2 + relation Axe1 | `src/Entity/Axe2.php` | FK valide |
| 2.4 | Entity Axe3 + relation Axe2 | `src/Entity/Axe3.php` | FK valide |
| 2.5 | Entity Periode + relations | `src/Entity/Periode.php` | FK valides |
| 2.6 | Entity TypeConge | `src/Entity/TypeConge.php` | Migration générée |
| 2.7 | Entity Conge + relations | `src/Entity/Conge.php` | FK valides |
| 2.8 | Entity Configuration | `src/Entity/Configuration.php` | Migration générée |
| 2.9 | Repositories personnalisés | `src/Repository/*.php` | Méthodes de base fonctionnent |
| 2.10 | Fixtures réalistes | `src/DataFixtures/AppFixtures.php` | Données cohérentes créées |
| 2.11 | Exécuter toutes les migrations | `migrations/` | `doctrine:schema:validate` OK |

#### Critère de fin de phase

- `bin/console doctrine:schema:validate` retourne OK
- `bin/console doctrine:fixtures:load` charge des données de test
- Les données sont visibles dans la BDD

---

### Phase 3 : Module Compta

**Objectif** : Saisie complète des heures de travail

#### Tâches

| # | Tâche | Description | Test de validation |
|---|-------|-------------|-------------------|
| 3.1 | Route /compta | Controller + vue de base | Page s'affiche |
| 3.2 | Affichage fiche journalière | Liste des périodes du jour | Périodes des fixtures visibles |
| 3.3 | Navigation jours (HTMX) | Boutons précédent/suivant | Navigation sans reload |
| 3.4 | URL sync (hx-push-url) | History API | Bouton retour fonctionne |
| 3.5 | Sélecteur date (datepicker) | Alpine.js | Saut à une date fonctionne |
| 3.6 | Formulaire nouvelle période | Modal ou inline | Formulaire s'affiche |
| 3.7 | Sélecteurs axes en cascade | HTMX + endpoints API | Filtrage dynamique |
| 3.8 | Validation formulaire | Symfony Validator | Erreurs affichées |
| 3.9 | Sauvegarde période | POST + redirect ou HTMX | Période créée en BDD |
| 3.10 | Édition période | Click → form pré-rempli | Modification OK |
| 3.11 | Suppression période | Confirmation + DELETE | Suppression OK |
| 3.12 | Calcul total journalier | Service ComptaService | Total affiché correctement |
| 3.13 | Affichage semaine | Vue semaine | 7 jours visibles |
| 3.14 | Total hebdomadaire | Calcul semaine | Total correct |
| 3.15 | Validation journée | Bouton + statut | Journée marquée validée |
| 3.16 | Validation semaine | Bouton + statut | Semaine marquée validée |
| 3.17 | Copier journée précédente | Action rapide | Périodes copiées |
| 3.18 | Tests fonctionnels | WebTestCase | Tous les tests passent |

#### Critère de fin de phase

Un utilisateur peut :
1. Voir ses périodes du jour
2. Naviguer entre les jours de façon fluide
3. Ajouter/modifier/supprimer des périodes
4. Voir les totaux journaliers et hebdomadaires
5. Valider une journée ou une semaine

---

### Phase 4 : Module Calendrier

**Objectif** : Gestion des congés et absences

#### Tâches

| # | Tâche | Description | Test de validation |
|---|-------|-------------|-------------------|
| 4.1 | Route /calendrier | Controller + vue | Page s'affiche |
| 4.2 | Vue mensuelle | Grille calendrier | Mois visible |
| 4.3 | Affichage congés | Badges sur les jours | Congés visibles |
| 4.4 | Formulaire nouveau congé | Modal | Formulaire s'affiche |
| 4.5 | Types de congés | Select dynamique | Types disponibles |
| 4.6 | Calcul nb jours | Auto selon dates | Calcul correct |
| 4.7 | Sauvegarde congé | POST | Congé créé |
| 4.8 | Édition congé | Click → form | Modification OK |
| 4.9 | Suppression congé | Confirmation | Suppression OK |
| 4.10 | Compteurs jours restants | Calcul par type | Compteurs affichés |
| 4.11 | Navigation mois | Précédent/suivant | Navigation fluide |
| 4.12 | Tests fonctionnels | WebTestCase | Tests passent |

#### Critère de fin de phase

Un utilisateur peut :
1. Voir le calendrier mensuel
2. Créer/modifier/supprimer des congés
3. Voir ses compteurs de jours restants

---

### Phase 5 : Module Stats + Exports

**Objectif** : Rapports et visualisations

#### Tâches

| # | Tâche | Description | Test de validation |
|---|-------|-------------|-------------------|
| 5.1 | Route /stats | Controller + vue | Page s'affiche |
| 5.2 | Filtres (période, axes) | Formulaire filtres | Filtrage fonctionne |
| 5.3 | Tableau résultats | Données agrégées | Données correctes |
| 5.4 | Graphique répartition | Chart.js pie | Graphique s'affiche |
| 5.5 | Graphique évolution | Chart.js line | Graphique s'affiche |
| 5.6 | Export CSV | Download | Fichier téléchargé |
| 5.7 | Export PDF (optionnel) | DomPDF ou autre | PDF généré |
| 5.8 | Stats congés | Récapitulatif | Stats correctes |
| 5.9 | Tests fonctionnels | WebTestCase | Tests passent |

#### Critère de fin de phase

Un utilisateur peut :
1. Consulter des stats filtrées
2. Voir des graphiques
3. Exporter en CSV

---

### Phase 6 : Module Admin

**Objectif** : Administration complète

#### Tâches

| # | Tâche | Description | Test de validation |
|---|-------|-------------|-------------------|
| 6.1 | Route /admin | Controller + layout admin | Page s'affiche |
| 6.2 | CRUD Utilisateurs | Liste, create, edit, delete | CRUD complet |
| 6.3 | CRUD Sections | Liste, create, edit, delete | CRUD complet |
| 6.4 | CRUD Axe1 | Avec filtre par section | CRUD complet |
| 6.5 | CRUD Axe2 | Avec filtre par axe1 | CRUD complet |
| 6.6 | CRUD Axe3 | Avec filtre par axe2 | CRUD complet |
| 6.7 | CRUD Types congés | Liste, create, edit, delete | CRUD complet |
| 6.8 | Configuration | Paramètres système | Édition OK |
| 6.9 | Restriction accès admin | Voter ou role | Non-admin refusé |
| 6.10 | Tests fonctionnels | WebTestCase | Tests passent |

#### Critère de fin de phase

Un admin peut :
1. Gérer tous les utilisateurs
2. Gérer la hiérarchie des axes
3. Configurer l'application

---

## Principes de développement

### Sécurité (OBLIGATOIRE)

1. **SQL** : TOUJOURS Doctrine (DQL/QueryBuilder), jamais de SQL brut
2. **CSRF** : Activé par défaut, ne pas désactiver
3. **Validation** : Constraints Symfony sur toutes les entities
4. **Auth** : Security bundle uniquement
5. **XSS** : Twig échappe par défaut, pas de `|raw`

### Code style

```
PHP:          PSR-12 (PHP-CS-Fixer)
Nommage:      camelCase (variables), PascalCase (classes), snake_case (BDD)
Commits:      feat:, fix:, refactor: + message en anglais
Tests:        Un test fonctionnel minimum par controller
```

### Conventions HTMX

- Fragments Twig préfixés par `_` (ex: `_fiche.html.twig`)
- `hx-push-url="true"` sur les navigations principales
- `hx-swap-oob="true"` pour mettre à jour plusieurs zones
- `hx-indicator` pour les spinners de chargement

---

## Commandes de référence

### Docker

```bash
docker compose up -d              # Démarrer
docker compose down               # Arrêter
docker compose logs -f php        # Logs PHP
docker compose exec php bash      # Shell PHP
docker compose exec db mysql -u linott -p linott  # MySQL
```

### Symfony

```bash
bin/console                                    # Liste commandes
bin/console make:entity                        # Créer entity
bin/console make:controller                    # Créer controller
bin/console make:migration                     # Générer migration
bin/console doctrine:migrations:migrate        # Exécuter migrations
bin/console doctrine:fixtures:load             # Charger fixtures
bin/console doctrine:schema:validate           # Valider schéma
bin/console cache:clear                        # Vider cache
```

### Tests

```bash
bin/phpunit                                    # Tous les tests
bin/phpunit tests/Controller/                  # Tests controllers
bin/phpunit --filter=AuthControllerTest        # Un test spécifique
vendor/bin/php-cs-fixer fix                    # Corriger style
vendor/bin/phpstan analyse                     # Analyse statique
```

---

## Notes pour Claude Code

### Contexte

- Développeur connaît PHP, découvre Symfony
- Entreprise utilise Symfony (projet Izzi) → alignement
- HTMX choisi pour éviter React et garder la logique côté serveur

### À éviter

- Sur-abstraction, over-engineering
- Bundles Symfony non nécessaires
- JavaScript complexe (HTMX + Alpine suffisent)
- `|raw` dans Twig sauf absolue nécessité

### À privilégier

- Conventions Symfony standard
- Code explicite
- Tests fonctionnels à chaque phase
- Fragments Twig réutilisables
- Commits atomiques

---

*Dernière mise à jour : 2026-02-03*
