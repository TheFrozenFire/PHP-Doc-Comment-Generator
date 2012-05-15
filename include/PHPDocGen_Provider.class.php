<?php
interface PHPDocGen_Provider {
	public static function getFunctionDoc(ReflectionFunction $reflector);
	public static function getClassDoc(ReflectionClass $reflector);
	public static function getInterfaceDoc(ReflectionInterface $reflector);
	public static function getMethodDoc(ReflectionMethod $reflector);
}
