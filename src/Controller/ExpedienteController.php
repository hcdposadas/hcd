<?php

namespace App\Controller;

use App\Entity\AreaAdministrativa;
use App\Entity\Giro;
use App\Entity\AnexoGiro;
use App\Entity\TextoDefinitivo;
use App\Entity\PeriodoLegislativo;
use App\Entity\ProyectoBAE;
use App\Entity\Dictamen;
use App\Entity\Dependencia;
use App\Entity\ExpedienteBloqueado;
use App\Entity\AnexoExpediente;
use App\Entity\TipoExpediente;
use App\Form\AsignarHojasType;
use App\Form\AsignarNumeroType;
use App\Form\GiroAdministrativoSectorType;
use App\Form\GiroAdministrativoType;
use App\Form\RechazarGiroType;
use App\Form\ExpedienteType;
use App\Form\ProyectoType;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Expediente;
use App\Entity\GiroAdministrativo;
use App\Entity\IniciadorExpediente;
use App\Entity\Log;
use App\Form\EditarExtractoType;
use App\Form\FirmaType;
use App\Form\ExpedienteAdministrativoExternoType;
use App\Form\ExpedienteAdministrativoType;
use App\Form\BloqueadoType;
use App\Form\ExpedienteAdministrativoSectorType;
use App\Form\ExpedienteExtractoType;
use App\Form\ExpedienteLegislativoExternoType;
use App\Form\Filter\ExpedienteFilterType;
use App\Form\Filter\ProyectoFilterType;
use App\Form\Filter\SeguimientoExpedienteFilterType;
use App\Form\NuevoGiroExpedienteComisionType;
use App\Form\NuevoGiroExpedienteDependenciaType;
use App\Service\TimeStampManager;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use setasign\Fpdi\Fpdi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Entity\Carrera;
use App\Entity\CarreraMateria;
use App\Entity\Comision;
use App\Entity\DictamenComision;
use App\Entity\ExpedienteExterno;
use App\Entity\ExpedienteAdjunto;
use App\Entity\GiroComision;
use App\Entity\Iniciador;
use App\Entity\IniciadorParticular;
use App\Entity\Mocion;
use App\Entity\Nota;
use App\Entity\TipoGiro;
use App\Entity\TipoProyecto;
use App\Entity\Persona;
use App\Entity\DiaLaboral;
use App\Form\CaratulaType;
use App\Form\DictamenComisionType;
use App\Form\DictamenType;
use App\Form\ExpedienteAdjuntoType;
use App\Form\ExpedienteAdministrativoFilterType;
use App\Form\ExpedienteEditarExtractoType;
use App\Form\FormularioProyectoType;
use App\Form\FormularioProyectoCompletoType;
use App\Form\GiroComisionType;
use App\Form\IniciadorParticularType;
use App\Form\IniciadorType;
use App\Form\ProyectoDictamenType;
use App\Repository\ComisionRepository;
use App\Repository\DictamenComisionRepository;
use App\Repository\DictamenRepository;
use App\Repository\ExpedienteAdjuntoRepository;
use App\Repository\ExpedienteRepository;
use App\Repository\GiroAdministrativoRepository;
use App\Repository\GiroComisionRepository;
use App\Repository\IniciadorRepository;
use App\Repository\IncorporarExpedienteASesionRepository;
use App\Service\LoggerService;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Expediente controller.
 *
 */
/**
 * Compat wrapper para reemplazar PDFMerger/TCPDI usando FPDI.
 * Mantiene API: addPDF($path), merge($mode, $outputName)
 */
class PDFMerger
{
	/** @var string|null */
	private $baseDir;

	/** @var string[] */
	private $files = [];

	public function __construct(?string $baseDir = null)
	{
		$this->baseDir = $baseDir ? rtrim($baseDir, '/') : null;
	}

	public function addPDF(string $path, $pages = 'all'): self
	{
		$path = trim($path);
		if ($path === '') {
			return $this;
		}

		// Absoluto?
		if ($path[0] !== '/' && !preg_match('/^[A-Za-z]:\\\\/', $path)) {
			if ($this->baseDir) {
				$path = $this->baseDir . '/' . ltrim($path, '/');
			}
		}

		// Normalizar
		$path = str_replace(['\\'], '/', $path);

		if (is_file($path) && is_readable($path)) {
			$this->files[] = $path;
		}

		return $this;
	}

	/**
	 * @param string $mode 'browser' | 'file' | 'string' (se ignora 'browser' y devuelve string)
	 * @param string|null $outputName si $mode == 'file', path de salida
	 * @return string
	 */
	public function merge(string $mode = 'string', ?string $outputName = null): string
	{
		$pdf = new Fpdi();

		foreach ($this->files as $file) {
			// $pageCount = $pdf->setSourceFile($file);
			$pageCount = $this->setSourceFileWithFallback($pdf, $file);

			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
				$tplId = $pdf->importPage($pageNo);
				$size  = $pdf->getTemplateSize($tplId);

				$pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
				$pdf->useTemplate($tplId);
			}
		}

		if ($mode === 'file' && $outputName) {
			$pdf->Output('F', $outputName);
			return $outputName;
		}

		return $pdf->Output('S');
	}

	private function setSourceFileWithFallback(Fpdi $pdf, string $file): int
	{
		try {
			return $pdf->setSourceFile($file);
		} catch (\setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException $e) {
			// intentar normalizar y reintentar
			$fixed = $this->normalizePdf($file);
			return $pdf->setSourceFile($fixed);
		}
	}

	private function normalizePdf(string $file): string
	{
		$tmp = sys_get_temp_dir();
		$out = $tmp . '/fpdi_fixed_' . uniqid() . '.pdf';

		// 1) probar qpdf
		$qpdf = trim((string) shell_exec('command -v qpdf'));
		if ($qpdf !== '') {
			$cmd = $qpdf
				. ' --qdf --object-streams=disable '
				. escapeshellarg($file) . ' '
				. escapeshellarg($out)
				. ' 2>&1';
			exec($cmd, $o, $code);

			if ($code === 0 && is_file($out) && filesize($out) > 0) {
				return $out;
			}
		}

		// 2) probar ghostscript
		$gs = trim((string) shell_exec('command -v gs'));
		if ($gs !== '') {
			$cmd = $gs
				. ' -o ' . escapeshellarg($out)
				. ' -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress '
				. escapeshellarg($file)
				. ' 2>&1';
			exec($cmd, $o, $code);

			if ($code === 0 && is_file($out) && filesize($out) > 0) {
				return $out;
			}
		}

		// si no hay herramientas o falló todo, reventamos con mensaje claro
		throw new \RuntimeException(
			'FPDI no puede parsear el PDF y no hay qpdf/gs para normalizarlo. Instalá qpdf o ghostscript en el server.'
		);
	}
}


class ExpedienteController extends AbstractController
{
	/**
	 * Lists all expediente entities.
	 *
	 */
	public function index(PaginatorInterface $paginator, Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);

		//		if ( $request->query->has( $filterType->getName() ) ) {
		//			$filterType->submit( $request->query->get( $filterType->getName() ) );
		//		}

		if ($filterType->get('buscar')->isClicked()) {

			$expedientes = $em->getRepository(Expediente::class)->getQbBuscar($filterType->getData());
		} else {

			$expedientes = $em->getRepository(Expediente::class)->getQbExpedientesMesaEntrada();
		}


		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get('page', 1)/* page number */,
			10/* limit per page */
		);

		return $this->render(
			'expediente/index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			)
		);
	}

	/**
	 * Creates a new expediente entity.
	 *
	 */
	public function new(Request $request)
	{
		$expediente = new Expediente();
		$form       = $this->createForm(ExpedienteType::class, $expediente);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($expediente);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);


			return $this->redirectToRoute('expediente_show', array('id' => $expediente->getId()));
		}

		return $this->render(
			'expediente/new.html.twig',
			array(
				'expediente' => $expediente,
				'form'       => $form->createView(),
			)
		);
	}

	/**
	 * Finds and displays a expediente entity.
	 *
	 */
	public function show(Request $request, Expediente $expediente)
	{


		$referer = $request->headers
			->get('referer');
		//
		//		$route = $request->getUri();
		//
		////		TODO is current route
		//		if ($referer == null || ($referer == $route)) {
		//			$referer = $this->generateUrl('expedientes_legislativos_index');
		//		}

		return $this->render(
			'expediente/show.html.twig',
			[
				'expediente' => $expediente,
				'referer'    => $referer
			]
		);
	}

	/**
	 * Displays a form to edit an existing expediente entity.
	 *
	 */
	public function edit(Request $request, Expediente $expediente)
	{


		$iniciadoresOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getIniciadores() as $iniciadorExpediente) {
			$iniciadoresOriginales->add($iniciadorExpediente);
		}

		$editForm = $this->createForm(ExpedienteType::class, $expediente);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {

			$em = $this->getDoctrine()->getManager();

			foreach ($iniciadoresOriginales as $iniciadorExpediente) {
				if (false === $expediente->getIniciadores()->contains($iniciadorExpediente)) {
					// remove the Task from the Tag
					//					$iniciadorExpediente->getExpediente()->removeElement( $expediente );

					// if it was a many-to-one relationship, remove the relationship like this
					// $tag->setTask(null);
					$iniciadorExpediente->setExpediente(null);

					$em->persist($iniciadorExpediente);

					// if you wanted to delete the Tag entirely, you can also do that
					$em->remove($iniciadorExpediente);
				}
			}


			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);

			return $this->redirectToRoute('expediente_edit', array('id' => $expediente->getId()));
		}

		return $this->render(
			'expediente/edit.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
			)
		);
	}

	/**
	 * Deletes a expediente entity.
	 *
	 */
	public function delete(Request $request, Expediente $expediente)
	{
		$form = $this->createDeleteForm($expediente);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($expediente);
			$em->flush();
		}

		return $this->redirectToRoute('expediente_index');
	}

	/**
	 * Creates a form to delete a expediente entity.
	 *
	 * @param Expediente $expediente The expediente entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(Expediente $expediente)
	{
		return $this->createFormBuilder()
			->set($this->generateUrl('expediente_delete', array('id' => $expediente->getId())))
			->setMethod('DELETE')
			->getForm();
	}

	public function seguimientoExpediente(Request $request)
	{

		$filterForm  = $this->createForm(SeguimientoExpedienteFilterType::class);
		$expedientes = array();
		if ($request->isMethod('post')) {
			$filterForm->handleRequest($request);
			$data = $filterForm->getData();
			$em   = $this->getDoctrine()->getManager();
			//            print_r($data);
			//            exit;
			$expedientes = $em->getRepository(Expediente::class)->search($data);
		}

		return $this->render(
			'expediente/seguimiento.html.twig',
			array(
				'filter_form' => $filterForm->createView(),
				'entities'    => $expedientes
			)
		);
	}

	public function seguimientoExpedienteTimeline(Request $request, $id)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);
		if (!$expediente) {
			$this->get('session')->getFlashBag()->add(
				'warning',
				'No existe el expediente'
			);

			return $this->redirectToRoute('app_homepage');
		}
		$girosBae = $em->getRepository(ProyectoBAE::class)->findByExpedienteTimeline($expediente);

		$giros = [];

		foreach ($girosBae as $giroBae) {
			foreach ($giroBae->getGiros() as $itemGiro) {
				$giros[] = $itemGiro;
			}
		}

		foreach ($expediente->getGiros() as $giro) {
			$giros[] = $giro;
		}
		foreach ($expediente->getGiroAdministrativos() as $giroAdministrativo) {
			$giros[] = $giroAdministrativo;
		}
		usort($giros, function ($a, $b) {
			if ($a->getFechaGiro() > $b->getFechaGiro() || $a->getFechaCreacion() > $b->getFechaCreacion()) {
				return 1;
			} else if ($a->getFechaGiro() < $b->getFechaGiro() || $a->getFechaCreacion() > $b->getFechaCreacion()) {
				return -1;
			} else {
				return 0;
			}
		});

		$referer = $request->headers
			->get('referer');

		return $this->render(
			'expediente/timeline.html.twig',
			[
				'expediente' => $expediente,
				'girosBae'   => $girosBae,
				'giros'      => $giros,
				'referer'    => $referer
			]
		);
	}

	public function imprimirCaratula(Pdf $knpSnappyPdf, $id)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);
		$title      = 'Carátula';

		$html = $this->renderView(
			'expediente/caratula.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

		//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml(
				$html,
				array(
					'page-size'      => 'Legal',
					'margin-left'  => "3cm",
					'margin-right' => "3cm",
					'margin-top'   => "3cm",
					//                    'margin-bottom' => "1cm"
				)
			),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}

	public function imprimirGiro(Pdf $knpSnappyPdf, $id, $giroId, $tipoExpediente)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);

		if (strtoupper($tipoExpediente) == 'ADMINISTRATIVO') {
			$giro = $em->getRepository(GiroAdministrativo::class)->find($giroId);
			$giro = $giro->getAreaDestino();
		} elseif (strtoupper($tipoExpediente) == 'LEGISLATIVO') {
			$giro = $em->getRepository(Giro::class)->find($giroId);
			$giro = $giro->getComisionDestino();
		}

		$title = 'Giro';

		$html = $this->renderView(
			'expediente/giro.pdf.twig',
			[
				'expediente' => $expediente,
				'giro'       => $giro,
				'title'      => $title,
			]
		);

		//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml(
				$html,
				array(
					'margin-left'  => "3cm",
					'margin-right' => "3cm",
					'margin-top'   => "3cm",
					//                    'margin-bottom' => "1cm"
				)
			),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}

	//	EXPEDIENTES LEGISLATIVOS

	public function expedientesLegislativosIndex(PaginatorInterface $paginator, Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'externo'
		]);

		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);


		if ($filterType->get('buscar')->isClicked()) {

			$expedientes = $em->getRepository(Expediente::class)->getQbBuscar(
				$filterType->getData(),
				$tipoExpediente
			);

			$expedientes = $em->getRepository(Expediente::class)->getQbExpedientesMesaEntrada($expedientes);
		} else {

			if ($this->get('security.authorization_checker')->isGranted('ROLE_LEGISLATIVO')) {

				$expedientes = $em->getRepository(Expediente::class)->getQbExpedientesLegislativoTipo($tipoExpediente);

				$expedientes = $expedientes->andWhere('e.expediente is not null');
			} else {
				$expedientes = $em->getRepository(Expediente::class)->getQbExpedientesMesaEntradaTipo($tipoExpediente);
				if ($this->get('security.authorization_checker')->isGranted('ROLE_MESA_ENTRADA')) { //si es mesa de entrada solo mostrar los expedientes legislativos
					$expedientes = $expedientes->andWhere('e.expediente is not null or e.expedienteInterno is not null');
				} else {
					$expedientes = $expedientes->andWhere('e.expediente is not null ');
				}
			}
		}

		// Verificar si se solicitó exportar a Excel
		if ($request->query->get('export') === 'excel') {
			// Limitar a 500 registros para evitar problemas de rendimiento
			$expedientes->setMaxResults(500);

			// Obtener resultados para Excel sin paginación
			$expedientesParaExcel = $expedientes->getQuery()->getResult();
			return $this->generarExcelExpedientes($expedientesParaExcel);
		}

		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get('page', 1)/* page number */,
			10/* limit per page */
		);

		return $this->render(
			'expediente/expedientes_legislativos_index.html.twig',
			[
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			]
		);
	}

	/**
	 * Acción pública para exportar expedientes legislativos a Excel
	 * (Esta acción ahora no se usa directamente, pero mantenemos la ruta por compatibilidad)
	 * 
	 * @param Request $request
	 * @return Response
	 */
	public function exportarExpedientesLegislativosExcelAction(Request $request)
	{
		// Redirigir a la página principal con parámetro de exportación
		return $this->redirectToRoute('expedientes_legislativos_index', ['export' => 'excel']);
	}

	/**
	 * Genera un archivo Excel con los expedientes proporcionados
	 * 
	 * @param array $expedientes
	 * @return Response
	 */
	private function generarExcelExpedientes($expedientes)
	{
		$totalRegistros = count($expedientes);
		$limitado = ($totalRegistros >= 500) ? '-limitado' : '';

		// Crear nuevo objeto Spreadsheet
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Establecer encabezados
		$sheet->setCellValue('A1', 'ID');
		$sheet->setCellValue('B1', 'Expediente');
		$sheet->setCellValue('C1', 'Fecha');
		$sheet->setCellValue('D1', 'Fecha Presentación');
		$sheet->setCellValue('E1', 'Extracto');
		$sheet->setCellValue('F1', 'Iniciador');

		// Estilo para encabezados
		$sheet->getStyle('A1:F1')->getFont()->setBold(true);

		// Llenar datos
		$row = 2;
		foreach ($expedientes as $expediente) {
			// Construir el valor del iniciador
			$iniciadorValue = '';
			if ($expediente->getIniciadores()->count() > 0) {
				foreach ($expediente->getIniciadores() as $iniciadorExpediente) {
					if ($iniciadorExpediente->getIniciador()) {
						$iniciadorValue .= $iniciadorExpediente->getIniciador()->getCargoPersona() . ' - ';
					}
				}
			} elseif ($expediente->getIniciadorParticular()) {
				$iniciadorValue = $expediente->getIniciadorParticular()->getNombreCompleto();
			} elseif ($expediente->getDependencia()) {
				$iniciadorValue = $expediente->getDependencia()->__toString();
			}

			$sheet->setCellValue('A' . $row, $expediente->getId());
			$sheet->setCellValue('B' . $row, $expediente->getExpediente() . '-' . $expediente->getLetra() . '-' .
				($expediente->getPeriodoLegislativo() ? $expediente->getPeriodoLegislativo() : $expediente->getAnio()));
			$sheet->setCellValue('C' . $row, $expediente->getFecha() ? $expediente->getFecha()->format('d/m/Y') : '');
			$sheet->setCellValue('D' . $row, $expediente->getFechaPresentacion() ? $expediente->getFechaPresentacion()->format('d/m/Y') : '');
			$sheet->setCellValue('E' . $row, $expediente->getExtracto());
			$sheet->setCellValue('F' . $row, $iniciadorValue);

			$row++;
		}

		// Agregar información sobre el límite si corresponde
		if ($limitado) {
			$sheet->setCellValue('A' . ($row + 1), 'NOTA: Se ha limitado la exportación a 500 registros para optimizar el rendimiento.');
			$sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);
			$sheet->mergeCells('A' . ($row + 1) . ':F' . ($row + 1));
		}

		// Autoajustar anchos de columna
		foreach (range('A', 'F') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Generar un nombre de archivo único
		$filename = 'expedientes_legislativos_' . $totalRegistros . '_registros' . $limitado . '_' . date('Y-m-d_H-i-s') . '.xlsx';

		// Crear el writer y guardar a un archivo temporal
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$tempFile = tempnam(sys_get_temp_dir(), 'excel_');
		$writer->save($tempFile);

		// Retornar el archivo como respuesta
		$response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($tempFile);
		$disposition = \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT;
		$response->setContentDisposition($disposition, $filename);
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$response->deleteFileAfterSend(true);

		return $response;
	}

	public function proyectosIndex(PaginatorInterface $paginator, Request $request)
	{


		if (
			$this->get('security.authorization_checker')->isGranted('ROLE_CONCEJAL') ||
			$this->get('security.authorization_checker')->isGranted('ROLE_DEFENSOR')
		) {

			$autor = $this->getUser()->getPersona()->getCargoPersona()->first()->getIniciador();


			$filterType = $this->createForm(
				ProyectoFilterType::class,
				null,
				[
					'method' => 'GET'
				]
			);

			$filterType->handleRequest($request);


			if ($filterType->get('buscar')->isClicked()) {
				$proyectos = $this->getDoctrine()->getRepository(Expediente::class)->getQbProyecetosPorConcejal(
					$autor,
					$filterType->getData()
				);
			} else {
				$proyectos = $this->getDoctrine()->getRepository(Expediente::class)->getQbProyecetosPorConcejal($autor);
			}


			$proyectos = $paginator->paginate(
				$proyectos,
				$request->query->get('page', 1)/* page number */,
				10/* limit per page */
			);


			return $this->render(
				'expediente/proyectos_index.html.twig',
				[
					'proyectos'   => $proyectos,
					'filter_type' => $filterType->createView()
				]
			);
		} else {
			throw $this->createAccessDeniedException();
		}
	}

	public function showProyecto(Request $request, Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();

		$signatureForm = $this->createForm(FirmaType::class, $expediente);

		$signatureForm->handleRequest($request);

		if ($signatureForm->isSubmitted() && $signatureForm->isValid()) {

			$expediente->setFechaPresentacion(new \DateTime());

			$em->flush();
		}
		return $this->render(
			'expediente/proyecto_show.html.twig',
			[
				'signature_form' => $signatureForm->createView(),
				'expediente' => $expediente,
			]
		);
	}

	public function newProyecto(Request $request, TimeStampManager $TimeStamp)
	{

		$em = $this->getDoctrine()->getManager();

		$expediente = new Expediente();
		$form       = $this->createForm(
			ProyectoType::class,
			$expediente
		);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {


			$toRoute = 'proyecto_edit';
			if ($form->get('guardar')->isClicked()) {
				$expediente->setBorrador(true);
			}

			if ($form->get('guardarYEnviar')->isClicked()) {
				$expediente->setBorrador(false);
				$codigoReferencia = md5(uniqid('hcd_'));
				$expediente->setCodigoReferencia($codigoReferencia);
				$texto = $expediente->getTexto();



				//				Departamento de Mesa de Entradas y Salidas

				$giroAdministrativo = new GiroAdministrativo();
				$areaDestino        = $em->getRepository(AreaAdministrativa::class)->findOneBy([
					'nombre' => 'Departamento de Mesa de Entradas y Salidas'
				]);
				$giroAdministrativo->setAreaDestino($areaDestino);
				$giroAdministrativo->setExpediente($expediente);
				$giroAdministrativo->setFechaGiro(new \DateTime('now'));


				$expediente->addGiroAdministrativo($giroAdministrativo);
				$em->persist($giroAdministrativo);
				$toRoute = 'proyecto_show';
				$TimeStamp->stamp($expediente);
			}

			$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
				'slug' => 'externo'
			]);

			$expediente->setTipoExpediente($tipoExpediente);

			$periodoLegislativo = $em->getRepository(PeriodoLegislativo::class)->findOneBy([
				'anio' => $expediente->getFecha()->format('Y')
			]);

			if (!$periodoLegislativo) {
				$this->get('session')->getFlashBag()->add(
					'warning',
					'No existe periodo legislativo creado. Contacte con el Administrador'
				);

				return $this->render(
					'expediente/proyecto_new.html.twig',
					array(
						'expediente' => $expediente,
						'form'       => $form->createView(),
					)
				);
			}

			$expediente->setPeriodoLegislativo($periodoLegislativo);

			$iniciadorExpediente = new IniciadorExpediente();

			$iniciadorExpediente->setExpediente($expediente);

			$autor = $this->getUser()->getPersona()->getCargoPersona()->first()->getIniciador()->first();
			$iniciadorExpediente->setIniciador($autor);
			$iniciadorExpediente->setAutor(true);

			$expediente->addIniciadore($iniciadorExpediente);


			$em->persist($expediente);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Proyecto creado correctamente'
			);


			return $this->redirectToRoute($toRoute, array('id' => $expediente->getId()));
		}

		return $this->render(
			'expediente/proyecto_new.html.twig',
			array(
				'expediente' => $expediente,
				'form'       => $form->createView(),
			)
		);
	}


	public function editProyecto(Request $request, Expediente $expediente, TimeStampManager $TimeStamp)
	{

		if (
			!$this->get('security.authorization_checker')->isGranted('ROLE_CONCEJAL') &&
			!$this->get('security.authorization_checker')->isGranted('ROLE_LEGISLATIVO') &&
			!$this->get('security.authorization_checker')->isGranted('ROLE_DEFENSOR')
		) {
			$this->get('session')->getFlashBag()->add(
				'warning',
				'No tiene permisos para modificar un Proyecto.'
			);

			return $this->redirectToRoute('app_homepage');
		}

		$em = $this->getDoctrine()->getManager();

		$iniciadoresOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getIniciadores() as $iniciadore) {
			$iniciadoresOriginales->add($iniciadore);
		}

		$anexosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getAnexos() as $anexo) {
			$anexosOriginales->add($anexo);
		}

		$girosAComisionOriginal = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getGiros() as $giro) {
			$girosAComisionOriginal->add($giro);
		}

		$signatureForm = $this->createForm(FirmaType::class, $expediente);

		$editForm = $this->createForm(ProyectoType::class, $expediente);
		if ($this->get('security.authorization_checker')->isGranted('ROLE_LEGISLATIVO')) {
			$editForm->remove('tipoProyecto');
			$editForm->remove('fecha');
		}
		$editForm->handleRequest($request);
		$signatureForm->handleRequest($request);

		if ($signatureForm->isSubmitted() && $signatureForm->isValid()) {
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',
				'Proyecto modificado correctamente'
			);

			return $this->redirectToRoute($toRoute, array('id' => $expediente->getId()));
		}

		if ($editForm->isSubmitted() && $editForm->isValid()) {

			$toRoute = 'proyecto_edit';

			foreach ($iniciadoresOriginales as $iniciadore) {
				if (false === $expediente->getIniciadores()->contains($iniciadore)) {
					$iniciadore->setExpediente(null);
					$em->remove($iniciadore);
				}
			}

			foreach ($anexosOriginales as $anexo) {
				if (false === $expediente->getAnexos()->contains($anexo)) {
					$anexo->setExpediente(null);
					$em->remove($anexo);
				}
			}

			foreach ($girosAComisionOriginal as $giro) {
				if (false === $expediente->getGiros()->contains($giro)) {
					$giro->setExpediente(null);
					$em->remove($giro);
				}
			}

			if ($editForm->get('guardar')->isClicked()) {
				if ($this->get('security.authorization_checker')->isGranted('ROLE_CONCEJAL')) {
					$expediente->setBorrador(true);
				}
			}

			if ($editForm->get('guardarYEnviar')->isClicked()) {
				$expediente->setBorrador(false);
				$codigoReferencia = md5(uniqid('hcd_'));
				$expediente->setCodigoReferencia($codigoReferencia);

				//				Departamento de Mesa de Entradas y Salidas

				$giroAdministrativo = new GiroAdministrativo();
				$areaDestino        = $em->getRepository(AreaAdministrativa::class)->findOneBy([
					'nombre' => 'Departamento de Mesa de Entradas y Salidas'
				]);
				$giroAdministrativo->setAreaDestino($areaDestino);
				$giroAdministrativo->setExpediente($expediente);
				$giroAdministrativo->setFechaGiro(new \DateTime('now'));


				$expediente->addGiroAdministrativo($giroAdministrativo);
				$em->persist($giroAdministrativo);
				$toRoute = 'proyecto_show';
				$TimeStamp->stamp($expediente);
			}

			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',
				'Proyecto modificado correctamente'
			);

			return $this->redirectToRoute($toRoute, array('id' => $expediente->getId()));
		}

		return $this->render(
			'expediente/proyecto_edit.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
				'signature_form' => $signatureForm->CreateView(),
			)
		);
	}

	public function imprimirProyecto(Pdf $knpSnappyPdf, $id, Request $request)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);



		$dataToEncode = $expediente->getCodigoReferencia();
		if ($expediente->getBorrador()) {
			$dataToEncode = null;
		}

		$title = 'Proyecto';

		if (!$expediente->getPeriodoLegislativo()) {
			$this->get('session')->getFlashBag()->add(
				'error',
				'El expediente no tiene periodo legislativo asignado'
			);

			return $this->redirectToRoute('expediente_show', ['id' => $expediente->getId()]);
		}

		$header = null;
		if (!$expediente->getBorrador()) {
			$header = $this->renderView(
				'default/membrete.pdf.twig',
				[
					"periodo"      => $expediente->getPeriodoLegislativo(),
					'dataToEncode' => $dataToEncode
				]
			);
		}

		$footer = $this->renderView('default/pie_pagina.pdf.twig');


		$array = array(
			'page-size'      => 'Legal',
			//					'page-width'     => '220mm',
			//					'page-height'     => '340mm',
			//					'margin-left'    => "3cm",
			//					'margin-right'   => "3cm",
			'margin-top'     => "5cm",
			'margin-bottom'  => "2cm",
			'header-html'    => $header,
			'header-spacing' => 4,
			'footer-spacing' => 5,
			'footer-html'    => $footer,
			//                    'margin-bottom' => "1cm"
		);

		$html = $this->renderView(
			'expediente/proyecto.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

		//        return new Response($html);
		$pdfMerge = new PDFMerger($this->getParameter('kernel.project_dir') . '/public');

		$filesystem = new Filesystem();
		$filesystem->remove('filePDF.pdf');
		$date = new \DateTime();
		$time = $date->getTimeStamp();
		$tmp = sys_get_temp_dir();
		$nombre = $tmp . '/' . $time . '.pdf';

		$knpSnappyPdf->generateFromHtml(
			$html,
			$nombre,
			array(
				'page-size'      => 'Legal',
				//					'page-width'     => '220mm',
				//					'page-height'     => '340mm',
				//					'margin-left'    => "3cm",
				//					'margin-right'   => "3cm",
				'margin-top'     => "5cm",
				'margin-bottom'  => "2cm",
				'header-html'    => $header,
				'header-spacing' => 4,
				'footer-spacing' => 5,
				'footer-html'    => $footer,
				//                    'margin-bottom' => "1cm"

			)
		);


		$pdfMerge->addPDF($nombre);


		foreach ($expediente->getAnexos() as $anexo) {

			$path = $anexo->getAnexo();

			$extension = pathinfo($path);

			$extension = strtolower($extension['extension']);

			if ($extension == 'pdf') {
				$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
			}
		}

		$pdf4 = $pdfMerge->merge('browser', 'pdf3.pdf');


		return new Response(
			$pdf4,
			// array(
			// 	'page-size'      => 'Legal',
			// 	//					'page-width'     => '220mm',
			// 	//					'page-height'     => '340mm',
			// 	//					'margin-left'    => "3cm",
			// 	//					'margin-right'   => "3cm",
			// 	'margin-top'     => "5cm",
			// 	'margin-bottom'  => "2cm",
			// 	'header-html'    => $header,
			// 	'header-spacing' => 4,
			// 	'footer-spacing' => 5,
			// 	'footer-html'    => $footer,
			// 	//                    'margin-bottom' => "1cm"

			// ),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}

	public function imprimirProyectoBlock(Pdf $knpSnappyPdf, $hash, Request $request)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->findOneBy(array('hash' => $hash));



		$dataToEncode = $expediente->getCodigoReferencia();
		if ($expediente->getBorrador()) {
			$dataToEncode = null;
		}

		$title = 'Proyecto';

		if (!$expediente->getPeriodoLegislativo()) {
			$this->get('session')->getFlashBag()->add(
				'error',
				'El expediente no tiene periodo legislativo asignado'
			);

			return $this->redirectToRoute('expediente_show', ['id' => $expediente->getId()]);
		}

		$header = null;
		if (!$expediente->getBorrador()) {
			$header = $this->renderView(
				'default/membrete.pdf.twig',
				[
					"periodo"      => $expediente->getPeriodoLegislativo(),
					'dataToEncode' => $dataToEncode
				]
			);
		}

		$footer = $this->renderView('default/pie_pagina.pdf.twig');


		$array = array(
			'page-size'      => 'Legal',
			//					'page-width'     => '220mm',
			//					'page-height'     => '340mm',
			//					'margin-left'    => "3cm",
			//					'margin-right'   => "3cm",
			'margin-top'     => "5cm",
			'margin-bottom'  => "2cm",
			'header-html'    => $header,
			'header-spacing' => 4,
			'footer-spacing' => 5,
			'footer-html'    => $footer,
			//                    'margin-bottom' => "1cm"
		);

		$html = $this->renderView(
			'expediente/proyecto.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

		//        return new Response($html);
		$pdfMerge = new PDFMerger($this->getParameter('kernel.project_dir') . '/public');

		$filesystem = new Filesystem();
		$filesystem->remove('filePDF.pdf');
		$date = new \DateTime();
		$time = $date->getTimeStamp();
		$tmp = sys_get_temp_dir();
		$nombre = $tmp . '/' . $time . '.pdf';

		$knpSnappyPdf->generateFromHtml(
			$html,
			$nombre,
			array(
				'page-size'      => 'Legal',
				//					'page-width'     => '220mm',
				//					'page-height'     => '340mm',
				//					'margin-left'    => "3cm",
				//					'margin-right'   => "3cm",
				'margin-top'     => "5cm",
				'margin-bottom'  => "2cm",
				'header-html'    => $header,
				'header-spacing' => 4,
				'footer-spacing' => 5,
				'footer-html'    => $footer,
				//                    'margin-bottom' => "1cm"

			)
		);


		$pdfMerge->addPDF($nombre);

		foreach ($expediente->getAnexos() as $anexo) {

			$path = $anexo->getAnexo();

			$extension = pathinfo($path);

			$extension = strtolower($extension['extension']);

			if ($extension == 'pdf') {
				$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
			}
		}

		$pdf4 = $pdfMerge->merge('browser', 'pdf3.pdf');


		return new Response(
			$pdf4,
			array(
				'page-size'      => 'Legal',
				//					'page-width'     => '220mm',
				//					'page-height'     => '340mm',
				//					'margin-left'    => "3cm",
				//					'margin-right'   => "3cm",
				'margin-top'     => "5cm",
				'margin-bottom'  => "2cm",
				'header-html'    => $header,
				'header-spacing' => 4,
				'footer-spacing' => 5,
				'footer-html'    => $footer,
				//                    'margin-bottom' => "1cm"

			),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}

	public function impresionProyecto(Request $request)
	{

		$expediente = null;
		$form       = null;

		if ($request->getMethod() == 'POST') {
			$em = $this->getDoctrine()->getManager();

			$form = $this->createForm(
				AsignarHojasType::class,
				$expediente,
				[
					'action' => $this->generateUrl('expediente_impresion_proyecto')
				]
			);

			$form->handleRequest($request);

			if ($form->get('asignar')->isClicked()) {

				$expediente = $em->getRepository(Expediente::class)->findOneById($request->get('codigoReferencia'));


				$numeroDeHojas = $form->getData()->getNumeroDeHojas();
				$expediente->setNumeroDeHojas($numeroDeHojas);
				$em->flush();

				$this->get('session')->getFlashBag()->add(
					'success',
					'Las hojas se han asignado correctamente'
				);

				return $this->render(
					'expediente/impresion.html.twig',
					array(
						'expediente' => null
					)
				);
			}

			//$codigoReferencia = substr(
			//	$request->get('codigoReferencia'),
			//	7,
			//	strlen($request->get('codigoReferencia'))
			//);
			$codigoReferencia = $request->get('codigoReferencia');
			//$expediente = $em->getRepository(Expediente::class)->findOneByCodigoReferencia($codigoReferencia);
			$expediente = $em->getRepository(Expediente::class)->findOneById($codigoReferencia);
			if (!$expediente) {
				$this->get('session')->getFlashBag()->add(
					'error',
					'El Proyecto no existe o el código no es válido'
				);

				return $this->render(
					'expediente/impresion.html.twig',
					array(
						'expediente' => $expediente
					)
				);
			}


			return $this->render(
				'expediente/impresion.html.twig',
				array(
					'expediente' => $expediente,
					'form'       => $form->createView()
				)
			);
		}

		return $this->render(
			'expediente/impresion.html.twig',
			array(
				'expediente' => $expediente
			)
		);
	}

	public function asignarNumeroExpediente(Request $request, TimeStampManager $TimeStamp)
	{
		$em = $this->getDoctrine()->getManager();


		$expediente       = null;
		$codigoReferencia = null;
		$form             = null;

		if ($request->getMethod() == 'POST') {

			$form = $this->createForm(AsignarNumeroType::class, $expediente);

			$form->handleRequest($request);

			if ($form->get('asignar')->isClicked()) {


				if ($form->isSubmitted() && $form->isValid()) {


					$expediente = $em->getRepository(Expediente::class)->findOneById($request->get('codigoReferencia'));

					$existeExpediente = $em->getRepository(Expediente::class)->findOneBy(
						[
							'expediente'         => $form->getData()->getExpediente(),
							'periodoLegislativo' => $expediente->getPeriodoLegislativo()
						]
					);

					if ($existeExpediente) {
						$form->get('expediente')->addError(new FormError('Ya existe el expediente en el año'));

						return $this->render(
							'expediente/asignar_numero_expediente.html.twig',
							array(
								'expediente'       => $existeExpediente,
								'form'             => $form->createView(),
								'codigoReferencia' => $request->get('codigoReferencia')
							)
						);
					} else {
						//			Prosecretaria Legislativa

						$giroAdministrativo = new GiroAdministrativo();
						$areaDestino        = $em->getRepository('App:AreaAdministrativa')->findOneBy([
							'nombre' => 'Prosecretaria Legislativa'
						]);
						$giroAdministrativo->setAreaDestino($areaDestino);
						$giroAdministrativo->setExpediente($expediente);
						$giroAdministrativo->setFechaGiro(new \DateTime('now'));


						$expediente->addGiroAdministrativo($giroAdministrativo);
						$em->persist($giroAdministrativo);

						$expediente->setExpediente($form->getData()->getExpediente());
						$expediente->setLetra($form->getData()->getLetra());
						$expediente->setFechaPresentacion(new \DateTime('now'));
						$expediente->setAsignadoPor($this->getUser());

						$TimeStamp->stampDefinitivo($expediente);

						$em->flush();



						return $this->redirectToRoute('expediente_show', ['id' => $expediente->getId()]);
					}
				}
			}

			//$codigoReferencia = substr(
			//	$request->get('codigoReferencia'),
			//	7,
			//	strlen($request->get('codigoReferencia'))
			//);

			$codigoReferencia = $request->get('codigoReferencia');

			$expediente = $em->getRepository(Expediente::class)->findOneById($codigoReferencia);


			if ($expediente->getExpediente() && $expediente->getLetra()) {
				$this->get('session')->getFlashBag()->add(
					'info',
					'El Proyecto ya tiene asignado un Número y una Letra'
				);

				return $this->render(
					'expediente/asignar_numero_expediente.html.twig',
					array(
						'expediente'       => $expediente,
						'codigoReferencia' => $codigoReferencia
					)
				);
			}

			if ($form) {
				$form = $form->createView();
			}
		}

		return $this->render(
			'expediente/asignar_numero_expediente.html.twig',
			array(
				'expediente'       => $expediente,
				'form'             => $form,
				'codigoReferencia' => $codigoReferencia
			)
		);
	}

	public function expedienteImprimirEtiqueta(Pdf $knpSnappyPdf, Request $request, $id)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);

		if (!$expediente->getPeriodoLegislativo()) {
			$this->get('session')->getFlashBag()->add(
				'error',
				'El expediente no tiene periodo legislativo asignado'
			);

			return $this->redirectToRoute('expediente_show', ['id' => $expediente->getId()]);
		}

		$html = $this->renderView(
			'expediente/etiqueta.pdf.twig',
			[
				'expediente' => $expediente,
			]
		);


		//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml(
				$html,
				array(
					'margin-left'   => "0mm",
					'margin-right'  => "0mm",
					'margin-top'    => "1mm",
					'margin-bottom' => "0mm",
					'page-width'    => '5cm',
					'page-height'   => '2.5cm'
				)
			),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="etiqueta.pdf"'
			)
		);
	}

	public function cargarExtracto(Request $request, $id)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);
		$form       = $this->createForm(ExpedienteExtractoType::class, $expediente);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Extracto Cargado Correctamentes'
			);

			return $this->redirectToRoute('expedientes_legislativos_index');
		}


		return $this->render(
			'expediente\cargar_extracto.html.twig',
			[
				'expediente' => $expediente,
				'form'       => $form->createView()
			]
		);
	}

	public function nuevoGiro(Request $request, $id)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);
		$form       = $this->createForm(NuevoGiroExpedienteComisionType::class, $expediente);
		$girosBae   = $em->getRepository(ProyectoBAE::class)->findByExpediente($expediente);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'El expediente se ha girado a la/s comision/es'
			);

			return $this->redirectToRoute('expediente_nuevo_giro_legislativo', ['id' => $id]);
		}


		return $this->render(
			'expediente/nuevo_giro_legislativo.html.twig',
			[
				'expediente' => $expediente,
				'girosBae'   => $girosBae,
				'form'       => $form->createView()
			]
		);
	}

	//  EXPEDIENTES ADMINISTRATIVOS

	public function expedientesAdministrativosIndex(PaginatorInterface $paginator, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'interno'
		]);

		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);

		if ($filterType->get('buscar')->isClicked()) {

			$expedientes = $em->getRepository(Expediente::class)->getQbBuscar(
				$filterType->getData(),
				$tipoExpediente
			);
		} else {

			$expedientes = $em->getRepository(Expediente::class)->getQbExpedientesMesaEntradaTipo($tipoExpediente);
		}


		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get('page', 1)/* page number */,
			10/* limit per page */
		);

		return $this->render(
			'expediente/expedientes_administrativos_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			)
		);
	}

	public function expedientesAdministrativosSecretarioIndex(PaginatorInterface $paginator, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa()->getNombre();
		$dependencia = $em->getRepository(Dependencia::class)->findOneBy(['nombre' => $area]);


		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'interno'
		]);

		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);

		if ($filterType->get('buscar')->isClicked()) {

			$expedientes = $em->getRepository(Expediente::class)->getQbBuscar(
				$filterType->getData(),
				$tipoExpediente,
				$dependencia
			);
			$expedientes = $paginator->paginate(
				$expedientes,
				$request->query->get('page', 1)/* page number */,
				10/* limit per page */
			);
		} else {
			if ($dependencia) {
				$expedientes = $em->getRepository(Expediente::class)->findBy(['tipoExpediente' => $tipoExpediente, 'dependencia' => $dependencia]);
				$expedientes = $paginator->paginate(
					$expedientes,
					$request->query->get('page', 1)/* page number */,
					10/* limit per page */
				);
			} else {
				$expedientes = null;
			}
		}




		return $this->render(
			'expediente/expedientes_administrativos_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			)
		);
	}

	public function expedientesAdministrativosSectorIndex(PaginatorInterface $paginator, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa()->getNombre();
		$dependencia = $em->getRepository(Dependencia::class)->findOneBy(['nombre' => $area]);


		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'interno'
		]);

		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);

		if ($filterType->get('buscar')->isClicked()) {

			$expedientes = $em->getRepository(Expediente::class)->getQbBuscar(
				$filterType->getData(),
				$tipoExpediente,
				$dependencia
			);
			$expedientes = $paginator->paginate(
				$expedientes,
				$request->query->get('page', 1)/* page number */,
				10/* limit per page */
			);
		} else {
			if ($dependencia) {
				$expedientes = $em->getRepository(Expediente::class)->findBy(['tipoExpediente' => $tipoExpediente, 'dependencia' => $dependencia], ['id' => 'DESC']);
				$expedientes = $paginator->paginate(
					$expedientes,
					$request->query->get('page', 1)/* page number */,
					10/* limit per page */
				);
			} else {
				$expedientes = null;
			}
		}




		return $this->render(
			'expediente/expedientes_administrativos_sector.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			)
		);
	}

	public function expedientesAdministrativosRecibidosIndex(PaginatorInterface $paginator, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();


		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->add('estado', ChoiceType::class, [
			'choices'  => [
				'Abierto' => 'abierto',
				'Pendiente' => 'pendiente',
				'Rechazado' => 'rechazado',
			],
			'required' => false,
		]);

		$filterType->handleRequest($request);

		if ($filterType->get('buscar')->isClicked()) {

			$giros = $em->getRepository(GiroAdministrativo::class)->getQbBuscarOrigen(
				$filterType->getData(),
				$area
			);
			$giros = $paginator->paginate(
				$giros,
				$request->query->get('page', 1)/* page number */,
				10/* limit per page */
			);
		} else {
			if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRETARIO')) {
				$giros = $em->getRepository(GiroAdministrativo::class)->findBy(['areaDestino' => $area], ['id' => 'DESC'], 300);
			} else {
				$giros = $em->getRepository(GiroAdministrativo::class)->findBy(['areaDestino' => $area], ['id' => 'DESC'], 300);
			}
			$giros = array_filter($giros, function ($giro) {
				return $giro->getExpediente()->getLetra() != null;
			});


			$giros = $paginator->paginate(
				$giros,
				$request->query->get('page', 1)/* page number */,
				10/* limit per page */
			);
		}
		$contador = 0;
		foreach ($giros as $giro) {

			$estado = $giro->getEstado();
			if ($estado == 'pendiente') {
				$contador = $contador + 1;
			}
		}
		if ($contador > 0) {
			$this->get('session')->getFlashBag()->add(
				'info',
				'Expedientes pendientes: ' . $contador . ''
			);
		}


		return $this->render(
			'expediente/expedientes_administrativos_recibidos_index.html.twig',
			array(
				'giros' => $giros,
				'filter_type' => $filterType->createView()
			)
		);
	}

	public function expedientesAdministrativosEnviadosIndex(PaginatorInterface $paginator, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();



		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);

		if ($filterType->get('buscar')->isClicked()) {

			$giros = $em->getRepository(GiroAdministrativo::class)->getQbBuscarDestino(
				$filterType->getData(),
				$area
			);
			$giros = $paginator->paginate(
				$giros,
				$request->query->get('page', 1)/* page number */,
				10/* limit per page */
			);
		} else {

			$giros = $em->getRepository(GiroAdministrativo::class)->findBy(['areaOrigen' => $area], ['id' => 'DESC']);

			$giros = $paginator->paginate(
				$giros,
				$request->query->get('page', 1)/* page number */,
				10/* limit per page */
			);
		}



		return $this->render(
			'expediente/expedientes_administrativos_enviados_index.html.twig',
			array(
				'giros' => $giros,
				'filter_type' => $filterType->createView()
			)
		);
	}


	public function showExpedienteCreado(Expediente $id)
	{
		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();
		$ruta = 'expedientes_administrativos_sector_index';

		if ($area->getNombre() == $id->getDependencia()) {


			return $this->render(
				'expediente/showExpedienteSector.html.twig',
				[
					'ruta' => $ruta,
					'expediente' => $id,
				]
			);
		}
	}

	public function imprimirExpedienteSector(Expediente $id)
	{
		$expediente = $id->getExpedienteInterno();



		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();
		$iniciador = false;
		if ($area->getNombre() == $id->getDependencia()) {
			$iniciador = true;
		}
		$giros = $id->getGiroAdministrativos();


		$areaGiros = false;
		foreach ($giros as $giro) {
			if ($area == $giro->getAreaOrigen() or $area == $giro->getAreaDestino()) {
				$areaGiros = true;
				break;
			}
		}


		if ($areaGiros || $iniciador) {
			$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/internos/' . $expediente;

			// Crear una BinaryFileResponse para el archivo PDF
			$response = new BinaryFileResponse($pdfPath);

			// Configurar la cabecera para forzar la descarga del archivo
			$response->headers->set('Content-Type', 'application/pdf');
			$response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');

			return $response;
		}
	}

	public function imprimirAnexoSector(AnexoExpediente $id)
	{
		$expediente = $id->getAnexo();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

		$giros = $id->getExpediente()->getGiroAdministrativos();

		$iniciador = false;
		if ($area->getNombre() == $id->getExpediente()->getDependencia()) {
			$iniciador = true;
		}
		$areaGiros = false;
		foreach ($giros as $giro) {
			if ($area == $giro->getAreaOrigen() or $area == $giro->getAreaDestino()) {
				$areaGiros = true;
				break;
			}
		}

		if ($areaGiros || $iniciador) {
			$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/anexos/' . $expediente;

			// Crear una BinaryFileResponse para el archivo PDF
			$response = new BinaryFileResponse($pdfPath);

			// Configurar la cabecera para forzar la descarga del archivo
			$response->headers->set('Content-Type', 'application/pdf');
			$response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');

			return $response;
		}
	}


	public function imprimirProyectoFirmado(Expediente $id)
	{
		$expediente = $id->getExpedienteInterno();



		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/internos/' . $expediente;

		// Crear una BinaryFileResponse para el archivo PDF
		$response = new BinaryFileResponse($pdfPath);

		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');

		return $response;
	}

	public function imprimirAnexoGiro(AnexoGiro $id)
	{
		$anexo = $id->getAnexo();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

		$giros = $id->getGiro()->getExpediente()->getGiroAdministrativos();

		$iniciador = false;
		if ($area->getNombre() == $id->getGiro()->getExpediente()->getDependencia()) {
			$iniciador = true;
		}

		$areaGiros = false;
		foreach ($giros as $giro) {
			if ($area == $giro->getAreaOrigen() or $area == $giro->getAreaDestino()) {
				$areaGiros = true;
				break;
			}
		}

		if ($areaGiros || $iniciador) {
			$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/giros/anexos/' . $anexo;

			// Crear una BinaryFileResponse para el archivo PDF
			$response = new BinaryFileResponse($pdfPath);

			// Configurar la cabecera para forzar la descarga del archivo
			$response->headers->set('Content-Type', 'application/pdf');
			$response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');

			return $response;
		}
	}

	public function showExpedienteRecibido(GiroAdministrativo $id)
	{
		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();
		$giro = $id;
		$expediente = $giro->getExpediente();

		if ($area == $giro->getAreaDestino()) {
			$em = $this->getDoctrine()->getManager();
			$rechazar = true;
			if ($giro->getEstado() == 'pendiente' or $giro->getEstado() == null) {
				$giro->setEstado('abierto');
			}
			$em->flush();
			$ruta = 'expedientes_administrativos_sector_recibidos';
			return $this->render(
				'expediente/showSector.html.twig',
				[
					'rechazar' => $rechazar,
					'area' => $area->getNombre(),
					'giro' => $giro,
					'ruta' => $ruta,
					'expediente' => $expediente,
				]
			);
		} elseif ($area == $giro->getAreaOrigen()) {
			$rechazar = false;
			$ruta = 'expedientes_administrativos_sector_enviados';
			return $this->render(
				'expediente/showSector.html.twig',
				[
					'rechazar' => $rechazar,
					'area' => $area->getNombre(),
					'giro' => $giro,
					'ruta' => $ruta,
					'expediente' => $expediente,
				]
			);
		}




		return $this->redirectToRoute('expedientes_administrativos_sector_recibidos');
	}

	public function RechazarExpedienteSector(Request $request, GiroAdministrativo $id)
	{
		$em = $this->getDoctrine()->getManager();

		$giro = $id;

		$form       = $this->createForm(RechazarGiroType::class, $giro);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			if ($giro->getEstado() == 'abierto') {
				$giro->setEstado('rechazado');
				$em->flush();
			}
			return $this->redirectToRoute('expedientes_administrativos_sector_recibidos');
		}


		return $this->render('expediente/rejectExpedienteSector.html.twig', [
			'giro' => $giro,
			'form'       => $form->createView()
		]);
	}


	public function nuevoGiroAdministrativoSector(Request $request, Expediente $id)
	{
		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

		$expediente = $id;
		$em         = $this->getDoctrine()->getManager();
		$giro = new giroAdministrativo;
		$form       = $this->createForm(GiroAdministrativoSectorType::class, $giro);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$date = new \DateTime();
			$giro->setFechaGiro($date);
			$giro->setEstado('pendiente');
			$giro->setAreaOrigen($area);
			$em->persist($giro);
			$expediente->addGiroAdministrativo($giro);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'El expediente se ha girado a la/s dependencia/s'
			);

			return $this->redirectToRoute('expedientes_administrativos_sector_enviados');
		}


		return $this->render(
			'expediente/nuevo_giro_sector.html.twig',
			[
				'expediente' => $expediente,
				'form'       => $form->createView()
			]
		);
	}

	public function nuevoExpedienteAdministrativoSector(Request $request, TimeStampManager $TimeStamp)
	{

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'interno'
		]);
		$periodo = $em->getRepository(PeriodoLegislativo::class)->findOneBy(['anio' => date('Y')]);


		$expediente = new Expediente();
		$expediente->setTipoExpediente($tipoExpediente);



		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa()->getNombre();
		$dependecia = $em->getRepository(Dependencia::class)->findOneBy(['nombre' => $area]);

		$periodo = $em->getRepository(PeriodoLegislativo::class)->findOneBy(['anio' => date('Y')]);
		if (!$dependecia) {
			$dependecia = new Dependencia();
			$dependecia->setNombre($area);
			$em->persist($dependecia);
			$em->flush();
		}
		$expediente->setFechaPresentacion(new \DateTime());
		$expediente->setDependencia($dependecia);
		$expediente->setPeriodoLegislativo($periodo);

		$mesa        = $em->getRepository(AreaAdministrativa::class)->findOneBy([
			'nombre' => 'Departamento de Mesa de Entradas y Salidas'
		]);
		$form = $this->createForm(ExpedienteAdministrativoSectorType::class, $expediente);

		$form->handleRequest($request);
		$numero = 0;
		if ($form->isSubmitted() && $form->isValid()) {
			$ultimo = $em->getRepository(Expediente::class)->findOneBy(['periodoLegislativo' => $periodo], ['expediente' => 'DESC']);

			if ($ultimo) {
				$numero = $ultimo->getExpediente();
			}

			$nuevo = $numero + 1;
			$existe = $em->getRepository(Expediente::class)->findOneBy(['expediente' => $nuevo, 'periodoLegislativo' => $periodo]);
			if ($existe ==  null) {
				$existe = $em->getRepository(ExpedienteBloqueado::class)->findOneBy(['numero' => $nuevo, 'ano' => date('Y')]);
			}

			while ($existe != null) {
				$nuevo = $nuevo + 1;
				$existe = $em->getRepository(Expediente::class)->findOneBy(['expediente' => $nuevo]);
				if ($existe ==  null) {
					$existe = $em->getRepository(ExpedienteBloqueado::class)->findOneBy(['numero' => $nuevo, 'ano' => date('Y')]);
				}
			}
			$giro = new giroAdministrativo;

			$date = new \DateTime();
			$giro->setFechaGiro($date);
			$giro->setEstado('pendiente');
			$giro->setAreaDestino($mesa);
			$em->persist($giro);

			$expediente->addGiroAdministrativo($giro);

			$expediente->setExpediente($nuevo);

			$expediente->setFecha($date);
			$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();
			foreach ($expediente->getGiroAdministrativos() as $giro) {

				$giro->setAreaOrigen($area);
				$giro->setFechaGiro($date);
				$giro->setEstado('pendiente');
			}
			$TimeStamp->stamp($expediente);

			$em->persist($expediente);



			$em->flush();

			// 			foreach ($expediente->getGirosAdministatrivos() as $giro){
			// /* 			$cargo = $em->getRepository( CargoPersona::class )->findOneByAreaAdministrativa( $giro->getAreaDestino() ); 
			// 			$user  = $em->getRepository( User::class )->findOneByPersona($cargo->getPersona());
			// 			$mail = $user->getEmail();
			// 			*/
			// 			$email=$giro->getAreaDestino()->getEmail();

			// 			$email=false;
			// 			if ( $email ) {
			// 				$asunto = 'HCD Posadas - Expediente Administrativo ' . $expediente->getNumero().' '. $expediente->getLetra(). ' '. $expediente->getAno().'';

			// 				$email = ( new TemplatedEmail() )
			// 					->from( new Address( $_ENV['EMAIL_FROM'], $_ENV['EMAIL_FROM_NAME'] ) )
			// 					->to( $mail )
			// 					->subject( $asunto )
			// 					->htmlTemplate( 'emails/plan_de_labor.html.twig' )
			// 					->context( [
			// 						'sesion' => '1'
			// 					] );

			// 				try {
			// 					$mailer->send($email);
			// 					return true;
			// 				} catch (TransportExceptionInterface $e) {
			// 					// some error prevented the email sending; display an
			// 					// error message or try to resend the message
			// 					return false;
			// 				}

			// 			} 
			// 		} 
			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);

			return $this->redirectToRoute('expedientes_administrativos_sector_index');
		}

		return $this->render(
			'expediente/new_administrativo_sector.html.twig',
			[
				'edit' => false,
				'form'       => $form->createView(),
				'expediente' => $expediente
			]
		);
	}

	public function bloqueadoIndex(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$bloqueados = $em->getRepository(ExpedienteBloqueado::class)->findAll();
		$bloqueado = new ExpedienteBloqueado;
		$periodo = $em->getRepository(PeriodoLegislativo::class)->findOneBy(['anio' => date('Y')]);

		$form = $this->createForm(BloqueadoType::class, $bloqueado);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$bloqueado->setAno(date('Y'));
			$repetido = $em->getRepository(ExpedienteBloqueado::class)->findOneBy(['ano' => date('Y'), 'numero' => $bloqueado->getNumero()]);
			$existe = $em->getRepository(Expediente::class)->findOneBy(['periodoLegislativo' => $periodo, 'expediente' => $bloqueado->getNumero()]);
			if ($existe) {
				$this->get('session')->getFlashBag()->add(
					'warning',
					'Expediente ya existe'
				);
				return $this->redirectToRoute('expedientes_bloqueados');
			}
			if ($repetido) {
				$this->get('session')->getFlashBag()->add(
					'warning',
					'Expediente ya esta bloqueado'
				);
				return $this->redirectToRoute('expedientes_bloqueados');
			}
			$em->persist($bloqueado);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente bloqueado correctamente'
			);

			return $this->redirectToRoute('expedientes_bloqueados');
		}

		return $this->render(
			'expediente/expedientesBloqueados.html.twig',
			[

				'form'       => $form->createView(),
				'bloqueados' => $bloqueados
			]
		);
	}

	public function deleteBloqueado(ExpedienteBloqueado $id)
	{
		$em = $this->getDoctrine()->getManager();

		$em->remove($id);

		$em->flush();
		$this->get('session')->getFlashBag()->add(
			'success',
			'Bloqueo eliminado correctamente'
		);

		return $this->redirectToRoute('expedientes_bloqueados');
	}


	//agregar block
	public function nuevoExpedienteAdministrativo(Request $request)
	{

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'interno'
		]);

		$expediente = new Expediente();
		$expediente->setTipoExpediente($tipoExpediente);

		$form = $this->createForm(ExpedienteAdministrativoType::class, $expediente);


		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$$expediente->setFechaPresentacion(new \DateTime());
			$em->persist($expediente);
			$em->flush();


			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);
			return $this->redirectToRoute('expediente_administrativo_editar', ['id' => $expediente->getId()]);
		}

		return $this->render(
			'expediente/new_administrativo.html.twig',
			[
				'edit' => false,
				'form'       => $form->createView(),
				'expediente' => $expediente
			]
		);
	}

	public function editarExpedienteAdministrativo(Request $request, Expediente $expediente)
	{

		$form = $this->createForm(ExpedienteAdministrativoType::class, $expediente);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($expediente);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);
			return $this->redirectToRoute('expedientes_administrativos_index');
		}

		return $this->render(
			'expediente/new_administrativo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente,
				'edit' => true,
			]
		);
	}

	public function nuevoGiroAdministrativo(Request $request, $id)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);
		$form       = $this->createForm(NuevoGiroExpedienteDependenciaType::class, $expediente);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'El expediente se ha girado a la/s dependencia/s'
			);

			return $this->redirectToRoute('expedientes_administrativos_index');
		}


		return $this->render(
			'expediente/nuevo_giro_administrativo.html.twig',
			[
				'expediente' => $expediente,
				'form'       => $form->createView()
			]
		);
	}

	//  EXPEDIENTES LEGISLATIVOS EXTERNOS

	public function expedientesLegislativoExternosIndex(PaginatorInterface $paginator, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'externo'
		]);

		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);

		if ($filterType->get('buscar')->isClicked()) {

			$expedientes = $em->getRepository(Expediente::class)->getQbBuscarExpedientesLegislativosExternos(
				$filterType->getData(),
				$tipoExpediente
			);
		} else {

			$expedientes = $em->getRepository(Expediente::class)->getQbExpedientesLegislativosExternos($tipoExpediente);
		}


		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get('page', 1)/* page number */,
			10/* limit per page */
		);

		return $this->render(
			'expediente/expedientes_legislativos_externos_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			)
		);
	}
	//agregar block
	public function nuevoExpedienteLegislativoExterno(Request $request)
	{

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'externo'
		]);

		$expediente = new Expediente();
		$expediente->setTipoExpediente($tipoExpediente);

		$form = $this->createForm(ExpedienteLegislativoExternoType::class, $expediente);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$expediente->setBorrador(false);
			$expediente->setFechaPresentacion(new \DateTime());
			$em->persist($expediente);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);

			return $this->redirectToRoute('expediente_legislativo_externo_editar', ['id' => $expediente->getId()]);
		}

		return $this->render(
			'expediente/new_legislativo_externo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente
			]
		);
	}

	public function editarExpedienteLegislativoExterno(Request $request, Expediente $expediente)
	{

		$form = $this->createForm(ExpedienteLegislativoExternoType::class, $expediente);

		$em = $this->getDoctrine()->getManager();

		$girosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getGiros() as $giro) {
			$girosOriginales->add($giro);
		}

		$girosAdministrativosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getGiroAdministrativos() as $giroAdministrativo) {
			$girosAdministrativosOriginales->add($giroAdministrativo);
		}

		$adjuntosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getExpedientesAdjunto() as $expAdj) {
			$adjuntosOriginales->add($expAdj);
		}

		$iniciadoresOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getIniciadores() as $iniciador) {
			$iniciadoresOriginales->add($iniciador);
		}


		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			foreach ($girosOriginales as $giro) {
				if (false === $expediente->getGiros()->contains($giro)) {
					$giro->setExpediente(null);
					$em->remove($giro);
				}
			}

			foreach ($girosAdministrativosOriginales as $giroAdministrativo) {
				if (false === $expediente->getGiroAdministrativos()->contains($giroAdministrativo)) {
					$giroAdministrativo->setExpediente(null);
					$em->remove($giroAdministrativo);
				}
			}

			foreach ($adjuntosOriginales as $expAdj) {
				if (false === $expediente->getExpedientesAdjunto()->contains($expAdj)) {
					$expAdj->setExpediente(null);
					$em->remove($expAdj);
				}
			}

			foreach ($iniciadoresOriginales as $iniciador) {
				if (false === $expediente->getIniciadores()->contains($iniciador)) {
					$iniciador->setExpediente(null);
					$em->remove($iniciador);
				}
			}

			$em->persist($expediente);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);

			return $this->redirectToRoute('expediente_legislativo_externo_editar', ['id' => $expediente->getId()]);
		}

		return $this->render(
			'expediente/new_legislativo_externo.html.twig',
			[
				'form' => $form->createView(),
				'edit' => true
			]
		);
	}

	//  EXPEDIENTES ADMINISTRATIVOS EXTERNOS

	public function expedientesAdministrativoExternosIndex(PaginatorInterface $paginator, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'interno'
		]);

		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);

		if ($filterType->get('buscar')->isClicked()) {

			$expedientes = $em->getRepository(Expediente::class)->getQbBuscar(
				$filterType->getData(),
				$tipoExpediente
			);
		} else {

			$expedientes = $em->getRepository(Expediente::class)->getQbExpedientesMesaEntradaTipo($tipoExpediente);
		}


		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get('page', 1)/* page number */,
			10/* limit per page */
		);

		return $this->render(
			'expediente/expedientes_administrativos_externos_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			)
		);
	}
	//agregar block
	public function nuevoExpedienteAdministrativoExterno(Request $request)
	{

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'interno'
		]);

		$expediente = new Expediente();
		$expediente->setTipoExpediente($tipoExpediente);

		$form = $this->createForm(ExpedienteAdministrativoExternoType::class, $expediente);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$expediente->setBorrador(false);
			$expediente->setFechaPresentacion(new \DateTime());
			$em->persist($expediente);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);

			return $this->redirectToRoute(
				'expediente_administrativo_externo_editar',
				['id' => $expediente->getId()]
			);
		}

		return $this->render(
			'expediente/new_administrativo_externo.html.twig',
			[
				'edit' => false,
				'form'       => $form->createView(),
				'expediente' => $expediente
			]
		);
	}

	public function editarExpedienteAdministrativoExterno(Request $request, Expediente $expediente)
	{

		$form = $this->createForm(ExpedienteAdministrativoExternoType::class, $expediente);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($expediente);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);

			return $this->redirectToRoute(
				'expediente_administrativo_externo_editar',
				['id' => $expediente->getId()]
			);
		}

		return $this->render(
			'expediente/new_administrativo_externo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente,
				'edit' => true,
			]
		);
	}

	/**
	 * @param Request $request
	 * @param Expediente $expediente
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editarExtracto(Request $request, Expediente $expediente)
	{
		$this->denyAccessUnlessGranted('ROLE_LEGISLATIVO', null, 'No tiene permiso para acceder a esta opción.');

		// Estos son los campos a auditar en el log
		$campos = ['extracto'];

		$valoresOriginales = [];
		foreach ($campos as $campo) {
			$getter                      = 'get' . ucfirst($campo);
			$valoresOriginales[$campo] = [
				'valor'  => $expediente->{$getter}(),
				'getter' => $getter,
			];
		}

		$editForm = $this->createForm(EditarExtractoType::class, $expediente);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$log = Log::forEntity($expediente);
			foreach ($valoresOriginales as $nombre => $campo) {
				if ($campo['valor'] != $expediente->{$campo['getter']}()) {
					$log->agregarCambio($nombre, $campo['valor'], $expediente->{$campo['getter']}());
				}
			}

			$em = $this->getDoctrine()->getManager();
			if ($log->hasCambios()) {
				$em->persist($log);
			}
			$em->persist($expediente);
			$em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Extracto guardado correctamente'
			);

			return $this->redirectToRoute('expedientes_legislativos_index');
		}

		return $this->render(
			'expediente/editarExtracto.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
				'logs'       => []
			)
		);
	}

	/**
	 * @param Request $request
	 * @param Expediente $expediente
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function showEdicionExtracto(
		Request $request,
		Expediente $expediente,
		Log $logExpediente
	) {
		$this->denyAccessUnlessGranted('ROLE_LEGISLATIVO', null, 'No tiene permiso para acceder a esta opción.');

		return $this->render(
			'expediente/showEdicionExtracto.html.twig',
			array(
				'expediente'    => $expediente,
				'logExpediente' => $logExpediente,
			)
		);
	}


	public function incorporarProyectosASesionIndex(PaginatorInterface $paginator, Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository(TipoExpediente::class)->findOneBy([
			'slug' => 'externo'
		]);

		$filterType = $this->createForm(
			ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);


		if ($filterType->get('buscar')->isClicked()) {

			$expedientes = $em->getRepository(Expediente::class)->getQbBuscar(
				$filterType->getData(),
				$tipoExpediente
			);
		} else {

			$expedientes = $em->getRepository(Expediente::class)->getQbExpedientesLegislativoTipo($tipoExpediente);

			$expedientes = $expedientes->andWhere('e.expediente is not null');
		}


		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get('page', 1)/* page number */,
			10/* limit per page */
		);

		return $this->render(
			'expediente/incorporar_expedientes_a_sesion_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			)
		);
	}

	public function asignarGiroComision(Request $request, Expediente $expediente)
	{

		if (
			!$this->get('security.authorization_checker')->isGranted('ROLE_CONCEJAL') &&
			!$this->get('security.authorization_checker')->isGranted('ROLE_LEGISLATIVO') &&
			!$this->get('security.authorization_checker')->isGranted('ROLE_DEFENSOR')
		) {
			$this->get('session')->getFlashBag()->add(
				'warning',
				'No tiene permisos para modificar un Proyecto.'
			);

			return $this->redirectToRoute('app_homepage');
		}

		$em = $this->getDoctrine()->getManager();


		$girosAComisionOriginal = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getGiros() as $giro) {
			$girosAComisionOriginal->add($giro);
		}

		$girosAdministrativosOriginal = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ($expediente->getGiroAdministrativos() as $giroAdministrativo) {
			$girosAdministrativosOriginal->add($giroAdministrativo);
		}


		$editForm = $this->createForm(ExpedienteType::class, $expediente);
		if ($this->get('security.authorization_checker')->isGranted('ROLE_LEGISLATIVO')) {
			$editForm->remove('tipoProyecto');
			$editForm->remove('fecha');
		}
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {

			$toRoute = 'expediente_legislativo_externo_giro';


			foreach ($girosAComisionOriginal as $giro) {
				if (false === $expediente->getGiros()->contains($giro)) {
					$giro->setExpediente(null);
					$em->remove($giro);
				}
			}

			foreach ($girosAdministrativosOriginal as $giroAdministrativo) {
				if (false === $expediente->getGiroAdministrativos()->contains($giroAdministrativo)) {
					$giroAdministrativo->setExpediente(null);
					$em->remove($giroAdministrativo);
				}
			}

			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);

			return $this->redirectToRoute($toRoute, ['id' => $expediente->getId()]);
		}

		return $this->render(
			'expediente/asignar_giro.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
			)
		);
	}


	function imprimirArchivo(Pdf $knpSnappyPdf, Pdf $knpSnappyPdf2, Expediente $expediente, LoggerInterface $logger = null)
	{
		// Usar el logger de Symfony
		if (!$logger) {
			$logger = $this->get('logger');
		}

		$logger->info("=== INICIO imprimirArchivo", [
			'expediente_id' => $expediente->getId(),
			'function' => 'imprimirArchivo'
		]);

		$em = $this->getDoctrine()->getManager();
		$decreto = $expediente->getIsDecreto();
		$logger->info("Verificando decreto", [
			'expediente_id' => $expediente->getId(),
			'es_decreto' => $decreto
		]);

		$DictamenRepository = $em->getRepository(Dictamen::class);

		$Dictamenes = $DictamenRepository->findBy(
			['expediente' => $expediente->getId()],
			['id' => 'ASC']
		);
		$logger->info("Dictamenes obtenidos", [
			'expediente_id' => $expediente->getId(),
			'cantidad_dictamenes' => count($Dictamenes)
		]);

		// Para debugging inmediato, descomenta esta línea:
		// dd("DEBUG: Función iniciada correctamente, expediente ID: " . $expediente->getId());

		//CARATULA
		$logger->info("Iniciando generación de carátula", [
			'expediente_id' => $expediente->getId()
		]);

		$dataToEncode = $expediente->getCodigoReferencia();
		if ($expediente->getBorrador()) {
			$dataToEncode = null;
		}
		$logger->info("Código de referencia verificado", [
			'expediente_id' => $expediente->getId(),
			'codigo_referencia' => $dataToEncode,
			'es_borrador' => $expediente->getBorrador()
		]);

		$title      = 'Carátula';

		try {
			$html = $this->renderView(
				'expediente/caratula.pdf.twig',
				[
					'expediente' => $expediente,
					'title'      => $title,
				]
			);
			$logger->info("HTML de carátula generado correctamente", [
				'expediente_id' => $expediente->getId()
			]);
		} catch (\Exception $e) {
			$logger->error("Error generando HTML de carátula", [
				'expediente_id' => $expediente->getId(),
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
			throw $e;
		}

		$pdfMerge = new PDFMerger($this->getParameter('kernel.project_dir') . '/public');

		$filesystem = new Filesystem();
		$filesystem->remove('filePDF.pdf');
		$date = new \DateTime();
		$time = $date->getTimeStamp();
		$tmp = sys_get_temp_dir();
		$nombre = $tmp . '/Caratula' . $time . '.pdf';
		error_log("Archivo temporal carátula: " . $nombre);

		try {
			$knpSnappyPdf->generateFromHtml(
				$html,
				$nombre,
				array(
					'page-size'      => 'Legal',
					//					'page-width'     => '220mm',
					//					'page-height'     => '340mm',
					//					'margin-left'    => "3cm",
					//					'margin-right'   => "3cm",
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'header-spacing' => 4,
					'footer-spacing' => 5,
					//                    'margin-bottom' => "1cm"

				)
			);
			error_log("PDF de carátula generado correctamente: " . $nombre);
		} catch (\Exception $e) {
			error_log("Error generando PDF de carátula: " . $e->getMessage());
			throw $e;
		}

		$esRM = strpos($expediente->getExpediente(), 'RM') !== false;
		error_log("Es RM: " . ($esRM ? 'true' : 'false'));

		if (!$esRM) {
			$pdfMerge->addPDF($nombre);
			error_log("Carátula agregada al PDF merge");
		} else {
			error_log("No se agrega carátula (es RM)");
		}

		//PROYECTO SIN FIRMAR
		$logger->info("Verificando tipo de proyecto", [
			'expediente_id' => $expediente->getId(),
			'expediente_interno' => $expediente->getExpedienteInterno(),
			'tiene_expediente_interno' => !empty($expediente->getExpedienteInterno())
		]);

		if (!$expediente->getExpedienteInterno()) {
			$logger->info("Procesando proyecto SIN FIRMAR", [
				'expediente_id' => $expediente->getId()
			]);
			$header = null;
			if (!$expediente->getBorrador()) {
				$header = $this->renderView(
					'default/membrete.pdf.twig',
					[
						"periodo"      => $expediente->getPeriodoLegislativo(),
						'dataToEncode' => $dataToEncode
					]
				);
			}
			$footer = $this->renderView('default/pie_pagina.pdf.twig');

			$array = array(
				'page-size'      => 'Legal',
				//					'page-width'     => '220mm',
				//					'page-height'     => '340mm',
				//					'margin-left'    => "3cm",
				//					'margin-right'   => "3cm",
				'margin-top'     => "5cm",
				'margin-bottom'  => "2cm",
				'header-html'    => $header,
				'header-spacing' => 4,
				'footer-spacing' => 5,
				'footer-html'    => $footer,
				//                    'margin-bottom' => "1cm"
			);

			//        return new Response($html);
			$title = 'Proyecto';

			try {
				$html = $this->renderView(
					'expediente/proyecto.pdf.twig',
					[
						'expediente' => $expediente,
						'title'      => $title,
					]
				);
				error_log("HTML de proyecto sin firmar generado correctamente");
			} catch (\Exception $e) {
				error_log("Error generando HTML de proyecto sin firmar: " . $e->getMessage());
				throw $e;
			}


			$date = new \DateTime();
			$time = $date->getTimeStamp();
			$nombre = $tmp . '/Firmado' . $time . '.pdf';

			$knpSnappyPdf->generateFromHtml(
				$html,
				$nombre,
				array(
					'page-size'      => 'Legal',
					//					'page-width'     => '220mm',
					//					'page-height'     => '340mm',
					//					'margin-left'    => "3cm",
					//					'margin-right'   => "3cm",
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'header-html'    => $header,
					'header-spacing' => 4,
					'footer-spacing' => 5,
					'footer-html'    => $footer,
					//                    'margin-bottom' => "1cm"

				)
			);



			//$pdfMerge->addPDF('uploads/expedientes/anexos/'.$archivo);

			$pdfMerge->addPDF($nombre);

			foreach ($expediente->getAnexos() as $anexo) {

				$path = $anexo->getAnexo();

				$extension = pathinfo($path);

				$extension = strtolower($extension['extension']);

				if ($extension == 'pdf') {
					$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
				}
			}
		} else {
			$logger->info("Procesando proyecto FIRMADO", [
				'expediente_id' => $expediente->getId()
			]);

			//PROYECTO FIRMADO
			$path = $expediente->getExpedienteInterno();
			$logger->info("Obteniendo ruta expediente interno", [
				'expediente_id' => $expediente->getId(),
				'ruta_expediente_interno' => $path
			]);

			$extension = pathinfo($path);

			$extension = strtolower($extension['extension']);

			if ($extension == 'pdf') {
				$pdfMerge->addPDF('uploads/expedientes/internos/' . $path);
			}

			if ($decreto || $expediente->getTipoExpediente()->getId() == 1) {
				foreach ($expediente->getAnexos() as $anexo) {

					$path = $anexo->getAnexo();

					if ($path) {

						$extension = pathinfo($path);

						$extension = strtolower($extension['extension']);

						if ($extension == 'pdf') {
							$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
						}
					}
				}
				$contador = 0;
				foreach ($expediente->getGiroAdministrativos() as $anexo) {
					$contador = $contador + 1;



					$path = $anexo->getAnexo();

					if ($path) {
						$extension = pathinfo($path);
						$extension = strtolower($extension['extension']);
						if ($extension == 'pdf') {
							$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
						}
					}


					foreach ($anexo->getAnexoGiros() as $anexoGiro) {

						$path = $anexoGiro->getAnexo();

						if ($path) {
							$extension = pathinfo($path);
							$extension = strtolower($extension['extension']);
							if ($extension == 'pdf') {
								$pdfMerge->addPDF('uploads/giros/anexos/' . $path);
							}
						}
					}

					$title = 'Giro';

					$html = $this->renderView(
						'expediente/giroSector.pdf.twig',
						[
							'expediente' => $expediente,
							'giro'       => $anexo,
							'title'      => $title,
						]
					);
					$date = new \DateTime();
					$time = $date->getTimeStamp();
					$nombre = $tmp . '/Giro' . $time . $contador . '.pdf';

					$knpSnappyPdf->generateFromHtml(
						$html,
						$nombre,
						array(
							'page-size'      => 'Legal',
							//					'page-width'     => '220mm',
							//					'page-height'     => '340mm',
							//					'margin-left'    => "3cm",
							//					'margin-right'   => "3cm",
							'margin-top'     => "5cm",
							'margin-bottom'  => "2cm",
							'header-spacing' => 4,
							'footer-spacing' => 5,
							//                    'margin-bottom' => "1cm"

						)
					);

					$pdfMerge->addPDF($nombre);
				}

				$pdf4 = $pdfMerge->merge('browser', 'pdf3.pdf');


				return new Response(
					$pdf4,
					array(
						'page-size'      => 'Legal',
						//					'page-width'     => '220mm',
						//					'page-height'     => '340mm',
						//					'margin-left'    => "3cm",
						//					'margin-right'   => "3cm",
						'margin-top'     => "5cm",
						'margin-bottom'  => "2cm",
						'header-spacing' => 4,
						'footer-spacing' => 5,
						//                    'margin-bottom' => "1cm"

					),
					200,
					array(
						'Content-Type'        => 'application/pdf',
						'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
					)
				);
			}
		}



		foreach ($expediente->getProveidos() as $proveido) {

			/*		$caratula=$proveido->getCaratula();
 		if($caratula){
			$pdfMerge->addPDF('uploads/expedientes/proveido/caratula/'.$caratula);
		} */

			$archivo = $proveido->getArchivo();
			if ($archivo) {
				$pdfMerge->addPDF('uploads/expedientes/proveido/' . $archivo);
			}

			/* 		$cierre=$proveido->getCierre();
		if($cierre){
			$pdfMerge->addPDF('uploads/expedientes/proveido/cierre/'.$cierre);
		} */
		}

		$proyectoBaeRepository = $em->getRepository(ProyectoBAE::class);

		$ProyectosBae = $proyectoBaeRepository->findBy(
			['expediente' => $expediente->getId()],
			['id' => 'ASC']
		);
		$logger->info("Iniciando procesamiento de ProyectosBae", [
			'expediente_id' => $expediente->getId(),
			'cantidad_proyectos_bae' => count($ProyectosBae)
		]);

		if ($ProyectosBae) {
			$firstProyectoBae = $ProyectosBae[0];
			$logger->info("Primer ProyectoBae obtenido", [
				'expediente_id' => $expediente->getId(),
				'proyecto_bae_id' => $firstProyectoBae->getId(),
				'es_informe_dem' => $firstProyectoBae->getEsInformeDem()
			]);

			// Si NO es informe DEM, incluir los archivos de giro, pedido y digesto
			if (!$firstProyectoBae->getEsInformeDem()) {
				$logger->info("Procesando archivos del ProyectoBae (NO es informe DEM)", [
					'expediente_id' => $expediente->getId(),
					'proyecto_bae_id' => $firstProyectoBae->getId()
				]);
				if ($firstProyectoBae->getFirmado()) {
					//GIRO FIRMADO
					$logger->info("Agregando giro firmado al PDF", [
						'expediente_id' => $expediente->getId(),
						'proyecto_bae_id' => $firstProyectoBae->getId(),
						'archivo_giro' => $firstProyectoBae->getFirmado()
					]);
					$pdfMerge->addPDF('uploads/expedientes/comision/giro/' . $firstProyectoBae->getFirmado());
				} else {
					//GIRO SIN FIRMAR
					$logger->info("Procesando giro SIN FIRMAR", [
						'expediente_id' => $expediente->getId(),
						'proyecto_bae_id' => $firstProyectoBae->getId()
					]);
					$giros = $firstProyectoBae->getGirosOrdenados();
					if ($giros) {
						$expediente = $firstProyectoBae->getExpediente();
						$sesion = $firstProyectoBae->getBoletinAsuntoEntrado()->getSesion();

						$titulo = "Giro " . $expediente->getExpediente() . "-" . $expediente->getLetra() . "-" . $expediente->getPeriodoLegislativo()->getAnio();
						$fecha = $sesion->getFecha();


						$html = $this->renderView(
							'comision/giroComision.pdf.twig',
							[
								'expediente' => $expediente,
								'sesion' => $sesion,
								'title'      => $titulo,
								'giros'      => $giros,
								'fecha'      => $fecha,

							]
						);

						$date = new \DateTime();
						$time = $date->getTimeStamp();
						$nombre = $tmp . '/Giro' . $time . '.pdf';
						$knpSnappyPdf->generateFromHtml(
							$html,
							$nombre,
							array(
								'page-size'      => 'Legal',
								//					'page-width'     => '220mm',
								//					'page-height'     => '340mm',
								//					'margin-left'    => "3cm",
								//					'margin-right'   => "3cm",
								'margin-top'     => "5cm",
								'margin-bottom'  => "2cm",
								//                    'margin-bottom' => "1cm"

							)
						);
						$pdfMerge->addPDF($nombre);
					}
				}
				if ($firstProyectoBae->getPedido()) {
					//PEDIDO FIRMADO
					$logger->info("Agregando pedido firmado al PDF", [
						'expediente_id' => $expediente->getId(),
						'proyecto_bae_id' => $firstProyectoBae->getId(),
						'archivo_pedido' => $firstProyectoBae->getPedido()
					]);
					$pdfMerge->addPDF('uploads/expedientes/pedido/' . $firstProyectoBae->getPedido());
				}
				if ($firstProyectoBae->getDigesto()) {
					//DIGESTO FIRMADO
					$logger->info("Agregando digesto firmado al PDF", [
						'expediente_id' => $expediente->getId(),
						'proyecto_bae_id' => $firstProyectoBae->getId(),
						'archivo_digesto' => $firstProyectoBae->getDigesto()
					]);
					$pdfMerge->addPDF('uploads/expedientes/digesto/' . $firstProyectoBae->getDigesto());
				}
			} else {
				$logger->info("OMITIENDO archivos giro/pedido/digesto (ES informe DEM)", [
					'expediente_id' => $expediente->getId(),
					'proyecto_bae_id' => $firstProyectoBae->getId(),
					'es_informe_dem' => true
				]);
			}

			$logger->info("Iniciando procesamiento de Dictamenes", [
				'expediente_id' => $expediente->getId(),
				'cantidad_dictamenes' => count($Dictamenes)
			]);
			if ($Dictamenes) {
				$logger->info("Procesando dictámenes", [
					'expediente_id' => $expediente->getId(),
					'cantidad' => count($Dictamenes)
				]);
				$firstDictamen = $Dictamenes[0];
				foreach ($expediente->getExpedientesAdjunto() as $adjunto) {
					if ($adjunto->getAdjunto) {
						if ($adjunto->getAdjunto()->getExpedienteInterno()) {
							$header = null;
							if (!$adjunto->getAdjunto()->getBorrador()) {
								$header = $this->renderView(
									'default/membrete.pdf.twig',
									[
										"periodo"      => $adjunto->getAdjunto()->getPeriodoLegislativo(),
										'dataToEncode' => $dataToEncode
									]
								);
							}
							$footer = $this->renderView('default/pie_pagina.pdf.twig');

							$array = array(
								'page-size'      => 'Legal',
								//					'page-width'     => '220mm',
								//					'page-height'     => '340mm',
								//					'margin-left'    => "3cm",
								//					'margin-right'   => "3cm",
								'margin-top'     => "5cm",
								'margin-bottom'  => "2cm",
								'header-html'    => $header,
								'header-spacing' => 4,
								'footer-spacing' => 5,
								'footer-html'    => $footer,
								//                    'margin-bottom' => "1cm"
							);

							//        return new Response($html);
							$title = 'Proyecto';


							$html = $this->renderView(
								'expediente/proyecto.pdf.twig',
								[
									'expediente' => $adjunto->getAdjunto(),
									'title'      => $title,
								]
							);


							$date = new \DateTime();
							$time = $date->getTimeStamp();
							$nombre = $tmp . '/Firmado' . $time . '.pdf';

							$knpSnappyPdf->generateFromHtml(
								$html,
								$nombre,
								array(
									'page-size'      => 'Legal',
									//					'page-width'     => '220mm',
									//					'page-height'     => '340mm',
									//					'margin-left'    => "3cm",
									//					'margin-right'   => "3cm",
									'margin-top'     => "5cm",
									'margin-bottom'  => "2cm",
									'header-html'    => $header,
									'header-spacing' => 4,
									'footer-spacing' => 5,
									'footer-html'    => $footer,
									//                    'margin-bottom' => "1cm"

								)
							);



							//$pdfMerge->addPDF('uploads/expedientes/anexos/'.$archivo);

							$pdfMerge->addPDF($nombre);

							foreach ($adjunto->getAdjunto()->getAnexos() as $anexo) {

								$path = $anexo->getAnexo();

								$extension = pathinfo($path);

								$extension = strtolower($extension['extension']);

								if ($extension == 'pdf') {
									$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
								}
							}
						} else {

							//PROYECTO FIRMADO
							$path = $adjunto->getAdjunto()->getExpedienteInterno();

							$extension = pathinfo($path);

							$extension = strtolower($extension['extension']);

							if ($extension == 'pdf') {
								$pdfMerge->addPDF('uploads/expedientes/internos/' . $path);
							}
						}
					}
				}
				//DICTAMEN FIRMADO
				if ($firstDictamen->getDictamen()) {
					$pdfMerge->addPDF('uploads/dictamenes/' . $firstDictamen->getDictamen());
				}

				//RAMA FIRMADO
				if ($firstDictamen->getRama()) {
					$pdfMerge->addPDF('uploads/expedientes/rama/' . $firstDictamen->getRama());
				}
				$TextoDefinitivoRepository = $em->getRepository(TextoDefinitivo::class);


				$textoDefinitivo = $TextoDefinitivoRepository->findOneBy(
					['dictamen' => $firstDictamen->getId()],
					['id' => 'DESC']
				);
				if ($textoDefinitivo) {
					//TEXTO DEFINITIVO FIRMADO
					if ($textoDefinitivo->getArchivo()) {
						$pdfMerge->addPDF('uploads/expedientes/definitivo/' . $textoDefinitivo->getArchivo());
					}
					if ($textoDefinitivo->getPase()) {
						$pdfMerge->addPDF('uploads/expedientes/pasedem/' . $textoDefinitivo->getPase());
					}
				}
			}

			foreach ($expediente->getInformeDems() as $anexo) {
				if ($anexo) {

					$path = $anexo->getArchivo();

					$extension = pathinfo($path);

					$extension = strtolower($extension['extension']);

					if ($extension == 'pdf') {
						$pdfMerge->addPDF('uploads/expedientes/dem/' . $path);
					}
				}
			}
		}
		if (count($ProyectosBae) > 1) {
			$secondProyectoBae = $ProyectosBae[1];


			if ($secondProyectoBae) {

				// Si NO es informe DEM, incluir los archivos de giro del segundo proyecto
				if (!$secondProyectoBae->getEsInformeDem()) {
					if ($secondProyectoBae->getFirmado()) {
						//GIRO FIRMADO
						$pdfMerge->addPDF('uploads/expedientes/comision/giro/' . $secondProyectoBae->getFirmado());
					} else {
						//GIRO SIN FIRMAR
						$giros = $secondProyectoBae->getGirosOrdenados();
						if ($giros) {
							$expediente = $secondProyectoBae->getExpediente();
							$sesion = $secondProyectoBae->getBoletinAsuntoEntrado()->getSesion();

							$titulo = "Giro " . $expediente->getExpediente() . "-" . $expediente->getLetra() . "-" . $expediente->getPeriodoLegislativo()->getAnio();
							$fecha = $sesion->getFecha();


							$html = $this->renderView(
								'comision/giroComision.pdf.twig',
								[
									'expediente' => $expediente,
									'sesion' => $sesion,
									'title'      => $titulo,
									'giros'      => $giros,
									'fecha'      => $fecha,

								]
							);

							$date = new \DateTime();
							$time = $date->getTimeStamp();
							$nombre = $tmp . '/Giro2' . $time . '.pdf';
							$knpSnappyPdf->generateFromHtml(
								$html,
								$nombre,
								array(
									'page-size'      => 'Legal',
									//					'page-width'     => '220mm',
									//					'page-height'     => '340mm',
									//					'margin-left'    => "3cm",
									//					'margin-right'   => "3cm",
									'margin-top'     => "5cm",
									'margin-bottom'  => "2cm",
									//                    'margin-bottom' => "1cm"

								)
							);
							$pdfMerge->addPDF($nombre);
						}
					}
				}
				if (count($Dictamenes) > 1) {
					$secondDictamen = $Dictamenes[1];
					if ($secondDictamen->getDictamen()) {

						$pdfMerge->addPDF('uploads/dictamenes/' . $secondDictamen->getDictamen());
					}

					$TextoDefinitivoRepository = $em->getRepository(TextoDefinitivo::class);


					$textoDefinitivo = $TextoDefinitivoRepository->findOneBy(
						['dictamen' => $secondDictamen->getId()],
						['id' => 'DESC']
					);
					if ($textoDefinitivo) {
						//TEXTO DEFINITIVO FIRMADO
						if ($textoDefinitivo->getArchivo()) {
							$pdfMerge->addPDF('uploads/expedientes/definitivo/' . $textoDefinitivo->getArchivo());
						}
						if ($textoDefinitivo->getPase()) {
							$pdfMerge->addPDF('uploads/expedientes/pasedem/' . $textoDefinitivo->getPase());
						}
					}
				}
			}
		}


		$logger->info("Iniciando merge final de PDF", [
			'expediente_id' => $expediente->getId()
		]);
		try {
			$pdf4 = $pdfMerge->merge('browser', 'pdf3.pdf');
			$logger->info("PDF merge completado exitosamente", [
				'expediente_id' => $expediente->getId()
			]);
		} catch (\Exception $e) {
			$logger->error("Error en merge de PDF", [
				'expediente_id' => $expediente->getId(),
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
			throw $e;
		}

		$logger->info("=== FIN imprimirArchivo", [
			'expediente_id' => $expediente->getId(),
			'function' => 'imprimirArchivo'
		]);
		return new Response(
			$pdf4,
			// array(
			// 	'page-size'      => 'Legal',
			// 	//					'page-width'     => '220mm',
			// 	//					'page-height'     => '340mm',
			// 	//					'margin-left'    => "3cm",
			// 	//					'margin-right'   => "3cm",
			// 	'margin-top'     => "5cm",
			// 	'margin-bottom'  => "2cm",
			// 	'header-spacing' => 4,
			// 	'footer-spacing' => 5,
			// 	//                    'margin-bottom' => "1cm"

			// ),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $titulo . '.pdf"'
			)
		);
	}

	function generarQRVerificación(Pdf $knpSnappyPdf, $url)
	{



		//        return new Response($html);
		$title = 'Proyecto';


		$html = $this->renderView(
			'default/qr.pdf.twig',
			[]
		);
		$date = new \DateTime();
		$time = $date->getTimeStamp();
		$tmp = sys_get_temp_dir();
		$nombre = $tmp . '/QR' . $time . '.pdf';
		return new Response(
			$knpSnappyPdf->getOutputFromHtml(
				$html,
				array(
					'page-size'      => 'Legal',
					//					'page-width'     => '220mm',
					//					'page-height'     => '340mm',
					//					'margin-left'    => "3cm",
					//					'margin-right'   => "3cm",
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'header-spacing' => 4,
					'footer-spacing' => 5,
					//                    'margin-bottom' => "1cm"

				)
			),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}

	function imprimirArchivoGirosV(Pdf $knpSnappyPdf, GiroAdministrativo $giro)
	{


		$url = [];


		$path = $giro->getAnexo();

		if ($path) {
			$extension = pathinfo($path);
			$extension = strtolower($extension['extension']);
			if ($extension == 'pdf') {
				array_push($url, 'uploads/expedientes/anexos/' . $path);
			}
		}


		foreach ($giro->getAnexoGiros() as $anexoGiro) {
			if ($anexoGiro) {
				$path = $anexoGiro->getAnexo();

				if ($path) {
					$extension = pathinfo($path);
					$extension = strtolower($extension['extension']);
					if ($extension == 'pdf') {
						array_push($url, 'uploads/giros/anexos/' . $path);
					}
				}
			}
		}

		return $this->render(
			'default/descargar.html.twig',
			[
				'urls' => $url
			]
		);
	}

	function imprimirArchivoFirmasV(Pdf $knpSnappyPdf, Pdf $knpSnappyPdf2, Expediente $expediente)
	{



		$url = [];
		$em = $this->getDoctrine()->getManager();
		$decreto = $expediente->getIsDecreto();

		$DictamenRepository = $em->getRepository(Dictamen::class);


		$firstDictamen = $DictamenRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);


		//CARATULA


		$dataToEncode = $expediente->getCodigoReferencia();
		if ($expediente->getBorrador()) {
			$dataToEncode = null;
		}

		$title      = 'Carátula';

		$html = $this->renderView(
			'expediente/caratula.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

		$pdfMerge = new PDFMerger($this->getParameter('kernel.project_dir') . '/public');

		$filesystem = new Filesystem();
		$filesystem->remove('filePDF.pdf');
		$date = new \DateTime();
		$tmp = sys_get_temp_dir();


		//PROYECTO SIN FIRMAR
		if (!$expediente->getExpedienteInterno()) {
			$header = null;
			if (!$expediente->getBorrador()) {
				$header = $this->renderView(
					'default/membrete.pdf.twig',
					[
						"periodo"      => $expediente->getPeriodoLegislativo(),
						'dataToEncode' => $dataToEncode
					]
				);
			}

			$array = array(
				'page-size'      => 'Legal',
				//					'page-width'     => '220mm',
				//					'page-height'     => '340mm',
				//					'margin-left'    => "3cm",
				//					'margin-right'   => "3cm",
				'margin-top'     => "5cm",
				'margin-bottom'  => "2cm",
				'header-html'    => $header,
				'header-spacing' => 4,
				'footer-spacing' => 5,
				'footer-html'    => $footer,
				//                    'margin-bottom' => "1cm"
			);

			//        return new Response($html);
			$title = 'Proyecto';


			$html = $this->renderView(
				'expediente/proyecto.pdf.twig',
				[
					'expediente' => $expediente,
					'title'      => $title,
				]
			);


			$date = new \DateTime();
			$time = $date->getTimeStamp();
			$nombre = $tmp . '/Firmado' . $time . '.pdf';

			$knpSnappyPdf->generateFromHtml(
				$html,
				$nombre,
				array(
					'page-size'      => 'Legal',
					//					'page-width'     => '220mm',
					//					'page-height'     => '340mm',
					//					'margin-left'    => "3cm",
					//					'margin-right'   => "3cm",
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'header-html'    => $header,
					'header-spacing' => 4,
					'footer-spacing' => 5,
					'footer-html'    => $footer,
					//                    'margin-bottom' => "1cm"

				)
			);



			//$pdfMerge->addPDF('uploads/expedientes/anexos/'.$archivo);

			$pdfMerge->addPDF($nombre);

			foreach ($expediente->getAnexos() as $anexo) {

				$path = $anexo->getAnexo();

				$extension = pathinfo($path);

				$extension = strtolower($extension['extension']);

				if ($extension == 'pdf') {
					$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
				}
			}
		} else {

			//PROYECTO FIRMADO
			$path = $expediente->getExpedienteInterno();

			$extension = pathinfo($path);

			$extension = strtolower($extension['extension']);

			if ($extension == 'pdf') {
				$pdfMerge->addPDF('uploads/expedientes/internos/' . $path);
				array_push($url, 'uploads/expedientes/internos/' . $path);
			}


			if ($decreto || $expediente->getTipoExpediente()->getId() == 1) {
				foreach ($expediente->getAnexos() as $anexo) {

					$path = $anexo->getAnexo();

					if ($path) {

						$extension = pathinfo($path);

						$extension = strtolower($extension['extension']);

						if ($extension == 'pdf') {
							$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
							array_push($url, 'uploads/expedientes/anexos/' . $path);
						}
					}
				}
				$contador = 0;
				foreach ($expediente->getGiroAdministrativos() as $anexo) {
					$contador = $contador + 1;



					$path = $anexo->getAnexo();

					if ($path) {
						$extension = pathinfo($path);
						$extension = strtolower($extension['extension']);
						if ($extension == 'pdf') {
							$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
							array_push($url, 'uploads/expedientes/anexos/' . $path);
						}
					}


					foreach ($anexo->getAnexoGiros() as $anexoGiro) {

						$path = $anexoGiro->getAnexo();

						if ($path) {
							$extension = pathinfo($path);
							$extension = strtolower($extension['extension']);
							if ($extension == 'pdf') {
								$pdfMerge->addPDF('uploads/giros/anexos/' . $path);
								array_push($url, 'uploads/giros/anexos/' . $path);
							}
						}
					}

					$title = 'Giro';

					$html = $this->renderView(
						'expediente/giroSector.pdf.twig',
						[
							'expediente' => $expediente,
							'giro'       => $anexo,
							'title'      => $title,
						]
					);
					$date = new \DateTime();
					$time = $date->getTimeStamp();
					$nombre = $tmp . '/Giro' . $time . $contador . '.pdf';

					$knpSnappyPdf->generateFromHtml(
						$html,
						$nombre,
						array(
							'page-size'      => 'Legal',
							//					'page-width'     => '220mm',
							//					'page-height'     => '340mm',
							//					'margin-left'    => "3cm",
							//					'margin-right'   => "3cm",
							'margin-top'     => "5cm",
							'margin-bottom'  => "2cm",
							'header-spacing' => 4,
							'footer-spacing' => 5,
							//                    'margin-bottom' => "1cm"

						)
					);

					$pdfMerge->addPDF($nombre);
				}





				return $this->render(
					'default/descargar.html.twig',
					[
						'urls' => $url
					]
				);
			}
		}


		foreach ($expediente->getProveidos() as $proveido) {

			/*		$caratula=$proveido->getCaratula();
 		if($caratula){
			$pdfMerge->addPDF('uploads/expedientes/proveido/caratula/'.$caratula);
		} */

			$archivo = $proveido->getArchivo();
			if ($archivo) {
				$pdfMerge->addPDF('uploads/expedientes/proveido/' . $archivo);
			}

			/* 		$cierre=$proveido->getCierre();
		if($cierre){
			$pdfMerge->addPDF('uploads/expedientes/proveido/cierre/'.$cierre);
		} */
		}

		$proyectoBaeRepository = $em->getRepository(ProyectoBAE::class);

		$firstProyectoBae = $proyectoBaeRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);

		if ($firstProyectoBae) {
			//
			// Si NO es informe DEM, incluir los archivos de giro, pedido y digesto
			if (!$firstProyectoBae->getEsInformeDem()) {
				if ($firstProyectoBae->getFirmado()) {
					//GIRO FIRMADO
					$pdfMerge->addPDF('uploads/expedientes/comision/giro/' . $firstProyectoBae->getFirmado());
				} else {
					//GIRO SIN FIRMAR
					$giros = $firstProyectoBae->getGirosOrdenados();
					if ($giros) {
						$expediente = $firstProyectoBae->getExpediente();
						$sesion = $firstProyectoBae->getBoletinAsuntoEntrado()->getSesion();

						$titulo = "Giro " . $expediente->getExpediente() . "-" . $expediente->getLetra() . "-" . $expediente->getPeriodoLegislativo()->getAnio();
						$fecha = $sesion->getFecha();


						$html = $this->renderView(
							'comision/giroComision.pdf.twig',
							[
								'expediente' => $expediente,
								'sesion' => $sesion,
								'title'      => $titulo,
								'giros'      => $giros,
								'fecha'      => $fecha,

							]
						);

						$date = new \DateTime();
						$time = $date->getTimeStamp();
						$nombre = $tmp . '/Giro' . $time . '.pdf';
						$knpSnappyPdf->generateFromHtml(
							$html,
							$nombre,
							array(
								'page-size'      => 'Legal',
								//					'page-width'     => '220mm',
								//					'page-height'     => '340mm',
								//					'margin-left'    => "3cm",
								//					'margin-right'   => "3cm",
								'margin-top'     => "5cm",
								'margin-bottom'  => "2cm",
								//                    'margin-bottom' => "1cm"

							)
						);
						$pdfMerge->addPDF($nombre);
					}
				}
				if ($firstProyectoBae->getPedido()) {
					//PEDIDO FIRMADO
					$pdfMerge->addPDF('uploads/expedientes/pedido/' . $firstProyectoBae->getPedido());
				}
				if ($firstProyectoBae->getDigesto()) {
					//DIGESTO FIRMADO
					$pdfMerge->addPDF('uploads/expedientes/digesto/' . $firstProyectoBae->getDigesto());
				}
			}


			if ($firstDictamen) {
				//DICTAMEN FIRMADO
				if ($firstDictamen->getDictamen()) {
					$pdfMerge->addPDF('uploads/dictamenes/' . $firstDictamen->getDictamen());
				}

				//RAMA FIRMADO
				if ($firstDictamen->getRama()) {
					$pdfMerge->addPDF('uploads/expedientes/rama/' . $firstDictamen->getRama());
				}
				$TextoDefinitivoRepository = $em->getRepository(TextoDefinitivo::class);


				$textoDefinitivo = $TextoDefinitivoRepository->findOneBy(
					['dictamen' => $firstDictamen->getId()],
					['id' => 'DESC']
				);
				if ($textoDefinitivo) {
					//TEXTO DEFINITIVO FIRMADO
					if ($textoDefinitivo->getArchivo()) {
						$pdfMerge->addPDF('uploads/expedientes/definitivo/' . $textoDefinitivo->getArchivo());
					}
					if ($textoDefinitivo->getPase()) {
						$pdfMerge->addPDF('uploads/expedientes/pasedem/' . $textoDefinitivo->getPase());
					}
				}
			}
		}




		$pdf4 = $pdfMerge->merge('browser', 'pdf3.pdf');

		$htmls = '<html><head><title>Descargar PDFs</title></head><body>';
		foreach ($url as $file) {
			$urls = $this->generateUrl('base_url', [], UrlGeneratorInterface::ABSOLUTE_URL) . $file;
			$htmls .= '<iframe src="' . $urls . '" style="display:none;"></iframe>';
		}
		$htmls .= '</body></html>';

		return new Response(null);
		/* 		return new Response($pdf4, array(
			'page-size'      => 'Legal',
		//					'page-width'     => '220mm',
		//					'page-height'     => '340mm',
		//					'margin-left'    => "3cm",
		//					'margin-right'   => "3cm",
			'margin-top'     => "5cm",
			'margin-bottom'  => "2cm",
			'header-spacing' => 4,
			'footer-spacing' => 5,
		//                    'margin-bottom' => "1cm"
			
		),
		200,
		array(
			'Content-Type'        => 'application/pdf',
			'Content-Disposition' => 'inline; filename="' . $titulo . '.pdf"'
		)); */
	}

	public function imprimirGiroComision(ProyectoBae $id)
	{
		$giros = $id->getGirosOrdenados();
		$expediente = $id->getExpediente();
		$sesion = $id->getBoletinAsuntoEntrado()->getSesion();

		$titulo = $sesion->getTitulo();
		$fecha = $sesion->getFecha();

		$html = $this->renderView(
			'expediente/giroComision.pdf.twig',
			[
				'expediente' => $expediente,
				'titulo'      => $titulo,
				'fecha'      => $fecha,
			]
		);
	}

	public function imprimirGiroComisionFirmado(Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();


		$proyectoBaeRepository = $em->getRepository(ProyectoBAE::class);

		$firstProyectoBae = $proyectoBaeRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);

		// Verificar si es informe DEM - no tiene giro firmado
		if ($firstProyectoBae && $firstProyectoBae->getEsInformeDem()) {
			$this->get('session')->getFlashBag()->add(
				'warning',
				'Los informes DEM no tienen archivo de giro firmado disponible.'
			);
			return $this->redirectToRoute('expediente_show', ['id' => $expediente->getId()]);
		}

		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/comision/giro/' . $firstProyectoBae->getFirmado();

		$response = new BinaryFileResponse($pdfPath);
		$titulo = $firstProyectoBae->getExpediente()->getExpediente() . "-" . $firstProyectoBae->getExpediente()->getLetra() . "-" . $firstProyectoBae->getExpediente()->getPeriodoLegislativo()->getAnio();
		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="' . $titulo . '.pdf"');


		return $response;
	}

	public function imprimirDictamenFirmado(Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();


		$DictamenRepository = $em->getRepository(Dictamen::class);


		$dictamen = $DictamenRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);




		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/dictamenes/' . $dictamen->getDictamen();
		// Crear una BinaryFileResponse para el archivo PDF
		$response = new BinaryFileResponse($pdfPath);
		$titulo = $dictamen->getExpediente()->getExpediente() . "-" . $dictamen->getExpediente()->getLetra() . "-" . $dictamen->getExpediente()->getPeriodoLegislativo()->getAnio();
		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="' . $titulo . '.pdf"');


		return $response;
	}

	public function imprimirRamaFirmado(Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();


		$DictamenRepository = $em->getRepository(Dictamen::class);


		$dictamen = $DictamenRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);




		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/rama/' . $dictamen->getRama();
		// Crear una BinaryFileResponse para el archivo PDF
		$response = new BinaryFileResponse($pdfPath);
		$titulo = $dictamen->getExpediente()->getExpediente() . "-" . $dictamen->getExpediente()->getLetra() . "-" . $dictamen->getExpediente()->getPeriodoLegislativo()->getAnio();
		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="' . $titulo . '.pdf"');


		return $response;
	}

	public function imprimirPedidoFirmado(Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();


		$proyectoBaeRepository = $em->getRepository(ProyectoBAE::class);

		$firstProyectoBae = $proyectoBaeRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);

		// Verificar si es informe DEM - no tiene pedido
		if ($firstProyectoBae && $firstProyectoBae->getEsInformeDem()) {
			$this->get('session')->getFlashBag()->add(
				'warning',
				'Los informes DEM no tienen archivo de pedido disponible.'
			);
			return $this->redirectToRoute('expediente_show', ['id' => $expediente->getId()]);
		}

		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/pedido/' . $firstProyectoBae->getPedido();


		$response = new BinaryFileResponse($pdfPath);
		$titulo = $firstProyectoBae->getExpediente()->getExpediente() . "-" . $firstProyectoBae->getExpediente()->getLetra() . "-" . $firstProyectoBae->getExpediente()->getPeriodoLegislativo()->getAnio();
		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="' . $titulo . '.pdf"');


		return $response;
	}

	public function imprimirInformeFirmado(Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();


		$proyectoBaeRepository = $em->getRepository(ProyectoBAE::class);

		$firstProyectoBae = $proyectoBaeRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);

		// Verificar si es informe DEM - no tiene digesto
		if ($firstProyectoBae && $firstProyectoBae->getEsInformeDem()) {
			$this->get('session')->getFlashBag()->add(
				'warning',
				'Los informes DEM no tienen archivo de digesto disponible.'
			);
			return $this->redirectToRoute('expediente_show', ['id' => $expediente->getId()]);
		}

		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/digesto/' . $firstProyectoBae->getDigesto();

		$response = new BinaryFileResponse($pdfPath);
		$titulo = $firstProyectoBae->getExpediente()->getExpediente() . "-" . $firstProyectoBae->getExpediente()->getLetra() . "-" . $firstProyectoBae->getExpediente()->getPeriodoLegislativo()->getAnio();
		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="' . $titulo . '.pdf"');


		return $response;
	}

	public function imprimirTextoFirmado(Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();


		$DictamenRepository = $em->getRepository(Dictamen::class);


		$dictamen = $DictamenRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);

		$TextoDefinitivoRepository = $em->getRepository(TextoDefinitivo::class);


		$textoDefinitivo = $TextoDefinitivoRepository->findOneBy(
			['dictamen' => $dictamen->getId()],
			['id' => 'DESC']
		);




		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/definitivo/' . $textoDefinitivo->getArchivo();


		$response = new BinaryFileResponse($pdfPath);
		$titulo = "Texto Definitivo";
		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="' . $titulo . '.pdf"');


		return $response;
	}

	public function imprimirPaseFirmado(Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();


		$DictamenRepository = $em->getRepository(Dictamen::class);


		$dictamen = $DictamenRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);

		$TextoDefinitivoRepository = $em->getRepository(TextoDefinitivo::class);


		$textoDefinitivo = $TextoDefinitivoRepository->findOneBy(
			['dictamen' => $dictamen->getId()],
			['id' => 'DESC']
		);




		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/pasedem/' . $textoDefinitivo->getPase();


		$response = new BinaryFileResponse($pdfPath);
		$titulo = "Texto Definitivo";
		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="' . $titulo . '.pdf"');


		return $response;
	}

	public function imprimirProyectoNumero(Pdf $knpSnappyPdf, $id, Request $request)
	{
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository(Expediente::class)->find($id);



		$dataToEncode = $expediente->getCodigoReferencia();
		if ($expediente->getBorrador()) {
			$dataToEncode = null;
		}

		$title = 'Proyecto';

		if (!$expediente->getPeriodoLegislativo()) {
			$this->get('session')->getFlashBag()->add(
				'error',
				'El expediente no tiene periodo legislativo asignado'
			);

			return $this->redirectToRoute('expediente_show', ['id' => $expediente->getId()]);
		}

		$header = null;
		if (!$expediente->getBorrador()) {
			$header = $this->renderView(
				'default/membrete.pdf.twig',
				[
					"periodo"      => $expediente->getPeriodoLegislativo(),
					'dataToEncode' => $dataToEncode
				]
			);
		}

		$footer = $this->renderView('default/pie_pagina.pdf.twig');


		$array = array(
			'page-size'      => 'Legal',
			//					'page-width'     => '220mm',
			//					'page-height'     => '340mm',
			//					'margin-left'    => "3cm",
			//					'margin-right'   => "3cm",
			'margin-top'     => "5cm",
			'margin-bottom'  => "2cm",
			'header-html'    => $header,
			'header-spacing' => 4,
			'footer-spacing' => 5,
			'footer-html'    => $footer,
			//                    'margin-bottom' => "1cm"
		);

		$html = $this->renderView(
			'expediente/proyectoNum.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

		//        return new Response($html);
		$pdfMerge = new PDFMerger($this->getParameter('kernel.project_dir') . '/public');

		$filesystem = new Filesystem();
		$filesystem->remove('filePDF.pdf');
		$date = new \DateTime();
		$time = $date->getTimeStamp();
		$tmp = sys_get_temp_dir();
		$nombre = $tmp . '/' . $time . '.pdf';

		$knpSnappyPdf->generateFromHtml(
			$html,
			$nombre,
			array(
				'page-size'      => 'Legal',
				//					'page-width'     => '220mm',
				//					'page-height'     => '340mm',
				//					'margin-left'    => "3cm",
				//					'margin-right'   => "3cm",
				'margin-top'     => "5cm",
				'margin-bottom'  => "2cm",
				'header-html'    => $header,
				'header-spacing' => 4,
				'footer-spacing' => 5,
				'footer-html'    => $footer,
				//                    'margin-bottom' => "1cm"

			)
		);


		$pdfMerge->addPDF($nombre);


		foreach ($expediente->getAnexos() as $anexo) {

			$path = $anexo->getAnexo();

			$extension = pathinfo($path);

			$extension = strtolower($extension['extension']);

			if ($extension == 'pdf') {
				$pdfMerge->addPDF('uploads/expedientes/anexos/' . $path);
			}
		}

		$pdf4 = $pdfMerge->merge('browser', 'pdf3.pdf');


		return new Response(
			$pdf4,
			// array(
			// 	'page-size'      => 'Legal',
			// 	//					'page-width'     => '220mm',
			// 	//					'page-height'     => '340mm',
			// 	//					'margin-left'    => "3cm",
			// 	//					'margin-right'   => "3cm",
			// 	'margin-top'     => "5cm",
			// 	'margin-bottom'  => "2cm",
			// 	'header-html'    => $header,
			// 	'header-spacing' => 4,
			// 	'footer-spacing' => 5,
			// 	'footer-html'    => $footer,
			// 	//                    'margin-bottom' => "1cm"

			// ),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}

	function imprimirArchivoAdministrativo(Pdf $knpSnappyPdf, Expediente $expediente)
	{
		$em = $this->getDoctrine()->getManager();

		//CARATULA

		$dataToEncode = $expediente->getCodigoReferencia();
		if ($expediente->getBorrador()) {
			$dataToEncode = null;
		}

		$title      = 'Carátula';

		$html = $this->renderView(
			'expediente/caratula.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

		$pdfMerge = new PDFMerger($this->getParameter('kernel.project_dir') . '/public');

		$filesystem = new Filesystem();
		$filesystem->remove('filePDF.pdf');
		$date = new \DateTime();
		$time = $date->getTimeStamp();
		$tmp = sys_get_temp_dir();
		$nombre = $tmp . '/Caratula' . $time . '.pdf';

		$knpSnappyPdf->generateFromHtml(
			$html,
			$nombre,
			array(
				'page-size'      => 'Legal',
				//					'page-width'     => '220mm',
				//					'page-height'     => '340mm',
				//					'margin-left'    => "3cm",
				//					'margin-right'   => "3cm",
				'margin-top'     => "5cm",
				'margin-bottom'  => "2cm",
				'header-spacing' => 4,
				'footer-spacing' => 5,
				//                    'margin-bottom' => "1cm"

			)
		);


		$pdfMerge->addPDF($nombre);

		//PROYECTO FIRMADO
		$path = $expediente->getExpedienteInterno();

		$extension = pathinfo($path);

		$extension = strtolower($extension['extension']);

		if ($extension == 'pdf') {
			$pdfMerge->addPDF('uploads/expedientes/internos/' . $path);
		}

		$giros = $expediente->getGiroAdministrativos();

		foreach ($giros as $giro) {
			$path = $giro->getGiro();

			$extension = pathinfo($path);

			$extension = strtolower($extension['extension']);

			if ($extension == 'pdf') {
				$pdfMerge->addPDF('uploads/expedientes/giros/' . $path);
			}
		}



		$pdf4 = $pdfMerge->merge('browser', 'pdf3.pdf');


		return new Response(
			$pdf4,
			// array(
			// 	'page-size'      => 'Legal',
			// 	//					'page-width'     => '220mm',
			// 	//					'page-height'     => '340mm',
			// 	//					'margin-left'    => "3cm",
			// 	//					'margin-right'   => "3cm",
			// 	'margin-top'     => "5cm",
			// 	'margin-bottom'  => "2cm",
			// 	'header-spacing' => 4,
			// 	'footer-spacing' => 5,
			// 	//                    'margin-bottom' => "1cm"

			// ),
			200,
			array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}
}
