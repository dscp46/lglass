<?php

namespace lib;

class proto_ssh implements \IRemoteExecutable {

    // Handle to the SSH connection
    private $hSession = null;

    public function __construct( array $config, array $global_params)
    {
        if(
            ( empty( $config) || !is_array( $config) || empty( $config['user'])) 
            && (empty($global_params) || !is_array( $global_params) || empty($global_params['user']))
        )
            throw new \Exception( '<strong>Invalid configuration</strong>: Username not specified.');

        $auth_method    = $config['auth_method'] ?? $global_params['auth_method'] ?? 'password';
        $host           = $config['hostname'];
        $port           = $config['port'] ?? 22;
        $username       = $config['user'] ?? $global_params['user'];
        $password       = $config['password'] ?? '';
        $privkey_fname  = isset( $config['privkeyfile']) ? $config['privkeyfile'] : $global_params['privkeyfile'] ?? '';
        $privkey_passwd = isset( $config['passphrase']) ? $config['passphrase'] : $global_params['passphrase'] ?? '';
        $pubkey_fname   = isset( $config['pubkeyfile']) ? $config['pubkeyfile'] : $global_params['pubkeyfile'] ?? '';

        $conopts = array();

        switch( $auth_method )
        {
        case 'password':
            // Connect to server
            $this->hSession = @ssh2_connect( $host, $port, $conopts);
            if( $this->hSession === false)
            {
                $conopts['kex'] = 'diffie-hellman-group1-sha1';
                $this->hSession = @ssh2_connect( $host, $port, $conopts);
                if( $this->hSession === false)
                {
                    $conopts['kex'] = 'diffie-hellman-group14-sha1';
                    if( $this->hSession === false)
                        throw new \Exception( "Unable to connect to '$host:$port'.");
                }
            }
            // Password
            if( @ssh2_auth_password( $this->hSession, $username, $password) === false )
                throw new \Exception( "SSH Authentication failed for user '$username'. Wrong password?");
            break;

        case 'pubkey':
            // Pubkey
            if( !is_readable( $pubkey_fname) )
                throw new \Exception( "Public key file '$pubkey_fname' doesn't exists or is unreadable.");
            
            if( !is_readable( $privkey_fname) )
                throw new \Exception( "Private key file '$privkey_fname' doesn't exists or is unreadable.");

            // Validate key format
            $priv = file_get_contents( $privkey_fname);

            if( preg_match( '/^-----BEGIN OPENSSH PRIVATE KEY-----/', $priv) )
                throw new \Exception( "<strong>Invalid private key</strong>: Private key must be in PEM format.");

            if( preg_match( '/^-----BEGIN EC PRIVATE KEY-----/', $priv) )
            {
                // Check libssh2 version through cURL
                $curl_ver = curl_version();
                if( !array_key_exists( 'libssh_version', $curl_ver) )
                    throw new \Exception('<strong>Unsupported pubkey format</strong> php-curl can\'t figure out libssh2 version, ECDSA pubkey auth disabled.');
                $libssh_ver = explode( '.', explode( '/', $curl_ver['libssh_version'])[1] );
                if( $libssh_ver[0] < 1 || ( $libssh_ver[0] == 1 && $libssh_ver[1] < 10 ) )
                    throw new \Exception( "<strong>Unsupported pubkey format</strong>: libssh2 >= 1.10.0 required to support ECDSA public keys.");

                $pub = file_get_contents( $pubkey_fname);
                $pub = explode( ' ', $pub);
                if( !preg_match( '/^ecdsa-sha2-nistp(?:256|384|521)$/', $pub[0]) )
                    throw new \Exception( "<strong>Unsupported format</strong>: private keys with format '${pub[0]}' aren't supported yet.");
                $conopts['hostkey'] = $pub[0].',ssh-rsa';
                unset( $pub);
            }
            else
                $conopts['hostkey'] = 'ssh-rsa';
            
            unset( $priv);

            // Connect to server
            $this->hSession = @ssh2_connect( $host, $port, $conopts);
            if( $this->hSession === false)
            {
                $conopts['kex'] = 'diffie-hellman-group1-sha1';
                $this->hSession = @ssh2_connect( $host, $port, $conopts);
                if( $this->hSession === false)
                {
                    $conopts['kex'] = 'diffie-hellman-group14-sha1';
                    if( $this->hSession === false)
                        throw new \Exception( "Unable to connect to '$host:$port'.");
                }
            }

            if( @ssh2_auth_pubkey_file( $this->hSession, $username, $pubkey_fname, $privkey_fname, $privkey_passwd) === false )
                throw new \Exception( "SSH Authentication failed for user '$username'. Wrong password?");
                break;

        default:
            throw new \Exception( "Unsupported auth method for SSH transport");
        }

    }

    public function __destruct()
    {
        if( $this->hSession !== false && $this->hSession !== null )
            ssh2_disconnect( $this->hSession);
    }
 
    public function exec_remote( string $command): ?array
    {
        if( $this->hSession === false || $this->hSession === null )
            return null;
        
        $stdout = ssh2_exec( $this->hSession, $command);
        $stderr = ssh2_fetch_stream( $stdout, SSH2_STREAM_STDERR);
        
        // Set streams as blocking
        stream_set_blocking( $stderr, true);
        stream_set_blocking( $stdout, true);
        
        // Fetch result
        $result_std = stream_get_contents( $stdout);
        $result_err = stream_get_contents( $stderr);
        
        // Close connection
        fclose( $stderr);
        fclose( $stdout);
        
        return [$result_std, $result_err];
    }
}

# vim: set et ts=4 sw=4:
