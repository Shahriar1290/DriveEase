<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * SqlHelper
 * ---------------------------------------------------------------------
 * This project uses 100% raw, hand-written SQL (no Eloquent ORM, no
 * query builder ->where()/->get() chains). Every database read/write
 * in every controller goes through DB::select / DB::insert / DB::update
 * / DB::delete / DB::statement with parameter-bound SQL strings.
 *
 * This helper only provides small generic utilities (last insert id,
 * pagination math, simple WHERE building) that are reused by many
 * controllers, so that the actual SQL text always stays visible and
 * explicit inside each controller method.
 * ---------------------------------------------------------------------
 */
class SqlHelper
{
    /**
     * Run a raw SELECT and return the rows as plain stdClass array.
     */
    public static function select(string $sql, array $bindings = []): array
    {
        return DB::select($sql, $bindings);
    }

    /**
     * Run a raw SELECT and return only the first row (or null).
     */
    public static function first(string $sql, array $bindings = [])
    {
        $rows = DB::select($sql, $bindings);
        return $rows[0] ?? null;
    }

    /**
     * Run INSERT and return the new auto-increment id.
     */
    public static function insertGetId(string $sql, array $bindings = []): int
    {
        DB::insert($sql, $bindings);
        return (int) DB::getPdo()->lastInsertId();
    }

    /**
     * Run UPDATE / DELETE and return affected row count.
     */
    public static function write(string $sql, array $bindings = []): int
    {
        return DB::update($sql, $bindings) ?: DB::affectingStatement($sql, $bindings);
    }

    /**
     * Simple manual paginator for raw SQL result sets.
     * $countSql must return a single row with column `total`.
     */
    public static function paginate(string $dataSql, string $countSql, array $bindings, int $perPage, int $page): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = (int) (self::first($countSql, $bindings)->total ?? 0);

        $pagedSql = $dataSql . " LIMIT {$perPage} OFFSET {$offset}";
        $rows = DB::select($pagedSql, $bindings);

        return [
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ];
    }
}
