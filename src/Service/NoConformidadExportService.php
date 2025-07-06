<?php

namespace App\Service;

use App\Entity\NoConformidad;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class NoConformidadExportService
{
    public function exportToExcel(array $noConformidades): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar encabezados
        $headers = [
            'A1' => 'ID',
            'B1' => 'Fecha',
            'C1' => 'Área',
            'D1' => 'Usuario que registró',
            'E1' => 'Empleado/Área involucrada',
            'F1' => 'Origen',
            'G1' => 'Categoría',
            'H1' => 'Norma',
            'I1' => 'PGCD',
            'J1' => 'Requisito Específico',
            'K1' => 'Requisito Legal',
            'L1' => 'Descripción del Hallazgo',
            'M1' => 'Requisito Legal Específico',
            'N1' => 'Estado',
            'O1' => 'Responsable Calidad',
            'P1' => 'Asignado A',
            'Q1' => 'Corrección',
            'R1' => 'Fecha Corrección',
            'S1' => 'Análisis Causa',
            'T1' => 'Acción Correctiva',
            'U1' => 'Fecha Acción Correctiva',
            'V1' => 'Corrección Verificada',
            'W1' => 'Efectividad Verificada',
            'X1' => 'Comentarios Verificación'
        ];
        
        // Establecer encabezados
        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }
        
        // Estilo para encabezados
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        
        $sheet->getStyle('A1:W1')->applyFromArray($headerStyle);
        
        // Llenar datos
        $row = 2;
        foreach ($noConformidades as $nc) {
            $sheet->setCellValue('A' . $row, $nc->getId());
            $sheet->setCellValue('B' . $row, $nc->getFecha() ? $nc->getFecha()->format('d/m/Y H:i') : '');
            $sheet->setCellValue('C' . $row, $nc->getArea() ? $nc->getArea()->getNombre() : '');
                            $sheet->setCellValue('D' . $row, $nc->getCreadoPor() ? ($nc->getCreadoPor()->getPersona() ? $nc->getCreadoPor()->getPersona()->getNombre() . ' ' . $nc->getCreadoPor()->getPersona()->getApellido() : $nc->getCreadoPor()->getUsername()) : '');
            $sheet->setCellValue('E' . $row, $nc->getEmpleado() ? $nc->getEmpleado()->getNombre() . ' ' . $nc->getEmpleado()->getApellido() : '');
            $sheet->setCellValue('F' . $row, $nc->getOrigen());
            $sheet->setCellValue('G' . $row, $nc->getCategoria());
            $sheet->setCellValue('H' . $row, $nc->getNorma());
            $sheet->setCellValue('I' . $row, $nc->getPgcd());
            $sheet->setCellValue('J' . $row, $nc->getRequisitoEspecifico());
            $sheet->setCellValue('K' . $row, $nc->getRequisitoLegal() !== null ? ($nc->getRequisitoLegal() ? 'Sí' : 'No') : '');
            $sheet->setCellValue('L' . $row, $nc->getDescripcionHallazgo());
            $sheet->setCellValue('M' . $row, $nc->getRequisito());
            $sheet->setCellValue('N' . $row, $this->getEstadoLabel($nc->getEstado()));
            $sheet->setCellValue('O' . $row, $nc->getResponsableCalidad());
            $sheet->setCellValue('P' . $row, $nc->getAsignadoA() ? ($nc->getAsignadoA()->getPersona() ? $nc->getAsignadoA()->getPersona()->getNombre() . ' ' . $nc->getAsignadoA()->getPersona()->getApellido() : $nc->getAsignadoA()->getUsername()) : '');
            $sheet->setCellValue('Q' . $row, $nc->getCorreccion());
            $sheet->setCellValue('R' . $row, $nc->getFechaCorreccion() ? $nc->getFechaCorreccion()->format('d/m/Y H:i') : '');
            $sheet->setCellValue('S' . $row, $nc->getAnalisisCausa());
            $sheet->setCellValue('T' . $row, $nc->getAccionCorrectiva());
            $sheet->setCellValue('U' . $row, $nc->getFechaAccionCorrectiva() ? $nc->getFechaAccionCorrectiva()->format('d/m/Y H:i') : '');
            $sheet->setCellValue('V' . $row, $nc->getCorreccionVerificada() ? 'Sí' : ($nc->getCorreccionVerificada() === false ? 'No' : ''));
            $sheet->setCellValue('W' . $row, $nc->getEfectividadVerificada() ? 'Sí' : ($nc->getEfectividadVerificada() === false ? 'No' : ''));
            $sheet->setCellValue('X' . $row, $nc->getComentariosVerificacion());
            
            $row++;
        }
        
        // Ajustar ancho de columnas
        foreach (range('A', 'X') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Aplicar bordes a toda la tabla
        $tableRange = 'A1:X' . ($row - 1);
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        
        // Crear archivo temporal
        $filename = tempnam(sys_get_temp_dir(), 'no_conformidades_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
        
        return $filename;
    }
    
    private function getEstadoLabel(string $estado): string
    {
        $estados = [
            'nuevo' => 'Nuevo',
            'revisado' => 'Revisado',
            'tratado' => 'Tratado',
            'aceptado' => 'Aceptado',
            'verificada_correccion' => 'Verificada Corrección',
            'verificada_efectividad' => 'Verificada Efectividad',
            'desestimado' => 'Desestimado'
        ];
        
        return $estados[$estado] ?? $estado;
    }
} 