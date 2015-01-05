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
class IntegrarFunc extends AbstractTableGateway
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
   // 1. Integracion de nomina 
   public function getIntegrarNomina($id)
   {
      $result=$this->adapter->query("insert into n_nomina_e_d_integrar (idNom, idInom ,idCon, nomCon, codCon, valor, idPref, pref,  codCta, gas, natCta, ter, nitTer, nitFon, idFonS, nitFonS, idFonP, nitFonP, error )
select a.idNom, b.id as idInom, d.id, d.nombre as nomCon , d.codigo , 
case when b.devengado > 0 then b.devengado else b.deducido end as valor, 
h.id as idPref, h.nombre as pref , 
case when e.gast=0 then # Si no es gasto lleva la cuenta completa
  d.codCta else # Si es une el prefijo a la cuenta de gasto
  concat( h.nombre, substr(d.codCta,3,100) )   
end as codCta, 
case when e.gast=0 then 'N' else 'S' end as gast,
case when d.natCta=0 then 'Debito' else 'Credito' end as natCta , 
case when e.ter=0 then 'N' else 'S' end as ter , 
case when e.ter = 0 then ' ' else  # No maneja tercero
  case when ( e.ter = 1 and d.nitFon = 0 and d.idTer > 0 ) then  # Maneja el tercero preasignado
      i.codigo  else 
         case when ( e.ter = 1 and d.nitFon = 1 and d.fondo = 1 ) then  # Maneja el tercero del fondo de salud
           f.nit  else 
              case when ( e.ter = 1 and d.nitFon = 1 and d.fondo = 2 ) then  # Maneja el tercero del fondo de pension
                g.nit  else  
                  case when ( e.ter = 1 and d.nitFon = 0 and d.idTer = 0 ) then  # Maneja el nit del empleado
                      c.CedEmp  end 
         end       
      end
  end 
 end as nitTer ,
case when d.nitFon=0 then 'N' else 'S' end as nitfon, f.id, f.nit as nitSal , g.id, g.nit as nitPen,
case when e.id is null then 'Sin definir' else '' end cuentaE 
from n_nomina_e a 
inner join n_nomina_e_d b on b.idInom = a.id
inner join a_empleados c on c.id = a.idEmp 
inner join n_conceptos d on d.id = b.idConc 
left join n_plan_cuentas e on e.codigo = d.codCta
inner join t_fondos f on f.id = c.idFsal
inner join t_fondos g on g.id = c.idFpen
inner join n_pref_con h on h.id = c.idPref 
left join n_terceros i on i.id = d.idTer 
where not exists (SELECT null from n_nomina_e_d_integrar where idInom = b.id )" ,Adapter::QUERY_MODE_EXECUTE);

    }                   
    
   // 2. Integracion de proviciones 
   public function getIntegrarProviciones($id)
   {
      $result=$this->adapter->query("( select a.idNom, b.id as idInom, d.id, d.nombre as nomCon , d.codigo , case when b.devengado > 0 then b.devengado else b.deducido end as valor, 
h.nombre as pref , d.codCta, case when e.gast=0 then 'N' else 'S' end as gast , case when d.natCta=0 then 'Debito' else 'Credito' end as natCta , 
e.ter, i.codigo as nitTerP , case when d.nitFon=0 then 'N' else 'S' end as nitfon, f.nit as nitSal , g.nit as nitFon, d.nombre  
from n_nomina_e a 
inner join n_nomina_e_d b on b.idInom = a.id
inner join a_empleados c on c.id = a.idEmp 
inner join n_conceptos d on d.id = b.idConc 
inner join n_plan_cuentas e on e.codigo = d.codCta
inner join t_fondos f on f.id = c.idFsal
inner join t_fondos g on g.id = c.idFpen
inner join n_pref_con h on h.id = c.idPref 
inner join n_terceros i on i.id = d.idTer ) 
" ,Adapter::QUERY_MODE_EXECUTE);

      $datos = $result->current();
      return $datos;
    }                       
   
}