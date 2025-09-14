# ✅ Plan de tests fonctionnels – Projet Ecoride

## US1 : Connexion / Inscription
- [x ] Un utilisateur peut créer un compte avec email + mot de passe valide
- [x] Un utilisateur ne peut pas s’inscrire avec un email déjà utilisé
- [x ] Un utilisateur peut se connecter avec ses identifiants
- [ ] Message d’erreur en cas de mauvais mot de passe
- [x ] Redirection vers la page d’accueil ou le dashboard après connexion

## US2 : Rôles utilisateurs
- [ ] L’utilisateur peut être « passager », « chauffeur » ou les deux
- [ ] Le rôle sélectionné s’affiche correctement dans son espace utilisateur
- [ ] Les fonctionnalités disponibles changent selon le rôle

## US3 : Ajout de véhicule
- [ ] Un chauffeur peut ajouter un véhicule avec plaque, marque, couleur, etc.
- [ ] Le nombre de places est requis
- [ ] Impossible de valider un formulaire avec un champ manquant

## US4 : Recherche de covoiturages
- [ ] Le formulaire de recherche fonctionne (adresse départ/arrivée + date)
- [ ] Les résultats s’affichent correctement
- [ ] Aucun résultat donne un message utilisateur
- [ ] Le filtre secondaire s’affiche uniquement si une recherche initiale a été faite

## US5 : Détail d’un covoiturage
- [ ] En cliquant sur un résultat, le détail s’affiche (chauffeur, date, véhicule…)
- [ ] Les commentaires et notes du chauffeur s’affichent depuis MongoDB

## US6 : Participer à un covoiturage
- [ ] Le bouton « Participer » s’affiche uniquement si : connecté + places dispo + crédits suffisants
- [ ] Une double confirmation est affichée avant validation
- [ ] Les crédits sont déduits
- [ ] Le nombre de places disponibles est mis à jour
- [ ] L’utilisateur voit le trajet dans son historique

## US7 : Crédits
- [ ] L’utilisateur voit son solde de crédits
- [ ] Les crédits sont bien débités à la participation
- [ ] Les crédits sont crédités si c’est le chauffeur
- [ ] Historique des crédits visible

## US8 : Espace utilisateur
- [ ] L’utilisateur voit ses informations
- [ ] Il peut modifier ses rôles
- [ ] Un chauffeur peut ajouter ses préférences
- [ ] Le formulaire d’ajout de véhicule est accessible

## US9 : Saisir un voyage
- [ ] Le formulaire est accessible si utilisateur est chauffeur
- [ ] Il est impossible de créer un covoiturage sans véhicule
- [ ] Le trajet est visible dans l’espace chauffeur

## US10 : Historique des covoiturages
- [ ] L’utilisateur voit les trajets passés (chauffeur ou passager)
- [ ] Il peut annuler un trajet
- [ ] Les crédits et places sont réajustés
- [ ] Les passagers reçoivent un mail si le chauffeur annule

## US11 : Laisser une note/commentaire
- [ ] Un passager peut laisser une note + commentaire après le trajet
- [ ] Le commentaire est bien stocké dans MongoDB
- [ ] Les moyennes de notes s’affichent dans les résultats de recherche

## US12 : Signaler un problème
- [ ] Le bouton « Signaler un problème » apparaît après le trajet
- [ ] Le formulaire de signalement fonctionne
- [ ] Les données sont bien enregistrées dans MongoDB
- [ ] L’employé peut voir les signalements dans son dashboard

## US13 : Espace employé / admin
- [ ] Création de comptes employés depuis l’admin
- [ ] L’employé voit les statistiques des trajets / crédits / utilisateurs
- [ ] L’employé peut suspendre un compte
- [ ] Les utilisateurs suspendus ne peuvent plus se connecter
- [ ] Les graphiques s’affichent (crédits par jour, trajets par jour)

## US14 : Sécurité / rôles / accès
- [ ] L’accès aux pages est bien restreint (admin / employé / user)
- [ ] Une erreur s’affiche si accès non autorisé
- [ ] Les routes sensibles sont protégées (CSRF + Auth)