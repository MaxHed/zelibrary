# 🌐 Configuration CORS - Correctif Appliqué

## ✅ Problème résolu

**Avant :** CORS hardcodé pour `http://localhost:3000` uniquement → impossible de déployer

**Après :** CORS configurable par variable d'environnement → déploiement flexible

---

## 📝 Modifications apportées

### 1. `config/packages/nelmio_cors.yaml`

```yaml
# AVANT (hardcodé ❌)
allow_origin: ['http://localhost:3000']
forced_allow_origin_value: 'http://localhost:3000'

# APRÈS (configurable ✅)
allow_origin: '%env(csv:CORS_ALLOW_ORIGIN)%'
```

### 2. `.env.example`

```bash
# Origins autorisées pour les requêtes CORS (séparées par des virgules)
CORS_ALLOW_ORIGIN=http://localhost:3000,http://127.0.0.1:3000
```

---

## 🚀 Configuration par environnement

### Développement local

Votre fichier `.env` ou `.env.local` :

```bash
CORS_ALLOW_ORIGIN=http://localhost:3000,http://127.0.0.1:3000
```

### Staging

Fichier `.env.staging` :

```bash
CORS_ALLOW_ORIGIN=https://staging-app.votre-domaine.com,https://staging-api.votre-domaine.com
```

### Production

Fichier `.env.prod` ou variables d'environnement serveur :

```bash
CORS_ALLOW_ORIGIN=https://app.zelibrary.com,https://www.zelibrary.com
```

---

## 🔧 Mise en place

### Étape 1 : Vérifier votre fichier .env

```bash
cd backend

# Si le fichier n'existe pas, le créer depuis l'exemple
cp .env.example .env

# Ou éditer le fichier existant
# Ajouter/vérifier la ligne CORS_ALLOW_ORIGIN
```

### Étape 2 : Configurer les origins

Éditez `backend/.env` et ajoutez :

```bash
###> nelmio/cors-bundle ###
CORS_ALLOW_ORIGIN=http://localhost:3000,http://127.0.0.1:3000
###< nelmio/cors-bundle ###
```

### Étape 3 : Vider le cache Symfony

```bash
cd backend
php bin/console cache:clear
```

### Étape 4 : Tester

```bash
# Démarrer le serveur
symfony serve --no-tls --port 8000

# Dans un autre terminal, tester CORS depuis le frontend
cd ../frontend
npm run dev
```

---

## 🧪 Vérification

### Test manuel avec curl

```bash
# Requête OPTIONS (preflight)
curl -X OPTIONS http://localhost:8000/api/books \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: GET" \
  -v

# Vérifier la présence de ces headers dans la réponse:
# Access-Control-Allow-Origin: http://localhost:3000
# Access-Control-Allow-Credentials: true
```

### Test dans le navigateur

```javascript
// Console du navigateur sur http://localhost:3000
fetch('http://localhost:8000/api/books', {
  credentials: 'include'
})
  .then(r => r.json())
  .then(data => console.log('✅ CORS OK', data))
  .catch(err => console.error('❌ CORS ERROR', err))
```

---

## 🔒 Sécurité en production

### ⚠️ Points importants

1. **HTTPS obligatoire**
   ```bash
   # En prod, utilisez TOUJOURS https://
   CORS_ALLOW_ORIGIN=https://app.zelibrary.com
   ```

2. **Cookies sécurisés**
   
   En production, les cookies JWT doivent être :
   - `Secure=true` (HTTPS uniquement)
   - `SameSite=None` (cross-origin)
   
   Voir `src/EventSubscriber/JWTCookieSubscriber.php` - déjà configuré ✅

3. **Pas de wildcard `*`**
   ```bash
   # ❌ DANGEREUX - N'importe quel site peut accéder à votre API
   CORS_ALLOW_ORIGIN=*
   
   # ✅ BON - Liste explicite des domaines autorisés
   CORS_ALLOW_ORIGIN=https://app.zelibrary.com,https://admin.zelibrary.com
   ```

4. **Domaines multiples**
   ```bash
   # Séparer par des virgules sans espaces
   CORS_ALLOW_ORIGIN=https://domain1.com,https://domain2.com,https://domain3.com
   ```

---

## 🐛 Dépannage

### Erreur : "CORS policy: No 'Access-Control-Allow-Origin' header"

**Cause :** Variable `CORS_ALLOW_ORIGIN` non définie ou mal formatée

**Solution :**
```bash
# Vérifier que la variable existe
grep CORS_ALLOW_ORIGIN backend/.env

# Vider le cache
php bin/console cache:clear

# Redémarrer le serveur
```

### Erreur : "The request includes 'credentials', but the Access-Control-Allow-Origin header is '*'"

**Cause :** Vous utilisez `*` avec `credentials: true`

**Solution :**
```bash
# Remplacer * par la liste explicite des domaines
CORS_ALLOW_ORIGIN=http://localhost:3000,http://127.0.0.1:3000
```

### Les cookies ne passent pas en cross-domain

**Vérifications :**
1. HTTPS en production (requis pour `SameSite=None`)
2. Origine exacte dans `CORS_ALLOW_ORIGIN` (pas de wildcard)
3. `credentials: 'include'` dans le fetch frontend
4. Cookie avec `Secure=true` et `SameSite=None` en prod

---

## 📚 Ressources

- [Documentation Symfony CORS](https://github.com/nelmio/NelmioCorsBundle)
- [MDN - CORS](https://developer.mozilla.org/fr/docs/Web/HTTP/CORS)
- [SameSite Cookies Explained](https://web.dev/samesite-cookies-explained/)

---

## ✅ Checklist de déploiement

- [x] Configuration CORS utilise des variables d'environnement
- [x] `.env.example` documenté avec exemples
- [ ] Variable `CORS_ALLOW_ORIGIN` définie dans `.env` local
- [ ] Cache Symfony vidé
- [ ] Tests CORS réussis en dev
- [ ] Configuration production avec HTTPS
- [ ] Cookies JWT avec `Secure=true` en prod
- [ ] Documentation mise à jour

---

**Date :** 6 octobre 2025  
**Status :** ✅ Correctif appliqué

