# 📊 RAPPORT D'ÉVALUATION TECHNIQUE — ZeLibrary

**Date d'évaluation :** 6 octobre 2025  
**Projet :** zelibrary (Symfony 7.3 + Nuxt 4)  
**Temps investi par le candidat :** 27h30  
**Évaluateur :** AI Code Reviewer

---

## 1. EXECUTIVE SNAPSHOT

### ✅ Points forts

Stack technique moderne (Symfony 7.3 + API Platform 4, Nuxt 4), authentification JWT avec cookies HttpOnly bien implémentée, architecture RESTful cohérente, groupes de sérialisation pour contrôler l'exposition des données, fixtures complètes pour faciliter le développement, pagination et filtres API Platform fonctionnels, composant générique `ResourceCollection` réutilisable côté frontend.

### ⚠️ Risques majeurs

**Aucun test automatisé** (backend/frontend), problème de **N+1 queries potentiel** (relations ManyToMany sans EAGER ni optimisation explicite), **pas de CI/CD**, **reset-password sans validation** (endpoint public non sécurisé), **CORS hardcodé** pour localhost uniquement, **localStorage** pour l'état d'auth (risque XSS), **pas de rate limiting**, **secrets JWT exposés dans le dépôt** (corrigé tardivement mais présents dans l'historique Git).

### 🔧 Dette technique immédiate

Supprimer endpoint `/api/reset-password` non sécurisé, ajouter une suite de tests minimale (PHPUnit pour endpoints critiques), créer `.env.example` complets, documenter les étapes de génération des clés JWT, optimiser les requêtes N+1 avec EAGER fetch ciblé.

### 🎯 Verdict global

**⚠️ À CORRIGER (51.7%)** — Le socle technique est solide et fonctionnel, mais l'**absence totale de tests** et les **failles de sécurité** (reset-password, secrets dans Git) sont des bloqueurs pour une mise en production. Le candidat a bien maîtrisé les technologies et dépassé le brief (27h30 investies), mais a négligé les fondamentaux de sécurité et qualité. Potentiel confirmé, rigueur à renforcer.

---

## 2. GRILLE DE QUESTIONS PRIORISÉES POUR REVUE ORALE

### Critères de priorisation

- **🔴 CRITIQUE** : bloquant sécurité/données, impact production immédiat
- **🟠 IMPORTANT** : qualité logicielle, maintenabilité, scalabilité
- **🟢 NICE-TO-HAVE** : polish, DX, optimisations futures

---

## 🔴 CRITIQUE — Sécurité & Données

### Q1 : Endpoint `/api/reset-password` — Pourquoi est-il public et sans validation ?

**Pourquoi on demande ça :**  
Endpoint public permettant de changer le password de **n'importe quel utilisateur** sans authentification ni token. Faille de sécurité critique.

**Où regarder :**
- `backend/src/Controller/UserController.php:91-111`
- Route `POST /api/reset-password` (public, aucune sécurité)

**Critères d'acceptation :**
- Reconnaissance du problème de sécurité
- Solution proposée : système de token one-time envoyé par email, ou suppression complète de l'endpoint
- Comprendre que la production nécessite une vraie procédure de reset

**Red flags :**
- "C'est juste pour le dev" (mais l'endpoint est en prod)
- Aucune conscience du risque
- Justification "les utilisateurs sont honnêtes"

**Follow-up bonus :**
> Si vous deviez implémenter un vrai reset password, quelles étapes seraient nécessaires ?
>
> **Attendu :** Génération token aléatoire, stockage avec expiration, envoi email, validation token + changement password

---

### Q2 : Secrets JWT dans l'historique Git — Quelle est votre stratégie de gestion des secrets ?

**Pourquoi on demande ça :**  
Les clés JWT privées ont été commitées puis supprimées (commits `cf11b4e`, `df86cd8`, `00570fd`). Les secrets restent dans l'historique Git, compromettant potentiellement tous les tokens.

**Où regarder :**
- Historique Git (commits mentionnés)
- `backend/config/jwt/` (vide maintenant)
- `backend/.env.example:43-45` (passphrase exposée)

**Critères d'acceptation :**
- Reconnaissance du problème : historique Git = immuable
- Compréhension que les clés doivent être régénérées en production
- `.env.example` ne doit **jamais** contenir de vraies valeurs sensibles

**Red flags :**
- "J'ai supprimé le fichier, c'est bon"
- Pas de distinction dev/prod pour les secrets
- Passphrase laissée dans `.env.example`

**Follow-up bonus :**
> Comment géreriez-vous les secrets en production ?
>
> **Attendu :** Variables d'environnement, vault/secrets manager, rotation des clés

---

### Q3 : CORS hardcodé — Comment l'app fonctionnera en staging/production ?

**Pourquoi on demande ça :**  
CORS configuré uniquement pour `http://localhost:3000` en dur. Impossible de déployer sans modifier le code.

**Où regarder :**
- `backend/config/packages/nelmio_cors.yaml:4,10,13`
- Variable `CORS_ALLOW_ORIGIN` dans `.env` non utilisée

**Critères d'acceptation :**
- Comprendre que CORS doit être configurable par environnement
- Proposer d'utiliser `%env(CORS_ALLOW_ORIGIN)%` dans la config
- Savoir que `SameSite=None` + `Secure=true` requis en prod cross-domain

**Red flags :**
- "On changera en prod manuellement"
- Confusion sur le rôle de CORS
- Oubli du flag `Secure` en production

**Follow-up bonus :**
> Pourquoi les cookies doivent-ils être `Secure=true` et `SameSite=None` en prod si frontend et backend sont sur des domaines différents ?

---

### Q4 : Stockage de l'état d'auth dans localStorage — Quels sont les risques XSS ?

**Pourquoi on demande ça :**  
`useAuth.ts` stocke l'état `isAuth` et `user` dans localStorage. Vulnérable aux attaques XSS (script malveillant peut lire/modifier).

**Où regarder :**
- `frontend/app/composable/useAuth.ts:12,24-25,41-42`

**Critères d'acceptation :**
- Reconnaître que localStorage est accessible en JS (XSS)
- Alternative : Pinia avec `persistedState` dans un cookie HttpOnly, ou confiance uniquement au cookie JWT serveur
- Comprendre que l'état "isAuth" devrait être dérivé du cookie JWT

**Red flags :**
- "localStorage est safe"
- Pas de conscience des attaques XSS
- Stocker des tokens sensibles en localStorage

**Follow-up bonus :**
> Si un script malveillant s'exécute sur votre page, que peut-il faire avec localStorage ? Comment protégeriez-vous l'état d'authentification ?

---

## 🔴 CRITIQUE — Tests & Qualité

### Q5 : Absence totale de tests — Comment garantissez-vous la non-régression ?

**Pourquoi on demande ça :**  
Aucun test automatisé (backend/frontend). Impossible de refactorer ou d'ajouter des features sans risque.

**Où regarder :**
- `backend/tests/` → seulement Postman collection
- Pas de `phpunit.xml`, pas de tests dans `backend/tests/`
- Pas de tests Vitest/Playwright côté frontend

**Critères d'acceptation :**
- Reconnaître que les tests sont essentiels pour la maintenance
- Proposer une suite minimale : tests API Platform pour endpoints critiques (auth, reviews, collection)
- Comprendre le ROI : automatisation > tests manuels

**Red flags :**
- "Je teste à la main, c'est suffisant"
- "Pas le temps pour les tests" (27h30 passées)
- Aucune conscience de la dette technique

**Follow-up bonus :**
> Si vous deviez ajouter 3 tests prioritaires, lesquels choisiriez-vous ?
>
> **Attendu :** Auth JWT, création review avec contrainte unicité, sécurité endpoints protégés

---

## 🟠 IMPORTANT — Backend

### Q6 : N+1 queries sur Books → Authors/Categories — Avez-vous vérifié les performances ?

**Pourquoi on demande ça :**  
Relations ManyToMany sur `Book` sans stratégie EAGER explicite. Risque de N+1 queries lors de la récupération de la liste des livres.

**Où regarder :**
- `backend/src/Entity/Book.php:102-110` (ManyToMany sans fetch)
- API Platform charge les collections par défaut (LAZY)
- Méthodes `getAuthorsNames()` et `getCategoriesNames()` itèrent sur collections (lignes 245-264)

**Critères d'acceptation :**
- Conscience du problème N+1
- Solution : EAGER fetch ciblé, ou DTO/DataProvider avec joins optimisés
- Savoir utiliser Symfony Profiler pour vérifier les requêtes

**Red flags :**
- Aucune conscience du problème de performance
- "Les collections sont petites, pas grave"
- Ne pas connaître la différence EAGER/LAZY

**Follow-up bonus :**
> Comment vérifieriez-vous si votre API fait 50 requêtes SQL pour une liste de 10 livres ? Quelle stratégie d'optimisation appliqueriez-vous ?

---

### Q7 : Endpoint `/api/me` — Pourquoi construire manuellement le JSON au lieu d'utiliser les groupes de sérialisation ?

**Pourquoi on demande ça :**  
`UserController::me()` reconstruit manuellement le JSON au lieu de déléguer à API Platform + groupes de sérialisation.

**Où regarder :**
- `backend/src/Controller/UserController.php:25-59`
- Itérations manuelles lignes 34-49

**Critères d'acceptation :**
- Comprendre que API Platform + groupes = automatisation de la sérialisation
- Reconnaître la duplication avec les groupes existants (`user:me`)
- Proposer une opération API Platform dédiée

**Red flags :**
- "C'est plus simple comme ça"
- Ne pas utiliser les outils du framework
- Duplication de logique

**Follow-up bonus :**
> Comment transformeriez-vous cet endpoint en opération API Platform avec ses avantages (doc auto, cache, etc.) ?

---

### Q8 : Règle métier "1 review/livre/utilisateur" — Où est la contrainte DB ?

**Pourquoi on demande ça :**  
La contrainte d'unicité est vérifiée en PHP (`CreateReview:33-39`) mais **pas en base de données**.

**Où regarder :**
- `backend/src/Controller/Review/CreateReview.php:33-39`
- Migrations → pas de contrainte UNIQUE sur `(book_id, user_id)`
- `backend/src/Entity/Review.php` → pas d'annotation `UniqueEntity`

**Critères d'acceptation :**
- Reconnaître qu'une contrainte métier critique doit être en DB (race condition)
- Proposer migration ajoutant index UNIQUE sur `(book_id, user_id)`
- Annotation Symfony `UniqueEntity` pour validation

**Red flags :**
- "Le check en PHP suffit"
- Pas de conscience des race conditions
- Ne pas connaître les contraintes DB

**Follow-up bonus :**
> Que se passe-t-il si 2 requêtes simultanées créent une review pour le même livre/utilisateur ? Comment la contrainte DB résout ce problème ?

---

## 🟠 IMPORTANT — Frontend

### Q9 : Composable `useAuth` — Quelle est votre stratégie SSR/hydration ?

**Pourquoi on demande ça :**  
`useAuth` utilise `localStorage` (client-only), méthode `restore()` appelée manuellement. Risque de flash non-authentifié en SSR.

**Où regarder :**
- `frontend/app/composable/useAuth.ts:10-14`
- Check `typeof window` ligne 11
- Pas de middleware Nuxt pour restaurer l'état

**Critères d'acceptation :**
- Comprendre le problème SSR vs client
- Proposer un middleware Nuxt ou plugin client-only pour restaurer l'état
- Savoir que `useState` est persisté entre SSR et hydration

**Red flags :**
- Confusion sur SSR/CSR
- Ne pas connaître les lifecycles Nuxt
- "Ça marche chez moi"

**Follow-up bonus :**
> Comment gérez-vous l'affichage d'un état d'auth cohérent dès le premier render serveur ? Quelles alternatives au localStorage en SSR ?

---

### Q10 : Gestion d'erreur API — Où sont centralisées les erreurs 403/500 ?

**Pourquoi on demande ça :**  
`useErrorApiHandler` gère le 401 mais ignore 403/500. Pas de toasts/notifications globales.

**Où regarder :**
- `frontend/app/composable/useErrorApiHandler.ts:16-38`
- Seul 401 redirige vers login
- Pas de système de notification utilisateur

**Critères d'acceptation :**
- Reconnaître qu'un système de toasts/notifications améliore l'UX
- Proposer une stratégie pour 403 (message d'erreur) et 500 (retry/alerte)
- Centralisation avec un store Pinia

**Red flags :**
- "La console suffit"
- Pas de conscience de l'UX d'erreur
- Laisser l'utilisateur dans le flou

**Follow-up bonus :**
> Comment afficheriez-vous une notification "Erreur serveur, veuillez réessayer" sans modifier chaque composant ?

---

## 🟢 NICE-TO-HAVE — DevOps & DX

### Q11 : Pas de Makefile/script bootstrap — Comment simplifier l'onboarding ?

**Pourquoi on demande ça :**  
Instructions manuelles nombreuses (8 commandes). Pas de script "one-command".

**Où regarder :**
- `README.md:25-52` (étapes manuelles)
- Pas de `Makefile`, `setup.sh`, ou `npm run bootstrap`

**Critères d'acceptation :**
- Proposer un script unique (Makefile, npm script)
- Compréhension que DX = productivité
- Idéal : `make install` → tout configuré

**Red flags :**
- "Les docs sont assez claires"
- Ne pas valoriser l'automatisation
- Ne pas connaître Make/scripts

**Follow-up bonus :**
> Quelles étapes automatiseriez-vous dans un script `make setup` ?

---

### Q12 : Docker mentionné puis abandonné — Quelle était la difficulté ?

**Pourquoi on demande ça :**  
README mentionne explicitement l'abandon de Docker par manque de maîtrise.

**Où regarder :**
- `README.md:3` : "Docker volontairement exclu"
- `backend/compose.yaml` existe mais non documenté

**Critères d'acceptation :**
- Honnêteté sur les limites techniques
- Curiosité : quelles erreurs rencontrées ?
- Volonté d'apprendre

**Red flags :**
- Pas d'intérêt pour Docker
- "Docker c'est inutile"
- Pas de plan pour progresser

**Follow-up bonus :**
> Si vous deviez proposer un `docker-compose.yml` pour le projet, quels services incluriez-vous ?

---

## 3. CHALLENGE DES CHOIX DÉPASSANT LE BRIEF

Le brief initial demandait : **Liste livres, Auth CRUD Livres/Auteurs/Catégories**.  
Voici les ajouts non demandés analysés :

| Choix ajouté | Bénéfice utilisateur | Coût (complexité/maintenance) | Alternative plus simple | Reco finale |
|--------------|---------------------|-------------------------------|------------------------|-------------|
| **Collection personnelle d'utilisateur** (ajout/suppression livres) | ✅ Fort : fonctionnalité centrale, valeur immédiate | 🟠 Modéré : relation ManyToMany, endpoints dédiés, state provider custom | Liste de favoris côté frontend (localStorage) | **✅ GARDER** — Justifié, bien implémenté |
| **Système de reviews + notes** (création, suppression, calcul moyenne) | ✅ Fort : engagement utilisateur, social feature | 🟠 Modéré : contrainte unicité, entité Review, calcul dynamique | Pas d'alternative simple | **✅ GARDER** — Ajoute de la valeur, bien architecturé |
| **Entité Library + employés** (bibliothèques physiques, employés, rôles LIBRARY_EMPLOYEE) | 🔴 Faible : aucune feature front exploitant cette entité | 🔴 Élevé : complexité DB, migrations, fixtures, mais pas utilisé | Supprimer ou mettre en commentaire | **❌ RETIRER pour V1** — Scope creep, pas exploité, dette |
| **Rôles granulaires** (ADMIN, SUPER_ADMIN, LIBRARY_EMPLOYEE) | 🔴 Faible : pas de feature admin backend | 🟠 Modéré : fixtures, logique rôles | ROLE_USER uniquement pour V1 | **⚠️ SIMPLIFIER** — Garder ROLE_USER, retirer les autres |
| **Endpoint reset-password** | 🔴 Négatif : faille de sécurité | 🔴 Élevé : risque production | Supprimer complètement | **❌ RETIRER IMMÉDIATEMENT** |
| **Composant générique ResourceCollection** | ✅ Très fort : réutilisabilité, DRY, maintenabilité | ✅ Faible : bien abstraite, pas de surcoût | Dupliquer le code dans chaque page | **✅ GARDER** — Excellent choix architecture |
| **Calcul dynamique averageRate** (méthode PHP itérant reviews) | 🟠 Moyen : affichage note moyenne utile | 🔴 Élevé : N+1 potential, calcul non caché | Colonne SQL calculée, ou cache | **⚠️ OPTIMISER** — Garder mais ajouter cache/agrégat SQL |
| **Fixtures complètes** (20 users, multiples libraries, books, authors) | ✅ Fort : dev/démo facile, onboarding rapide | ✅ Faible : fichiers Fixtures bien structurés | Fixtures minimales (3 users, 5 books) | **✅ GARDER** — Excellent pour la démo |
| **Composition Docker** (fichiers présents mais non utilisés) | 🔴 Aucun : non fonctionnel | 🟠 Modéré : confusion dans le README | Supprimer les fichiers ou finaliser | **⚠️ NETTOYER** — Soit finir Docker, soit supprimer les fichiers |

### Synthèse des recommandations

**✅ À garder :**
- Collection personnelle
- Système de reviews
- Composant `ResourceCollection`
- Fixtures complètes

**⚠️ À simplifier :**
- Rôles (ROLE_USER uniquement pour V1)
- averageRate (ajouter cache ou agrégat SQL)

**❌ À retirer :**
- Entité Library/employés (non exploitée)
- Endpoint reset-password (sécurité)
- Fichiers Docker incomplets

### Analyse du temps investi

**Durée totale :** 27h30  
**Estimation sur features non exploitées :** ~4-5h (Library, rôles avancés, Docker)  
**Opportunité manquée :** Ce temps aurait pu être investi dans les **tests** (priorité critique absente)

---

## 4. PLAN DE CORRECTION PAR PHASES

### 📅 J+1 (Urgence critique — Bloquants production)

**Durée estimée :** 4-6h

| Tâche | Fichiers impactés | Mesure de succès |
|-------|-------------------|------------------|
| ✅ **Supprimer endpoint reset-password** | `UserController.php` | **FAIT** - Endpoint supprimé, nouveau système sécurisé implémenté |
| **Régénérer clés JWT en production** | `config/jwt/*.pem`, `.env` | Nouvelles clés non dans Git, doc mise à jour |
| **Ajouter contrainte UNIQUE Review(book_id, user_id)** | Migration Doctrine | Contrainte DB créée, test de conflit réussi |
| ✅ **Sécuriser CORS par variable d'env** | `nelmio_cors.yaml` | **FAIT** - Config utilise `%env(csv:CORS_ALLOW_ORIGIN)%` |
| ✅ **Remplacer localStorage par solution SSR-safe** | `useAuth.ts`, plugins, middleware | **FAIT** - État dérivé du cookie JWT HttpOnly |
| **Créer .env.example complets sans secrets** | `.env.example` (backend/frontend) | Aucune valeur sensible, instructions claires |
| ✅ **Tests critiques** (25 tests) | `backend/tests/` | **FAIT** - Suite complète : Auth, Reviews, Collection, Reset Password |

---

### 📅 Semaine 1 (Dette technique majeure)

**Durée estimée :** 12-16h

| Tâche | Fichiers impactés | Mesure de succès |
|-------|-------------------|------------------|
| **Retirer entité Library + relations** | Entities, Migrations, Fixtures | Migrations clean, fixtures allégées |
| **Simplifier rôles** (garder ROLE_USER uniquement) | User entity, Fixtures, Security | Rôles admin retirés, doc simplifiée |
| **Optimiser N+1 queries** (EAGER fetch ciblé) | Book entity, DataProvider | Symfony Profiler : max 5 requêtes pour liste 50 livres |
| ✅ **Remplacer localStorage auth par solution SSR-safe** | `useAuth.ts`, plugins, middleware | **FAIT** - Plugin auto-init, middleware protection routes |
| **Ajouter rate limiting** (login, register) | Security config, bundle | Max 5 tentatives/min, test de blocage |
| **Améliorer gestion erreurs API** | Composables, toast system | 403/500 affichent notification utilisateur |
| **Créer Makefile/script bootstrap** | `Makefile` ou `package.json` scripts | `make install` → app prête en 1 commande |

---

### 📅 Mois 1 (Qualité & Scalabilité)

**Durée estimée :** 30-40h

| Tâche | Fichiers impactés | Mesure de succès |
|-------|-------------------|------------------|
| **Suite de tests complète** (couverture 60%+) | Backend tests, Frontend e2e | CI vert, couverture mesurée |
| **Intégration CI/CD** (GitHub Actions) | `.github/workflows/*.yml` | Pipeline lint + test + deploy |
| **Optimiser averageRate** (colonne calculée ou cache) | Migration, Book entity | Calcul instantané même avec 1000 reviews |
| **Documentation API complète** (Swagger enrichi) | Annotations API Platform | Descriptions, exemples, erreurs documentées |
| **Monitoring & logs structurés** | Monolog config, Sentry | Erreurs 500 tracées, alertes configurées |
| **Finaliser ou supprimer Docker** | `compose.yaml` ou suppression | Docker fonctionnel OU fichiers supprimés |
| **Accessibilité (a11y)** frontend | Composants Vue | Tests axe-core PASS, navigation clavier OK |
| **Internationalisation (i18n)** | Nuxt i18n module | FR/EN disponibles |

---

## 5. GRILLE DE NOTATION (0–3)

### Échelle de notation

- **0** : Absent ou gravement défaillant
- **1** : Présent mais incomplet/problématique
- **2** : Satisfaisant avec réserves mineures
- **3** : Excellent, référence

### Évaluation détaillée

| Critère | Note | Justification | Poids |
|---------|------|---------------|-------|
| **1. Conformité au brief** | 3/3 | ✅ Toutes les fonctionnalités demandées présentes + dépassement | x2 |
| **2. Architecture & Structure** | 2/3 | ✅ Stack moderne, bien organisée<br>⚠️ Library/rôles non exploités (overengineering) | x2 |
| **3. Sécurité** | 0/3 | 🔴 Reset-password non sécurisé, secrets dans Git, localStorage auth, pas de rate limiting | x3 |
| **4. Qualité du code** | 2/3 | ✅ Groupes sérialisation, conventions Symfony<br>⚠️ Duplication (UserController::me), N+1 non traité | x2 |
| **5. Tests** | 0/3 | 🔴 Aucun test automatisé | x3 |
| **6. Documentation** | 2/3 | ✅ README clair, temps passé documenté<br>⚠️ Manque .env.example, instructions JWT incomplètes | x1 |
| **7. DevOps & DX** | 1/3 | ⚠️ Pas de CI/CD, pas de script bootstrap<br>🔴 Docker mentionné mais abandonné | x1 |
| **8. UX/UI** | 2/3 | ✅ Interface propre, composants réactifs, modal confirmation<br>⚠️ Pas de gestion erreurs visibles | x1 |
| **9. Performance** | 1/3 | ⚠️ N+1 queries non traité, averageRate calculé à chaque requête<br>✅ Pagination présente | x2 |
| **10. Maintenabilité** | 2/3 | ✅ Composable réutilisable, structure claire<br>⚠️ Pas de tests = refactoring risqué | x2 |
| **11. Gestion Git** | 1/3 | ✅ Commits cohérents, messages clairs<br>🔴 Secrets committés (même si supprimés après) | x1 |
| **12. Initiatives/Créativité** | 3/3 | ✅ Collection perso, reviews, composant générique, fixtures riches, effort de polish | x1 |

### Calcul du score global

**Score brut :**  
(3×2 + 2×2 + 0×3 + 2×2 + 0×3 + 2×1 + 1×1 + 2×1 + 1×2 + 2×2 + 1×1 + 3×1) = **31 / 60**

**Score pondéré :**  
31/60 × 100 = **51.7%**

### Barème d'évaluation

| Score | Verdict | Action |
|-------|---------|--------|
| 80-100% | ✅ Excellent | Validation immédiate |
| 60-79% | ✅ Satisfaisant | Validation avec réserves mineures |
| 40-59% | ⚠️ À corriger | Corrections obligatoires avant validation |
| 0-39% | ❌ Insuffisant | Refonte majeure nécessaire |

### VERDICT FINAL

**⚠️ À CORRIGER (51.7%)**

Le projet démontre une bonne maîtrise technique et une ambition louable, mais les **failles de sécurité critiques** et l'**absence totale de tests** nécessitent des corrections obligatoires avant toute validation.

---

## 6. SYNTHÈSE POUR L'ENTRETIEN ORAL

### Points à valoriser 👍

1. **Maîtrise technique confirmée**
   - Stack moderne parfaitement exploitée
   - Architecture RESTful propre et cohérente
   - Bonne compréhension de Symfony et Nuxt

2. **Ambition et dépassement**
   - Fonctionnalités à valeur ajoutée (collection, reviews)
   - Investissement conséquent (27h30)
   - Volonté de bien faire

3. **Qualité architecturale**
   - Composant `ResourceCollection` réutilisable (excellent)
   - Groupes de sérialisation bien utilisés
   - Séparation des préoccupations respectée

4. **Developer Experience**
   - Fixtures complètes facilitant la démo
   - Documentation du temps passé
   - Honnêteté sur les limites (Docker)

### Points à challenger fermement 🔍

1. **Sécurité catastrophique** 🔴
   - Endpoint reset-password public → **ZERO TOLÉRANCE**
   - Secrets JWT dans l'historique Git → compromission permanente
   - localStorage pour l'auth → vulnérabilité XSS
   - → **Question :** Comment évaluez-vous les risques de sécurité avant de déployer ?

2. **Absence totale de tests** 🔴
   - 27h30 investies, 0 test automatisé
   - Priorisation défaillante
   - → **Question :** Comment garantissez-vous la non-régression sans tests ?

3. **Overengineering non justifié** ⚠️
   - Entité Library/rôles avancés jamais exploités
   - ~4-5h perdues sur des features fantômes
   - → **Question :** Comment priorisez-vous vos développements face au temps limité ?

4. **Performance non vérifiée** ⚠️
   - N+1 queries probables, aucune mesure
   - Calcul dynamique averageRate non optimisé
   - → **Question :** Avez-vous utilisé le Symfony Profiler pour vérifier les requêtes SQL ?

### Question finale ouverte 💬

> **"Avec le recul, si vous aviez 6h supplémentaires : où les investiriez-vous et pourquoi ?"**

**Réponse attendue :**
- Prioriser les tests (sécurité endpoints, auth, reviews)
- Sécuriser reset-password
- Vérifier les performances avec le profiler

**🚩 Red flag :**
- Ajouter encore plus de features
- Ignorer les tests
- Ne pas mentionner la sécurité

### Recommandation finale

**Potentiel technique : Confirmé ✅**  
**Rigueur qualité/sécurité : À renforcer ⚠️**

Le candidat possède les compétences techniques nécessaires mais doit développer une conscience accrue des fondamentaux de sécurité et de la valeur des tests. Avec un encadrement adapté et une formation sur les bonnes pratiques, ce profil peut devenir très solide.

---

## 📋 ANNEXE : Checklist de correction prioritaire

### Bloquants critiques (à corriger immédiatement)

- [ ] Supprimer endpoint `/api/reset-password`
- [ ] Régénérer toutes les clés JWT pour la production
- [ ] Ajouter contrainte DB UNIQUE sur `review(book_id, user_id)`
- [ ] Configurer CORS via variables d'environnement
- [ ] Créer suite de tests minimale (3 tests critiques minimum)

### Dette technique majeure (semaine 1)

- [ ] Retirer entité Library et relations associées
- [ ] Simplifier système de rôles (ROLE_USER uniquement)
- [ ] Optimiser N+1 queries avec EAGER fetch
- [ ] Remplacer localStorage par solution SSR-safe
- [ ] Ajouter rate limiting sur login/register
- [ ] Créer script bootstrap (Makefile ou npm script)

### Améliorations qualité (mois 1)

- [ ] Couverture de tests > 60%
- [ ] Pipeline CI/CD fonctionnel
- [ ] Optimisation averageRate (cache ou SQL)
- [ ] Documentation API complète
- [ ] Monitoring et logs structurés
- [ ] Décision finale sur Docker (finir ou supprimer)

---

**Document généré le :** 6 octobre 2025  
**Version :** 1.0  
**Contact évaluateur :** AI Code Reviewer

