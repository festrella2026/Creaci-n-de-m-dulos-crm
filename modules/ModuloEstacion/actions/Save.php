<?php

class ModuloEstacion_Save_Action extends Vtiger_Save_Action {
	const DUPLICATE_ASSOCIATION_MESSAGE = 'Este módulo ya está asociado a esta estación.';

	public function process(Vtiger_Request $request) {
		try {
			parent::process($request);
		} catch (Exception $exception) {
			if ($request->isAjax() || $exception->getMessage() !== self::DUPLICATE_ASSOCIATION_MESSAGE) {
				throw $exception;
			}

			$redirectUrl = 'index.php?module=ModuloEstacion&view=Edit&saveError=duplicateModule';
			$recordId = (int)$request->get('record');
			if ($recordId) {
				$redirectUrl .= '&record=' . $recordId;
			}
			foreach (array('account_id', 'modulo_id') as $fieldName) {
				$fieldValue = (int)$request->get($fieldName);
				if ($fieldValue) {
					$redirectUrl .= '&' . $fieldName . '=' . $fieldValue;
				}
			}

			header('Location: ' . $redirectUrl);
			exit;
		}
	}
}
