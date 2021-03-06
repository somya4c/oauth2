<?php

/**
 * This file is part of the authbucket/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AuthBucket\OAuth2\Tests\TestBundle\Entity\ModelManagerFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;

// Define SQLite DB path.
$app['db.options'] = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/../cache/' . $app['env'] . '/.ht.sqlite',
);

// Return an instance of Doctrine ORM entity manager.
$app['authbucket_oauth2.orm'] = $app->share(function ($app) {
    $conn = $app['dbs']['default'];
    $em = $app['dbs.event_manager']['default'];

    $driver = new AnnotationDriver(new AnnotationReader(), array(__DIR__ . '/../../tests/src/AuthBucket/OAuth2/Tests/TestBundle/Entity'));

    $config = Setup::createConfiguration(false);
    $config->setMetadataDriverImpl($driver);
    $config->setMetadataCacheImpl(new ArrayCache());
    $config->setQueryCacheImpl(new ArrayCache());

    return EntityManager::create($conn, $config, $em);
});

// Return entity classes for model manager.
$app['authbucket_oauth2.model'] = array(
    'access_token' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\AccessToken',
    'authorize' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\Authorize',
    'client' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\Client',
    'code' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\Code',
    'refresh_token' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\RefreshToken',
    'scope' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\Scope',
    'user' => 'AuthBucket\\OAuth2\\Tests\\TestBundle\\Entity\\User',
);

// Add model managers from ORM.
$app['authbucket_oauth2.model_manager.factory'] = $app->share(function ($app) {
    return new ModelManagerFactory($app['authbucket_oauth2.orm'], $app['authbucket_oauth2.model']);
});
