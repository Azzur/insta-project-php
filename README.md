#Outil de plannification

L'école INSTA souhaite développer un outil d'aide à la planification.

Les plannings sont effectués par promo. Chaque promo dispose de trois types de plannings:

 - Planning de synthèse (modèle en annexe) comportant les dates de présence en formation
 - Planning détaillé (modèle en annexe) comportant pour chaque date de formation:
    - La date 
    - L'intitulé du cours
    - La durée du cours (en heure ou en demi-journée) pour chaque séance
    - Le lieu (Parmentier, Jussieu, autres)
    - La ou les Salle(s)
    - Le ou les Professeur(s) – un prof principal et un prof assistant 
    - La date 

- Planning mensuel(en annexe)

L'outil doit être capable de gérer:

- les conflits relatifs aux : 
- site (deux séances successives ne peuvent pas s dérouler sur deux sites éloignés)
- occupations de salles (deux cours dans une même salle)
- capacité des salles (le nombre d'élèves dans une promo doit être inférieur au nombre de sièges dans une salle)
- ordinateurs (on doit avoir plus de PC que d'élèves)
- charges des professeurs (on ne peut pas avoir un professeur sur deux sites ou dans deux salles,…)

On peut imaginer, dans une première version, que le système ne soit bloquant. Un warning est affiché dans les cas des conflits.

On peut prévoir:

- la gestion de la périodicité des cours (au lieu de saisir dix fois par exemple la même chose)
- des filtres 
- une fonction qui vérifie la cohérence d'un planning sur les points précédents (site, périodicité, ...)
- une fonction qui propose sous certaines contraintes de proposer un planning général (de synthèse), telles que le nombre de salles, la périodicité, etc.
- la programmation des soutenances
- L'invitation des tuteurs aux soutenances
- Affichage des horaires de passage en soutenance

En extension au conflit relatif à la gestion des ordinateurs, on peut gérer les élèves d'une promo qui préfèrent travailler sur leur propre ordinateur.

L'idée est qu'en positionnant la souris sur une rubrique, on obtient une petite fenêtre qui fournirait quelques informations de synthèse sur la rubrique en question.

En double cliquant sur une rubrique, on obtient une fenêtre avec plus de détails.

- La planification se fait par promo; elle peut être répétitive sur plusieurs semaines (même cours sur plusieurs semaines)
- En cliquant sur un cours on peut avoir accès à:
    - Programme général
    - Découpage pédagogique
    - Objectif du cours
    - Un lien au support de cours (avant le cours ou après)
    - Un lien vers les sujets TP, les corrigés (ne sont visibles que lorsque le professeur le décide)
    - Un lien vers le projet
    - Organise les soutenances par groupe
    - Etc…

- En cliquant sur un site, on a son adresse, le numéro de téléphone, le métro, le plan d'accès, le contact, son numéro de téléphone, son mail, etc…
- L'information doit être disponible sur le web
- On peut envoyer ce planning par mail
- L'outil doit pouvoir recenser: les professeurs (…), les sites (…), les salles (…), les horaires (…), les promos, les jours fériés, les vacances scolaires, les soutenances, etc…

***

Travail demandé :

- Planification
- Livrables
    - [Conception générale](/Specifications.md)
    - [Spécifications fonctionnelles](/Features.md)
    - [Spécifications techniques](/Technicals.md)
    - [Manuel d'installation](/Installation.md)
- Tests
- Mise en ligne
