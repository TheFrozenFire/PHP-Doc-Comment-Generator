<?php
class PHPDocGen_Generator {
	protected $reflectors;
	protected $generated;

	public function __construct(Array $reflectors) {
		foreach($reflectors as $reflector)
			if(!(
				is_a($reflector, 'ReflectionFunction')
				|| is_a($reflector, 'ReflectionClass')
				|| is_a($reflector, 'ReflectionInterface')
				|| is_a($reflector, 'ReflectionMethod')
			)) {
				trigger_error('The provided array must contain only reflectors for functions, classes and interfaces', E_USER_WARNING);
				return false;
			}
		
		$this->reflectors = $reflectors;
	}
	
	public function process($provider_name) {
		if(!(
			interface_exists($provider_name)
			&& in_array('PHPDocGen_Provider', class_implements($provider_name))
		)) {
			trigger_error('The provider must exist and implement PHPDocGen_Provider', E_USER_WARNING);
			return false;
		}
		
		foreach($reflectors as $reflector) {
			switch(true) {
				case is_a($reflector, 'ReflectionFunction'):
					$name = $reflector->getName();
					$docBlock = $provider::getFunctionDoc($reflector);
					break;
				case is_a($reflector, 'ReflectionClass'):
					$name = $reflector->getName();
					$docBlock = $provider::getClassDoc($reflector);
					break;
				case is_a($reflector, 'ReflectionInterface'):
					$name = $reflector->getName();
					$docBlock = $provider::getInterfaceDoc($reflector);
					break;
				case is_a($reflector, 'ReflectionMethod'):
					$name = "{$reflector->getDeclaringClass()}::{$reflector->getName()}";
					$docBlock = $provider::getMethodDoc($reflector);
					break;
				default:
					continue;
			}
			$this->generated[$provider_name][$name] = $docBlock;
		}
	}
}
