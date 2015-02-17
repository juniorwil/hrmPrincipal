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
use Principal\Model\Pgenerales; // Parametros generales
use Principal\Model\Paranomina; // Parametros de nomina
use Principal\Model\AlbumTable; // Libro de consultas
use Principal\Model\Gnomina; // Generacion de nomina

/// INDICE

//// FUNCIONES BASICAS ------------------------------------------
// 0. FUNCION GENERAL PARA CALCULOS EN NOMINA
// 01. VALOR DE FORMULAS
// 1. Sueldo empleado
// 2. Valor hora empleado
// 3. Valor dia empleado
// 4. Sumatoria procesos Ley 100
// 5. Solidaridad
// 6. Sumatoria procesos subsidios
// 7. Devolver dia de inicio calendario1
// 8. Dias laborado por empleado
// 9. Dias habiles o no habiles 
// 10.Subsidio de transporte
// 11. Sumatoria procesos cesantias
// 12. Recalculo de documento de nomina
// 13. Sumaoria Devengados deducidos y totales a pagar 
     
//// FUNCIONES GENERALES ----------------------------------------
class NominaFunc extends AbstractTableGateway
{
   protected $table  = '';   
      
   public $dbAdapter;
   public $salarioMinimo;
   public $horasDias;
   public $salarioMinimoCovencional;
   public $subsidioTransporte;

   public function __construct(Adapter $adapter)
   {
        $this->adapter = $adapter;
        $this->initialize();
        // Parametros de nomina para funciones de consulta 
        $pn = new Paranomina($this->adapter);
        $dp = $pn->getGeneral1(1);
        $this->salarioMinimo=$dp['valorNum'];// Salario minimo

        $dp = $pn->getGeneral1(2);
        $this->horasDias=$dp['valorNum'];// Horas dia de trabajo

        $dp = $pn->getGeneral1(3);
        $this->salarioMinimoCovencional=$dp['formula'];// Salario minimo convencional   

        $dp = $pn->getGeneral1(11);
        $this->subsidioTransporte=$dp['valorNum']; // Subsidio de trasnporte    
   }
   // 0. FUNCION GENERAL PARA CALCULOS EN NOMINA
   public function getNomina($idn, $iddn, $idin, $ide ,$diasLab, $diasVac ,$horas ,$formula ,$tipo ,$idCcos ,
                             $idCon, $ac, $tipn,$dev,$ded,$idfor,$diasLabC,$idCpres,$calc,$conVac,$obId)
   {       
       // $ac      Insertar o modificar novedad ( 0: nuevo  1:modificar )
       // $idn     Id de la nomina
       // $iddn    Id dcumento de novedad (n_nomina_e)
       // $idin    Id novedad             (n_nomina_e_d)
       // $ide     Id del empleado
       // $diasLab Dias laborados 
       // $diasVac Dias vacaciones
       // $horas   Horas laborados o valores
       // $formula Formula
       // $tipo    Devengado o Deducido  
       // $idCcos  id del Centro de costo   
       // $idCon   id del Concepto
       // $ac      accion 
       // $tipn    Tipo dde novedad ( 1: tipos auto, 2:otros automa, 3:calculados )
       // $dev     Devengados de consulta
       // $ded     Deducidos de consulta
       // idfor   Ide de la formula, importante para determinar si es calculada o MANUAL
       // $diasLabC Dias laborados, solo para calculados
       // $idCpres Id de la cuota de un prestamo 
       // $calc   Define si se calcula o se deja el concepto con los valoes que viene
       // $conVac Si es 1 el concepto se recalcula con dias calendario en vacaciones para pagar o descontar
       // $obId   Obtener id
     $t = new AlbumTable($this->adapter);  
     $datAus = $t->getGeneral1("select count(id) as sw from n_nomina_e where dias=0 and aus=1 and id = ".$iddn); // Verificar si esta asutente totalmente 
     if ($datAus['sw']==0) // Valdiacion importante para empleados ausentes
	 {
       $saldoPact=0; // Variable para saldos de prestamos
                    
       if ($calc==0) // Debe ser calculado el concepto , si es 1 ya fue calculado
       {    
            // Funcion de formula 
            //if ( ($dev==0) and ($ded==0) )
            //{
                $datFor = $this->getFormula($formula,$idfor,$tipo,$horas,$ide,$iddn,$diasLab, $diasVac,$idCon);// Funcion de formulas
                if ($datFor['dev']>0)
                    $dev = $datFor['dev'];
                if ($datFor['ded']>0)
                   $ded = $datFor['ded'];
           // }
            //  if ( ( $horas > 0 )  ) // Si hay horas se calcula 
            //  {
            //    $datFor = $this->getFormula($formula,$idfor,$tipo,$horas,$ide,$iddn,$diasLab, $diasVac,$idCon);// Funcion de formulas
            //    if ($datFor['dev']>0)
            //       $dev = $datFor['dev'];
            //    if ($datFor['ded']>0)
            //       $ded = $datFor['ded'];
            //  }            
            //} 
            // CONCEPTOS EN AUTOMATICOS QUE SE CALCULEN NUEVAMENTE CONDIAS CALENDARIO PARA PAGARLOS DURANTE LAS VACACIONES
            if ( ($conVac>0) and ($diasVac>0) )
            {
                $datFor = $this->getFormula($formula,$idfor,$tipo,$horas,$ide,$iddn,15, 0,$idCon);// Funcion de formulas
                if ($datFor['dev']>0)
                    $dev = $dev + $datFor['dev'];
                if ($datFor['ded']>0)
                   $ded = $ded + $datFor['ded'];
            }            
            
       }else{
           if ($idCpres>0) // Se Valida el saldo del prestamo realizado
           {
             // BUscar saldo actual del prestamo
             $datNom = $t->getGeneral1("select idTnom from n_nomina where id = ".$idn);
             $datPres = $t->getGeneral1("select valor-(pagado+saldoIni) as saldoAct "
                                     ." from n_prestamos_tn where id=".$idCpres." and idTnom=".$datNom['idTnom']);
			 
			 $saldoPact = $datPres['saldoAct'];						 
			 if ( $ded > $saldoPact )					 
                $ded = $saldoPact; // Se pone el saldo actual para cobro
           }
       }
       // ---       
       
       if ( ($dev>0) or ($ded>0))
       {
          $dev = round($dev,0); 
          $ded = round($ded,0); 
          $detalle = '';
          // Comportamiento del concepto con respecto a los fondos de salud y pension
          $datCon = $t->getGeneral1("select fondo from n_conceptos where id = ".$idCon);
          if ( $datCon['fondo']>0 ) // Aplica datos de los fondos
          {
             $fondo = $datCon['fondo'];    
             // Consulta fondos del empleado
             $datEmp = $t->getGeneral1("select d.nombre as nomSal, e.nombre as nomPen,
				f.nombre as nomCes, g.nombre as nomArp, g.nombre as nomFav, h.nombre as nomFafc 
                                from a_empleados a 
                                inner join n_cencostos c on a.idCcos=c.id 
                                inner join t_fondos d on d.id=a.idFsal
                                inner join t_fondos e on e.id=a.idFpen
                                left  join t_fondos f on f.id=a.idFces
                                left join t_fondos g on g.id=a.idFarp 
                                left join t_fondos h on h.id=a.idFav 
                                left join t_fondos i on i.id=a.idFafc where a.id=".$ide);              
             switch ($fondo) {
                 case 1:// Salud 
                     $detalle = $datEmp['nomSal'];
                     break;
                 case 2:// Pension
                     $detalle = $datEmp['nomPen'];
                     break;
                 default:
                     break;
             }              
              
          }// Fin validacion datos de los fondos
             
          // Guardar cambios
          If ($ac==0) // Inertar registro
          {
             //$result=$this->adapter->query("call getGenomina(".$idn.",".$iddn.",".$idCon.",".$idCcos.",".$horas.",".$tipn.",".$dev.",".$ded.",'".$var."')",Adapter::QUERY_MODE_EXECUTE);                           
             $result=$this->adapter->query("insert into n_nomina_e_d (idNom,idInom,idConc,idCcos,horas,tipo,devengado, deducido, horDias, idCpres, saldoPact, detalle)
values (".$idn.",".$iddn.",".$idCon.",".$idCcos.",".$horas.",".$tipn.",".$dev.",".$ded.",".$diasLabC.",".$idCpres.",".$saldoPact.",'".$detalle."' );" ,Adapter::QUERY_MODE_EXECUTE);                            
             if ($obId == 1)
             {
                $id = $this->adapter->getDriver()->getLastGeneratedValue(); 
                return $id;          
             }else 
                return 0;               
          }else{ // Modificar registro
             $result=$this->adapter->query("Update n_nomina_e_d set idCcos=".$idCcos.",horas=".$horas.",devengado=".$dev.", 
                                         deducido=".$ded.", saldoPact=".$saldoPact." where id =".$idin  ,Adapter::QUERY_MODE_EXECUTE);                  
             return 0;               
          }
       }// Validar que valor no sea cero          
     }// Validar ausentismo total del empleado  
   }                
   // 01. VALOR DE FORMULAS
   public function getFormula($formula,$idfor,$tipo,$horas,$ide,$iddn,$diasLab, $diasVac,$idCon)
   {       
       ini_set('max_execution_time', 300); // 5 minutos pro procesamiento ( si Safe mode en php.ini esta desabilitado funciona )            
       $cadena=ltrim($formula);
       // Recorro buscando las variables de la formula para buscar su funcion        
       $variablesg=$this->getVarForm($cadena); // Extraer variables de formulas que se validan en este registro
       $var='';$i=0;       
       // FUNCIONES DE LA EMPRESA
        
        
       // FUNCIONES PROPIAS DE NOMINA ----------------------------------------------------------------------
       if (in_array('sueldo', $variablesg)) // SUELDO
       {
         $dat     =  $this->getSueldo($ide);
         $sueldo  =  $dat->valor; 
       }       
       if (in_array('Valhora', $variablesg)) // VALOR HORA
       {
         $dat     =  $this->getVlrhora($ide);
         $Valhora =  $dat->valor; 
       }
       if (in_array('Valdia', $variablesg)) // VALOR DIA 
       {
         $dat     =  $this->getVlrdia($ide); 
         $Valdia  =  $dat->valor;       
       }
       if (in_array('SumLey', $variablesg))  // LEY 100
       {
         $dat     =  $this->getLey($iddn);   
         $SumLey  =  $dat->valor;             
       }                   
       if (in_array('Subsidio', $variablesg))  // SUBSIDIO
       {
         $dat     =  $this->getSubsidio($iddn);   
         $Subsidio  =  $dat->valor;             
       }          
       if (in_array('SubTrans', $variablesg))  // Transporte
       {
         $dat     =  $this->getSubTrans($iddn);   
         $SubTrans  =  $dat->valor;             
       }
       $diasInca  =  0;             
       $dat     =  $this->getDiasInca($iddn, $ide);  // Dias de incapacidad 
       if ($dat!='')
          $diasInca  =  $dat->valor;         
	   
       $sumDev  =  0;
       $sumDed  =  0;
       $sumTot  =  0;
	                
       $dat     =  $this->getSumDeCreTot($iddn);  // Dias de incapacidad 
       if ($dat!=''){
          $sumDev  =  $dat['devengado'] ;
          $sumDed  =  $dat['deducido'] ; 
          $sumTot  =  $dat['pagar'];
	   }

       $dat     =  $this->getPeriodoVaca($iddn);  // Datos de las vacaciones
       if ($dat!=''){
          $perVac  =  $dat['perVac'] ; // Periodos pagados en esta nomina , ejemplo 1 = 15 dias, 2 = 30 dias 
	   }	   
	   
	   
       $smlc = $this->salarioMinimoCovencional; 
       $smlv = $this->salarioMinimo;       

       // PROCESOS ESPECIALES EMPRESAS       
       $difSueldo  =  0;             
       $dat     =  $this->getDiferenciaSueldo($ide, $iddn);  // Diferencia en sueldo empleados
       if ($dat!='')
          $difSueldo  =  $dat->valor;   
       
       $dev=0;
       $ded=0;
       if ($idfor!=1) // Predeteminado el 1 para formulas manuales en conceptos
       {
         // Ejecucion de formula ------------------------------------------------------------              
         $valor = 0;
         if ($formula!='')           
          {
             eval("\$str =$formula;");
             $valor = $str;  
          }
           if ($valor!=0)
             {
              if ($horas==0)
              {
                if ($tipo==1)
                   $dev  = $valor;
                else 
                   $ded  = $valor; 
              }else{
                if ($tipo==1)
                   $dev  = $valor*$horas;
                else 
                   $ded  = $valor*$horas;                   
              }
             }// Fin val valor
       }// Fin valdia id de formula       
       if ($idCon==122) // Temporal para no embarrarla sueldo
       {
           if ($horas==0)
              $dev=0; 
       }
           
       $valores = array("dev"=>$dev,"ded"=>$ded);
       
       return $valores; 

   } // Fin guardar formula en novedades   
   // 
   public function getNove($idNom, $idEmp ,$diasLab, $diasVac ,$horas ,$formula ,$tipo ,$idCcos ,
                             $idCon, $ac, $tipn,$dev,$ded,$idfor,$diasLabC,$idCpres,$calc,$conVac,$obId)
   { 

   }

   // Busqueda de variables dentro de la formula para validar funciones de busqueda
   public function getVarForm($cadena)
   {
       $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
       $variablesg=array();
       $var='';
       for( $i=0; $i<=strlen($cadena); $i++)
       {
	 $caracter = substr(ltrim($cadena),$i,1);
         if (strstr($permitidos, $caracter ))
	 { 
            $var .=$caracter;			
         }else{
	    // Buscar nombre de la variable
            if (!(in_array($var, $variablesg)) )
 	       {
		  $variablesg[]=ltrim($var); 
                  $var = '';
               }// Fin si variable 		
	 }// Fin armado variable		 
       }// Fin para               
       return $variablesg;       
   }  
   
    
   // ----------------------------------------------------- 
   // FORMULAS FIJAS EN PROGRAMA *-----------------------------------------------------------------------------------------------
   // ------------------------------------------------- 
   // 1. Salario empleado
   public function getSueldo($id)
   {
      $result=$this->adapter->query("select (sueldo) as valor 
              from a_empleados where id=".$id,Adapter::QUERY_MODE_EXECUTE);

      $datos = $result->current();
      return $datos;
    }                   
   // 2. Valor hora empleado
   public function getVlrhora($id)
   {
      $result=$this->adapter->query("select (sueldo/horas) as valor from a_empleados where id=".$id,Adapter::QUERY_MODE_EXECUTE);
      //$datos=$result->toArray();
      $datos = $result->current();
      return $datos;
    }                   
   // 3. Valor dia empleado
   public function getVlrdia($id)
   {
      $result=$this->adapter->query("select (sueldo/30) as valor from a_empleados where id=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();      
      return $datos;
    }                   
   // 4. Sumatoria procesos Ley 100
   public function getLey($id)
   {
      $result=$this->adapter->query("Select case b.tipo when 1 then sum(devengado) when 2 then sum(deducido) end as valor  
                        from n_nomina_e_d a inner join n_conceptos b on a.idConc=b.id 
                        inner join n_conceptos_pr c on c.idConc=b.id where c.idProc=1 and  a.idInom=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();      
      return $datos;
   }                           
   // 5. Solidaridad
   public function getSolidaridad($ano, $mes , $idEmp)
   {
      $result=$this->adapter->query("Select d.id,  
case when ( ( sum(a.devengado) ) >(4*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(16*".$this->salarioMinimo.")  ) then ( sum(a.devengado)*(1/100) )
     when ( ( sum(a.devengado) ) >(16*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(17*".$this->salarioMinimo.")  ) then  ( sum(a.devengado)*(1.2/100) )
     when ( ( sum(a.devengado) ) >(17*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(18*".$this->salarioMinimo.") ) then ( sum(a.devengado)*(1.4/100) )    
     when ( ( sum(a.devengado) ) >(18*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(19*".$this->salarioMinimo.") ) then ( sum(a.devengado)*(1.6/100) )             
     when ( ( sum(a.devengado) ) >(19*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(20*".$this->salarioMinimo.") ) then ( sum(a.devengado)*(1.8/100) )                      
     when ( ( sum(a.devengado) ) >(20*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(30*".$this->salarioMinimo.") ) then ( sum(a.devengado)*(2/100) )                               
	  else 0  
end as valor  
from  n_nomina_e_d a 
inner join n_conceptos b on a.idConc=b.id 
inner join n_conceptos_pr c on c.idConc=b.id 
inner join n_nomina_e d on d.id=a.idInom
inner join a_empleados e on e.id=d.idEmp
inner join n_nomina f on f.id = d.idNom 
where c.idProc=1 and # Todos los conceptos que hacen part de ley 100  
year(f.fechaI) = ".$ano." and month(f.fechaI) = ".$mes." and e.id =".$idEmp ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
    }
   // 5. Solidaridad VACACIONES
   public function getSolidaridadv($id, $idEmp)
   {
      $result=$this->adapter->query("Select d.id,  
case when ( ( sum(a.devengado) ) >(4*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(16*".$this->salarioMinimo.")  ) then ( sum(a.devengado)*(1/100) )
     when ( ( sum(a.devengado) ) >(16*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(17*".$this->salarioMinimo.")  ) then  ( sum(a.devengado)*(1.2/100) )
     when ( ( sum(a.devengado) ) >(17*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(18*".$this->salarioMinimo.") ) then ( sum(a.devengado)*(1.4/100) )    
     when ( ( sum(a.devengado) ) >(18*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(19*".$this->salarioMinimo.") ) then ( sum(a.devengado)*(1.6/100) )             
     when ( ( sum(a.devengado) ) >(19*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(20*".$this->salarioMinimo.") ) then ( sum(a.devengado)*(1.8/100) )                      
     when ( ( sum(a.devengado) ) >(20*".$this->salarioMinimo.") and ( sum(a.devengado) ) <=(30*".$this->salarioMinimo.") ) then ( sum(a.devengado)*(2/100) )                               
	  else 0  
end as valor  
from  n_nomina_e_d a 
inner join n_conceptos b on a.idConc=b.id 
inner join n_conceptos_pr c on c.idConc=b.id 
inner join n_nomina_e d on d.id=a.idInom
inner join a_empleados e on e.id=d.idEmp
where c.idProc=1 and a.idNom=".$id." and e.id=".$idEmp ,Adapter::QUERY_MODE_EXECUTE);

      $datos = $result->current();
      return $datos;
    }     
   // 6. Sumatoria procesos subsidios
   public function getSubsidio($id)
   {
      $result=$this->adapter->query("Select 
case when ( e.sueldo < (2*".$this->salarioMinimo.") ) then ".$this->subsidioTransporte."/2
	  else 0  
end as valor  
from  n_nomina_e_d a 
inner join n_conceptos b on a.idConc=b.id 
inner join n_conceptos_pr c on c.idConc=b.id 
inner join n_nomina_e d on d.id=a.idInom
inner join a_empleados e on e.id=d.idEmp
where c.idProc=1 and  a.idInom=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
    }    
    // 7. Devolver dia de inicio calendario   
   public function getPeriodo($fechai,$dias)
   {
       
       $nuevafecha = strtotime ( '+'.$dias.' day' , strtotime ( $fechai ) ) ;
       $fechaf  = date ( 'Y-m-j' , $nuevafecha );
       $dias=1;
       $nuevafecha = strtotime ( '+'.$dias.' day' , strtotime ( $fechaf ) ) ; // Inicio del periodo siguiente
       $fechai2 = date ( 'Y-m-j' , $nuevafecha );
       // Regular las fechas 
       $ini=0;$fin=0;
       
       $fechas = array("fechaI"=>$fechai , "fechaF"=>$fechaf, "fechaI2"=>$fechai2 );         
       
       return $fechas;       
   }    

   // 8. Dias laborado por empleado SIN USO
   public function getDiasLab($id)
   {
      $result=$this->adapter->query("select DATEDIFF( '2002-11-02', fecIng) from a_empleados where id=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }         
   // 9. Dias habiles o no habiles SIN USO
   public function getDiasHN($id)
   {
      $result=$this->adapter->query("select DATEDIFF( '2002-11-02', fecIng) from a_empleados where id=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }             
   // 10. Subsidio de transporte
   public function getSubTrans($id)
   {
      // Sumar todos los conceptos que hagan parte del sueldo 122 = Concepto de sueldo

      $result=$this->adapter->query("Select 
     case when (
          ( ( e.sueldo/2 ) +
       case when c.idProc = 2 then sum( a.devengado ) 
           else 0 end ) < ".$this->salarioMinimo." ) then ( ( ".$this->subsidioTransporte." )/ 30 ) else 0 end as valor   
        from n_nomina_e_d a 
            inner join n_conceptos b on a.idConc=b.id 
            inner join n_conceptos_pr c on c.idConc=b.id 
            inner join n_nomina_e d on d.id=a.idInom
            inner join a_empleados e on e.id=d.idEmp
            where a.idInom =".$id,Adapter::QUERY_MODE_EXECUTE);
//      $result=$this->adapter->query("Select  
//case when ( sum( case when a.idConc!=122 then a.devengado else e.sueldo end ) < (2*".$this->salarioMinimo.") ) 
//then ( (".$this->subsidioTransporte.") / 30 )
    //else 0  
//end as valor  
//from  n_nomina_e_d a 
//inner join n_conceptos b on a.idConc=b.id 
//inner join n_conceptos_pr c on c.idConc=b.id 
//inner join n_nomina_e d on d.id=a.idInom
//inner join a_empleados e on e.id=d.idEmp
//where c.idProc = 2 and  a.idInom =".$id,Adapter::QUERY_MODE_EXECUTE);

     // $result=$this->adapter->query("select case when ( sueldo < (2*".$this->salarioMinimo.") ) then 36000/15
	 //            else 0 end as valor   
       //              from a_empleados where id=".$id,Adapter::QUERY_MODE_EXECUTE);					 
					 
      $datos = $result->current();
      return $datos;
   }                
   // 11. Sumatoria procesos cesantias
   public function getCesantias($idEmp, $fechaI, $fechaF)
   {
      $result=$this->adapter->query("Select ( round ( ( ( case b.tipo when 1 then sum(devengado) when 2 then sum(deducido) end ) / 360 ) * 30  )  )  as valor, f.sueldo 
                        from n_nomina_e_d a inner join n_conceptos b on a.idConc=b.id # Todos los conceptos de cesantias menos el sueldo
                        inner join n_conceptos_pr c on c.idConc=b.id 
                        inner join n_nomina d on d.id=a.idNom 
                        inner join n_nomina_e e on e.id = a.idInom and a.idInom = e.id 
                        inner join a_empleados f on f.id=e.idEmp 
			where c.idProc=5 and b.id!=122 and e.idEmp=".$idEmp." 
			and ( d.fechaI >= '".$fechaI."'and d.fechaF <= '".$fechaF."' )",Adapter::QUERY_MODE_EXECUTE);
      //$datos = $result->current();
      $datos=$result->toArray();    
      return $datos;
   }                              
   // 11. Sumatoria procesos cesantias con sueldo
   public function getCesantiasS($idEmp, $fechaI, $fechaF)
   {
      $result=$this->adapter->query("Select ( round ( ( ( case b.tipo when 1 then sum(devengado) when 2 then sum(deducido) end ) + f.sueldo  / 360 ) * 30  ) )  as valor, f.sueldo 
                        from n_nomina_e_d a inner join n_conceptos b on a.idConc=b.id # Todos los conceptos de cesantias menos el sueldo
                        inner join n_conceptos_pr c on c.idConc=b.id 
                        inner join n_nomina d on d.id=a.idNom 
                        inner join n_nomina_e e on e.id = a.idInom and a.idInom = e.id 
                        inner join a_empleados f on f.id=e.idEmp 
			where c.idProc=5 and b.id!=122 and e.idEmp=".$idEmp." 
			and ( d.fechaI >= '".$fechaI."'and d.fechaF <= '".$fechaF."' )",Adapter::QUERY_MODE_EXECUTE);
      //$datos = $result->current();
      $datos=$result->toArray();    
      return $datos;
   }                                 
   // 12. Numero de dias por incapacidad
   public function getDiasInca($id, $ide)
   {
      $result=$this->adapter->query("select  case when a.reportada = 0 
          then sum( a.diasAp + a.diasDp ) else sum(a.diasDp )  end as valor  
           from n_nomina_e_i a 
           inner join n_nomina_e b on b.idNom = a.idNom and b.idEmp = a.idEmp
            where b.id = ".$id." and a.idEmp = ".$ide,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                
   
   
   // Sacar diferencia en sueldo para sumarlo al sueldo ( Temporal  )
   public function getDiferenciaSueldo($id, $idnn )
   {
      # Se trae el concepto de diferencia en sueldo para procesos especiales  
      $result=$this->adapter->query("select round((devengado/15)/8,0) as valor 
              from n_nomina_e_d a
              inner join n_nomina_e b on b.id = a.idInom
              where idConc = 134 and b.id=".$idnn." and b.idEmp = ".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                
   
   
   
   // Recalculo de documento de nomina   
   public function getRecalculo($idn)
   {
        $u = new Gnominag($this->adapter);
        // *-------------------------------------------------------------------------------
        // ----------- RECALCULO DE DOCUMENTO DE NOMINA -----------------------------------
        // *-------------------------------------------------------------------------------
        $datos2 = $u->getDocNove($idn, " and b.tipo in ('0','1','3')" );// Insertar nov automaticas ( n_nomina_e_d ) por tipos de automaticos                                                  
        foreach ($datos2 as $dato)
        {         
             $id      = $dato['idNom'];; // Id nomina  
             $iddn    = $idn;            // Id dcumento de novedad
             $idin    = $dato['id'];      // Id novedad
             $ide     = $dato['idEmp'];   // Id empleado
             $diasLab = $dato['dias'];    // Dias laborados 
             $diasVac = $dato['diasVac'];    // Dias vacaciones
             $horas   = $dato["horas"];   // Horas laborados 
             $formula = $dato["formula"]; // Formula
             $tipo    = $dato["tipo"];    // Devengado o Deducido  
             $idCcos  = $dato["idCcos"];  // Centro de costo   
             $idCon   = $dato["idCon"];   // Concepto
             $dev     = $dato["devengado"]; // Devengado
             $ded     = $dato["deducido"];  // Deducido  
             $idfor   = $dato["idFor"];   // Id de la formula   
             $diasLabC= $dato["horDias"];   // Si se afecta el cambio de dias laborados en el registro 
             $conVac   = 0;
             $obId     = 0;
             // Llamado de funion -------------------------------------------------------------------
             $this->getNomina($id, $iddn, $idin, $ide ,$diasLab,$diasVac ,$horas ,$formula ,$tipo ,$idCcos , $idCon, 1, 2,$dev,$ded,$idfor,$diasLabC,0,0,$conVac,$obId);                                          
        }
   }

   // Total para suma de creditos y debitos en documento de empleados
   public function getSumDeCreTot($id)
   {
      # Se trae el concepto de diferencia en sueldo para procesos especiales  
      $result=$this->adapter->query("Select sum( a.devengado ) as devengado,
        sum( a.deducido) as deducido, 
        sum( a.devengado ) - 
		  ( sum( a.deducido ) ) as pagar 
        from  n_nomina_e_d a 
        where a.idInom = ".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                

   // Datos de vacacion de empleado 
   public function getPeriodoVaca( $idnn )
   {
      $result=$this->adapter->query("select a.idVac , round( b.dias / 15, 2 ) as perVac 
              from n_nomina_e a 
              inner join n_vacaciones b on b.id = a.idVac 
              where a.id=".$idnn,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                   
   

}