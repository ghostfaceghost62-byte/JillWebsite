<?php
require_once __DIR__ . '/../includes/bootstrap.php';

try {
    $pdo = db();
    echo "Connected to Aiven DB for Room Update...\n";

    // Disable foreign key checks temporarily
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

    // Clear existing sample rooms and types
    $pdo->exec("TRUNCATE TABLE room_images");
    $pdo->exec("TRUNCATE TABLE rooms");
    $pdo->exec("TRUNCATE TABLE room_types");

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    // Insert Lido De Paris Room Types
    $roomTypes = [
        [
            'id' => 1,
            'name' => 'Deluxe Room',
            'description' => 'Newly renovated modern city room featuring King or Twin bedding, LED HDTV, work desk, high-speed Wi-Fi, and private bathroom in Manila Chinatown.',
            'base_price' => 3300.00,
            'max_guests' => 2,
            'bed_type' => 'King Bed or 2 Twin Beds',
            'size' => '32 sqm'
        ],
        [
            'id' => 2,
            'name' => 'Balcony Superior Suite',
            'description' => 'Sunlit suite featuring a private balcony overlooking historic Ongpin Street, cozy lounge seating, vanity desk, and city views.',
            'base_price' => 5300.00,
            'max_guests' => 3,
            'bed_type' => '1 King Bed + Daybed',
            'size' => '42 sqm'
        ],
        [
            'id' => 3,
            'name' => 'Hospitality KTV Suite',
            'description' => 'Signature Lido entertainment suite equipped with private KTV karaoke system, lounge sofa, mini-bar, and party space.',
            'base_price' => 7000.00,
            'max_guests' => 4,
            'bed_type' => '1 King Bed + Lounge Sofa',
            'size' => '55 sqm'
        ],
        [
            'id' => 4,
            'name' => 'Presidential Suite',
            'description' => 'Grand luxury presidential suite featuring a separate master bedroom, executive living room, dining nook, and VIP concierge privileges.',
            'base_price' => 8000.00,
            'max_guests' => 4,
            'bed_type' => '1 King Bed',
            'size' => '72 sqm'
        ],
        [
            'id' => 5,
            'name' => 'Presidential Kitchen Suite',
            'description' => 'Top-tier penthouse residence featuring full kitchenette, dining table for 6, master bedroom suite, guest bedroom, and panoramic Binondo views.',
            'base_price' => 12000.00,
            'max_guests' => 6,
            'bed_type' => '1 King Bed + 2 Queen Beds',
            'size' => '95 sqm'
        ]
    ];

    $typeStmt = $pdo->prepare("INSERT INTO room_types (id, name, description, base_price, max_guests, bed_type, size, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    foreach ($roomTypes as $rt) {
        $typeStmt->execute([$rt['id'], $rt['name'], $rt['description'], $rt['base_price'], $rt['max_guests'], $rt['bed_type'], $rt['size']]);
    }
    echo "Inserted " . count($roomTypes) . " Lido Room Types.\n";

    // Insert Lido De Paris Rooms
    $rooms = [
        [
            'room_number' => '201',
            'title' => 'Deluxe King Room',
            'description' => 'Stylish Deluxe room with 1 King bed, LED HDTV, high-speed Wi-Fi, and Chinatown city views.',
            'room_type_id' => 1,
            'price_per_night' => 3300.00,
            'max_guests' => 2,
            'bed_type' => 'King Bed',
            'size' => '32 sqm',
            'status' => 'AVAILABLE',
            'featured' => 1
        ],
        [
            'room_number' => '202',
            'title' => 'Deluxe Twin Room',
            'description' => 'Comfortable Deluxe room featuring 2 Twin beds, ideal for friends or business colleagues.',
            'room_type_id' => 1,
            'price_per_night' => 3300.00,
            'max_guests' => 2,
            'bed_type' => '2 Twin Beds',
            'size' => '32 sqm',
            'status' => 'AVAILABLE',
            'featured' => 0
        ],
        [
            'room_number' => '301',
            'title' => 'Balcony Superior Suite',
            'description' => 'Spacious room with a private outdoor balcony directly overlooking vibrant Ongpin Street.',
            'room_type_id' => 2,
            'price_per_night' => 5300.00,
            'max_guests' => 3,
            'bed_type' => '1 King Bed + Daybed',
            'size' => '42 sqm',
            'status' => 'AVAILABLE',
            'featured' => 1
        ],
        [
            'room_number' => '401',
            'title' => 'Hospitality KTV Suite',
            'description' => 'Exclusive Lido suite equipped with soundproof karaoke KTV setup, party sofa lounge, and mini-bar.',
            'room_type_id' => 3,
            'price_per_night' => 7000.00,
            'max_guests' => 4,
            'bed_type' => '1 King Bed + Lounge Sofa',
            'size' => '55 sqm',
            'status' => 'AVAILABLE',
            'featured' => 1
        ],
        [
            'room_number' => '501',
            'title' => 'Presidential Suite',
            'description' => 'Master presidential suite with separate parlor lounge, marble bath, and VIP luxury amenities.',
            'room_type_id' => 4,
            'price_per_night' => 8000.00,
            'max_guests' => 4,
            'bed_type' => '1 King Bed',
            'size' => '72 sqm',
            'status' => 'AVAILABLE',
            'featured' => 1
        ],
        [
            'room_number' => '601',
            'title' => 'Presidential Kitchen Suite',
            'description' => 'Ultimate luxury penthouse suite with full kitchenette, 6-seat dining suite, and panoramic skyline view.',
            'room_type_id' => 5,
            'price_per_night' => 12000.00,
            'max_guests' => 6,
            'bed_type' => '1 King Bed + 2 Queen Beds',
            'size' => '95 sqm',
            'status' => 'AVAILABLE',
            'featured' => 1
        ]
    ];

    $roomStmt = $pdo->prepare("INSERT INTO rooms (room_number, title, description, room_type_id, price_per_night, max_guests, bed_type, size, status, featured, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $imgStmt = $pdo->prepare("INSERT INTO room_images (room_id, image_path, is_primary, created_at) VALUES (?, ?, 1, NOW())");

    $roomImages = [
        1 => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=85',
        2 => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=85',
        3 => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=85',
        4 => 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?auto=format&fit=crop&w=1200&q=85',
        5 => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85',
        6 => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85'
    ];

    foreach ($rooms as $r) {
        $roomStmt->execute([
            $r['room_number'], $r['title'], $r['description'], $r['room_type_id'],
            $r['price_per_night'], $r['max_guests'], $r['bed_type'], $r['size'],
            $r['status'], $r['featured']
        ]);
        $roomId = $pdo->lastInsertId();
        if (isset($roomImages[$roomId])) {
            $imgStmt->execute([$roomId, $roomImages[$roomId]]);
        }
    }

    echo "Inserted " . count($rooms) . " Lido De Paris Rooms and primary room images!\n";

} catch (Exception $e) {
    echo "Error syncing rooms: " . $e->getMessage() . "\n";
}

