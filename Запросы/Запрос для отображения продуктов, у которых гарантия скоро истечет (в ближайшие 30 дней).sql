SELECT -- Запрос для отображения продуктов, у которых гарантия скоро истечет (в ближайшие 30 дней)
    p.product_name,
    wh.warranty_start_date,
    wh.warranty_end_date,
    calculate_warranty_remaining(wh.warranty_end_date) AS warranty_remaining
FROM
    Products p
JOIN
    Warranty_History wh ON p.product_id = wh.product_id
WHERE
   wh.warranty_end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY);