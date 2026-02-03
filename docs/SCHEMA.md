# Schéma de base de données

Documentation du Modèle Logique de Données (MLD) pour Linott et Qualitatmo.

---

## Base de données Linott

### Vue d'ensemble

| Table | Description | Enregistrements type |
|-------|-------------|---------------------|
| **Utilisateurs** | Employés de l'organisation | ~20-50 |
| **Groupes** | Groupes d'utilisateurs | ~5-10 |
| **Section** | Sections comptables (niveau 0) | ~6-12 |
| **Axe1** | Catégories principales (niveau 1) | ~15-40 |
| **Axe2** | Types d'activité (niveau 2) | ~50-100 |
| **Axe3** | Projets (niveau 3, optionnel) | ~20-60 |
| **Periodes** | Heures travaillées par jour | ~10k-100k |
| **CalendrierConges** | Événements du calendrier | ~1k-5k |
| **HeureSup** | Cumul heures sup par jour | ~5k-50k |
| **RachatHeures** | Historique des rachats | ~50-200 |
| **JoursTypes** | Modèles de journées | ~10-50 |
| **Configuration** | Paramètres système | ~25 |
| **zonesVacances** | Vacances scolaires importées | ~500 |
| **Calendar** | Table calendrier auxiliaire | ~3650 (10 ans) |

---

### Diagramme des relations

```
                         ┌────────────────┐
                         │  Configuration │
                         │  (paramètres)  │
                         └────────────────┘

┌────────────┐           ┌────────────────┐           ┌────────────────┐
│  Groupes   │◄─────────►│  Utilisateurs  │◄──────────│   JoursTypes   │
│            │  N:M      │                │  1:N      │ (modèles jour) │
└────────────┘           └───────┬────────┘           └────────────────┘
                                 │
                                 │ 1:N
            ┌────────────────────┼────────────────────┐
            │                    │                    │
            ▼                    ▼                    ▼
   ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
   │ CalendrierConges│  │    HeureSup     │  │  RachatHeures   │
   │  (événements)   │  │ (cumul H sup)   │  │   (rachats)     │
   └─────────────────┘  └─────────────────┘  └─────────────────┘


┌────────────┐
│  Section   │◄────────────────────────────────────────────────┐
│ (niveau 0) │                                                 │
└─────┬──────┘                                                 │
      │ 1:N                                                    │
      ├─────────────────┐                                      │
      │                 │                                      │
      ▼                 ▼                                      │
┌────────────┐    ┌────────────┐    ┌────────────┐            │
│    Axe1    │    │    Axe2    │    │    Axe3    │            │
│ (niveau 1) │    │ (niveau 2) │    │ (niveau 3) │            │
└─────┬──────┘    └─────┬──────┘    └─────┬──────┘            │
      │                 │                 │                    │
      └─────────────────┼─────────────────┘                    │
                        │ N:1                                  │
                        ▼                                      │
                 ┌─────────────────┐                           │
                 │    Periodes     │───────────────────────────┘
                 │ (heures/jour)   │
                 └─────────────────┘


┌─────────────────┐    ┌─────────────────┐
│  zonesVacances  │    │    Calendar     │
│ (vac. scolaires)│    │   (auxiliaire)  │
└─────────────────┘    └─────────────────┘
```

---

### Structure détaillée des tables

#### Utilisateurs

Gestion des employés de l'organisation.

```sql
CREATE TABLE IF NOT EXISTS `Utilisateurs` (
  `idUtilisateurs` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(45) DEFAULT NULL,
  `prenom` varchar(45) DEFAULT NULL,
  `nbrHeuresSemaine` decimal(4,2) NOT NULL,          -- Ex: 39.00, 35.00
  `nbrConges` decimal(4,2) DEFAULT NULL,             -- Jours de congés annuels
  `nbrRTT` decimal(4,2) DEFAULT NULL,                -- Jours de RTT annuels
  `login` varchar(45) NOT NULL,                      -- Identifiant unique
  `motDePasse` varchar(255) DEFAULT NULL,            -- Hash MD5
  `couleur` varchar(10) NOT NULL DEFAULT 'White',    -- Couleur calendrier (hex)
  `admin` tinyint(1) NOT NULL DEFAULT '0',           -- 1=admin, 0=user
  `active` tinyint(1) NOT NULL DEFAULT '1',          -- 1=actif, 0=désactivé
  `preferences_masque_id_axe1` varchar(512) DEFAULT NULL,   -- IDs à masquer (CSV)
  `preferences_masque_id_axe2` varchar(1024) DEFAULT NULL,
  `preferences_masque_id_axe3` varchar(2048) DEFAULT NULL,
  `afficher_astreintes` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idUtilisateurs`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Groupes

Regroupement d'utilisateurs (pour visibilité calendrier, etc.).

```sql
CREATE TABLE IF NOT EXISTS `Groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` char(45) NOT NULL,
  `idUtilisateurs` text,                -- Liste d'IDs séparés par virgule
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Section

Sections comptables de niveau 0 (regroupement d'axes).

```sql
CREATE TABLE IF NOT EXISTS `Section` (
  `idSection` int(11) NOT NULL AUTO_INCREMENT,
  `codeSection` varchar(8) NOT NULL,    -- Ex: "1", "2", "3"
  `nomSection` varchar(255) NOT NULL,   -- Ex: "MESURAGE ET METROLOGIE"
  PRIMARY KEY (`idSection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Axe1

Catégories principales de niveau 1.

```sql
CREATE TABLE IF NOT EXISTS `Axe1` (
  `idAxe1` int(11) NOT NULL AUTO_INCREMENT,
  `codeAxe1` varchar(45) NOT NULL,       -- Ex: "10", "11", "50"
  `nomAxe1` varchar(255) NOT NULL,       -- Ex: "Congés et autres absences"
  `Section_idSection` int(11) NOT NULL,  -- FK vers Section
  PRIMARY KEY (`idAxe1`),
  KEY `fk_idSection` (`Section_idSection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Axe2

Types d'activité de niveau 2.

```sql
CREATE TABLE IF NOT EXISTS `Axe2` (
  `idAxe2` int(11) NOT NULL AUTO_INCREMENT,
  `codeAxe2` varchar(45) NOT NULL,       -- Ex: "1100", "5001"
  `nomAxe2` varchar(255) NOT NULL,       -- Ex: "Congé", "RTT", "Maladie"
  `Section_idSection` int(11) NOT NULL,  -- FK vers Section
  PRIMARY KEY (`idAxe2`),
  KEY `fk_idSection` (`Section_idSection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Axe3

Projets de niveau 3 (optionnel, peut être désactivé).

```sql
CREATE TABLE IF NOT EXISTS `Axe3` (
  `idAxe3` int(11) NOT NULL AUTO_INCREMENT,
  `codeAxe3` varchar(10) NOT NULL DEFAULT '0',
  `nomAxe3` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idAxe3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Periodes

Table centrale : heures travaillées par période (jusqu'à 12 par jour).

```sql
CREATE TABLE IF NOT EXISTS `Periodes` (
  `idHoraires` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `horaireDebut` decimal(4,2) NOT NULL,   -- Ex: 8.00, 14.25
  `horaireFin` decimal(4,2) NOT NULL,     -- Ex: 12.00, 18.50
  `totalHoraire` decimal(4,2) DEFAULT NULL, -- Calculé: fin - début
  `Utilisateurs_idUtilisateurs` int(11) NOT NULL,
  `Utilisateurs_login` varchar(45) NOT NULL,
  `Section_idSection` int(11) NOT NULL,
  `Axe1_idAxe1` int(11) NOT NULL,
  `Axe2_idAxe2` int(11) NOT NULL,
  `Axe3_idAxe3` int(11) NOT NULL,
  `numeroLigne` int(11) NOT NULL,         -- 1-12 (position formulaire)
  PRIMARY KEY (`idHoraires`),
  KEY `fk_idUtilisateurs` (`Utilisateurs_idUtilisateurs`),
  KEY `fk_loginUtilisateurs` (`Utilisateurs_login`),
  KEY `fk_idSection` (`Section_idSection`),
  KEY `fk_idAxe1` (`Axe1_idAxe1`),
  KEY `fk_idAxe2` (`Axe2_idAxe2`),
  KEY `fk_idAxe3` (`Axe3_idAxe3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### CalendrierConges

Événements du calendrier (congés, RTT, événements, astreintes).

```sql
CREATE TABLE IF NOT EXISTS `CalendrierConges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Utilisateurs_idUtilisateurs` int(11) NOT NULL,
  `Utilisateurs_login` varchar(45) NOT NULL,
  `date` date NOT NULL,
  `periode` char(2) NOT NULL,            -- "JO"=journée, "AM"=matin, "PM"=après-midi
  `type` text NOT NULL,                  -- "absence", "event", "astreinte", "ferie"
  `valide` char(1) NOT NULL,             -- "N"=non validé, "V"=validé
  `description` text,                    -- "Congé", "RTT", "Maladie", etc.
  `indisponible` tinyint(1) NOT NULL DEFAULT '0',
  `bloquant` tinyint(1) NOT NULL DEFAULT '0',
  `commentaire` text,
  `id_utilisateurs_concernes` text,      -- Pour événements: liste IDs ou "ALL"
  `id_groupes_concernes` text,           -- Pour événements: liste IDs ou "ALL"
  `date_creation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_idUtilisateurs` (`Utilisateurs_idUtilisateurs`),
  KEY `fk_loginUtilisateurs` (`Utilisateurs_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### HeureSup

Cumul des heures supplémentaires par jour et utilisateur.

```sql
CREATE TABLE IF NOT EXISTS `HeureSup` (
  `idHeureSup` int(11) NOT NULL AUTO_INCREMENT,
  `Utilisateurs_idUtilisateurs` int(11) NOT NULL,
  `Utilisateurs_login` varchar(45) NOT NULL,
  `date` date NOT NULL,
  `nbrHeureSup` decimal(5,2) NOT NULL,   -- Peut être négatif
  `totalJournee` decimal(4,2) NOT NULL,  -- Total heures du jour
  PRIMARY KEY (`idHeureSup`),
  KEY `fk_idUtilisateurs` (`Utilisateurs_idUtilisateurs`),
  KEY `fk_loginUtilisateurs` (`Utilisateurs_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### RachatHeures

Historique des rachats d'heures supplémentaires.

```sql
CREATE TABLE IF NOT EXISTS `RachatHeures` (
  `idRachatHeure` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `nbr` decimal(5,2) NOT NULL,           -- Nombre d'heures rachetées
  `Utilisateurs_idUtilisateurs` int(11) NOT NULL,
  `Utilisateurs_login` varchar(45) NOT NULL,
  PRIMARY KEY (`idRachatHeure`),
  KEY `fk_idUtilisateurs` (`Utilisateurs_idUtilisateurs`),
  KEY `fk_loginUtilisateurs` (`Utilisateurs_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### JoursTypes

Modèles de journées réutilisables par utilisateur.

```sql
CREATE TABLE IF NOT EXISTS `JoursTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUtilisateur` int(11) NOT NULL,
  `nom` varchar(64) NOT NULL,
  `periode1` varchar(64) DEFAULT NULL,   -- Format: "debut,fin,section,axe1,axe2,axe3"
  `periode2` varchar(64) DEFAULT NULL,
  `periode3` varchar(64) DEFAULT NULL,
  `periode4` varchar(64) DEFAULT NULL,
  `periode5` varchar(64) DEFAULT NULL,
  `periode6` varchar(64) DEFAULT NULL,
  `periode7` varchar(64) DEFAULT NULL,
  `periode8` varchar(64) DEFAULT NULL,
  `periode9` varchar(64) DEFAULT NULL,
  `periode10` varchar(64) DEFAULT NULL,
  `periode11` varchar(64) DEFAULT NULL,
  `periode12` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Configuration

Paramètres système clé-valeur.

```sql
CREATE TABLE IF NOT EXISTS `Configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(64) NOT NULL,
  `valeur` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

**Paramètres disponibles :**

| Nom | Description | Valeurs typiques |
|-----|-------------|------------------|
| `affiche_axe3` | Afficher l'axe 3 dans l'interface | 0, 1 |
| `activer_axe3` | Activer la fonctionnalité axe 3 | 0, 1 |
| `afficher_heures_sup` | Afficher le compteur d'heures sup | 0, 1 |
| `nombre_jours_conges` | Congés annuels par défaut | 30 |
| `nombre_jours_RTT` | RTT annuels par défaut | 24 |
| `mois_depart_annee_conge` | Mois de début d'année congés | 1-12 |
| `mois_depart_annee_RTT` | Mois de début d'année RTT | 1-12 |
| `periode_gestion_RTT` | Mode de gestion RTT | "Annuel", "Trimestre" |
| `majoration_samedi` | Coefficient samedi | 1, 1.25, 1.5 |
| `majoration_dimanche` | Coefficient dimanche | 1, 1.5, 2 |
| `majoration_ferie` | Coefficient jours fériés | 1, 2 |
| `axe2_exclus_totaux` | Codes Axe2 exclus des totaux | "5004,5005" |
| `zone_vacances` | Zone académique | "A", "B", "C" |
| `afficher_jours_types` | Afficher les modèles de journées | 0, 1 |

#### zonesVacances

Vacances scolaires importées.

```sql
CREATE TABLE IF NOT EXISTS `zonesVacances` (
  `zone` varchar(24) NOT NULL,           -- "A", "B", "C", "Corse", etc.
  `dateDebut` date NOT NULL,
  `dateFin` date NOT NULL,
  PRIMARY KEY (`zone`, `dateDebut`, `dateFin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Calendar

Table auxiliaire de dates (pour requêtes calendaires).

```sql
CREATE TABLE IF NOT EXISTS `Calendar` (
  `cdate` date NOT NULL,
  `cday` int(2) unsigned NOT NULL,
  `cmonth` int(2) unsigned NOT NULL,
  `cyear` int(4) unsigned NOT NULL,
  PRIMARY KEY (`cdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

---

## Base de données Qualitatmo

Base de données séparée pour le module de gestion qualité.

### Vue d'ensemble

| Table | Description |
|-------|-------------|
| **fiche** | Fiches d'action qualité |
| **nature** | Types de problèmes par domaine |
| **marque** | Référentiel des marques |
| **materiel** | Référentiel des équipements |
| **type_materiel** | Types de matériel |
| **num_serie** | Numéros de série |
| **site** | Référentiel des sites/lieux |
| **type_action** | Types d'actions (Amélioration, Dysfonctionnement) |
| **users** | Utilisateurs Qualitatmo |
| **groups** | Groupes Qualitatmo |

### Structure détaillée

#### fiche

Fiches d'action qualité (non-conformités, améliorations).

```sql
CREATE TABLE IF NOT EXISTS `fiche` (
  `id_fiche` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(18) NOT NULL COMMENT 'YYYYMMDD-HH:mm-XXX',
  `date` date NOT NULL,
  `num_fiche_jour` smallint(6) NOT NULL,
  `type_fiche_action` varchar(14) NOT NULL,       -- "Amelioration", "Dysfonctionnement"
  `redacteur` varchar(16) NOT NULL,
  `id_nature` varchar(64) NOT NULL,
  `noms_natures` text NOT NULL,
  `marque_appareil` varchar(64) NOT NULL,
  `site` varchar(64) NOT NULL,
  `type_appareil` varchar(64) NOT NULL,
  `materiel_logiciel` varchar(64) NOT NULL,
  `numero_serie` varchar(32) NOT NULL,
  `faits` text NOT NULL,
  `causes` text NOT NULL,
  `consequences` text NOT NULL,
  `actions_court_terme` text NOT NULL,
  `incidence_qualite` varchar(24) NOT NULL,
  `commentaire_indice_qualite` text NOT NULL,
  `action_sur_produit` varchar(3) NOT NULL,       -- "oui", "non"
  `commentaire_action_sur_produit` text NOT NULL,
  `besoin_actions` text NOT NULL,
  `type_action_CPA` varchar(16) NOT NULL,
  `realisateur` varchar(16) NOT NULL,
  `delai` varchar(16) NOT NULL,
  `realisation` varchar(16) NOT NULL,
  `justificatifs` text NOT NULL,
  `efficacite` text NOT NULL,
  `cloture` varchar(16) NOT NULL,
  `visa_responsable` varchar(16) NOT NULL,
  `visa_direction` varchar(16) NOT NULL,
  UNIQUE KEY `nom` (`nom`),
  KEY `id_fiche` (`id_fiche`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

#### nature

Classification des types de problèmes par domaine.

```sql
CREATE TABLE IF NOT EXISTS `nature` (
  `ID_nature` smallint(4) NOT NULL AUTO_INCREMENT,
  `Nature` varchar(32) DEFAULT NULL,
  `Type` varchar(32) NOT NULL,            -- Domaine: "Mesurage", "Infrastructure", etc.
  `Createur` varchar(16) NOT NULL,
  `Date` date NOT NULL,
  UNIQUE KEY `Nature` (`Nature`),
  KEY `ID_nature` (`ID_nature`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

**Domaines (Type) :**
- Mesurage
- Infrastructure
- Etude
- Secretariat_comptabilite
- Qualite
- Modelisation
- Information_alerte
- Direction

#### marque / materiel / type_materiel / num_serie / site

Référentiels d'équipements.

```sql
-- Marques
CREATE TABLE IF NOT EXISTS `marque` (
  `ID_marque` smallint(6) NOT NULL AUTO_INCREMENT,
  `Nom_marque` varchar(32) NOT NULL,
  `Createur` varchar(16) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`ID_marque`),
  UNIQUE KEY `Nom_marque` (`Nom_marque`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Matériel
CREATE TABLE IF NOT EXISTS `materiel` (
  `ID_materiel` smallint(6) NOT NULL AUTO_INCREMENT,
  `Nom_materiel` varchar(32) NOT NULL,
  `Createur` varchar(16) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`ID_materiel`),
  UNIQUE KEY `Nom_marque` (`Nom_materiel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Types de matériel
CREATE TABLE IF NOT EXISTS `type_materiel` (
  `ID_type` smallint(6) NOT NULL AUTO_INCREMENT,
  `Nom_type` varchar(32) NOT NULL,
  `Createur` varchar(16) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`ID_type`),
  UNIQUE KEY `Nom_marque` (`Nom_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Numéros de série
CREATE TABLE IF NOT EXISTS `num_serie` (
  `ID_num_serie` smallint(6) NOT NULL AUTO_INCREMENT,
  `Num_serie` varchar(32) NOT NULL,
  `Createur` varchar(16) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`ID_num_serie`),
  UNIQUE KEY `Nom_marque` (`Num_serie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Sites
CREATE TABLE IF NOT EXISTS `site` (
  `ID_site` smallint(6) NOT NULL AUTO_INCREMENT,
  `Nom_site` varchar(32) NOT NULL,
  `Createur` varchar(16) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`ID_site`),
  UNIQUE KEY `Nom_marque` (`Nom_site`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

#### type_action

Types d'actions qualité.

```sql
CREATE TABLE IF NOT EXISTS `type_action` (
  `ID_type_action` tinyint(4) NOT NULL AUTO_INCREMENT,
  `type_action` varchar(18) NOT NULL,
  PRIMARY KEY (`ID_type_action`),
  UNIQUE KEY `type_action` (`type_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Valeurs par défaut
INSERT INTO `type_action` VALUES (1, 'Amelioration'), (2, 'Dysfonctionnement');
```

---

## Fichiers SQL disponibles

| Fichier | Description |
|---------|-------------|
| `Linott.20170116.localhost.sql` | Dump complet Linott (20 Mo, version 2017) |
| `Sources/Qualitatmo/BDD/qualitatmo.sql` | Dump Qualitatmo (schéma + données exemple) |

---

## Notes de migration

Le fichier `Sources/update.php` contient les scripts de migration pour :
- v2.0 : Renommage Projet → Axe3
- v3.0 : Nouvelle comptabilité analytique 2017 (nouveaux codes Section/Axe1/Axe2)

Les tables `*_pre_2017` sont des sauvegardes créées lors de la migration v3.0.
