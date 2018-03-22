<?php
namespace Port1HybridAuth\Service\AuthenticationService;

use Port1HybridAuth\Model\User;
use Port1HybridAuth\Service\AbstractAuthenticationService;

/**
 * Class FacebookAuthenticationService
 * @package Port1HybridAuth\Service\AuthenticationService
 */
class FacebookAuthenticationService extends AbstractAuthenticationService
{
    const SCOPE_BIRTHDAY = 'user_birthday';

    /**
     * @return User|null
     */
    public function getUser()
    {
        $user = parent::getUser();

        $config = $this->configurationService->getProviderConfiguration($this->provider);

        if (
            array_key_exists('providers', $config)
            && is_array($config['providers'])
            && count($config['providers']) > 0
        ) {
            foreach ($config['providers'] as $providerName => $provider) {
                if ($providerName === $this->provider) {
                    if (
                        $provider['enabled']
                        && in_array(self::SCOPE_BIRTHDAY, $provider['scope'])
                        && $user !== null
                    ) {
                        $userPrf = $this->getUserProfile();

                        if (
                            !empty($userPrf->birthDay)
                            && !empty($userPrf->birthMonth)
                            && !empty($userPrf->birthYear)
                        ) {
                            // can be empty, if not public accessibility is set
                            $birthDay = strlen((string) $userPrf->birthDay) === 1
                                ? '0'.$userPrf->birthDay
                                : (string) $userPrf->birthDay;
                            $birthMonth = strlen((string) $userPrf->birthMonth) === 1
                                ? '0'.$userPrf->birthMonth
                                : (string) $userPrf->birthMonth;

                            $dtBirthday = new \DateTime(
                                sprintf('%s-%s-%s', $userPrf->birthYear, $birthMonth, $birthDay)
                            );
                            $user->setBirthday($dtBirthday);
                        }
                    }

                    break;
                }
            }
        }

        return $user;
    }
}
