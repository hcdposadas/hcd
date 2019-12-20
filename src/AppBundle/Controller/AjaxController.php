<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BoletinAsuntoEntrado;
use AppBundle\Entity\Cargo;
use AppBundle\Entity\Dependencia;
use AppBundle\Entity\DictamenOD;
use AppBundle\Entity\OrdenDelDia;
use AppBundle\Entity\Persona;
use AppBundle\Entity\ProyectoBAE;
use AppBundle\Entity\Sesion;
use AppBundle\Form\DependenciaAjaxType;
use AppBundle\Form\PersonaType;
use Endroid\QrCode\QrCode;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use MesaEntradaBundle\Entity\AnexoExpediente;
use MesaEntradaBundle\Entity\Dictamen;
use MesaEntradaBundle\Entity\Expediente;
use MesaEntradaBundle\Entity\Giro;
use MesaEntradaBundle\Entity\IniciadorExpediente;
use MesaEntradaBundle\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UsuariosBundle\Entity\Usuario;

class AjaxController extends Controller
{
    public function getAjaxDefaultAction(Request $request)
    {
        $value = strtoupper($request->get('term'));
        //$value = $request->get('term');
        $class = $request->get('class');
        $property = $request->get('property');
        $searchMethod = $request->get('search_method');
        $em = $this->getDoctrine()->getManagerForClass($class);

        $entities = $em->getRepository($class)->$searchMethod($value);

        $json = array();

        if (!count($entities)) {
            $json[] = array(
                'label' => 'No se encontraron coincidencias',
                'value' => ''
            );
        } else {

            foreach ($entities as $entity) {
                $json[] = array(
                    'id' => $entity['id'],
                    //'label' => $entity[$property],
                    'text' => $entity[$property]
                );
            }
        }

        return new JsonResponse($json);
    }

    public function getPersonaPorDniAction(Request $request)
    {

        $em = $this->getDoctrine();

        $value = $request->get('q');
        $limit = $request->get('page_limit');
        $entities = $em->getRepository('AppBundle:Persona')->getPersonaPorDni($value, $limit, true);

        $json = array();

        if (!count($entities)) {
            $json[] = array(
                'text' => 'No se encontraron coincidencias',
                'id' => ''
            );
        } else {

            foreach ($entities as $entity) {
                $json[] = array(
                    'id' => $entity['id'],
                    //'label' => $entity[$property],
                    'text' => $entity['nombre'] . ' ' . $entity['apellido']
                );
            }
        }

        return new JsonResponse($json);
    }

    public function getCargosPorNombreAction(Request $request)
    {

        $em = $this->getDoctrine();

        $value = $request->get('q');
        $limit = $request->get('page_limit');
//		$entities = $em->getRepository( 'MesaEntradaBundle:Iniciador' )->getCargosPorNombre( $value, $limit, true );
        $entities = $em->getRepository('MesaEntradaBundle:Iniciador')->getACargosPorNombre($value, $limit);

        $json = array();

        if (!count($entities)) {
            $json[] = array(
                'text' => 'No se encontraron coincidencias',
                'id' => ''
            );
        } else {

            foreach ($entities as $entity) {
//				foreach ( $entity['cargoPersona'] as $cargoPersona ) {
                $json[] = array(
                    'id' => $entity['id'],
                    //'label' => $entity[$property],
                    'text' => $entity['cargo'] . ' ' . $entity['nombre_persona'] . ' ' . $entity['apellido_persona']
                );
//				}

            }
        }

        return new JsonResponse($json);
    }

	public function getCargosPorNombreLegacyAction(Request $request)
	{

		$em = $this->getDoctrine();

		$value = $request->get('q');
		$limit = $request->get('page_limit');

		$entities = $em->getRepository('MesaEntradaBundle:Iniciador')->getACargosPorNombreLegacy($value, $limit);

		$json = array();

		if (!count($entities)) {
			$json[] = array(
				'text' => 'No se encontraron coincidencias',
				'id' => ''
			);
		} else {

			foreach ($entities as $entity) {
				$json[] = array(
					'id' => $entity['id'],
					'text' => $entity['cargo'] . ' ' . $entity['nombre_persona'] . ' ' . $entity['apellido_persona']
				);

			}
		}

		return new JsonResponse($json);
	}

    public function formPersonaAction(Request $request)
    {

        $persona = new Persona();
        $form = $this->createForm(PersonaType::class, $persona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($persona);
            $em->flush();

            return new JsonResponse(['mensaje' => 'Persona Guardada Correctamente']);
        }
        $responseStatus = 200;
        if ($request->getMethod() == 'POST') {
            $responseStatus = 500;
        }

        $formHtml = $this->renderView('@App/Ajax/form_persona.html.twig',
            array(
                'form' => $form->createView()
            ));

        return new JsonResponse(['form' => $formHtml], $responseStatus);
    }

    public function getDependenciaPorNombreAction(Request $request)
    {
        $em = $this->getDoctrine();

        $value = $request->get('q');
        $limit = $request->get('page_limit');
        $entities = $em->getRepository('AppBundle:Dependencia')->getDependenciasPorNombre($value, $limit, true);

        $json = array();

        if (!count($entities)) {
            $json[] = array(
                'text' => 'No se encontraron coincidencias',
                'id' => ''
            );
        } else {

            foreach ($entities as $entity) {
                $json[] = array(
                    'id' => $entity['id'],
                    //'label' => $entity[$property],
                    'text' => $entity['nombre']
                );
            }
        }

        return new JsonResponse($json);
    }

    public function formDependenciaAction(Request $request)
    {

        $persona = new Dependencia();
        $form = $this->createForm(DependenciaAjaxType::class, $persona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($persona);
            $em->flush();

            return new JsonResponse(['mensaje' => 'Dependencia Guardada Correctamente']);
        }
        $responseStatus = 200;
        if ($request->getMethod() == 'POST') {
            $responseStatus = 500;
        }

        $formHtml = $this->renderView('@App/Ajax/form.html.twig',
            array(
                'form' => $form->createView()
            ));

        return new JsonResponse(['form' => $formHtml], $responseStatus);
    }

    public function getTiposProyectosAction(Request $request)
    {

        $em = $this->getDoctrine();

        $id = $request->get('id');

        $tipoProyecto = $em->getRepository('MesaEntradaBundle:TipoProyecto')->find($id);

        if (!$tipoProyecto) {
            return new JsonResponse('No se encontro el tipo de proyeto', 404);
        }

        $response = '';
        if ($tipoProyecto->getTextoPorDefecto()) {
            $response = $tipoProyecto->getTextoPorDefecto();
        }

        return new JsonResponse($response);
    }

    public function getMisCargosPorNombreAction(Request $request)
    {

        $em = $this->getDoctrine();

        $value = $request->get('q');
        $limit = $request->get('page_limit');
        $entities = $em->getRepository('MesaEntradaBundle:Iniciador')->getACargosPorNombre($value, $limit);

        $json = array();

        if (!count($entities)) {
            $json[] = array(
                'text' => 'No se encontraron coincidencias',
                'id' => ''
            );
        } else {

            foreach ($entities as $entity) {
//				foreach ( $entity['cargoPersona'] as $cargoPersona ) {
                $json[] = array(
                    'id' => $entity['id'],
                    //'label' => $entity[$property],
                    'text' => $entity['cargo'] . ' ' . $entity['nombre_persona'] . ' ' . $entity['apellido_persona']
                );
//				}

            }
        }

        return new JsonResponse($json);
    }

    public function getUltimaSesionAction()
    {

        $json = $this->getDoctrine()->getRepository('AppBundle:Sesion')->findUltimaSesion();

        return new JsonResponse($json[0]);
    }

    public function buscarExpedienteAction(Request $request)
    {

        $data = $request->get('data');
        $data = json_decode($data, true);

        if ($data['expediente']) {
            $expediente = explode(' ', $data['expediente']);
            if (count($expediente) == 3) {
                $data['expediente'] = $expediente[0];
                $data['letra'] = $expediente[1];
                $data['anio'] = $expediente[2];
            } else if (count($expediente) == 2) {
                $data['expediente'] = $expediente[0];
                $data['letra'] = $expediente[1];
            } else if (count($expediente) == 1) {
                $data['expediente'] = $expediente[0];
            }
        }

        $expedientes = $this->getDoctrine()
            ->getRepository('MesaEntradaBundle:Expediente')
            ->buscarExpedientesSesion($data);
        $expedientes = array_map([$this, 'mapExpediente'], $expedientes);

        return JsonResponse::create($expedientes);
    }

    public function enviarMailCodigoProyectoAction(Request $request)
    {

        $expedienteId = $request->get('expedienteId');
        $expediente = $this->getDoctrine()->getRepository('MesaEntradaBundle:Expediente')->find($expedienteId);

        if (!$expediente) {
            return new JsonResponse('No se encontro el tipo de proyeto', 404);
        }

        $mailer = $this->get('mailer');

        $mail = $this->getUser()->getEmail();

        $asunto = 'HCD Posadas - Código Impresión Proyecto';

        $code = new QrCode($expediente->getCodigoReferencia());
        $code->setLogoPath($this->get('kernel')->getRootDir() . '/../web/apple-touch-icon.png')
            ->setLogoWidth(50);

        $nombreAdjunto = $expediente->getId() . '.png';

        $message = (new \Swift_Message($asunto));

        $img = $message->embed(\Swift_Image::newInstance($code->writeString(),
            $nombreAdjunto,
            $code->getContentType()));

        $message
            ->setFrom($this->getParameter('mailer_sender_as'), $this->getParameter('mailer_sender'))
            ->setTo($mail)
            ->setBody(
                $this->renderView(
                    'emails/codigo_proyecto.html.twig',
                    [
                        'expediente' => $expediente,
                        'img' => $img
                    ]
                ),
                'text/html'
            );

        $mailer->send($message);

        return new JsonResponse('ok');
    }

    public function getProyectosBAEAction(Request $request)
    {
        $em = $this->getDoctrine();

        $value = $request->get('q');
        $limit = $request->get('page_limit');


        if ($value) {
            $expediente = explode(' ', $value);
            if (is_numeric($expediente[0][0])) {
                if (count($expediente) == 3) {
                    $data['expediente'] = $expediente[0];
                    $data['letra'] = $expediente[1];
                    $data['anio'] = $expediente[2];
                } else if (count($expediente) == 2) {
                    $data['expediente'] = $expediente[0];
                    $data['letra'] = $expediente[1];
                } else if (count($expediente) == 1) {
                    $data['expediente'] = $expediente[0];
                }
            } else {
                if (count($expediente) == 4) {
                    $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
                    $data['letra'] = $expediente[2];
                    $data['anio'] = $expediente[2];
                } else if (count($expediente) == 3) {
                    $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
                    $data['letra'] = $expediente[1];
                    $data['anio'] = $expediente[2];
                } else if (count($expediente) == 2) {
                    $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
                } else if (count($expediente) == 1) {
                    $data['expediente'] = $expediente[0];
                }
            }

        }


        $entities = $em->getRepository('MesaEntradaBundle:Expediente')->getProyectosBAE($data);

        $json = array();

        if (!count($entities)) {
            $json[] = array(
                'text' => 'No se encontraron coincidencias',
                'id' => ''
            );
        } else {

            foreach ($entities as $entity) {
                $anio = $entity['anio'] ? $entity['anio'] : $entity['periodoLegislativo']['anio'];
                $text = $entity['expediente'] . '-' . strtoupper($entity['letra']) . '-' . $anio;
                $json[] = array(
                    'id' => $entity['id'],
                    //'label' => $entity[$property],
                    'text' => $text
                );
            }
        }

        return new JsonResponse($json);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDictamenesODAction(Request $request)
    {
        $em = $this->getDoctrine();

        $value = $request->get('q');
        $limit = $request->get('page_limit');

        if ($value) {
            $expediente = explode(' ', $value);
            if (is_numeric($expediente[0][0])) {
                if (count($expediente) == 3) {
                    $data['expediente'] = $expediente[0];
                    $data['letra'] = $expediente[1];
                    $data['anio'] = $expediente[2];
                } else if (count($expediente) == 2) {
                    $data['expediente'] = $expediente[0];
                    $data['letra'] = $expediente[1];
                } else if (count($expediente) == 1) {
                    $data['expediente'] = $expediente[0];
                }
            } else {
                if (count($expediente) == 4) {
                    $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
                    $data['letra'] = $expediente[2];
                    $data['anio'] = $expediente[2];
                } else if (count($expediente) == 3) {
                    $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
                    $data['letra'] = $expediente[1];
                    $data['anio'] = $expediente[2];
                } else if (count($expediente) == 2) {
                    $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
                } else if (count($expediente) == 1) {
                    $data['expediente'] = $expediente[0];
                }
            }

        }

        $dictamenes = $em->getRepository(Expediente::class)->getDictamenesOD($data);

        $json = array();

        if (!count($dictamenes)) {
            $json[] = array(
                'text' => 'No se encontraron coincidencias',
                'id' => ''
            );
        } else {
            /** @var Dictamen $dictamen */
            foreach ($dictamenes as $dictamen) {

                $dictamenOd = $em->getRepository(DictamenOD::class)->findBy([
                    'dictamen' => $dictamen->getId(),
                ], [
                    'id' => 'desc'
                ]);

                if (count($dictamenOd)) {
                    $dictamenOd = $dictamenOd[0];
                    /** @var Sesion $sesion */
                    $sesion = $em->getRepository(Sesion::class)
                        ->findQbUltimaSesion()
                        ->getQuery()
                        ->getSingleResult();

                    if ($dictamenOd->getOrdenDelDia()->getSesion()->getId() == $sesion->getId()) {
                        $dictamenOd = null;
                    }
                } else {
                    $dictamenOd = null;
                }

                $json[] = array(
                    'id' => $dictamen->getId(),
                    'text' => $dictamen->__toString().
                        ($dictamenOd?(
                            '( En OD de '.$dictamenOd->getOrdenDelDia()->getSesion()->__toString().')'
                        ):''),
                );
            }
        }

        return new JsonResponse($json);
    }

    public function getExpedientesAction(Request $request)
    {
        $em = $this->getDoctrine();

        $value = $request->get('q');
        $limit = $request->get('page_limit');


        if ($value) {
	        $expediente = explode(' ', $value);
	        if (is_numeric($expediente[0][0])) {
		        if (count($expediente) == 3) {
			        $data['expediente'] = $expediente[0];
			        $data['letra'] = $expediente[1];
			        $data['anio'] = $expediente[2];
		        } else if (count($expediente) == 2) {
			        $data['expediente'] = $expediente[0];
			        $data['letra'] = $expediente[1];
		        } else if (count($expediente) == 1) {
			        $data['expediente'] = $expediente[0];
		        }
	        } else {
		        if (count($expediente) == 4) {
			        $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
			        $data['letra'] = $expediente[2];
			        $data['anio'] = $expediente[2];
		        } else if (count($expediente) == 3) {
			        $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
			        $data['letra'] = $expediente[1];
			        $data['anio'] = $expediente[2];
		        } else if (count($expediente) == 2) {
			        $data['expediente'] = $expediente[0] . ' ' . $expediente[1];
		        } else if (count($expediente) == 1) {
			        $data['expediente'] = $expediente[0];
		        }
	        }
        }


        $entities = $em->getRepository('MesaEntradaBundle:Expediente')->getQbExpedientes($data)
            ->join('e.periodoLegislativo', 'pl')
            ->addSelect('pl')
            ->getQuery()->getArrayResult();

        $json = array();

        if (!count($entities)) {
            $json[] = array(
                'text' => 'No se encontraron coincidencias',
                'id' => ''
            );
        } else {

            foreach ($entities as $entity) {
                $anio = $entity['anio'] ? $entity['anio'] : $entity['periodoLegislativo']['anio'];
                $text = $entity['expediente'] . '-' . strtoupper($entity['letra']) . '-' . $anio;
                $json[] = array(
                    'id' => $entity['id'],
                    //'label' => $entity[$property],
                    'text' => $text
                );
            }
        }

        return new JsonResponse($json);
    }

    public function consultarSesionesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Sesion')->getQbAll()
            ->getQuery()->getArrayResult();

        $json = array();

        if (!count($entities)) {
            $json[] = array(
                'text' => 'No se encontraron coincidencias',
                'id' => ''
            );
        } else {

            foreach ($entities as $entity) {
                $json[] = array(
                    'id' => $entity['id'],
                    //'label' => $entity[$property],
                    'text' => $entity['titulo'],
                    'acta' => $entity['acta'],
                    'homenajes' => $entity['homenajes']
                );
            }
        }

        return new JsonResponse($json);

    }

    public function getUsuariosAction()
    {
        $usuarios = $this->getDoctrine()
            ->getManager()
            ->getRepository(Usuario::class)
            ->findBy(['enabled' => true]);

        $usuarios = array_map(function (Usuario $usuario) {
            return [
                'id' => $usuario->getId(),
                'username' => $usuario->getUsername(),
                'nombre' => $usuario->getPersona() ? $usuario->getPersona()->getNombreCompleto() : null,
                'roles' => $usuario->getRoles(),
            ];
        },
            $usuarios);

        return JsonResponse::create($usuarios);
    }

    public function renderExtractoTemarioFormAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');

        $proyectoBae = $em->getRepository('AppBundle:ProyectoBAE')->find($id);
        $expediente = $proyectoBae->getExpediente();

        // Estos son los campos a auditar en el log
        $campos = ['extractoTemario'];

        $valoresOriginales = [];
        foreach ($campos as $campo) {
            $getter = 'get' . ucfirst($campo);
            $valoresOriginales[$campo] = [
                'valor' => $expediente->{$getter}(),
                'getter' => $getter,
            ];
        }

        $form = $this->createFormBuilder($expediente,
            [
                'attr' => [
                    'id' => 'editar-extracto-temario-form',
                    'data-id' => $id,
                ],
                'method' => 'post'
            ])
            ->add('extractoTemario',
                CKEditorType::class,
                [
                    'required' => true,
                    'config' => array(
                        'uiColor' => '#ffffff',
                    ),
                    'attr' => ['class' => 'texto_por_defecto']
                ])
            ->getForm();

        if ($request->get('data')) {

            $originalReq = $request->getContent();

            parse_str($originalReq, $aReq);

            parse_str($aReq['data'], $formReq);

            $request->request->set('form', $formReq['form']);

            $form->handleRequest($request);

            if ($form->isValid()) {

                $log = Log::forEntity($expediente);
                foreach ($valoresOriginales as $nombre => $campo) {
                    if ($campo['valor'] != $expediente->{$campo['getter']}()) {
                        $log->agregarCambio($nombre, $campo['valor'], $expediente->{$campo['getter']}());
                    }
                }

                if ($log->hasCambios()) {
                    $em->persist($log);
                }

                $em->flush();

                return new JsonResponse(array('message' => 'Extracto Guardado Correctamente'), 200);
            }
        }

        $html = $this->renderView(':ajax:editar_extracto.html.twig',
            [
                'expediente' => $expediente,
                'form' => $form->createView()
            ]);

        return new JsonResponse(
            ['form' => $html]
        );
    }

    public function renderExtractoDictamenFormAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');

        $dictamenOd = $em->getRepository('AppBundle:DictamenOD')->find($id);
        $expediente = $dictamenOd->getExpediente();

        // Estos son los campos a auditar en el log
        $campos = ['extractoDictamen'];

        $valoresOriginales = [];
        foreach ($campos as $campo) {
            $getter = 'get' . ucfirst($campo);
            $valoresOriginales[$campo] = [
                'valor' => $expediente->{$getter}(),
                'getter' => $getter,
            ];
        }

        $form = $this->createFormBuilder($expediente,
            [
                'attr' => [
                    'id' => 'editar-extracto-dictamen-form',
                    'data-id' => $id,
                ],
                'method' => 'post'
            ])
            ->add('extractoDictamen',
                CKEditorType::class,
                [
                    'required' => true,
                    'config' => array(
                        'uiColor' => '#ffffff',
                    ),
                    'attr' => ['class' => 'texto_por_defecto']
                ])
            ->getForm();

        if ($request->get('data')) {

            $originalReq = $request->getContent();

            parse_str($originalReq, $aReq);

            parse_str($aReq['data'], $formReq);

            $request->request->set('form', $formReq['form']);

            $form->handleRequest($request);

            if ($form->isValid()) {

                $log = Log::forEntity($expediente);
                foreach ($valoresOriginales as $nombre => $campo) {
                    if ($campo['valor'] != $expediente->{$campo['getter']}()) {
                        $log->agregarCambio($nombre, $campo['valor'], $expediente->{$campo['getter']}());
                    }
                }

                if ($log->hasCambios()) {
                    $em->persist($log);
                }

                $em->flush();

                return new JsonResponse(array('message' => 'Extracto Guardado Correctamente'), 200);
            }
        }

        $html = $this->renderView(':ajax:editar_extracto.html.twig',
            [
                'expediente' => $expediente,
                'form' => $form->createView()
            ]);

        return new JsonResponse(
            ['form' => $html]
        );
    }

    /**
     * @return JsonResponse
     */
    public function getConcejalesAction()
    {
        $concejales = $this->getDoctrine()
            ->getManager()
            ->getRepository(Usuario::class)
            ->createQueryBuilder('u')
            ->join('u.persona', 'p')
            ->join('p.cargoPersona', 'cp')
            ->join('cp.cargo', 'c')
            ->where('cp.activo = true')
            ->andWhere('p.activo = true')
            ->andWhere('u.enabled = true')
            ->andWhere('c.id = :id')
            ->setParameter('id', Cargo::CARGO_CONCEJAL)
            ->getQuery()
            ->getResult();

        $concejales = array_map(function (Usuario $usuario) {
            $persona = $usuario->getPersona();

            return [
                'id' => $usuario->getId(),
                'nombre' => ucwords(strtolower($persona->getNombreCompleto())),
            ];
        },
            $concejales);

        usort($concejales,
            function ($c1, $c2) {
                return $c1['nombre'] <=> $c2['nombre'];
            });

        return JsonResponse::create(['concejales' => $concejales]);
    }

    public function verDictamenAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');

        $dictamen = $em->getRepository('MesaEntradaBundle:Dictamen')->find($id);

        $json = [];
        if ($dictamen) {
            $json['texto'] = $dictamen->getTextoDictamen();

            $json['expediente'] = $dictamen->getExpediente()->__toString();
            foreach ($dictamen->getFirmantes() as $firmante) {
                $json['firmantes'][] = $firmante->getIniciador()->__toString();
            }
        }

        return new JsonResponse($json);
    }

    /**
     * @param Request $request
     * @param Sesion $sesion
     * @return JsonResponse
     */
    public function getSesionAction(Request $request, Sesion $sesion)
    {
        /** @var BoletinAsuntoEntrado $bae */
        $bae = $sesion->getBae()->first();
        /** @var OrdenDelDia $od */
        $od = $sesion->getOd()->first();

        $mapBae = function (ProyectoBAE $bae) use ($sesion) {
            return [
                'id' => $bae->getId(),
                'extracto' => $bae->getExtracto(),
                'expediente' => $bae->getExpediente() ? $this->mapExpediente($bae->getExpediente(), $sesion) : null,
            ];
        };

        $mapOd = function (DictamenOD $od) {
            $firmantes = [];
            $dictamen = $od->getDictamen();
	        foreach ( $dictamen->getFirmantes() as $firmante ) {
		        if ( $firmante->getIniciador() ) {
			        $firmantes[] = $firmante->getIniciador()->__toString();
		        }
	        }
            return [
                'id' => $od->getId(),
                'extracto' => $od->getExtracto(),
                'expediente' => $this->mapExpediente($od->getDictamen()->getExpediente()),
                'dictamen' => [
                    'id' => $dictamen->getId(),
                    'texto' => $dictamen->getTextoDictamen(),
                    'firmantes' => $firmantes,
                ]
            ];
        };

        $proyectos = [
            'ejecutivo' => [
                'titulo' => 'INFORMES DEL DEPARTAMENTO EJECUTIVO',
                'proyectos' => [],
            ],
            'pejecutivo' => [
                'titulo' => 'PROYECTOS DEL DEPARTAMENTO EJECUTIVO',
                'proyectos' => [],
            ],
            'concejales' => [
                'titulo' => 'PROYECTOS DE CONCEJALES',
                'proyectos' => [],
            ],
            'defensor' => [
                'titulo' => 'PROYECTOS DEL DEFENSOR DEL PUEBLO',
                'proyectos' => [],
            ],
        ];

        $i = 0;
        foreach (array_map($mapBae, $bae->getInformesDeDEM()->toArray()) as $proyecto) {
            $proyectos['ejecutivo']['proyectos'][$i++] = $proyecto;
        }

        $i = 0;
        foreach (array_map($mapBae, $bae->getProyectosDeDEM()->toArray()) as $proyecto) {
            $proyectos['pejecutivo']['proyectos'][$i++] = $proyecto;
        }

        $i = 0;
        foreach (array_map($mapBae, $bae->getProyectosDeConcejales()->toArray()) as $proyecto) {
            $proyectos['concejales']['proyectos'][$i++] = $proyecto;
        }

        $i = 0;
        foreach (array_map($mapBae, $bae->getProyectosDeDefensor()->toArray()) as $proyecto) {
            $proyectos['defensor']['proyectos'][$i++] = $proyecto;
        }


        $dictamenes = [
            'preferencial' => [
                'titulo' => 'EXPEDIENTES CON TRATAMIENTO PREFERENCIAL',
                'dictamenes' => [],
            ],
            'declaracion' => [
                'titulo' => 'DICTÁMENES DE DECLARACIÓN',
                'dictamenes' => [],
            ],
            'comunicacion' => [
                'titulo' => 'DICTÁMENES DE COMUNICACIÓN',
                'dictamenes' => [],
            ],
            'resolucion' => [
                'titulo' => 'DICTÁMENES DE RESOLUCIÓN',
                'dictamenes' => [],
            ],
            'ordenanza' => [
                'titulo' => 'DICTÁMENES DE ORDENANZA',
                'dictamenes' => [],
            ],
        ];

        $i = 0;
        foreach (array_map($mapOd, $od->getDictamenesConTratamientoPreferencial()->toArray()) as $dictamen) {
            $dictamenes['preferencial']['dictamenes'][$i++] = $dictamen;
        }

        $i = 0;
        foreach (array_map($mapOd, $od->getDictamenesDeDeclaracion()->toArray()) as $dictamen) {
            $dictamenes['declaracion']['dictamenes'][$i++] = $dictamen;
        }

        $i = 0;
        foreach (array_map($mapOd, $od->getDictamenesDeComunicacion()->toArray()) as $dictamen) {
            $dictamenes['comunicacion']['dictamenes'][$i++] = $dictamen;
        }

        $i = 0;
        foreach (array_map($mapOd, $od->getDictamenesDeResolucion()->toArray()) as $dictamen) {
            $dictamenes['resolucion']['dictamenes'][$i++] = $dictamen;
        }

        $i = 0;
        foreach (array_map($mapOd, $od->getDictamenesDeOrdenanza()->toArray()) as $dictamen) {
            $dictamenes['ordenanza']['dictamenes'][$i++] = $dictamen;
        }

        return JsonResponse::create([
            'sesion' => [
                'id' => $sesion->getId(),
                'fecha' => $sesion->getFecha()->format('Y-m-d H:i:s'),
                'titulo' => $sesion->getTitulo(),
                'asuntosEntrados' => $sesion->getAsuntosEntrados(),
                'tipoSesion' => $sesion->getTipoSesion()->getValor(),
                'proyectos' => $proyectos,
                'dictamenes' => $dictamenes,
                'acta' => $sesion->getActa(),
            ]
        ]);
    }

    private function mapExpediente(Expediente $exp, Sesion $sesion = null)
    {
        $anexos = $exp->getAnexos()->map(function (AnexoExpediente $anexo) {
            return [
                'id' => $anexo->getId(),
                'descripcion' => $anexo->getDescripcion(),
                'anexo' => $anexo->getAnexo(),
            ];
        })->toArray();

        $giros = $exp->getGirosOrdenados($sesion)->map(function (Giro $giro) {
            return [
                'id' => $giro->getId(),
                'cabecera' => $giro->getCabecera(),
                'comisionOrigen' => $giro->getComisionOrigen() ? [
                    'id' => $giro->getComisionOrigen()->getId(),
                    'nombre' => $giro->getComisionOrigen()->getNombre(),
                    'abreviacion' => $giro->getComisionOrigen()->getAbreviacion(),
                ] : null,
                'comisionDestino' => $giro->getComisionDestino() ? [
                    'id' => $giro->getComisionDestino()->getId(),
                    'nombre' => $giro->getComisionDestino()->getNombre(),
                    'abreviacion' => $giro->getComisionDestino()->getAbreviacion(),
                ] : null,
                'archivado' => $giro->getArchivado(),
                'texto' => $giro->getTexto(),
            ];
        })->toArray();

        $autor = null;
        $iniciadores = $exp->getIniciadores()->map(function (IniciadorExpediente $ie) use (&$autor) {
            if ($ie->getAutor()) {
                $autor = [
                    'nombre' => $ie->getIniciador()->getCargoPersona()->getPersona()->getNombreCompleto(),
                    'cargo' => $ie->getIniciador()->getCargoPersona()->getCargo()->getNombre(),
                ];
            }
            if ($ie->getIniciador()) {
                return [
                    'nombre' => $ie->getIniciador()->getCargoPersona()->getPersona()->getNombreCompleto(),
                    'cargo' => $ie->getIniciador()->getCargoPersona()->getCargo()->getNombre(),
                    'esAutor' => $ie->getAutor(),
                ];
            } else {
                return [];
            }
        })->toArray();

        return [
            'id' => $exp->getId(),
            'fecha' => $exp->getFecha() ? $exp->getFecha()->format('Y-m-d H:i:s') : null,
            'fechaPresentacion' => $exp->getFechaPresentacion() ? $exp->getFechaPresentacion()->format('Y-m-d H:i:s') : null,
            'expediente' => $exp->__toString(),
            'autor' => $autor,
            'iniciadores' => $iniciadores,
            'extracto' => $exp->getExtracto(),
//            'extractoDictamen' => $exp->getExtractoDictamen(),
//            'extractoTemario' => $exp->getExtractoTemario(),
            'giros' => $giros,
            'textoDelGiro' => $exp->getGirosOrdenados($sesion)->count() ? $exp->getTextoDelGiro($sesion) : null,
            'texto' => $exp->getTexto(),
            'textoDefinitivo' => $exp->getTextoDefinitivo(),
            'anexos' => $anexos,
        ];
    }
}
