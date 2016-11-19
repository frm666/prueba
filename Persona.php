<?php

class Database extends PDO {

	
	function __construct(){

		$dsn = "mysql:dbname=test";
		$username = "root";
		$password = "";

		try {  
			parent::__construct( $dsn, $username, $password );  
			$this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
	
		}
		catch(PDOException $e) {  

		 echo 'ERROR: ' . $e->getMessage();
		}   

	}

}
class Persona
{
    private $codigo;     // user id
    private $fields;  // other record fields

    // initialize a User object
    public function __construct()
    {
        $this->codigo = null;
        $this->fields = array('nombre' => '',
                              'apellidos' => '');
    }

    // override magic method to retrieve properties
    public function __get($field)
    {
        if ($field == 'codigo')
        {
            return $this->codigo;
        }
        else 
        {
            return $this->fields[$field];
        }
    }

    // override magic method to set properties
    public function __set($field, $value)
    {
        if (array_key_exists($field, $this->fields))
        {
            $this->fields[$field] = $value;
        }
    }

    // return if nombre is valid format
    public static function validatenombre($nombre)
    {
        return preg_match('/^[A-Z0-9]{2,20}$/i', $nombre);
    }
    
    
    
    // return an object populated based on the record's user id
    public static function getByCodigo($codigo)
    {
        $u = new Persona();

		$conexion = new Database();
		$sql = sprintf( "SELECT * FROM personas where codigo = %d", $codigo );
		$rows = $conexion->query( $sql );
		
		if( $rows->rowCount()  == 0  )
			return null;
		else
		{
			$u = new Persona();
			$row = $rows->fetch();
			$u->nombre = $row['nombre'];
            $u->apellidos = $row['apellidos'];
            $u->codigo = $codigo;
			return $u;
		}
	}
	
	// return an object populated based on the record's user id
    public static function getAll()
    {
        $v = array();
		$conexion = new Database();
        $sql = sprintf('SELECT * FROM personas' );
        $rows = $conexion->query( $sql );
		

        foreach( $rows as $row )
		{
			$u = new Persona();
			$u->nombre = $row['nombre'];
            $u->apellidos = $row['apellidos'];
            $u->codigo = $row['codigo'];
			$v[] = $u;
		}

        return $v;
    }
	
	
    
    // save the record to the database
    public function save()
    {
		$conexion = new Database();
		
        if ($this->codigo)
        {
            $query = sprintf('UPDATE personas SET nombre = "%s" , apellidos = "%s" WHERE codigo = %d',
                $this->nombre,
                $this->apellidos,
                $this->codigo);
            $rows = $conexion->exec( $query );
        }
        else
        {
            $query = sprintf('INSERT INTO personas ( nombre, apellidos) VALUES ("%s", "%s")',
                $this->nombre,
                $this->apellidos
                );
			$rows = $conexion->exec( $query );

            $this->codigo = $conexion->lastInsertId();
        }
    }
	
	// save the record to the database
    public function delete()
    {
        $conexion = new Database();
		
		if ($this->codigo)
        {
            $sql = sprintf('DELETE FROM personas WHERE codigo = %d',
                     $this->codigo);
            $conexion->exec( $sql );
        }
    }

}

function prueba()
{
	
	
	$p = new Persona();
	$p->nombre = "antonio";
	$p->apellidos = "lujan";
	$p->save();

	$a = Persona::getByCodigo( 8 );
	echo "hola";
	echo $a->nombre;
	echo $a->apellidos;
	
	$a->delete();
	
	$v = Persona::getAll();
	print_r( $v );

}




?>
