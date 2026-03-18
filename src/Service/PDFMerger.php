<?php

namespace App\Service;

use setasign\Fpdi\Fpdi;

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
                $size = $pdf->getTemplateSize($tplId);

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
        $qpdf = trim((string)shell_exec('command -v qpdf'));
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
        $gs = trim((string)shell_exec('command -v gs'));
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