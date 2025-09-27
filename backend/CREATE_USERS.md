# Création d'utilisateurs d'exemple

Cette commande crée automatiquement 4 utilisateurs d'exemple pour tester l'application ZeLibrary avec les 4 types d'utilisateurs définis.

## Utilisation

### Création des 4 utilisateurs (sans paramètres)
```bash
php bin/console app:create-example-users
```

C'est tout ! Aucun paramètre nécessaire.

## Utilisateurs créés

La commande crée exactement 4 utilisateurs :

### 👑 Administrateur (Admin)
- **Email** : `admin@zelibrary.com`
- **Mot de passe** : `admin123`
- **Rôle** : `ROLE_ADMIN`

### 🔥 Super Administrateur (SuperAdmin)
- **Email** : `superadmin@zelibrary.com`
- **Mot de passe** : `admin123`
- **Rôle** : `ROLE_SUPER_ADMIN`

### 📚 Employé de bibliothèque (LibraryEmployee)
- **Email** : `bibliothecaire@zelibrary.com`
- **Mot de passe** : `employee123`
- **Rôle** : `ROLE_LIBRARY_EMPLOYEE`

### 👤 Utilisateur lambda (User)
- **Email** : `user@example.com`
- **Mot de passe** : `user123`
- **Rôle** : `ROLE_USER`

## Fonctionnalités

- **Hachage sécurisé** : Les mots de passe sont automatiquement hachés avec Symfony PasswordHasher
- **Gestion des doublons** : Les utilisateurs existants sont ignorés automatiquement
- **Création simple** : Aucun paramètre nécessaire, juste exécuter la commande
- **Tableau de connexion** : Affichage des informations de connexion après création

## Exemple de sortie

```
Création des 4 utilisateurs d'exemple
=====================================

Utilisateur créé : admin@zelibrary.com (Administrateur)
Utilisateur créé : superadmin@zelibrary.com (Super Administrateur)
Utilisateur créé : bibliothecaire@zelibrary.com (Bibliothécaire)
Utilisateur créé : user@example.com (Utilisateur)

4 utilisateurs créés avec succès !

Informations de connexion
-------------------------
+----------------------------+-------------+----------+----------------------------+
| Email                      | Mot de passe| Type     | Rôles                       |
+----------------------------+-------------+----------+----------------------------+
| admin@zelibrary.com        | admin123    | Admin    | ROLE_ADMIN                 |
| superadmin@zelibrary.com   | admin123    | SuperAdmin | ROLE_SUPER_ADMIN         |
| bibliothecaire@zelibrary.com | employee123 | LibraryEmployee | ROLE_LIBRARY_EMPLOYEE |
| user@example.com           | user123     | User     | ROLE_USER                  |
+----------------------------+-------------+----------+----------------------------+
```

## Sécurité

⚠️ **Important** : Ces utilisateurs sont destinés uniquement au développement et aux tests. 

## Migration de base de données

Assurez-vous d'avoir exécuté les migrations avant d'utiliser cette commande :

```bash
php bin/console doctrine:migrations:migrate
```
