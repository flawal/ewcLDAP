ewcLDAP
=======

**ewcLDAP** is a minimal PHP Class for  Lightweight Directory Access Protocol (LDAP) Authentication.

This class will authenticate users over distributed directory information services using the LDAP protocol.

Usage
------

The script works well out-of-the-box; simply drop the **`Ewcldap.Class.php`** file in the directory containing the rest of your scripts and *instantiate* the class.

Set your LDAP connection parameters

    $ldapOptions = array(
        'host'   => 'YOUR_LDAP_HOST', // Replace with your LDAP host
        'ou'     => 'OU=Corporate Users,OU=Corporate,OU=YOUR_COMPANY', // Replace with your organization
        'dc'     => 'DC=YOUR_DOMAIN,DC=com', // Replace with your LDAP domain
        'port'   => '389', // Replace with your LDAP port
        'domain' => 'YOUR_DOMAIN', // Replace with your LDAP domain
    );

Instantiate the class and call the login method

    try {
        $ldapAuth = new Ewcldap($ldapOptions);
        list($status, $userData) = $ldapAuth->login($username, $password);
        if ($status && !empty($userData)) {
            // authentication successful...
        }
    } catch (Exception $e) {
        // handle errors here...
    }

Contact
-------

I'll answer any questions about how to use the code. Also, if you create an app which uses the code, I'd love to hear about it. You can find my contact details below.

If you want to submit a feature request or bug report, fork the code and implement the feature/fix, then submit a pull request.

Thanks,

Fuad Lawal

Me: http://www.fuadlawal.com
My Work: http://www.ewebconsult.com Twitter: http://twitter.com/ewebconsult
Hire Me: https://www.linkedin.com/in/fadguru
