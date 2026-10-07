jQuery(function ($) {
	var moduleFields = {
		'PINPAD': [
			'pinpad_version', 'pinpad_cantidad_lados', 'pinpad_cantidad_dispensadores',
			'pinpad_cantidad_islas', 'pinpad_cantidad_equipos'
		],
		'TANK': ['tank_marca_consola', 'tank_cantidad_tanques', 'tank_productos'],
		'LUBRICANTS': ['lubricants_dispositivo'],
		'PRINTER': ['printer_cantidad', 'printer_marca_dispensadores'],
		'BARCODE': ['barcode_cantidad'],
		'RFID HF': ['rfid_hf_cantidad_lectores', 'rfid_hf_version_lectores', 'rfid_hf_lector_escritorio'],
		'AVI RFID': [
			'avi_rfid_version_kit', 'avi_rfid_cantidad_antenas',
			'avi_rfid_cantidad_lectores', 'avi_rfid_cantidad_lectores_escritorio'
		]
	};
	var allSpecificFields = [].concat.apply([], Object.keys(moduleFields).map(function (key) {
		return moduleFields[key];
	}));

	function getSelectedModuleName() {
		return String($('#modulo_id_display').val() || '').trim().toUpperCase();
	}

	function rowForField(fieldName) {
		var $field = $('[name="' + fieldName + '"]');
		if (!$field.length) {
			$field = $('#' + fieldName);
		}
		var $valueCell = $field.closest('td.fieldValue');
		if ($valueCell.length) {
			return $valueCell.add($valueCell.prev('td.fieldLabel'));
		}
		return $field.closest('.fieldValue, .fieldLabel');
	}

	function updateModuleFields(selectedName) {
		var selected = String(selectedName || getSelectedModuleName()).trim().toUpperCase();
		var visible = moduleFields[selected] || [];
		allSpecificFields.forEach(function (fieldName) {
			var $cells = rowForField(fieldName);
			if ($cells.length) {
				$cells.toggle(visible.indexOf(fieldName) !== -1);
			}
		});
	}

	function hideGeneratedNameOnCreate() {
		var $name = $('[name="moduloestacion_name"]');
		if (!$name.length) {
			return;
		}

		var recordId = String($name.closest('form').find('[name="record"]').val() || '').trim();
		if (!recordId || recordId === '0') {
			rowForField('moduloestacion_name').hide();
		}
	}

	$(document).on('change input', '#modulo_id, #modulo_id_display', function () {
		updateModuleFields();
	});
	if (window.Vtiger_Edit_Js) {
		$(document).on(Vtiger_Edit_Js.referenceSelectionEvent, '[name="modulo_id"]', function (event, selection) {
			updateModuleFields(selection && (selection.selectedName || selection.label || selection.name));
		});
		$(document).on(Vtiger_Edit_Js.referenceDeSelectionEvent, '[name="modulo_id"]', function () {
			updateModuleFields('');
		});
	}
	$(document).on('click', '.relatedPopup', function () {
		window.setTimeout(updateModuleFields, 250);
	});
	updateModuleFields();
	hideGeneratedNameOnCreate();

	var saveError = new URLSearchParams(window.location.search).get('saveError');
	if (saveError === 'duplicateModule') {
		app.helper.showErrorNotification({
			message: 'Este módulo ya está asociado a esta estación.'
		}, {
			delay: 0
		});
	}
});