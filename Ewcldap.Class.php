<?php
/**
 * Performs LDAP operations like authentication and user GUID extraction
 *
 * @author Fuad Lawal <fuad@ewebconsult.com>
 */
Class Ewcldap
{
    private $_options;
    private $_connection;
    private $_attributes = array('objectguid', 'cn', 'sn', 'givenname', 'mail');

    /**
     * Constructor method
     */
    public function __construct($ldapOptions = array())
    {
        if (empty($ldapOptions)) {
            throw new Exception('You must specify your LDAP connection parameters in order to use this class.');
        }
        $this->_options = $ldapOptions;
        if (!$this->_connection = ldap_connect($this->_options['host'], $this->_options['port'])) {
            throw new Exception('Unable to establish a connection to the host on the port provided.');
        }
        ldap_set_option($this->_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->_connection, LDAP_OPT_REFERRALS, 0);
    }

    /**
     * Performs an LDAP authentication using the user-supplied username and password
     *
     * @param string $username User-supplied username
     * @param string $password User-supplied password
     * @return array A collection comprising authentication result and user GUID
     */
    public function login($username, $password)
    {
        try {
            if (ldap_bind($this->_connection, $this->_options['domain']."\\".$username, $password)) {
                return array(true, $this->_getAttributes($username));
            } else {
                throw new Exception('Your username and password combination is incorrect.');
            }
        } catch (Exception $e){
            throw new Exception('Invalid Identity provided.');
        }
        return array(false, null);
    }

    /**
     * Performs an LDAP search of the autheticated user, to extract his data,
     * including unique objectGUID
     *
     * @return array Array of user data
     */
    private function _getAttributes($username)
    {
        $results = array();
        $filter = "(sAMAccountName=" . $username .")";

        $ldapSearchResult = ldap_search($this->_connection, $this->_options['ou'].','.$this->_options['dc'], $filter, $this->_attributes);

        if (ldap_count_entries($this->_connection, $ldapSearchResult)) {
            $ldapResults = ldap_get_entries($this->_connection, $ldapSearchResult);
            $results['guid'] = $this->_guidToString($ldapResults[0]["objectguid"][0]);
            $results['fullName'] = $ldapResults[0]["cn"][0];
            $results['lastName'] = $ldapResults[0]["sn"][0];
            $results['firstName'] = $ldapResults[0]["givenname"][0];
            $results['email'] = $ldapResults[0]["mail"][0];
        } else {
            throw new Exception('Error extracting metadata for the given user.');
        }
        return $results;
    }

    /**
     * Converts Hex objectGUID to String
     * @param  resource $binaryGuid Binary guid
     * @return string   String representation of the binary Hex guid
     */
    private function _guidToString($binaryGuid)
    {
        $hexGuid = unpack("H*hex", $binaryGuid);
        $hex = $hexGuid["hex"];

        $hex1 = substr($hex, -26, 2) . substr($hex, -28, 2) . substr($hex, -30, 2) . substr($hex, -32, 2);
        $hex2 = substr($hex, -22, 2) . substr($hex, -24, 2);
        $hex3 = substr($hex, -18, 2) . substr($hex, -20, 2);
        $hex4 = substr($hex, -16, 4);
        $hex5 = substr($hex, -12, 12);

        return $hex1 . "-" . $hex2 . "-" . $hex3 . "-" . $hex4 . "-" . $hex5;
    }

    /**
     * Close our LDAP connection when done & null the connection handle.
     */
    public function __destruct()
    {
        if ($this->_connection) {
            ldap_unbind($this->_connection);
            $this->_connection = null;
        }
    }
}