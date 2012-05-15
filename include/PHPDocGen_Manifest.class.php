<?php
class PHPDocGen_Manifest {
	protected $manifest = array();
	protected $reflectors = array();

	public function __construct($files = null) {
		if(is_string($files)) $this->globManifest($files);
		elseif(is_array($files))
			foreach($files as $expression) {
				if(is_string($expression)) $this->globManifest($expression);
				elseif(is_a($expression, "FilesystemIterator")) $this->iteratorManifest($expression);
				else continue;
			}
		elseif(is_a($files, "FilesystemIterator")) $this->iteratorManifest($files);
	}
	
	public function globManifest($expression) {
		$this->iteratorManifest(glob($expression));
	}
	
	public function iteratorManifest($iterator) {
		foreach($iterator as $file)
			$this->addFile($file);
	}
	
	public function addFile($file) {
		if(!is_readable($file) || !is_file($file)) return;
		
		$this->manifest[] = realpath($file);
	}
	
	public function process() {
		foreach($this->manifest as $file) include_once($file);
		
		$this->generateReflectors();
	}
	
	public function generateReflectors() {
		$functions = get_defined_functions();
		$functions = $functions["user"];
		
		foreach($function as $function) {
			$reflector = new ReflectionFunction($function);
			if($reflector && in_array(realpath($reflector->getFileName()), $this->manifest))
				$this->reflectors[] = $reflector;
		}
		
		$classes = get_declared_classes();
		
		foreach($classes as $class) {
			$reflector = new ReflectionClass($class);
			if($reflector && in_array(realpath($reflector->getFileName()), $this->manifest))
				$this->reflectors[] = $reflector;
				
			$methods = get_class_methods($class);
			foreach($methods as $method) {
				$methodReflector = new ReflectionMethod($class, $method);
				if($methodReflector)
					$this->reflectors[] = $methodReflector;
			}
		}
		
		$interfaces = get_declared_interfaces();
		
		foreach($interfaces as $interface) {
			$reflector = new ReflectionInterface($interface);
			if($reflector && in_array(realpath($reflector->getFileName()), $this->manifest))
				$this->reflectors[] = $reflector;
		}
		
		return $this->reflectors;
	}
	
	public function getReflectors() {
		return $this->reflectors;
	}
}
