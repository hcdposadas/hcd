<?php

namespace App\Controller;

use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TicketType;
use App\Form\CloseTicketType;



class TicketController extends AbstractController
{
    /**
     * @Route("/ticket", name="ticket")
     */
    public function enviadoindex(PaginatorInterface $paginator,Request $request)
    {

        $em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $tickets = $em->getRepository(Ticket::class)->findByAreaOrigen($area);
			
        $tickets = $paginator->paginate(
        $tickets,
        $request->query->get('page', 1)/* page number */,
        10/* limit per page */
    );



        return $this->render('ticket/enviado_index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

	public function recibidoindex(PaginatorInterface $paginator, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $tickets = $em->getRepository(Ticket::class)->findByAreaDestino($area);
			
        $tickets = $paginator->paginate(
        $tickets,
        $request->query->get('page', 1)/* page number */,
        10/* limit per page */
    );


        return $this->render('ticket/recibido_index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    public function newTicket(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $ticket= new Ticket();


		$form = $this->createForm(TicketType::class, $ticket);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$ticket->setAreaOrigen($area);
			$ticket->setCompleto(false);
			$ticket->setFecha(new \DateTime('now'));
			$em->persist($ticket);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Ticket Generado con exito'
			);

			return $this->redirectToRoute('tickets_enviados');
		}


		return $this->render(
			'ticket/newTicket.html.twig',
			[
				'form'       => $form->createView()
			]
		);
	}

	public function cancelTicket(Ticket $id){
        $em = $this->getDoctrine()->getManager();


		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();
		if ($id->getCompleto() == false){
		$id->setObservacion('Cancelado');
		}
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'Warning',
				'Ticket Cancelado '
			);

		return $this->redirectToRoute('tickets_enviados');
		

    }

    public function closeTicket(Request $request,Ticket $id){
        $em = $this->getDoctrine()->getManager();


		$form = $this->createForm(CloseTicketType::class, $id);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if($id->getCompleto() == false and $id->getObservacion() != 'Cancelado'){
			$id->setCompleto(true);
			$em->flush();
			}
			$this->get('session')->getFlashBag()->add(
				'success',
				'Ticket finalizado con exito'
			);

			return $this->redirectToRoute('tickets_recibidos');
		}


		return $this->render(
			'ticket/closeTicket.html.twig',
			[
				'ticket' => $id,
				'form'       => $form->createView()
			]
		);

    }

}
