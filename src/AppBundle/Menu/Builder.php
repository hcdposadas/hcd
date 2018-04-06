<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface {
	use ContainerAwareTrait;

	public function mainMenu( FactoryInterface $factory, array $options ) {
//		$menu = $factory->createItem('root');
//
//		$menu->addChild('Home', array('route' => 'app_homepage'));

		$menu = $factory->createItem(
			'root',
			array(
				'childrenAttributes' => array(
					'class' => 'sidebar-menu',
				),
			)
		);

		$menu->addChild(
			'MENU PRINCIPAL'
		)->setAttribute( 'class', 'header' );

		if ( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_MESA_ENTRADA' ) ) {

			$keyEmpresa = 'MESA ENTRADA';
			$menu->addChild(
				$keyEmpresa,
				array(
					'childrenAttributes' => array(
						'class' => 'treeview-menu',
					),
				)
			)
			     ->setUri( '#' )
			     ->setExtra( 'icon', 'fa fa-exchange' )
			     ->setAttribute( 'class', 'treeview' );

//			$menu[ $keyEmpresa ]
//				->addChild(
//					'Expedientes',
//					array(
//						'route' => 'expediente_index',
//					)
//				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Imprimir Proyecto',
					array(
						'route' => 'expediente_impresion_proyecto',
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Asingar Nº Expte',
					array(
						'route' => 'expediente_asignar_numero',
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Expedientes Legislativos',
					array(
						'route' => 'expedientes_legislativos_index',
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Expedientes Administrativos',
					array(
						'route' => 'expedientes_administrativos_index',
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Expedientes Legislativos Externos',
					array(
						'route' => 'expediente_legislativo_externo_index',
					)
				);

			$menu[ $keyEmpresa ]
				->addChild(
					'Expedientes Administrativos Externos',
					array(
						'route' => 'expediente_administrativo_externo_index',
					)
				);

			//Dependencia
			$menu[ $keyEmpresa ]
				->addChild(
					'Dependencias',
					array(
						'route' => 'dependencia_index',
					)
				);

		}
		if ( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_PERSONAL' ) ) {

			$keyPersonal = 'PERSONAL';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'treeview-menu',
					),
				)
			)
			     ->setUri( '#' )
			     ->setExtra( 'icon', 'fa fa-users' )
			     ->setAttribute( 'class', 'treeview' );
			$menu[ $keyPersonal ]
				->addChild(
					'Personas',
					array(
						'route' => 'persona_index',
					)
				);
		}

		if ( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' ) ) {

			$keyPersonal = 'PROYECTOS';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'treeview-menu',
					),
				)
			)
			     ->setUri( '#' )
			     ->setExtra( 'icon', 'fa fa-folder-open' )
			     ->setAttribute( 'class', 'treeview' );
			$menu[ $keyPersonal ]
				->addChild(
					'Listado',
					array(
						'route' => 'proyectos_index',
					)
				);
			$menu[ $keyPersonal ]
				->addChild(
					'Nuevo Proyecto',
					array(
						'route' => 'proyecto_new',
					)
				);
		}

		if ( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) ) {

			$keyPersonal = 'PROYECTOS';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'treeview-menu',
					),
				)
			)
			     ->setUri( '#' )
			     ->setExtra( 'icon', 'fa fa-folder-open' )
			     ->setAttribute( 'class', 'treeview' );

			$menu[ $keyPersonal ]
				->addChild(
					'Listado',
					array(
						'route' => 'expedientes_legislativos_index',
					)
				);
		}

		if ( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) || $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) ) {
			$keyPersonal = 'SESIONES';
			$menu->addChild(
				$keyPersonal,
				array(
					'childrenAttributes' => array(
						'class' => 'treeview-menu',
					),
				)
			)
			     ->setUri( '#' )
			     ->setExtra( 'icon', 'fa fa-file-text-o' )
			     ->setAttribute( 'class', 'treeview' );

			$menu[ $keyPersonal ]
				->addChild(
					'Conformar Plan de Labor',
					array(
						'route' => 'sesiones_index',
					)
				);

			$menu[ $keyPersonal ]
				->addChild(
					'Listado',
					array(
						'route' => 'sesiones_index',
					)
				);
		}


		$keyPersonal = 'DOCUMENTOS';
		$menu->addChild(
			$keyPersonal,
			array(
				'childrenAttributes' => array(
					'class' => 'treeview-menu',
				),
			)
		)
		     ->setUri( '#' )
		     ->setExtra( 'icon', 'fa fa-file-text-o' )
		     ->setAttribute( 'class', 'treeview' );
		$menu[ $keyPersonal ]
			->addChild(
				'Carta Orgánica',
				array(
					'route' => 'documento_carta_organica',
				)
			);

//		$menu[ $keyPersonal ]
//			->addChild(
//				'Reglamento Interno',
//				array(
//					'route' => 'documento_reglamento_interno',
//				)
//			);

//		if ( $this->container->get( 'security.authorization_checker' )->isGranted( 'ROLE_EMPRESA' ) ) {
//
//			$keyEmpresa = 'EMPRESA';
//			$menu->addChild(
//				$keyEmpresa,
//				array(
//					'childrenAttributes' => array(
//						'class' => 'treeview-menu',
//					),
//				)
//			)
//			     ->setUri( '#' )
//			     ->setExtra( 'icon', 'fa fa-building' )
//			     ->setAttribute( 'class', 'treeview' );
//
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.profile',
//						array(),
//						'app' ),
//					array(
//						'route' => 'empresa_perfil',
//					)
//				);
//
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.nuevo_evento',
//						array(),
//						'app' ),
//					array(
//						'route' => 'evento_new',
//					)
//				);
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.nueva_oferta',
//						array(),
//						'app' ),
//					array(
//						'route' => 'oferta_new',
//					)
//				);
//
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.noticias_recientes',
//						array(),
//						'app' ),
//					array(
//						'route' => 'noticias_index',
//					)
//				);
//
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.publicaciones_recientes',
//						array(),
//						'app' ),
//					array(
//						'route' => 'publicaciones_index',
//					)
//				);
//
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.noticias_internas',
//						array(),
//						'app' ),
//					array(
//						'route' => 'noticia_interna_index',
//					)
//				);
//
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.suscripcion',
//						array(),
//						'app' ),
//					array(
//						'route' => 'plan_empresa_index',
//					)
//				);
//
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.promo_calendario',
//						array(),
//						'app' ),
//					array(
//						'route' => 'promocion_calendario_index',
//					)
//				);
//
//			$menu[ $keyEmpresa ]
//				->addChild(
//					$this->container->get( 'translator' )->trans( 'menu.empresa.comentarios',
//						array(),
//						'app' ),
//					array(
//						'route' => 'comentarios_index',
//					)
//				);
//
//		}

		return $menu;
	}
}