<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Security;

use Nette;


/**
 * User authentication and authorization.
 *
 * @property-read bool $loggedIn
 * @property-read IIdentity $identity
 * @property-read mixed $id
 * @property-read array $roles
 * @property-read int $logoutReason
 * @property   IAuthenticator $authenticator
 * @property   IAuthorizator $authorizator
 */
class User
{
	use Nette\SmartObject;

	/** @deprecated */
	const
		MANUAL = IUserStorage::MANUAL,
		INACTIVITY = IUserStorage::INACTIVITY;

	/** @deprecated */
	const BROWSER_CLOSED = IUserStorage::BROWSER_CLOSED;

	/** @var string  default role for unauthenticated user */
	public $guestRole = 'guest';

	/** @var string  default role for authenticated user without own identity */
	public $authenticatedRole = 'authenticated';

	/** @var callable[]  function (User $sender); Occurs when the user is successfully logged in */
	public $onLoggedIn;

	/** @var callable[]  function (User $sender); Occurs when the user is logged out */
	public $onLoggedOut;

	/** @var IUserStorage Session storage for current user */
	private $storage;

	/** @var IAuthenticator */
	private $authenticator;

	/** @var IAuthorizator */
	private $authorizator;


	public function __construct(IUserStorage $storage, IAuthenticator $authenticator = NULL, IAuthorizator $authorizator = NULL)
	{
		$this->storage = $storage;
		$this->authenticator = $authenticator;
		$this->authorizator = $authorizator;
	}


	/**
	 * @return IUserStorage
	 */
	public function getStorage()
	{
		return $this->storage;
	}


	/********************* Authentication ****************d*g**/


	/**
	 * Conducts the authentication process. Parameters are optional.
	 * @param  mixed optional parameter (e.g. username or IIdentity)
	 * @param  mixed optional parameter (e.g. password)
	 * @return void
	 * @throws AuthenticationException if authentication was not successful
	 */
	public function login($id = NULL, $password = NULL)
	{
		$this->logout(TRUE);
		if (!$id instanceof IIdentity) {
			$id = $this->getAuthenticator()->authenticate(func_get_args());
		}
		$this->storage->setIdentity($id);
		$this->storage->setAuthenticated(TRUE);
		$this->onLoggedIn($this);
	}


	/**
	 * Logs out the user from the current session.
	 * @param  bool  clear the identity from persistent storage?
	 * @return void
	 */
	public function logout($clearIdentity = FALSE)
	{
		if ($this->isLoggedIn()) {
			$this->onLoggedOut($this);
			$this->storage->setAuthenticated(FALSE);
		}
		if ($clearIdentity) {
			$this->storage->setIdentity(NULL);
		}
	}


	/**
	 * Is this user authenticated?
	 * @return bool
	 */
	public function isLoggedIn()
	{
		return $this->storage->isAuthenticated();
	}


	/**
	 * Returns current user identity, if any.
	 * @return IIdentity|NULL
	 */
	public function getIdentity()
	{
		return $this->storage->getIdentity();
	}


	/**
	 * Returns current user ID, if any.
	 * @return mixed
	 */
	public function getId()
	{
		$identity = $this->getIdentity();
		return $identity ? $identity->getId() : NULL;
	}


	/**
	 * Sets authentication handler.
	 * @return static
	 */
	public function setAuthenticator(IAuthenticator $handler)
	{
		$this->authenticator = $handler;
		return $this;
	}


	/**
	 * Returns authentication handler.
	 * @return IAuthenticator|NULL
	 */
	public function getAuthenticator($need = TRUE)
	{
		if ($need && !$this->authenticator) {
			throw new Nette\InvalidStateException('Authenticator has not been set.');
		}
		return $this->authenticator;
	}


	/**
	 * Enables log out after inactivity.
	 * @param  string|int|\DateTimeInterface number of seconds or timestamp
	 * @param  int|bool  flag IUserStorage::CLEAR_IDENTITY
	 * @param  bool  clear the identity from persistent storage? (deprecated)
	 * @return static
	 */
	public function setExpiration($time, $flags = NULL, $clearIdentity = FALSE)
	{
		$clearIdentity = $clearIdentity || $flags === IUserStorage::CLEAR_IDENTITY;
		$this->storage->setExpiration($time, $clearIdentity ? IUserStorage::CLEAR_IDENTITY : 0);
		return $this;
	}


	/**
	 * Why was user logged out?
	 * @return int|NULL
	 */
	public function getLogoutReason()
	{
		return $this->storage->getLogoutReason();
	}


	/********************* Authorization ****************d*g**/


	/**
	 * Returns a list of effective roles that a user has been granted.
	 * @return array
	 */
	public function getRoles()
	{
		if (!$this->isLoggedIn()) {
			return [$this->guestRole];
		}

		$identity = $this->getIdentity();
		return $identity && $identity->getRoles() ? $identity->getRoles() : [$this->authenticatedRole];
	}


	/**
	 * Is a user in the specified effective role?
	 * @param  string
	 * @return bool
	 */
	public function isInRole($role)
	{
		return in_array($role, $this->getRoles(), TRUE);
	}


	/**
	 * Has a user effective access to the Resource?
	 * If $resource is NULL, then the query applies to all resources.
	 * @param  string  resource
	 * @param  string  privilege
	 * @return bool
	 */
	public function isAllowed($resource = IAuthorizator::ALL, $privilege = IAuthorizator::ALL)
	{
		foreach ($this->getRoles() as $role) {
			if ($this->getAuthorizator()->isAllowed($role, $resource, $privilege)) {
				return TRUE;
			}
		}

		return FALSE;
	}


	/**
	 * Sets authorization handler.
	 * @return static
	 */
	public function setAuthorizator(IAuthorizator $handler)
	{
		$this->authorizator = $handler;
		return $this;
	}


	/**
	 * Returns current authorization handler.
	 * @return IAuthorizator|NULL
	 */
	public function getAuthorizator($need = TRUE)
	{
		if ($need && !$this->authorizator) {
			throw new Nette\InvalidStateException('Authorizator has not been set.');
		}
		return $this->authorizator;
	}

}
