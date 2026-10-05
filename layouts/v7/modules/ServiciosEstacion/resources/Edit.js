jQuery(function ($) {
    var serviceFields = {
        'NOTICES': ['notices_correo_configurado', 'notices_estado_envio_correos'],
        'E-INVOICING': ['e_invoicing_estado_facturacion'],
        'IISE': ['iise_sistema_contable', 'iise_estado_certificacion', 'iise_credenciales_entregadas'],
        'PAY FUELCONTROL-PAYPHONE': ['pay_fuelcontrol_payphone_estado_afiliacion'],
        'PAY FUELCONTROL-DATAFAST': [
            'pay_fuelcontrol_datafast_estado_afiliacion',
            'pay_fuelcontrol_datafast_esquema_instalado',
            'pay_fuelcontrol_datafast_version_equipos',
            'pay_fuelcontrol_datafast_mid',
            'pay_fuelcontrol_datafast_tid',
            'pay_fuelcontrol_datafast_estado_certificacion'
        ],
        'PAY FUELCONTROL-MEDIANET': [
            'pay_fuelcontrol_medianet_estado_afiliacion',
            'pay_fuelcontrol_medianet_esquema_instalado',
            'pay_fuelcontrol_medianet_version_equipos',
            'pay_fuelcontrol_medianet_mid',
            'pay_fuelcontrol_medianet_tid',
            'pay_fuelcontrol_medianet_estado_certificacion'
        ],
        'PAY FUELCONTROL-DEUNA': [
            'pay_fuelcontrol_deuna_esquema_instalado',
            'pay_fuelcontrol_deuna_cantidad_equipos',
            'pay_fuelcontrol_deuna_version_equipos',
            'pay_fuelcontrol_deuna_puntos_venta'
        ],
        'TUNNEL-WEBHOOK': ['tunnel_webhook_aplicacion', 'tunnel_webhook_acceso_webhook'],
        'TUNNEL-API REST': ['tunnel_api_rest_aplicacion', 'tunnel_api_rest_acceso_api'],
        'ARCH': ['arch_estado_proceso_permisos'],
        'VPN CONNECT': ['vpn_connect_cantidad_vpn_activas', 'vpn_connect_acceso_servidor', 'vpn_connect_acceso_clientes']
    };

    var allSpecificFields = [].concat.apply([], Object.keys(serviceFields).map(function (key) {
        return serviceFields[key];
    }));

    function getSelectedServiceName() {
        return String($('#servicio_id_display').val() || '').trim().toUpperCase();
    }

    function rowForField(fieldName) {
        var $field = $('[name="' + fieldName + '"]');
        if (!$field.length) {
            $field = $('#' + fieldName);
        }
        return $field.closest('tr');
    }

    function updateServiceFields() {
        var selected = getSelectedServiceName();
        var visible = serviceFields[selected] || [];
        allSpecificFields.forEach(function (fieldName) {
            var $row = rowForField(fieldName);
            if ($row.length) {
                $row.toggle(visible.indexOf(fieldName) !== -1);
            }
        });
    }

    $(document).on('change input', '#servicio_id, #servicio_id_display', updateServiceFields);
    $(document).on('click', '.relatedPopup', function () {
        window.setTimeout(updateServiceFields, 250);
    });
    updateServiceFields();
});
