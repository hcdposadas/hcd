<?php

namespace App\Menu;

use App\Entity\Configuracion;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class Builder
{

	private $factory;
	private $authorizationChecker;
	private $em;

	/**
	 * @param FactoryInterface $factory
	 *
	 * Add any other dependency you need
	 */
	public function __construct(
		FactoryInterface $factory,
		AuthorizationCheckerInterface $authorizationChecker,
		EntityManagerInterface $em
	) {
		$this->factory              = $factory;
		$this->authorizationChecker = $authorizationChecker;
		$this->em                   = $em;
	}

	public function mainMenu(array $options)
	{
		//		$menu = $factory->createItem('root');
		//
		//		$menu->addChild('Home', array('route' => 'app_homepage'));


		$menu = $this->factory->createItem(
			'root',
			array(
				'childrenAttributes' => array(
					'class'          => 'nav nav-pills nav-sidebar flex-column',
					'data-widget'    => 'treeview',
					'data-accordion' => 'false',
					'role'           => 'menu'
				),
			)
		);

		$menu->addChild(
			'MENU PRINCIPAL'
		)->setAttribute('class', 'nav-header');

		if (
			$this->authorizationChecker->isGranted('ROLE_MESA_ENTRADA') ||
			$this->authorizationChecker->isGranted('ROLE_LEGISLATIVO') ||
			$this->authorizationChecker->isGranted('ROLE_BIBLIOTECA')  ||
			$this->authorizationChecker->isGranted('ROLE_PROSECRETARIO')

		) {

			$keyEmpresa = 'MESA ENTRADA';
			$menu->addChild(
				$keyEmpresa,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fas fa-exchange-alt')
				->setAttribute('class', 'nav-item has-treeview');
				// if ($this->authorizationChecker->isGranted('ROLE_SECRETARIO')) {
				// 	$menu[$keyEmpresa]
				// 	->addChild(
				// 		'Expedientes Secretaria',
				// 		array(
				// 			'route'          => 'expedientes_administrativos_secretario_index',
				// 			'attributes'     => ['class' => 'nav-item'],
				// 			'linkAttributes' => ['class' => 'nav-link']
				// 		)
				// 	);
				// }
			if ($this->authorizationChecker->isGranted('ROLE_MESA_ENTRADA')) {

				$menu[$keyEmpresa]
					->addChild(
						'Imprimir Proyecto',
						array(
							'route'          => 'expediente_impresion_proyecto',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);

				$menu[$keyEmpresa]
					->addChild(
						'Asingar Nº Expte',
						array(
							'route'          => 'expediente_asignar_numero',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
				$menu[$keyEmpresa]
					->addChild(
						'Bloquear Nº Expte',
						array(
							'route'          => 'expedientes_bloqueados',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
			}
			if (
				$this->authorizationChecker->isGranted('ROLE_MESA_ENTRADA') ||
				$this->authorizationChecker->isGranted('ROLE_LEGISLATIVO') ||
				$this->authorizationChecker->isGranted('ROLE_BIBLIOTECA')  ||
				 $this->authorizationChecker->isGranted('ROLE_PROSECRETARIO')

			) {

				$menu[$keyEmpresa]
					->addChild(
						'Expedientes Legislativos',
						array(
							'route'          => 'expedientes_legislativos_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
				if (
					$this->authorizationChecker->isGranted('ROLE_MESA_ENTRADA') ||
					$this->authorizationChecker->isGranted('ROLE_LEGISLATIVO') ||
                                        $this->authorizationChecker->isGranted('ROLE_PROSECRETARIO')

				) {
					$menu[$keyEmpresa]
						->addChild(
							'Expedientes Administrativos',
							array(
								'route'          => 'expedientes_administrativos_index',
								'attributes'     => ['class' => 'nav-item'],
								'linkAttributes' => ['class' => 'nav-link']
							)
						);
				}


				$menu[$keyEmpresa]
					->addChild(
						'Expedientes Legislativos Externos',
						array(
							'route'          => 'expediente_legislativo_externo_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
			}
			if ($this->authorizationChecker->isGranted('ROLE_MESA_ENTRADA')) {
				$menu[$keyEmpresa]
					->addChild(
						'Expedientes Administrativos Externos',
						array(
							'route'          => 'expediente_administrativo_externo_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);

				//Dependencia
				$menu[$keyEmpresa]
					->addChild(
						'Dependencias',
						array(
							'route'          => 'dependencia_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);

			}
		}
		// Decretos

		if (
			$this->authorizationChecker->isGranted('ROLE_MESA_ENTRADA') ||
			$this->authorizationChecker->isGranted('ROLE_SECRETARIO')
		) {
			$keyDecretos = 'Decretos';
			$menu->addChild(
				$keyDecretos,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fas fa-file-alt')
				->setAttribute('class', 'nav-item has-treeview');

			$menu[$keyDecretos]
				->addChild(
					'Listado',
					array(
						'route'          => 'decreto_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
		}

		if ($this->authorizationChecker->isGranted('ROLE_RRHH')) {

			$keyPersonal = 'Recursos Humanos';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fa fa-users')
				->setAttribute('class', 'nav-item has-treeview');
			$menu[$keyPersonal]
				->addChild(
					'Personas',
					array(
						'route'          => 'persona_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link'],
					)
				);
		}

		if ($this->authorizationChecker->isGranted('ROLE_RRMM')) {

			$keyPersonal = 'Recursos Médicos';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fa fa-users')
				->setAttribute('class', 'nav-item has-treeview');
			$menu[$keyPersonal]
				->addChild(
					'Personal',
					array(
						'route'          => 'paciente_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link'],
					)
				);
		}
		if ($this->authorizationChecker->isGranted('ROLE_LEGISLATIVO')) {

			$keyPersonal = 'PROYECTOS';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fa fa-folder-open')
				->setAttribute('class', 'nav-item has-treeview');

			$menu[$keyPersonal]
				->addChild(
					'Listado',
					array(
						'route'          => 'expedientes_legislativos_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
			
				$menu[$keyPersonal]
				->addChild(
					'Giros a comisiones',
					array(
						'route'          => 'giros_comisiones_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);

				$menu[$keyPersonal]
				->addChild(
					'Pedidos de informes',
					array(
						'route'          => 'pedidos_informe_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
				if(!$this->authorizationChecker->isGranted('ROLE_SECRETARIO')){
				$menu[$keyPersonal]
				->addChild(
					'Informes del Digesto',
					array(
						'route'          => 'digesto_informe_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);

				$menu[$keyPersonal]
				->addChild(
					'Asignación de rama y num.',
					array(
						'route'          => 'digesto_asignacion_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);}
		}
		if (
			$this->authorizationChecker->isGranted('ROLE_CONCEJAL') ||
			$this->authorizationChecker->isGranted('ROLE_DEFENSOR')
		) {

			$keyPersonal = 'PROYECTOS';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fa fa-folder-open')
				->setAttribute('class', 'nav-item has-treeview');
			$menu[$keyPersonal]
				->addChild(
					'Mis Proyectos',
					array(
						'route'          => 'proyectos_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
			$menu[$keyPersonal]
				->addChild(
					'Nuevo Proyecto',
					array(
						'route'          => 'proyecto_new',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
			$menu[$keyPersonal]
				->addChild(
					'Otros Proyectos',
					array(
						'route'          => 'expedientes_legislativos_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
		}
		if (
			$this->authorizationChecker->isGranted('ROLE_COMISION')){
				$keyComision = 'COMISIONES';
				$menu->addChild(
					$keyComision,
					array(
						'childrenAttributes' => array(
							'class' => 'nav nav-treeview',
						),
					)
				)
					->setUri('#')
					->setLinkAttribute('class', 'nav-link')
					->setExtra('icon', 'fa fa-folder-open')
					->setAttribute('class', 'nav-item has-treeview');
				$menu[$keyComision]
					->addChild(
						'Proyectos Comisión',
						array(
							'route'          => 'comision_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
				$menu[$keyComision]
					->addChild(
						'Otros Proyectos',
						array(
							'route'          => 'expedientes_legislativos_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);	
				$menu[$keyComision]
				->addChild(
					'Mis Dictámenes',
					array(
						'route'          => 'comision_dictamen_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
			}

		if (
			$this->authorizationChecker->isGranted('ROLE_CONCEJAL') ||
			$this->authorizationChecker->isGranted('ROLE_LEGISLATIVO')
			|| $this->authorizationChecker->isGranted('ROLE_COMISION')||
			$this->authorizationChecker->isGranted('ROLE_DIGESTO')
		) {

			$keyDictamenes = 'DICTÁMENES';
			$menu->addChild(
				$keyDictamenes,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'far fa-file')
				->setAttribute('class', 'nav-item has-treeview');

			$menu[$keyDictamenes]
				->addChild(
					'Listado',
					array(
						'route'          => 'dictamen_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
				if (
					$this->authorizationChecker->isGranted('ROLE_CONCEJAL') ){
				$menu[$keyDictamenes]
				->addChild(
					'Crear Dictamen a Expte Existente',
					array(
						'route'          => 'dictamen_asignar_a_expte',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
			}
				if (
					$this->authorizationChecker->isGranted('ROLE_DIGESTO')
				) {
			$menu[$keyDictamenes]
				->addChild(
					'Informes del Digesto',
					array(
						'route'          => 'digesto_informe_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);

			$menu[$keyDictamenes]
				->addChild(
					'Asignación de rama y num.',
					array(
						'route'          => 'digesto_asignacion_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
			}
		}

		if (
			$this->authorizationChecker->isGranted('ROLE_LEGISLATIVO') &&
			!$this->authorizationChecker->isGranted('ROLE_SECRETARIO')
		) {
			$menu[$keyDictamenes]
				->addChild(
					'Crear Dictamen',
					array(
						'route'          => 'dictamen_alta',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
			$menu[$keyDictamenes]
				->addChild(
					'Crear Dictamen a Expte Existente',
					array(
						'route'          => 'dictamen_asignar_a_expte',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
		}

		if (
			$this->authorizationChecker->isGranted('ROLE_CONCEJAL') ||
			$this->authorizationChecker->isGranted('ROLE_LEGISLATIVO') ||
			$this->authorizationChecker->isGranted('ROLE_DEFENSOR') ||
			$this->authorizationChecker->isGranted('ROLE_MESA_ENTRADA') ||
			$this->authorizationChecker->isGranted('ROLE_BIBLIOTECA')|| 
			$this->authorizationChecker->isGranted('ROLE_COMISION')

		) {
			$keyPersonal = 'SESIONES';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'far fa-file-alt')
				->setAttribute('class', 'nav-item has-treeview');

			if (
				$this->authorizationChecker->isGranted('ROLE_LEGISLATIVO') &&
				!$this->authorizationChecker->isGranted('ROLE_SECRETARIO') && 
				!$this->authorizationChecker->isGranted('ROLE_DIGESTO') ||
				!$this->authorizationChecker->isGranted('ROLE_SECRETARIO') && 
				!$this->authorizationChecker->isGranted('ROLE_DIGESTO')  &&
				!$this->authorizationChecker->isGranted('ROLE_COMISION') &&
				!$this->authorizationChecker->isGranted('ROLE_CONCEJAL')
			) {
				$menu[$keyPersonal]
					->addChild(
						'Conformar Plan de Labor',
						array(
							'route'          => 'sesiones_conformar_plan_de_labor_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
			}
			$menu[$keyPersonal]
				->addChild(
					'Listado',
					array(
						'route'          => 'sesiones_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
			if ($this->authorizationChecker->isGranted('ROLE_LEGISLATIVO')) {
				$menu[$keyPersonal]
					->addChild(
						'Incorporar Expedientes en Sesión',
						[
							'route'          => 'incorporar_expedientes_a_sesion_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						]
					);
				$menu[$keyPersonal]
					->addChild(
						'Incorporar Dictamen en Sesión',
						[
							'route'          => 'incorporar_dictamenes_en_sesion_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						]
					);
			}
		}

		if ($this->authorizationChecker->isGranted('ROLE_CEREMONIAL')) {
			$keyCeremonial = 'CEREMONIAL';
			$menu->addChild(
				$keyCeremonial,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fa fa-folder-open')
				->setAttribute('class', 'nav-item has-treeview');

			$menu[$keyCeremonial]
				->addChild(
					'Homenajes',
					array(
						'route'          => 'sesiones_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
		}

		if ($this->authorizationChecker->isGranted('ROLE_SECTOR')) {
			if (!$this->authorizationChecker->isGranted('ROLE_EXTERNO')) {
				$keySector = 'EXPEDIENTES';
				$menu->addChild(
					$keySector,
					array(
						'childrenAttributes' => array(
							'class' => 'nav nav-treeview',
						),
					)
				)
					->setUri('#')
					->setLinkAttribute('class', 'nav-link')
					->setExtra('icon', 'fa fa-folder-open')
					->setAttribute('class', 'nav-item has-treeview');

				$menu[$keySector]
					->addChild(
						'Mis Expedientes',
						array(
							'route'          => 'expedientes_administrativos_sector_index',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
						);
						$menu[$keySector]	->addChild(
						'Giros Enviados',
						array(
							'route'          => 'expedientes_administrativos_sector_enviados',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
						);
						$menu[$keySector]->addChild(
						'Giros Recibidos',
						array(
							'route'          => 'expedientes_administrativos_sector_recibidos',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
				}
				$keySector = 'COMUNICADOS';
				$menu->addChild(
					$keySector,
					array(
						'childrenAttributes' => array(
							'class' => 'nav nav-treeview',
						),
					)
				)
					->setUri('#')
					->setLinkAttribute('class', 'nav-link')
					->setExtra('icon', 'fa fa-folder-open')
					->setAttribute('class', 'nav-item has-treeview');
	
					$menu[$keySector]->addChild(
						'Enviados',
						array(
							'route'          => 'comunicaciones_enviadas',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
						);
						$menu[$keySector]->addChild(
						'Recibidos',
						array(
							'route'          => 'comunicaciones_recibidas',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
						);
					
				if (!$this->authorizationChecker->isGranted('ROLE_EXTERNO')) {
					$keyTicket = 'TICKETS';
					$menu->addChild(
						$keyTicket,
						array(
							'childrenAttributes' => array(
								'class' => 'nav nav-treeview',
							),
						)
					)
					->setUri('#')
					->setLinkAttribute('class', 'nav-link')
					->setExtra('icon', 'fa fa-folder-open')
					->setAttribute('class', 'nav-item has-treeview');
					$menu[$keyTicket]->addChild(
						'Tickets Enviados',
						array(
							'route'          => 'tickets_enviados',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
					$menu[$keyTicket]->addChild(
						'Tickets Recibidos',
						array(
							'route'          => 'tickets_recibidos',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
				}
		}

		if ($this->authorizationChecker->isGranted('ROLE_ADMINISTRACION')) {
			$keyAdministracion = 'ADMINISTRACION';
			$menu->addChild(
				$keyAdministracion,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fa fa-folder-open')
				->setAttribute('class', 'nav-item has-treeview');

			$menu[$keyAdministracion]
				->addChild(
					'Ordenes de pago',
					array(
						'route'          => 'orden_de_pago_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);
		}

		if (
			$this->authorizationChecker->isGranted('ROLE_LEGISLATIVO') ||
			$this->authorizationChecker->isGranted('ROLE_CONCEJAL')
		) {

			$textosDefinitivos = 'TEXTOS DEFINITIVOS';
			$menu->addChild(
				$textosDefinitivos,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fa fa-folder-open')
				->setAttribute('class', 'nav-item has-treeview');
			$menu[$textosDefinitivos]
				->addChild(
					'Textos definitivos',
					array(
						'route'          => 'texto_definitivo_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);

			//DIGESTO
			$digesto = 'DIGESTO';
			$menu->addChild(
				$digesto,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fas fa-book-open')
				->setAttribute('class', 'nav-item has-treeview');

			$configuracion = $this->em->getRepository(Configuracion::class)->findAll()[0];
			if ($configuracion) {
				$menu[$digesto]
					->addChild(
						'ConsolidaciÃ³n en curso',
						[
							'uri'            => $configuracion->getConsolidacionEnCurso(),
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link', 'target' => '_blank']
						]
					);
			}
		}

		if($this->authorizationChecker->isGranted('ROLE_SECRETARIO')){
		$keyLista = 'TICKETS';
		$menu->addChild(
			$keyLista,
			array(
				'childrenAttributes' => array(
					'class' => 'nav nav-treeview',
				),
			)
		)
			->setUri('#')
			->setLinkAttribute('class', 'nav-link')
			->setExtra('icon', 'fa fa-folder-open')
			->setAttribute('class', 'nav-item has-treeview');
		$menu[$keyLista]
			->addChild(
				'Lista de tickets',
				array(
					'route'          => 'tickets_all',
					'attributes'     => ['class' => 'nav-item'],
					'linkAttributes' => ['class' => 'nav-link']
				)
			);
$menu[$keyLista]->addChild(
                                                'Tickets Enviados',
                                                array(
                                                        'route'          => 'tickets_enviados',
                                                        'attributes'     => ['class' => 'nav-item'],
                                                        'linkAttributes' => ['class' => 'nav-link']
                                                )
                                                );
                                                $menu[$keyLista]->addChild(
                                                'Tickets Recibidos',
                                                array(
                                                        'route'          => 'tickets_recibidos',
                                                        'attributes'     => ['class' => 'nav-item'],
                                                        'linkAttributes' => ['class' => 'nav-link']
                                                )
                                                );
                                                $menu[$keyLista]->addChild(
						'Agenda',
						array(
						'route'          => 'agenda',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
						)
						);

		}

		// NO CONFORMIDADES
		// Todos los usuarios autenticados pueden ver el menú de no conformidades
		// COMENTADO TEMPORALMENTE - NO CONFORMIDADES
		/*
		if ($this->authorizationChecker->isGranted('ROLE_USER')) {
			$keyNoConformidad = 'NO CONFORMIDADES';
			$menu->addChild(
				$keyNoConformidad,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'fas fa-exclamation-triangle')
				->setAttribute('class', 'nav-item has-treeview');

			$menu[$keyNoConformidad]
				->addChild(
					'Listado',
					array(
						'route'          => 'no_conformidad_index',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);

			$menu[$keyNoConformidad]
				->addChild(
					'Nueva No Conformidad',
					array(
						'route'          => 'no_conformidad_new',
						'attributes'     => ['class' => 'nav-item'],
						'linkAttributes' => ['class' => 'nav-link']
					)
				);

			// Solo mostrar consulta avanzada para ROLE_CALIDAD o ROLE_ADMIN
			if ($this->authorizationChecker->isGranted('ROLE_CALIDAD') || 
			    $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
				$menu[$keyNoConformidad]
					->addChild(
						'Consulta Avanzada',
						array(
							'route'          => 'no_conformidad_consulta',
							'attributes'     => ['class' => 'nav-item'],
							'linkAttributes' => ['class' => 'nav-link']
						)
					);
			}

		}
		*/



$keyManual = 'MANUALES';
$menu->addChild(
	$keyManual,
	array(
		'childrenAttributes' => array(
			'class' => 'nav nav-treeview hidden',
		),
	)
)
	->setUri('#')
	->setLinkAttribute('class', 'nav-link')
	->setExtra('icon', 'far fa-file-alt')
	->setAttribute('class', 'nav-item has-treeview');

$menu[$keyManual]->addChild(
	'Misiones y Funciones',
	array(
		'route' => 'documento_manual',
		'attributes' => ['class' => 'nav-item'],
		'linkAttributes' => ['class' => 'nav-link']
	)
);
$menu[$keyManual]->addChild(
	'Técnica Legislativa',
	array(
		'route' => 'documento_manual_legis',
		'attributes' => ['class' => 'nav-item'],
		'linkAttributes' => ['class' => 'nav-link']
	)
);
$menu[$keyManual]
	->addChild(
		'Organigrama',
		array(
			'route' => 'documento_organigrama',
			'attributes' => ['class' => 'nav-item'],
			'linkAttributes' => ['class' => 'nav-link']
		)
	);

		$keyPersonal = 'NORMATIVA';
		$menu->addChild(
			$keyPersonal,
			array(
				'childrenAttributes' => array(
					'class' => 'nav nav-treeview',
				),
			)
		)
			->setUri('#')
			->setLinkAttribute('class', 'nav-link')
			->setExtra('icon', 'far fa-file-alt')
			->setAttribute('class', 'nav-item has-treeview');
		$menu[$keyPersonal]
			->addChild(
				'Carta Orgánica',
				array(
					'route'          => 'documento_carta_organica',
					'attributes'     => ['class' => 'nav-item'],
					'linkAttributes' => ['class' => 'nav-link']
				)
			);
		$menu[$keyPersonal]
			->addChild(
				'Reglamento Interno',
				array(
					'route'          => 'documento_reglamento_interno',
					'attributes'     => ['class' => 'nav-item'],
					'linkAttributes' => ['class' => 'nav-link']
				)
			);
		$menu[$keyPersonal]
			->addChild(
				'Constitución Nacional',
				array(
					'route'          => 'cn_nacional',
					'attributes'     => ['class' => 'nav-item'],
					'linkAttributes' => ['class' => 'nav-link']
				)
			);
		$menu[$keyPersonal]
			->addChild(
				'Constitución Provincial',
				array(
					'route'          => 'cn_provincial',
					'attributes'     => ['class' => 'nav-item'],
					'linkAttributes' => ['class' => 'nav-link']
				)
			);

			if($this->authorizationChecker->isGranted('ROLE_ASESOR')){

			$keyAsesor = 'EXPEDIENTES';
			$menu->addChild(
				$keyAsesor,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
				->setUri('#')
				->setLinkAttribute('class', 'nav-link')
				->setExtra('icon', 'far fa-file-alt')
				->setAttribute('class', 'nav-item has-treeview');
				$menu[$keyAsesor]
			->addChild(
				'Expedientes Administrativos',
				array(
					'route'          => 'giros_asesor',
					'attributes'     => ['class' => 'nav-item'],
					'linkAttributes' => ['class' => 'nav-link']
				)
			);
			}


		return $menu;
	}
}
