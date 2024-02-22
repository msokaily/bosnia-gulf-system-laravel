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
                ],
                "create" => [],
                "update" => [],
                "delete" => []
            ];
        } else if ($role == 'monitor') {
            return [
                "view" => [
                    "home",
                    "cars",
                    "accommodations",
                    "orders",
                    "packages",
                    "stats",
                    "reports",
                    "drivers",
                ],
                "create" => [],
                "update" => [],
                "delete" => []
            ];
        }
    }
}
