<?php 

interface IRemoteExecutable
{
    public function __construct( array $config, array $global_params);
    public function exec_remote( string $command): ?array;
}

# vim: set et ts=4 sw=4:
