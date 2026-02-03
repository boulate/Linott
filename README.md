# Linott

**Linott Is Not Only a Time Tracker**

Logiciel de comptabilité analytique des heures de travail, développé en PHP/MySQL au milieu des années 2000.

## Qu'est-ce que Linott ?

Linott est une application web permettant aux organisations de suivre précisément le temps passé par leurs employés sur différents projets et activités. Contrairement à un simple pointage, Linott offre une **classification multi-axes** permettant une analyse comptable fine des heures travaillées.

---

## Liste des fonctionnalités

### Module 1 : Authentification et sessions

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 1.1 | Login utilisateur | Connexion avec login/mot de passe | `index.php`, `authentification.php` |
| 1.2 | Déconnexion | Destruction de session | `deconnexion.php` |
| 1.3 | Gestion de session | Maintien de l'état connecté entre les pages | Sessions PHP |
| 1.4 | Protection des pages | Redirection vers login si non authentifié | Chaque fichier PHP |
| 1.5 | Rôles utilisateur | Distinction admin / utilisateur standard | `checkAdmin.php` |

### Module 2 : Saisie des heures (Compta)

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 2.1 | Calendrier de navigation | Sélection de date avec datepicker jQuery | `compta.php` |
| 2.2 | Navigation jour précédent/suivant | Boutons pour naviguer entre les jours | `compta.php` |
| 2.3 | Saisie des périodes | Formulaire heure début/fin par période | `compta.php` |
| 2.4 | Sélection Axe 1 | Liste déroulante des catégories principales | `compta.php`, `fenetre_choix_axe1.php` |
| 2.5 | Sélection Axe 2 | Liste déroulante des types d'activité | `compta.php`, `fenetre_choix_axe2.php` |
| 2.6 | Sélection Axe 3 | Liste déroulante des projets | `compta.php`, `fenetre_choix_axe3.php` |
| 2.7 | Sélection Section | Classification complémentaire optionnelle | `compta.php` |
| 2.8 | Calcul automatique des heures | Total calculé à partir heure début/fin | `compta.php` |
| 2.9 | Affichage 6 périodes matin | 6 lignes de saisie pour le matin | `compta.php` |
| 2.10 | Affichage 6 périodes après-midi | 6 lignes de saisie pour l'après-midi | `compta.php` |
| 2.11 | Enregistrement AJAX | Sauvegarde sans rechargement de page | `creer_fiche.php`, `modifier_fiche.php` |
| 2.12 | Suppression de période | Supprimer une ligne de saisie | `supprimer_periode.php` |
| 2.13 | Import fiche existante | Charger les données d'un jour déjà saisi | `importer_fiche.php` |
| 2.14 | Récupération fiche précédente | Copier les données de la veille | `compta.php` |
| 2.15 | Affichage total journée | Somme des heures du jour | `importer_dates.php` |
| 2.16 | Affichage total semaine | Somme des heures de la semaine | `importer_dates.php` |
| 2.17 | Affichage total mois | Somme des heures du mois | `importer_dates.php` |
| 2.18 | Affichage heures supplémentaires | Compteur d'heures sup cumulées | `importer_dates.php` |
| 2.19 | Aller au jour non validé | Navigation vers le plus ancien jour incomplet | `trouve_jour_non_valide.php` |
| 2.20 | Validation journée | Marquer un jour comme validé | `valider_fiche.php` |
| 2.21 | Validation semaine | Marquer toute la semaine comme validée | `validation_semaine.php` |
| 2.22 | Indicateur visuel validation | Couleur différente si jour validé/non validé | `compta.php` |
| 2.23 | Création jour type | Créer un modèle de journée réutilisable | `creer_jour_type.php` |
| 2.24 | Suppression jour type | Supprimer un modèle de journée | `supprimer_jour_type.php` |

### Module 3 : Calendrier et absences

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 3.1 | Vue mensuelle | Affichage calendrier du mois | `calendrier.php` |
| 3.2 | Navigation mois précédent/suivant | Boutons pour changer de mois | `calendrier.php` |
| 3.3 | Affichage événements | Visualisation des congés/RTT sur le calendrier | `calendrier.php` |
| 3.4 | Création événement congé | Déclarer un jour de congé | `creer_evenement_calendrier.php` |
| 3.5 | Création événement RTT | Déclarer un jour de RTT | `creer_evenement_calendrier.php` |
| 3.6 | Création événement récupération | Déclarer une récupération | `creer_evenement_calendrier.php` |
| 3.7 | Création événement demi-journée | Matin ou après-midi seulement | `creer_evenement_calendrier.php` |
| 3.8 | Modification événement | Changer le type ou la date | `modifier_calendrier.php` |
| 3.9 | Suppression événement | Annuler un congé/RTT | `supprimer_evenement_calendrier.php` |
| 3.10 | Validation congé | Approuver une demande d'absence | `validation_conge.php` |
| 3.11 | Drag & drop événements | Déplacer un événement à la souris | `calendrier.php` (jQuery UI) |
| 3.12 | Coloration jours fériés | Affichage différencié des jours fériés | `calendrier.php` |
| 3.13 | Import vacances scolaires | Charger les vacances depuis XML externe | `lister_jours_vacances.php` |
| 3.14 | Affichage "Qui est là" | Vue des présences/absences de l'équipe | `lister_qui_est_la.php` |
| 3.15 | Gestion astreintes | Marquer des périodes d'astreinte | `calendrier.php` |
| 3.16 | Compteur congés restants | Affichage solde de congés | `calendrier.php` |
| 3.17 | Compteur RTT restants | Affichage solde de RTT | `calendrier.php` |
| 3.18 | Synchronisation avec fiche heures | Création automatique de période sur absence | `calendrier.php` |

### Module 4 : Statistiques et rapports

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 4.1 | Stats par axe 1 | Répartition des heures par catégorie | `statsAxes.php` |
| 4.2 | Stats par axe 2 | Répartition des heures par type d'activité | `statsAxes.php` |
| 4.3 | Stats par axe 3 | Répartition des heures par projet | `statsAxes.php` |
| 4.4 | Filtre par période | Sélection mois ou plage de dates | `statsAxes.php` |
| 4.5 | Filtre par utilisateur | Stats d'un employé spécifique | `statsAxes.php` |
| 4.6 | Filtre combiné axes | Croiser plusieurs filtres | `statsAxes.php` |
| 4.7 | Stats congés | Vue des absences par utilisateur | `statsConges.php` |
| 4.8 | Graphiques tendance | Courbes d'évolution sur 6 mois | `pChart/` |
| 4.9 | Total heures par semaine | Graphique hebdomadaire | `statsAxes.php` |

### Module 5 : Export de données

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 5.1 | Choix type d'export | Interface de sélection du format | `choix_type_export.php` |
| 5.2 | Export type 1 | Somme heures par axe1/axe2/axe3 | `exporter_donnees.php` |
| 5.3 | Export type 2 | Heures par utilisateur et couple Axe1_Axe2 | `exporter_donnees.php` |
| 5.4 | Export type 3 | Pourcentage journalier par Axe1_Axe2 | `exporter_donnees.php` |
| 5.5 | Export type 4 | Pourcentage période par Axe1_Axe2 | `exporter_donnees.php` |
| 5.6 | Export type 5 | Pourcentage période par Axe1_Axe2_Axe3 | `exporter_donnees.php` |
| 5.7 | Export type 6 | Pourcentage journalier (axes exclus compris) | `exporter_donnees.php` |
| 5.8 | Export type 7 | Total journalier par employé | `exporter_donnees.php` |
| 5.9 | Export type 8 | Détail complet par jour et employé | `exporter_donnees.php` |
| 5.10 | Format CSV | Téléchargement fichier CSV | `exporter_donnees.php` |

### Module 6 : Administration

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 6.1 | Liste utilisateurs | Affichage de tous les utilisateurs | `administration.php` |
| 6.2 | Création utilisateur | Ajouter un nouvel employé | `creer_utilisateur.php` |
| 6.3 | Modification utilisateur | Éditer nom, prénom, heures/semaine | `modifier_utilisateur.php` |
| 6.4 | Suppression utilisateur | Désactiver un compte | `supprimer_utilisateur.php` |
| 6.5 | Reset mot de passe | Réinitialiser le password d'un user | `modifier_password.php` |
| 6.6 | Gestion groupes | Créer/modifier des groupes d'utilisateurs | `administration.php` |
| 6.7 | Affectation groupe | Associer utilisateurs à un groupe | `administration.php` |
| 6.8 | Liste axes 1 | Afficher toutes les catégories | `administration.php` |
| 6.9 | Création axe 1 | Ajouter une catégorie principale | `creer_axe1.php` |
| 6.10 | Modification axe 1 | Éditer nom et code | `modifier_axe1.php` |
| 6.11 | Suppression axe 1 | Supprimer une catégorie | `supprimer_axe1.php` |
| 6.12 | Liste axes 2 | Afficher tous les types d'activité | `administration.php` |
| 6.13 | Création axe 2 | Ajouter un type d'activité | `creer_axe2.php` |
| 6.14 | Modification axe 2 | Éditer nom et code | `modifier_axe2.php` |
| 6.15 | Suppression axe 2 | Supprimer un type | `supprimer_axe2.php` |
| 6.16 | Liste axes 3 | Afficher tous les projets | `administration.php` |
| 6.17 | Création axe 3 | Ajouter un projet | `creer_axe3.php` |
| 6.18 | Modification axe 3 | Éditer nom et code | `modifier_axe3.php` |
| 6.19 | Suppression axe 3 | Supprimer un projet | `supprimer_axe3.php` |
| 6.20 | Gestion sections | CRUD des sections | `administration.php` |
| 6.21 | Configuration jours fériés | Définir les jours non travaillés | `administration.php` |
| 6.22 | Import jours fériés | Importer depuis source externe | `administration.php` |
| 6.23 | Rachat heures sup | Gérer le solde d'heures sup | `rachat_heures.php` |
| 6.24 | Configuration congés annuels | Nombre de jours par défaut | `importer_configuration.php` |
| 6.25 | Configuration RTT | Nombre de RTT annuels/trimestriels | `importer_configuration.php` |
| 6.26 | Mois départ année comptable | Configurer l'exercice | `importer_configuration.php` |
| 6.27 | Axes 2 exclus des totaux | Définir les axes à ne pas compter | `importer_configuration.php` |
| 6.28 | Consultation fiches autres | Voir les fiches d'autres utilisateurs | `consulter_fiche.php` |

### Module 7 : Préférences utilisateur

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 7.1 | Changement mot de passe | Modifier son propre password | `preferences.php`, `modifier_password.php` |
| 7.2 | Axes favoris | Définir des axes par défaut | `preferences.php` |
| 7.3 | Masquer axes inutilisés | Cacher certains axes de l'affichage | `modifier_preferences_utilisateurs.php` |
| 7.4 | Couleur personnelle | Choisir sa couleur sur le calendrier | `preferences.php` |

### Module 8 : Qualitatmo (Gestion Qualité)

> **Note** : Module optionnel activable dans `Configuration/menu.php`

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 8.1 | Liste fiches actions | Afficher toutes les fiches qualité | `Qualitatmo/fiches_actions.php` |
| 8.2 | Création fiche action | Nouvelle fiche de non-conformité | `Qualitatmo/creer_fiche.php` |
| 8.3 | Modification fiche | Éditer une fiche existante | `Qualitatmo/modifier_fiche.php` |
| 8.4 | Validation fiche | Approuver/refuser une fiche | `Qualitatmo/valider_fiche.php` |
| 8.5 | Impression fiche | Version imprimable | `Qualitatmo/imprimer_fiche.php` |
| 8.6 | Section Nature | Classification du type de problème | `Qualitatmo/nature.php` |
| 8.7 | Natures par domaine | Direction, Étude, Mesurage, Qualité... | `Qualitatmo/ajouter_nature_*.php` |
| 8.8 | Section Description | Détail du problème | `Qualitatmo/description.php` |
| 8.9 | Gestion matériel | Association avec équipement concerné | `Qualitatmo/materiel.php` |
| 8.10 | Marques matériel | Référentiel des marques | `Qualitatmo/ajouter_marque.php` |
| 8.11 | Types matériel | Référentiel des types | `Qualitatmo/ajouter_type_materiel.php` |
| 8.12 | Numéros de série | Gestion des N° série | `Qualitatmo/ajouter_num_serie.php` |
| 8.13 | Gestion sites | Référentiel des sites/lieux | `Qualitatmo/ajouter_site.php` |
| 8.14 | Section Analyse/Action | Définir les actions correctives | `Qualitatmo/analyse_action.php` |
| 8.15 | Création action | Nouvelle action corrective | `Qualitatmo/nouvelle_action.php`, `creer_action.php` |
| 8.16 | Import action | Charger une action existante | `Qualitatmo/importer_action.php` |
| 8.17 | Filtrage fiches | Recherche multicritère | `Qualitatmo/filtre.php` |
| 8.18 | Statistiques qualité | Tableaux de bord qualité | `Qualitatmo/statistiques.php` |
| 8.19 | Statuts fiche | En attente / Validé / Refusé | `Qualitatmo/valider_formulaire.php` |
| 8.20 | Base de données séparée | Schema dédié Qualitatmo | `Qualitatmo/BDD/qualitatmo.sql.gz` |

### Module 9 : Technique / Transverse

| ID | Fonctionnalité | Description | Fichiers concernés |
|----|----------------|-------------|-------------------|
| 9.1 | Validation des entrées | Contrôle des saisies utilisateur | `verifier_input_php.php`, `verifier_input_javascript.js` |
| 9.2 | Connexion base de données | Pool de connexion PDO | `connexion_base.php` |
| 9.3 | Configuration centralisée | Paramètres clé-valeur en base | `importer_configuration.php` |
| 9.4 | Migrations BDD | Scripts de mise à jour du schéma | `update.php` |
| 9.5 | Menu général | Navigation principale | `menu_general.php` |
| 9.6 | Thème jQuery UI Delta | Interface utilisateur cohérente | `CSS/Delta/` |
| 9.7 | Graphiques pChart | Génération de graphiques | `pChart/` |

---

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Backend | PHP (procédural) |
| Base de données | MySQL/MariaDB |
| Frontend | XHTML 1.0 Strict |
| JavaScript | jQuery 1.8.3, jQuery UI |
| Graphiques | pChart |
| Styles | CSS personnalisé + thème jQuery UI Delta |

## Architecture

```
Sources/
├── index.php                 # Page de login
├── compta.php                # Saisie des heures (module principal)
├── calendrier.php            # Gestion des congés et absences
├── administration.php        # Panneau d'administration
├── statsAxes.php             # Statistiques par axes
├── statsConges.php           # Statistiques congés
├── preferences.php           # Préférences utilisateur
├── choix_type_export.php     # Sélection format d'export
├── exporter_donnees.php      # Génération des exports CSV
├── update.php                # Migrations de base de données
├── Configuration/
│   ├── bdd.php               # Paramètres de connexion MySQL
│   └── menu.php              # Configuration du menu
├── CSS/                      # Thème jQuery UI
├── Images/                   # Icônes et images
├── pChart/                   # Librairie de graphiques
└── Qualitatmo/               # Module qualité (optionnel)
    ├── fiches_actions.php    # Liste des fiches
    ├── creer_fiche.php       # Création
    ├── modifier_fiche.php    # Modification
    ├── valider_fiche.php     # Validation
    └── BDD/                  # Schema dédié
```

## Modèle de données

### Tables principales (Linott)

- **Utilisateurs** : Employés avec login/password
- **Periodes** : Heures saisies par jour, utilisateur et axe
- **Axe1, Axe2, Axe3** : Classifications analytiques
- **Section** : Classification complémentaire
- **CalendrierConges** : Événements (congés, RTT, récup...)
- **HeureSup** : Gestion des heures supplémentaires
- **RachatHeures** : Historique des rachats
- **JoursTypes** : Jours fériés et spéciaux
- **Groupes** : Groupes d'utilisateurs
- **Configuration** : Paramètres clé-valeur

### Tables Qualitatmo

- **fiche** : Fiches d'action qualité
- **action** : Actions correctives/préventives
- **materiel** : Équipements concernés
- **nature_*** : Référentiels par domaine
- **site** : Lieux/sites

> **Documentation détaillée** : Voir [`docs/SCHEMA.md`](docs/SCHEMA.md) pour les CREATE TABLE complets, les relations entre tables et les paramètres de configuration.

## Installation

### Prérequis

- Serveur web avec PHP 5.x+
- MySQL ou MariaDB
- Extensions PHP : mysql/mysqli, session, gd (pour pChart)

### Configuration

1. Copier les sources sur le serveur web

2. Configurer la base de données dans `Sources/Configuration/bdd.php` :
```php
$host_BDD = "localhost";
$login_BDD = "linott";
$password_BDD = "votre_mot_de_passe";
$name_BDD = "Linott";
```

3. Créer la base de données et lancer les migrations via `/update.php`

4. Configurer le menu dans `Sources/Configuration/menu.php` :
```php
$afficher_menu_fiches_actions = true; // Pour activer Qualitatmo
```

5. Pour Qualitatmo : importer `Sources/Qualitatmo/BDD/qualitatmo.sql`

### Contraintes métier importantes

Pour le bon fonctionnement des automatismes :
- L'Axe1 "Congés et autres absences" doit avoir le code **50**
- L'Axe2 code **5000** doit s'appeler "Absences"
- Les Axe2 pour congés/RTT/récupération doivent porter ces noms exacts

## Historique

Linott a été développé au milieu des années 2000 comme outil de gestion du temps pour des organisations françaises. Il a été vendu à plusieurs dizaines d'entreprises et utilisé en production pendant de nombreuses années.

### Versions notables

- **v1.0** : Version initiale
- **v2.0** : Refonte des axes analytiques
- **v3.0** : Améliorations du calendrier et des statistiques

## Licence

Logiciel propriétaire - Tous droits réservés

## Notes techniques

Ce logiciel représente les pratiques de développement web PHP des années 2005-2010 :
- Architecture procédurale (pas de POO)
- Requêtes SQL directes (PDO)
- JavaScript avec jQuery 1.x
- Authentification par sessions PHP et MD5

Il témoigne d'une époque où ces choix techniques étaient standards et fonctionnels pour des applications métier de gestion.
