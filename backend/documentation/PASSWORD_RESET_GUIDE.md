# 🔒 Guide du Reset Password Sécurisé

## Vue d'ensemble

Le système de reset password a été complètement refactorisé pour corriger la **faille de sécurité critique** de l'ancien endpoint `/api/reset-password`.

### ⚠️ Ancien système (RETIRÉ)
- ❌ Endpoint public permettant de changer n'importe quel mot de passe
- ❌ Aucune validation ni authentification
- ❌ Énumération d'utilisateurs possible

### ✅ Nouveau système (SÉCURISÉ)
- ✅ Token unique à usage unique avec expiration (1 heure)
- ✅ Validation côté serveur
- ✅ Protection contre l'énumération d'utilisateurs
- ✅ Prêt pour l'envoi d'email (à configurer)

---

## Architecture

### Nouveaux champs dans l'entité User

```php
#[ORM\Column(type: 'string', length: 64, nullable: true)]
private ?string $resetPasswordToken = null;

#[ORM\Column(type: 'datetime_immutable', nullable: true)]
private ?\DateTimeImmutable $resetPasswordTokenExpiresAt = null;
```

### Endpoints disponibles

#### 1. Demande de reset : `POST /api/password-reset/request`
#### 2. Confirmation du reset : `POST /api/password-reset/confirm`

---

## Flux utilisateur

```mermaid
sequenceDiagram
    participant U as Utilisateur
    participant F as Frontend
    participant B as Backend
    participant M as Email (à implémenter)
    
    U->>F: Clique "Mot de passe oublié"
    F->>B: POST /api/password-reset/request {email}
    B->>B: Génère token unique (32 bytes)
    B->>B: Stocke token + expiration (1h)
    B->>M: Envoie email avec lien + token
    B->>F: 200 OK (message générique)
    
    Note over M,U: Email reçu avec lien
    
    U->>F: Clique sur le lien dans l'email
    F->>F: Page de reset avec token dans l'URL
    U->>F: Entre nouveau mot de passe
    F->>B: POST /api/password-reset/confirm {token, newPassword}
    B->>B: Valide token (existence + expiration)
    B->>B: Change le password + supprime le token
    B->>F: 200 OK
    F->>F: Redirection vers /login
```

---

## Utilisation

### Étape 1 : Demander un reset

**Endpoint :** `POST /api/password-reset/request`

**Body :**
```json
{
  "email": "utilisateur@example.com"
}
```

**Réponse (toujours 200, même si l'email n'existe pas) :**
```json
{
  "message": "Si cet email existe, un lien de réinitialisation a été envoyé."
}
```

**En mode DEV uniquement :**
```json
{
  "message": "Si cet email existe, un lien de réinitialisation a été envoyé.",
  "dev_token": "a1b2c3d4e5f6...",
  "dev_warning": "Token visible uniquement en mode dev"
}
```

### Étape 2 : Confirmer le reset

**Endpoint :** `POST /api/password-reset/confirm`

**Body :**
```json
{
  "token": "a1b2c3d4e5f6...",
  "newPassword": "MonNouveauMotDePasse123!"
}
```

**Réponse succès (200) :**
```json
{
  "message": "Mot de passe réinitialisé avec succès"
}
```

**Réponse erreur (400) :**
```json
{
  "error": "Token invalide ou expiré"
}
```

---

## Tests manuels

### 1. Test du flux complet

```bash
# 1. Demander un reset
curl -X POST http://localhost:8000/api/password-reset/request \
  -H "Content-Type: application/json" \
  -d '{"email":"jean.dupont@email.com"}'

# Réponse en dev : copier le dev_token

# 2. Confirmer le reset avec le token
curl -X POST http://localhost:8000/api/password-reset/confirm \
  -H "Content-Type: application/json" \
  -d '{"token":"VOTRE_TOKEN_ICI","newPassword":"nouveaupass123"}'

# 3. Tester la connexion avec le nouveau password
curl -X POST http://localhost:8000/api/login_check \
  -H "Content-Type: application/json" \
  -d '{"email":"jean.dupont@email.com","password":"nouveaupass123"}'
```

### 2. Test du token expiré

```bash
# Dans MySQL/PostgreSQL, modifier manuellement l'expiration
UPDATE user 
SET reset_password_token_expires_at = DATE_SUB(NOW(), INTERVAL 2 HOUR) 
WHERE email = 'jean.dupont@email.com';

# Tenter de confirmer le reset
curl -X POST http://localhost:8000/api/password-reset/confirm \
  -H "Content-Type: application/json" \
  -d '{"token":"TOKEN_EXPIRÉ","newPassword":"nouveaupass123"}'

# Attendu : {"error":"Token invalide ou expiré"}
```

### 3. Test de l'énumération d'utilisateurs (sécurité)

```bash
# Email qui existe
curl -X POST http://localhost:8000/api/password-reset/request \
  -H "Content-Type: application/json" \
  -d '{"email":"jean.dupont@email.com"}'

# Email qui N'existe PAS
curl -X POST http://localhost:8000/api/password-reset/request \
  -H "Content-Type: application/json" \
  -d '{"email":"nexistepas@example.com"}'

# Les deux doivent retourner le MÊME message (protection anti-énumération)
```

---

## Configuration de l'envoi d'email (TODO)

### Option 1 : Symfony Mailer + SMTP

```bash
composer require symfony/mailer
```

**Configuration `.env` :**
```env
MAILER_DSN=smtp://user:pass@smtp.example.com:587
```

**Service d'envoi :**
```php
// src/Service/EmailService.php
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

public function sendPasswordResetEmail(User $user, string $token): void
{
    $resetUrl = "https://votre-frontend.com/reset-password?token={$token}";
    
    $email = (new Email())
        ->from('noreply@zelibrary.com')
        ->to($user->getEmail())
        ->subject('Réinitialisation de votre mot de passe')
        ->html("
            <h1>Réinitialisation de mot de passe</h1>
            <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
            <a href='{$resetUrl}'>Réinitialiser mon mot de passe</a>
            <p>Ce lien est valide pendant 1 heure.</p>
            <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
        ");
    
    $this->mailer->send($email);
}
```

### Option 2 : Service tiers (SendGrid, Mailgun, etc.)

Suivre la documentation Symfony : https://symfony.com/doc/current/mailer.html

---

## Implémentation Frontend

### Page de demande de reset

```vue
<!-- pages/forgot-password.vue -->
<template>
  <div class="max-w-md mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Mot de passe oublié</h1>
    
    <form @submit.prevent="handleSubmit">
      <input
        v-model="email"
        type="email"
        placeholder="Votre email"
        required
        class="w-full border rounded px-3 py-2 mb-4"
      />
      
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded"
      >
        {{ loading ? 'Envoi...' : 'Recevoir le lien' }}
      </button>
    </form>
    
    <div v-if="message" class="mt-4 p-3 bg-green-100 rounded">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { useApi } from '@/composable/useApi'

const { post } = useApi()
const email = ref('')
const loading = ref(false)
const message = ref('')

const handleSubmit = async () => {
  loading.value = true
  try {
    const response = await post('/password-reset/request', { email: email.value })
    message.value = response.message
    
    // En dev, afficher le token pour les tests
    if (response.dev_token) {
      console.warn('DEV MODE - Token:', response.dev_token)
    }
  } catch (error) {
    message.value = 'Une erreur est survenue'
  } finally {
    loading.value = false
  }
}
</script>
```

### Page de confirmation du reset

```vue
<!-- pages/reset-password.vue -->
<template>
  <div class="max-w-md mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Nouveau mot de passe</h1>
    
    <form @submit.prevent="handleSubmit">
      <input
        v-model="newPassword"
        type="password"
        placeholder="Nouveau mot de passe"
        required
        minlength="6"
        class="w-full border rounded px-3 py-2 mb-4"
      />
      
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded"
      >
        {{ loading ? 'Changement...' : 'Changer le mot de passe' }}
      </button>
    </form>
    
    <div v-if="error" class="mt-4 p-3 bg-red-100 rounded">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { useApi } from '@/composable/useApi'

const route = useRoute()
const router = useRouter()
const { post } = useApi()

const token = route.query.token
const newPassword = ref('')
const loading = ref(false)
const error = ref('')

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  
  try {
    await post('/password-reset/confirm', {
      token: token,
      newPassword: newPassword.value
    })
    
    // Succès : rediriger vers login
    alert('Mot de passe changé avec succès !')
    router.push('/login')
  } catch (err) {
    error.value = err.data?.error || 'Une erreur est survenue'
  } finally {
    loading.value = false
  }
}
</script>
```

---

## Sécurité

### ✅ Protections implémentées

1. **Token sécurisé**
   - Génération avec `random_bytes(32)` (cryptographiquement sûr)
   - 64 caractères hexadécimaux = 2^256 possibilités

2. **Expiration temporelle**
   - Validité : 1 heure par défaut
   - Token invalidé automatiquement après usage

3. **Protection anti-énumération**
   - Même réponse que l'email existe ou non
   - Empêche de découvrir les utilisateurs existants

4. **Usage unique**
   - Token supprimé après utilisation
   - Impossible de réutiliser le même token

5. **Validation du mot de passe**
   - Minimum 6 caractères (peut être renforcé)

### 🔒 Recommandations supplémentaires

1. **Rate limiting**
   ```php
   // À ajouter dans security.yaml ou avec un bundle
   // Limiter à 3 demandes par IP/heure
   ```

2. **Rotation des tokens**
   ```php
   // Invalider tous les tokens existants avant d'en créer un nouveau
   if ($user->getResetPasswordToken()) {
       // Token déjà existant : invalider et créer un nouveau
   }
   ```

3. **Logging**
   ```php
   // Logger toutes les tentatives de reset
   $this->logger->info('Password reset requested', [
       'email' => $data['email'],
       'ip' => $request->getClientIp(),
   ]);
   ```

4. **Notification de sécurité**
   ```php
   // Envoyer un email même si le reset n'a pas été demandé
   // "Si vous n'avez pas demandé ce reset, changez votre mot de passe"
   ```

---

## Migration depuis l'ancien système

L'ancien endpoint `/api/reset-password` a été **complètement retiré**.

Si vous aviez du code frontend utilisant cet endpoint :

**Avant (DANGEREUX) :**
```javascript
await post('/reset-password', {
  email: 'user@example.com',
  newPassword: 'nouveaupass'
})
```

**Après (SÉCURISÉ) :**
```javascript
// Étape 1 : Demande
await post('/password-reset/request', {
  email: 'user@example.com'
})

// Étape 2 : Utilisateur reçoit l'email et clique sur le lien
// Le frontend extrait le token de l'URL

// Étape 3 : Confirmation
await post('/password-reset/confirm', {
  token: tokenFromUrl,
  newPassword: 'nouveaupass'
})
```

---

## Checklist de déploiement

- [ ] Migration de la base de données exécutée
- [ ] Service d'envoi d'email configuré (SMTP/SendGrid/etc.)
- [ ] Templates d'email créés
- [ ] Pages frontend créées (forgot-password + reset-password)
- [ ] Tests manuels réalisés en dev
- [ ] Rate limiting configuré
- [ ] Logging des tentatives activé
- [ ] Documentation utilisateur rédigée

---

## Support

Pour toute question ou problème :
1. Vérifier les logs Symfony : `tail -f var/log/dev.log`
2. Tester en mode dev avec les tokens exposés
3. Vérifier la validité du token en base de données

---

**Document créé le :** 6 octobre 2025  
**Dernière mise à jour :** 6 octobre 2025  
**Version :** 1.0

