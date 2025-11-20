<?php

// use Exception;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        // التأكد من وجود config
        throw_if(empty($tableNames), new Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'));
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), new Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.'));

        // جدول الصلاحيات
        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            $table->bigIncrements('id'); // PK
            $table->string('name')->index()->comment('اسم الصلاحية (مثال: manage-users)');
            $table->string('guard_name')->comment('guard name (web/api)');
            $table->timestamps();
            $table->softDeletes()->comment('حذف ناعم لصلاحيات إن احتجنا');
            $table->unique(['name', 'guard_name']);
        });

        // جدول الأدوار
        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id'); // PK
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable()->index();
            }
            $table->string('name')->index()->comment('اسم الدور (مثال: Admin)');
            $table->string('guard_name')->comment('guard name (web/api)');
            $table->timestamps();
            $table->softDeletes()->comment('حذف ناعم للدور إن احتجنا');
            if ($teams) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        // model_has_permissions
        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission)->comment('FK: permission id');
            $table->string('model_type')->comment('model class (morph)');
            $table->unsignedBigInteger($columnNames['model_morph_key'])->comment('model id (morph)');
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable()->index();
                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type']);
            } else {
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type']);
            }
        });

        // model_has_roles
        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole)->comment('FK: role id');
            $table->string('model_type')->comment('model class (morph)');
            $table->unsignedBigInteger($columnNames['model_morph_key'])->comment('model id (morph)');
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable()->index();
                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type']);
            } else {
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type']);
            }
        });

        // role_has_permissions
        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission)->comment('FK: permission id');
            $table->unsignedBigInteger($pivotRole)->comment('FK: role id');

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        // تأكد من تنظيف كاش الحزمة
        Cache::forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        throw_if(empty($tableNames), new Exception('Error: config/permission.php not found. Please publish the package configuration.'));

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
