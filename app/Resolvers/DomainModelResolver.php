<?php

namespace App\Resolvers;

use App\Models\User;

class DomainModelResolver
{
    public static function model(string $db_connection, string $model)
    {
        $namespace = match($db_connection) {
            'pwk' => 'App\\Models\\PWK\\',
            'elektro' => 'App\\Models\\Elektro\\',
            default => abort(403),
        };

        $class = $namespace.$model;

        if(!class_exists($class)) {
            abort(500, "Model {$class} not found");
        }

        return $class;
    }

    //  $model = DomainModelResolver::model(auth()->user(), 'Mahasiswa')
}
