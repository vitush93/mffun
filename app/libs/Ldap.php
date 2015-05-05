<?php

namespace Vitush;

/**
 * Object wrapper of the ldap_* functions.
 *
 * Class Ldap
 * @package Vitush
 */
class Ldap
{
    /**
     * @var string server's address
     */
    private $server;

    /**
     * @var resource
     */
    private $connection;

    /**
     * @var resource
     */
    private $lastSearch;

    /**
     * @param string $server valid server address eg. "ldap.example.com"
     */
    public function __construct($server)
    {
        $this->server = $server;
    }

    /**
     * Attempts to connect to the ldap server.
     *
     * @return $this
     * @throws LdapException
     */
    public function connect()
    {
        $res = @ldap_connect($this->server);
        if (!$res) throw new LdapException("Could not connect: " . $res);

        $this->connection = $res;

        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     * @throws LdapException
     */
    public function bind($username, $password)
    {
        if (!$this->connection) throw new LdapException("Cannot bind: not connected to server.");

        $r = @ldap_bind($this->connection, $username, $password);
        return $r;
    }

    public function unbind()
    {
        ldap_unbind($this->connection);
    }

    public function close()
    {
        ldap_close($this->connection);
    }

    /**
     * @param $base_dn
     * @param $filter
     * @return $this
     */
    public function ldapSearch($base_dn, $filter)
    {
        $this->lastSearch = @ldap_search($this->connection, $base_dn, $filter);

        return $this;
    }

    /**
     * @return LdapSearchResult
     */
    public function getSearchResult()
    {
        $result = @ldap_get_entries($this->connection, $this->lastSearch);

        return new LdapSearchResult($result);
    }

}

class LdapSearchResult
{
    /** @var array */
    private $result;

    /** @var string */
    private $serialized;

    /**
     * @param array $arr
     */
    public function __construct($arr)
    {
        $this->result = $arr;
        $this->serialized = serialize($arr);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->result) == 1;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $key
     * @param bool $full return entire value
     * @return mixed
     */
    public function get($key, $full = FALSE)
    {
        if ($full) {
            return $this->result[0][$key];
        } else {
            return $this->result[0][$key][0];
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function containsKey($key)
    {
        return isset($this->result[$key]);
    }

    /**
     * Performs search over serialized result array.
     *
     * @param string $needle substring to search
     * @return bool
     */
    public function contains($needle)
    {
        return (strpos($this->serialized, $needle) !== FALSE);
    }
}

class LdapException extends \Exception
{
}