<?php

namespace App\Security;

use App\Entity\NoConformidad;
use App\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class HallazgoVoter extends Voter
{
    const EDIT = 'HALLAZGO_EDIT';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof NoConformidad;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Usuario) {
            return false;
        }

        /** @var NoConformidad $noConformidad */
        $noConformidad = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($noConformidad, $user);
        }

        return false;
    }

    private function canEdit(NoConformidad $noConformidad, Usuario $user): bool
    {
        // Permite editar si el usuario tiene ROLE_CALIDAD
        if (in_array('ROLE_CALIDAD', $user->getRoles())) {
            return true;
        }

        // Permite editar si el usuario está asignado a la no conformidad
        return $noConformidad->getAsignadoA() === $user;
    }
} 