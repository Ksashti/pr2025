SELECT -- Запрос для отображения всех продуктов с их гарантийным статусом и информацией о производителе
    p.product_name,
    p.price,
    p.description,
    wh.warranty_start_date,
    wh.warranty_end_date,
    wh.status AS warranty_status,
    c.company_name AS manufacturer,
    calculate_warranty_remaining(wh.warranty_end_date) AS warranty_remaining
FROM
    Products p
JOIN
    Warranty_History wh ON p.product_id = wh.product_id
JOIN
    Companies c ON p.company_id = c.company_id;