<?php
/*
 * STANDAR DE NISSI CONSULTAS
 * 
 */
namespace Principal\Model;
 
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Principal\Model\Paranomina; // Album de consultas
use Principal\Model\AlbumTable; // Parametros de nomina
 
/// INDICE

// Generacion de empleados 


class Retefuente extends AbstractTableGateway
{
   protected $table  = '';
   
   
   public $dbAdapter;
   public $salarioMinimo;
   public $horasDias;
    
   public function __construct(Adapter $adapter)
   {
        $this->adapter = $adapter;
        $this->initialize();
        // Parametros de nomina para funciones de consulta 
        $pn = new Paranomina($this->adapter);
        $dp = $pn->getGeneral1(1);
        $this->salarioMinimo=ltrim($dp['formula']);   
        $dp = $pn->getGeneral1(2);
        $this->horasDias=$dp['valorNum'];  
   }   
   // 1. Retencion en la fuente
   public function getReteConc($id, $idEmp)
   {
   	
      $result=$this->adapter->query("select a.porcentaje, a.promSalud, a.tipo        
       from a_empleados_rete a
           where a.idEmp= ".$idEmp ,Adapter::QUERY_MODE_EXECUTE);  
      $datos = $result->current();  	  	  
	  $porcentaje = $datos['porcentaje']; // porcentaje fijo por promedio año anterior 
	  $promSalud  = $datos['promSalud']; // promedio salud año anterior	  
	  $tipo       = $datos['tipo']; // Tipo de retencion 	
	
   	  // 1. TOTAL DEVENGADOS ( CONCEPTOS QUE HACEN PARTE DE LA RETENCION EN LA FUENTE)
      $result=$this->adapter->query("Select ( sum(a.devengado) - sum(a.deducido) ) as valor  
            from  n_nomina_e_d a 
               inner join n_conceptos b on a.idConc=b.id 
               inner join n_conceptos_pr c on c.idConc=b.id 
               inner join n_nomina_e d on d.id=a.idInom
               inner join a_empleados e on e.id=d.idEmp
               inner join n_nomina f on f.id = d.idNom 
                 where c.idProc=8 and # Todos los conceptos que hacen part de la retencion  
                 a.idInom = ".$id ,Adapter::QUERY_MODE_EXECUTE);  
      $datos = $result->current();
	  $totIngresosBases = $datos['valor'];
	  
	  echo 'Inregsos: '.number_format($totIngresosBases).'<br />';
	  
   	  // 2. TOTAL EXENTAS ( EJEMPLO: PENSION, FONDO DE SOLIDARIDAD, ECT )
      $result=$this->adapter->query("Select (  sum(a.deducido) - sum(a.devengado) ) as valor  
            from  n_nomina_e_d a 
               inner join n_conceptos b on a.idConc=b.id 
               left join n_conceptos_pr c on c.idConc=b.id 
               inner join n_nomina_e d on d.id=a.idInom
               inner join a_empleados e on e.id=d.idEmp
               inner join n_nomina f on f.id = d.idNom 
                 where c.idProc=14 and a.idInom = ".$id ,Adapter::QUERY_MODE_EXECUTE);  
      $datos = $result->current();
	  $totExentas = $datos['valor'];
	  	  
	  echo 'Total exentas : '.number_format($totExentas).'<br />';

   	  // 3. TOTAL EXENTAS ( PENSION ) ESPECIAL PARA 384 
      $result=$this->adapter->query("Select (  sum(a.deducido) - sum(a.devengado) ) as valor  
            from  n_nomina_e_d a 
               inner join n_conceptos b on a.idConc=b.id 
               left join n_conceptos_pr c on c.idConc=b.id 
               inner join n_nomina_e d on d.id=a.idInom
               inner join a_empleados e on e.id=d.idEmp
               inner join n_nomina f on f.id = d.idNom 
                 where a.idConc in ('11') and a.idInom = ".$id ,Adapter::QUERY_MODE_EXECUTE);  
      $datos = $result->current();
	  $totPen = $datos['valor'];	  	  
	  
	  echo 'Total pension : '.number_format($totPen).'<br />';
	  
   	  // 3. TOTAL DEDUCCIONES FIJAS  
      $result=$this->adapter->query("select sum(b.valor) as valor, a.porcentaje, a.promSalud, a.tipo        
       from a_empleados_rete a
           inner join a_empleados_rete_d b on b.idEret = a.id
           inner join n_rete_conc c on c.id = b.idCret
           where a.idEmp= ".$idEmp." and c.formula = '' " ,Adapter::QUERY_MODE_EXECUTE);  
      $datos = $result->current();
	  $totDeducciones = $datos['valor'];	  	  	  

   	  // 3.1 TOTAL DEDUCCIONES FORMULADAS  
      $result=$this->adapter->query("select c.formula      
       from a_empleados_rete a
           inner join a_empleados_rete_d b on b.idEret = a.id
           inner join n_rete_conc c on c.id = b.idCret
           where a.idEmp= ".$idEmp." and c.formula != ''" ,Adapter::QUERY_MODE_EXECUTE);  
       $datos = $result->toArray();
	   $totDeduccionesF = 0;
 	   foreach($datos as $dat)
	   {
          $formula = $dat['formula'];
          eval("\$totDeduccionesF = $formula;");           
	   }	  
	  $totDeducciones = $totDeducciones + $totDeduccionesF; // Sumo las deducciones con formulas y las que no tiene formula	  
	  	  
	  	  
	  echo 'Total deducciones: '.number_format($totDeducciones).'<br />';
	  echo 'Porcenta: '.number_format($porcentaje).'<br />';
	  
	  $red = 3;
	  
	  // Calulos de pie
	  $baseGravableBruta = $totIngresosBases - $totExentas -  ( $totDeducciones + $promSalud ) ;
	  
	  echo 'baseGravableBruta:  '.number_format($baseGravableBruta, $red).'<br />';
	  
	  $pn = new Paranomina($this->adapter);
      $dp = $pn->getGeneral1(4); // Funcion para traer parametros de nomina
      $uvt = $dp['valorNum'];// Uvt
              	  
	  // Validar si el 25% excede 240 UVT
	  $rentaExenta = $baseGravableBruta * ( 25/100 );	   
	  	  	  
	  if ( $rentaExenta > ( $uvt * 240 ) ) // Si el valor esta por debajo de los UVT toma el limite de UTV
	      $rentaExenta = ($uvt * 240);
	  
	  $rentaExenta = ceil( $rentaExenta );
	  
	  echo 'rentaExenta:  '.number_format( $rentaExenta, $red ).'<br />';
	  
	  // CALCULOS PARA CALCULO DE LA RENTECION PARA 383 ---------------------------------------------------------
	  $baseGravableNeta = $baseGravableBruta - $rentaExenta;
	  $baseGravableNeta383 = $baseGravableBruta - $rentaExenta;
	  
	  echo 'baseGravableNeta 383: '.number_format($baseGravableNeta383,$red).'<br />';
	  	  
	  // Se multiplica por el porcentaje del empleado
	  $reteArt383 = ( $porcentaje/100 ) * $baseGravableNeta; 	  
	  	  
	  echo 'reteArt383:  '.number_format($reteArt383, $red ).'<br />';
	  
	  
	  // CALCULOS PARA CALCULO DE LA RENTECION PARA 384 ---------------------------------------------------------
	  $baseGravableNeta = ( $totIngresosBases - $totPen ) / $uvt ;
    	  
	  echo 'baseGravableNeta 384: '.number_format($baseGravableNeta,$red).'<br />';
	  		  
      $result=$this->adapter->query("select impuesto from n_rete_art384
                        where ".$baseGravableNeta." >= desde and ".$baseGravableNeta." <= hasta" ,Adapter::QUERY_MODE_EXECUTE);  
      $datos = $result->current();
	  $reteArt384 = $datos['impuesto'] * $uvt ; 
	  
	  echo 'reteArt384:  '.number_format($reteArt384,$red).'<br />';
	  
	  // Retornar valor final - proc 2	   
	  $valorProce2 = 0;
	  if ( $reteArt384 > $reteArt383 )
	     $valorProce2 = $reteArt384;
	  else 
	     $valorProce2 = $reteArt383;
	  
	  echo 'valorProce2:  '.number_format( $valorProce2 ).'<br />';
	  
	  // Retornar valor final - proc 1
	  $por = round( ( $baseGravableNeta383 / $uvt ), 0 );
	  
	  echo 'por :  '.$por.'<br />';
	  
      $result=$this->adapter->query("select
        case when ( ".$por." >= desde and ".$por." < hasta ) then ## Caso 1
           round(( ( ( ".$baseGravableNeta383." / ".$uvt." ) - 95 ) * ( 19 / 100) ) * ".$uvt.", 0) else 

             case when ( ".$por." >= desde and ".$por." < hasta ) then ## Caso 2
                round(( ( ( ( ".$baseGravableNeta383." / ".$uvt." ) - 150 ) * ( 28/100 ) + 10 ) * ".$uvt." ), 0 )  else 
 	  
                case when ( ".$por." >= desde and ".$por." < hasta ) then ## Caso 3
                   round(( ( ( ( ".$baseGravableNeta383." / ".$uvt." ) - 360 ) * ( 33/100 ) + 69 ) * ".$uvt." ), 0 )  else 0
                end # Fin caso 3  
            end # Fin caso 1  
        end as valor # Fin caso 2           
        from n_rete_art383" ,Adapter::QUERY_MODE_EXECUTE);  
      $datos = $result->toArray();
	  $valorProce1 = 0;
	  foreach($datos as $dat)
	  {
	  	 if ($dat['valor']>0)
	        $valorProce1 = $dat['valor'] ;	  
	  }
	  
	  echo 'valorProce1:  '.number_format( $valorProce1 ).'<br />';	  
	  
	  if ($tipo==2)
         return $valorProce2;
      else
	     return $valorProce1;
    }

}
?>

