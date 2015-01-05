<?php
/*
 * FUNCIONES DE NOMINA NISSI
 * 
 */
namespace Principal\Model;
 
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Principal\Model\Paranomina; // Parametros de nomina
use Principal\Model\AlbumTable; // Libro de consultas
use Principal\Model\Gnomina; // Generacion de nomina

/// INDICE

//// FUNCIONES BASICAS ------------------------------------------
// 0. FUNCION GENERAL PARA CALCULOS EN PLANILLAS
// 01. VALOR DE FORMULAS
// 1. Dias del mes trabajador 
// 2. Sumatoria ley 100 
     
//// FUNCIONES GENERALES ----------------------------------------
class PlanillaFunc extends AbstractTableGateway
{
   protected $table  = '';   
      
   public $dbAdapter;
   public $salarioMinimo;
   public $horasDias;
   public $salarioMinimoCovencional;
     
   public function __construct(Adapter $adapter)
   {
        $this->adapter = $adapter;
        $this->initialize();
        // Parametros de nomina para funciones de consulta 
        $pn = new Paranomina($this->adapter);
        $dp = $pn->getGeneral1(1);
        $this->salarioMinimo=$dp['formula'];
        $dp = $pn->getGeneral1(2);
        $this->horasDias=$dp['valorNum'];
        $dp = $pn->getGeneral1(3);
        $this->salarioMinimoCovencional=$dp['formula'];// Salario minimo convencional        
   }
    
   // ----------------------------------------------------- 
   // FORMULAS FIJAS EN PROGRAMA *-----------------------------------------------------------------------------------------------
   // ------------------------------------------------- 
   // 1. Dias del mes trabajador 
   public function getDiasEmp($id)
   {
      $result=$this->adapter->query("select 30 as valor 
              from a_empleados where id=".$id,Adapter::QUERY_MODE_EXECUTE);

      $datos = $result->current();
      return $datos;
    }                   

   // 2. Sumatoria procesos Ley 100
   public function getLey($id, $idEmp)
   {
      $result=$this->adapter->query("Select case f.tipo when 1 then sum(e.devengado) when 2 then sum(e.deducido) end as valor  
                        from n_planilla_unica a 
                        inner join n_nomina b on year(b.fechaI) = a.ano and  month(b.fechaI) = a.mes # Traer datos del mes en cuestion
                        inner join n_planilla_unica_e c on c.idPla = a.id
                        inner join n_nomina_e d on d.idNom = b.id and d.idEmp = c.idEmp
                        inner join n_nomina_e_d e on e.idInom = d.id 
			inner join n_conceptos f on f.id = e.idConc
                        inner join n_conceptos_pr g on g.idConc = f.id
                        where g.idProc=1 # 1 es proceso de ley 100
                        and c.idEmp=".$idEmp." and a.id=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();      
      return $datos;
   }                           

   // 3. Sumatoria fondos de solidaridad
   public function getSolidaridad($id, $idEmp)
   {
      $result=$this->adapter->query("Select case when sum(e.deducido) != null then sum(e.deducido) else 0 end as valor  
                        from n_planilla_unica a 
                        inner join n_nomina b on year(b.fechaI) = a.ano and  month(b.fechaI) = a.mes # Traer datos del mes en cuestion
                        inner join n_planilla_unica_e c on c.idPla = a.id
                        inner join n_nomina_e d on d.idNom = b.id and d.idEmp = c.idEmp
                        inner join n_nomina_e_d e on e.idInom = d.id 
                        where e.idConc = 21 # 1 Fondos de solidaridad
                        and c.idEmp=".$idEmp." and a.id=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();      
      return $datos;
   }                              
   
   // 4. Sumatoria caja de compensacion 
   public function getCaja($id, $idEmp)
   {
      $result=$this->adapter->query("Select case f.tipo when 1 then sum(e.devengado) when 2 then sum(e.deducido) end as valor  
                        from n_planilla_unica a 
                        inner join n_nomina b on year(b.fechaI) = a.ano and  month(b.fechaI) = a.mes # Traer datos del mes en cuestion
                        inner join n_planilla_unica_e c on c.idPla = a.id
                        inner join n_nomina_e d on d.idNom = b.id and d.idEmp = c.idEmp
                        inner join n_nomina_e_d e on e.idInom = d.id 
			inner join n_conceptos f on f.id = e.idConc
                        inner join n_conceptos_pr g on g.idConc = f.id
                        where g.idProc=1 # 1 es proceso de ley 100
                        and c.idEmp=".$idEmp." and a.id=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();      
      return $datos;
   }                
   
   // 5. Estados del empleado en el mes 
   public function getEstados($id, $idEmp)
   {
      $result=$this->adapter->query("Select d.idInc, d.idVac 
                        from n_planilla_unica a 
                        inner join n_nomina b on year(b.fechaI) = a.ano and  month(b.fechaI) = a.mes # Traer datos del mes en cuestion
                        inner join n_planilla_unica_e c on c.idPla = a.id
                        inner join n_nomina_e d on d.idNom = b.id and d.idEmp = c.idEmp
                        where a.id = ".$id." and c.idEmp = ".$idEmp ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();      
      return $datos;
   }                               

   
}