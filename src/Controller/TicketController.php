<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Usuario;
use App\Entity\CargoPersona;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TicketType;
use App\Form\Filter\TicketFilterType;
use App\Form\CloseTicketType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\TemplatedEmail;



class TicketController extends AbstractController
{
    /**
     * @Route("/ticket", name="ticket")
     */
    public function enviadoindex(PaginatorInterface $paginator,Request $request)
    {

        $em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $tickets = $em->getRepository(Ticket::class)->findByAreaOrigenWithRelated($area,['id'=>'Desc']);
		
				
		$form = $this->createForm(TicketFilterType::class,			null,
		[
			'method' => 'GET'
		]);
	
		$form->handleRequest($request);

		if ($form->get( 'buscar' )->isClicked()) {
			$data=$form->getData();

			$tickets = $em->getRepository(Ticket::class)->getQbBuscar($data->getAreaDestino(),$area,$data->getFecha(),$data->getTexto());
		}

        $tickets = $paginator->paginate(
        $tickets,
        $request->query->get('page', 1)/* page number */,
        10/* limit per page */
    );



        return $this->render('ticket/enviado_index.html.twig', [
            'tickets' => $tickets,
			'filter_type' => $form->createView(),

        ]);
    }

	public function agenda(Request $request)
	{
		return $this->render('ticket/agenda.html.twig');
	}

	public function recibidoindex(PaginatorInterface $paginator, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $tickets = $em->getRepository(Ticket::class)->findByAreaDestinoWithRelated($area,['id' => 'DESC']);
		
		$form = $this->createForm(TicketFilterType::class,			null,
		[
			'method' => 'GET'
		]);
	
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data=$form->getData();

			$tickets = $em->getRepository(Ticket::class)->getQbBuscar($area,$data->getAreaOrigen(),$data->getFecha(),$data->getTexto());
		}

        $tickets = $paginator->paginate(
        $tickets,
        $request->query->get('page', 1)/* page number */,
        10/* limit per page */
    );


        return $this->render('ticket/recibido_index.html.twig', [
            'tickets' => $tickets,
			'filter_type' => $form->createView(),
        ]);
    }

    public function newTicket(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $ticket = new Ticket();

		// Crear el formulario
		$form = $this->createForm(TicketType::class, $ticket);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$ticket->setAreaOrigen($area);
			$ticket->setFecha(new \DateTime('now'));
			$ticket->setAbierto(false);
			$em->persist($ticket);
			$em->flush();

			$this->addFlash('success', 'Ticket generado con éxito');

			return $this->redirectToRoute('tickets_enviados');
		}

		return $this->render(
			'ticket/newTicket.html.twig',
			[
				'form' => $form->createView()
			]
		);
	}

	public function cancelTicket(Ticket $id){
        $em = $this->getDoctrine()->getManager();


		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();
		if ($id->getCompleto() == null){
		$id->setObservacion('Cancelado');
		$id->setCompleto(false);
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
			if($id->getCompleto() == null ){
			$id->setCompleto(true);
			$id->setFechaC(new \DateTime('now'));
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

	public function rejectTicket(Request $request,Ticket $id){
        $em = $this->getDoctrine()->getManager();


		$form = $this->createForm(CloseTicketType::class, $id);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if($id->getCompleto() == null ){
			$id->setCompleto(false);
			$id->setFechaC(new \DateTime('now'));
			$em->flush();
			}
			$this->get('session')->getFlashBag()->add(
				'Warning',
				'Ticket rechazado'
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


	public function aceptarTicket(Ticket $id){
        $em = $this->getDoctrine()->getManager();

		$id->setAbierto(true);
		$id->setFechaV(new \DateTime());

		$em->flush();

		return $this->redirectToRoute('tickets_recibidos');
	}

	public function todosindex(PaginatorInterface $paginator,Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $tickets = $em->getRepository(Ticket::class)->findBy([],['id' => 'DESC']);
			
        $tickets = $paginator->paginate(
        $tickets,
        $request->query->get('page', 1)/* page number */,
        10/* limit per page */
    );



        return $this->render('ticket/all_index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

		public function confirmarTicket(Ticket $id){
        $em = $this->getDoctrine()->getManager();

		$id->setConfirmado(true);
		$id->setFechaCon(new \DateTime());

		$em->flush();

		return $this->redirectToRoute('tickets_enviados');
	}

	public function observarTicket(Ticket $id){
        $em = $this->getDoctrine()->getManager();

		$id->setConfirmado(false);
		$id->setFechaCon(new \DateTime());

		$em->flush();

		return $this->redirectToRoute('tickets_enviados');
	}

	public function createRelatedTicket(Request $request, Ticket $ticket)
	{
		$em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();
		
		// Verificar que el ticket a relacionar tiene como destino el área del usuario actual
		if ($ticket->getAreaDestino()->getId() !== $area->getId()) {
			$this->addFlash('error', 'Solo puede relacionar tickets recibidos por su área');
			return $this->redirectToRoute('tickets_enviados');
		}

		$nuevoTicket = new Ticket();
		$nuevoTicket->setTicketPadre($ticket);
		
		// Crear el formulario
		$form = $this->createForm(TicketType::class, $nuevoTicket);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$nuevoTicket->setAreaOrigen($area);
			$nuevoTicket->setFecha(new \DateTime('now'));
			$nuevoTicket->setAbierto(false);
			$em->persist($nuevoTicket);
			$em->flush();

			$this->addFlash('success', 'Ticket relacionado generado con éxito');

			return $this->redirectToRoute('tickets_enviados');
		}

		return $this->render(
			'ticket/newRelatedTicket.html.twig',
			[
				'form' => $form->createView(),
				'ticketPadre' => $ticket
			]
		);
	}

	/**
	 * Muestra el seguimiento completo de un ticket, desde el original hasta el último relacionado
	 */
	public function seguimientoTicket(Request $request, Ticket $ticket)
	{
		$em = $this->getDoctrine()->getManager();
		
		// Encontrar el ticket raíz (el primero de la cadena)
		$ticketRaiz = $ticket;
		while ($ticketRaiz->getTicketPadre() !== null) {
			$ticketRaiz = $ticketRaiz->getTicketPadre();
		}
		
		// Cargar toda la cadena de tickets relacionados
		$cadenaTickets = $this->cargarCadenaTickets($ticketRaiz);
		
		return $this->render('ticket/seguimiento.html.twig', [
			'ticketInicial' => $ticket,
			'ticketRaiz' => $ticketRaiz,
			'cadenaTickets' => $cadenaTickets
		]);
	}
	
	/**
	 * Carga recursivamente toda la cadena de tickets relacionados
	 */
	private function cargarCadenaTickets($ticket)
	{
		$em = $this->getDoctrine()->getManager();
		$resultado = [];
		
		// Primero agregar el ticket actual
		$resultado[] = $ticket;
		
		// Luego buscar y agregar todos sus hijos
		$hijos = $em->getRepository(Ticket::class)->findBy(['ticketPadre' => $ticket], ['id' => 'ASC']);
		
		foreach ($hijos as $hijo) {
			$resultado = array_merge($resultado, $this->cargarCadenaTickets($hijo));
		}
		
		return $resultado;
	}

}
