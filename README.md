# IGS Sécurité — Site web (PHP + PostgreSQL) pour Render

## Déploiement sur Render
1. Mettez ce dossier sur **GitHub** (nouveau dépôt privé, tout le contenu à la racine).
2. Sur Render : **New → Blueprint** → choisissez le dépôt. `render.yaml` crée :
   - le site web (Docker/PHP/Apache) ;
   - la base **PostgreSQL** ;
   - les variables `DATABASE_URL` (automatique) et `INSTALL_KEY` (générée).
3. Quand le déploiement est terminé, ouvrez Render → service `igs-securite` → **Environment**, copiez la valeur de `INSTALL_KEY`.
4. Allez sur `https://VOTRE-SITE.onrender.com/install.php?key=VALEUR_INSTALL_KEY` et créez le compte **super administrateur**. La page se verrouille ensuite toute seule.
5. Connexion : `https://VOTRE-SITE.onrender.com/admin/login.php`

Nom de domaine perso : Render → Settings → Custom Domains.

## Rôles
- **Administrateur** : demandes de devis (statuts, notes, export CSV), gestion des services, mot de passe.
- **Super administrateur** : tout ce qui précède + comptes administrateurs, paramètres du site (téléphones, email, adresse, textes), journal d'activité.

## Notes
- Offre gratuite Render : le site s'endort après inactivité (premier chargement lent) et la base gratuite expire après 30 jours. Pour un site professionnel, passez en offre payante (web *Starter* + base *Basic*).
- Les demandes de devis sont enregistrées en base et visibles dans l'admin (l'envoi d'email par `mail()` n'existe pas sur Render).
- Images : `assets/img/`.
