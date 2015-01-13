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

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

use Principal\Model\LogFunc;

/// INDICE

// Update general
// Consulta general
// Consulta general publica
// Consulta mostrar borrado o no 

// ESPECIAL: Datos de acceso a la opcion actual 

// Lista de roles
// Rol usuario
// rol de un usuario 
// Niveles de aspectos 
// Listado de lista de chqueos o niveles de cargos 
// Etapas de contratacion para lista de chequeo 
// Etapas de contratacion en tipo de contratacion
// Listado de cargos
// Listado de listas para cargos
// Listado de aspirantea de chqueos o niveles de cargos 
// Listado de departamentos
// Listado de sedes
// Listado de documentos aprobados para contratacion de cargos
// Listado de vacantes de cargos
// Cargos hoja de vida 
// Etapas de la lista de chequeo
// Listado de aspirantes cabecera lista de chequeo y resultados
// Listado de formularios
// Listado de etapas items y formularios
// Grupo de nomina
// Sub grupo de nomina
// Calendario
// Centros de costos
// Tipos de nomina
// Conceptos de nomina
// 
// Tipos de automaticos
// Prefijo contable
// Tipos de contratacion
// Tipos de automaticos en nominas aplicadas
// Movimiento de calendario en nomina
// Tipos de empleado
// Tipos de conceptos automaticos en nominas aplicadas
// Conceptos de nominas hijas 
// Lista de procesos
// Procesos del concepto
// Lista de empleados activos
// Listado maestro de empleados activos
// Lista de ausentismos
// Lista de incapacidades
// Calendario por tipo de nominas
// Periodos del tipo de calendario    
// Conceptos aplicados a una matriz
// Configuraciones generales
// Listado de cabeceras
// Promedio pago vacaciones
// Listado de vacaciones
// Listado de tipos de prestamos
// Consulta dias no habiles 
// Consulta empleados en nomina 
// Conceptos aplicados a tipos de incapacidades
// Conceptos aplicados a tipos de ausentismos
// Lista de salarios
// Escalas salariales 
// Escalas salariales en el cargo
// Listado de tipos de embargos
// Listado de bancos


//// LISTADOS MAESTROS Y DOCUMENTOS ///

// Documento novedades antes de nomina
// Listado maestro de conceptos activos
// Listado maestro de empleados activos
// Listado maestro de tipos de automaticos 
// Listado maestro de otros automaticos 


//// LISTADOS TALENTO HUMANO ///

// Listado de nivel de estudios
// Filtro de hojas de vidas por cargos
// Datos de la solictud de contratacion 
// Inventarios de dotaciones
// Listado grupos de dotaciones
// Listado areas
// Listado de tipos de descargas
// Listado de tipos de eventos
// Listado de descargos
// Listado lineas de dotaciones

class AlbumTable extends AbstractTableGateway
{
   protected $table  = 't_nivelasp';
   protected $table2 = 't_etapas_con';
   protected $table3 = 't_etapas_con';
   protected $table4 = 't_cargos';
   
   
   public $dbAdapter;
    
   public function __construct(Adapter $adapter)
   {
        $this->adapter = $adapter;
        $this->initialize();
   }
   
   // Update general
   public function modGeneral($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);

    }                  
   // Consulta general
   public function getGeneral($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }
   // Consulta general publica
   public function getGeneralP($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }   
   // Consulta general 1
   public function getGeneral1($con)
   {
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      //$datos=$result->toArray();
      $datos = $result->current();
      return $datos;
   }   
   // Consulta mostrar borrado o no
   public function getBregistro($tabla,$campo,$id)
   {
      $result=$this->adapter->query("select id as bloquear from ".$tabla." where ".$campo." =".$id,Adapter::QUERY_MODE_EXECUTE);
      //$datos=$result->toArray();
      $datos = $result->current();
      return $datos;
   }   
    
   // ESPECIAL: Datos de acceso a la opcion actual  
   public function getPermisos($lin)
   {
      $t = new LogFunc($this->adapter);
      $dt = $t->getDatLog();      

      if ($dt['admin']==1)
         $con = "select 1 as nuevo, 1 as modificar, 1 as eliminar, 1 as aprobar from c_mu2 limit 1";      
      else // Usuario no administrador
         $con = "select c.nuevo,c.modificar,c.eliminar,c.aprobar 
                 from c_mu2 a inner join c_opciones_m b on b.id=a.idOpM
                 inner join c_roles_o c on c.idM3=a.id 
                 inner join c_roles d on d.id=c.idRol
                 inner join users e on e.idRol=d.id 
                 where d.estado=0 and e.id=".$dt['idUsu']." and concat('/',a.modelo , '/' , a.controlador, '/' ,  a.vista)='$lin'";        
      
      $result=$this->adapter->query($con,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();

//      if ( ($datos['nuevo']==0) and ($datos['modificar']==0) and ($datos['eliminar']==0) )
//     {
//          $datos='';
//      }
      return $datos;
   }
   // ESPECIAL: Enviar correo
   public function sendMail($htmlBody, $textBody, $subject, $from, $to)
   {
       ini_set('max_execution_time', 300); // 5 minutos pro procesamiento ( si Safe mode en php.ini esta desabilitado funciona )            
       $message = new Message();
       $message->addTo('wilsonmet8@gmail.com')
               ->addFrom('wilsonmet8@gmail.com')
               ->setSubject('Nissi web Invitación capacitación ');
    
        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
      $options   = new SmtpOptions(array(
    'name' => 'localhost.localdomain',
    'host' => 'localhost',
    'port' => 25,
));
//        $options   = new SmtpOptions(array(
  //           'host'              => 'smtp.gmail.com',
    //         'connection_class'  => 'login',
      //       'connection_config' => array(
        //     'ssl'       => 'ssl', // tls
          //   'username' => 'wilsonmet8@gmail.com',
        // 'password' => 'junior19247'
       // ),
      //  'port' => 587,
     //  ));
 // 587 
       $html = new MimePart('<strong>Empresa de prueba<strong><br /> '
               . 'Ref: Invitación capacitación <hr /> '
               . 'Sr(a) DIANA ORTEGA FLEREZ <br /><br /><br /> '
               . 'Le hacemos participe de la capacitacion proxima a dictarse en nuestras instalaciones.<hr /><br /> '
               . 'Area : Informatica <br /> '
               . 'Tipo : Conferencia <br />  '
               . 'Tematica : Tecnologías de la información <hr />'
               . '<br /><br />'
               . '');
       $html->type = "text/html";
 
       $body = new MimeMessage();
       $body->addPart($html);
 
       $message->setBody($body);
 
       $transport->setOptions($options);
       $transport->send($message);
    }   
                
    // CONSULTAS FIJAS *-----------------------------------------------****

   // Lista de roles
   public function getRoles($con)
   {
      $result=$this->adapter->query("select * from c_roles where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                          
   // Rol usuario
   public function getRolUsu($usu)
   {
      $result=$this->adapter->query("select id, idRol, admin from users where usr_name='".$usu."'" ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                             
   
   // Listado de aspectos de cargos
   public function getAsp($con)
   {
      $result=$this->adapter->query("select * from t_asp_cargo $con order by nombre ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }   
   // Niveles de aspectos   
   public function getNasp()
   {
      $result=$this->adapter->query("select * from t_nivelasp order by nombre ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }
   // Etapas de contratacion para lista de chequeo  
   public function getLcheq($id)
   {
      $id  = (int) $id; 
      $result=$this->adapter->query("select a.id, a.nombre
         from t_etapas_con a left join t_nivel_cargo_o b on a.id=b.idEtapa and b.idNcar=$id
         where b.id is null order by a.nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }    
   // Etapas de contratacion en tipo de contratacion
   public function getLcheqTcon($id)
   {
      $id  = (int) $id;
      $result=$this->adapter->query("select a.id, a.nombre, b.orden  
                                       from t_etapas_con a 
                                        inner join t_nivel_cargo_o b on a.id=b.idEtapa where b.idNcar=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }        
   // Listado de cargos
   public function getCargos()
   {
      $result=$this->adapter->query("select * from t_cargos order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }            
   // Listado de lista de chqueos o niveles de cargos 
   public function getNcargos()
   {
      $result=$this->adapter->query("select * from t_nivel_cargo order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                
   // Listado de departamentos
   public function getDepar()
   {
      $result=$this->adapter->query("select * from t_departamentos order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                    
   // Listado de grupo de dotaciones
   public function getGdot()
   {
      $result=$this->adapter->query("select * from t_grup_dota order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                        
   // Listado de sedes
   public function getSedes()
   {
      $result=$this->adapter->query("select * from t_sedes order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                            
   // Listado de vacantes para cargos
   public function getVaca($con)
   {
      $result=$this->adapter->query("select distinct a.id,a.nombre as nom_hoj,a.apellido as ape_hoj,
          b.idCar,a.estado,a.fecReg as fec_reg, c.nombre as nomCar, 
          case when d.idSol is null then 0 else d.idSol end as idSol,a.estado  
          from t_hoja_vida a 
			 inner join t_hoja_vida_c b on a.id=b.idHoj 
			 left join t_cargos c on c.id=b.idCar 
          left join t_lista_cheq d on d.idHoj=a.id
			 where a.estado != 2 $con ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                            
    
   // Cargos hoja de vida
   public function getCarHoj($con)
   {
      $result=$this->adapter->query("select * from t_hoja_vida_c where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                              
   // Listado de documentos aprobados para contratacion de cargos
   public function getSolcon($con)
   {
      $result=$this->adapter->query("select a.*, concat(b.nombre,' (', b.deno,')' ) as nomCar,
                                            c.nombre as nomSed, d.nombre as nomCcos , 
                                            case when b.id is null then 0 else b.id end as idCheq,count(b.id) as numAsp    
                                            from t_sol_con a 
                                            inner join t_cargos b on b.id=a.idCar 
                                            inner join t_sedes c on c.id=b.idSed
                                            inner join n_cencostos d on d.id = a.idCcos 
                                            left join t_lista_cheq e on e.idSol = a.id 
                                            where ".$con." 
                                            group by a.id     
                                            order by a.id desc ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                        
   // Listado de documentos aprobados para contratacion de cargos
   public function getSolconG($con)
   {
      $result=$this->adapter->query("select a.*, concat(b.nombre,' (', b.deno,')' ) as nomCar, c.nombre as nomSed, d.nombre as nomCcos  
                                            from t_sol_con a 
                                            inner join t_cargos b on b.id=a.idCar 
                                            inner join t_sedes c on c.id=b.idSed
                                            inner join n_cencostos d on d.id = a.idCcos 
                                            where ".$con."  
                                            order by a.id desc ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->Current();
      return $datos;
    }                                    
   // Listado de aspirantes cabecera lista de chequeo 
   public function getAspi($con)
   {
      $result=$this->adapter->query("select distinct a.*,b.fecDoc,d.nombre as nomCar, e.nombre as nomSede
          ,c.cedula,  c.nombre, c.apellido, a.contratado, a.empleado, b.vacantes    
          from t_lista_cheq a inner join t_sol_con b
          inner join t_hoja_vida c on c.id=a.idHoj           
          inner join t_cargos d on d.id=b.idCar  
          inner join t_sedes e on e.id=d.idSed
          inner join t_hoja_vida_c f on f.idHoj=c.id and f.idCar=b.idCar  
          where $con ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                    
   // Etapas de la lista de chqueo
   public function getEtcehq($con)
   {
      $result=$this->adapter->query("select h.id as idDlchq, a.contratado, a.idHoj, a.empleado, e.id,e.idNcar,e.idEtapa,f.nombre,g.id as idItem, g.nombre as nomItem, g.idForm,
      i.nombre as nomForm, h.id , h.descripcion , h.estado, i.tipo, a.id as idCheq, g.orden     
      from t_lista_cheq a inner join t_sol_con b
      on a.idSol=b.id inner join t_cargos c on b.idCar=c.id
      inner join t_nivel_cargo d on d.id=c.idNcar
      inner join t_nivel_cargo_o e on e.idNcar=d.id
      inner join t_etapas_con f on e.idEtapa=f.id
      inner join t_etapas_con_i g on g.idEtacon=f.id
      left join t_lista_cheq_d h on h.idEtaI=g.id and h.idCheq=a.id 
      left join t_form i on g.idForm=i.id 
      where $con order by e.orden, g.orden",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                          
   // Etapas de la lista de chqueo sum total
   public function getEtcehqSt($con)
   {
      $result=$this->adapter->query("select a.id, count(b.estado) as estado from t_lista_cheq a 
                inner join t_lista_cheq_d b on b.idCheq=a.id 
                where $con group by a.id ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                          
   // Etapas de la lista de chqueo sum total calificados
   public function getEtcehqStc($con)
   {
      $result=$this->adapter->query("select a.id, count(b.estado) as estado from t_lista_cheq a 
                inner join t_lista_cheq_d b on b.idCheq=a.id 
                where $con group by a.id ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                          
    
   // Listado de formularios                                       
   public function getForm()
   {
      $result=$this->adapter->query("select * from t_form order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                               
   // Listado de etapas items y formularios
   public function getIform($con)
   {
      $result=$this->adapter->query("select a.*,b.nombre as nomform 
                                     from t_etapas_con_i a left join t_form b on a.idForm=b.id ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                   
   // Listado de etapas items, formularios e items
   public function getIformI($con, $idCheq)
   {
      $result=$this->adapter->query("select a.*,b.nombre as nomform, c.idForm, c.id as idIform, 
                                     c.nombre as nomIform, c.lista, c.tipo, c.ubi, d.lista as lisForm, 
			   	     d.texto as texForm, d.casilla as casForm    
                                     from t_etapas_con_i a 
			             inner join t_form b on a.idForm=b.id
			             inner join t_form_i c on c.idForm=b.id 
			             left join t_lista_cheq_f d on d.idIform=c.id 
                                     and d.idDcheq=".$idCheq." where a.id>0".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                       
   // Listado de aspectos de cargo en nivel de aspectos 
   public function getNaspN($con, $idC)
   {
      $result=$this->adapter->query("select a.*,b.nombre as nomAsp,                                     
 			    	    d.texto as texRes, d.lista as listRes, # datos guardados directamente en los items del aspecto
                                    d.a as aR, d.b as bR, d.c as cR, d.d as dR, d.e as eR, 
				    c.a as aC, c.b as bC, c.c as cC, c.d as dC, c.e as eC,# Datos guardados en el cargo cuando el item del aspecto lo pide
				    d.texto as textCar, d.estado  
                                    from t_asp_cargo_i a 
                                    inner join t_asp_cargo b on a.idAsp=b.id
                                    left join t_cargos_a c on c.idIasp = a.id 
                                    left join t_lista_cheq_vc d on d.idAspI = a.id and d.idDcheq=".$idC." 
                                    where b.tipo=2 ".$con,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                
   // Adjuntos lista de chequeo
   public function getAdjCheq($id)
   {
      $result=$this->adapter->query("Select * from t_lista_cheq_d_a where idCheq=".$id,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                   
   
   // Fondos de prestacion social 
   public function getFondos($con)
   {
      $result=$this->adapter->query("select * from t_fondos where tipo=".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                           
   // Grupo de nomina
   public function getGrupo()
   {
      $result=$this->adapter->query("select * from n_grupos where activa = 0 order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                               
   // Grupo de nomina 2
   public function getGrupo2()
   {
      $result=$this->adapter->query("select * from n_grupos order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                                   
   // Sub grupo de nomina
   public function getSgrupo()
   {
      $result=$this->adapter->query("select * from n_subgrupos order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                                   
   // Calendario
   public function getCalen($con)
   {    
      $result=$this->adapter->query("select * from n_tip_calendario where activo=0 $con order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                       
   // Centros de costos
   public function getCencos()
   {
      $result=$this->adapter->query("select * from n_cencostos where estado=0 order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                           
   // Tipos de nomina
   public function getTnom($con)
   {
      $result=$this->adapter->query("select * from n_tip_nom where estado=0 ".$con." order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                           
   // Conceptos de nomina 
   public function getConnom()
   {
      $result=$this->adapter->query("select *, case when valor=1 then 'HORAS' else 'VALOR' end as tipVal"
              . " from n_conceptos order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                   
   // Conceptos de nomina  2
   public function getConnom2($con)
   {
      $result=$this->adapter->query("select *, case when valor=1 then 'HORAS' else 'VALOR' end as tipVal 
                                    from n_conceptos where id > 0 ".$con." order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                      
   // Variables de nomina 
   public function getFormulas()
   {
      $result=$this->adapter->query("select * from n_formulas order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                      
   // Tipos de automaticos
   public function getTautoma()
   {
      $result=$this->adapter->query("select * from n_tip_auto order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                         
   // Prefijos contables
   public function getPrefcont()
   {
      $result=$this->adapter->query("select * from n_pref_con order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                            
   // Tipos de contratacion
   public function getTipcont()
   {
      $result=$this->adapter->query("select * from a_tipcon order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                               
   // Tipos de automaticos en nominas aplicadas
   public function getTipaNapl($con)
   {
      $result=$this->adapter->query("select * from n_tip_auto_tn where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                
   // Tipos de prestamos en nominas aplicadas
   public function getTippNapl($con)
   {
      $result=$this->adapter->query("select * from n_tip_prestamo_tn where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                   
   // Movimiento de calendario en nomina
   public function getMcalen($con)
   {
      $result=$this->adapter->query("select * from n_tip_calendario_d where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                     
  // Tipos de empleado
   public function getTemp($con)
   {
      $result=$this->adapter->query("select * from n_tipemp where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }           
   // Tipos de conceptos automaticos en nominas aplicadas
   public function getConaNapl($con)
   {
      $result=$this->adapter->query("select * from n_conceptos_tn where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                    
   // Conceptos de nominas hijas 
   public function getConNhij($con)
   {
      $result=$this->adapter->query("select * from n_conceptos_th where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                       
   // Conceptos a tipos de empleados
   public function getConNtemp($con)
   {
      $result=$this->adapter->query("select * from n_conceptos_te where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                          
   // Lista de procesos
   public function getProcesos($con)
   {
      $result=$this->adapter->query("select * from n_procesos where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                       
   // Procesos del concepto
   public function getConPro($con)
   {
      $result=$this->adapter->query("select * from n_conceptos_pr where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                       
   // Lista de empleados activos
   public function getEmp($con)
   {
      $result=$this->adapter->query("select * from a_empleados where activo=0 ".$con." order by nombre,apellido" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
   // Listado maestro de empleados activos
   public function getEmpM($con)
   {
      $result=$this->adapter->query("select a.id, a.CedEmp, a.activo, a.nombre, a.apellido, b.codigo as codCar, 
                                b.nombre as nomCar, c.id as idCcos, c.nombre as nomCcos,
                                idVac, vacAct, idInc, a.SexEmp, a.SexEmp, 
                                d.id as idGdot, d.nombre as nomGdot, e.fecDoc as FecUdot, 
				a.estado , count(e.id) as numDot, d.numero as numDotP, f.porc             
                                from a_empleados a 
				left join t_cargos b on a.idCar=b.id
                                inner join n_cencostos c on a.idCcos=c.id 
                                left join t_grup_dota d on d.id=b.idGdot 
                                left join t_dotaciones e on e.idEmp=a.id and year(e.fecDoc) = year(now()) 
                                left join n_tarifas f on f.id = a.idRies 
                                where a.activo=0 ".$con." 
                                group by a.id     
                                order by a.nombre,a.apellido, e.fecDoc desc" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
   // Listado maestro de conceptos activos
   public function getConM($con)
   {
      $result=$this->adapter->query("select a.*,case a.tipo when 1 then 'DEVENGADO' 
                          when 2 then 'DEDUCIDO' end as nomTipo,
			  case a.valor when 1 then 'HORAS' 
			  when 2 then 'VALOR' end as nomVal   
                          from n_conceptos a                            
                          order by a.valor, a.nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   } 
   // Listado maestro de tipos de automaticos 
   public function getTauM($con)
   {
      $result=$this->adapter->query("select a.*,c.nombre as nomTnom, e.codigo, e.nombre as nomCon 
           from n_tip_auto a 
           left join n_tip_auto_tn b on b.idTauto=a.id 
           left join n_tip_nom c on c.id=b.idTnom
           left join n_tip_auto_i d on d.idTauto=a.id
           left join n_conceptos e on e.id=d.idCon" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }    
   // Listado maestro de otros automaticos 
   public function getOtauM($con)
   {
      $result=$this->adapter->query("select distinct a.id,a.CedEmp, a.nombre as nomEmp,
                  a.apellido,b.nombre as nomCar,
                  c.nombre as nomGrup, d.nombre as nomtau, 
                  f.codigo, f.nombre as nomCon, e.valor, h.nombre as nomTnom   
                  from a_empleados a 
		  inner join t_cargos b on a.idCar=b.id 
		  inner join n_grupos c on a.idGrup=c.id 
		  inner join n_tip_auto d on d.id=a.idTau 
		  inner join n_emp_conc e on e.idEmp=a.id 
		  inner join n_conceptos f on f.id=e.idCon
                  inner join n_emp_conc_tn g on g.idEmCon=e.id 
                  inner join n_tip_nom h on h.id=g.idTnom" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }       
   // Listado maestro de empleados activos general 
   public function getEmpMG($con)
   {
      $result=$this->adapter->query("select a.CedEmp, a.activo, a.id, a.nombre, a.apellido, b.codigo as codCar, 
                                b.nombre as nomCar, c.id as idCcos, c.nombre as nomCcos,
                                idVac, vacAct, idInc, d.nombre as nomSal, e.nombre as nomPen,
				f.nombre as nomCes, g.nombre as nomArp, g.nombre as nomFav, h.nombre as nomFafc,
				j.nombre as nomTcon, k.nombre as nomTemp, m.nombre as nomGrup,
				n.nombre as nomTau1, o.nombre as nomTau2, p.nombre as nomTau3, 
                                q.nombre as nomTau4, a.sueldo, a.FecNac, a.DirEmp , a.TelEmp, 
                                a.email, a.fecIng, 
				( DATEDIFF( now() , a.fecIng  ) )  as dias,
				round( ( DATEDIFF( now() , a.fecIng ) ) / 365 , 0 )   as anos                                 
                                from a_empleados a  
				inner join t_cargos b on a.idCar=b.id
                                inner join n_cencostos c on a.idCcos=c.id 
                                inner join t_fondos d on d.id=a.idFsal
                                inner join t_fondos e on e.id=a.idFpen
                                left  join t_fondos f on f.id=a.idFces
                                left join t_fondos g on g.id=a.idFarp 
                                left join t_fondos h on h.id=a.idFav 
                                left join t_fondos i on i.id=a.idFafc
                                left join a_tipcon j on j.id=a.IdTcon 
                                left join n_tipemp k on k.id=a.idTemp 
                                left join n_grupos m on m.id=a.idGrup
                                left join n_tip_auto n on n.id=a.idTau
                                left join n_tip_auto o on o.id=a.idTau2
                                left join n_tip_auto p on p.id=a.idTau3
                                left join n_tip_auto q on q.id=a.idTau4 
				where a.activo=0 ".$con." order by a.nombre,a.apellido" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }     
   
   // Lista de ausentismos
   public function getAusentismos($con)
   {
      $result=$this->adapter->query("select * from n_tip_aus where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                             
   // Lista de incapacidades
   public function getIncapacidades($con)
   {
      $result=$this->adapter->query("select * from n_tipinc where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                   
   // Calendario por tipo de nominas
   public function getCalendario($con)
   {
      $result=$this->adapter->query("Select b.valor,a.idTcal  
                             from n_tip_nom a inner join n_tip_calendario b 
                             on a.idTcal=b.id where a.id=".$con ,Adapter::QUERY_MODE_EXECUTE);
      //$datos=$result->toArray();
      $datos = $result->current();
      return $datos;
   }                
   // Periodos del tipo de calendario    
   public function getCalenIniFin($idGrupo, $idCal, $idTnom)
   {
      $result=$this->adapter->query("select * from n_tip_calendario_d 
         where idGrupo = $idGrupo and idCal = $idCal and idTnom =".$idTnom,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }              
   // Periodos del tipo de calendario   2 
   public function getCalenIniFin2($idGrupo, $idCal, $idTnom)
   {
      $result=$this->adapter->query("select a.*, case when b.id is null then 0 else b.id end as idNom 
         from n_tip_calendario_d a
         left join n_nomina b on b.idTnom = a.idTnom and b.idGrupo = a.idGrupo 
	 and b.idCal = a.idCal and b.fechaI=a.fechaI and b.fechaF = a.fechaF
         where a.estado=0 and a.idGrupo = $idGrupo and a.idCal = $idCal and a.idTnom =".$idTnom." order by fechaI",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }              
   // Listado de matrices
   public function getMatz($con)
   {
      $result=$this->adapter->query("select * from n_tip_matriz where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                           
   // Conceptos aplicados a una matriz
   public function getConaMatz($con)
   {
      $result=$this->adapter->query("select * from n_tip_matriz_tnv where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                        
   // Configuraciones generales
   public function getConfiguraG($con)
   {
      $result=$this->adapter->query("select * from c_general ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                                           
   // Listado de cabeceras
   public function getCabInf($con)
   {
      $result=$this->adapter->query("select * from i_cabecera ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                              
   // Listado de pies de documentos
   public function getPieInf($con)
   {
      $result=$this->adapter->query("select * from i_pie ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;
   }                                              
   
   // Promedio pago vacaciones
   public function getVacaP($idEmp, $fecSal)
   {
      // 6 id proceso de vacaciones
      $result=$this->adapter->query("select sum( round( ( (c.devengado) / 360  ) * 30, 0 ) ) as promedio  
from n_nomina a 
inner join n_nomina_e b on b.idNom=a.id
inner join n_nomina_e_d c on c.idInom=b.id
inner join n_conceptos d on d.id=c.idConc
inner join n_conceptos_pr e on e.idConc=d.id 
inner join a_empleados f on f.id=b.idEmp 
where a.estado=2 and e.idProc=6 and b.idEmp=".$idEmp ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->toArray();
      return $datos;      
   }                
   // Listado de vacaciones
   public function getSovac($con)
   {
      $result=$this->adapter->query("select a.*,b.CedEmp,b.nombre,b.apellido,
                      concat(c.nombre,' (', c.deno,')' ) as nomCar
                                            from n_vacaciones a 
                                            inner join a_empleados b on b.id=a.idEmp
                                            left join t_cargos c on c.id=b.idCar 
                                            where ".$con."  
                                            order by a.fecDoc desc ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                              
   // Listado de tipos de prestamos
   public function getTpres($con)
   {
      $result=$this->adapter->query("select * from n_tip_prestamo order by nombre ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                  
    // Consulta dias no habiles 
   public function getConfHn($fecha)
   {
      $result=$this->adapter->query("select * from c_general_dnh where fecha='".$fecha."'",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;
   }                                      
   // Consulta empleados en nomina     
   public function getNomEmp($con)
   {
      $result=$this->adapter->query("select * from n_nomina_e ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                     
   // Conceptos aplicados a tipos de incapacidades   
   public function getConTinc($con)
   {
      $result=$this->adapter->query("select * from n_tipinc_c where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                           
   // Conceptos aplicados a tipos de ausentismos
   public function getConTaus($con)
   {
      $result=$this->adapter->query("select * from n_tip_aus_c where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                              
   // Lista de salarios
   public function getSalarios($con)
   {
      $result=$this->adapter->query("select * from n_salarios where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                 
   // Escalas salariales
   public function getSalCargos($con)
   {
      $result=$this->adapter->query("select * from t_cargos_sa where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                       
   // Escalas salariales en el cargo
   public function getEsalCargo($con)
   {
      $result=$this->adapter->query("select b.*,a.id as idCsa, a.idCar 
                                     from t_cargos_sa a 
                                     inner join n_salarios b on b.id=a.idSal 
                                     where b.estado=0 ".$con." order by b.salario " ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                          
   // Listado de tipos de embargos
   public function getTemb($con)
   {
      $result=$this->adapter->query("select * from n_tip_emb order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }               
   // Documento novedades antes de nomina    
   public function getDnovedades($con)
   {
      $result=$this->adapter->query("select a.id, b.nombre as nomConc, c.nombre as nomEmp, c.apellido as apeEmp, c.CedEmp,   
                          a.devengado, a.deducido, a.horas, d.fechaI , d.fechaF, c.sueldo  
								  from n_novedades a 
                          inner join n_conceptos b on b.id=a.idConc
                          inner join a_empleados c on c.id=a.idEmp 
			  inner join n_tip_calendario_d d on d.id=a.idCal 
                          where a.estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                   
   // Listado de nivel de estudios
   public function getNestudios($con)
   {
      $result=$this->adapter->query("select * from t_nivel_estudios where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }           
   // Filtro de hojas de vidas por cargos
   public function getHojasVida($con)
   {
      $result=$this->adapter->query("select b.*, case when c.id is null then 0 else c.id end as idEmp 
                                     from t_hoja_vida_c a 
                                     inner join t_hoja_vida b on b.id=a.idHoj
                                     left join a_empleados c on c.idHoj = b.id
                                     where b.estado in ('0', '2')".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                 
   // Datos de la solictud de contratacion 
   public function getDatSol($id)
   {
      $result=$this->adapter->query("select distinct c.nombre as nomCar, e.nombre as nomSed,b.vacantes,b.fecDoc,
                            f.cedula, f.nombre,  f.apellido , b.estado,
                            e.nombre as nomSed      
                            from t_lista_cheq a 
                            inner join t_lista_cheq_d d on a.id=d.idCheq
                            inner join t_sol_con b on a.idSol=b.id
                            inner join t_cargos c on b.idCar=c.id
                            inner join t_sedes e on e.id=c.idSed 
                            inner join t_hoja_vida f on f.id=a.idHoj 
                            where a.id=".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                    
   // 
   // Inventario de dotaciones
   public function getInvDot($id)
   {
      $result=$this->adapter->query("select *, case tipo when 1 then 'Hombre' 
                                                  when 2 then 'Mujer'
						  when 3 then 'Unisex' end as tipNom   
						  from t_mat_dota order by nombre, tipNom ".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                    
   // Listado grupos de dotaciones 
   public function getGrupDot($id)
   {
      $result=$this->adapter->query("select * from t_grup_dota".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                    
   // Listado areas
   public function getAreas($con)
   {
      $result=$this->adapter->query("select * from t_areas_capa ".$con." order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                       
   // Listado lineas de dotaciones
   public function getLinDot($con)
   {
      $result=$this->adapter->query("select * from t_lineas_dot ".$con." order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                          
   // Listado tallas
   public function getTallasDot($con)
   {
      $result=$this->adapter->query("select * from t_tallas ".$con." order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                             
   // Listado de tipos de descargas   
   public function getTdescargos($con)
   {
      $result=$this->adapter->query("select * from t_tipo_descar ".$con." order by nombre" ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                          
   // Listado de descargos
   public function getDescargos($con)
   {
      $result=$this->adapter->query("select a.* , b.nombre as nomTdes
                                        from t_descargos a 
                                        inner join t_tipo_descar b on b.id = a.idTdes 
                                        where ".$con."  
                                        order by a.fecDoc desc ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                       
   // Listado de tipos de eventos
   public function getTeventos($con)
   {
      $result=$this->adapter->query("select  * 
                                            from t_tipo_eventos                                               
                                            order by nombre ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                         
   // Listado de tipos de capacitaciones
   public function getTcapa($con)
   {
      $result=$this->adapter->query("select  * 
                                            from t_tipo_capa  
                                            order by nombre ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                               
   // Listado de bancos    
   public function getBancos($con)
   {
      $result=$this->adapter->query("select  * 
                                            from n_bancos 
                                            order by nombre ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                                               
   // Hojas de vidas para excel
   public function getHojasVidaE($con)
   {
      $result=$this->adapter->query("select a.*, c.nombre as nomCar from t_hoja_vida a 
           inner join t_hoja_vida_c b on b.idHoj=a.id  
           inner join t_cargos c on c.id = b.idCar 
           where a.estado=0".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                     
   // Hojas de vidas para excel
   public function getPresCuotas($id, $idPres)
   {
      $result=$this->adapter->query("select distinct  a.idTnom , b.nombre as nomTnom, c.valor, c.cuotas, c.valCuota   
                                    from n_tip_prestamo_tn a inner join n_tip_nom b on b.id = a.idTnom
                                    left join n_prestamos_tn c on c.idTnom = b.id  and c.idPres = ".$idPres."   
                                    " ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                     
   // Listado de entidaes
   public function getEntidades()
   {
      $result=$this->adapter->query("select * from t_entidades ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }               
   // Primas de antiguedad
   public function getPrimaAnt()
   {
      $result=$this->adapter->query("select a.*, b.id as idCon, b.tipo, a.anual 
                                    from n_prima_anti a 
                                    inner join n_conceptos b on b.id = a.idConc 
                                    where a.estado=0 ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }               
   // Tipos de liquidacion
   public function getLiquidacion()
   {
      $result=$this->adapter->query("select * from n_tip_liqu ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }               
   // Motivos de retiro
   public function getMotRetiro()
   {
      $result=$this->adapter->query("select * from n_mot_ret order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }                   
   // Consulta ultimo fecha ultimo aumento salaria empleado
   public function getAsalariaF($id, $fecha)
   {
      $result = $this->adapter->query("select b.fecDoc, 
                   round( ( DATEDIFF( '".$fecha."' , b.fecDoc ) ) / 30, 0 ) as meses 
                   from a_empleados a 
                   inner join n_asalarial_d c on c.idEsal = a.idSal  
                   inner join n_asalarial b on b.id = c.idAsal 
                   where a.id=".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                
   // Consulta periodo de nomina
   public function getPerNomina($id)
   {
      $result = $this->adapter->query("Select idGrupo,fechaI,fechaF from n_nomina where id=".$id ,Adapter::QUERY_MODE_EXECUTE);
      $datos = $result->current();
      return $datos;
   }                   
   // Listado de motivos de contratacion
   public function getMotivosContra()
   {
      $result=$this->adapter->query("select * from t_motivos_contra order by nombre",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }               
   // Invitados capacitacion
   public function getInvCap($con)
   {
      $result=$this->adapter->query("select * from t_sol_cap_i where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                       
   // Listado de evaluadores de descargos
   public function getEvaDescar($can)
   {
      $result=$this->adapter->query("Select a.id, b.id as idEmp , b.CedEmp, b.nombre, b.apellido, c.nombre as nomCar,
                                     d.nombre as nomCcos  
                                     from t_eva_descar a 
                                     inner join a_empleados b on b.id = a.idEmp 
                                     inner join t_cargos c on c.id = b.idCar
                                     inner join n_cencostos d on d.id = b.idCcos ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      //$datos = $result->current();
      return $datos;
   }   
   // Listado de terceros
   public function getTerceros($con)
   {
      $result=$this->adapter->query("select a.codigo, b.id, 
                   case when b.central = 1 then concat( a.nombre, ' ', '(Principal)') else b.nombre end as nombre  
                   from n_terceros a
                      inner join n_terceros_s b on b.idTer = a.id 
                      where a.id>0  ".$con." 
                      order by a.nombre ",Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
    }               
   // Elementos de un reporte
   public function getEleRep($con)
   {
      $result=$this->adapter->query("select * from i_constructor_ele where id>0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                              
   // Menu 3
   public function getMenRepor($con)
   {
      $result=$this->adapter->query("select a.*, b.nombre as nomC2, 
           c.nombre as nomC3, d.nombre as nomC4  
           from c_mu3 a 
           inner join c_mu2 b on b.id = a.idM2
           inner join c_mu1 c on c.id = b.idM1
           inner join c_mu d on d.id = c.idM 
           where d.id != 4 and a.repor = 1 
           order by d.nombre, c.nombre, b.nombre, a.nombre".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                 
   
   // Composicion del repore
   public function getConRepor($con, $id)
   {
      $result=$this->adapter->query("select b.id, b.nombre, c.tipo from c_mu3 a 
                                      inner join i_constructor b on b.idOm = a.id 
                                      left join i_constructor_ele c on c.idCon = b.id 
                                      where a.id = ".$id."  
                                      group by c.tipo".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                   
   // Listado de cuentas
   public function getCuentas($con)
   {
      $result=$this->adapter->query("select * from n_plan_cuentas order by codigo".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                                                      
   // Listado de proviciones
   public function getProviciones($con)
   {
        //  1 then 'Cesantias' 
        //  2 then 'Intereses' 
	//  3 then 'Primas' 
	//  4 then 'Vacaciones' 
	//  5 then 'Salud' 
	//  6 then 'Pensiones' 
	//  7 then 'Caja de compensación' 
	//  8 then 'Sena' 
	//  9 then 'Icbf'
	//  10 then 'Riesgos profesionales'         
      $result=$this->adapter->query("select *, (porc/100) as por, 
                                        case nombre when 1 then 'Cesantias' 
                                        when 2 then 'Intereses' 
					when 3 then 'Primas' 
					when 4 then 'Vacaciones' 
					when 5 then 'Salud' 
					when 6 then 'Pensiones' 
					when 7 then 'Caja de compensación' 
					when 8 then 'Sena' 
					when 9 then 'Icbf'
					when 10 then 'Riesgos profesionales' end as con 
                                        from n_proviciones  
                                        where id > 0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->current();
      return $datos;
   }                                                         
   // Tarifas Arl
   public function getTarifas($con)
   {
      $result=$this->adapter->query("select * from n_tarifas where estado=0 ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                      
   // Codigos de enfermedades
   public function getCodEnf($con)
   {
      $result=$this->adapter->query("select * from n_cod_enf ".$con ,Adapter::QUERY_MODE_EXECUTE);
      $datos=$result->toArray();
      return $datos;
   }                         
}

