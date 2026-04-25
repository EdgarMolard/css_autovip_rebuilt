# AutoVip - Système de Gestion VIP Multi-Serveurs

## 📋 Vue d'ensemble

AutoVip est une plateforme web complète de gestion de comptes VIP pour serveurs de jeu (CS:GO, Source Engine, etc.). Elle permet aux utilisateurs d'acheter des droits VIP sur vos serveurs via PayPal, StarPass, ou Allopass, avec un système d'authentification sécurisé via Steam OpenID.

## ✨ Fonctionnalités principales

- **Authentification Steam OpenID** - Connexion et enregistrement automatiques via Steam
- **Gestion des tokens VIP** - Système de crédits pour les accès VIP
- **Paiements intégrés** - Support PayPal, StarPass, Allopass
- **Multi-serveurs** - Gestion de plusieurs serveurs de jeu
- **Administration complète** - Interface admin pour gérer les utilisateurs, logs, et serveurs
- **Support multilingue** - Interface en français et anglais
- **Système de logs** - Historique complet des actions

## 🏗️ Structure du projet

```
Autovip/
├── GAME/                           # Plugins SourceMod
│   ├── Time_Players.sql           # Scripts SQL pour les joueurs
│   └── addons/sourcemod/
│       ├── plugins/               # Fichiers compilés (.smx)
│       └── scripting/             # Fichiers sources (.sp)
│           └── simple_admin/      # Module admin personnalisé
│
└── WEB/                            # Application web PHP
    ├── configuration.php           # Configuration générale du site
    ├── fonctions.php               # Fonctions utilitaires
    ├── bdd.sql                     # Schéma de base de données
    ├── index.php                   # Page d'accueil
    ├── login.php                   # Page de connexion (Steam OpenID)
    ├── logout.php                  # Page de déconnexion
    ├── steam_openid.php            # Classe d'authentification OpenID
    ├── steam_callback.php          # Callback de réponse Steam
    │
    ├── css/                        # Feuilles de style
    │   ├── reset.css
    │   ├── style.css
    │   └── ie.css
    │
    ├── js/                         # Scripts JavaScript
    │   ├── jquery-1.4.4.min.js
    │   ├── jquery-custom.js
    │   └── blazy.js
    │
    ├── img/                        # Images et icônes
    │   ├── favicon.ico
    │   ├── icon/                   # Icônes (drapeaux, etc.)
    │   ├── regles/                 # Images des règles
    │   └── smileys/                # Émoticônes
    │
    └── pages/                      # Pages de contenu
        ├── accueil.php             # Accueil
        ├── erreur.php              # Page d'erreur
        ├── menu_gauche.php         # Menu latéral
        ├── selection_page.php      # Routeur de pages
        │
        ├── admin/                  # Pages d'administration
        │   ├── client_list.php
        │   ├── client_edit.php
        │   ├── client_suspend.php
        │   ├── liste_vip.php
        │   ├── news_add.php
        │   ├── news_edit.php
        │   ├── news_gestion.php
        │   ├── server_add.php
        │   └── server_gestion.php
        │
        ├── compte/                 # Pages utilisateur
        │   ├── main.php
        │   ├── credits.php
        │   ├── droit_list.php
        │   ├── historique.php
        │   └── droit_list(Vip par serveur).php
        │
        ├── inscription/            # Inscription (auto via Steam)
        │   └── formulaire.php
        │
        ├── paiements/              # Gestion des paiements
        │   ├── paypal.php
        │   ├── paypal_notification.php
        │   ├── starpass.php
        │   ├── allopass.php
        │   ├── rentabiliweb_callback.php
        │   ├── protection_starpass.php
        │   └── mini_token.php
        │
        ├── guest/                  # Pages publiques
        │   ├── aide.php
        │   ├── avantages.php
        │   ├── charte.php
        │   ├── pwr.php
        │   ├── staff.php
        │   └── topconnected.php
        │
        └── fonctions/              # Modules fonctionnels
            └── bbcode.php          # Parser BBCode
```

## 🔐 Configuration - Authentification Steam OpenID

### 1. Obtenir une clé API Steam

1. Allez sur https://steamcommunity.com/dev/apikey
2. Connectez-vous avec votre compte Steam
3. Acceptez l'accord de service
4. Copiez votre clé API

### 2. Configurer les paramètres

Éditez `WEB/configuration.php` et configurez:

```php
// Configuration Steam OpenID
define("STEAM_API_KEY", "VOTRE_CLE_API_STEAM"); // Clé API Steam
define("STEAM_OPENID_ENABLED", 1);              // 1 = Activé, 0 = Désactivé

// URL de votre site (IMPORTANT: doit être HTTPS ou HTTP, pas les deux)
define("URL_SITE", "https://vip.lastfate.fr");

// Configuration des mails
define("MAIL_INSCRIPTION", 1);      // Envoyer email à l'inscription
define("MAIL_AUTEUR", "AutoVip");   // Nom de l'auteur
define("MAIL_REPLY", "noreply@example.com");

// Configuration de la base de données
define("SQL_HOST", 'localhost');
define("SQL_USER", 'Autovip');
define("SQL_BDD", 'Autovip');
define("SQL_PASSWORD", 'Autovip');
```

### 3. Flux d'authentification

#### Première connexion (nouvel utilisateur)

1. Utilisateur clique sur "Se connecter via Steam"
2. Redirection vers Steam pour autorisation
3. Steam vérifie l'identité et renvoie le Steam ID
4. **Compte créé automatiquement:**
   - Pseudo: nom Steam
   - Email: `steam_<STEAM64ID>@steam.local`
   - Steam ID: format `STEAM_1:X:Y:Z`
   - Mot de passe: généré cryptographiquement
5. Email de bienvenue envoyé (si configuré)
6. Utilisateur connecté et redirigé vers son compte

#### Connexion ultérieure

1. Utilisateur clique sur "Se connecter via Steam"
2. Autorise (ou accepte directement si déjà autorisé)
3. Steam retourne le Steam ID
4. Compte trouvé et utilisateur connecté

### 4. Sécurité

- ✓ Mots de passe générés cryptographiquement (`random_bytes()`)
- ✓ Sessions validées par IP
- ✓ Requêtes HTTPS vers Steam
- ✓ Steam IDs validés et normalisés
- ✓ Protection contre les injections SQL

## 💳 Configuration des paiements

### PayPal

```php
define("PAYPAL_PAIEMENT", 1);
define("PAYPAL_MAIL", "paypal@example.com");
define("PAYPAL_CURRENCY", "EUR");
define("PAYPAL_NOM_PRODUIT_1", "Créditation 1 token");
define("PAYPAL_PRIX_1", "1.25");
define("PAYPAL_TOKEN_1", 1);
```

### StarPass

```php
define("STARPASS_PAIEMENT", 1);
define("STARPASS_IDP", '61332');
define("STARPASS_IDD", '397915');
define("STARPASS_CODETEST", "test_code");
```

### Allopass

```php
define("ALLOPASS_PAIEMENT", 0);
define("ALLOPASS_IDS", '341287');
define("ALLOPASS_IDD", '1497877');
```

## 🎮 Configuration SourceMod

Les plugins SourceMod permettent aux utilisateurs VIP d'utiliser leurs droits directement sur les serveurs:

- **simple_admin.smx** - Système d'administration basé sur les droits VIP
- **csgo_timer.smx** - Système de timer pour les maps
- **AntiBhop.smx** - Anti-bhop pour les serveurs

Modifiez les fichiers `.sp` pour adapter les commandes et droits à vos besoins.

## 🗄️ Base de données

Le schéma SQL complet est fourni dans `WEB/bdd.sql`. Tables principales:

- `af_users` - Comptes utilisateurs
- `af_users_vip` - Droits VIP par utilisateur
- `af_logs` - Historique des actions
- `af_servers` - Liste des serveurs
- `af_paiements` - Historique des paiements

## 📊 Fonctionnalités Admin

Les administrateurs peuvent:
- ✓ Gérer les utilisateurs (créer, modifier, suspendre)
- ✓ Gérer les droits VIP
- ✓ Afficher les logs d'actions
- ✓ Gérer les serveurs
- ✓ Ajouter/modifier les news
- ✓ Consulter l'historique des paiements

Les niveaux d'admin vont de 1 (Admin) à 5 (Vice-Président), plus ROOT pour l'accès complet.

## 🚀 Installation

### Prérequis

- PHP 5.6+ (PHP 7+ recommandé)
- MySQL/MariaDB 5.5+
- Serveur web (Apache, Nginx, etc.)
- HTTPS recommandé
- Compte Steam pour la clé API

### Étapes

1. **Clonez le repository:**
   ```bash
   git clone https://github.com/EdgarMolard/css_autovip_rebuilt.git
   ```

2. **Configurez la base de données:**
   - Créez une base de données MySQL
   - Importez `WEB/bdd.sql`

3. **Configurez PHP:**
   - Éditez `WEB/configuration.php`
   - Entrez vos identifiants MySQL
   - Entrez votre clé API Steam
   - Vérifiez l'URL du site

4. **Configurez les droits d'accès:**
   - Assurez-vous que `WEB/` est accessible par le serveur web
   - Vérifiez les permissions des dossiers

5. **Installez les plugins SourceMod** (optionnel):
   - Compilez les fichiers `.sp` ou utilisez les `.smx` pré-compilés
   - Placez-les dans le dossier `addons/sourcemod/plugins/` de vos serveurs
   - Configurez les droits dans le fichier de config de SourceMod

## 🔧 Dépannage & Debug Steam

### 🆕 Outils de Debugging (NOUVEAU)

J'ai ajouté un système complet de logging et debugging pour l'authentification Steam:

#### Page de Debug Steam (Localhost uniquement)
**URL:** `http://localhost/debug_steam.php`

Cette page affiche:
- ✓ État de la configuration (API Key, Base de données)
- 📋 Logs Steam en temps réel (50 dernières entrées)
- ⚠️ Logs d'erreurs PHP
- 📝 Dernières inscriptions
- 🧪 Test de l'API Steam (vérifier qu'une Steam64 ID est valide)

**Protection:** Accessible uniquement depuis localhost (127.0.0.1)

#### Fichier de logs Steam
**Chemin:** `WEB/logs/steam_errors.log`

Format:
```
[2026-04-25 14:23:45] IP: 192.168.1.100 - Validation successful - Steam64: 76561198..., Steam32: STEAM_1:0:...
[2026-04-25 14:23:48] IP: 192.168.1.100 - Steam validation failed - GET params: {...}
```

### ❌ Erreurs courantes et solutions

#### "Erreur lors de la validation avec Steam"
**Causes possibles:**
1. `URL_SITE` dans `configuration.php` ne correspond pas au domaine exact (HTTP vs HTTPS)
2. Le serveur ne peut pas accéder à `https://steamcommunity.com` (pare-feu/proxy)
3. PHP n'a pas `file_get_contents` activé

**Solutions:**
- Vérifiez `URL_SITE` dans `configuration.php` (ex: `https://vip.monsite.fr`, pas de slash final)
- Testez la connectivité: `curl -I https://steamcommunity.com`
- Consultez `/WEB/logs/steam_errors.log` pour plus de détails
- Accédez à `http://localhost/debug_steam.php` pour voir les logs détaillés

#### "Impossible de récupérer vos informations Steam"
**Causes:**
1. API Key Steam invalide ou non configurée
2. Profil Steam du joueur est privé
3. Problème de connectivité avec l'API Steam

**Solutions:**
- Vérifiez `STEAM_API_KEY` dans `configuration.php`
- Le profil Steam doit être public (pas privé)
- Testez l'API via la page debug: cliquez sur "🧪 Test Steam API"

#### "La validation avec Steam a échoué"
**Causes:**
1. Paramètres OpenID invalides
2. Réponse Steam corrompue/incomplète
3. Session expire

**Solutions:**
- Consultez les logs: `/WEB/logs/steam_errors.log`
- Assurez-vous que les sessions PHP sont activées
- Essayez à nouveau - peut être un timeout transitoire

#### "Erreur lors de la création de votre compte"
**Causes:**
1. Erreur base de données (permissions manquantes)
2. Pseudo en doublon (très rare)

**Solutions:**
- Vérifiez les permissions MySQL: `GRANT ALL ON Autovip.* TO 'Autovip'@'localhost'`
- Consultez les logs détaillés: `/WEB/logs/steam_errors.log`
- Vérifiez les logs MySQL

#### Utilisateurs ne peuvent pas se connecter
- Vérifiez que la clé API Steam est correctement configurée
- Consultez `/WEB/logs/steam_errors.log` pour le message d'erreur exact
- Vérifiez que la table `af_users` existe: `mysql -u Autovip -p Autovip -e "SHOW TABLES;"`

#### Les comptes ne sont pas créés
- Vérifiez les permissions de la base de données
- Vérifiez les logs MySQL: `tail -f /var/log/mysql/error.log`
- Vérifiez la configuration des mails et que `MAIL_INSCRIPTION = 1`

#### Les mails ne sont pas envoyés
- Vérifiez que `MAIL_INSCRIPTION = 1` dans `configuration.php`
- Vérifiez que `php.ini` a SMTP configuré
- Consultez les logs PHP pour les erreurs mail
- Testez avec: `php -r "mail('test@example.com', 'Test', 'Test'); echo 'Sent';"`

## 📝 Fichiers importants

| Fichier | Description |
|---------|-------------|
| `configuration.php` | Configuration globale du site |
| `fonctions.php` | Fonctions utilitaires et helpers |
| `steam_openid.php` | Classe d'authentification Steam |
| `steam_callback.php` | Callback de réponse Steam |
| `bdd.sql` | Schéma de base de données |
| `index.php` | Point d'entrée principal |
| `pages/selection_page.php` | Routeur de pages |

## 🔄 Workflow Git

Pour contribuer:

```bash
git clone https://github.com/EdgarMolard/css_autovip_rebuilt.git
cd css_autovip_rebuilt
git checkout -b ma-feature
# Faites vos modifications
git add .
git commit -m "Description claire des changements"
git push origin ma-feature
# Créez une Pull Request sur GitHub
```

## 📄 Licence

Voir le fichier `licence` pour plus de détails.

## 📞 Support

Pour toute question ou problème:
- Consultez la documentation Steam: https://steamcommunity.com/dev
- Vérifiez les spécifications OpenID 2.0: http://openid.net/
- Consultez les logs d'erreur de votre serveur

## 🎯 Améliorations futures possibles

- [ ] API REST pour les applications mobiles
- [ ] Support OAuth 2.0
- [ ] Dashboard statistiques avancées
- [ ] Intégration Discord
- [ ] Support multilingue supplémentaire
- [ ] Système de referral

---

**Dernier commit:** Implémentation complète de l'authentification Steam OpenID
**Version:** 1.0.0
**Auteur:** Edgar Molard
