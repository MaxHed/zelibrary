<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur sécurisé pour le reset de mot de passe.
 * Utilise un système de token temporaire avec expiration.
 */
#[AsController]
class PasswordResetController
{
    private const TOKEN_VALIDITY_HOURS = 1; // Validité du token : 1 heure

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    /**
     * Étape 1 : Demande de reset password.
     * Génère un token unique et l'envoie par email (à implémenter).
     */
    #[Route('/api/password-reset/request', name: 'api_password_reset_request', methods: ['POST'])]
    public function requestReset(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email'])) {
            return new JsonResponse(['error' => 'Email requis'], 400);
        }

        $user = $this->userRepository->findOneBy(['email' => $data['email']]);
        
        // IMPORTANT : Toujours retourner le même message pour éviter l'énumération d'utilisateurs
        $message = 'Si cet email existe, un lien de réinitialisation a été envoyé.';
        
        if (!$user) {
            // Ne pas révéler que l'utilisateur n'existe pas
            return new JsonResponse(['message' => $message], 200);
        }

        // Générer un token sécurisé
        $token = bin2hex(random_bytes(32));
        $expiresAt = new \DateTimeImmutable('+' . self::TOKEN_VALIDITY_HOURS . ' hours');

        $user->setResetPasswordToken($token);
        $user->setResetPasswordTokenExpiresAt($expiresAt);

        $this->entityManager->flush();

        // TODO : Envoyer un email avec le lien contenant le token
        // Exemple de lien : https://votre-frontend.com/reset-password?token={$token}
        // Pour le développement, vous pouvez logger le token ou le retourner en dev uniquement
        
        $isDev = ($_ENV['APP_ENV'] ?? 'prod') === 'dev';
        $responseData = ['message' => $message];
        
        if ($isDev) {
            // En dev seulement : retourner le token pour faciliter les tests
            $responseData['dev_token'] = $token;
            $responseData['dev_warning'] = 'Token visible uniquement en mode dev';
        }

        return new JsonResponse($responseData, 200);
    }

    /**
     * Étape 2 : Validation du token et reset du mot de passe.
     * Le token doit être valide et non expiré.
     */
    #[Route('/api/password-reset/confirm', name: 'api_password_reset_confirm', methods: ['POST'])]
    public function confirmReset(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['token']) || !isset($data['newPassword'])) {
            return new JsonResponse(['error' => 'Token et nouveau mot de passe requis'], 400);
        }

        if (strlen($data['newPassword']) < 6) {
            return new JsonResponse(['error' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
        }

        $user = $this->userRepository->findOneBy(['resetPasswordToken' => $data['token']]);
        
        if (!$user) {
            return new JsonResponse(['error' => 'Token invalide ou expiré'], 400);
        }

        if (!$user->isResetPasswordTokenValid()) {
            // Token expiré : le nettoyer
            $user->setResetPasswordToken(null);
            $user->setResetPasswordTokenExpiresAt(null);
            $this->entityManager->flush();
            
            return new JsonResponse(['error' => 'Token invalide ou expiré'], 400);
        }

        // Token valide : changer le mot de passe
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['newPassword']));
        
        // Supprimer le token (usage unique)
        $user->setResetPasswordToken(null);
        $user->setResetPasswordTokenExpiresAt(null);

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Mot de passe réinitialisé avec succès'
        ], 200);
    }
}

