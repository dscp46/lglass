<?php
namespace lib;

class drv_mikrotik implements \IRouterDriver {
    public function requires_arg( string $command): bool
    {
        switch( $command)
        {
        case 'ping':
        case 'trac':
        case 'sh ip b nei adv':
        case 'sh ip b nei rec':
            return true;
        default:
            return false;
        }
    }

    public function postprocess_output( string $cmd, string $str): string
    {
        $result = '';

        if( $cmd == 'trac' )
        {
            preg_match_all( '/^[ ]{0,2}([0-9]+|#) (.+)$/m', $str, $matches, PREG_PATTERN_ORDER);

            $nb_lines = sizeof( $matches[0]);

            $found = array();
            $out = array();
            for( $i=0; $i < $nb_lines; ++$i)
                $out[ $matches[1][$i]] = $matches[0][$i];


            $result = "${out['#']}\n";
            $nb_lines = sizeof($out)-1;
            for( $i=1; $i<=$nb_lines; ++$i)
                $result .= "${out[$i]}\n";
        }
        else
            $result = $str;

        return $result;
    }

    public function translate_cmd( string $command, array $config)
    {
        $source = $config['source'] ?? null;
        $vrf = $config['vrf'] ?? null;

        switch( $command)
        {
        case 'ping':
            $cmd = 'ping ##__ARG__## ';
            $cmd .= (!empty($vrf)) ? "routing-table=$vrf " : '';
            $cmd .= 'count=4 interval=0.2 ';
            return $cmd;
        case 'trac':
            $cmd = 'tool traceroute ##__ARG__## ';
            $cmd .= (!empty($vrf)) ? "routing-table=$vrf " : '';
            $cmd .= 'protocol=icmp max-hops=20 timeout=1s count=1';
            return $cmd;
        
        case 'sh ip b sum':
            $cmd = 'routing bgp peer print brief ';
            $cmd .= (!empty($vrf)) ? "where routing-mark=$vrf " : '';
            return $cmd;
        case 'sh ip b nei':
            $cmd = 'routing bgp peer print status ';
            $cmd .= (!empty($vrf)) ? "where routing-mark=$vrf " : '';
            return $cmd;
        case 'sh ip b nei adv':
            $cmd = 'routing bgp advertisements print peer=##__ARG__## ';
            return $cmd;
        case 'sh ip b nei rec':
            $cmd = 'ip route print where bgp=yes and received-from=##__ARG__## ';
            return $cmd;
        /*
        case 'sh ip b damp':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip bgp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'dampening dampened-paths';
            return $cmd;
        case 'sh ip b flap':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip bgp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'dampening flap-statistics';
            return $cmd;
        case 'sh ip b rgx':
            $cmd = (!empty($vrf)) ? "show bgp vrf $vrf all " : 'show ip bgp ';
            $cmd .= 'regexp ##__ARG__##';
            return $cmd;
        case 'sh ip ig gr':
            $cmd = 'show ip igmp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'groups';
            return $cmd;
        case 'sh ip ig int':
            $cmd = 'show ip igmp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'interface';
            return $cmd;
         */
        case 'sh ip int br':
            $cmd = 'ip address print';
            return $cmd; 
        case 'sh ip r':
            $cmd = 'ip route print ';
            $cmd .= (!empty($vrf)) ? "where routing-table=$vrf " : '';
            return $cmd;
default:
            return false;
        }
    }
}

# vim: set et ts=4 sw=4:
