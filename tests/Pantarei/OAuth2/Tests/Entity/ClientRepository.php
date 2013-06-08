<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Entity;

use Doctrine\ORM\EntityRepository;
use Pantarei\OAuth2\Model\ClientInterface;
use Pantarei\OAuth2\Model\ClientManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends EntityRepository implements ClientManagerInterface, UserProviderInterface
{
    public function createClient()
    {
        return new $this->getClassName();
    }

    public function deleteClient(ClientInterface $client)
    {
        $this->remove($client);
        $this->flush();
    }

    public function findClientByClientId($client_id)
    {
        return $this->findOneBy(array(
            'client_id' => $client_id,
        ));
    }

    public function reloadClient(ClientInterface $client)
    {
        $this->refresh($client);
    }

    public function updateClient(ClientInterface $client)
    {
        $this->persist($client);
        $this->flush();
    }

    public function loadUserByUsername($username)
    {
        $result = $this->findOneBy(array(
            'client_id' => $username,
        ));
        if ($result === null) {
            throw new UsernameNotFoundException();
        }

        return $result;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException();
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }
}