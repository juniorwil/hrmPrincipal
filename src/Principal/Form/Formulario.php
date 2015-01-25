<?php
/**
 * Standar formularios NISSI
 * @copyright 2013
 */
namespace Principal\Form;

use Zend\Captcha; 
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Factory;

// CAMPOS GENERICOS *-----------------------------------------------------
// FECHA DOCUMENTO
// FECHA INICIAL
// FECHA FINAL 
// ID GENERICO
// ID GENERICO 2
// ID GENERICO 3
// ID GENERICO 4
// CEDULA
// NOMBRE GENERICO
// TELEFONOS
// CODIGO
// DIRECCION GENERICO
// TIPO GENERICO
// TIPO GENERICO MULTIPLE
// TIPO GENERICO 2
// TIPO GENERICO 3
// NUMERO GENERICO
// VALOR
// VALOR SEPARADOR DE MILES
// VALOR MATRICES 
// COMENTARIOS
// COMENTARIOS SIN EDITOR
// ENVIAR GENERICO
// AGREGAR ITEMS GENERICO
// AGREGAR NOVEDAD
// GENERAR REPORTE
// CERRAR PROCESO
// GENERAR NOMINA
// VALOR O FORMULA
// BUSCAR 
// CHECK 1
// CHECK 2
// CHECK 3
// GENERAR MINI
// LINK
// ROLES
// PASSWORD 1
// PASSWORD 2
// SEXO
// ESTADO CIVIL
// EMAIL
// SUBIR ARCHIVO
// ENVIAR ARCHIVO 
// VALDIACION DEL PERIODO A GUARDAR
 
       
// CAMPOS DIRECTOS *-----------------------------------------------------

// NOMBRE 1
// NOMBRE 2
// APELLIDO 1
// APELLIDO 2
// OCUPACION 
// DENOMINACION
// NIVEL DEL ASPECTO
// CARGOS
// RESPONSABILIDADES
// MISION
// LISTADO DE DEPARTAMENTOS
// LISTADO DE DEPARTAMENTOS MULTI 
// LISTADO DE GRUPO DE DOTACIONES
// SEDES
// ESTADO INICIAL DE DOCUMENTOS
// CERRAR EMPLEADOS
// SUELDO
// ID SALUD
// ID PENSION
// ID ARP
// ID CESANTIAS
// ID CAJA DE COMPENSACION
// GRUPOS
// GRUPOS MULTIPLE
// SUBGRUPOS
// CALENDARIO DE NOMINA
// CALENDARIO DE NOMINA MULTIPLE
// CENTRO DE COSTO
// CENTRO DE COSTO BUSCAR
// CENTRO DE COSTOS MULTI
// FONDO DE APORTES VOLUNTARIOS
// FONDO DE APORTES AFC
// HORA
// FORMULA
// TIPO DE AUTOMATICOS
// TIPO DE AUTOMATICOS 2
// TIPO DE AUTOMATICOS 3
// TIPO DE AUTOMATICOS 4
// CONCEPTOS
// CONCEPTOS MUTIPLES
// TIPOS DE NOMINA
// TIPOS DE NOMINA MULTIPLE
// CHECK HORAS 
// FECHA INICIO CONTRATO
// TIPO DE EMPLEADO
// ALIAS 
// VALORES EN VARIABLE 
// BUSCAR EMPLEADO
// OPCION DE MODULO (MENUES)
// DIAS
// DEVENGADO
// DEDUCIDO
// AUSENTISMO
// INCAPACIDADES
// NUMERO CUOTAS
// CUOTAS
// FECHA DE INGRESO
// MESES
// SEXO
// NIT
// TIPOS DE FONDOS
// CAMPOS CONSULTAS
// COLUMNAS
// FILTROS
// DETALLE CONS
// DIAS PAGO VACACIONES
// DIAS PAGO VACACIONES PENDIENTE       
// TIPOS DE PRESTAMOS
// ESCALA SALARIAL DEL CARGO
// NIVEL DE ESTUDIOS
// TIPO DE SANGRE
// AREA DE CAPACITACIONES
// ESTATURA
// INSTITUCION
// PARENTESCO
// BANCOS
// FORMA DE PAGO

class Formulario extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('album');
        $this->setAttribute('method', 'post');   
        $this->setAttribute('enctype','multipart/form-data');
        
        // CAMPOS GENERICOS --------------------------------------------------------------------------------
        // ID GENERICO
        $this->add(array(
            'name' => 'id',            
            'attributes' => array(
                'type'  => 'hidden',
                'id'   => 'id',
            ),
        ));        
        // ID GENERICO 2
        $this->add(array(
            'name' => 'id2',            
            'attributes' => array(
                'type'  => 'hidden',
                'id'   => 'id2',
            ),
        ));                
        // ID GENERICO 3
        $this->add(array(
            'name' => 'id3',            
            'attributes' => array(
                'type'  => 'hidden',
                'id'   => 'id3',
            ),
        ));                        
        // ID GENERICO 4
        $this->add(array(
            'name' => 'id4',            
            'attributes' => array(
                'type'  => 'hidden',
                'id'   => 'id4',
            ),            
        ));        
        // CEDULA
        $this->add(array(
            'name' => 'cedula',            
            'attributes' => array(
                'type'  => 'text',
                'id'   => 'cedula',
                'required'  => 'required',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Cedula'
            ),                        
        ));                
        // FECHA DOCUMENTO
        $this->add(array(
            'name' => 'fecDoc',            
            'attributes' => array(
                'type'  => 'Date',
                'id'   => 'fecDoc',     
                'required'  => 'required',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Fecha del documento'
            ),            
        ));  
        // VALDIACION DEL PERIODO A GUARDAR
        $this->add(array(
            'name' => 'verPer',            
            'attributes' => array(
                'type'  => 'text',
                'id'   => 'verPer',
            ),
            'options' => array(
                'label' => 'Cedula'
            ),                        
        ));                
        // FECHA INICIO
        $this->add(array(
            'name' => 'fechaIni',            
            'attributes' => array(
                'type'  => 'Date',
                'requerid'  => 'requerid',
                'id'   => 'fechaIni',
                'class'   => 'form-control',
            ),
            'options' => array(
                'label' => 'Dese el:'
            ),            
        ));          
        // FECHA FIN
        $this->add(array(
            'name' => 'fechaFin',            
            'attributes' => array(
                'type'  => 'Date',
                'requerid'  => 'requerid',
                'id'   => 'fechaFin',
                'class'   => 'form-control',
            ),
            'options' => array(
                'label' => 'Hasta el:'
            ),            
        ));                  
        // NUMERO GENERICO
        $this->add(array(
            'name' => 'numero',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Numero',
                'id'    => 'numero'
            ),
        ));        
        // NUMERO GENERICO 1
        $this->add(array(
            'name' => 'numero1',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Numero',
                'id'    => 'numero'
            ),
        ));        
        // NUMERO GENERICO 2
        $this->add(array(
            'name' => 'numero2',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Numero',
                'id'    => 'numero'
            ),
        ));        

        // AÑOS
        $this->add(array(
            'name' => 'ano',
            'attributes' => array(
                'type'  => 'numeric',
            ),
            'options' => array(
                'label' => '',
                'id'    => 'ano'
            ),
        ));                
        // VALOR
        $this->add(array(
            'name' => 'valor',
            'attributes' => array(
                'type'   => 'text',
                'id'     => 'valor',
                'class'  => 'span3',
            ),
            'options' => array(
                'label' => 'Numero',
            ),
        ));                
        // VALOR SEPARADOR DE MILES
        $this->add(array(
            'name' => 'valorS',
            'attributes' => array(
                'type'   => 'numeric',
                'id'     => 'valorS',
            ),
            'options' => array(
                'label' => 'Valor',
            ),
        ));                        
        // NOMBRE GENERICO
        $this->add(array(
            'name' => 'nombre',
            'attributes' => array(
                'type'  => 'text',
                'required'  => 'required',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Nombre',
            ),
        ));                
        // NOMBRE GENERICO 2
        $this->add(array(
            'name' => 'nombre2',
            'attributes' => array(
                'type'  => 'text',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Nombre',
            ),
        ));                        
        // TELEFONOS
        $this->add(array(
            'name' => 'telefonos',
            'attributes' => array(
                'type'  => 'text',
                'required'  => 'required',
                'class'    => 'form-control',
            ),
            'options' => array(
                'label' => 'Telefonos',
            ),
        ));                        
        // TELEFONOS 2
        $this->add(array(
            'name' => 'telefonos2',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Telefonos',
            ),
        ));                                
        // CODIGO
        $this->add(array(
            'name' => 'codigo',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Codigo de referencia',
            ),
        ));                        
        // DIRECCION GENERICO
        $this->add(array(
            'name' => 'dir',
            'attributes' => array(
                'type'  => 'text',
                'required'  => 'required',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Direccion',                
            ),
        ));
        // DIRECCION GENERICO
        $this->add(array(
            'name' => 'dir2',
            'attributes' => array(
                'type'  => 'text',
                'required'  => 'required',
            ),
            'options' => array(
                'label' => 'Direccion',
            ),
        ));        
        // TIPO GENERICO 
        $select = new Element\Select('tipo');
        $select->setLabel('Tipo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'tipo');
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione...'); 
        $this->add($select);              
        // TIPO GENERICO 1
        $select = new Element\Select('tipo1');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $this->add($select);                      
        // TIPO GENERICO 2
        $select = new Element\Select('tipo2');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $this->add($select);                         
        // TIPO GENERICO 3
        $select = new Element\Select('tipo3');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $this->add($select);                                  
        // TIPO GENERICO MULTIPLE
        $select = new Element\Select('tipoM');
        $select->setLabel('Tipo');
        $select->setAttribute('multiple', true);
        $select->setAttribute('id', 'tipoM');
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                      

        // OPCION DE MODULO (MENUES)        
        $select = new Element\Select('idM');
        $select->setLabel('Modulo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'idM');
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione opcion...'); // Agregar en el controlador las opciones
        $this->add($select);                      
        
        // TIPO INFORMES
        $select = new Element\Select('tipoI');
        $select->setLabel('Modulo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'tipoI');
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione opcion...'); // Agregar en el controlador las opciones
        $this->add($select);                              
        
        // COMENTARIOS
        $this->add(array( 
            'name' => 'comen', 
            'type' => 'textarea', 
            'attributes' => array( 
                'class'    => 'ckeditor',
                'id'       => 'comen',
            ), 
            'options' => array( 
                'label' => 'Comentarios',
            ), 
        ));                         
        // COMENTARIOS SIN EDITOR
        $this->add(array( 
            'name' => 'comenN', 
            'type' => 'textarea', 
            'attributes' => array( 
                'class'    => 'form-control',
                'id'       => 'comenN', 
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                 
        // COMENTARIOS SIN EDITOR 2
        $this->add(array( 
            'name' => 'comenN2', 
            'type' => 'textarea', 
            'attributes' => array( 
                'required' => 'required', 
                'class'    => 'form-control',
                'id'       => 'comenN2', 
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                         
        // COMENTARIOS SIN EDITOR 3
        $this->add(array( 
            'name' => 'comenN3', 
            'type' => 'textarea', 
            'attributes' => array( 
                'required' => 'required', 
                'class'    => 'span4',
                'id'       => 'comenN3', 
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                                 
        // ENVIAR GENERICO
        $this->add(array(
            'name' => 'send',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Actualizar datos',
                'id'    => 'submitbutton',
                'class' => 'btn btn-info'
            ),
        ));
        // ORDENAR
        $this->add(array(
            'name' => 'orden',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Ordenar',
                'id'    => 'submitbutton',
                'class' => 'btn btn-sm btn-danger'
            ),
        ));        
        // CONTRATAR
        $this->add(array(
            'name' => 'contratar',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'CONTRATAR',
                'id'    => 'submitbutton',
                'class' => 'btn btn-sm btn-danger'
            ),
        ));                
        // AGREGAR ITEMS GENERICO
        $this->add(array(
            'name' => 'agregar',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Agregar items',
                'id' => 'submitbutton',
                'class' => 'btn btn-purple'
            ),
        ));
        // GENERAR REPORTE        
        $this->add(array(
            'name' => 'reporte',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Generar consulta',
                'id' => 'reporte',
                'class' => 'btn btn-white btn-info btn-bold'
            ),
        ));         
        // GENERAR NOMINA
        $this->add(array(
            'name' => 'GenerarN',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Generar nomina',
                'id' => 'generarnom',
                'class' => 'btn btn-purple'
            ),
        ));                 
        // GENERAR PLANILLA
        $this->add(array(
            'name' => 'GenerarP',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Generar planilla',
                'id' => 'generarnom',
                'class' => 'btn btn-purple'
            ),
        ));                         
        // AGREGAR NOVEDAD
        $this->add(array(
            'name' => 'agregarnov',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Agregar novedad',
                'id' => 'submitbutton',
                'class' => 'btn btn-mini btn-danger'
            ),
        ));        
        // GENERAR MINI
        $this->add(array(
            'name' => 'generarM',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Generar',
                'id' => 'generarnom',
                'class' => 'btn btn-mini btn-info'
            ),
        ));                         
        // BUSCAR
        $this->add(array(
            'name' => 'buscar',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Buscar',
                'id' => 'submitbutton',
                'class' => 'btn btn-white btn-info btn-bold'
            ),
        ));        
        // BUSCAR 2
        $this->add(array(
            'name' => 'agregar2',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Agregar',
                'id' => 'agregar2',
                'class' => 'btn btn-white btn-info btn-bold'
            ),
        ));        
        // GUARDAR
        $this->add(array(
            'name' => 'guardar',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Guardar',
                'id' => 'guardar',
                'class' => 'btn btn-white btn-info btn-bold'
            ),
        ));        
        // CERRAR PROCESO
        $this->add(array(
            'name' => 'cerrar',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Cerrar proceso',
                'id' => 'submitbutton',
                'class' => 'btn btn-danger'
            ),
        ));        
        // CERRAR EMPLEADOS
        $this->add(array(
            'name' => 'crear',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Crear empleado',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary'
            ),
        ));   
        // CHECK 1
        $this->add(array(
           'type' => 'Checkbox',
           'name' => 'check1',
           'attributes' => array('id'=>'check', 'class' => 'check' ), 
           'options' => array(
              'label' => 'A checkbox',
              'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
           )
        ));                
        // CHECK 2
        $this->add(array(
           'type' => 'Checkbox',
           'name' => 'check2',
           'attributes' => array('id'=>'check2', 'class' => 'check2' ), 
           'options' => array(
              'label' => 'A checkbox',
              'use_hidden_element' => true,
               'checked_value' => '1',
               'unchecked_value' => '0'
           )
        ));                
        // CHECK 3
        $this->add(array(
           'type' => 'Checkbox',
           'name' => 'check3',
           'options' => array(
              'label' => 'A checkbox',
              'use_hidden_element' => true,
               'checked_value' => '1',
               'unchecked_value' => '0'
           )
        ));    
        // CHECK 4
        $this->add(array(
           'type' => 'Checkbox',
           'name' => 'check4',
           'options' => array(
              'label' => 'A checkbox',
              'use_hidden_element' => true,
               'checked_value' => '1',
               'unchecked_value' => '0'
           )
        ));            
        // LINK
        $this->add(array( 
            'name' => 'link', 
            'type' => 'textarea', 
            'attributes' => array( 
                'required' => 'required', 
            ), 
            'options' => array( 
                'label' => 'Link',
            ), 
        ));                                 
        // PASWORD 1
        $this->add(array(
            'name' => 'clave1',            
            'attributes' => array(
                'type'  => 'password',
                'id'   => 'clave',
            ),
            'options' => array(
                'label' => 'Clave'
            ),                        
        ));                
        // PASWORD 2
        $this->add(array(
            'name' => 'clave2',            
            'attributes' => array(
                'type'  => 'password',
                'id'   => 'clave2',
            ),
            'options' => array(
                'label' => 'Confirmar clave'
            ),                        
        ));                        
        // EMAIL
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
                'class'    => 'form-control',
            ),
            'options' => array(
                'label' => 'e-mail',
            ),
        ));                        
        // Subir archivo
        $file = new Element\File('image-file');
        $file->setLabel('-')
             ->setAttribute('id', 'image-file');
        $this->add($file);
        
        // ENVIAR ARCHIVO
        $this->add(array(
            'name' => 'enviarArchivo',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Enviar',
                'id' => 'enviarArchivo',
                'class' => 'btn btn-warning'
            ),
        ));                
        
        // CAMPOS UNICOS --------------------------------------------------------------------------------

        // NOMBRE 1
        $this->add(array(
            'name' => 'nombre1',
            'attributes' => array(
                'type'  => 'text',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Primer nombre',
            ),
        ));                        
        // NOMBRE 2
        $this->add(array(
            'name' => 'nombre2',
            'attributes' => array(
                'type'  => 'text',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Segundo nombre',
            ),
        ));                          
        // APELLIDO 1
        $this->add(array(
            'name' => 'apellido1',
            'attributes' => array(
                'type'  => 'text',
                'required'  => 'required',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Primer apellido',
            ),
        ));                                
        // APELLIDO 2
        $this->add(array(
            'name' => 'apellido2',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Segundo apellido',
            ),
        ));        
        // OCUPACION 
        $this->add(array(
            'name' => 'ocupacion',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => '',
            ),
        ));                
        // SUELDO
        $this->add(array(
            'name' => 'sueldo',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Sueldo',
            ),
        ));                
        // UBICACION
        $select = new Element\Select('ubicacion');
        $select->setLabel('Ubicación');
        $select->setAttribute('multiple', false);
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);
        
        // DENOMINACION
        $this->add(array(
            'name' => 'deno',
            'attributes' => array(
                'type'  => 'text',
                'class'    => 'form-control'
            ),
            'options' => array(
                'label' => 'Denominación',
            ),
        ));        
        // RESPONSABILIDADES
        $this->add(array(
            'name' => 'respo',
            'attributes' => array(
                'type'  => 'textarea',
                'class'    => 'ckeditor'
            ),
            'options' => array(
                'label' => 'Responsabilidades',
            ),
        ));                
        // MISION
        $this->add(array(
            'name' => 'mision',
            'attributes' => array(
                'type'  => 'textarea',
                'class'    => 'ckeditor'
            ),
            'options' => array(
                'label' => 'Misión',
            ),
        ));                        
        // CARGO
        $select = new Element\Select('idCar');
        $select->setLabel('Cargo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");        
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);
        // VARIOS CARGO
        $select = new Element\Select('idCarM');
        $select->setLabel('Cargo');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");        
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);
        
        // NIVEL DE ASPECTOS
        $select = new Element\Select('idNasp');
        $select->setLabel('Nivel de aspectos');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                     
        // NIVEL DEL CARGO
        $select = new Element\Select('idNcar');
        $select->setLabel('Nivel del cargo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                     
        // LISTADO DE DEPARTAMENTOS
        $select = new Element\Select('idDep');
        $select->setLabel('Departamento');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'idDep');
        $select->setAttribute('class', "chosen-select"); 
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                           
        // LISTADO DE DEPARTAMENTOS MULTI
        $select = new Element\Select('idDepM');
        $select->setLabel('Departamento');
        $select->setAttribute('multiple', true);
        $select->setAttribute('id', 'idDepM');
        $select->setAttribute('class', "chosen-select"); 
        $this->add($select);                           
        // LISTADO DE GRUPO DE DOTACIONES
        $select = new Element\Select('idGdot');
        $select->setLabel('Grupo de dotaciones');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('No aplica...'); // Agregar en el controlador las opciones
        $this->add($select);                    
        // LISTADO DE GRUPO DE DOTACIONES SIN FORMATO
        $select = new Element\Select('idGdot2');
        $select->setLabel('Grupo de dotaciones');
        $select->setAttribute('multiple', false);
        $select->setEmptyOption('Seleccione ...'); // Agregar en el controlador las opciones
        $this->add($select);                            
        // SEDES
        $select = new Element\Select('idSed');
        $select->setLabel('Sede');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                            
        // ID SALUD
        $select = new Element\Select('idSal');
        $select->setLabel('Salud');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);
        // ID PENSION
        $select = new Element\Select('idPen');
        $select->setLabel('Pensión');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);
        // ID ARP
        $select = new Element\Select('idArp');
        $select->setLabel('ARP');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);        
        // ID CESANTIAS
        $select = new Element\Select('idCes');
        $select->setLabel('Cesantias');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                        
        // ID CAJA DE COMPENSACION
        $select = new Element\Select('idCaja');
        $select->setLabel('Caja de compensacion familiar');
        $select->setAttribute('class', "chosen-select"); 
        $select->setAttribute('multiple', false);
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                
        // GRUPOS
        $select = new Element\Select('idGrupo');
        $select->setLabel('Grupo de nomina');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione un grupo...'); // Agregar en el controlador las opciones
        $this->add($select);                
        // LINEA
        $select = new Element\Select('idLin');
        $select->setLabel('Grupo de nomina');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione una linea...'); // Agregar en el controlador las opciones
        $this->add($select);                        
        // GRUPOS MULTIPLE
        $select = new Element\Select('idGrupoM');
        $select->setLabel('Grupos de nomina');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', 'idGrupoM');
        $this->add($select);                        
        // SUBGRUPOS
        $select = new Element\Select('idSubgrupo');
        $select->setLabel('Sub grupo de nomina');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                
        // CALENDARIO DE NOMINA
        $select = new Element\Select('idCal');
        $select->setLabel('Calendario de nomina');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', "idCal");
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);
        // CALENDARIO DE NOMINA MULTIPLE
        $select = new Element\Select('idCalM');
        $select->setLabel('Calendarios de nomina');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                                
        // CENTRO DE COSTO
        $select = new Element\Select('idCencos');
        $select->setLabel('');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");        
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                  
        // CENTRO DE COSTO 
        $select = new Element\Select('idCcos');
        $select->setLabel('');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");        
        $this->add($select);                                          
		
        $select = new Element\Select('idCcosM1');
        $select->setLabel('');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");      
        $this->add($select);                                    		
        $select = new Element\Select('idCcosM2');
        $select->setLabel('');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");      
        $this->add($select);                                    		

        // CENTRO DE COSTO VARIOS
        $select = new Element\Select('idCcosM');
        $select->setLabel('');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");        
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                          
        
        // CENTRO DE COSTO SIMPLRE
        $select = new Element\Select('idCencosS');
        $select->setLabel('');
        $select->setAttribute('multiple', false);
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                          

        
        // CENTRO DE COSTO BUSCAR
        $select = new Element\Select('idCcosB');
        $select->setLabel('Centro de costo');
        $select->setAttribute('class', "chosen-select");        
        $select->setAttribute('multiple', false);
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);              
        
        // CENTRO DE COSTO MULTI
        $select = new Element\Select('idCcosM');
        $select->setLabel('Centro de costo');
        $select->setAttribute('class', "chosen-select");        
        $select->setAttribute('id', "idCcosM");        
        $select->setAttribute('multiple', true);
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);              
        
        
        // FONDO DE APORTES VOLUNTARIOS
        $select = new Element\Select('idFav');
        $select->setLabel('Fondo de aportes voluntario');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setAttribute('requerid', "requerid"); 
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                                        
        // FONDO DE APORTES AFC
        $select = new Element\Select('idFafc');
        $select->setLabel('Fondo de aportes AFC');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                                                
        // PREFIJOS
        $select = new Element\Select('idPrej');
        $select->setLabel('Prefijo contable');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                                                        
        // ESTADO INICIAL DE DOCUMENTOS       
        $select = new Element\Select('estado');
        $select->setLabel('Estado del documento');
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', "estado");
        //$select->setValueOptions($val); // Agregar en el controlador las opciones        
        $this->add($select);   
        // VALIDAR ITEMS LISTA DE CHEQUEO
        $this->add(array(
            'name' => 'validar',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Validar',
                'id' => 'submitbutton',
                'class' => 'btn btn-success'
            ),
        ));        
        // VALOR O FORMULA
        $this->add(array(
            'name' => 'formula',
            'attributes' => array(
                'class'    => 'form-control',                
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Formula o valor',
            ),
        ));                
        // HORA
        $this->add(array(
            'name' => 'hora',
            'attributes' => array(
                'type'  => 'time',
                'id'    => 'hora',
                'class'   => 'form-control',
            ),
            'options' => array(
                'label' => '',
            ),
        ));        
        // HORA 2
        $this->add(array(
            'name' => 'hora2',
            'attributes' => array(
                'type'  => 'time',
                'id'    => 'hora2',
                'class'   => 'form-control',
            ),
            'options' => array(
                'label' => '',
            ),
        ));                
        // HORA
        $this->add(array(
            'name' => 'horaG',
            'attributes' => array(
                'type'  => 'numeric',
                'id'    => 'horaG',
                'class'   => 'form-control',
            ),
            'options' => array(
                'label' => '',
            ),
        ));                
        // FORMULA
        $select = new Element\Select('idFor');
        $select->setLabel('Formulas contables');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                                                         
        // TIPO DE AUTOMATICOS
        $select = new Element\Select('idTau');
        $select->setLabel('Tipo de automatico');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones       
        $this->add($select);                     
        // TIPO DE AUTOMATICOS 2
        $select = new Element\Select('idTau2');
        $select->setLabel('Tipo de automatico 2');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Opcional...'); // Agregar en el controlador las opciones       
        $this->add($select);                     
        // TIPO DE AUTOMATICOS 3
        $select = new Element\Select('idTau3');
        $select->setLabel('Tipo de automatico 3');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Opcional...'); // Agregar en el controlador las opciones       
        $this->add($select);                             
        // TIPO DE AUTOMATICOS 4
        $select = new Element\Select('idTau4');
        $select->setLabel('Tipo de automatico 4');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Opcional...'); // Agregar en el controlador las opciones       
        $this->add($select);                                     
        // CONCEPTOS
        $select = new Element\Select('idConc');
        $select->setLabel('Conceptos');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "idConc");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                             
        // CONCEPTOS MUTIPLES
        $select = new Element\Select('idConcM');
        $select->setLabel('Conceptos');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "idConcM");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones        
        $this->add($select); 
        $select = new Element\Select('idConcM2');
        $select->setLabel('Conceptos');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "idConcM2");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones        
        $this->add($select);         
        // TIPOS DE NOMINA
        $select = new Element\Select('idTnom');
        $select->setLabel('Conceptos');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "idTnom");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                                     
        // TIPOS DE NOMINA MULTIPLE
        $select = new Element\Select('idTnomm');
        $select->setLabel('Conceptos');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);           
        // CHECK HORAS  
        $this->add(array( 
            'name' => 'horasC', 
            'type' => 'Zend\Form\Element\MultiCheckbox', 
            'attributes' => array( 
                'required' => 'required', 
                'value' => '0', 
            ), 
            'options' => array( 
                'label' => 'Checkboxes Label', 
                'value_options' => array(
                    '0' => 'debo', 
                ),
            ), 
        )); 
        // FECHA INICIO CONTRATO
        $this->add(array(
            'name' => 'fecCon',            
            'attributes' => array(
                'type'  => 'text',
                'id'   => 'id',
                'required'  => 'required',
            ),
            'options' => array(
                'label' => 'Fecha inicio de labores'
            ),            
        ));  
        // FECHA ULTIMO PAGO DE NOMINA
        $this->add(array(
            'name' => 'fecPnom',            
            'attributes' => array(
                'type'  => 'text',
                'id'   => 'id',
                'class'   => 'form-control',
            ),
            'options' => array(
                'label' => 'Fecha inicio de labores'
            ),            
        ));          
        // FECHA ULTIMO PAGO DE VACACIONES
        $this->add(array(
            'name' => 'fecPvac',            
            'attributes' => array(
                'type'  => 'text',
                'id'   => 'id',
                'class'   => 'form-control',
            ),
            'options' => array(
                'label' => 'Fecha inicio de labores'
            ),            
        ));                
     
        // TIPO DE EMPLEADO
        $select = new Element\Select('idTemp');
        $select->setLabel('Conceptos');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                   

        // TIPO DE EMPLEADO MULTIPLEX
        $select = new Element\Select('idTempM');
        $select->setLabel('Conceptos');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                           
        
        // ALIAS
        $this->add(array(
            'name' => 'alias',
            'attributes' => array(
                'type'  => 'text',
                'size'  => '4',
                'class' => 'span10'
            ),
            'options' => array(
                'label' => 'Alias',
            ),
        ));         
        // VALORES EN VARIABLE 
        $this->add(array( 
            'name' => 'valorvar', 
            'type' => 'textarea', 
            'attributes' => array( 
                'required' => 'required', 
                'class'    => 'ckeditor'
            ), 
            'options' => array( 
                'label' => 'Valor',
            ), 
        ));                         
        // BUSCAR EMPLEADO
        $select = new Element\Select('idEmp');
        $select->setLabel('Empleado');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "idEmp");
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                   
        
        // BUSCAR EMPLEADO MULTI
        $select = new Element\Select('idEmpM');
        $select->setLabel('Empleados');
        $select->setAttribute('multiple', true);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "idEmpM");
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);   
        
        // BUSCAR EMPLEADO MULTI
        $select = new Element\Select('idEmp2');
        $select->setLabel('Empleados');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "idEmp2");
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);   
        
        // NUMERO CUOTAS
        $select = new Element\Select('cuotas');
        $select->setLabel('Numero de cuotas');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "cuotas");
        $select->setValueOptions(array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5',
                                       '6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10',
                                       '11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15',
                                       '16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20',
                        '21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27',
            '28'=>'28','29'=>'29','30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35',
            '36'=>'36','37'=>'37','38'=>'38','39'=>'39','40'=>'40','41'=>'41','42'=>'42','43'=>'43',
            '44'=>'44','45'=>'45','46'=>'46','47'=>'47','48'=>'48','49'=>'49','50'=>'51','52'=>'52','53'=>'54',
            '55'=>'55','56'=>'56','57'=>'57','58'=>'58','59'=>'59','60'=>'60','61'=>'61','62'=>'62','63'=>'63',
            '64'=>'64','65'=>'65','66'=>'66','67'=>'67','68'=>'68','69'=>'69','70'=>'70','71'=>'71','72'=>'72',
            '73'=>'73','74'=>'74','75'=>'75','76'=>'76','77'=>'77','78'=>'78','79'=>'79','80'=>'80','81'=>'81','82'=>'82',
            '83'=>'83','84'=>'84','85'=>'85','86'=>'86','87'=>'87','88'=>'88','89'=>'89','90'=>'90','91'=>'91','92'=>'92',
            '93'=>'93','94'=>'94','95'=>'95','96'=>'96','97'=>'97','98'=>'98','99'=>'99','100'=>'100','101'=>'101','102'=>'102',
            '100'=>'100','101'=>'101','102'=>'102','103'=>'103','104'=>'104','105'=>'105','106'=>'106','107'=>'107','108'=>'108',
            '109'=>'109','110'=>'110','111'=>'111','112'=>'112','113'=>'113','114'=>'114','115'=>'115','116'=>'116','117'=>'117',
            '118'=>'118','119'=>'119','120'=>'120','121'=>'121','122'=>'122','123'=>'123','124'=>'124','125'=>'125','126'=>'126',
            '127'=>'127','128'=>'128','129'=>'129','130'=>'130','131'=>'131','132'=>'132','133'=>'133','134'=>'134','135'=>'135',
            '136'=>'136','137'=>'137','138'=>'138','139'=>'139','140'=>'140','141'=>'141','142'=>'142','143'=>'143','144'=>'144',
            '145'=>'145','146'=>'146','147'=>'147','148'=>'148','149'=>'149','150'=>'150','151'=>'151','152'=>'152','153'=>'153',
            '154'=>'154','155'=>'155','156'=>'156','157'=>'157','158'=>'158','159'=>'159','160'=>'160','161'=>'161','162'=>'162',
            '163'=>'163','164'=>'164','165'=>'165','166'=>'166','167'=>'167','168'=>'168','169'=>'169','170'=>'170','171'=>'171',
            '172'=>'172','173'=>'173','174'=>'174','175'=>'175','176'=>'176','177'=>'177','178'=>'178','179'=>'179','180'=>'180',
            '181'=>'181','182'=>'182','183'=>'183','184'=>'184','185'=>'185','186'=>'186','187'=>'187','188'=>'188','189'=>'189',
            '190'=>'190','191'=>'191','192'=>'192','193'=>'193','194'=>'194','195'=>'195','196'=>'196','197'=>'197','198'=>'198',
            '199'=>'199','200'=>'200','201'=>'201','202'=>'202','203'=>'203','204'=>'204','205'=>'205','206'=>'206','207'=>'207',
            '208'=>'208','209'=>'209','210'=>'210','211'=>'211','212'=>'212','213'=>'213','214'=>'214','215'=>'215','216'=>'216',
            '217'=>'217','218'=>'218','219'=>'219','220'=>'220','221'=>'221','222'=>'222','223'=>'223','224'=>'224','225'=>'225',
            '226'=>'226','227'=>'227','228'=>'228','229'=>'229','230'=>'230','231'=>'231','232'=>'232','233'=>'233','234'=>'234',
            '235'=>'235','236'=>'236','237'=>'237','238'=>'238','239'=>'239','240'=>'240','241'=>'241','242'=>'242','243'=>'243',
            '244'=>'244','245'=>'245','246'=>'246','247'=>'247','248'=>'248','249'=>'249','250'=>'250','251'=>'251','252'=>'252',
            '253'=>'253','254'=>'254','255'=>'255','256'=>'256','257'=>'257','258'=>'258','259'=>'259','260'=>'260','261'=>'261',
            '262'=>'262','263'=>'263','264'=>'264','265'=>'265','266'=>'266','267'=>'267','268'=>'268','269'=>'269','270'=>'270',
            '271'=>'271','272'=>'272','273'=>'273','274'=>'274','275'=>'275','276'=>'276','277'=>'277','278'=>'278','279'=>'279',
            '280'=>'280','281'=>'281','282'=>'282','283'=>'283','284'=>'284','285'=>'285','286'=>'286','287'=>'287','288'=>'288',
            '289'=>'289','290'=>'290','291'=>'291','292'=>'292','293'=>'293','294'=>'294','295'=>'295','296'=>'296','297'=>'297',
            '298'=>'298','299'=>'299','300'=>'300','301'=>'301','303'=>'303','304'=>'304','305'=>'305','306'=>'306','307'=>'307',
            '308'=>'308','309'=>'309','310'=>'310','311'=>'311','312'=>'312','313'=>'313','314'=>'314','315'=>'315',
            '316'=>'316','317'=>'317','318'=>'318','319'=>'319','320'=>'320','321'=>'321','322'=>'322','323'=>'323','324'=>'324',
            '325'=>'325','326'=>'326','327'=>'327','328'=>'328','329'=>'329','330'=>'330','321'=>'331','332'=>'332','333'=>'333',
            '334'=>'334','335'=>'335','336'=>'336','337'=>'337','338'=>'338','339'=>'339','340'=>'340','341'=>'341','342'=>'342',
            '343'=>'343','344'=>'344','345'=>'345','346'=>'346','347'=>'347','348'=>'348','349'=>'349','350'=>'350','351'=>'351') ); // Agregar en el controlador las opciones
        $this->add($select);     
        // PERIODOS
        $select = new Element\Select('periodo');
        $select->setLabel('Numero de cuotas');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "periodo");
        $select->setValueOptions(array('0'=>'Todos' , '1'=>'1','2'=>'2') ); 
        $this->add($select);        
        		
        // CUOTAS
        $this->add(array( 
              'name' => 'vcuotas', 
              'type' => 'text', 
              'attributes'  => array( 
                  'class'   => 'vcuotas'
              ), 
              'options' => array( 
                 'label' => '',
              ), 
        ));                                  
        // CUOTAS DIGITADAS
        $this->add(array( 
              'name' => 'ncuotas', 
              'type' => 'text', 
              'attributes'  => array( 
                  'class'   => 'numeric'
              ), 
              'options' => array( 
                 'label' => '',
              ), 
        ));                                          
        // ROLES
        $select = new Element\Select('idRol');
        $select->setLabel('Roles');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $this->add($select);                     
        
         // DIAS
        $this->add(array(
            'name' => 'dias',
            'attributes' => array(
                'type'  => 'text',
                'size'  => '2',
                'class' => 'col-sm-2',
                'id'    => 'dias'
            ),
            'options' => array(
                'label' => '',
            ),
        ));               
        // DEVENGADOS
        $this->add(array(
            'name' => 'devengado',
            'attributes' => array(
                'type'  => 'text',
                'size'  => '2',
                'class' => 'devengados'
            ),
            'options' => array(
                'label' => '',
            ),
        ));                       
        // DEDUCIDOS
        $this->add(array(
            'name' => 'deducido',
            'attributes' => array(
                'type'  => 'text',
                'size'  => '2',
                'class' => 'deducidos'
            ),
            'options' => array(
                'label' => '',
            ),
        ));                               
        // AUSENTISMO
        $select = new Element\Select('idAus');
        $select->setLabel('Ausentismo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $this->add($select);                             
        
        // INCAPACIDADES
        $select = new Element\Select('idInc');
        $select->setLabel('Incapacidades');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'idInc');
        $select->setAttribute('class', "chosen-select");
        $this->add($select);                             
 
        // FECHA DE INGRESO
        $this->add(array(
            'name' => 'fecIng',            
            'attributes' => array(
                'type'  => 'Date',
                'id'   => 'id',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Fecha de ingreso'
            ),            
        ));  
        // FECHA DE INGRESO 2
        $this->add(array(
            'name' => 'fecIng2',            
            'attributes' => array(
                'type'  => 'Date',
                'id'   => 'fecIng2',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Fecha de ingreso'
            ),            
        ));  

         // MESES
        $select = new Element\Select('meses');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "meses");
        $select->setValueOptions(array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo',
                                       '06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre'
                                      ,'11'=>'Noviembre','12'=>'Diciembre')); // Agregar en el controlador las opciones
        $this->add($select);     
 
        // SEXO
        $select = new Element\Select('sexo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "sexo");
        $select->setValueOptions(array('1'=>'Masculino','2'=>'Femenino')); // Agregar en el controlador las opciones
        $this->add($select);     
        // SEXO 2
        $select = new Element\Select('sexo2');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "sexo2");
        $select->setValueOptions(array('1'=>'Masculino','2'=>'Femenino')); // Agregar en el controlador las opciones
        $this->add($select);             
        // ESTADO CIVIL
        $select = new Element\Select('estCivil');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "estCivil");
        $select->setValueOptions(array('1'=>'Soltero(a)','2'=>'Casado(a)','3'=>'Divorciado(a)','4'=>'Viudo(a)')); // Agregar en el controlador las opciones
        $this->add($select);             
         // NIT
        $this->add(array(
            'name' => 'nit',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'NIT',
            ),
        ));                              
        // TIPOS DE FONDOS
        $select = new Element\Select('idTfonM');
        $select->setLabel('Tipo de fondo');
        $select->setAttribute('multiple', true);
        $select->setAttribute('id', 'idTfonM');
        $select->setAttribute('class', "chosen-select");
        $select->setValueOptions(array('1'=>'EPS','2'=>'Pensión','3'=>'ARP','4'=>'Cesantias','5'=>'Caja de compensación'));       
        $this->add($select);
        
        // CAMPOS CONSULTAS 2
        $this->add(array( 
            'name' => 'comen2', 
            'type' => 'textarea', 
            'attributes' => array( 
                'required' => 'required', 
                'rows'   => 100,                 
                'cols'   => 200,             
                'class'  => 'form-control'
            ), 
            'options' => array( 
                'label'  => 'Función',                
            ), 
        ));                         
        // COLUMNAS
        $this->add(array( 
            'name' => 'columnas', 
            'type' => 'textarea', 
            'attributes' => array( 
                'rows'   => 100,                 
                'cols'   => 200,             
                'class'  => 'span7'
            ), 
            'options' => array( 
                'label'  => 'Columnas',                
            ), 
        ));                                 
        // FILTROS 
        $this->add(array( 
            'name' => 'filtros', 
            'type' => 'textarea', 
            'attributes' => array( 
                'rows'   => 100,                 
                'cols'   => 200,             
                'class'  => 'span4'
            ), 
            'options' => array( 
                'label'  => 'Filtros',                
            ), 
        ));                                 
        // DETALLE CONS
        $this->add(array( 
            'name' => 'detalleC', 
            'type' => 'textarea', 
            'attributes' => array( 
                'required' => 'required', 
                'rows'   => 100,                 
                'cols'   => 200,             
                'class'  => 'form-control'
            ), 
            'options' => array( 
                'label'  => 'Cuerpo',                
            ), 
        ));                                 
        // DIAS PAGO VACACIONES       
         $this->add(array(
            'name' => 'diasVac',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'span5',
            ),
            'options' => array(
                'label' => '',
            ),
        ));               
        // DIAS PAGO VACACIONES PENDIENTE       
         $this->add(array(
            'name' => 'diasVacP',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'span6',
            ),
            'options' => array(
                'label' => '',
            ),
        ));                        
                     
        // Tipos de prestamos
        $select = new Element\Select('idTpres');
        $select->setLabel('Tipo dev prestamo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione el tipo de prestamo...'); // Agregar en el controlador las opciones
        $this->add($select);                          
        // VALOR MATRICES
        $this->add(array(
            'name' => 'valorM',
            'attributes' => array(
                'type'   => 'text',
                'id'     => 'valor',
            ),
            'options' => array(
                'label' => 'Numero',
            ),
        ));                
        // ESCALA SALARIAL DEL CARGO
        $select = new Element\Select('idEsal');
        $select->setLabel('Escala salarial');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        //$select->setEmptyOption('Seleccione salario...'); // Agregar en el controlador las opciones
        $this->add($select);                          
        

        // NIVEL DE ESTUDIOS
        $select = new Element\Select('idNest');
        $select->setLabel('Nivel de estudios');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'idNest');
        $select->setAttribute('class', "chosen-select"); 
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                

        // NIVEL DE ESTUDIOS 2
        $select = new Element\Select('idNest2');
        $select->setLabel('Nivel de estudios');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'idNest2');
        $select->setAttribute('class', "chosen-select"); 
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                
        
        // TIPO DE SANGRE   
        $select = new Element\Select('sangre');
        $select->setLabel('Tipo de sangre');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', "estado");
        $select->setValueOptions(array("0"=>"O-", "1"=>"O+", "2"=>"A-", "3"=>"A+", "4"=>"B-", "3"=>"B+", "5"=>"AB-", "6"=>"AB+")); // Agregar en el controlador las opciones        
        $this->add($select);
        // AREA DE CAPACITACIONES        
        $select = new Element\Select('idArea');
        $select->setLabel('Area de capacitación');
        $select->setAttribute('class', "chosen-select"); 
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', "estado");
        //$select->setValueOptions(array("0"=>"O-", "1"=>"O+", "2"=>"A-", "3"=>"A+", "4"=>"B-", "3"=>"B+", "5"=>"AB-", "6"=>"AB+")); // Agregar en el controlador las opciones        
        $this->add($select);        
        

        // ESTADO FISICO
        $this->add(array(
            'name' => 'estatura',
            'attributes' => array(
                'type'   => 'number',
                'id'     => 'estatura',
                'required' => 'required',
                "pattern",'[0-9]+',
                "step",'any',
                "min",'1',                  
            ),
            'options' => array(
                'label' => 'Estatura',
            ),  
        ));            
        // ESTADO FISICO
        $this->add(array(
            'name' => 'estatura',
            'attributes' => array(
                'type'   => 'number',
                'id'     => 'estatura',
                'required' => 'required',
                "pattern",'[0-9]+',
                "step",'any',
                "min",'1',                  
            ),
            'options' => array(
                'label' => 'Estatura',
            ),  
        ));                
        // ALERGIAS
        $this->add(array( 
            'name' => 'alergias', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'alergias',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                      
        // OPERACIONES
        $this->add(array( 
            'name' => 'operaciones', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'operaciones',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                      
        // ENFERMEDADES
        $this->add(array( 
            'name' => 'enfermedades', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'enfermedades',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                              
        // LIMITACION FISICA
        $this->add(array( 
            'name' => 'limitacion', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'limitacion',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                                      
        // LIMITACION FISICA 2
        $this->add(array( 
            'name' => 'limitacion2', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'limitacion',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                                              
        // LENTES
        $this->add(array(
           'type' => 'Checkbox',
           'name' => 'lentes',
           'attributes' => array('id'=>'check', 'class' => 'check' ), 
           'options' => array(
              'label' => 'A checkbox',
              'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
           )
        ));                
        // LENTES 2
        $this->add(array(
           'type' => 'Checkbox',
           'name' => 'lentes2',
           'attributes' => array('id'=>'check', 'class' => 'check' ), 
           'options' => array(
              'label' => 'A checkbox',
              'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
           )
        ));                        
        // FUMA
        $this->add(array(
           'type' => 'Checkbox',
           'name' => 'fuma',
           'attributes' => array('id'=>'check', 'class' => 'check' ), 
           'options' => array(
              'label' => 'A checkbox',
              'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
           )
        ));                
        // BEBE
        $this->add(array(
           'type' => 'Checkbox',
           'name' => 'bebe',
           'attributes' => array('id'=>'check', 'class' => 'check' ), 
           'options' => array(
              'label' => 'A checkbox',
              'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
           )
        ));                
        // DEPORTES
        $this->add(array( 
            'name' => 'deportes', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'deportes',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                              
        // CLUB SOCIAL
        $this->add(array( 
            'name' => 'clubSocial', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'clubSocial',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                              
        // LIBROS
        $this->add(array( 
            'name' => 'libros', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'libros',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                                      
        // MUSICA
        $this->add(array( 
            'name' => 'musica', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'musica',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                                      
        // OTRAS ACTIVIADES
        $this->add(array( 
            'name' => 'otrasAct', 
            'type' => 'textarea', 
            'attributes' => array( 
                'id'       => 'otrasAct',
            ), 
            'options' => array( 
                'label' => '',
            ), 
        ));                                                      
        // INSTITUCION
        $this->add(array( 
            'name' => 'instituto', 
            'type' => 'textarea', 
            'attributes' => array( 
                'required' => 'required', 
            ), 
            'options' => array( 
                'label'  => '',                
            ), 
        ));    
        // PARENTESCO
        $select = new Element\Select('parentesco');
        $select->setLabel('Parentesco');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', "estado");
        $select->setValueOptions(array("1"=>"Mama", "2"=>"Papa", "3"=>"Esposo(a)", "4"=>"Hijo(a)", "5"=>"Abuelo(a)" )); // Agregar en el controlador las opciones        
        $this->add($select);
        
        // BANCOS
        $select = new Element\Select('idBanco');
        $select->setLabel('Seleccione el banco');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', "idBanco");
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones        
        $this->add($select);        
 
        
        // FORMA DE PAGO
        $select = new Element\Select('formaPago');
        $select->setLabel('Forma de pago');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'formaPago');
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione...'); 
        $select->setValueOptions(array("1"=>"Efectivo", "2"=>"Cheque", "3"=>"Transferencia" )); // Agregar en el controlador las opciones                
        $this->add($select);                      
        
        // NUMERO DE CUENTA
        $this->add(array(
            'name' => 'numCuenta',
            'attributes' => array(
                'type'  => 'text',
                'class'  => 'form-control',
            ),
            'options' => array(
                'label' => 'Denominación',
            ),
        ));               
        // ENTIDAD
        $select = new Element\Select('idEnt');
        $select->setLabel('Entidad');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select");
        $select->setAttribute('id', "idEnt");
        //$select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                    
        // MOTIVOS DE CONTRATACION
        $select = new Element\Select('idMot');
        $select->setLabel('Motivo de contratación');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);       
        
         // TERCEROS
        $select = new Element\Select('idTer');
        $select->setLabel('Tercero');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'Tercero');
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione...'); 
        $this->add($select);                
		
         // TERCEROS MULTIPLE
        $select = new Element\Select('idTerM');
        $select->setLabel('Tercero');
        $select->setAttribute('multiple', true);
        $select->setAttribute('id', 'Terceros');
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione...'); 
        $this->add($select);                		
        
        // CODIGO DE CUENTA
        $select = new Element\Select('codCta');
        $select->setLabel('Codigo de cuenta');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'codCta');
        $select->setAttribute('class', "chosen-select");
        $select->setEmptyOption('Seleccione...'); 
        $this->add($select);                
        
        // NATURALEZA DE LA CUENTA
        $select = new Element\Select('natCta');
        $select->setLabel('Naturaleza de cuenta');
        $select->setAttribute('multiple', false);
        $select->setAttribute('id', 'natCta');
        $select->setAttribute('class', "chosen-select");
        $select->setValueOptions(array("0" => "Debito", "1" => "Credito"));                         
        $this->add($select);                        
        // ID SALARIO
        $select = new Element\Select('idSalario');
        $select->setLabel('Sueldo');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);
        // TARIFAS ARL
        $select = new Element\Select('idTar');
        $select->setLabel('Tarifa');
        $select->setAttribute('multiple', false);
        $select->setAttribute('class', "chosen-select"); 
        $select->setEmptyOption('Seleccione...'); // Agregar en el controlador las opciones
        $this->add($select);                             
        
        
        $this->add(array( 
            'name' => 'csrf', 
            'type' => 'Zend\Form\Element\Csrf', 
        ));              
    }// Fin funcion    
}