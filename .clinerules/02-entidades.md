# Entidades del sistema y sus relaciones

## 1. Configuración
- **Establishment**: locales/establecimientos (nombre, dirección, teléfono, email, código correlativo).
- **User**: usuarios (name, email, password, phone, establishment_id, roles).
- **Role / Permission**: control de acceso (spatie/laravel-permission).

## 2. Clientes y vehículos
- **Client**: personas naturales o empresas (document_type, document_number, business_name, ubigeo, address, phone, mobile, email, is_insurance_company, insurance_hourly_rate, insurance_panel_rate, establishment_id).
- **Vehicle**: placa (unique), client_id, brand, model, body_type, color, vin, engine_number, year.
- **VehicleContact**: contactos del vehículo (tipo: aprobador, chofer, operador) con nombre, celular, email.

## 3. Inventario
- **CheckIn**: ingreso de vehículo (vehicle_id, client_id, service_type, insurance_company_id, mileage, fuel_level, property_card, soat_date, technical_review_date, keys_count, remote_control, client_request, comments, user_id, establishment_id).
- **CheckInChecklist**: ítems del checklist con estado (bueno, regular, malo, no_aplica) y observaciones.
- **CheckInDamage**: daños (tipo, coordenadas x/y, lado del vehículo).
- **CheckInPhoto**: fotos (ruta, orden).

## 4. Presupuestos
- **Estimate**: presupuesto (check_in_id nullable, vehicle_id, client_id, insurance_company_id, claim_number, advisor_id, work_days, contact_name, contact_phone, contact_email, comments, hourly_rate, panel_rate, status, total, iva).
- **EstimateItem**: ítems del presupuesto (tipo: servicio, repuesto, orden_compra_tercero, category, quantity, unit_price, discount, subtotal, iva, total).
- **ThirdPartyOrder**: órdenes de compra de terceros (estimate_id, description, amount_without_iva, provider_name).
- **FranchiseCalculation**: cálculo de franquicia (estimate_id, minimum_amount, percentage, base_amount, total_franchise_without_iva).

## 5. Órdenes de Trabajo
- **WorkOrder**: orden de trabajo (estimate_id, vehicle_id, start_date, estimated_end_date, status, notes).
- **WorkOrderSubstage**: subetapas (nombre, descripción, orden).
- **WorkOrderAssignment**: asignaciones (work_order_id, user_id (técnico), substage_id, hours, cost).

## 6. Servicios Tercerizados
- **ServiceVoucher**: comprobante de servicio (work_order_id, provider_id (cliente), execution_date, description, agreed_amount, discount_applied, final_amount, status, provider_settlement_id nullable).
- **ProviderSettlement**: liquidación (provider_id, period_start, period_end, subtotal, global_discount, discount_reason, total_payable, status, approved_by, paid_by).

## 7. Almacén
- **Part**: repuesto (name, description, sku, brand, category, min_stock).
- **Warehouse**: almacén (name, establishment_id).
- **WarehouseStock**: stock por almacén (part_id, warehouse_id, quantity).
- **StockMovement**: movimiento de stock (part_id, warehouse_id, type, quantity, unit_cost, total_cost, movement_date, document_type, document_number, work_order_id, purchase_order_id, notes).
- **PartOrder**: pedido de repuesto de seguro (estimate_id, part_id, provider_id, order_date, estimated_arrival_date, status, notes).

## 8. Citas y seguimiento
- **Appointment**: cita (client_id, vehicle_id, advisor_id, datetime, service_type, reason, status).
- **FollowUp**: seguimiento (client_id, vehicle_id, date, type, notes, next_action_date).

## 9. Facturación y pagos
- **Invoice**: factura/boleta (estimate_id, type, series, number, emission_date, subtotal, iva, total, amount_paid, status, sunat_code).
- **Payment**: pago (payable_type, payable_id, client_id, amount, payment_method, reference, payment_date, direction, notes).
- **ExchangeRate**: tipo de cambio (date, type, buy_rate, sell_rate, source).