<?php

$old_csv_file = "backend-task.csv";
$new_csv_file  = uniqid() . '-' . "new-task.csv";

$filter_data = [];
// open the old file first
$f = fopen($old_csv_file, "r");
if ($f !== false) {
    fgetcsv($f, 1000, ",");
    while (($data = fgetcsv($f, 1000, ",")) !== false) {
        $orderDate = $data[0];
        $email = $data[2];
        $quantity = $data[3];

        if (!isset($filter_data[$email])) {
            $filter_data[$email]  = [
                'firstOrderDate' => $orderDate,
                'lastOrderDate' => $orderDate,
                'orderDates' => [$orderDate],
                'totalOrder' => 1,
                'totalQuantity' => $quantity
            ];
        } else {
            $filter_data[$email]['lastOrderDate'] = $orderDate;
            $filter_data[$email]['orderDates'][] = $orderDate;
            $filter_data[$email]['totalOrder']++;
            $filter_data[$email]['totalQuantity'] = $quantity;
        }
    }
}
fclose($f);

$newOpen = fopen($new_csv_file, "w");
if ($newOpen !== false) {
    // add the headings
    fputcsv($newOpen, ['Customer Email', 'First Order Date', 'Last Order Date', 'Days Difference', 'email', 'quantity']);

    foreach ($filter_data as $email => $customer) {
        $firstOrderDate = $customer['firstOrderDate'];
        $lastOrderDate = $customer['lastOrderDate'];
        $daysDifference = strtotime($firstOrderDate) - strtotime($lastOrderDate);
        $daysDifference = floor($daysDifference / 24 * 60 * 60);
        $totalOrder = $customer['totalOrder'];
        $quantity = $customer['totalQuantity'];

        fputcsv($newOpen, [$email, $firstOrderDate, $lastOrderDate, $daysDifference, $totalOrder, $quantity]);
    }
}
fclose($newOpen);

echo "File has been created successfully";
