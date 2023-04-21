<?php
namespace lib;

class drv_cisco implements \IRouterDriver {
    public function requires_arg( string $command): bool
    {
        switch( $command)
        {
        case 'ping':
        case 'trac':
        case 'sh ip b nei adv':
        case 'sh ip b nei rec':
        case 'sh ip b rgx':
        case 'mtrace':
            return true;
        default:
            return false;
        }
    }

    public function postprocess_output( string $cmd, string $str): string
    {
        return $str;
    }

    public function translate_cmd( string $command, array $config)
    {
        $source = $config['source'] ?? null;
        $vrf = $config['vrf'] ?? null;

        switch( $command)
        {
        case 'mtrace':
            $cmd = 'mtrace ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= '##__ARG__##';
            return $cmd;
        case 'ping':
            $cmd = 'ping ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= '##__ARG__## repeat 4 timeout 2 ';
            $cmd .= (!empty($source)) ? "source $source " : '';
            return $cmd;
        case 'trac':
            $cmd = 'traceroute ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= '##__ARG__## ttl 0 20 timeout 1 probe 1 ';
            $cmd .= (!empty($source)) ? "source $source " : '';
            return $cmd;
        case 'sh ip b sum':
            $cmd = (!empty($vrf)) ? "show bgp vrf $vrf all " : 'show ip bgp ';
            $cmd .= 'summary';
            return $cmd;
        case 'sh ip b nei':
            $cmd = (!empty($vrf)) ? "show bgp vrf $vrf all " : 'show ip bgp ';
            $cmd .= 'neighbors';
            return $cmd;
        case 'sh ip b nei adv':
            $cmd = (!empty($vrf)) ? "show bgp vrf $vrf all " : 'show ip bgp ';
            $cmd .= 'neighbors ##__ARG__## advertised-routes';
            return $cmd;
        case 'sh ip b nei rec':
            $cmd = (!empty($vrf)) ? "show bgp vrf $vrf all " : 'show ip bgp ';
            $cmd .= 'neighbors ##__ARG__## received-routes';
            return $cmd;
        case 'sh ip b damp':
            $cmd = (!empty($vrf)) ? "show bgp vrf $vrf all " : 'show ip bgp ';
            $cmd .= 'dampening dampened-paths';
            return $cmd;
        case 'sh ip b flap':
            $cmd = (!empty($vrf)) ? "show bgp vrf $vrf all " : 'show ip bgp ';
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
        case 'sh ip int br':
            $cmd = 'show ip interface brief';
            return $cmd;
        case 'sh ip r':
            $cmd = 'show ip route ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            return $cmd;
default:
            return false;
        }
    }
}

# vim: set et ts=4 sw=4:
