<?php

namespace App\Controller;

use App\Form\PerfilUsuarioType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {


	/**
	 * @Route("/login", name="app_login")
	 */
	public function login( AuthenticationUtils $authenticationUtils ): Response {
		// if ($this->getUser()) {
		//     return $this->redirectToRoute('target_path');
		// }

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render( 'security/login.html.twig', [ 'last_username' => $lastUsername, 'error' => $error ] );
	}

	/**
	 * @Route("/logout", name="app_logout")
	 */
	public function logout() {

		return $this->redirectToRoute( 'app_login' );
//        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
	}

	/**
	 * @Route("/perfil", name="app_profile")
	 */
	public function perfil( Request $request, UserPasswordEncoderInterface $passwordEncoder ) {

		$em      = $this->getDoctrine()->getManager();
		$usuario = $this->getUser();

		$form = $this->createForm( PerfilUsuarioType::class, $usuario );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$passwordPlano = $request->get( 'perfil_usuario' )['passwordPlano']['first'];
			if ( $passwordPlano ) {
				$usuario->setPassword( $passwordEncoder->encodePassword(
					$usuario,
					$passwordPlano
				) );
			}
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Perfil actualizado correctamente'
			);
		}

		return $this->render( 'security/profile.html.twig',
			[
				'form' => $form->createView()
			] );
	}
//
//	/**
//	 * @Route("/resetting/request", name="app_forgot_password")
//	 */
//	public function forgot() {
//		return $this->render( 'security/forgot.html.twig',
//			[
////				'form' => $form->createView()
//			] );
//	}
}
