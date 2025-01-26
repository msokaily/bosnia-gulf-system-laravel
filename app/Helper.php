<?php
class Helper
{
    public static $roles = [
        'admin',
        'reserver',
        'accountant',
        'monitor'
    ];
    public static $parnersTypes = [
        'cars',
        'accommodations',
    ];
    public static $repairTypes = [
        'car',
        'accommodation',
    ];
    public static $productTypes = [
        'car',
        'accommodation',
        'driver',
    ];
    public static $currencies = [
        'EUR',
        'BAM',
        'USD',
    ];
    public static $orderStatus = [
        0 => [
            "ar" => "جديد",
            "en" => "New",
        ],
        1 => [
            "ar" => "تم التأكيد",
            "en" => "Confirmed",
        ],
        2 => [
            "ar" => "مكتمل",
            "en" => "Completed",
        ],
        3 => [
            "ar" => "ملغي",
            "en" => "Canceled",
        ],
        4 => [
            "ar" => "تم إعادة الملبغ",
            "en" => "Refunded",
        ],
    ];

    public static function errorsFormat($errors)
    {
        $ret = [];
        foreach ($errors as $value) {
            $ret[] = is_string($value) ? $value : $value[0];
        }
        return $ret;
    }

    public static function permissions($role)
    {
        if ($role == 'admin') {
            return [
                "view" => [
                    "home",
                    "cars",
                    "car-companies",
                    "accommodations",
                    "orders",
                    "partners",
                    "packages",
                    "stats",
                    "reports",
                    "users",
                    "admins",
                    "drivers",
                    "repairs",
                ],
                "create" => [
                    "cars",
                    "car-companies",
                    "accommodations",
                    "orders",
                    "partners",
                    "packages",
                    "stats",
                    "reports",
                    "users",
                    "admins",
                    "drivers",
                    "repairs",
                ],
                "update" => [
                    "cars",
                    "car-companies",
                    "accommodations",
                    "orders",
                    "partners",
                    "packages",
                    "stats",
                    "reports",
                    "users",
                    "admins",
                    "drivers",
                    "repairs",
                ],
                "delete" => [
                    "cars",
                    "car-companies",
                    "accommodations",
                    "orders",
                    "partners",
                    "packages",
                    "stats",
                    "reports",
                    "users",
                    "admins",
                    "drivers",
                    "repairs",
                ]
            ];
        } else if ($role == 'reserver') {
            return [
                "view" => [
                    "home",
                    "orders",
                ],
                "create" => [
                    "orders",
                ],
                "update" => [
                    "orders",
                ],
                "delete" => [
                    "orders",
                ]
            ];
        } else if ($role == 'accountant') {
            return [
                "view" => [
                    "home",
                    "stats",
                    "reports",
                    "orders"
                ],
                "create" => [],
                "update" => [
                    "orders_paid",
                ],
                "delete" => []
            ];
        } else if ($role == 'monitor') {
            return [
                "view" => [
                    "home",
                    "orders",
                    "cars",
                    "accommodations",
                    "orders",
                    "packages",
                    "stats",
                    "reports",
                    "drivers",
                ],
                "create" => [],
                "update" => [
                    "orders",
                ],
                "delete" => []
            ];
        }
    }

    public static function orderStatusName($status)
    {
        try {
            return self::$orderStatus[$status];
        } catch (\Throwable $th) {
            return self::$orderStatus[0];
        }
    }
    
    public static function arrayDiffValues($array1, $array2)
    {
        $result = array_diff($array1, $array2);

        return $result;
    }

}
