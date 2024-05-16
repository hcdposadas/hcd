<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ProyectoBAE;
use App\Entity\GirosDestino;
use App\Entity\Comision;
use App\Entity\Dictamen;
use App\Entity\Expediente;
use App\Form\CrearDictamenType;
use App\Form\CrearDictamenComisionType;
use App\Form\FirmaDictamenType;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use App\Form\FirmaBAEType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class ComisionController extends AbstractController
{
    /**
     * @Route("/comision", name="comision")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $comision = $this->getUser()->getPersona()->getCargoPersona()->first()->getComision();

        if (!$comision) {
            
        }

        $giros = $comision->getGirosDestinos()->toArray();
        usort($giros, function($a, $b) {
            return $b->getId() - $a->getId();
        });

        $giros = array_filter($giros, function($giro) {
            return  $giro->getProyectoBae();
        });

        $giros = $paginator->paginate(
            $giros,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('comision/index.html.twig', [
            'controller_name' => 'ComisionController',
            'giros' => $giros
        ]);
    }

    public function misDictamenes(PaginatorInterface $paginator, Request $request)
    {

        $esPresidenteComision = $this->getUser()->getPersona()->esPresidenteComision();


        $comision =$esPresidenteComision->getComision();

        if (!$comision) {
            
        }


        $em = $this->getDoctrine()->getManager();
        $dictamenes = $em->getRepository(Dictamen::class)
            ->findByPresidenteComision($esPresidenteComision);

        $dictamenes = $paginator->paginate(
            $dictamenes,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('comision/dictamenes.html.twig', [
            'controller_name' => 'ComisionController',
            'dictamenes' => $dictamenes
        ]);
    }


    public function newdictamen(Request $request,$id )
    {

        $esPresidenteComision = $this->getUser()->getPersona()->esPresidenteComision();

        

        $expediente = $this->getDoctrine()->getRepository(Expediente::class)->find($id);

        
        $dictamen = new Dictamen();

        $form = $this->createForm(CrearDictamenComisionType::class, $dictamen);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $dictamen->setExpediente($expediente);

            $dictamen->setPresidenteComision($esPresidenteComision);

            $em->persist($dictamen);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Dictamen creado correctamente'
            );

            return $this->redirectToRoute('dictamen_index');
        }

        return $this->render('dictamen/crear.html.twig',
            [
                'form' => $form->createView()
            ]);
    }


    public function editdictamen(Request $request,$id )
    {

        //$esPresidenteComision = $this->getUser()->getPersona()->esPresidenteComision();

        $esPresidenteComision = true;

        $expediente = $this->getDoctrine()->getRepository(Expediente::class)->find($id);

        
        $dictamen = new Dictamen();

        $form = $this->createForm(CrearDictamenComisionType::class, $dictamen);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $dictamen->setExpediente($expediente);

            $em->persist($dictamen);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Dictamen creado correctamente'
            );

            return $this->redirectToRoute('dictamen_index');
        }

        return $this->render('dictamen/crear.html.twig',
            [
                'form' => $form->createView()
            ]);
    }


    public function cargarGiroSecretaria(ProyectoBae $id )
    {

        $giros = $comision->getGiros()->slice(0, 100);

        var_dump($giros);
        die();



        return $this->render('comision/index.html.twig', [
            'controller_name' => 'ComisionController',
        ]);
    }

    public function showDictamen(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $dictamen = $em->getRepository(Dictamen::class)->find($id);

        $form = $this->createForm(FirmaDictamenType::class, $dictamen);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Dictamen creado correctamente'
            );

            return $this->render('comision/showDictamen.html.twig',
            [
                'form' => $form->createView(),
                'dictamen' => $dictamen
            ]);
        }
        return $this->render('comision/showDictamen.html.twig',
            [
                'form' => $form->createView(),
                'dictamen' => $dictamen
            ]);
    }

    public function showProyectoComision(Request $request,Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();

		return $this->render(
			'comision/showProyecto.html.twig',
			[
				'expediente' => $expediente,
			]
		);
	}

    public function indexProyectosBae(PaginatorInterface $paginator, Request $request){

        $em = $this->getDoctrine()->getManager();



        $proyectosBae = $em->getRepository(ProyectoBae::class)->findBy(
            ['tratamientoSobretabla' => [null, false]],
            ['fechaCreacion' => 'DESC'],
            10
        );

        $proyectosBae = $paginator->paginate(
            $proyectosBae,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('comision/proyectosBae.html.twig', [
            'controller_name' => 'ComisionController',
            'proyectos' => $proyectosBae
        ]);
    }

    public function indexGiros(PaginatorInterface $paginator, Request $request){

        $em = $this->getDoctrine()->getManager();
        $proyectosBae = $em->getRepository(ProyectoBae::class)->findBy(
            ['tratamientoSobretabla' => [false]],
            ['id' => 'DESC']
        );
        $proyectosBae = $paginator->paginate(
            $proyectosBae,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );
        return $this->render('comision/indexGiros.html.twig', [
            'controller_name' => 'ComisionController',
            'proyectos' => $proyectosBae
        ]);
    }

    public function showProyectoBae(Request $request, ProyectoBAE $proyectoBae): Response
    {
        $em = $this->getDoctrine()->getManager();

        $expediente = $proyectoBae->getExpediente();
    
        $form = $this->createForm(FirmaBaeType::class, $proyectoBae);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
    
            $this->addFlash(
                'success',
                'Giro firmado correctamente'
            );
    
            return $this->render(
                'comision/firmar.html.twig',
                [
                    'form' => $form->createView(),
                    'proyectobae' => $proyectoBae,
                    'expediente' => $expediente,
                ]
            );
        }
    
        return $this->render(
            'comision/firmar.html.twig',
            [
                'form' => $form->createView(),
                'proyectobae' => $proyectoBae,
                'expediente' => $expediente,
            ]
        );
    }
    public function imprimirGiros(Pdf $knpSnappyPdf, Request $request,ProyectoBae $id){
		$giros=$id->getGirosOrdenados();
		$expediente=$id->getExpediente();
		$sesion=$id->getBoletinAsuntoEntrado()->getSesion();

		$titulo ="Giro ". $expediente->getExpediente()."-".$expediente->getLetra()."-". $expediente->getPeriodoLegislativo()->getAnio();
		$fecha=$sesion->getFecha();


		$html = $this->renderView(
			'comision/giroComision.pdf.twig',
			[
				'expediente' => $expediente,
                'sesion'=>$sesion,
				'title'      => $titulo,
                'giros'      => $giros,
				'fecha'      => $fecha,
                
			]
		);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml(
				$html,
				array(
					'page-size'      => 'Legal',
					'margin-left'  => "2cm",
					'margin-right' => "3cm",
					'margin-top'   => "3cm",
					//                    'margin-bottom' => "1cm"
				)
			),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $titulo . '.pdf"'
			)
		);
	}
}
