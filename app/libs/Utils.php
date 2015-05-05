<?php

namespace App\Libs;


use Nette\Object;
use Vitush\Ldap;
use Vitush\LdapException;

class Utils extends Object
{
    public static function safeExplodeByComma(&$data)
    {
        $data = trim($data, ' ,');
        $data = explode(',', $data);
        array_walk($data, function (&$value, $key) {
            $value = trim($value, ' ,');
        });
        $data = array_values(array_unique(array_filter($data)));
    }

    public static function texy($text)
    {
        $t = self::getTexy()->process($text);

        return $t;
    }

    /**
     * Fetches numeric (ISIC number) uid by SIS login (uid).
     *
     * @param $uid
     * @param null|Ldap $ldap use existing connection
     * @return array
     * @throws LdapException
     */
    public static function getMffLdapNumericUid($uid, $ldap = NULL)
    {
        if (!$ldap) {
            $ldap = new Ldap("ldaps://ldap.cuni.cz");
        }

        $result = $ldap->connect()
            ->ldapSearch("ou=people,dc=cuni,dc=cz", "(&(objectClass=person)(uid={$uid}))")
            ->getSearchResult();
        $ldap->close();

        // check if such user exists and if so, is he associated with MFF?
        if ($result->isEmpty() || !$result->contains("mff")) {
            throw new LdapException;
        }

        // if user entered SIS login instead of UKCO, fetch UKCO first (needed for auth)
        $ukco = $uid;
        if (!is_numeric($ukco)) {

            $uids = $result->get("uid", TRUE);
            for ($i = 0; $i < $uids['count']; $i++) {
                if (is_numeric($uids[$i])) {
                    $ukco = $uids[$i];
                    break;
                }
            }
        }

        return [
            'ukco' => $ukco,
            'result' => $result
        ];
    }

    /**
     * @return \Texy
     */
    private static function getTexy()
    {
        $texy = new \Texy();
        $texy->allowed['block'] = FALSE;
        $texy->allowed['blockquote'] = FALSE;
        $texy->allowed['figure'] = FALSE;
        $texy->allowed['heading/underlined'] = FALSE;
        $texy->allowed['heading/surrounded'] = FALSE;
        $texy->allowed['horizline'] = FALSE;
        $texy->allowed['html/tag'] = FALSE;
        $texy->allowed['html/comment'] = FALSE;
        $texy->allowed['image'] = FALSE;
        $texy->allowed['image/definition'] = FALSE;
        $texy->allowed['link/definition'] = FALSE;
        $texy->allowed['link/reference'] = FALSE;
        $texy->allowed['link/url'] = FALSE;
        $texy->allowed['link/email'] = FALSE;
        $texy->allowed['list'] = FALSE;
        $texy->allowed['list/definition'] = FALSE;
        $texy->allowed['phrase/strong'] = FALSE;
        $texy->allowed['script'] = FALSE;

        return $texy;
    }

} 