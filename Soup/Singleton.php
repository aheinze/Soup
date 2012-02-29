<?php
namespace Soup;

/**
 * Singleton implementation.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
abstract class Singleton {

    private static $_instances = array();

	/**
	 * Returns instance of the called class.
	 *
	 * @return	object
	 */
    final public static function instance() {
        
        $class = get_called_class();

        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class;
        }

        return self::$_instances[$class];
    }

    /**
     * Disallow cloning of a singleton
     */
    final private function __clone() {}
}