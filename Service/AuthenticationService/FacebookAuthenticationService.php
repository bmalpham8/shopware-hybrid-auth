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
    protected $scopeBirthday = 'user_birthday';
}
