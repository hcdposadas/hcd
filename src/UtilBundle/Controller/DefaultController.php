<?php

namespace UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UtilBundle:Default:index.html.twig');
    }

	public function getAjaxDefaultAction(Request $request) {
		$value = strtoupper($request->get('term'));
		//$value = $request->get('term');
		$class = $request->get('class');
		$property = $request->get('property');
		$getProperty = 'get' . ucwords( $property );
		$searchMethod = $request->get('search_method');
		$em= $this->getDoctrine()->getManagerForClass($class);
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
					'id' => $entity->getId(),
					//'label' => $entity[$property],
					'value' => $entity->$getProperty()
				);
			}
		}
//		$response = new Response();
//		$response->setContent(json_encode($json));
//		return $response;
		return new JsonResponse($json);
	}
}
