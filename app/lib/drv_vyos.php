<?php
namespace lib;

class drv_vyos implements \IRouterDriver {
    public function requires_arg( string $command): bool
    {
        switch( $command)
        {
        case 'ping':
        case 'trac':
        case 'sh ip b nei adv':
        case 'sh ip b nei rec':
        case 'sh ip b rgx':
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
        case 'ping':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper ping ##__ARG__## ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= (!empty($source)) ? "interface $source " : '';
            $cmd .= 'count 4 deadline 1 interval 0.2 ';
            return $cmd;
        case 'trac':
            $cmd = '/usr/bin/sudo ';
            $cmd .= (!empty($vrf)) ? "ip vrf exec $vrf " : '';
            $cmd .= 'traceroute -Im 20 -q 1 ';
            $cmd .= (!empty($source)) ? "-s $source " : '';
            $cmd .= '##__ARG__## ';
            return $cmd;
        case 'sh ip b sum':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip bgp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'summary ';
            return $cmd;
        case 'sh ip b nei':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip bgp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'neighbors ';
            return $cmd;
        case 'sh ip b nei adv':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip bgp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'neighbors ##__ARG__## advertised-routes';
            return $cmd;
        case 'sh ip b nei rec':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip bgp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'neighbors ##__ARG__## received-routes';
            return $cmd;
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
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip bgp ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            $cmd .= 'regex \'##__ARG__##\'';
            return $cmd;
        case 'sh ip ig gr':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip igmp groups';
            return $cmd;
        case 'sh ip ig int':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip igmp interfaces';
            return $cmd;
        case 'sh ip int br':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show interfaces';
            return $cmd;
        case 'sh ip r':
            $cmd = '/opt/vyatta/bin/vyatta-op-cmd-wrapper show ip route ';
            $cmd .= (!empty($vrf)) ? "vrf $vrf " : '';
            return $cmd;
default:
            return false;
        }
    }
}

# vim: set et ts=4 sw=4:
