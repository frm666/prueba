<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style type="text/css">
.error { background: #d33; color: white; padding: 0.2em; }
</style>
</head>
<body>
<?php

include 'Persona.php';

function checkCampo( $campo )
{	
	if( strlen( $campo ) < 5 ) 
		$resultado = 0;
	else 
		$resultado = 1;
	return $resultado;
}
function validateField( $fieldName, $missingFields ) 
{
	if ( in_array( $fieldName, $missingFields ) ) 
	{
		echo ' class="error"';
	}
}
function setValue( $fieldName ) 
{
	if ( isset( $_POST[$fieldName] ) ) 
	{
		echo $_POST[$fieldName];
	}
}
function processForm( $campos ) 
{
	foreach ( $campos as $campo ) 
	{
		//echo $campo[ 'nombre' ] . $campo[ 'funcion' ];
		if ( !isset( $_POST[$campo[ 'nombre' ] ] ) or !$_POST[$campo[ 'nombre' ] ] ) 
		{
			$missingFields[] = $campo[ 'nombre' ];
		}
		elseif( ! call_user_func( $campo[ 'funcion' ],  $_POST[$campo[ 'nombre' ] ] ) )
		{
			$missingFields[] = $campo[ 'nombre' ];
		}
	}
	if( isset ( $missingFields ) )
		return( $missingFields );
	else
		return null;
}

function error()
{
	printf("<a href=\"aplicacion2.php\">Se ha producido un errror</a>");
}

function editar( $codigo, $missingFields )
{	
	$persona = Persona::getByCodigo($codigo);
	
	if( $persona == null )
	{
		header('Location: aplicacion2.php?opcion=error');
	}
	else
	{
		?>
		<form method="post" action="aplicacion2.php">
		<input type="hidden" name="codigo" value="<?php echo $persona->codigo?>" >
		<input type="hidden" name="opcion" value="editar_tratamiento" >
		<label for=”nombre” <?php validateField( "nombre",	$missingFields ) ?>>Nombre</label>
		<input type="text" name="nombre" value="<?php echo $persona->nombre ?>">
		<label for=”apellidos” <?php validateField( "apellidos",	$missingFields ) ?>>Apellidos</label>
		<input type="text" name="apellidos" value="<?php echo $persona->apellidos?>">
		<input type="submit" name= "accion" value="Editar">
		<input type="submit" name= "accion" value="Eliminar">
		<input type="submit" name= "accion" value="Cancelar">
		</form>
		<?php
	}

}

function create( $missingFields )
{
	
	?>
	<form method="post" action="aplicacion2.php">
	<input type="hidden" name="opcion" value="create_tratamiento" >
	<label for=”nombre” <?php validateField( "nombre",	$missingFields ) ?> >Nombre</label>
	<input type="text" name="nombre" value="">
	<label for=”apellidos” <?php validateField( "apellidos", $missingFields ) ?> >Apellidos</label>
	<input type="text" name="apellidos" value="">
	<input type="submit" name="accion" value="Aceptar">
	<input type="submit" name="accion" value="Cancelar">
	</form>
	<?php


}

function create_tratamiento()
{
	switch( $_REQUEST['accion'] ){
		case 'Aceptar':
			if( !isset($_POST["nombre"])|| !isset($_POST["apellidos"]))
			{
				header('Location: aplicacion2.php?opcion=error');
			}
			else
			{	$persona = new Persona();
				$persona->nombre = $_POST["nombre"];
				$persona->apellidos = $_POST["apellidos"];
				$persona->save();
			}
			header('Location: aplicacion2.php');
			break;	
		case 'Cancelar':
			header('Location: aplicacion2.php');
			break;				
	}
}
function editar_tratamiento()
{
	if( isset( $_REQUEST['codigo']) )
	{
		switch( $_REQUEST['accion'] ){
		case 'Editar':
			$persona = Persona::getByCodigo( $_REQUEST['codigo']);
			$persona->nombre = ( $_REQUEST['nombre']);
			$persona->apellidos = ( $_REQUEST['apellidos']);
			$persona->save();
			header('Location: aplicacion2.php');
			break;	
		case 'Eliminar':
			$persona = Persona::getByCodigo( $_REQUEST['codigo']);
			$persona->delete();
			header('Location: aplicacion2.php');
			break;
		case 'Cancelar':
			header('Location: aplicacion2.php');
			break;				
		}
	}
	else
		error();
}


function listado()
{
	$lista = Persona::getAll();
	
	foreach( $lista as $item )
	{
		printf( "<br>%s %s ", $item->nombre, $item->apellidos );
		printf( "<a href= \"aplicacion2.php?opcion=editar&codigo=%s\">>></a>", $item->codigo );
	}
	printf( "<br><a href= \"aplicacion2.php?opcion=create\">Nuevo</a>" );
}

$opcion=isset( $_REQUEST['opcion'])?$_REQUEST['opcion']:'listado';

switch( $opcion ){
case 'listado':
	listado();
	break;
case 'error':
	error();
	break;
case 'create':
	create( array()  );
	break;
case 'create_tratamiento':
	$campos = array( 
				array( 'nombre' => 'nombre', 'funcion' => 'checkCampo' ), 
				array( 'nombre' => 'apellidos', 'funcion' => 'checkCampo' ) );
	$missingFields = processForm( $campos );

	if ( $missingFields ) 
	{
		create( $missingFields );
	} 
	else
	{
		create_tratamiento();
	}
	
	break;
case 'editar':
	if( isset( $_REQUEST['codigo']))
	{
		editar($_REQUEST['codigo'],array() );
	}
	break;
case 'editar_tratamiento':
	$campos = array( 
				array( 'nombre' => 'nombre', 'funcion' => 'checkCampo' ), 
				array( 'nombre' => 'apellidos', 'funcion' => 'checkCampo' ) );
	$missingFields = processForm( $campos );

	if ( $missingFields ) 
	{
		editar($_REQUEST['codigo'], $missingFields );
	} 
	else
	{
		editar_tratamiento();
	}
	break;
}
