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
                    "cars",
                    "accommodations",
                    "reservations",
                    "packages",
                    "stats",
                    "reports",
                    "users",
                    "admins",
                    "drivers",
                ],
                "create" => [
                    "cars",
                    "accommodations",
                    "reservations",
                    "packages",
                    "stats",
                    "reports",
                    "users",
                    "admins",
                    "drivers",
                ],
                "update" => [
                    "cars",
                    "accommodations",
                    "reservations",
                    "packages",
                    "stats",
                    "reports",
                    "users",
                    "admins",
                    "drivers",
                ],
                "delete" => [
                    "cars",
                    "accommodations",
                    "reservations",
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
                    "reservations",
                ],
                "create" => [
                    "reservations",
                ],
                "update" => [
                    "reservations",
                ],
                "delete" => [
                    "reservations",
                ]
            ];
        } else if ($role == 'accountant') {
            return [
                "view" => [
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
                    "cars",
                    "accommodations",
                    "reservations",
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
