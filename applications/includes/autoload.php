<?php


	class autoload
	{

		public function __construct()
		{
			return;
		}

		public static function init()
		{
			#Order in which to call autoloaders
			spl_autoload_register( array( 'self', 'Load_From_Class_Folder' ), false );
			spl_autoload_register( array( 'self', 'Load_From_Applications_Folder' ), false );
			spl_autoload_register( array( 'self', 'Load_From_Modules_Folder' ), false );
		}

		public static function register( $callback, $prepend = false )
		{
			spl_autoload_register( $callback, false, $prepend );
		}

		#Load From Class Folder
		public static function Load_From_Class_Folder( $class_name )
		{
			$class_name = str_replace( '_', '/', $class_name );

			$require_path = CLASS_PATH . '/' . $class_name . '.class.php';

			if( file_exists($require_path) )
			{
				require_once $require_path;
				return true;
			}
			return false;
		}

		#Load From Global Folder
		public static function Load_From_Applications_Folder( $class_name )
		{
			$class_name = str_replace( '_', '/', $class_name );

			$require_path = APPLICATION_PATH . '/models/' . $class_name . '.model.php';

			if(file_exists($require_path))
			{
				require_once $require_path;
				return true;
			}
			return false;
		}

		#Load From Application Folder
		public static function Load_From_Modules_Folder( $class_name )
		{
			$class_name = str_replace( '_', '/', $class_name );

			$require_path = MODULE_PATH . '/' . Light_Router::$module . '/models/' . $class_name . '.model.php';

			if(file_exists($require_path))
			{
				require_once $require_path;
				return true;
			}
			return false;
		}
	}
