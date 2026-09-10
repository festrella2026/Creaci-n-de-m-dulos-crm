<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/../viewers/HeaderViewer.php';

class Vtiger_PDF_InventoryHeaderViewer extends Vtiger_PDF_HeaderViewer
{

	function totalHeight($parent)
	{
		$height = 100;

		if ($this->onEveryPage)
			return $height;
		// se agrego el return
		if ($this->onFirstPage && $parent->onFirstPage())
			return $height;
		return 0;
	}

	function display($parent)
	{
		$pdf = $parent->getPDF();
		$headerFrame = $parent->getHeaderFrame();
		if ($this->model) {
			$headerColumnWidth = $headerFrame->w / 3.0;

			$modelColumns = $this->model->get('columns');

			// Column 1
			$offsetX = 5;

			$modelColumn0 = $modelColumns[0];

			list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
				$modelColumn0['logo']
			);
			//division because of mm to px conversion
			$w = $imageWidth / 3;
			if ($w > 60) {
				$w = 60;
			}
			$h = $imageHeight / 3;
			if ($h > 30) {
				$h = 30;
			}
			$pdf->Image($modelColumn0['logo'], $headerFrame->x, $headerFrame->y, $w, $h);
			$imageHeightInMM = 30;

			$pdf->SetFont('freeserif', 'B');
			$contentHeight = $pdf->GetStringHeight($modelColumn0['summary'], $headerColumnWidth);
			$pdf->MultiCell(
				$headerColumnWidth,
				$contentHeight,
				$modelColumn0['summary'],
				0,
				'L',
				0,
				1,
				$headerFrame->x,
				$headerFrame->y + $imageHeightInMM + 2
			);

			$pdf->SetFont('freeserif', '');
			$contentHeight = $pdf->GetStringHeight($modelColumn0['content'], $headerColumnWidth);
			$pdf->MultiCell(
				$headerColumnWidth,
				$contentHeight,
				$modelColumn0['content'],
				0,
				'L',
				0,
				1,
				$headerFrame->x,
				$pdf->GetY()
			);

			// Column 2
			$offsetX = 5;
			// Ajustamos la posición Y para que el contenido de la columna 2 comience a la misma altura que el contenido de la columna 1
			$pdf->SetY($headerFrame->y + 8);

			$modelColumn1 = $modelColumns[1];

			$offsetY = 8;
			foreach ($modelColumn1 as $label => $value) {

				if (!empty($value)) {
					$pdf->SetFont('freeserif', 'B');
					$pdf->SetFillColor(205, 201, 201);
					$pdf->MultiCell($headerColumnWidth - $offsetX, 7, $label, 1, 'C', 1, 1, $headerFrame->x + $headerColumnWidth + $offsetX, $pdf->GetY() + $offsetY);

					$pdf->SetFont('freeserif', '');
					$pdf->MultiCell($headerColumnWidth - $offsetX, 7, $value, 1, 'C', 0, 1, $headerFrame->x + $headerColumnWidth + $offsetX, $pdf->GetY());
					$offsetY = 2;
				}
			}

			// Column 3
			$offsetX = 10;
			$pdf->SetY($headerFrame->y);
			$modelColumn2 = $modelColumns[2];
			//Agrega el título del documento en la parte superior derecha del encabezado con mayúscula inicial y un fondo redondeado
			$title = ucfirst($this->model->get('title'));

			// Tamaño fijo
			$roundedRectW = 122;
			$roundedRectH = 12;

			// Posición
			$roundedRectX = $headerFrame->x + $headerFrame->w - $roundedRectW;
			$roundedRectY = 10;

			// Fondo
			$pdf->SetFillColor(205, 201, 201);
			$pdf->RoundedRect(
				$roundedRectX,
				$roundedRectY,
				$roundedRectW,
				$roundedRectH,
				3,
				'1111',
				'DF'
			);

			// Texto
			$pdf->SetFont('freeserif', 'B', 10);

			// Centrado
			$textWidth = $pdf->GetStringWidth($title);
			$textX = $roundedRectX + ($roundedRectW - $textWidth) / 2;
			$textY = $roundedRectY + ($roundedRectH / 2) - 3;

			$pdf->SetXY($textX, $textY);
			$pdf->Cell($textWidth, 5, $title, 0, 0, 'C');
			

			// Forzar inicio debajo del título
			$startY = $roundedRectY + $roundedRectH + 1; // margen

			// Si ya hay contenido más abajo, respetarlo
			if ($pdf->GetY() > $startY) {
				$startY = $pdf->GetY();
			}

			$pdf->SetY($startY);
			//-------------------------------------------------------

			$index = 0;
			//excluimos dirreccion envio de la columna, seria mejor editar el model column para no traer ese dato, pero por ahora lo ocultamos en la vista
			$excludedLabels = ['Dirección de envío'];
			$modelColumn2 = array_diff_key($modelColumn2, array_flip($excludedLabels));
			foreach ($modelColumn2 as $label => $value) {
				//si es el segundo valor del tercer bloque, agregar un espacio extra para separar visualmente del título del documento
				//$offsetY = ($index == 1) ? 18 : 3;
				$offsetY = 3;
				if (is_array($value)) {
					$pdf->SetFont('freeserif', '');
					foreach ($value as $l => $v) {
						$pdf->MultiCell(
							$headerColumnWidth - $offsetX,
							7,
							sprintf('%s: %s', $l, $v),
							1,
							'C',
							0,
							1,
							$headerFrame->x + $headerColumnWidth * 2.0 + $offsetX,
							$pdf->GetY() + $offsetY
						);
						$offsetY = 0;
					}
				} else {
					$offsetX = 5;
					$pdf->SetFont('freeserif', 'B');
					$pdf->SetFillColor(205, 201, 201);
					if ($label == 'Shipping Address') {
						$width = $pdf->GetStringWidth($value);
						$height = $pdf->GetStringHeight($value, $width);
						$pdf->MultiCell(
							$headerColumnWidth - $offsetX,
							7,
							$label,
							1,
							'L',
							1,
							1,
							$headerFrame->x + $headerColumnWidth * 2.0 + $offsetX,
							$pdf->GetY() + $offsetY - $height - $offsetX - 4.0
						);

						$pdf->SetFont('freeserif', '');
						$pdf->MultiCell(
							$headerColumnWidth - $offsetX,
							7,
							$value,
							1,
							'L',
							0,
							1,
							$headerFrame->x + $headerColumnWidth * 2.0 + $offsetX,
							$pdf->GetY()
						);
					} else {
						$offsetX = 10;
						//se agrega un espacio extra antes del valor para separar visualmente del título del documento
						$pdf->MultiCell(
							$headerColumnWidth - $offsetX,
							7,
							$label,
							1,
							'L',
							1,
							1,
							$headerFrame->x + $headerColumnWidth * 2.0 + $offsetX,
							$pdf->GetY() + 2//+ $offsetY
						);

						$pdf->SetFont('freeserif', '');
						//se agrega un espacio extra antes del valor para separar visualmente del título del documento
						$pdf->MultiCell(
							$headerColumnWidth - $offsetX,
							7,
							$value,
							1,
							'L',
							0,
							1,
							$headerFrame->x + $headerColumnWidth * 2.0 + $offsetX,
							$pdf->GetY()
						);
					}
				}
				$index++;
			}
			$pdf->setFont('freeserif', '');

			// Add the border cell at the end
			// This is required to reset Y position for next write
			$pdf->MultiCell($headerFrame->w, $headerFrame->h - $headerFrame->y, "", 0, 'L', 0, 1, $headerFrame->x, $headerFrame->y);
		}

	}

}
