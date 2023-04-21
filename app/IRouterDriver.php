<?php 

interface IRouterDriver {
	public function requires_arg( string $command): bool;
    public function postprocess_output( string $cmd, string $str): string;
	public function translate_cmd( string $command, array $config);
}

# vim: set et ts=4 sw=4:
