<?php
namespace exporter;

class regex {

    //matches comment for inactive command
    public static $regexcominactive = '[#]+[^#]*#+';

    //matches minutes 2 or 0-5 or * or 1,2 or */5
    public static $regexmin = '(?:\*\/[0-9]*|\*|[0-5]?[0-9]|(?:[0-5]?[0-9])-(?:[0-5]?[0-9])|(?:[0-5]?[0-9])(?:,(?:[0-5]?[0-9]))+)';

    //matches hours 2 or 0-5 or * or 1,2 or */5
    public static $regexhrs = '(?:\*\/[0-9]*|\*|(?:[0-1]?[0-9]|[2]?[0-3])|(?:(?:[0-1]?[0-9]|[2]?[0-3]))-(?:(?:[0-1]?[0-9]|[2]?[0-3]))|(?:(?:[0-1]?[0-9]|[2]?[0-3]))(?:,(?:(?:[0-1]?[0-9]|[2]?[0-3])))+)';

    //matches day of month 2 or 0-5 or * or 1,2 or */5
    public static $regexdom = '(?:\*\/[0-9]*|\*|(?:[1-2]?[0-9]|[3]?[0-1])|(?:(?:[1-2]?[0-9]|[3]?[0-1]))-(?:(?:[1-2]?[0-9]|[3]?[0-1]))|(?:(?:[1-2]?[0-9]|[3]?[0-1]))(?:,(?:(?:[1-2]?[0-9]|[3]?[0-1])))+)';

    //matches month 2 or 0-5 or * or 1,2 or */5
    public static $regexmon = '(?:\*\/[0-9]*|\*|(?:[0]?[0-9]|[1]?[0-2])|(?:(?:[0]?[0-9]|[1]?[0-2]))-(?:(?:[0]?[0-9]|[1]?[0-2]))|(?:(?:[0]?[0-9]|[1]?[0-2]))(?:,(?:(?:[0]?[0-9]|[1]?[0-2])))+)';

    //matches day of week 2 or 0-5 or * or 1,2 or */5
    public static $regexdow = '(?:\*\/[0-9]*|\*|[0-6]|[0-6]-[0-6]|\*\/[0-6]|[0-6](?:,[0-6])+)';

    //matches comment at the end of the line
    public static $regexcomeol = '#*\s*(.[^#]+)';
}
