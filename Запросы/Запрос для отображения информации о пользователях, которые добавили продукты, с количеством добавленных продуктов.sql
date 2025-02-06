SELECT --  Запрос для отображения информации о пользователях, которые добавили продукты, с количеством добавленных продуктов
    u.username,
    u.first_name,
    u.last_name,
    COUNT(p.product_id) AS products_added
FROM
    Users u
LEFT JOIN
    Products p ON u.user_id = p.user_id
GROUP BY
    u.user_id, u.username, u.first_name, u.last_name
ORDER BY products_added DESC;