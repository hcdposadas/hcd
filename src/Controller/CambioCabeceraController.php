<?php

namespace App\Controller;

use App\Entity\ProyectoBAE;
use App\Entity\Sesion;
use App\Form\CambioCabeceraType;
use App\Service\CambioCabeceraManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SESIONES → Cambio de Cabecera.
 *
 * Diseño: docs/superpowers/specs/2026-09-22-cambio-de-cabecera-design.md
 */
class CambioCabeceraController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        if (!$this->isGranted('ROLE_LEGISLATIVO')) {
            return $this->sinPermiso();
        }

        $numero = trim((string) $request->query->get('numero', ''));

        $cambios = $paginator->paginate(
            $em->getRepository(ProyectoBAE::class)->getQbCambiosDeCabecera($numero),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('cambio_cabecera/index.html.twig', [
            'cambios' => $cambios,
            'numero' => $numero,
        ]);
    }

    public function nuevo(Request $request, EntityManagerInterface $em, CambioCabeceraManager $manager): Response
    {
        if (!$this->isGranted('ROLE_LEGISLATIVO')) {
            return $this->sinPermiso();
        }

        $sesion = null;
        $proyectos = [];
        $cabecerasAnteriores = [];

        $sesionId = $request->query->getInt('sesion');
        if ($sesionId) {
            $sesion = $em->getRepository(Sesion::class)->find($sesionId);
            if (!$sesion || !$sesion->esOrdinaria()) {
                $this->addFlash('warning', 'La sesión elegida no es una sesión ordinaria.');

                return $this->redirectToRoute('sesiones_cambio_cabecera_nuevo');
            }

            $proyectos = $em->getRepository(ProyectoBAE::class)->findParaCambioDeCabecera($sesion);
            $cabecerasAnteriores = $manager->cabecerasAnteriores($proyectos);
        }

        return $this->render('cambio_cabecera/nuevo.html.twig', [
            'sesiones' => $em->getRepository(Sesion::class)->findOrdinariasConBae(),
            'sesion' => $sesion,
            'proyectos' => $proyectos,
            'cabecerasAnteriores' => $cabecerasAnteriores,
        ]);
    }

    public function formulario(Request $request, ProyectoBAE $proyectoBae, EntityManagerInterface $em, CambioCabeceraManager $manager): Response
    {
        if (!$this->isGranted('ROLE_LEGISLATIVO')) {
            return $this->sinPermiso();
        }

        $bae = $proyectoBae->getBoletinAsuntoEntrado();
        $sesion = $bae ? $bae->getSesion() : null;
        if (!$sesion || !$sesion->esOrdinaria() || $proyectoBae->getTratamientoSobretabla() || !$proyectoBae->getExpediente()) {
            $this->addFlash('warning', 'El cambio de cabecera solo se registra para proyectos del BAE de una sesión ordinaria, con expediente y que no sean de tratamiento sobre tablas.');

            return $this->redirectToRoute('sesiones_cambio_cabecera_index');
        }

        $esEdicion = (bool) $proyectoBae->getEsCambioCabecera();
        $giroCabecera = $proyectoBae->getGiroCabecera();

        $form = $this->createForm(CambioCabeceraType::class, [
            'comision' => $esEdicion && $giroCabecera ? $giroCabecera->getComisionDestino() : null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $manager->aplicar($proyectoBae, $form->get('comision')->getData());
                $em->flush();

                $this->addFlash('success', $esEdicion
                    ? 'El cambio de cabecera se modificó correctamente.'
                    : 'El cambio de cabecera se registró correctamente.');
                $this->addFlash('cambio_cabecera_imprimir', (string) $proyectoBae->getId());

                return $this->redirectToRoute('sesiones_cambio_cabecera_index');
            } catch (\DomainException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('cambio_cabecera/formulario.html.twig', [
            'form' => $form->createView(),
            'proyectoBae' => $proyectoBae,
            'sesion' => $sesion,
            'esEdicion' => $esEdicion,
            'cabeceraAnterior' => $manager->cabeceraAnterior($proyectoBae),
        ]);
    }

    private function sinPermiso(): Response
    {
        $this->addFlash('warning', 'No tiene permisos para gestionar cambios de cabecera.');

        return $this->redirectToRoute('app_homepage');
    }
}
