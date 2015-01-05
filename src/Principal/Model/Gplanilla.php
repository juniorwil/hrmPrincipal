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
use Principal\Model\AlbumTable; // Parametros de nomina
 
/// INDICE

// Generacion de empleados 

class Gplanilla extends AbstractTableGateway
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
   // Generacion de empleados 
   public function modGeneral($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);

    }                  
   // Generacion de empleados
   public function getNominaE($id,$idg)
   {
      $result=$this->adapter->query("insert into n_planilla_unica_e (idPla, idEmp ) 
       (select b.id , a.id 
         from a_empleados a 
			inner join n_planilla_unica b on b.idGrupo=a.idGrup and b.id = ".$id."
         WHERE a.activo=0 and a.estado=0 
         and not exists (SELECT null from n_planilla_unica_e where a.id=idEmp and idPla=".$id." )"
         . " and a.idGrup=".$idg." )",Adapter::QUERY_MODE_EXECUTE);
    }
 
   // Modificacion planilla unida por empleado
   public function getPlanillaE($id, $campo, $valor )
   {
      $result=$this->adapter->query("update n_planilla_unica_e set ".$campo."= ROUND( ".$valor.",0) where id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
    }    
    
   // Modificacion planilla unida por empleado
   public function getPlanillaEr($id, $campo, $valor )
   {
      $val = 0; 
      if ($valor>0)
          $val = 1;    
      $result=$this->adapter->query("update n_planilla_unica_e set ".$campo."= ".$val." where id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
    }        
}
?>