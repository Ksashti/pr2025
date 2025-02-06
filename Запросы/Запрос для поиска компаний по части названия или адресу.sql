SELECT -- Запрос для поиска компаний по части названия или адресу:
    company_name,
    address,
    email
FROM
    Companies
WHERE
    company_name LIKE 'Company A' OR address LIKE 'coadasnyk@example.com';