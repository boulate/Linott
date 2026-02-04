# Premier chiffre = priorité (sur 5)
# B = BUG, F = FEATURE, A = Amélioration
# Si chiffre ensuite : Difficulté (sur 5)
# Commenter une ligne quand elle est traitée

#1B : Je ne peux plus ajouter de périodes dans la zone "admin" ou la zone "mes modèles". La fenetre modale ne s'ouvre pas.
#1F1 : Désactivons pour le moment le tableau de bord. Je n'en ai pas l'utilité. L'onglet par défaut devient "compta".
#1F1 : Le nom "Compta" n'a pas de sens pour l'onglet principal. Renomons le "Ventilation analytique"
2F2 : Ajouter des couleurs par sections. Les axes qui en découlent conservent ces couleurs. Proposer des couleurs par défaut (comme les labels, souvent utilisés dans notion ou autre), et proposer de créer d'autres couleurs également pour chaque section.
#1F3 : Les filtres ne doivent pas être excluants dans la selection des axes si je n'en ai pas selectionné plus en amont. Exemple : Tous les axes3 doivent apparaitre si je n'ai pas pris d'axe2, tous les axes2 apparaissent si je n'ai pas selectionné d'axe1, etc.
#1F2 : Je veux pouvoir changer les noms "Section, axe1, axe2 et axe3" dans l'adminitration, pour pouvoir coller au vocabulaire métier.
2F3 : Pouvoir avoir des codes comptables sur chaque section et chaque axe. Exemple : 23456 pour l'axe3 "Toto". Possibilité de selectionner l'axe en tapant directement son code comptable.
2F2 : Je veux pouvoir faire des équipes, et les utilisateurs doivent pouvoir faire partie d'une équipe (compta, TOOLIB, prod, etc.). Une équipe peut avoir plusieurs utilisateurs, et un utilisateur peut être dans plusieurs équipe.
2F3 : Dans les stats, il nous faut des filtres croisés pour pouvoir isoler les heures par personne, par équipe, par équipe sur un projet, par équipe sur un couple "Axe1 et axe2", etc.
2F2 : J'ai besoin d'un bouton du type "Warning" qui permet de dire "une semaine passée n'a pas été renseignée totalement". En cliquant dessus, cela nous emène sur le plus vieux jour de l'année en cours de consultation qui n'a pas été renseigné.
2F4 : J'ai besoin de me connecter à l'API du logiciel LUCCA pour aller chercher les informations d'absence (congé, maladie, RTT, temps partiel, etc.) des salariés. Aides moi à développer dans l'interface admin une zone qui permet de connecter un utilisateur au salarié qui lui correspond.
2F4 : Il faut que les données de Lucca pour un salarié permettent de remplir automatiquement les jours des utilisateurs liés à ce compte salarié. Exemple il est en congé le 02/02/2026 dans Lucca et a un contrat 39h -> 39/5 heures (donc 7.8) heures sur le 02/02/2026 avec le label "Congés" de Linott (il faudra donc pouvoir "lier" chaque type d'absence à un combo "section / axe1 / axe2 / axe3" dans l'administration également).
3F1 : Revoir la section administration pour séparer les liens par sections (Gestion des utilisateurs et equipes, une section pour la gestion des congés et des journées types, regrouper la gestion des sections et celle des axes, etc.)
2F3 : Possibilité de pouvoir décoréller un des axes de ses parents (exemple : axe3 devient indépendant et n'est filtré par ni section, ni axe1, ni axe 2).
2F3 : Je veux pouvoir activer désactiver l'axe 3. Comment construire tout cela pour que les données restent cohérentes ?



Je me suis mal exprimé : Section est le parent de Axe 1 qui est le parent de Axe 2 qui est le parent de Axe 3.
On est sur une relation 1 vers n.
Chaque enfant a un seul parent.
Chaque parent a plusieurs enfants.
Quand aucun parent n'est selectionné, je veux que tous les enfants apparaissent. Ca me permet par exemple de trouver rapidement ce que je veux dans axe2, et de voir axe1 et section se remplir. Il ne me reste alors plus que axe3 à renseigner.
Si je selectionne un parent, seuls ses enfants possibles apparaissent.
Si je selectionne un enfant, tous ses parents se selectionnent automatiquement.
Travaillons ensemble à la réflexion sur la meilleure facon d'implémenter cela, l'argorithme à mettre en place, etc.
J'ai beoin qu'on réfléchisse PROPREMENT avant de faire du code.
