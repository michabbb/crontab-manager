<?php

namespace exporter\utils;

class utils
{

	/**
	 * @return bool
	 */
	public static function isCLI(): bool {
		return PHP_SAPI === 'cli';
	}

    /**
     * @param $value
     * @return bool|string
     */
    public static function getParamType($value) : string
    {
        if (\is_int($value)) {
            return 'i';
        }

        if (\is_string($value)) {
            return 's';
        }

        if (\is_float($value)) {
            return 'd';
        }

        return 's';
    }

    /**
     * @param $mode
     * @param $txt
     */
    public static function debug($mode, $txt): void {
        //$trace = debug_backtrace();
        //echo $trace[1]['class'] . ' : MODE: ' . $mode . ' -> ' . $txt . "\n";
    }

    /**
     * @param $dir
     * @return mixed
     */
    public static function find_all_files($dir) : array
    {
        $root = scandir($dir, SCANDIR_SORT_ASCENDING);
        $result = array();
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file("$dir/$value")) {
                $result[$value] = 1;
                continue;
            }
            /** @noinspection ForeachSourceInspection */
            foreach (self::find_all_files("$dir/$value") as $valuef) {
                $result[$valuef] = 1;
            }
        }

        return $result;
    }
}