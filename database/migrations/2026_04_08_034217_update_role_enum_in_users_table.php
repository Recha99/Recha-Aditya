<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MySQL/MariaDB: ubah enum
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'petugas', 'peminjam'])->default('peminjam')->change();
            });
        }
        // Untuk SQLite: recreate table dengan constraint yang benar
        else if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement("PRAGMA foreign_keys=OFF");

            // Create temporary table dengan constraint yang benar
            DB::statement("
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    name VARCHAR NOT NULL,
                    email VARCHAR NOT NULL UNIQUE,
                    password VARCHAR NOT NULL,
                    role VARCHAR NOT NULL DEFAULT 'peminjam' CHECK(role IN ('admin', 'petugas', 'peminjam')),
                    remember_token VARCHAR,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ");

            // Copy data dari table lama ke table baru
            DB::statement("INSERT INTO users_new (id, name, email, password, role, remember_token, created_at, updated_at)
                          SELECT id, name, email, password, CASE WHEN role = 'user' THEN 'peminjam' ELSE role END,
                                 remember_token, created_at, updated_at FROM users");

            // Drop table lama dan rename table baru
            DB::statement("DROP TABLE users");
            DB::statement("ALTER TABLE users_new RENAME TO users");

            DB::statement("PRAGMA foreign_keys=ON");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'petugas', 'user'])->default('user')->change();
            });
        } else if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement("PRAGMA foreign_keys=OFF");

            DB::statement("
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    name VARCHAR NOT NULL,
                    email VARCHAR NOT NULL UNIQUE,
                    password VARCHAR NOT NULL,
                    role VARCHAR NOT NULL DEFAULT 'user' CHECK(role IN ('admin', 'petugas', 'user')),
                    remember_token VARCHAR,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ");

            DB::statement("INSERT INTO users_new (id, name, email, password, role, remember_token, created_at, updated_at)
                          SELECT id, name, email, password, CASE WHEN role = 'peminjam' THEN 'user' ELSE role END,
                                 remember_token, created_at, updated_at FROM users");

            DB::statement("DROP TABLE users");
            DB::statement("ALTER TABLE users_new RENAME TO users");

            DB::statement("PRAGMA foreign_keys=ON");
        }
    }
};
