# 🔒 Nettoyage du dépôt avec `git filter-repo`

Ce dépôt a été nettoyé pour supprimer des fichiers sensibles (clés JWT utilisées uniquement pour une démo locale).

## 🧩 Contexte
Par erreur, des clés privées et publiques JWT ont été commit dans le dossier  
`backend/config/jwt/`.  
Même après suppression dans un commit ultérieur, ces fichiers restaient accessibles dans l’historique Git.

## 🧹 Solution appliquée
Le nettoyage a été effectué avec l’outil officiel [`git filter-repo`](https://github.com/newren/git-filter-repo), qui permet de réécrire complètement l’historique d’un dépôt.

### Commandes exécutées
```bash
# Exécution dans un clone mirror
git clone --mirror https://github.com/MaxHed/zelibrary.git
cd zelibrary.git

# Suppression du dossier contenant les clés JWT de tout l’historique
python -m git_filter_repo --force --path backend/config/jwt/ --invert-paths

# Réajout du remote et push forcé
git remote add origin https://github.com/MaxHed/zelibrary.git
git push --force --all
git push --force --tags
