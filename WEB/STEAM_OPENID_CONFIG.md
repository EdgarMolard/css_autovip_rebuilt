# Configuration de l'Authentification Steam OpenID

Cette documentation explique comment configurer l'authentification Steam OpenID pour votre système AutoVip.

## Fichiers modifiés/créés

1. **steam_openid.php** - Classe pour gérer l'authentification OpenID 2.0 avec Steam
2. **login.php** - Page de connexion remplacée pour utiliser Steam OpenID
3. **steam_callback.php** - Page de callback qui gère la réponse de Steam
4. **pages/inscription/formulaire.php** - Page d'inscription simplifiée (enregistrement automatique)
5. **logout.php** - Page de déconnexion
6. **configuration.php** - Ajout des paramètres de configuration Steam

## Configuration requise

### 1. Obtenir une clé API Steam

1. Allez sur https://steamcommunity.com/dev/apikey
2. Connectez-vous avec votre compte Steam
3. Acceptez l'accord de service
4. Copier votre clé API

### 2. Configurer les paramètres dans configuration.php

Ouvrez le fichier `configuration.php` et modifiez les paramètres Steam:

```php
define("STEAM_API_KEY", "VOTRE_CLE_API_STEAM"); // Remplacez par votre clé API
define("STEAM_OPENID_ENABLED", 1); // 1 = Activé, 0 = Désactivé
```

### 3. Vérifier la variable URL_SITE

Assurez-vous que la variable `URL_SITE` dans `configuration.php` est correctement définie:

```php
define("URL_SITE", "https://vip.lastfate.fr"); // Remplacez par votre URL
```

**Important:** L'URL doit correspondre exactement à celle définie dans votre navigateur (HTTPS ou HTTP).

## Flux d'authentification

### Première connexion (nouvel utilisateur)

1. L'utilisateur clique sur le bouton "Se connecter via Steam"
2. Il est redirigé vers Steam pour autoriser l'application
3. Steam vérifie l'identité et renvoie le Steam ID
4. L'application crée automatiquement un compte utilisateur:
   - Pseudo: nom Steam
   - Email: steam_<STEAM64ID>@steam.local
   - Steam ID: au format STEAM_1:X:Y:Z
   - Mot de passe: généré automatiquement
5. Un email de bienvenue est envoyé (si MAIL_INSCRIPTION = 1)
6. L'utilisateur est connecté et redirigé vers son compte

### Connexion ultérieure

1. L'utilisateur clique sur le bouton "Se connecter via Steam"
2. Il autorise l'application (ou accepte directement si déjà autorisé)
3. Steam renvoie le Steam ID
4. L'application trouve le compte existant
5. L'utilisateur est connecté

## Changement de mot de passe

Avec l'authentification Steam, les utilisateurs n'ont plus de formulaire d'inscription ou de formulaire "mot de passe oublié" traditionnel.

**Pour changer de mot de passe:** Les utilisateurs peuvent utiliser la commande `/pw` sur les serveurs de jeu (si implémentée), ou contacter l'administrateur.

## Pages modifiées

### login.php
- Remplacé par une page avec un seul bouton Steam
- Plus d'authentification par email/mot de passe
- Messages d'erreur en français et anglais

### pages/inscription/formulaire.php
- Remplacée par une page d'information
- Explique l'enregistrement automatique via Steam
- Bouton pour rediriger vers la connexion Steam

### configuration.php
- Ajout de deux nouvelles constantes pour Steam

## Sécurité

- Les mots de passe des utilisateurs sont générés de manière cryptographique (`random_bytes()`)
- Les sessions sont validées par IP
- Les requêtes à Steam OpenID utilisent HTTPS
- Les Steam IDs sont validés et normalisés

## Dépannage

### "Erreur lors de la validation avec Steam"

- Vérifiez que `URL_SITE` dans configuration.php correspond à votre URL réelle
- Assurez-vous que votre serveur peut accéder à `https://steamcommunity.com`
- Vérifiez que PHP a les fonctions `file_get_contents` ou `cURL` activées

### Utilisateurs ne peuvent pas se connecter

- Vérifiez que la clé API Steam est correctement configurée
- Vérifiez les logs d'erreur PHP
- Assurez-vous que la table `af_users` existe et a les bonnes colonnes

### Les comptes ne sont pas créés

- Vérifiez que les droits d'écriture dans la base de données sont corrects
- Vérifiez que `MAIL_INSCRIPTION` ne cause pas d'erreurs (mails non envoyés)
- Vérifiez les logs MySQL

## Désactiver Steam OpenID

Pour revenir à l'ancien système d'authentification, modifiez configuration.php:

```php
define("STEAM_OPENID_ENABLED", 0);
```

Et restaurez les anciens fichiers `login.php` et `formulaire.php` à partir de votre backup.

## Support

Pour plus d'informations sur Steam OpenID:
- Documentation Steam: https://steamcommunity.com/dev
- Spécifications OpenID 2.0: http://openid.net/
