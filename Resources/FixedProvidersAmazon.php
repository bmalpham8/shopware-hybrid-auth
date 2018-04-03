<?php

if (!class_exists('Hybrid_Providers_Amazon')) {
    $dir = realpath(dirname(__FILE__));
    $dirs = explode(DIRECTORY_SEPARATOR, $dir);
    unset($dirs[count($dirs) - 1]);
    $dir = implode(DIRECTORY_SEPARATOR, $dirs);

    $file = sprintf(
        '%1$s%2$svendor%2$shybridauth%2$shybridauth%2$sadditional-providers%2$shybridauth-amazon%2$sProviders%2$sAmazon.php',
        $dir,
        DIRECTORY_SEPARATOR
    );

    require $file;
}

class FixedProvidersAmazon extends Hybrid_Providers_Amazon
{
    function initialize()
    {

        if (!$this->config['keys']['id'] || !$this->config['keys']['secret']) {
            throw new Exception(
                "Your application id and secret are required in order to connect to {$this->providerId}.", 4
            );
        }

        // override requested scope
        if (isset($this->config['scope']) && !empty($this->config['scope'])) {
            $this->scope = $this->config['scope'];
        }

        // include OAuth2 client
        $dirs = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        unset($dirs[count($dirs) - 1]);
        $baseDir = implode(DIRECTORY_SEPARATOR, $dirs);
        $oauth2Client = sprintf(
            '%1$s%2$svendor%2$shybridauth%2$shybridauth%2$shybridauth%2$sHybrid%2$sthirdparty%2$sOAuth%2$sOAuth2Client.php',
            $baseDir,
            DIRECTORY_SEPARATOR
        );

        $path = Hybrid_Auth::$config['path_libraries'].'Amazon/AmazonOAuth2Client.php';;

        require_once $oauth2Client;

        $amazonOauth2Client = sprintf(
            '%1$s%2$svendor%2$shybridauth%2$shybridauth%2$sadditional-providers%2$shybridauth-amazon%2$sthirdparty%2$sAmazon%2$sAmazonOAuth2Client.php',
            $baseDir,
            DIRECTORY_SEPARATOR
        );

        require_once $amazonOauth2Client;

        // create a new OAuth2 client instance
        $this->api = new AmazonOAuth2Client(
            $this->config['keys']['id'],
            $this->config['keys']['secret'],
            $this->endpoint,
            $this->compressed
        );

        $this->api->api_base_url = 'https://api.amazon.com';
        $this->api->authorize_url = 'https://www.amazon.com/ap/oa';
        $this->api->token_url = 'https://api.amazon.com/auth/o2/token';

        $this->api->curl_header = array('Content-Type: application/x-www-form-urlencoded');

        // If we have an access token, set it
        if ($this->token('access_token')) {
            $this->api->access_token = $this->token('access_token');
            $this->api->refresh_token = $this->token('refresh_token');
            $this->api->access_token_expires_in = $this->token('expires_in');
            $this->api->access_token_expires_at = $this->token('expires_at');
        }

        // Set curl proxy if exists
        if (isset(Hybrid_Auth::$config['proxy'])) {
            $this->api->curl_proxy = Hybrid_Auth::$config['proxy'];
        }
    }
}
