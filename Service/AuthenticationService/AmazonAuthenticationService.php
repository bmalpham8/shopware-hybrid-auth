<?php
namespace Port1HybridAuth\Service\AuthenticationService;

use Port1HybridAuth\Model\User;
use Port1HybridAuth\Service\AbstractAuthenticationService;
use Port1HybridAuth\Service\ConfigurationServiceInterface;

/**
 * Class AmazonAuthenticationService
 * @package Port1HybridAuth\Service\AuthenticationService
 */
class AmazonAuthenticationService extends AbstractAuthenticationService
{
    /**
     * @return User|null
     */
    public function getUser()
    {
        $user = parent::getUser();

        if ($user != null) {
            $userProfil = $this->getUserProfile();

            if (empty($userProfil->firstName) && empty($userProfil->lastName) && !empty($userProfil->displayName)) {
                // we can use displayName to determine the firstName and lastName by exploding the space delimiter
                list($firstName, $lastName) = explode(' ', $userProfil->displayName);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
            } else {
                $user->setFirstName($userProfil->firstName);
                $user->setLastName($userProfil->lastName);
            }
        }

        return $user;
    }
}
