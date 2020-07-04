<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class Builder {

	private $factory;
	private $authorizationChecker;

	/**
	 * @param FactoryInterface $factory
	 *
	 * Add any other dependency you need
	 */
	public function __construct( FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker ) {
		$this->factory              = $factory;
		$this->authorizationChecker = $authorizationChecker;
	}

	public function mainMenu( array $options ) {
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
		)->setAttribute( 'class', 'nav-header' );

		if ( $this->authorizationChecker->isGranted( 'ROLE_MESA_ENTRADA' ) ) {

			$keyEmpresa = 'MESA ENTRADA';
			$menu->addChild(
				$keyEmpresa,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'fas fa-exchange-alt' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );


			$menu[ $keyEmpresa ]
				->addChild(
					'Imprimir Proyecto',
					array(
						'route'          => 'expediente_impresion_proyecto',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Asingar Nº Expte',
					array(
						'route'          => 'expediente_asignar_numero',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Expedientes Legislativos',
					array(
						'route'          => 'expedientes_legislativos_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Expedientes Administrativos',
					array(
						'route'          => 'expedientes_administrativos_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Expedientes Legislativos Externos',
					array(
						'route'          => 'expediente_legislativo_externo_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Expedientes Administrativos Externos',
					array(
						'route'          => 'expediente_administrativo_externo_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);

			//Dependencia
			$menu[ $keyEmpresa ]
				->addChild(
					'Dependencias',
					array(
						'route'          => 'dependencia_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);


		}
		// Decretos

		if ( $this->authorizationChecker->isGranted( 'ROLE_MESA_ENTRADA' ) ||
		     $this->authorizationChecker->isGranted( 'ROLE_SECRETARIO' ) ) {
			$keyDecretos = 'Decretos';
			$menu->addChild(
				$keyDecretos,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'fas fa-file-alt' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );

			$menu[ $keyDecretos ]
				->addChild(
					'Listado',
					array(
						'route'          => 'decreto_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
		}

		if ( $this->authorizationChecker->isGranted( 'ROLE_PERSONAL' ) ) {

			$keyPersonal = 'PERSONAL';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'fa fa-users' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );
			$menu[ $keyPersonal ]
				->addChild(
					'Personas',
					array(
						'route'          => 'persona_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ],
					)
				);
		}

		if ( $this->authorizationChecker->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->authorizationChecker->isGranted( 'ROLE_DEFENSOR' ) ) {

			$keyPersonal = 'PROYECTOS';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'fa fa-folder-open' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );
			$menu[ $keyPersonal ]
				->addChild(
					'Mis Proyectos',
					array(
						'route'          => 'proyectos_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
			$menu[ $keyPersonal ]
				->addChild(
					'Nuevo Proyecto',
					array(
						'route'          => 'proyecto_new',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
			$menu[ $keyPersonal ]
				->addChild(
					'Otros Proyectos',
					array(
						'route'          => 'expedientes_legislativos_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
		}

		if ( $this->authorizationChecker->isGranted( 'ROLE_LEGISLATIVO' ) ) {

			$keyPersonal = 'PROYECTOS';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'fa fa-folder-open' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );

			$menu[ $keyPersonal ]
				->addChild(
					'Listado',
					array(
						'route'          => 'expedientes_legislativos_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);


		}

		if ( $this->authorizationChecker->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->authorizationChecker->isGranted( 'ROLE_LEGISLATIVO' ) ) {

			$keyDictamenes = 'DICTÁMENES';
			$menu->addChild(
				$keyDictamenes,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'far fa-file' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );

			$menu[ $keyDictamenes ]
				->addChild(
					'Listado',
					array(
						'route'          => 'dictamen_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
		}

		if ( $this->authorizationChecker->isGranted( 'ROLE_LEGISLATIVO' ) &&
		     ! $this->authorizationChecker->isGranted( 'ROLE_SECRETARIO' )
		) {
			$menu[ $keyDictamenes ]
				->addChild(
					'Crear Dictamen',
					array(
						'route'          => 'dictamen_alta',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
			$menu[ $keyDictamenes ]
				->addChild(
					'Crear Dictamen a Expte Existente',
					array(
						'route'          => 'dictamen_asignar_a_expte',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
		}

		if ( $this->authorizationChecker->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->authorizationChecker->isGranted( 'ROLE_LEGISLATIVO' ) ||
		     $this->authorizationChecker->isGranted( 'ROLE_DEFENSOR' ) ||
		     $this->authorizationChecker->isGranted( 'ROLE_MESA_ENTRADA' ) ) {
			$keyPersonal = 'SESIONES';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'far fa-file-alt' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );

			if ( $this->authorizationChecker->isGranted( 'ROLE_LEGISLATIVO' ) &&
			     ! $this->authorizationChecker->isGranted( 'ROLE_SECRETARIO' ) ) {
				$menu[ $keyPersonal ]
					->addChild(
						'Conformar Plan de Labor',
						array(
							'route'          => 'sesiones_conformar_plan_de_labor_index',
							'attributes'     => [ 'class' => 'nav-item' ],
							'linkAttributes' => [ 'class' => 'nav-link' ]
						)
					);
			}
			$menu[ $keyPersonal ]
				->addChild(
					'Listado',
					array(
						'route'          => 'sesiones_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
			if ( $this->authorizationChecker->isGranted( 'ROLE_LEGISLATIVO' ) ) {
				$menu[ $keyPersonal ]
					->addChild(
						'Incorporar Expedientes en Sesión',
						[
							'route'          => 'incorporar_expedientes_a_sesion_index',
							'attributes'     => [ 'class' => 'nav-item' ],
							'linkAttributes' => [ 'class' => 'nav-link' ]
						]
					);
				$menu[ $keyPersonal ]
					->addChild(
						'Incorporar Dictamen en Sesión',
						[
							'route' => 'incorporar_dictamenes_en_sesion_index',
							'attributes'     => [ 'class' => 'nav-item' ],
							'linkAttributes' => [ 'class' => 'nav-link' ]
						]
					);
			}
		}

		if ( $this->authorizationChecker->isGranted( 'ROLE_CEREMONIAL' ) ) {
			$keyCeremonial = 'CEREMONIAL';
			$menu->addChild(
				$keyCeremonial,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'fa fa-folder-open' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );

			$menu[ $keyCeremonial ]
				->addChild(
					'Homenajes',
					array(
						'route'          => 'sesiones_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
		}


		if ( $this->authorizationChecker->isGranted( 'ROLE_ADMINISTRACION' ) ) {
			$keyAdministracion = 'ADMINISTRACION';
			$menu->addChild(
				$keyAdministracion,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'fa fa-folder-open' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );

			$menu[ $keyAdministracion ]
				->addChild(
					'Ordenes de pago',
					array(
						'route'          => 'orden_de_pago_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
		}

		if ( $this->authorizationChecker->isGranted( 'ROLE_LEGISLATIVO' ) ) {

			$textosDefinitivos = 'TEXTOS DEFINITIVOS';
			$menu->addChild(
				$textosDefinitivos,
				array(
					'childrenAttributes' => array(
						'class' => 'nav nav-treeview',
					),
				)
			)
			     ->setUri( '#' )
			     ->setLinkAttribute( 'class', 'nav-link' )
			     ->setExtra( 'icon', 'fa fa-folder-open' )
			     ->setAttribute( 'class', 'nav-item has-treeview' );
			$menu[ $textosDefinitivos ]
				->addChild(
					'Textos definitivos',
					array(
						'route'          => 'texto_definitivo_index',
						'attributes'     => [ 'class' => 'nav-item' ],
						'linkAttributes' => [ 'class' => 'nav-link' ]
					)
				);
		}

		$keyPersonal = 'DOCUMENTOS';
		$menu->addChild(
			$keyPersonal,
			array(
				'childrenAttributes' => array(
					'class' => 'nav nav-treeview',
				),
			)
		)
		     ->setUri( '#' )
		     ->setLinkAttribute( 'class', 'nav-link' )
		     ->setExtra( 'icon', 'far fa-file-alt' )
		     ->setAttribute( 'class', 'nav-item has-treeview' );
		$menu[ $keyPersonal ]
			->addChild(
				'Carta Orgánica',
				array(
					'route'          => 'documento_carta_organica',
					'attributes'     => [ 'class' => 'nav-item' ],
					'linkAttributes' => [ 'class' => 'nav-link' ]
				)
			);

		return $menu;
	}
}