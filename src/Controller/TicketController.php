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
			$ticket->setFecha(new \DateTime('now'));
			$ticket->setAbierto(false);
			$em->persist($ticket);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Ticket Generado con exito'
			);

				 			$cargo = $em->getRepository( CargoPersona::class )->findOneByAreaAdministrativa( $ticket->getAreaDestino() ); 


							$user  = $em->getRepository( Usuario::class )->findOneByPersona($cargo->getPersona());
							if($user){
							
							$mail = $user->getEmail();
						
				
							$email=false;
							if ( $email ) {
								$asunto = 'HCD Posadas - Ticket De Servicio ' . $ticket->getAreaOrigen()->getNombre() . ' - ' . $ticket->getFecha()->format('d/m/Y');
					
								$email = ( new TemplatedEmail() )
									->from( new Address( $_ENV['EMAIL_FROM'], $_ENV['EMAIL_FROM_NAME'] ) )
									->to( $mail )
									->subject( $asunto )
									->htmlTemplate( 'emails/ticket.html.twig' )
									->context( [
										'ticket' => $ticket,
									] );;

					
								try {
									$mailer->send($email);
									
								} catch (TransportExceptionInterface $e) {
									// some error prevented the email sending; display an
									// error message or try to resend the message
									$this->get('logger')->error($e->getMessage());
								$this->get('logger')->error(sprintf('%s: %s', $e->getMessage(), $e->getTraceAsString()));
								}
					
							} 
						}
						 

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

		$em->flush();

		return $this->redirectToRoute('tickets_recibidos');
	}
}
