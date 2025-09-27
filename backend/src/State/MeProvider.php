<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class MeProvider implements ProviderInterface
{
    public function __construct(private Security $security) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|null
    {
        /** @var object|null $user */
        $user = $this->security->getUser();
        return $user; // API Platform va sérialiser selon 'user:me'
    }
}
