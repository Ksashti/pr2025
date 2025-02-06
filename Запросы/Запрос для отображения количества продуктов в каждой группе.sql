SELECT -- Запрос для отображения количества продуктов в каждой группе
    pg.group_name,
    COUNT(p.product_id) AS product_count
FROM
    Products_Groups pg
LEFT JOIN
    Products p ON pg.product_group_id = p.product_group_id
GROUP BY
    pg.group_name;