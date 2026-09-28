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
		return $field.closest('tr');
	}

	function updateModuleFields() {
		var selected = getSelectedModuleName();
		var visible = moduleFields[selected] || [];
		allSpecificFields.forEach(function (fieldName) {
			var $row = rowForField(fieldName);
			if ($row.length) {
				$row.toggle(visible.indexOf(fieldName) !== -1);
			}
		});
	}

	$(document).on('change input', '#modulo_id, #modulo_id_display', updateModuleFields);
	$(document).on('click', '.relatedPopup', function () {
		window.setTimeout(updateModuleFields, 250);
	});
	updateModuleFields();
});