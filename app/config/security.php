<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

$app['security.encoder.digest'] = $app->share(function ($app) {
    return new PlaintextPasswordEncoder();
});

$app['security.user_provider.default'] = $app->share(function ($app) {
    return $app['authbucket_oauth2.model_manager.factory']->getModelManager('user');
});

$app['security.user_provider.admin'] = $app['security.user_provider.inmemory._proto'](array(
    'admin' => array('ROLE_ADMIN', 'secrete'),
));

$app['security.firewalls'] = array(
    'admin' => array(
        'pattern' => '^/admin',
        'http' => true,
        'users' => $app['security.user_provider.admin'],
    ),
    'oauth2_login' => array(
        'pattern' => '^/oauth2/login$',
        'anonymous' => true,
    ),
    'oauth2_authorize_http' => array(
        'pattern' => '^/oauth2/authorize/http$',
        'http' => true,
        'users' => $app['security.user_provider.default'],
    ),
    'oauth2_authorize' => array(
        'pattern' => '^/oauth2/authorize',
        'form' => array(
            'login_path' => '/oauth2/login',
            'check_path' => '/oauth2/authorize/login_check',
        ),
        'logout' => array(
            'logout_path' => '/oauth2/authorize/logout',
            'target_url' => '/demo',
        ),
        'users' => $app['security.user_provider.default'],
    ),
    'oauth2_token' => array(
        'pattern' => '^/oauth2/token$',
        'oauth2_token' => true,
    ),
    'oauth2_debug' => array(
        'pattern' => '^/oauth2/debug$',
        'oauth2_resource' => array(
            'resource_type' => 'model',
            'scope' => array('debug'),
        ),
    ),
    'resource_resource_type_model' => array(
        'pattern' => '^/resource/resource_type/model$',
        'oauth2_resource' => array(
            'resource_type' => 'model',
            'scope' => array('demoscope1'),
        ),
    ),
    'resource_resource_type_debug_endpoint' => array(
        'pattern' => '^/resource/resource_type/debug_endpoint$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'client_id' => 'http://democlient1.com/',
                'client_secret' => 'demosecret1',
                'cache' => false,
            ),
        ),
    ),
    'resource_resource_type_debug_endpoint_cache' => array(
        'pattern' => '^/resource/resource_type/debug_endpoint/cache$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'client_id' => 'http://democlient1.com/',
                'client_secret' => 'demosecret1',
            ),
        ),
    ),
    'resource_resource_type_debug_endpoint_invalid_options' => array(
        'pattern' => '^/resource/resource_type/debug_endpoint/invalid_options$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(),
        ),
    ),
    'resource_resource_type_debug_endpoint_invalid_client' => array(
        'pattern' => '^/resource/resource_type/debug_endpoint/invalid_client$',
        'oauth2_resource' => array(
            'resource_type' => 'debug_endpoint',
            'scope' => array('demoscope1'),
            'options' => array(
                'client_id' => 'nonexistsclient',
                'client_secret' => 'fakesecret',
                'cache' => false,
            ),
        ),
    ),
);
