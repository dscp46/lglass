<?php
namespace lib;

class drv_linux implements \IRouterDriver {
    public function requires_arg( string $command): bool
    {
        switch( $command)
        {
        case 'ping':
        case 'trac':
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
            $cmd = (!empty($vrf)) ? "ip vrf exec $vrf " : '';
            $cmd .= 'ping -i 0.2 -c 4 -w 2 -W 2 ';
            $cmd .= (!empty($source)) ? "-I '$source' " : '';
            $cmd .= '##__ARG__##';
            return $cmd;
        case 'trac':
            $cmd = (!empty($vrf)) ? "ip vrf exec $vrf " : '';
            $cmd .= 'traceroute -Im 20 -q 1 ';
            $cmd .= (!empty($source)) ? "-s '$source' " : '';
            $cmd .= '##__ARG__##';
            return $cmd;
        case 'sh ip int br':
            $cmd = 'ip address ';
            $cmd = (!empty($vrf)) ? "show vrf $vrf " : '';
            return $cmd;
        case 'sh ip r':
            $cmd = (!empty($vrf)) ? "ip vrf exec $vrf " : '';
            $cmd .= 'ip route';
            return $cmd;
        default:
            return false;
        }
    }
}

# vim: set et ts=4 sw=4:
