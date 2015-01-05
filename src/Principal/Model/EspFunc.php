<?php
/*
 * FUNCIONES DE NOMINA NISSI
 * 
 */
namespace Principal\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

use Principal\Model\AlbumTable; 

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

//
/// INDICE

//// FUNCIONES BASICAS ------------------------------------------
// 0. DATOS DEL PC Y USUARIO ACTIVO

class EspFunc extends AbstractTableGateway
{      
   protected $table  = '';  
   // Url actual
   public function getUrl()
   {
       $url = $_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];       
       
       // Obtener el id de la cadena , que sera el codigo del id del informe
       //echo $_SERVER['REQUEST_URI'].'<br />';
       $pIdr = strripos( $_SERVER['REQUEST_URI'] , '/');
       $idRepor = substr( $_SERVER['REQUEST_URI'] , $pIdr+1 ,100 ) ; // Obtener el id del reporte              
       
       $ruta = array("server" => $_SERVER['HTTP_HOST'],
                     "puerto" => $_SERVER['SERVER_PORT'],
                     "rutaC"  => $_SERVER['REQUEST_URI'],
                     "ruta"   => $_SERVER['REQUEST_URI'],
                     "para"   => $idRepor,
                    );       
       return $ruta ;
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
//      $options   = new SmtpOptions(array(
    //'name' => 'localhost.localdomain',
 //   'host' => 'localhost',
 //   'port' => 25,
//));
        $options   = new SmtpOptions(array(
             'host'              => 'smtp.gmail.com',
             'connection_class'  => 'login',
             'connection_config' => array(
             'ssl'       => 'ssl', // tls
             'username' => 'wilsonmet8@gmail.com',
         'password' => 'junior19247'
        ),
        'port' => 587,
       ));
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
                
}