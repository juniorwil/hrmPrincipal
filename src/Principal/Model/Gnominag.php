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
// Insertar nov automaticas ( n_nomina_e_d ) por tipos de automaticos 
// Insertar nov automaticas ( n_nomina_e_d ) por conceptos automaticos
// Insertar nov automaticas ( n_nomina_e_d ) por otros automaticos
// Consulta para recorrer nomina generada
// Documento de novedades por empleado de acuerdo al tipo 
// Generar periodos de nominas por tipos de nomina, grupos de empleados y calendario 
// ( REGISTRO DE VACACIONES ) ( n_vacaciones ) 
// Vacaciones de empleados
// Incapacidades de empleados
// EMBARGOS NOMINA ( n_nomina_e_d ) 
// ( DIAS TRABAJADOS ULTIMO PERIODO CESANTIAS ) 

class Gnominag extends AbstractTableGateway
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
   public function getNominaE($id,$idg, $idEmp)
   {
      if ($idEmp=='')// Se generan todos los empleados del grupo
      {
         $result=$this->adapter->query("insert into n_nomina_e (idNom, idEmp, dias, idVac, actVac, sueldo ) 
           (select ".$id.' as id2, a.id, c.valor,
              case when ( ( b.fechaI <= d.fechaI) or (d.fechaF <= b.fechaF ) ) then a.idVac else 0 end as idVac,a.vacAct, a.sueldo   
              from a_empleados a inner join n_nomina b on b.idGrupo=a.idGrup and b.id= '.$id."       
              inner join n_tip_calendario c on c.id=b.idCal
              left join n_vacaciones d on d.id=a.idVac
              WHERE a.activo=0 and a.estado=0 and ( a.fecIng <= b.fechaI or a.fecIng <= b.fechaF  )  and  # activo para la migracion de empelados activos 
              not exists (SELECT null from n_nomina_e where a.id=idEmp and idNom=".$id." ) and a.idGrup=".$idg." )",Adapter::QUERY_MODE_EXECUTE);
      }
      if ($idEmp>0)// Se generan por empleado
      {
         $result=$this->adapter->query("insert into n_nomina_e (idNom, idEmp, dias ) 
          ( select b.id , a.id , ( DATEDIFF( b.fechaF, b.fechaI ) ) as dias 
              from a_empleados a 
              inner join n_nomina b on b.id = ".$id."
              WHERE a.activo=0 and a.id = ".$idEmp."  and a.estado=0 and  a.idGrup=".$idg." )",Adapter::QUERY_MODE_EXECUTE);
      }      
    }
   // Generacion de incapacidades para empleados
   public function getIncapaEmp($id, $tabla)
   {
      $tipo=0;
      if ( $tabla=='n_incapacidades' )
         $tipo=0;
      else 
         $tipo=1;

         $result=$this->adapter->query("insert into n_nomina_e_i (idNom, idEmp, idInc, dias, diasAp, diasDp, reportada, tipo) 
( select a.id, b.idEmp, c.id as idInc, ( DATEDIFF( c.fechaf, c.fechai ) +1 ) as diasI, # Dias totales de incapacidad

   # CUANDO LA INCAPACIDAD ESTA POR DEBAJO DEL PERIODO INICIAL DE NOMINA ----------------------------------
( case when d.incAtrasada = 1 then 
case when ( ( c.fechai < a.fechaI) and ( c.fechaf < a.fechaI )  )
  then ( DATEDIFF( c.fechaf , c.fechai ) + 1 ) else 
     case when ( ( c.fechai < a.fechaI) and ( c.fechaf >= a.fechaI )  ) 
      then ( DATEDIFF( a.fechaI , c.fechai ) ) else 0 end 
    end 
 else 0 end )    
    as diasAp, # Dias no reportados antes de periodo
    
  # CUANDO LA INCAPACIDAD TIENE PERIODO SUPERIOR AL PERIODO INICIAL DE NOMINA ----------------------------------
case when ( ( c.fechai >= a.fechaI) and ( c.fechaf <= a.fechaF) ) # Incapacidad dentro del periodo
  then ( DATEDIFF( c.fechaf , c.fechai )+1 )
  else 
    case when ( ( c.fechai <= a.fechaI) and ( c.fechaf >= a.fechaI) ) # Incapacidad antes y despues del periodo de nomina
         then 
          case when ( ( DATEDIFF( c.fechaf, a.fechaI )+1 ) > 15 ) then 
            15  
         else 
            ( DATEDIFF( c.fechaf, a.fechaI )+1 ) # pasa de periodo a periodo y se de tomar la fecha inicio de nomina menos la fecha fin dincapacidad
       end    
      else 
        case when ( ( c.fechai >= a.fechaI) and ( c.fechaf >= a.fechaF) )  then # Inicia en el periodo y pasa al otro periodo de nomina
           ( DATEDIFF( a.fechaF, c.fechai )+1 )
        else 0 end  
      end 
    end as diasDp,
     c.reportada, # Dias no reportados despues del periodo        
     ".$tipo."  
from n_nomina a 
inner join n_nomina_e b on b.idNom = a.id
inner join ".$tabla." c on c.reportada in ('0','1')  and c.idEmp = b.idEmp #  Se cargan todas las incapacidades antes de fin del perioso en cuestio
left join c_general d on d.id = 1 # Buscar datos de la confguracion general para incapaciades 
where a.id = ".$id."    )   ",Adapter::QUERY_MODE_EXECUTE);
//and ( (c.fechai <= a.fechaF) and (c.fechaf >= a.fechaI ) ) ## OJOA ANALIZAR ESTO DE LA MEJR FORMA 
    }    
   // ( POR TIPO DE AUTOMATICOS ) ( n_nomina_e_d ) 
   public function getNominaEtau($id,$idg)
   {        
     $result=$this->adapter->query('select distinct c.idNom, c.id, a.idCon, 
                case a.cCosEmp when 0 then a.idCcos
                  when 1 then b.idCcos  End as idCcos, 
                case a.horasCal when 0 then 0 
                  when 1 then (c.dias*'.$this->horasDias.') End as horas, 1, 
                case when g.valor=2 then 
                     case when g.tipo = 1 then a.valor else 0 End				                      
                when g.valor=1 then 0      
                End as dev,       
                case when g.valor=2 then 
                     case when g.tipo = 2 then a.valor else 0 End				                                      
                when g.valor=1 then 0     
		End as ded, h.formula, c.dias, g.tipo
                , b.id as idEmp , g.idFor , a.diasLab, c.diasVac, a.vaca,
                case when ii.codigo is null then "" else ii.codigo end as nitTer 
                from n_tip_auto_i a inner join a_empleados b on a.idTauto=b.idTau
                  inner join n_nomina_e c on b.id=c.idEmp 
                  inner join n_nomina d on d.id=c.idNom
                  inner join n_tip_auto_tn e on e.idTnom=d.idTnom  
                  inner join n_tip_calendario f on f.id=d.idCal
                  inner join n_conceptos g on g.id=a.idCon 
                  inner join n_formulas h on h.id=g.idFor                                    
                  left join n_terceros_s i on i.id = g.idTer  
                  left join n_terceros ii on ii.id = i.idTer 
                  WHERE not exists (SELECT null from n_nomina_e_d 
                  where c.id=idInom and a.idCon=idConc and a.idCcos=idCcos and tipo=1 ) 
                  and d.estado=0 and b.idGrup='.$idg.' and c.idNom='.$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;     
    }    
   // ( POR TIPO DE AUTOMATICOS 2 opcionales ) ( n_nomina_e_d ) 
   public function getNominaEtau2($id,$idg)
   {        
     $result=$this->adapter->query('select distinct c.idNom, c.id, a.idCon, 
                case a.cCosEmp when 0 then a.idCcos
                  when 1 then b.idCcos  End as idCcos, 
                case a.horasCal when 0 then 0 
                  when 1 then (c.dias*'.$this->horasDias.') End as horas, 1, 
                case when g.valor=2 then 
                     case when g.tipo = 1 then a.valor else 0 End				                      
                when g.valor=1 then 0      
                End as dev,       
                case when g.valor=2 then 
                     case when g.tipo = 2 then a.valor else 0 End				                                      
                when g.valor=1 then 0     
		End as ded, h.formula, c.dias, g.tipo  , b.id as idEmp , g.idFor , a.diasLab, c.diasVac, a.vaca,  
                case when ii.codigo is null then "" else ii.codigo end as nitTer 
                from n_tip_auto_i a inner join a_empleados b on a.idTauto=b.idTau2
                  inner join n_nomina_e c on b.id=c.idEmp 
                  inner join n_nomina d on d.id=c.idNom
                  inner join n_tip_auto_tn e on e.idTnom=d.idTnom  
                  inner join n_tip_calendario f on f.id=d.idCal
                  inner join n_conceptos g on g.id=a.idCon
                  inner join n_formulas h on h.id=g.idFor
                  left join n_terceros_s i on i.id = g.idTer  
                  left join n_terceros ii on ii.id = i.idTer                   
                  WHERE not exists (SELECT null from n_nomina_e_d 
                  where c.id=idInom and a.idCon=idConc and a.idCcos=idCcos and tipo=1 ) 
                  and d.estado=0 and b.idGrup='.$idg.' and c.idNom='.$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;     
    }        
   // ( POR TIPO DE AUTOMATICOS 3 opcionales ) ( n_nomina_e_d ) 
   public function getNominaEtau3($id,$idg)
   {        
     $result=$this->adapter->query('select distinct c.idNom, c.id, a.idCon, 
                case a.cCosEmp when 0 then a.idCcos
                  when 1 then b.idCcos  End as idCcos, 
                case a.horasCal when 0 then 0 
                  when 1 then (c.dias*'.$this->horasDias.') End as horas, 1, 
                case when g.valor=2 then 
                     case when g.tipo = 1 then a.valor else 0 End				                      
                when g.valor=1 then 0      
                End as dev,       
                case when g.valor=2 then 
                     case when g.tipo = 2 then a.valor else 0 End				                                      
                when g.valor=1 then 0     
		End as ded, h.formula, c.dias, g.tipo  , b.id as idEmp , g.idFor , a.diasLab, c.diasVac, a.vaca,   
                case when ii.codigo is null then "" else ii.codigo end as nitTer 
                from n_tip_auto_i a inner join a_empleados b on a.idTauto=b.idTau3
                  inner join n_nomina_e c on b.id=c.idEmp 
                  inner join n_nomina d on d.id=c.idNom
                  inner join n_tip_auto_tn e on e.idTnom=d.idTnom  
                  inner join n_tip_calendario f on f.id=d.idCal
                  inner join n_conceptos g on g.id=a.idCon
                  inner join n_formulas h on h.id=g.idFor
                  left join n_terceros_s i on i.id = g.idTer  
                  left join n_terceros ii on ii.id = i.idTer                   
                  WHERE not exists (SELECT null from n_nomina_e_d 
                  where c.id=idInom and a.idCon=idConc and a.idCcos=idCcos and tipo=1 ) 
                  and d.estado=0 and b.idGrup='.$idg.' and c.idNom='.$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;     
    }            
   // ( POR TIPO DE AUTOMATICOS 4 opcionales ) ( n_nomina_e_d ) 
   public function getNominaEtau4($id,$idg)
   {        
     $result=$this->adapter->query('select distinct c.idNom, c.id, a.idCon, 
                case a.cCosEmp when 0 then a.idCcos
                  when 1 then b.idCcos  End as idCcos, 
                case a.horasCal when 0 then 0 
                  when 1 then (c.dias*'.$this->horasDias.') End as horas, 1, 
                case when g.valor=2 then 
                     case when g.tipo = 1 then a.valor else 0 End				                      
                when g.valor=1 then 0      
                End as dev,       
                case when g.valor=2 then 
                     case when g.tipo = 2 then a.valor else 0 End				                                      
                when g.valor=1 then 0     
		End as ded, h.formula, c.dias, g.tipo  , b.id as idEmp , g.idFor , a.diasLab, c.diasVac, a.vaca,
                case when ii.codigo is null then "" else ii.codigo end as nitTer 
                from n_tip_auto_i a inner join a_empleados b on a.idTauto=b.idTau4 
                  inner join n_nomina_e c on b.id=c.idEmp 
                  inner join n_nomina d on d.id=c.idNom
                  inner join n_tip_auto_tn e on e.idTnom=d.idTnom  
                  inner join n_tip_calendario f on f.id=d.idCal
                  inner join n_conceptos g on g.id=a.idCon
                  inner join n_formulas h on h.id=g.idFor
                  left join n_terceros_s i on i.id = g.idTer  
                  left join n_terceros ii on ii.id = i.idTer                                     
                  WHERE not exists (SELECT null from n_nomina_e_d 
                  where c.id=idInom and a.idCon=idConc and a.idCcos=idCcos and tipo=1 ) 
                  and d.estado=0 and b.idGrup='.$idg.' and c.idNom='.$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;     
    }                
    // ( POR OTROS AUTOMATICOS ) ( n_nomina_e_d ) por otros automaticos
    public function getNominaEeua($id)
    {        
      $result=$this->adapter->query('select distinct c.idNom, c.id, a.idCon, f.formula,c.dias,e.tipo,a.idEmp,   
                case a.cCosEmp when 0 then a.idCcos
                  when 1 then g.idCcos  End as idCcos, # Centros de costos
                case a.horasCal when 0 then 0 
                  when 1 then (c.dias*8) End as horas, 1, # Horas desrrollo
                case when e.valor=2 then 
                case when e.tipo = 1 then a.valor else 0 End                              
                  when e.valor=1 then 0      
                End as dev,  # Devengado
                 ( case when e.valor=2 then 
                     case when e.tipo = 2 then a.valor else 0 End                                             
                when e.valor=1 then 0
        End ) / cal.valor  as ded  , # Deducido 
         e.idFor, c.diasVac, hh.codigo as nitTer, c.diasVac       
from n_emp_conc a 
inner join n_nomina_e c on a.idEmp=c.idEmp 
inner join n_nomina d on d.id=c.idNom
inner join n_conceptos e on e.id=a.idCon
inner join n_formulas f on f.id=e.idFor
inner join a_empleados g on g.id=c.idEmp
left join n_terceros_s h on h.id = e.idTer  
left join n_terceros hh on hh.id = h.idTer 
inner join n_tip_calendario cal on cal.id = d.idCal 
WHERE not exists (SELECT null from n_nomina_e_d 
where c.id=idInom and a.idCon=idConc and a.idCcos=idCcos and tipo=2 )
and d.estado=0 and c.idNom='.$id.' and c.actVac = 0 ',Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;        
    }        
   // ( POR CONCEPTOS DE AUTOMATICOS ) ( n_nomina_e_d ) por conceptos automaticos 
   public function getNominaEcau($id)
   {        
     $result=$this->adapter->query('select a.id as idNom, d.id ,d.dias as dias, b.idConc as idCon, 
            e.idCcos , f.formula, c.tipo, e.id as idEmp, 0 as horas, c.idFor, e.idFpen, c.fondo  
            from n_nomina a 
            inner join n_conceptos_tn b on b.idTnom=a.idTnom
            inner join n_conceptos c on c.id=b.idConc
            inner join n_nomina_e  d on d.idNom = a.id
            inner join a_empleados e on e.id = d.idEmp 
            inner join n_formulas f on f.id=c.idFor
            where c.auto=1 and c.perAuto=0 and a.id='.$id.' order by d.idEmp',Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
    }        

   // CONCEPTOS HIJOS  
   public function getNominaConH($id)
   {        
     $result=$this->adapter->query('select a.id as idNom, e.id, e.dias, d.id as idCon, f.idCcos,
       g.formula, d.tipo, e.idEmp, 0 as horas, g.id as idFor,  
		 case when cc.id is null then 0 else cc.id end as Temp, e.diasVac  
		 from n_nomina_e_d a 
       inner join n_conceptos b on b.id=a.idConc 
       inner join n_conceptos_th c on c.idConc=b.id 
       inner join n_conceptos d on d.id=c.idCon 
       inner join n_nomina_e e on e.id=a.idInom
       inner join a_empleados f on f.id = e.idEmp
       left join  n_conceptos_te cc on cc.idConc=c.idCon and cc.idTemp = f.idTemp        
       inner join n_formulas g on g.id = d.idFor  
       where a.idNom='.$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   }               
   // FONDOS DE SOLIDARIDAD
   public function getSolidaridad($id)
   {
      $result=$this->adapter->query("Select a.id, a.idEmp , e.idCcos, year(b.fechaI) as ano , month(b.fechaI) as mes    
             from  n_nomina_e a 
             inner join a_empleados e on e.id = a.idEmp
             inner join n_nomina b on b.id = a.idNom 
             inner join n_tip_calendario c on c.id = b.idCal 
             inner join n_tip_calendario_p d on d.idCal = c.id 
             where a.idNom = ".$id." and e.idFpen!=1 # Recordar que 1 es no tiene pension  
			 and d.idCal = c.id and substr(b.fechaI,6,2) = d.mesI 
             and LPAD(trim(LEFT( (substr(b.fechaI,9,2)),10)),2,'0') = LPAD(trim(LEFT( (d.diaI),10)),2,'0')               
             and d.periodo = 2 # El fondo solo se ejecuta durante el 2 periodo o a final del mes  
             group by a.idEmp ",Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
    }    
    
   // Estado de periodos generados por nomina
   public function getNominaCuP($id)
   {        
     $result=$this->adapter->query('update n_conceptos a 
        inner join n_conceptos_tn b on b.idConc=a.id
        inner join n_nomina c on c.idTnom=b.idTnom
        set b.periodo = case when a.perAuto>b.periodo then b.periodo+1 else 1 end  
        where a.perAuto>1 and c.id='.$id ,Adapter::QUERY_MODE_EXECUTE);
   }                  
    

   // ( PRESTAMOS ) ( n_nomina_e_d ) 
   public function getPrestamos($id)
   {        
     $result=$this->adapter->query("select distinct a.idEmp, c.vacAct, a.dias , 
                      case when ff.codigo is null then '' else ff.codigo end as nitTer   
                      from n_nomina_e a 
                      inner join n_prestamos b on b.idEmp=a.idEmp
                      inner join a_empleados c on c.id=a.idEmp 
                      inner join n_tip_prestamo d on d.id = b.idTpres 
                      inner join n_conceptos e on e.id = d.idConE 
                      left join n_terceros_s f on f.id = e.idTer  
     		          left join n_terceros ff on ff.id = f.idTer # Funciones para retorno y salida de vacaciones 
                      where c.vacAct in ('0','2') and b.estado=1 and a.idNom=".$id." group by a.idEmp order by a.idEmp ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   }            
   // ( PRESTAMOS CUOTAS ) ( n_nomina_e_d ) Salida
   public function getCprestamosS($id,$idEmp)
   {        
     $result=$this->adapter->query('select distinct a.id, a.idEmp,0 as dias,0 as horas, 0 as formula, f.tipo, h.idCcos,
             e.idConE as idCon, f.idFor, cc.id as idPres, 1 as cuota, 
				 case when j.id >0 then ( (cc.valCuota / i.valor ) * ( a.dias + j.diasCal ) )  else 
				 cc.valCuota end as valor, 
				 cc.cuotas, cc.valCuota, 
         case when kk.codigo is null then "" else kk.codigo end as nitTer,
         case when np.valor is null then 0 else np.valor end as valorPresN    
				 from n_nomina_e a 
             inner join n_nomina b on b.id=a.idNom
             inner join n_prestamos c on c.idEmp=a.idEmp 
             inner join n_prestamos_tn cc on cc.idPres = c.id and cc.idTnom = c.idTnom  
             inner join n_tip_prestamo e on e.id=c.idTpres
             inner join n_conceptos f on f.id=e.idConE  
             inner join n_formulas g on g.id=f.idFor 
             inner join a_empleados h on h.id=a.idEmp 
             inner join n_tip_calendario i on i.id = b.idCal 
	           left join n_vacaciones j on j.id=a.idVac
             left join n_terceros_s k on k.id = f.idTer
     		     left join n_terceros kk on kk.id = k.idTer                                   
             left join n_nomina_pres np on np.idPres = cc.id and np.fechaI = b.fechaI and np.fechaF = b.fechaF and np.estado=0 # Buscar cambios en nomina activa con el prestamo
             where a.idNom='.$id.' and c.estado=1 and a.idEmp='.$idEmp." and ( cc.pagado + cc.saldoIni ) < cc.valor group by c.id" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   }               
   // ( PRESTAMOS CUOTAS ) ( n_nomina_e_d ) Regreso
   public function getCprestamosR($id,$idEmp)
   {        
     $result=$this->adapter->query('select distinct a.id, a.idEmp,0 as dias,0 as horas, 0 as formula, f.tipo, h.idCcos,
             e.idConE as idCon, f.idFor, cc.id as idPres, 1 as cuota, 
				 ( (cc.valCuota / i.valor ) * ( a.dias ) )  as valor,
                                 case when kk.codigo is null then "" else kk.codigo end as nitTer ,
                                 case when np.valor is null then 0 else np.valor end as valorPresN  
				 from n_nomina_e a 
             inner join n_nomina b on b.id=a.idNom
             inner join n_prestamos c on c.idEmp=a.idEmp 
             inner join n_prestamos_tn cc on cc.idPres = c.id and cc.idTnom = c.idTnom 
             inner join n_tip_prestamo e on e.id=c.idTpres
             inner join n_conceptos f on f.id=e.idConE  
             inner join n_formulas g on g.id=f.idFor 
             inner join a_empleados h on h.id=a.idEmp 
             inner join n_tip_calendario i on i.id = b.idCal 
             left join n_terceros_s k on k.id = f.idTer
     		     left join n_terceros kk on kk.id = k.idTer    
             left join n_nomina_pres np on np.idPres = cc.id and np.fechaI = b.fechaI and np.fechaF = b.fechaF and np.estado=0 # Buscar cambios en nomina activa con el prestamo                               
             where a.idNom='.$id.' and c.estado=1 and a.idEmp='.$idEmp." and ( cc.pagado + cc.saldoIni ) < cc.valor group by c.id" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   }     
   
   // Consulta para recorrer nomina generada 
   public function getNgenerada($id,$ti)
   {
      $result=$this->adapter->query("select b.id,a.idEmp,b.horas,e.formula,d.tipo,a.dias,b.horas,b.idInom
                                   from n_nomina_e a inner join n_nomina_e_d b on a.id=b.idInom
                                   inner join a_empleados c on c.id=a.idEmp
                                   inner join n_conceptos d on d.id=b.idConc
                                   inner join n_formulas e on e.id=d.idFor
           where a.idNom=".$id." and b.tipo=".$ti."  and ( b.devengado=0 and b.deducido=0)  
    order by a.idEmp",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }        

   // Generar periodos de nominas por tipos de nomina, grupos de empleados y calendario 
   public function getGenerarP($idTnom,$idGrupE,$idCal)
   {
       $this->adapter->query("insert into n_tip_calendario_d (idTnom,idGrupo,idCal,fechaI,fechaF,estado)(
                              select ".$idTnom." as idTnom, ".$idGrupE." as idGrup, ".$idCal." as idTcal,
                                     concat( '2014','-',a.mesI,'-',a.diaI),
                                     concat('2014','-',a.mesF,'-',a.diaF), 0 as estado 								 
				     from n_tip_calendario_p a 
                                     where a.idCal=".$idCal." and not exists (SELECT null from n_tip_calendario_d c 
                                        where c.idTnom=".$idTnom." and c.idGrupo=".$idGrupE." and c.idCal=".$idCal." and year(c.fechaI)='2014' )
                                        order by a.orden)",Adapter::QUERY_MODE_EXECUTE);                                                      
   }            
   // ( REGISTRO DE NOVEDADES ) ( n_novedades ) 
   public function getRnovedades($id,$fechaI,$fechaF)
   {        
     $result=$this->adapter->query("select a.id, a.idEmp, 0 as dias, c.horas, c.devengado as dev, c.deducido as ded, i.formula, 
g.tipo, h.idCcos, c.idConc as idCon, i.id as idFor, c.calc        
from n_nomina_e a inner join n_nomina b on b.id=a.idNom
inner join n_novedades c on c.idEmp=a.idEmp
left join n_tip_matriz_tnv d on d.id=c.idTmatz
left join n_tip_matriz e on e.id=d.idTmatz and e.idTnom=b.idTnom 
inner join n_conceptos g on g.id=c.idConc 
inner join a_empleados h on h.id=a.idEmp 
inner join n_formulas i on i.id=g.idFor 
inner join n_tip_calendario_d j on j.id=c.idCal 
where c.estado=0 and j.fechaI>='".$fechaI."' "
             . "and j.fechaF<='".$fechaF."' and  b.id =".$id."  order by g.ordenN desc # Colocar de primero los de gran prioridad por logeneral sin formulas ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   }                   
   // Documento de novedades por empleado de acuerdo al tipo
   public function getDocNove($idn,$con)
   {        
     // $id    : Id documento de novedades
     // $tipo  : tipo ('1','2','3')

     $result=$this->adapter->query("select a.idNom,a.dias,b.id,b.horas,d.nombre,e.formula,d.tipo,d.valor,b.idCcos,b.devengado,b.deducido
                                        ,d.id as idCon,c.id as idEmp,d.idFor,b.horDias, a.diasVac, b.saldoPact, b.idCpres   
                                        from n_nomina_e a 
                                        inner join n_nomina_e_d b on a.id=b.idInom
                                        inner join a_empleados c on c.id=a.idEmp
                                        inner join n_conceptos d on d.id=b.idConc
                                        inner join n_formulas e on e.id=d.idFor
                                        where b.idInom=".$idn." ".$con." order by d.tipo " ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;     
    }        
   // Numero de novedades por empleado de acuerdo al tipo
   public function getDocNoveN($idn,$con)
   {        
     // $id    : Id documento de novedades
     // $tipo  : tipo ('1','2','3')

     $result=$this->adapter->query("select count(b.id) as num   
                                        from n_nomina_e a 
                                        inner join n_nomina_e_d b on a.id=b.idInom
                                        inner join a_empleados c on c.id=a.idEmp
                                        inner join n_conceptos d on d.id=b.idConc
                                        inner join n_formulas e on e.id=d.idFor
                                        where b.idInom=".$idn." ".$con." order by d.tipo " ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;     
    }            
   // ( REGISTRO DE VACACIONES ) ( n_vacaciones ) 
   public function getVacacionesG($id)
   {        
     $result=$this->adapter->query("select a.idNom, a.id, b.idCon, ' ' as formula, b.dias, d.tipo,  a.idEmp,  
                 f.idCcos , 0 as horas , # Para obtener el numero de periodos pagados en esta nomina 
				 b.valor as dev,0 as ded, e.id as idFor, b.diasCal, day( b.fechaI ) as diaI       
					  from n_nomina_e a 
                 inner join n_vacaciones b on b.id=a.idVac and b.estado in ('1')  
                 inner join n_nomina c on c.id=a.idNom     
                 inner join n_conceptos d on d.id=b.idCon
                 inner join n_formulas e on e.id=d.idFor
                 inner join a_empleados f on f.id=a.idEmp
                 where b.fechaI >= c.fechaI and b.fechaI <= b.fechaF and a.idNom = ".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   }                   
   // Vacaciones de empleados
   public function getVacaciones($id)
   {
      $result=$this->adapter->query("select b.fechaI as fecVacI, b.fechaF as fecVacF,
                 c.fechaI as fecPerI, c.fechaF as fecPerF, 
                 case when b.fechaI >= c.fechaI and b.fechaI <= c.fechaF then # vacaciones inician con el perioro de nomina o inician dentro del periodo					  
					  DATEDIFF(b.fechaI , c.fechaI ) + 1					  
					  else 0 end as periI,
                 case when b.fechaF >= c.fechaI and b.fechaF <= c.fechaF then # vacaciones finalizan dentro del periodo actual de nomina 					  
					  DATEDIFF(c.fechaF, b.fechaF )  					  					  
					  else 0 end as periF , a.idEmp,
					  case when ( b.fechaI=c.fechaI and b.fechaF>c.fechaI ) then 1 else # Vacaciones inician en el periodo y terminan en otro periodo
					  0 end as vacInPer, 
					  b.diasCal, # Dias de vacaciones 					  
					  case when ( b.fechaI>=c.fechaF ) then b.diasCal else 
					  0 end as diasCalOt # Dias calculados del periodo superior                                              
					  , b.estado, d.idCcos  
					  from n_nomina_e a 
                 inner join n_vacaciones b on b.id=a.idVac and b.estado in ('1','2')  
                 inner join n_nomina c on c.id=a.idNom    
                 inner join a_empleados d on d.id = a.idEmp 
                 where a.id=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
    }         
   // Incapacidades de empleados
   public function getIncapacidades($id)
   {
        $result=$this->adapter->query("select a.id, c.fechai, c.fechaf, b.idEmp, b.idInc,
          d.fechaI, d.fechaF, a.dias, ( case when b.reportada = 0 then sum( b.diasAp )else 0 end ) as diasAp, sum( b.diasDp ) as diasDp,
          e.dias as diasEnt , e.nombre, b.reportada     
           from n_nomina_e a
            inner join n_nomina_e_i b on b.idEmp = a.idEmp and b.idNom = a.idNom 
            left join n_incapacidades c on c.id = b.idInc
            inner join n_nomina d on d.id = b.idNom 
            left join n_tipinc e on e.id = c.idInc 
            where a.idNom = ".$id." 
            group by a.idEmp 
            order by a.idEmp ",Adapter::QUERY_MODE_EXECUTE);
      //$datos = $result->current();
      $datos=$result->toArray();
      return $datos;
    }             
   // Ausentismos de empleados
   public function getAusentismos($id)
   {
        $result=$this->adapter->query("select b.id , a.id as idAus, 
        DATEDIFF(c.fechaF , c.fechaI ) + 1  as diasH, # Total dias nomina 
case when ( ( a.fechai <= c.fechaI ) and ( a.fechaf >= c.fechaF ) ) then  # Si la fecha final del ausentismo es mayor al periodo de nomina 
DATEDIFF(c.fechaF , c.fechaI ) + 1 else  
  case when ( ( a.fechai <= c.fechaI ) and ( a.fechaf <= c.fechaF ) ) then # Si el regreso ya se dio se sca la difrencia
      ( DATEDIFF(c.fechaF , c.fechaI ) + 1 ) - ( DATEDIFF( c.fechaF, a.fechaf ) ) 
      else   
         case when ( ( a.fechai > c.fechaI ) and ( a.fechaf < c.fechaF ) ) then  # Si el ausentismo esta dentro del periodo
            DATEDIFF( a.fechaf, a.fechai )    
      end  
   end 
end as diasAus ,a.fechai , a.fechaf, c.fechaI, c.fechaF, a.idEmp   
                  from n_ausentismos a 
                     inner join n_nomina_e b on b.idEmp = a.idEmp
                     inner join n_nomina c on c.id = b.idNom 
                     inner join n_tip_aus d on d.id = a.idTaus
                     where c.id = ".$id." and a.estado in ('1') and d.tipo = 2  # Solo para no remunados para quitar los dias  ",Adapter::QUERY_MODE_EXECUTE);
      //$datos = $result->current();
      $datos=$result->toArray();
      return $datos;
    }                 
   // EMBARGOS NOMINA ( n_nomina_e_d ) 
   public function getIembargos($id)
   {
      $result=$this->adapter->query("select a.id, a.idEmp, a.idEmp, 
                c.idCon as idCon,0 as dias, b.id as idEmb, b.valor - ( b.pagado + b.saldoIni ) as pagado ,
                b.formula , e.tipo, g.idCcos , 0 as idFor ,0 as horas, hh.codigo as nitTer   # Se colcoa 9 para que realice la formula
                from n_nomina_e a 
                inner join n_embargos b on b.idEmp=a.idEmp  
                inner join n_tip_emb c on c.id = b.idTemb 
                inner join n_conceptos e on e.id=c.idCon 
                inner join a_empleados g on g.id=a.idEmp 
                inner join n_terceros_s h on h.id = b.idTer
      	    inner join n_terceros hh on hh.id = h.idTer	 
                where b.estado=1 and a.idNom=".$id." group by b.id" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }             
   // INCAPACIDADES NOMINA ( n_nomina_e_d ) 
   public function getIncapNom($id)
   {
      $result=$this->adapter->query("select h.id, a.idEmp, a.idEmp, d.idConc as idCon,0 as dias,
                f.formula , e.tipo, g.idCcos , e.idFor, a.diasAp, a.diasDp,
					 case when ( ( a.diasAp + a.diasDp ) >= (c.dias - 1) )# Dias inicio pago empreasa
					   then ( c.dias - 1) else ( a.diasAp + a.diasDp ) end as diasEmp,              
					   # Se buscan dias anteriores reportados o no reportados 
					case when a.reportada = 1 then    
 					  case when ( ( a.diasDp ) > (c.dias - 1) )# Dias inicio pago entidad
					     then ( ( a.diasDp ) - ( c.dias - 1 ) ) else 0 end 
					else 
 					  case when ( ( a.diasAp + a.diasDp ) > (c.dias - 1) )# Dias inicio pago entidad
					     then ( ( a.diasAp + a.diasDp ) - ( c.dias - 1 ) ) else 0 end 
					end  as diasEnt,					   
						   d.tipo as tipInc, b.id as idInc        
                from n_nomina_e_i a 
                inner join n_incapacidades b on b.id=a.idInc 
                inner join n_tipinc c on c.id=b.idInc
                inner join n_tipinc_c d on d.idTinc=c.id 
                inner join n_conceptos e on e.id=d.idConc
                inner join n_formulas f on f.id=e.idFor
                inner join a_empleados g on g.id=a.idEmp
                inner join n_nomina_e h on h.idEmp = a.idEmp and h.idNom = a.idNom   
                where a.idNom = ".$id." and a.tipo = 0 
                order by a.idEmp",Adapter::QUERY_MODE_EXECUTE);       
   
      $datos=$result->toArray();
      return $datos;
    }                 
   // INCAPACIDADES POR PROROGAS NOMINA ( n_nomina_e_d ) 
   public function getIncaPpNom($id)
   {
      $result=$this->adapter->query("select h.id, a.idEmp, a.idEmp, d.idConc as idCon,0 as dias,
                f.formula , e.tipo, g.idCcos , e.idFor, a.diasAp, a.diasDp,
           case when ( ( a.diasAp + a.diasDp ) >= (c.dias - 1) )# Dias inicio pago empreasa
             then ( c.dias - 1) else ( a.diasAp + a.diasDp ) end as diasEmp,              
             # Se buscan dias anteriores reportados o no reportados 
          case when a.reportada = 1 then    
            case when ( ( a.diasDp ) > (c.dias - 1) )# Dias inicio pago entidad
               then ( ( a.diasDp ) - ( c.dias - 1 ) ) else 0 end 
          else 
            case when ( ( a.diasAp + a.diasDp ) > (c.dias - 1) )# Dias inicio pago entidad
               then ( ( a.diasAp + a.diasDp ) - ( c.dias - 1 ) ) else 0 end 
          end  as diasEnt,             
               d.tipo as tipInc, b.id as idInc        
                from n_nomina_e_i a 
                inner join n_incapacidades_pro b on b.id=a.idInc 
                inner join n_incapacidades bp on bp.id = b.idInc 
                inner join n_tipinc c on c.id=bp.idInc
                inner join n_tipinc_c d on d.idTinc=c.id 
                inner join n_conceptos e on e.id=d.idConc
                inner join n_formulas f on f.id=e.idFor
                inner join a_empleados g on g.id=a.idEmp
                inner join n_nomina_e h on h.idEmp = a.idEmp and h.idNom = a.idNom   
                where a.idNom = ".$id."  and a.tipo = 1 
                order by a.idEmp",Adapter::QUERY_MODE_EXECUTE);       
   
      $datos=$result->toArray();
      return $datos;
    }                     
   // ( POR AUSENTISMOS ) 
   public function getNominaAus($id)
   {        
     $result=$this->adapter->query("select a.id, a.idEmp, a.idEmp, d.idConc as idCon,0 as dias,
                ' ' as formula , e.tipo, g.idCcos , e.idFor, g.idCcos, 
                case when (b.fechaf >=  h.fechaF) then 
                    DATEDIFF(h.fechaF , b.fechai ) * 8 
                 else    
                    DATEDIFF(b.fechaf , b.fechai ) * 8 end as horas, # temporal para cuando cambie el periodo , tener en cuenta saldo horas
                b.fechai , b.fechaf, e.nombre as nomCon  					   
                from n_nomina_e a 
                inner join a_empleados g on g.id = a.idEmp 
                inner join n_ausentismos b on b.id = a.idAus                                
                inner join n_tip_aus c on c.id = b.idTaus
                inner join n_tip_aus_c d on d.idTaus = c.id 
                inner join n_conceptos e on e.id = d.idConc
                inner join n_formulas f on f.id = e.idFor  
                inner join n_nomina h on h.id = a.idNom 
                where b.estado=1 and a.idNom=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   }

   // ( DIAS TRABAJADOS ULTIMO PERIODO CESANTIAS ) 
   public function getDiasCesa($id, $idNom)
   {        
     $result=$this->adapter->query("select a.idNom, a.id, a.idEmp, b.idFces,   
              case when c.fechaF is null then b.fecIng else c.fechaF end as fechaC,
                 case when c.fechaF is null 
                      then (DATEDIFF( d.fechaF , b.fecIng ) ) else (DATEDIFF( d.fechaF , c.fechaF ) ) end as diasCes,
			   e.tipo, e. id as idCon, b.idCcos    
                 from n_nomina_e a 
                 inner join a_empleados b on b.id = a.idEmp 
                 left join  n_cesantias c on c.idEmp = b.id  
                 inner join n_nomina d on d.id = a.idNom 
                 inner join n_conceptos e on e.id=195 # Se debe colocar el id del concepto usado para las cesantias
                 where b.idGrup = ".$id." and a.idNom = ".$idNom,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   } 
   // ( CONSULTA EMPLEADOS PRIMAS SEMESTRALES ) 
   public function getDiasPrima($idEmp, $fechaI, $fechaF)
   {        
     $result=$this->adapter->query("select ( sum(i.devengado) - sum(i.deducido)  ) as valor ,  sum( ( DATEDIFF( b.fechaF , b.fechaI ) + 1 ) ) as diasPromPrima ,
( ( sum(i.devengado) - sum(i.deducido)  ) / sum( ( DATEDIFF( b.fechaF , b.fechaI ) + 1 ) ) ) as basePromedio 
                from n_nomina_e a 
                inner join n_nomina b on b.id=a.idNom
                inner join a_empleados h on h.id=a.idEmp 
                inner join n_nomina_e_d i on i.idInom = a.id 
                inner join n_conceptos j on j.id = i.idConc 
                inner join n_conceptos_pr k on k.idConc = j.id and k.idProc = 4 # Procesos de primas 
                where b.fechaI>='".$fechaI."' and b.fechaF<='".$fechaF."' and a.idEmp = ".$idEmp
                 ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;       
   }        
   // ( CONSULTA EMPELADOS PARA PRIMA DE ANTIGUEDADES CONDICIONADAS ) 
   public function getDiasPanti($id, $ano, $mes)
   {                  
      if ($mes > 0 )
      {
       $mes = $mes + ( $ano * 12 ); // Pasar los años a meses    
       $result=$this->adapter->query("select b.id, b.idEmp ,0 as dias,c.idCcos, 
             c.fecing , DATE_ADD( c.fecing , interval ".$mes." month) , c.CedEmp , c.nombre , c.idTemp, case when e.id > 0 then 1 else 0 end as pg,  
             case when (day( d.fecha) >= day(a.fechaI)) and (day( d.fecha) <= day(a.fechaF)) then 1 else 0 end as diaI   
             from n_nomina a
             inner join n_nomina_e b on b.idNom = a.id
             inner join a_empleados c on c.id = b.idEmp 
             inner join n_tipemp_p d on d.idEmp = b.idEmp and d.idTemp = 1 # mientras 1 es para los convencionados 
             left join n_pg_primas_ant e on e.idEmp = b.idEmp and year(e.fechaI) = year(a.fechaI) and month( e.fechaI ) and month( a.fechaI )
             where c.activo=0 and c.estado = 0 
             and (  ( year( DATE_ADD( d.fecha , interval ".$mes." month) ) = year( a.fechaI ) )
             and ( month( DATE_ADD( d.fecha , interval ".$mes." month) ) = month( a.fechaI ) ) ) 
             and a.id = ".$id." 
             order by c.fecing desc",Adapter::QUERY_MODE_EXECUTE);		             
      }else{
       $result=$this->adapter->query("select b.id, b.idEmp ,0 as dias,c.idCcos, 
             c.fecing , day( d.fecha ) as diaI, DATE_ADD( c.fecing , interval ".$ano." year) , c.CedEmp , c.nombre , c.idTemp, case when e.id > 0 then 1 else 0 end as pg,
             case when (day( d.fecha) >= day(a.fechaI)) and (day( d.fecha) <= day(a.fechaF)) then 1 else 0 end as diaI                  
             from n_nomina a
             inner join n_nomina_e b on b.idNom = a.id
             inner join a_empleados c on c.id = b.idEmp 
             inner join n_tipemp_p d on d.idEmp = b.idEmp and d.idTemp = 1 # mientras 1 es para los convencionados
             left join n_pg_primas_ant e on e.idEmp = b.idEmp and year(e.fechaI) = year(a.fechaI) and month( e.fechaI ) and month( a.fechaI ) 
             where c.activo=0 and c.estado = 0 
             and (  ( year( DATE_ADD( d.fecha , interval ".$ano." year) ) = year( a.fechaI ) )
             and ( month( DATE_ADD( d.fecha , interval ".$ano." year) ) = month( a.fechaI ) ) ) 
             and a.id = ".$id." 
             order by c.fecing desc ",Adapter::QUERY_MODE_EXECUTE);		   
      }		   
      $datos=$result->toArray();
      return $datos;       
   } 
   // ( CONSULTA EMPELADOS PARA PRIMA DE ANTIGUEDADES ANUALES ) 
   public function getDiasPantiA($id, $ano)
   {                        
       $result=$this->adapter->query("select b.id, b.idEmp ,0 as dias,c.idCcos, 
             c.fecing, ( DATE_FORMAT( a.fechaF  , '%Y-%m-%d' ) - DATE_FORMAT( d.fecha , '%Y-%m-%d' ) ) 
				 , c.CedEmp , c.nombre , c.idTemp , case when e.id > 0 then 1 else 0 end as pg, 
             case when (day( d.fecha) >= day(a.fechaI)) and (day( d.fecha) <= day(a.fechaF)) then 1 else 0 end as diaI   
             from n_nomina a
             inner join n_nomina_e b on b.idNom = a.id
             inner join a_empleados c on c.id = b.idEmp 
             inner join n_tipemp_p d on d.idEmp = b.idEmp and d.idTemp = 1 # mientras 1 es para los convencionados 
             left join n_pg_primas_ant e on e.idEmp = b.idEmp and year(e.fechaI) = year(a.fechaI) and month( e.fechaI ) and month( a.fechaI ) 
             where c.activo=0 and c.estado = 0 
             and a.id = ".$id." and ( DATE_FORMAT( a.fechaF  , '%Y-%m-%d' )
                 - DATE_FORMAT( d.fecha , '%Y-%m-%d' ) )> ".$ano." 
             and month( d.fecha ) = month(a.fechaF)
             order by c.fecing desc  ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;       
   } 
   // Retencion en la fuente a empleados
   public function getRetFuente($id)
   {
        $result=$this->adapter->query("select b.id , b.idEmp, d.idCcos, year(a.fechaI) as ano , month(a.fechaI) as mes      
             from n_nomina a
               inner join n_nomina_e b on b.idNom = a.id
               inner join a_empleados_rete c on c.idEmp = b.idEmp
               inner join a_empleados d on d.id = c.idEmp
               where a.id = ".$id ,Adapter::QUERY_MODE_EXECUTE);
      //$datos = $result->current();
      $datos=$result->toArray();
      return $datos;
    }                    
}
?>