<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    "accepted"         => ":attribute debe ser aceptado.",
    "active_url"       => ":attribute no es una URL válida.",
    "after"            => ":attribute debe ser una fecha posterior a :date.",
    "alpha"            => ":attribute solo debe contener letras.",
    "alpha_dash"       => ":attribute solo debe contener letras, números y guiones.",
    "alpha_num"        => ":attribute solo debe contener letras y números.",
    "array"            => ":attribute debe ser un conjunto.",
    "before"           => ":attribute debe ser una fecha anterior a :date.",
    "between"          => [
        "numeric" => ":attribute tiene que estar entre :min - :max.",
        "file"    => ":attribute debe pesar entre :min - :max kilobytes.",
        "string"  => ":attribute tiene que tener entre :min - :max caracteres.",
        "array"   => ":attribute tiene que tener entre :min - :max ítems.",
    ],
    "boolean"          => "El campo :attribute debe tener un valor verdadero o falso.",
    "confirmed"        => "La confirmación de :attribute no coincide.",
    "date"             => ":attribute no es una fecha válida.",
    "date_format"      => ":attribute no corresponde al formato :format.",
    "different"        => ":attribute y :other deben ser diferentes.",
    "digits"           => ":attribute debe tener :digits dígitos.",
    "digits_between"   => ":attribute debe tener entre :min y :max dígitos.",
    "email"            => ":attribute no es un correo válido",
    "exists"           => ":attribute es inválido.",
    "filled"           => "El campo :attribute es obligatorio.",
    "image"            => ":attribute debe ser una imagen.",
    "in"               => ":attribute es inválido.",
    "integer"          => ":attribute debe ser un número entero.",
    "ip"               => ":attribute debe ser una dirección IP válida.",
    "max"              => [
        "numeric" => ":attribute no debe ser mayor a :max.",
        "file"    => ":attribute no debe ser mayor que :max kilobytes.",
        "string"  => ":attribute no debe ser mayor que :max caracteres.",
        "array"   => ":attribute no debe tener más de :max elementos.",
    ],
    "mimes"            => ":attribute debe ser un archivo con formato: :values.",
    "min"              => [
        "numeric" => "El tamaño de :attribute debe ser de al menos :min.",
        "file"    => "El tamaño de :attribute debe ser de al menos :min kilobytes.",
        "string"  => ":attribute debe contener al menos :min caracteres.",
        "array"   => ":attribute debe tener al menos :min elementos.",
    ],
    "not_in"           => ":attribute es inválido.",
    "numeric"          => ":attribute debe ser numérico.",
    "regex"            => "El formato de :attribute es inválido.",
    "required"         => "El campo :attribute es obligatorio.",
    "required_if"      => "El campo :attribute es obligatorio cuando :other es :value.",
    "required_with"    => "El campo :attribute es obligatorio cuando :values está presente.",
    "required_with_all" => "El campo :attribute es obligatorio cuando :values está presente.",
    "required_without" => "El campo :attribute es obligatorio cuando :values no está presente.",
    "required_without_all" => "El campo :attribute es obligatorio cuando ninguno de :values estén presentes.",
    "same"             => ":attribute y :other deben coincidir.",
    "size"             => [
        "numeric" => "El tamaño de :attribute debe ser :size.",
        "file"    => "El tamaño de :attribute debe ser :size kilobytes.",
        "string"  => ":attribute debe contener :size caracteres.",
        "array"   => ":attribute debe contener :size elementos.",
    ],
    "timezone"         => "El :attribute debe ser una zona válida.",
    "unique"           => ":attribute ya ha sido registrado.",
    "url"              => "El formato :attribute es inválido.",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'email'                =>'Correo electrónico',
        'name'                 =>'Nombre',
        'description'          =>'Descripcion',
        'company_name'         =>'Razón Social',
        'brand_name'           =>'Nombre Comercial',
        'paternal_surname'     =>'Apellido Paterno',
        'maternal_surname'     =>'Apellido Materno',
        'id_type_id'           =>'Tipo de documento',
        'doc'                  =>'Número de documento',
        'address'              =>'Dirección',
        'ubigeo_id'            =>'Distrito',
        'phone'                =>'Teléfono',
        'mobile'               =>'Celular',
        'birth'                =>'Fecha de nacimiento',
        'is_provider'          =>'Es proveedor',
        'symbol'               =>'Símbolo',
        'to_sales'             =>'Venta',
        'to_purchases'         =>'Compra',
        'full_name'            =>'Nombre Completo',
        'gender'               =>'Género',
        'email_personal'       =>'Correo personal',
        'email_personal'       =>'Correo empresarial',
        'sales'                =>'Venta',
        'purchase'             =>'Compra',
        'intern_code'          =>'Código interno',
        'provider_code'        =>'Código proveedor',
        'manufacturer_code'    =>'Código fabricante',
        'sub_category_id'      =>'Sub Categoría',
        'category_id'          =>'Categoría',
        'unit_id'              =>'Unidad',
        'brand_id'             =>'Marca',
        'currency_id'          =>'Moneda',
        'last_pruchase'        =>'Costo',
        'profit_margin'        =>'Utilidad',
        'price'                =>'Precio',
        'set_price'            =>'Precio Asignado',
        'date'                 =>'Fecha',
        'document_type_id'     =>'Tipo de documento',
        'payment_condition_id' =>'Condición de Pago',
        'due_date'             =>'Fecha de Vencimiento',
        'quantity'             =>'Cantidad',
        'discount'             =>'Descuento',
        'role_id'              =>'Rol',
        'permission_id'        =>'Permiso',
        'warehouse_id'         =>'Almacén',
        'unit_type_id'         =>'Tipo de Unidad',
        'value'                =>'Valor',
        'is_superuser'         =>'Es Super Usuario'
    ],

];
