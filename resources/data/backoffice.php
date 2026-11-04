<?php

/*
| Hard-coded backoffice data, shaped like the backend's Additions,
| CarAdditions, Users, Roles, Sales, SaleAdditions and Favorites tables.
*/

return [

    'roles' => [
        'customer' => 'Customer',
        'salesman' => 'Sales team',
        'admin' => 'Admin',
    ],

    'users' => [
        ['id' => 1, 'first_name' => 'Petra', 'last_name' => 'Kovač', 'email' => 'admin@rivamotors.example', 'phone' => '+385 91 000 1101', 'role' => 'admin', 'created_at' => '2026-01-12'],
        ['id' => 2, 'first_name' => 'Marko', 'last_name' => 'Babić', 'email' => 'sales@rivamotors.example', 'phone' => '+385 91 000 1102', 'role' => 'salesman', 'created_at' => '2026-01-12'],
        ['id' => 3, 'first_name' => 'Ivana', 'last_name' => 'Perić', 'email' => 'ivana.peric@rivamotors.example', 'phone' => '+385 91 000 1103', 'role' => 'salesman', 'created_at' => '2026-03-02'],
        ['id' => 4, 'first_name' => 'Ana', 'last_name' => 'Horvat', 'email' => 'customer@rivamotors.example', 'phone' => '+385 98 000 2201', 'role' => 'customer', 'created_at' => '2026-08-19'],
        ['id' => 5, 'first_name' => 'Luka', 'last_name' => 'Marić', 'email' => 'luka.maric@example.com', 'phone' => '+385 98 000 2202', 'role' => 'customer', 'created_at' => '2026-09-03'],
        ['id' => 6, 'first_name' => 'Maja', 'last_name' => 'Jurić', 'email' => 'maja.juric@example.com', 'phone' => '+385 98 000 2203', 'role' => 'customer', 'created_at' => '2026-09-27'],
        ['id' => 7, 'first_name' => 'Tomislav', 'last_name' => 'Novak', 'email' => 'tomislav.novak@example.com', 'phone' => '+385 98 000 2204', 'role' => 'customer', 'created_at' => '2026-10-08'],
    ],

    'additions' => [
        ['id' => 1, 'name' => 'Winter tyres on alloy wheels', 'description' => 'A full set fitted and stored for you between seasons.', 'price' => 890, 'icon' => 'snow', 'is_active' => true],
        ['id' => 2, 'name' => 'Extended warranty, 24 months', 'description' => 'Covers engine, gearbox and electrics on top of the standard warranty.', 'price' => 1190, 'icon' => 'shield', 'is_active' => true],
        ['id' => 3, 'name' => 'Removable tow bar', 'description' => 'Fitted with wiring and registered in the car papers.', 'price' => 690, 'icon' => 'hitch', 'is_active' => true],
        ['id' => 4, 'name' => 'Ceramic paint protection', 'description' => 'Keeps the paint glossy and easier to wash for up to three years.', 'price' => 450, 'icon' => 'drop', 'is_active' => true],
        ['id' => 5, 'name' => 'All-weather floor mats', 'description' => 'Rubber mats cut for this model, front and rear.', 'price' => 95, 'icon' => 'mat', 'is_active' => true],
        ['id' => 6, 'name' => 'Registration and first tank', 'description' => 'We register the car in your name and hand it over with a full tank.', 'price' => 290, 'icon' => 'key', 'is_active' => true],
        ['id' => 7, 'name' => 'Home wallbox, installed', 'description' => '11 kW charger fitted at your home by a certified electrician.', 'price' => 1290, 'icon' => 'plug', 'is_active' => true],
        ['id' => 8, 'name' => 'Roof box, 420 litres', 'description' => 'Lockable box with roof bars, fitted before handover.', 'price' => 540, 'icon' => 'box', 'is_active' => false],
    ],

    /*
    | Which additions each car category can take. Electric cars get the
    | wallbox instead of a first tank of fuel.
    */
    'car_additions' => [
        'combustion' => [1, 2, 3, 4, 5, 6],
        'electric' => [1, 2, 4, 5, 7],
    ],

    'sales' => [
        ['id' => 'RM-2026-00418', 'car_id' => 128, 'buyer_id' => 4,'salesman_id' => 2, 'base_car_price' => 21900, 'additions' => [2, 6], 'total_price' => 23380, 'status' => 'completed', 'payment_status' => 'paid', 'payment_reference' => 'pi_3Q8x2RivaA1', 'requested_at' => '2026-10-03 11:20'],
        ['id' => 'RM-2026-00421', 'car_id' => 139, 'buyer_id' => 6, 'salesman_id' => 3, 'base_car_price' => 34900, 'additions' => [1, 7], 'total_price' => 37080, 'status' => 'requested', 'payment_status' => 'pending', 'payment_reference' => 'pi_3Q9a7RivaB4', 'requested_at' => '2026-10-19 16:20'],
        ['id' => 'RM-2026-00415', 'car_id' => 137, 'buyer_id' => 7, 'salesman_id' => 2, 'base_car_price' => 15900, 'additions' => [5], 'total_price' => 15995, 'status' => 'cancelled', 'payment_status' => 'failed', 'payment_reference' => 'pi_3Q7m1RivaC9', 'requested_at' => '2026-09-24 10:05'],    ],

];
