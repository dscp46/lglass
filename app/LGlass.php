<?php

class LGlass {

    private $config;

    private $commands = array(
        'ping' => array( 'display' => 'ping', 'arg' => 'ipaddr', 'trim' => true),
        'trac' => array( 'display' => 'traceroute', 'arg' => 'ipaddr', 'trim' => true),
        'sh ip b sum' => array( 'display' => 'show ip bgp summary', 'arg' => 'none', 'trim' => false),
        'sh ip b nei' => array( 'display' => 'show ip bgp neighbors', 'arg' => 'none', 'trim' => false),
        'sh ip b nei adv' => array( 'display' => 'show ip bgp neighbors &lt;ip&gt; advertised-routes', 'arg' => 'ipaddr', 'trim' => true),
        'sh ip b nei rec' => array( 'display' => 'show ip bgp neighbors &lt;ip&gt; received-routes', 'arg' => 'ipaddr', 'trim' => true),
        'sh ip b damp' => array( 'display' => 'show ip bgp dampened-paths', 'arg' => 'none', 'trim' => false), 
        'sh ip b flap' => array( 'display' => 'show ip bgp flap-statistics', 'arg' => 'none', 'trim' => false),
        'sh ip b rgx' => array( 'display' => 'show ip bgp regexp', 'arg' => 'regexp', 'trim' => false),
        'sh ip ig gr' => array( 'display' => 'show ip igmp groups', 'arg' => 'none', 'trim' => false),
        'sh ip ig int' => array( 'display' => 'show ip igmp interfaces', 'arg' => 'none', 'trim' => false),
        'sh ip int br' => array( 'display' => 'show ip interfaces brief', 'arg' => 'none', 'trim' => false),
        'sh ip r' => array( 'display' => 'show ip route', 'arg' => 'none', 'trim' => false),
        'mtrace' => array( 'display' => 'mtrace', 'arg' => 'mtrace', 'trim' => true),
    );

    function __construct()
    {
        self::check_php_ext( 'curl');
        self::check_php_ext( 'ssh2');
        self::check_php_ext( 'yaml');
        
        $this->config = self::load_config( './conf/config.yaml');
        if( $this->config !== false )
            $this->rtr_list = array_keys($this->config['routers']);
        else
        {
            $this->rtr_list = array();
            self::crit_error('<strong>Configutation loading failed</strong> Check that the config file exists and is a valid YAML file.');
        }
    }


    public static function about()
    {
        $app_name = get_called_class();
        require('view/about.php');
    }

    // Checks if a module is present, and makes the app die if not.
    public static function check_php_ext( string $mod_name, bool $die_if_unloadable = true)
    {
        if( !extension_loaded( $mod_name) ) 
        {
            // Check if we can dynamically load the extension
            if( is_callable('dl') )
                if( dl( "$mod_name.so"))
                    return true;
            
            if( $die_if_unloadable )
                self::crit_error( "Unable to load extension '$mod_name'.\n");
            else
                return false;
        }
        return true;
    }
    
    
    // Attempt to load config from a YAML file
    public static function load_config( string $fname)
    {
        if( !is_readable($fname) )
        {
            error_log( "[LGlass::load_config] Unable to open config file '$fname'.");
            return false;
        }

        $config = yaml_parse_file( $fname);

        if( $config === false )
        {
            error_log( "[LGlass::load_config] Unable to parse config file '$fname'.");
            return false;
        }

        return $config['config'];
	}

    // Load an equipment driver, with prior existence check and interface implementation.
    public static function load_driver( string $drv_name)
    {
        if( !file_exists( "./app/lib/drv_${drv_name}.php" ) )
            return null;

        // The following can be done because of the spl_autoload_register set in index.php
        $class_name = "\\lib\\drv_$drv_name";
        $drv = new $class_name;
        return ($drv instanceof IRouterDriver) ? $drv : false;
    }

    public static function load_protocol( array $config, array $global_params)
    {
        $proto_name = $config['protocol'];
        if( !file_exists( "./app/lib/proto_${proto_name}.php" ) )
            return null;

        // The following can be done because of the spl_autoload_register set in index.php
        $class_name = "\\lib\\proto_$proto_name";
        $proto = new $class_name( $config, $global_params);
        return ($proto instanceof IRemoteExecutable) ? $proto : false;
    }

    public static function crit_error( string $err_mesg)
    {
        $app_name = get_called_class();
        $app_warn_dlg = $err_mesg;
        include('view/crit_error.php');
        exit(-1);
    }

    public function error( $err_mesg)
    {
        $this->render(
            null,
            $err_mesg
        );
        exit(1);
    }

	public function render( $output=null, $warn_dlg=null)
	{
		$app_name = get_class( $this);
		$app_warn_dlg = $warn_dlg;
        $rtr_list = is_array( $this->rtr_list) ? $this->rtr_list : array( $this->rtr_list);

        if( isset($this->debug) )
            $app_output = "--- \$_POST ---\n".var_export($_POST,true)."\n\n--- Config ---\n".var_export($this->config,true)."\n\n--- Output ---\n" . ($output ?? '');
        else
            $app_output = $output;

        $app_commands = $this->commands;
        // Call the user interface view
		require('view/ui.php');
    }

    public function validate_arg( string $arg, string $type, $rtr_config)
    {
        // Skip argument validation if no arg is expected.
        if( $type == 'none' )
            return true;

        $regexes = array(
            'ham_ipaddr' => array( 
                'rgx' => '/^0?44\.(25[0-5]|2[0-4][0-9]|[01]?[0-9]{1,2})\.(?1)\.(?1)$/', 
                'errmsg' => 'Argument must be a valid IPv4 address within 44.0.0.0/8.'
            ),
            'ipaddr' => array( 
                'rgx' => '/^(25[0-5]|2[0-4][0-9]|[01]?[0-9]{1,2})\.(?1)\.(?1)\.(?1)$/', 
                'errmsg' => 'Argument must be a valid IPv4 address.'
            ),
            'ham_mtrace' => array(
                'rgx' => '/^(0?44\.(25[0-5]|2[0-4][0-9]|[01]?[0-9]{1,2})\.(?2)\.(?2))(?: (?1))(?: (?:22[4-9]|2[34][0-9])\.(?2)\.(?2)\.(?2))(?: (?:25[0-5]|2[0-4][0-9]|1?[0-9]{1,2}))?$/',
                'errmsg' => 'Argument must be 2 consecutive Hamnet IPv4 addresses, then a multicast IPv4 address, and optionally a TTL spanning from 1 to 255, separated by spaces.'
            ),
            'mtrace' => array(
                'rgx' => '/^((25[0-5]|2[0-4][0-9]|[01]?[0-9]{1,2})\.(?2)\.(?2)\.(?2))(?: (?1))(?: (?:22[4-9]|2[34][0-9])\.(?2)\.(?2)\.(?2))(?: (?:25[0-5]|2[0-4][0-9]|1?[0-9]{1,2}))?$/',
                'errmsg' => 'Argument must be 2 consecutive IPv4 addresses, then a multicast IPv4 address, and optionally a TTL spanning from 1 to 255, separated by spaces.'
            ),
            'regexp' => array( 
                'rgx' => '/^\^?((?:([ _]?\\\\?[0-9\.]+[?+*]?[?+]?\\\\?[ _]?|\.[?+*]?[?+]?)|([ _]?\[\^?(?:[0-9]-[0-9]|[0-9]+)\][?+*]?[?+]?[ _]?)|[ _]?\(((?:(?2)+|(?3)))(?:\|(?4))?\)[?+*]?[ _]?)*)(?::(?1))?\$?$/', 
                'errmsg' => 'Argument must be a valid BGP regex.'
            ),
        );

        $is_hamnet = false;

        if( isset( $rtr_config['hamnet']) )
            $is_hamnet = ( $rtr_config['hamnet'] !== 0 && $rtr_config['hamnet'] != 'false' && $rtr_config['hamnet'] !== 'no' );
        else if( isset( $this->config['hamnet']) )
            $is_hamnet = ( $this->config['hamnet'] !== 0 && $this->config['hamnet'] != false && $this->config['hamnet'] !== 'no' );


        // Replace the ipaddr type with a hamnet-specific restrictive version, if applicable.
        if( $is_hamnet && ($type == 'ipaddr' || $type == 'mtrace') )
            $type = 'ham_' . $type;

        // Skip argument validation if no arg is expected.
        if( $type == 'none' )
            return true;

        // Fail validation
        if( !array_key_exists( $type, $regexes))
            return 'Unrecognized argument type, argument validator can\'t perform its job.';

        return (preg_match( $regexes[$type]['rgx'], $arg)) ? true : $regexes[$type]['errmsg'];
    }

    public function init()
    {

        $path = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']);
        $path = preg_replace( '/^(.+)\/[^\/]+$/', '$1', $path);
        $path = str_replace( $path, '', $_SERVER['REQUEST_URI']);
        if( preg_match( '/\/?about/', $path) )
        {
            self::about();
            die();
        }

        //$this->debug=1;
        // Replay resistance
        if( !session_start() )
            $this->error( '<strong>Server-side error</strong> Unable to start session.');

        if( isset( $_POST) && !empty($_POST) )
        {
            // Get the query interval (in secs)
            $query_interval = $this->config['query_interval'] ?? 10;

            if( empty( $_SESSION['lastread'] ) )
            {
                // Write the last read timer, then release the session lock immediately
                $_SESSION['lastread'] = time();
                session_write_close();

                 header('HTTP/1.1 403 Forbidden');
                $this->error( '<strong>Forbidden</strong> This website doesn\'t accept automated queries.');
            }

            // Write the last read timer, then release the session lock immediately
            $_SESSION['lastread'] = time();
            session_write_close();
                
            if( !empty( $_SESSION['lastrun'] ) && (time()-$_SESSION['lastrun'] <= $query_interval) )
            {
                header('HTTP/1.1 429 Too Many Requests');
                header("Retry-After: ${query_interval}");
                $this->error( "<strong>Too many requests</strong> You must wait ${query_interval} seconds before sending another query.");
            }

            // Fetch router config from _POST['router']
            if( preg_match( '/^[A-Za-z0-9._-]+$/', $_POST['router']) )
            {
                if( !array_key_exists( $_POST['router'], $this->config['routers']) )
                    $this->error( '<strong>Router not found</strong>: This may happen if your request has been sent after a config change.');
                $router = $this->config['routers'][$_POST['router']];

                // Fill hostname with equipment friendly name if none has been supplied
                if( empty( $router['hostname'] ) )
                    $router['hostname'] = $_POST['router'];
            }
            else
                $this->error('<strong>Not acceptable</strong> Invalid router name.');

            if( empty( $router['type']) )
                $this->error( "<strong>Configuration error</strong>: No type specified for router '${_POST['router']}'.");

            // Load driver for equipment
            $driver = self::load_driver( $router['type'] );
            if( $driver === null )
                $this->error( '<strong>Not supported</strong>: No driver found for that model of router.');

            // Check for a bad driver
            if( $driver === false )
                $this->error( '<strong>Bad driver</strong>: Selected driver doesn\'t implement the IRouterDriver interface.');

            // Sanitize command from _POST['command']
            if( empty( $_POST['command']) || !preg_match( '/^[a-z ]+$/', $_POST['command']) )
                $this->error('<strong>Not acceptable</strong> Invalid command format.');

            // Check if command is implemented for that model of equipment.
            $xlated_cmd = $driver->translate_cmd( $_POST['command'], $router);
            if( $xlated_cmd === false )
                $this->error( '<strong>Not implemented</strong>: Command not available for that model of equipment.');

            // Check if selected command requires an arg.
            if( $driver->requires_arg( $_POST['command']) )
            {
                if( !empty($_POST['arg'] ) )
                {
                    $arg = ($this->commands[$_POST['command']]['trim']) ? trim($_POST['arg']) : $_POST['arg'];
                    if( ($errmsg = $this->validate_arg( $arg, $this->commands[$_POST['command']]['arg'], $router)) !== true )
                        $this->error( "<strong>Invalid argument</strong>: $errmsg");

                    // Replace argument from xlated cmd
                    $xlated_cmd = str_replace( '##__ARG__##', $arg, $xlated_cmd);
                }
                else
                    $this->error( '<strong>Missing argument</strong>.');
            }

            // Load the transport driver
            if( empty( $router['protocol']) )
                $this->error( "<strong>Configuration error</strong>: No communication protocol specified for router '${_POST['router']}'.");

            $result = '<no output>';
            $stderr = '';

            // Load equipment driver
            try {
                $global_params = $this->config['auth'] ?? array();
                $comm_iface = self::load_protocol( $router, $global_params);
                if( $comm_iface === null )
                    $this->error( '<strong>Not supported</strong>: No driver found for that communication protocol.');
                
                // Run command on equipment
                [$result, $stderr] = $comm_iface->exec_remote( $xlated_cmd);

                // Postprocess output
                $result = $driver->postprocess_output( $_POST['command'], $result);
            }
            catch(Exception $e)
            {
                $this->error( $e->getMessage());
            }

            // Set the last run timer to now.
            session_start();
            $_SESSION['lastrun'] = time();
            session_write_close();

            if( !empty($result) )
            {
                $result = preg_replace( '/^[ \t]*[\r\n]+/m', '', $result);
                $this->render( $result);
            }
            else
            {
                $stderr = preg_replace( '/^[ \t]*[\r\n]+/m', '', $stderr);
                $this->render( $stderr);
            }
            exit(0);
		}
        
        $_SESSION['lastread'] = time();
        session_write_close();

        // Default rendering
        unset($this->debug);
        $this->render();
    }
};

# vim: set et ts=4 sw=4:
