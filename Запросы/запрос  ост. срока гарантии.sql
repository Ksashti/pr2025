SELECT
    product_id,
    warranty_start_date,
    warranty_end_date,
    calculate_warranty_remaining(warranty_end_date) AS remaining_time
FROM
    Warranty_History;